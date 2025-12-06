<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LukaReedaController extends Controller
{
    /**
     * Memuat form Penilaian Luka REEDA dengan data yang ada.
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

        // Ambil data yang sudah ada dari database
        $data = DB::connection('sqlsrv')->table('LUKAREEDA')->where('NOPENDAFTARAN', $noPendaftaran)->first();

        // Ambil data user yang sedang login
        $user = session('user');

        // Tentukan interpretasi berdasarkan TOTALSKOR
        $interpretationText = '';
        $rtlText = '';
        if ($data && isset($data->TOTALSKOR)) {
            $totalSkor = (int)$data->TOTALSKOR;
            if ($totalSkor >= 0 && $totalSkor <= 3) {
                $interpretationText = 'Normal';
                $rtlText = "- Perawatan Luka rutin\n- Antibiotik diberikan melihat hasil leukosit";
            } else if ($totalSkor >= 4 && $totalSkor <= 6) {
                $interpretationText = 'Waspada';
                $rtlText = "- Perawatan luka khusus\n- Antibiotik Broad Spectrum";
            } else if ($totalSkor >= 7) {
                $interpretationText = 'Curiga Infeksi Luka';
                $rtlText = "- Kultur luka\n- Antiobiotik Broad Spectrum\n- Antibiotik spesifik setelah hasil kultir keluar";
            }
        }


        $viewData = [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'data' => $data, // Kirim data (bisa null jika belum ada)
            'interpretationText' => $interpretationText,
            'rtlText' => $rtlText,
            'user' => $user,
        ];

        return view("rme.igd.forms.lukareeda.index", $viewData);
    }

    /**
     * Menyimpan atau memperbarui data form Penilaian Luka REEDA.
     */
    public function store(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');

        // Validasi dasar
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'JENISLUKA' => 'nullable|string|max:50',
            'UNIT' => 'nullable|string|max:50',
            'TOTALSKOR' => 'nullable|string|max:10',
            'CATATANKLINIS' => 'nullable|string|max:200',
            'INTERPRETASI' => 'nullable|string|max:50', // Tambahkan validasi untuk interpretasi
            'RTLLAIN_KET' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak valid.'], 400);
        }

        // Daftar semua kolom BIT untuk di-handle sebagai checkbox/radio
        $bitColumns = [
            'R_TDKMERAH0', 'R_MERAH1', 'R_MERAH2', 'R_MERAH3',
            'E_TDKEDEMA0', 'E_RINGAN1', 'E_SEDANG2', 'E_BERAT3',
            'E_TDKMEMAR0', 'E_MEMAR1', 'E_MEMAR2', 'E_MEMAR3',
            'D_TDKCAIRAN0', 'D_SEROSASDKT1', 'D_SERSDG2', 'D_PURULEN3',
            'A_LUKAMENYATU0', 'A_TERBUKA1', 'A_TERBUKA2', 'A_TERBUKA3',
        ];

        $data = $request->only('NOPENDAFTARAN', 'JENISLUKA', 'UNIT', 'TOTALSKOR', 'CATATANKLINIS', 'INTERPRETASI', 'RTLLAIN_KET');

        // Proses kolom BIT
        foreach ($bitColumns as $column) {
            $data[$column] = $request->has($column) ? 1 : 0;
        }

        // Tambahkan informasi user dan waktu
        $data['TGLJAM_ENTRY'] = now();
        $loggedInUser = Auth::user();
        $sessionUser = session('user');
        $data['USER_ENTRY'] = $loggedInUser->username ?? $sessionUser['username'] ?? 'SYSTEM';

        try {
            // Cek apakah data sudah ada
            $existingData = DB::connection('sqlsrv')->table('LUKAREEDA')->where('NOPENDAFTARAN', $noPendaftaran)->first();

            if ($existingData) {
                // Lakukan UPDATE
                DB::connection('sqlsrv')->table('LUKAREEDA')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->update($data);
                $message = 'Data Penilaian Luka REEDA berhasil diperbarui.';
            } else {
                // Lakukan INSERT
                DB::connection('sqlsrv')->table('LUKAREEDA')->insert($data);
                $message = 'Data Penilaian Luka REEDA berhasil disimpan.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            // Kirim pesan error yang lebih deskriptif
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}