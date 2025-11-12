<?php

namespace App\Http\Controllers;

use App\Models\ResepKronis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKronisController extends Controller
{
    /**
     * Menampilkan halaman laporan dan data yang difilter.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->format('Y-m-d'));

        // Ambil data yang sudah ada dari MySQL (koneksi 'mysql_ims')
        $existingData = ResepKronis::select('no_sep', 'norm', 'tanggal_daftar', 'nama_barang')
            ->get()
            ->keyBy(function ($item) {
                return $item->no_sep . '|' . $item->norm . '|' . $item->tanggal_daftar->format('Y-m-d') . '|' . $item->nama_barang;
            });

        // Ambil nomor resep terakhir dari MySQL untuk bulan ini
        $lastResep = ResepKronis::where('created_at', '>=', now()->startOfMonth())->max('noresep');
        $nextNo = ($lastResep ?? 0) + 1;

        // Query ke SQL Server (koneksi 'sqlsrv')
        $query = "SELECT 
                A.NOPENDAFTARAN,
                A.TANGGALUPDATED,
                E.KDPOLI_BPJS,
                F.KD_BPJS,
                ISNULL(B.NOSEP, ISNULL(W.NO_SEP, '')) AS NOSEP, 
                A.NORM, 
                A.NAMAPASIEN + ',' + A.TITLE AS NAMAPASIEN, 
                J.ID_BPJS,
                C.NAMAMITRA AS ASURANSI, 
                E.NAMAPOLI,   
                F.NAMAPEMERIKSA AS DPJP, 
                A.TANGGALDAFTAR, 
                H.NAMABARANG, 
                J.KATEGORI AS JENISOBAT, 
                H.JUMLAHBARANG AS QTY, 
                CAST(H.RUPIAHJUAL AS BIGINT) AS RPJUAL, 
                ISNULL(I.V_1 + ' x ' + I.V_2 + ' ' + I.V_3, '') AS ZIGNA,
                H.NOMORPENJUALAN
            FROM DIP A  
            LEFT JOIN SEP B ON B.NODAFTAR = A.NOPENDAFTARAN  
            LEFT JOIN web_sepkunjungan W ON W.TANGGAL_SEP = A.TANGGALDAFTAR AND W.NO_KARTU = A.NOPESERTA
            LEFT JOIN MITRAPASIEN C ON C.NOMITRA = A.NOMITRA  
            INNER JOIN PASIENMASUK D ON D.NOPENDAFTARAN = A.NOPENDAFTARAN  
            INNER JOIN POLIKLINIK E ON E.NOPOLI = D.NORUANG  
            LEFT JOIN PEMERIKSA F ON F.NOPEMERIKSA = D.NOPEMERIKSA  
            INNER JOIN RMPOLI G ON G.NOPENDAFTARAN = A.NOPENDAFTARAN  
            INNER JOIN PENJUALAN H ON H.NOPENDAFTARAN = A.NOPENDAFTARAN
            LEFT JOIN ETIKETLABEL I ON I.NOPENJUALAN = H.NOMORPENJUALAN 
                AND I.NORM = A.NORM 
                AND I.NAMABARANG = H.NAMABARANG 
            INNER JOIN FARMASI J ON H.KODEBARANG = J.KODEBARANG
            WHERE A.TANGGALDAFTAR >= ? AND A.TANGGALDAFTAR < ?
            AND C.NOMITRA IN (2, 3) AND J.KATEGORI = 'KRONIS'
            ORDER BY A.TANGGALDAFTAR, E.NOPOLI, F.NOPEMERIKSA, A.NORM, H.NAMABARANG";

        $results = DB::connection('sqlsrv')->select($query, [$startDate, date('Y-m-d', strtotime($startDate . ' +1 day'))]);

        // Kelompokkan hasil query
        $groups = collect($results)->groupBy(function ($row) {
            return $row->NORM . '|' . $row->NOSEP . '|' . $row->NAMAPOLI . '|' . $row->DPJP . '|' . date('Y-m-d', strtotime($row->TANGGALDAFTAR));
        })->map(function ($items) {
            return ['header' => $items->first(), 'items' => $items];
        })->filter(function ($group) use ($existingData) { // <-- Logika filter yang disesuaikan
            // Hanya tampilkan grup jika memiliki setidaknya satu item yang belum disimpan
            $hasUnsavedItem = false;
            foreach ($group['items'] as $item) {
                $itemKey = $item->NOSEP . '|' . $item->NORM . '|' . date('Y-m-d', strtotime($item->TANGGALDAFTAR)) . '|' . $item->NAMABARANG;
                if (!isset($existingData[$itemKey])) {
                    $hasUnsavedItem = true;
                    break; // Keluar dari loop jika satu item yang belum disimpan ditemukan
                }
            }
            return $hasUnsavedItem;
        });

        return view('laporan-kronis.index', compact('startDate', 'groups', 'nextNo', 'existingData'));
    }

    /**
     * Menyimpan data resep kronis yang dipilih.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'selected_rows' => 'required|array|min:1',
            'start_no' => 'required|integer|min:1',
        ]);

        $startNo = $validated['start_no'];
        $selectedRows = $validated['selected_rows'];

        // Kelompokkan data berdasarkan pasien, poli, dpjp, dan tanggal
        $groupedData = collect($selectedRows)->map(function ($row) {
            return explode('|', $row);
        })->groupBy(function ($data) {
            return $data[1] . '|' . $data[2] . '|' . $data[6] . '|' . $data[7] . '|' . $data[8]; // no_sep|norm|nama_poli|dpjp|tanggal_daftar
        });

        DB::connection('mysql_ims')->transaction(function () use ($groupedData, $startNo) {
            $currentNo = $startNo;
            foreach ($groupedData as $group) {
                foreach ($group as $data) {
                    $resep = new ResepKronis();
                    $resep->noresep = $currentNo;
                    $resep->no_sep = $data[1];
                    $resep->norm = $data[2];
                    $resep->nama_pasien = $data[3];
                    $resep->id_bpjs = $data[4];
                    $resep->asuransi = $data[5];
                    $resep->nama_poli = $data[6];
                    $resep->dpjp = $data[7];
                    $resep->tanggal_daftar = date('Y-m-d H:i:s', strtotime($data[8]));
                    $resep->nama_barang = $data[9];
                    $resep->jenis_obat = $data[10];
                    $resep->qty = $data[11];
                    $resep->rp_jual = $data[12];
                    $resep->zigna = $data[13];
                    $resep->save();
                }
                $currentNo++;
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }
}
