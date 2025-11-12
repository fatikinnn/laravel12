<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MoewsController extends Controller
{
    /**
     * Load the MOEWS form view.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        return view('rme.igd.forms.moews.index', compact('noPendaftaran', 'norm', 'user'));
    }

    /**
     * Fetch all MOEWS records for a given registration number.
     */
    public function history(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $data = DB::connection('sqlsrv')
                ->table('MOEWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('COUNTER', 'asc')
                ->get();

            // Format date and time fields
            $data->transform(function ($item) {
                if (!empty($item->TGL)) {
                    $item->TGL = Carbon::parse($item->TGL)->format('Y-m-d');
                }
                if (!empty($item->JAM)) {
                    // Assuming JAM is stored as a full datetime or time string
                    $item->JAM = Carbon::parse($item->JAM)->format('H:i');
                }
                return $item;
            });

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error fetching MOEWS history: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat riwayat MOEWS.'], 500);
        }
    }

    /**
     * Map field names to their scores.
     */
    private function getScoreMap()
    {
        return [
            // Frekuensi Nafas
            'FREKUENSI_NAFAS_25' => 3,
            'FREKUENSI_NAFAS_21' => 2,
            'FREKUENSI_NAFAS_20' => 0,
            'FREKUENSI_NAFAS_12' => 3,
            // Saturasi Oksigen
            'SATURASI_OKSIGEN_90' => 3,
            'SATURASI_OKSIGEN_95' => 0,
            'SATURASI_OKSIGEN_92' => 1,
            // Oksigen
            'OKSIGEN_YA' => 2,
            'OKSIGEN_TIDAK' => 0,
            // Suhu
            'SUHU_37' => 3,
            'SUHU_36' => 1,
            'SUHU_35' => 0,
            'SUHU_30' => 3,
            // TD Sistolik
            'TD_SISTOLIK_160' => 3,
            'TD_SISTOLIK_151' => 2,
            'TD_SISTOLIK_141' => 1,
            'TD_SISTOLIK_91' => 0, // Covers 91-140 range
            'TD_SISTOLIK_90' => 3,
            // TD Diastolik
            'TD_DIASTOLIK_111' => 3, 'TD_DIASTOLIK_60' => 3,
            'TD_DIASTOLIK_101' => 1,
            // Nadi
            'NADI_121' => 3, 'NADI_40' => 3,
            'NADI_50' => 2,
            'NADI_111' => 1, 'NADI_101' => 1,
            // Kesadaran, Nyeri, Discharge
            'KESADARAN_VPU' => 3, 'NYERI_ABNORMAL' => 3, 'DISCHARGE_ABNORMAL' => 3,
            // Proteinuria
            'PROTEINURIA_POSITIF2' => 3,
            'PROTEINURIA_POSITIF' => 2,
            // Kondisi Lain
            'POSTPARTUM' => 3, // In native, this has score 3
            'PREEKLAMPSIA' => 0, // Explicitly set to 0
        ];
    }

    /**
     * Get all possible MOEWS fields to ensure completeness.
     * Based on the native PHP version.
     */
    private function getAllMoewsFields()
    {
        return [
            'FREKUENSI_NAFAS_25', 'FREKUENSI_NAFAS_21', 'FREKUENSI_NAFAS_20', 'FREKUENSI_NAFAS_12',
            'SATURASI_OKSIGEN_95', 'SATURASI_OKSIGEN_92', 'SATURASI_OKSIGEN_90',
            'OKSIGEN_YA', 'OKSIGEN_TIDAK',
            'SUHU_37', 'SUHU_36', 'SUHU_35', 'SUHU_30',
            'TD_SISTOLIK_160', 'TD_SISTOLIK_151', 'TD_SISTOLIK_141', 'TD_SISTOLIK_131', 'TD_SISTOLIK_121', 'TD_SISTOLIK_111', 'TD_SISTOLIK_101', 'TD_SISTOLIK_91', 'TD_SISTOLIK_90',
            'TD_DIASTOLIK_111', 'TD_DIASTOLIK_101', 'TD_DIASTOLIK_91', 'TD_DIASTOLIK_61', 'TD_DIASTOLIK_60',
            'NADI_121', 'NADI_111', 'NADI_101', 'NADI_61', 'NADI_50', 'NADI_40',
            'KESADARAN_A', 'KESADARAN_VPU',
            'NYERI_NORMAL', 'NYERI_ABNORMAL',
            'DISCHARGE_NORMAL', 'DISCHARGE_ABNORMAL',
            'PROTEINURIA_NEGATIF', 'PROTEINURIA_POSITIF', 'PROTEINURIA_POSITIF2',
            'POSTPARTUM', 'PREEKLAMPSIA',
            'KESIMPULAN_SKOR2', 'KESIMPULAN_SKOR3', 'KESIMPULAN_SKOR5', 'KESIMPULAN_SKOR7',
            'HENTI_JANTUNG'
        ];
    }

    /**
     * Store or update MOEWS data.
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

                // Prepare data for insert/update
                $recordData = [
                    'NOPENDAFTARAN' => $noPendaftaran,
                    'USER_ENTRY' => $user,
                    'TGLJAM_ENTRY' => now(),
                    'COUNTER' => $counter,
                    'TGL' => !empty($data['TGL']) ? Carbon::parse($data['TGL'])->format('Y-m-d') : null,
                    'JAM' => !empty($data['JAM']) ? $data['JAM'] : null,
                    'NAMA_PPA' => $data['NAMA_PPA'] ?? $user,
                ];

                $scoreMap = $this->getScoreMap();
                $totalScore = 0;

                // Loop through all possible fields from the data sent by the client
                foreach ($data as $field => $value) {
                    // Process only fields that are part of the scoring system or checkboxes
                    if (array_key_exists($field, $scoreMap)) {
                        $isChecked = ($value == '1');
                        $recordData[$field] = $isChecked ? '1' : '0'; // Store as string '1' or '0' for varchar/bit columns
                        if ($isChecked) {
                            $totalScore += $scoreMap[$field];
                        }
                    } elseif (strpos($field, '_') !== false && !isset($recordData[$field])) {
                        // Handle fields with 0 score that are not in the map (e.g., FREKUENSI_NAFAS_20)
                        $recordData[$field] = ($value == '1') ? '1' : '0'; // Store as string '1' or '0'
                    }
                }

                $recordData['TOTAL_SKOR'] = $totalScore;

                // Logic for conclusion based on score, ensure they are strings
                $recordData['KESIMPULAN_SKOR2'] = ($totalScore >= 0 && $totalScore <= 2) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR3'] = ($totalScore >= 3 && $totalScore <= 4) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR5'] = ($totalScore >= 5 && $totalScore <= 6) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR7'] = ($totalScore >= 7) ? '1' : '0';
                $recordData['HENTI_JANTUNG'] = (isset($data['HENTI_JANTUNG']) && $data['HENTI_JANTUNG'] == '1') ? '1' : '0';

                // Ensure all possible fields exist in the recordData, defaulting to '0' if not set
                $allFields = $this->getAllMoewsFields();
                foreach ($allFields as $field) {
                    if (!array_key_exists($field, $recordData)) {
                        $recordData[$field] = '0';
                    }
                }

                // Use updateOrInsert for simplicity
                DB::connection('sqlsrv')->table('MOEWS_IGD')->updateOrInsert(
                    ['NOPENDAFTARAN' => $noPendaftaran, 'COUNTER' => $counter],
                    $recordData
                );
            }

            DB::connection('sqlsrv')->commit();

            return response()->json(['status' => 'success', 'message' => 'Data MOEWS berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error storing MOEWS data: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    /**
     * Delete a MOEWS record and re-order subsequent counters.
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

            // 1. Delete the specified record
            $deleted = DB::connection('sqlsrv')
                ->table('MOEWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counterToDelete)
                ->delete();

            if ($deleted === 0) {
                // If no record was deleted, it might not exist.
                // We can either throw an error or just commit and return success.
                // Let's assume it's not an error if the record is already gone.
                DB::connection('sqlsrv')->commit();
                return response()->json(['status' => 'success', 'message' => 'Data tidak ditemukan atau sudah dihapus.']);
            }

            // 2. Get all records with a counter greater than the one deleted
            $recordsToUpdate = DB::connection('sqlsrv')
                ->table('MOEWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', '>', $counterToDelete)
                ->orderBy('COUNTER', 'asc')
                ->get();

            // 3. Update their counters sequentially
            $currentCounter = $counterToDelete;
            foreach ($recordsToUpdate as $record) {
                DB::connection('sqlsrv')
                    ->table('MOEWS_IGD')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $record->COUNTER)
                    ->update(['COUNTER' => $currentCounter]);
                $currentCounter++;
            }

            DB::connection('sqlsrv')->commit();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error deleting MOEWS data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}