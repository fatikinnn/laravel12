<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Rm16bController extends Controller
{
    /**
     * Memuat view utama untuk form RM16B.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        // Ambil semua data pasien yang sudah dikirim oleh AJAX dari request.
        // Ini mengikuti pola yang lebih baik seperti di Rm3bController.
        $patientDetails = $request->all();

        return view('rme.igd.forms.rm16b.index', compact('noPendaftaran', 'norm', 'user', 'patientDetails'));
    }

    /**
     * Mengambil riwayat monitoring infus.
     */
    public function history(Request $request)
    {
        try {
            $noPendaftaran = $request->input('noPendaftaran');

            $history = DB::connection('sqlsrv')
                ->table('RM16B')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('COUNTER', 'asc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $history]);
        } catch (\Exception $e) {
            Log::error('Error loading RM16B history: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat riwayat.'], 500);
        }
    }

    /**
     * Menyimpan atau memperbarui data monitoring infus.
     */
    public function store(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $monitoringData = $request->input('monitoring', []);
            $user = session('user.username', 'SYSTEM');

            if (empty($noPendaftaran) || empty($monitoringData)) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap.'], 400);
            }

            DB::connection('sqlsrv')->beginTransaction();

            $maxCounter = DB::connection('sqlsrv')
                ->table('RM16B')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->max('COUNTER') ?: 0;

            foreach ($monitoringData as $item) {
                $counter = isset($item['COUNTER']) ? intval($item['COUNTER']) : 0;

                $waktu = !empty($item['WAKTU']) ? Carbon::parse($item['WAKTU'])->format('Y-m-d H:i:s') : null;

                $dataToUpsert = [
                    'WAKTU' => $waktu,
                    'CAIRAN' => $item['CAIRAN'] ?? null,
                    'TTS' => $item['TTS'] ?? null,
                    'KONDISI' => $item['KONDISI'] ?? null,
                    'TINDAKAN' => $item['TINDAKAN'] ?? null,
                    'NM_PERAWAT' => $item['NM_PERAWAT'] ?? $user,
                    'NM_PASIEN' => $item['NM_PASIEN'] ?? null,
                    'USERENTRI' => $user,
                ];

                if ($counter == 0) { // Insert
                    $maxCounter++;
                    $dataToUpsert['NOPENDAFTARAN'] = $noPendaftaran;
                    $dataToUpsert['COUNTER'] = $maxCounter;
                    DB::connection('sqlsrv')->table('RM16B')->insert($dataToUpsert);
                } else { // Update
                    DB::connection('sqlsrv')
                        ->table('RM16B')
                        ->where('NOPENDAFTARAN', $noPendaftaran)
                        ->where('COUNTER', $counter)
                        ->update($dataToUpsert);
                }
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => 'Data monitoring infus berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error saving RM16B data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus baris data monitoring infus.
     */
    public function destroy(Request $request)
    {
        try {
            DB::connection('sqlsrv')
                ->table('RM16B')
                ->where('NOPENDAFTARAN', $request->input('nopendaftaran'))
                ->where('COUNTER', $request->input('counter'))
                ->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error deleting RM16B data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}