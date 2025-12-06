<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rm3aController extends Controller
{
    /**
     * Memuat form RM3A dengan data yang ada jika sudah pernah diisi.
     */
    public function load(Request $request)
    {
        // Tambahkan parameter untuk mode readonly
        $readonly = $request->has('readonly') && $request->input('readonly') == 'true';

        $validator = Validator::make($request->all(), [
            'NoPendaftaran' => 'required|string',
            'NoRM' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response('Parameter tidak valid.', 400);
        }

        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');

        // Ambil data pasien untuk kalkulasi otomatis di form
        $patient = DB::connection('sqlsrv')->table('dip')
            ->where('NoPendaftaran', $noPendaftaran)
            ->first();

        // Ambil data RM3A yang sudah ada dari database
        $rm3a = DB::connection('sqlsrv')->table('rm3a')->where('NOPENDAFTARAN', $noPendaftaran)->first();

        // Ambil data user yang sedang login
        $user = session('user'); // Ambil data user dari session yang Anda buat

        $data = [
            'patient' => $patient,
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'rm3a' => $rm3a, // Kirim data rm3a (bisa null jika belum ada)
            'user' => $user,
            'readonly' => $readonly, // Kirim flag readonly ke view
        ];

        // Kembalikan view parsial
        return view("rme.igd.forms.rm3a.index", $data);
    }

    /**
     * Menyimpan atau memperbarui data form RM3A.
     */
    public function storeOrUpdate(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');

        // Daftar semua kolom yang mungkin dari form
        // Disederhanakan dan diurutkan untuk kejelasan
        $columns = [
            'NOPENDAFTARAN', 'TGL_TRIAGE', 'JAM_TRIAGE', 'PETUGAS', 'KELUHAN',
            'COV_USIA_KAJI', 'COV_MAKS_KAJI', 'COV_JK_KAJI', 'COV_GEJA_KAJI', 'COV_RASIO_KAJI', 'COV_USIA_SKOR',
            'COV_MAKS_SKOR', 'COV_JK_SKOR', 'COV_GEJA_SKOR', 'COV_RASIO_SKOR',
            'PACS2_SADAR', 'PACS3_RESPON', 'PACS4_RESPON', 'PACS1_MENINGGAL',
            'PACS1_TD_SIS', 'PACS1_TD_DIA', 'PACS1_NADI', 'PACS2_NAFAS', 'PACS2_TEMP', 'PACS3_SATURASI',
            'PACS3_NYERI', 'PACS4_BB', 'PACS4_TB', 'BMI',
            'WORT_SKOR0_SADAR', 'WORT_SKOR3_SELAIN', 'WORT_SKOR0_100', 'WORT_SKOR2_99', 'WORT_SKOR0_101',
            'WORT_SKOR1_102', 'WORT_SKOR0_19', 'WORT_SKOR1_20', 'WORT_SKOR2_22', 'WORT_SKOR0_35',
            'WORT_SKOR3_35', 'WORT_SKOR0_96', 'WORT_SKOR1_94', 'WORT_SKOR2_92', 'WORT_SKOR3_92',
            'WORT_SKOR0_TOT', 'WORT_SKOR1_TOT', 'WORT_SKOR2_TOT', 'WORT_SKOR3_TOT',
            'CAT_KHUSUS', 'CARMAS_JALAN', 'CARMAS_BRAND', 'CARMAS_KURSI', 'CARMAS_GENDONG',
            'TERPASANG', 'DATANG_SDR', 'DATANG_POL', 'DATANG_RUJUKAN', 'DATANG_RUKETERANGAN',
            'DATANG_JEMPUT', 'DATANG_JEMKETERANGAN', 'KEND_AMBL', 'KEND_LAIN', 'IDPENGANTAR_NAMA',
            'IDNOTELP', 'KASUS_NONTRAU', 'KASUS_TRAUMA', 'KASUS_TRAUMALAIN', 'KASUS_TRAUMAKET',
            'KLL_TUNGGAL', 'KLL_LAIN', 'TKEJADIAN', 'TGLKEJADIAN', 'JAMKEJADIAN',
            'JATUH_TINGGI', 'JATUH_LUKA', 'JATUH_KETERANGAN'
        ];

        $data = [];
        foreach ($columns as $column) {
            // Untuk checkbox, jika tidak ada di request, beri nilai 0, jika ada beri nilai 1
            $checkboxColumns = [
                'PACS2_SADAR', 'PACS3_RESPON', 'PACS4_RESPON', 'PACS1_MENINGGAL',
                'WORT_SKOR0_SADAR', 'WORT_SKOR3_SELAIN', 'WORT_SKOR0_100', 'WORT_SKOR2_99',
                'WORT_SKOR0_101', 'WORT_SKOR1_102', 'WORT_SKOR0_19', 'WORT_SKOR1_20',
                'WORT_SKOR2_22', 'WORT_SKOR0_35', 'WORT_SKOR3_35', 'WORT_SKOR0_96',
                'WORT_SKOR1_94', 'WORT_SKOR2_92', 'WORT_SKOR3_92', 'WORT_SKOR0_TOT',
                'WORT_SKOR1_TOT', 'WORT_SKOR3_TOT', 'WORT_SKOR2_TOT', 'CARMAS_JALAN',
                'CARMAS_BRAND', 'CARMAS_KURSI', 'CARMAS_GENDONG', 'DATANG_SDR',
                'DATANG_POL', 'DATANG_RUJUKAN', 'DATANG_JEMPUT', 'KEND_AMBL',
                'KASUS_NONTRAU', 'KASUS_TRAUMA', 'KLL_TUNGGAL', 'JATUH_TINGGI', 'JATUH_LUKA'
            ];

            if (in_array($column, $checkboxColumns)) {
                $data[$column] = $request->has($column) ? 1 : 0;
            } elseif ($request->filled($column)) { // `filled` checks if the input is present and not empty
                $data[$column] = $request->input($column);
            } else {
                // Jika input tidak diisi (kosong atau null), set sebagai string kosong untuk kolom teks
                // atau null untuk tipe data lain yang mengizinkannya.
                $data[$column] = '';
            }
        }

        // Penanganan nilai khusus
        $data['TGL_ENTRY'] = now();

        // Mengambil user dengan fallback untuk mencegah error sesi habis
        $loggedInUser = Auth::user();
        $sessionUser = session('user');

        // Prioritaskan user dari Auth, fallback ke session, lalu ke default
        $data['USER_ENTRY'] = $loggedInUser->username ?? $sessionUser['username'] ?? null;

        $data['JAM_TRIAGE'] = $request->input('JAM_TRIAGE') ? Carbon::parse($request->input('JAM_TRIAGE'))->format('H:i:s') : null;
        // Pastikan tanggal dan waktu opsional di-set ke null jika kosong
        $data['TGLKEJADIAN'] = $request->filled('TGLKEJADIAN') ? $request->input('TGLKEJADIAN') : null;
        $data['JAMKEJADIAN'] = $request->filled('JAMKEJADIAN') ? Carbon::parse($request->input('JAMKEJADIAN'))->format('H:i:s') : null;

        // Untuk operasi UPDATE, kita hanya ingin memperbarui field yang ada di request. Filter nilai kosong.
        $allowedNulls = ['TGLKEJADIAN', 'JAMKEJADIAN', 'JAM_TRIAGE'];
        $updateData = array_filter($data, fn($value, $key) => $value !== '' || in_array($key, $allowedNulls) || is_numeric($value), ARRAY_FILTER_USE_BOTH);
        
        try {
            // Cek apakah data sudah ada
            $existingRm3a = DB::connection('sqlsrv')->table('rm3a')->where('NOPENDAFTARAN', $noPendaftaran)->first();

            if ($existingRm3a) {
                // Lakukan UPDATE
                DB::connection('sqlsrv')->table('rm3a')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->update($updateData);
                $message = 'Data RM3A berhasil diperbarui.';
            } else {
                // Lakukan INSERT
                DB::connection('sqlsrv')->table('rm3a')->insert($data);
                $message = 'Data RM3A berhasil disimpan.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            // Kirim pesan error yang lebih deskriptif
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}