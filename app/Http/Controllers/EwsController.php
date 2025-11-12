<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EwsController extends Controller
{
    /**
     * Load the EWS form view.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        return view('rme.igd.forms.ews.index', compact('noPendaftaran', 'norm', 'user'));
    }

    /**
     * Fetch all EWS records for a given registration number.
     */
    public function history(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $data = DB::connection('sqlsrv')
                ->table('EWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('COUNTER', 'asc')
                ->get();

            // Format date and time fields
            $data->transform(function ($item) {
                if (!empty($item->TGL)) {
                    $item->TGL = Carbon::parse($item->TGL)->format('Y-m-d');
                }
                if (!empty($item->JAM)) {
                    $item->JAM = Carbon::parse($item->JAM)->format('H:i');
                }
                return $item;
            });

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error fetching EWS history: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat riwayat EWS.'], 500);
        }
    }

    /**
     * Map field names to their scores for EWS.
     */
    private function getScoreMap()
    {
        return [
            'SUHU_37' => 2,
            'SUHU_35' => 1,
            'SUHU_30' => 2,
            'FREKUENSI_NAFAS_81' => 2,
            'FREKUENSI_NAFAS_61' => 1,
            'FREKUENSI_NAFAS_26' => 1,
            'FREKUENSI_NAFAS_25' => 2,
            'NADI_181' => 2,
            'NADI_161' => 1,
            'NADI_60' => 1,
            'NADI_59' => 2,
            'SATURASI_OKSIGEN_92' => 1,
            'SATURASI_OKSIGEN_87' => 2,
            'KESADARAN_GELISAH' => 1,
            'KESADARAN_LETARGIS' => 2,
        ];
    }

    /**
     * Get all possible EWS fields to ensure completeness.
     */
    private function getAllEwsFields()
    {
        return [
            'SUHU_37', 'SUHU_36', 'SUHU_35', 'SUHU_30',
            'FREKUENSI_NAFAS_81', 'FREKUENSI_NAFAS_61', 'FREKUENSI_NAFAS_40', 'FREKUENSI_NAFAS_26', 'FREKUENSI_NAFAS_25',
            'MERINTIH',
            'NADI_181', 'NADI_161', 'NADI_100', 'NADI_60', 'NADI_59',
            'SATURASI_OKSIGEN_93', 'SATURASI_OKSIGEN_92', 'SATURASI_OKSIGEN_87',
            'KESADARAN_AKTIF', 'KESADARAN_GELISAH', 'KESADARAN_LETARGIS',
            'KEJANG', 'GLUKOSA',
            'KESIMPULAN_HIJAU', 'KESIMPULAN_KUNING', 'KESIMPULAN_MERAH', 'KESIMPULAN_BIRU'
        ];
    }

    /**
     * Store or update EWS data.
     */
    public function store(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $norm = $request->input('norm');
            $user = data_get(session('user'), 'username', 'system');
            $dataToProcess = $request->input('assessments', []);

            DB::connection('sqlsrv')->beginTransaction();

            foreach ($dataToProcess as $data) {
                $counter = $data['COUNTER'];

                $recordData = [
                    'NOPENDAFTARAN' => $noPendaftaran,
                    'USER_ENTRY' => $user,
                    'TGLJAM_ENTRY' => now(),
                    'COUNTER' => $counter,
                    'TGL' => !empty($data['TGL']) ? Carbon::parse($data['TGL'])->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                    'JAM' => !empty($data['JAM']) ? Carbon::parse($data['JAM'])->format('H:i') : Carbon::now()->format('H:i'),
                    'NAMA_PPA' => $data['NAMA_PPA'] ?? $user,
                ];

                $scoreMap = $this->getScoreMap();
                $totalScore = 0;

                $allEwsFields = $this->getAllEwsFields();

                foreach ($allEwsFields as $field) {
                    if (!isset($recordData[$field])) {
                        $recordData[$field] = '0';
                    }
                }

                foreach ($allEwsFields as $field) {
                    $isChecked = isset($data[$field]) && $data[$field] == '1';
                    $recordData[$field] = $isChecked ? '1' : '0';
                    if ($isChecked && isset($scoreMap[$field])) {
                        $totalScore += $scoreMap[$field];
                    }
                }

                $recordData['TOTAL_SKOR'] = $totalScore;

                // Logic for conclusion based on score and critical conditions
                $isCritical = (isset($data['MERINTIH']) && $data['MERINTIH'] == '1') ||
                              (isset($data['KEJANG']) && $data['KEJANG'] == '1') ||
                              (isset($data['GLUKOSA']) && $data['GLUKOSA'] == '1');

                if ($isCritical) {
                    $recordData['KESIMPULAN_BIRU'] = '1';
                    $recordData['KESIMPULAN_HIJAU'] = '0';
                    $recordData['KESIMPULAN_KUNING'] = '0';
                    $recordData['KESIMPULAN_MERAH'] = '0';
                } else {
                    $recordData['KESIMPULAN_BIRU'] = '0';
                    $recordData['KESIMPULAN_HIJAU'] = ($totalScore == 0) ? '1' : '0';
                    $recordData['KESIMPULAN_KUNING'] = ($totalScore == 1) ? '1' : '0';
                    $recordData['KESIMPULAN_MERAH'] = ($totalScore >= 2) ? '1' : '0';
                }

                DB::connection('sqlsrv')->table('EWS_IGD')->updateOrInsert(
                    ['NOPENDAFTARAN' => $noPendaftaran, 'COUNTER' => $counter],
                    $recordData
                );
            }

            DB::connection('sqlsrv')->commit();

            return response()->json(['status' => 'success', 'message' => 'Data EWS berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error storing EWS data: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    /**
     * Delete an EWS record and re-order subsequent counters.
     */
    public function destroy(Request $request)
    {
        $noPendaftaran = $request->input('nopendaftaran');
        $counterToDelete = $request->input('counter');

        if (empty($noPendaftaran) || empty($counterToDelete)) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tidak valid.'], 400);
        }

        try {
            DB::connection('sqlsrv')->beginTransaction();

            $deleted = DB::connection('sqlsrv')
                ->table('EWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counterToDelete)
                ->delete();

            if ($deleted === 0) {
                DB::connection('sqlsrv')->commit();
                return response()->json(['status' => 'success', 'message' => 'Data tidak ditemukan atau sudah dihapus.']);
            }

            $recordsToUpdate = DB::connection('sqlsrv')
                ->table('EWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', '>', $counterToDelete)
                ->orderBy('COUNTER', 'asc')
                ->get();

            $currentCounter = $counterToDelete;
            foreach ($recordsToUpdate as $record) {
                DB::connection('sqlsrv')
                    ->table('EWS_IGD')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $record->COUNTER)
                    ->update(['COUNTER' => $currentCounter]);
                $currentCounter++;
            }

            DB::connection('sqlsrv')->commit();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error deleting EWS data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}