<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Rm4aController extends Controller
{
    /**
     * Memuat data dan menampilkan view untuk form RM4A.
     */
    public function load(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoPendaftaran' => 'required|string',
            'NoRM' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response('Parameter tidak valid.', 400);
        }

        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user'); // Menggunakan session user agar konsisten dengan form lain

        $data = [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'user' => $user,
        ];

        return view('rme.igd.forms.rm4a.index', $data);
    }
    /**
     * Mengambil riwayat penilaian RM4A untuk pasien.
     */
    public function getHistory(Request $request)
    {
        $validator = Validator::make($request->all(), ['noPendaftaran' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'No Pendaftaran diperlukan.'], 400);
        }

        try {
            $history = DB::connection('sqlsrv')
                ->table('RM4A')
                ->where('NOPENDAFTARAN', $request->noPendaftaran)
                ->orderBy('TGL', 'asc')->orderBy('JAM', 'asc')
                ->get(['COUNTER', 'TGL', 'JAM', 'TOTAL_SKOR', 'NAMA_PETUGAS']);

            return response()->json(['status' => 'success', 'data' => $history]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil riwayat: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil detail entri RM4A untuk diedit.
     */
    public function getDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noPendaftaran' => 'required|string',
            'counter' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tidak valid.'], 400);
        }

        try {
            $detail = DB::connection('sqlsrv')
                ->table('RM4A')
                ->where('NOPENDAFTARAN', $request->noPendaftaran)
                ->where('COUNTER', $request->counter)
                ->first();

            if ($detail) {
                return response()->json(['status' => 'success', 'data' => $detail]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil detail: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menyimpan atau memperbarui data form RM4A.
     */
    public function store(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $counter = $request->input('COUNTER');

        // Daftar semua kolom dari form
        $allColumns = [
            'TGL', 'JAM', 'RIWAYAT_JATUH_SKOR', 'KONDISI_KESEHATAN_SKOR', 'BANTUAN_AMBULANSI_SKOR',
            'TERAPI_IV_SKOR', 'GAYA_BERJALAN_SKOR', 'STATUS_MENTAL_SKOR', 'TOTAL_SKOR',
            'RR_ORIENTASI', 'RR_PASTIKAN_BEL', 'RR_RODA_TT', 'RR_POSISIKAN_TT', 'RR_NAIKKAN_PAGAR',
            'RR_LAMPU_TIDUR', 'RR_EDUKASI', 'RS_LAKUKAN_SEMUA', 'RS_TANDA_SEGITIGA', 'RS_TANDA_RESIKO',
            'RT_LAKUKAN_SEMUA', 'RT_1JAM', 'RT_TEMPATKAN_PASIEN', 'RT_ALAT_BANTU', 'RT_LIBATKAN_KELUARGA',
            'NAMA_PETUGAS'
        ];

        $data = [];
        foreach ($allColumns as $column) {
            // Jika kolom adalah checkbox, nilainya 1 jika ada di request, jika tidak 0.
            // Selain itu, ambil nilai dari input.
            if (strpos($column, 'RR_') === 0 || strpos($column, 'RS_') === 0 || strpos($column, 'RT_') === 0) {
                $data[$column] = $request->input($column, 0);
            } else {
                $data[$column] = $request->input($column);
            }
        }

        // Penanganan nilai khusus
        $user = session('user');
        $username = $user['username'] ?? 'default_user';
        $data['USER_ENTRY'] = $username;
        $data['NAMA_PETUGAS'] = $username; // Selalu gunakan user yang sedang login untuk update
        $data['TGLJAM_ENTRY'] = Carbon::now()->format('Y-m-d H:i:s');

        try {
            $isUpdate = !empty($counter);

            if ($isUpdate) {
                // Update data
                DB::connection('sqlsrv')->table('RM4A')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $counter)
                    ->update($data);
                $message = 'Data berhasil diperbarui.';
            } else {
                // Insert data baru
                $data['NOPENDAFTARAN'] = $noPendaftaran;
                
                // Dapatkan counter berikutnya
                $nextCounter = DB::connection('sqlsrv')->table('RM4A')
                                 ->where('NOPENDAFTARAN', $noPendaftaran)
                                 ->max('COUNTER') + 1;
                $data['COUNTER'] = $nextCounter;

                DB::connection('sqlsrv')->table('RM4A')->insert($data);
                $message = 'Data berhasil disimpan.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}

?>