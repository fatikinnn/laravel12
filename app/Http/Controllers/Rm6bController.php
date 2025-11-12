<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rm6bController extends Controller
{
    /**
     * Memuat data dan menampilkan view untuk form RM6B.
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
        $user = session('user');

        $data = [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'user' => $user,
        ];

        return view('rme.igd.forms.rm6b.index', $data);
    }

    /**
     * Mengambil riwayat RM6B untuk ditampilkan di tabel.
     */
    public function getHistory(Request $request)
    {
        $validator = Validator::make($request->all(), ['noPendaftaran' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'No Pendaftaran diperlukan.'], 400);
        }

        try {
            $history = DB::connection('sqlsrv')
                ->table('RM6B')
                ->where('NOPENDAFTARAN', $request->noPendaftaran)
                ->select('COUNTER', 'TGL', 'JAM', 'TOTAL_SKOR', 'NAMA_PETUGAS')
                ->orderBy('TGL', 'asc')
                ->orderBy('JAM', 'asc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $history]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil riwayat: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil detail satu entri RM6B untuk diedit.
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
                ->table('RM6B')
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
     * Menyimpan, memperbarui, atau menghapus data RM6B.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'COUNTER' => 'nullable|integer', // Counter bisa null untuk data baru
            'TGL' => 'required|date',
            'JAM' => 'required|date_format:H:i',
            // Tambahkan validasi lain jika perlu
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak valid.', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::connection('sqlsrv')->beginTransaction();

            $noPendaftaran = $request->input('NOPENDAFTARAN');
            $counter = $request->input('COUNTER'); // Bisa jadi null atau berisi nilai
            $user = session('user')['username'] ?? 'SYSTEM';

            // Helper function untuk mengubah string kosong menjadi null
            $toNull = fn($value) => $value === '' ? null : $value;

            $dataToSave = [
                'NOPENDAFTARAN' => $noPendaftaran, // NOPENDAFTARAN akan ditambahkan lagi nanti
                'TGL' => Carbon::parse($request->input('TGL'))->format('Y-m-d'),
                'JAM' => Carbon::parse($request->input('JAM'))->format('H:i:s'),
                'RIWAYAT_JATUH_SKOR' => $toNull($request->input('RIWAYAT_JATUH_SKOR')),
                'RIWAYAT_JATUH_SKOR_2' => $toNull($request->input('RIWAYAT_JATUH_SKOR_2')),
                'STATUS_MENTAL_SKOR' => $toNull($request->input('STATUS_MENTAL_SKOR')),
                'STATUS_MENTAL_SKOR_2' => $toNull($request->input('STATUS_MENTAL_SKOR_2')),
                'STATUS_MENTAL_SKOR_3' => $toNull($request->input('STATUS_MENTAL_SKOR_3')),
                'PENGELIHATAN_SKOR' => $toNull($request->input('PENGELIHATAN_SKOR')),
                'PENGELIHATAN_SKOR_2' => $toNull($request->input('PENGELIHATAN_SKOR_2')),
                'PENGELIHATAN_SKOR_3' => $toNull($request->input('PENGELIHATAN_SKOR_3')),
                'KEBIASAAN_BERKEMIH_SKOR' => $toNull($request->input('KEBIASAAN_BERKEMIH_SKOR')), // Hanya satu kolom
                'TRANSFER_TT_SKOR' => $toNull($request->input('TRANSFER_TT_SKOR')),
                'TRANSFER_TT_SKOR_2' => $toNull($request->input('TRANSFER_TT_SKOR_2')),
                'TRANSFER_TT_SKOR_3' => $toNull($request->input('TRANSFER_TT_SKOR_3')),
                'TRANSFER_TT_SKOR_4' => $toNull($request->input('TRANSFER_TT_SKOR_4')),
                'MOBILITAS_SKOR' => $toNull($request->input('MOBILITAS_SKOR')),
                'MOBILITAS_SKOR2' => $toNull($request->input('MOBILITAS_SKOR2')),
                'MOBILITAS_SKOR3' => $toNull($request->input('MOBILITAS_SKOR3')),
                'MOBILITAS_SKOR4' => $toNull($request->input('MOBILITAS_SKOR4')),
                'TOTAL_TRANFER_MOBILITAS_SKOR' => $request->input('TOTAL_TRANFER_MOBILITAS_SKOR', '0'),
                'TOTAL_SKOR' => $request->input('TOTAL_SKOR', '0'),
                'KESIMPULAN_RR' => $request->input('KESIMPULAN_RR') ? 1 : 0,
                'KESIMPULAN_RS' => $request->input('KESIMPULAN_RS') ? 1 : 0,
                'KESIMPULAN_RT' => $request->input('KESIMPULAN_RT') ? 1 : 0,
                'NAMA_PETUGAS' => $user // Selalu gunakan user saat ini sebagai petugas yang mengubah
            ];

            // Cek apakah ini operasi update atau insert
            if (!empty($counter)) {
                // Ini adalah UPDATE
                // Tambahkan field yang hanya ada di update
                $dataToSave['USER_ENTRY'] = $user;
                $dataToSave['TGLJAM_ENTRY'] = now()->format('Y-m-d H:i:s');

                DB::connection('sqlsrv')->table('RM6B')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $counter)
                    ->update($dataToSave);
                $message = 'Data RM6B berhasil diperbarui.';
            } else {
                // Ini adalah INSERT
                // 1. Dapatkan counter terakhir dari DB dan tambahkan 1
                $lastCounter = DB::connection('sqlsrv')->table('RM6B')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->max('COUNTER');
                $newCounter = ($lastCounter ?? 0) + 1;

                // 2. Tambahkan field yang hanya ada di insert
                $dataToSave['COUNTER'] = $newCounter;
                $dataToSave['USER_ENTRY'] = $user;
                $dataToSave['TGLJAM_ENTRY'] = now()->format('Y-m-d H:i:s');

                // 3. Lakukan insert
                DB::connection('sqlsrv')->table('RM6B')->insert($dataToSave);
                $message = 'Data RM6B berhasil disimpan.';
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }
}