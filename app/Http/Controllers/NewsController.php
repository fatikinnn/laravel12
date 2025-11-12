<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * Load the NEWS form view.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        return view('rme.igd.forms.news.index', compact('noPendaftaran', 'norm', 'user'));
    }

    /**
     * Fetch all NEWS records for a given registration number.
     */
    public function history(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $data = DB::connection('sqlsrv')
                ->table('NEWS_IGD')
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
            Log::error('Error fetching NEWS history: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat riwayat NEWS.'], 500);
        }
    }

    /**
     * Map field names to their scores for NEWS.
     */
    private function getScoreMap()
    {
        return [
            'FREKUENSI_NAFAS_25' => 3, 'FREKUENSI_NAFAS_21' => 2, 'FREKUENSI_NAFAS_11' => 1, 'FREKUENSI_NAFAS_8' => 3,
            'SATURASI_OKSIGEN_91' => 3, 'SATURASI_OKSIGEN_92' => 2, 'SATURASI_OKSIGEN_94' => 1,
            'OKSIGEN_YA' => 2,
            'SUHU_39' => 2, 'SUHU_38' => 1, 'SUHU_35' => 1, 'SUHU_30' => 3,
            'TD_SISTOLIK_220' => 3, 'TD_SISTOLIK_91' => 1, 'TD_SISTOLIK_81' => 2, 'TD_SISTOLIK_70' => 3,
            'NADI_131' => 3, 'NADI_111' => 2, 'NADI_91' => 1, 'NADI_41' => 1, 'NADI_40' => 3,
            'KESADARAN_VPU' => 3,
        ];
    }

    /**
     * Get all possible NEWS fields to ensure completeness.
     */
    private function getAllNewsFields()
    {
        return [
            'FREKUENSI_NAFAS_25', 'FREKUENSI_NAFAS_21', 'FREKUENSI_NAFAS_20', 'FREKUENSI_NAFAS_11', 'FREKUENSI_NAFAS_8',
            'SATURASI_OKSIGEN_96', 'SATURASI_OKSIGEN_94', 'SATURASI_OKSIGEN_92', 'SATURASI_OKSIGEN_91',
            'OKSIGEN_YA', 'OKSIGEN_TIDAK',
            'SUHU_39', 'SUHU_38', 'SUHU_37', 'SUHU_36', 'SUHU_35', 'SUHU_30',
            'TD_SISTOLIK_220', 'TD_SISTOLIK_201', 'TD_SISTOLIK_181', 'TD_SISTOLIK_161', 'TD_SISTOLIK_141', 'TD_SISTOLIK_121', 'TD_SISTOLIK_111', 'TD_SISTOLIK_101', 'TD_SISTOLIK_91', 'TD_SISTOLIK_81', 'TD_SISTOLIK_71', 'TD_SISTOLIK_70',
            'NADI_131', 'NADI_121', 'NADI_111', 'NADI_101', 'NADI_91', 'NADI_81', 'NADI_71', 'NADI_61', 'NADI_51', 'NADI_41', 'NADI_40',
            'KESADARAN_A', 'KESADARAN_VPU',
            'KESIMPULAN_SKOR2', 'KESIMPULAN_SKOR3', 'KESIMPULAN_SKOR5', 'KESIMPULAN_SKOR7',
            'HENTI_JANTUNG'
        ];
    }

    /**
     * Store or update NEWS data.
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
                    'TGL' => !empty($data['TGL']) ? Carbon::parse($data['TGL'])->format('Y-m-d') : null,
                    'JAM' => !empty($data['JAM']) ? $data['JAM'] : null,
                    'NAMA_PPA' => $data['NAMA_PPA'] ?? $user,
                ];

                $scoreMap = $this->getScoreMap();
                $totalScore = 0;

                $allFields = $this->getAllNewsFields();
                foreach ($allFields as $field) {
                    $isChecked = isset($data[$field]) && $data[$field] == '1';
                    $recordData[$field] = $isChecked ? '1' : '0';
                    if ($isChecked && isset($scoreMap[$field])) {
                        $totalScore += $scoreMap[$field];
                    }
                }

                $recordData['TOTAL_SKOR'] = $totalScore;

                // Logic for conclusion based on score
                $recordData['KESIMPULAN_SKOR2'] = ($totalScore >= 0 && $totalScore <= 2) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR3'] = ($totalScore >= 3 && $totalScore <= 4) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR5'] = ($totalScore >= 5 && $totalScore <= 6) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR7'] = ($totalScore >= 7) ? '1' : '0';
                $recordData['HENTI_JANTUNG'] = (isset($data['HENTI_JANTUNG']) && $data['HENTI_JANTUNG'] == '1') ? '1' : '0';

                DB::connection('sqlsrv')->table('NEWS_IGD')->updateOrInsert(
                    ['NOPENDAFTARAN' => $noPendaftaran, 'COUNTER' => $counter],
                    $recordData
                );
            }

            DB::connection('sqlsrv')->commit();

            return response()->json(['status' => 'success', 'message' => 'Data NEWS berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error storing NEWS data: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    /**
     * Delete a NEWS record and re-order subsequent counters.
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
                ->table('NEWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counterToDelete)
                ->delete();

            if ($deleted === 0) {
                DB::connection('sqlsrv')->commit();
                return response()->json(['status' => 'success', 'message' => 'Data tidak ditemukan atau sudah dihapus.']);
            }

            $recordsToUpdate = DB::connection('sqlsrv')
                ->table('NEWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', '>', $counterToDelete)
                ->orderBy('COUNTER', 'asc')
                ->get();

            $currentCounter = $counterToDelete;
            foreach ($recordsToUpdate as $record) {
                DB::connection('sqlsrv')
                    ->table('NEWS_IGD')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $record->COUNTER)
                    ->update(['COUNTER' => $currentCounter]);
                $currentCounter++;
            }

            DB::connection('sqlsrv')->commit();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error deleting NEWS data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}
