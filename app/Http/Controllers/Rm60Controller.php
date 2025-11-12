<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class Rm60Controller extends Controller
{
    /**
     * Load the RM60 form view.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = Session::get('user');

        // Pass all patient details from the request to the view
        $patientDetails = $request->all();

        return view('rme.igd.forms.rm60.index', compact('noPendaftaran', 'norm', 'user', 'patientDetails'));
    }

    /**
     * Fetch monitoring history for RM60.
     */
    public function history(Request $request)
    {
        $noPendaftaran = $request->input('noPendaftaran');

        try {
            $data = DB::connection('sqlsrv')
                ->table('RM60')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('COUNTER')
                ->get()
                ->map(function ($item) {
                    // Pastikan WAKTU ditangani dengan benar
                    if (!empty($item->WAKTU)) {
                        try {
                            // Coba parsing dengan Carbon dan format ke string standar
                            $item->WAKTU = Carbon::parse($item->WAKTU)->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            // Jika parsing gagal, biarkan nilai aslinya tetapi log error jika perlu
                            // \Log::error("RM60 WAKTU parsing failed for NOPENDAFTARAN {$item->NOPENDAFTARAN}: {$item->WAKTU}");
                        }
                    }
                    return $item;
                });

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store or update monitoring data for RM60.
     */
    public function store(Request $request)
    {
        $noPendaftaran = $request->input('nopendaftaran');
        $monitoringData = $request->input('monitoring', []);
        $user = Session::get('user.username', 'SYSTEM');

        if (empty($noPendaftaran) || empty($monitoringData)) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap.'], 400);
        }

        try {
            DB::connection('sqlsrv')->beginTransaction();

            foreach ($monitoringData as $item) {
                $counter = $item['COUNTER'] ?? 0;

                // If it's a new row (COUNTER is 0 or null), get the next counter
                if (empty($counter)) {
                    $maxCounter = DB::connection('sqlsrv')
                        ->table('RM60')
                        ->where('NOPENDAFTARAN', $noPendaftaran)
                        ->max('COUNTER');
                    $counter = ($maxCounter ?? 0) + 1;
                }

                // Prepare data for the stored procedure
                $waktu = !empty($item['WAKTU']) ? Carbon::parse($item['WAKTU'])->format('Y-m-d H:i:s') : null;
                $kesadaran = $item['KESADARAN'] ?? '';
                $td = $item['TD'] ?? '';
                $nadi = $item['NADI'] ?? '';
                $rr = $item['RR'] ?? '';
                $suhu = $item['SUHU'] ?? '';
                $spo = $item['SPO'] ?? '';
                $urine = $item['URINE'] ?? '';
                $terapi = $item['TERAPI'] ?? '';
                $konsul = $item['KONSUL'] ?? '';

                // Execute the stored procedure
                $sql = "EXEC SaveRM60SP_fatikin ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
                DB::connection('sqlsrv')->statement($sql, [
                    $noPendaftaran, $counter, $waktu, $kesadaran, $td, $nadi, $rr, $suhu, $spo, $urine, $terapi, $konsul, $user
                ]);
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => 'Data monitoring berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a monitoring entry from RM60.
     */
    public function destroy(Request $request)
    {
        $noPendaftaran = $request->input('nopendaftaran');
        $counter = $request->input('counter');

        if (empty($noPendaftaran) || empty($counter)) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tidak valid.'], 400);
        }

        try {
            $deleted = DB::connection('sqlsrv')
                ->table('RM60')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counter)
                ->delete();

            if ($deleted) {
                return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan untuk dihapus.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}