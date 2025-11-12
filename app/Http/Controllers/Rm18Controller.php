<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Rm18Controller extends Controller
{
    // Menampilkan form RM18
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        return view('rme.igd.forms.rm18.index', compact('noPendaftaran', 'norm', 'user'));
    }

    // Mengambil daftar obat pasien dari tabel Penjualan
    public function getObatList(Request $request)
    {
        try {
            $noPendaftaran = $request->input('noPendaftaran');

            $data = DB::connection('sqlsrv')
                ->table('Penjualan')
                ->select('KodeBarang', 'NamaBarang', 'DosisObat', 'TanggalJual', DB::raw("CONVERT(VARCHAR, GETDATE(), 108) AS JamObat"))
                ->where('NoPendaftaran', $noPendaftaran)
                ->where('KodeBarang', '<>', 'FA000001')
                ->orderBy('NamaBarang')
                ->orderBy('TanggalJual')
                ->get();

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error loading RM18 obat list: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat daftar obat.'], 500);
        }
    }

    // Mengambil riwayat pemberian obat dari tabel RM18
    public function getHistory(Request $request)
    {
        try {
            $noPendaftaran = $request->input('noPendaftaran');

            $data = DB::connection('sqlsrv')
                ->table('RM18')
                ->select(
                    'COUNTER',
                    'TGLOBAT as TGL_OBAT',
                    'JAMOBAT as JAM_OBAT',
                    'TGLBERI as TGL_PEMBERIAN',
                    'JAMBERI as JAM_PEMBERIAN',
                    'NAMABARANG as NAMA_OBAT',
                    'DOSIS',
                    'FREKUENSI',
                    'RUTE as CARA_PEMBERIAN',
                    'D_CHECK',
                    'USER_ENTRI as NAMA_PERAWAT',
                    'TTD_PENERIMA'
                )
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('COUNTER', 'desc')
                ->get();

            // Encode TTD to Base64 for direct display in <img> tag
            $data->each(function ($item) {
                if (!empty($item->TTD_PENERIMA)) {
                    // Langkah 1: Konversi hex string (jika ada) ke biner.
                    // Ini adalah langkah krusial yang meniru RM8A.
                    $binaryData = $item->TTD_PENERIMA;
                    if (is_string($binaryData) && strpos($binaryData, '0x') === 0) { // Cek jika ada prefix '0x'
                        $binaryData = hex2bin(substr($binaryData, 2));
                    } elseif (is_string($binaryData) && ctype_xdigit($binaryData)) { // Fallback: Cek jika seluruh string adalah hex
                        $binaryData = hex2bin($binaryData);
                    }

                    // Langkah 2: Setelah menjadi biner, encode ke Base64 untuk pratinjau di tabel.
                    $item->TTD_PENERIMA_BASE64 = base64_encode($binaryData);
                } else {
                    $item->TTD_PENERIMA_BASE64 = null;
                }
                // Hapus data asli untuk mengurangi ukuran response JSON.
                unset($item->TTD_PENERIMA);
            });

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error loading RM18 history: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat riwayat pemberian obat.'], 500);
        }
    }

    // Menyimpan data baru ke tabel RM18
    public function store(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $counter = $request->input('counter'); // Get counter for update logic
            $user = session('user');

            // Validasi dasar
            if (empty($noPendaftaran) || empty($request->input('NAMA_OBAT'))) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap.'], 400);
            }
            
            // Proses Tanda Tangan
            $ttd_binary = null;
            if ($request->has('TTD_PENERIMA') && !empty($request->input('TTD_PENERIMA'))) {
                $base64_image = $request->input('TTD_PENERIMA');
                if (strpos($base64_image, 'data:image/jpeg;base64,') === 0) {
                    $base64_image = str_replace('data:image/jpeg;base64,', '', $base64_image);
                    $ttd_binary = base64_decode($base64_image);
                }
            }

            $dataToSave = [
                'TGLOBAT' => $request->input('TGL_OBAT') ? Carbon::parse($request->input('TGL_OBAT'))->format('Y-m-d H:i:s') : null,
                'JAMOBAT' => $request->input('JAM_OBAT'),
                'KODEBARANG' => $request->input('KODEBARANG'),
                'NAMABARANG' => $request->input('NAMA_OBAT'),
                'DOSIS' => $request->input('DOSIS'),
                'FREKUENSI' => $request->input('FREKUENSI'),
                'RUTE' => $request->input('CARA_PEMBERIAN'),
                'TGLBERI' => $request->input('TGL_PEMBERIAN') ? Carbon::parse($request->input('TGL_PEMBERIAN'))->format('Y-m-d H:i:s') : null,
                'JAMBERI' => $request->input('JAM_PEMBERIAN'),
                'KETERANGAN' => $request->input('KETERANGAN'),
                'USER_ENTRI' => $user['username'] ?? 'SYSTEM',
                'TGLUPDATED' => Carbon::now(),
                'D_CHECK' => $request->input('D_CHECK'),
            ];

            if ($ttd_binary) {
                $dataToSave['TTD_PENERIMA'] = $ttd_binary;
            }

            if (empty($counter)) {
                // --- INSERT ---
                // Dapatkan COUNTER baru
                $maxCounter = DB::connection('sqlsrv')
                    ->table('RM18')
                    ->where('NoPendaftaran', $noPendaftaran)
                    ->max('COUNTER');
                $newCounter = ($maxCounter ?? 0) + 1;

                $dataToSave['NOPENDAFTARAN'] = $noPendaftaran;
                $dataToSave['COUNTER'] = $newCounter;

                DB::connection('sqlsrv')->table('RM18')->insert($dataToSave);
                $message = 'Data berhasil disimpan.';
            } else {
                // --- UPDATE ---
                DB::connection('sqlsrv')->table('RM18')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $counter)
                    ->update($dataToSave);
                $message = 'Data berhasil diperbarui.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('Error storing RM18 data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    // Menghapus data dari tabel RM18
    public function destroy(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $counter = $request->input('counter');

            if (empty($noPendaftaran) || empty($counter)) {
                return response()->json(['status' => 'error', 'message' => 'Parameter tidak lengkap.'], 400);
            }

            $deleted = DB::connection('sqlsrv')
                ->table('RM18')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counter)
                ->delete();

            if ($deleted) {
                return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan untuk dihapus.'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting RM18 data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menghapus data.'], 500);
        }
    }

    // Menampilkan gambar tanda tangan (meniru RM8A)
    public function showSignature($noPendaftaran, $counter)
    {
        $imageData = DB::connection('sqlsrv')
            ->table('RM18')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->where('COUNTER', $counter)
            ->value('TTD_PENERIMA');

        if (empty($imageData)) {
            // Jika tidak ada gambar, kirim response 404
            return response('Image not found', 404);
        }

        // Ini adalah bagian terpenting yang meniru logika RM8A untuk kanvas.
        // Driver database untuk SQL Server sering mengembalikan tipe data 'image'
        // sebagai string heksadesimal yang diawali dengan '0x'.
        $binaryData = $imageData;
        if (is_string($imageData) && strpos($imageData, '0x') === 0) { // Cek jika ada prefix '0x'
            $binaryData = hex2bin(substr($imageData, 2));
        } elseif (is_string($imageData) && ctype_xdigit($imageData)) { // Fallback: Cek jika seluruh string adalah hex
            $binaryData = hex2bin($imageData);
        }

        // Kembalikan data biner sebagai respons gambar.
        // Browser akan menafsirkannya sebagai file gambar JPEG.
        return response($binaryData, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}