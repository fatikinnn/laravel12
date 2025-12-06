<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SkriningSepsisController extends Controller
{
    /**
     * Memuat form Skrining Sepsis dengan data yang ada.
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
        $data = DB::connection('sqlsrv')->table('SKRINSEPSIS')->where('NOPENDAFTARAN', $noPendaftaran)->first();

        // Ambil data user yang sedang login
        $user = session('user');

        $viewData = [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'data' => $data, // Kirim data (bisa null jika belum ada)
            'user' => $user,
        ];

        return view("rme.igd.forms.skrining-sepsis.index", $viewData);
    }

    /**
     * Menyimpan atau memperbarui data form Skrining Sepsis.
     */
    public function store(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');

        // Validasi dasar
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'PENURUNANSADAR' => 'nullable',
            'TAKIPNEU' => 'nullable',
            'SISTOLIK' => 'nullable',
            'KESIMPULAN' => 'nullable|string|max:50',
            'RTL' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak valid.'], 400);
        }

        $data = $request->only('NOPENDAFTARAN', 'KESIMPULAN', 'RTL');

        // Proses kolom BIT dari checkbox
        $bitColumns = ['PENURUNANSADAR', 'TAKIPNEU', 'SISTOLIK'];
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
            $existingData = DB::connection('sqlsrv')->table('SKRINSEPSIS')->where('NOPENDAFTARAN', $noPendaftaran)->first();

            if ($existingData) {
                // Lakukan UPDATE
                DB::connection('sqlsrv')->table('SKRINSEPSIS')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->update($data);
                $message = 'Data Skrining Sepsis berhasil diperbarui.';
            } else {
                // Lakukan INSERT
                DB::connection('sqlsrv')->table('SKRINSEPSIS')->insert($data);
                $message = 'Data Skrining Sepsis berhasil disimpan.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            // Kirim pesan error yang lebih deskriptif
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}