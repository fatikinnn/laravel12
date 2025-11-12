<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PewsController extends Controller
{
    /**
     * Load the PEWS form view.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        return view('rme.igd.forms.pews.index', compact('noPendaftaran', 'norm', 'user'));
    }

    /**
     * Fetch all PEWS records for a given registration number.
     */
    public function history(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $data = DB::connection('sqlsrv')
                ->table('PEWS_IGD')
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
            Log::error('Error fetching PEWS history: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat riwayat PEWS.'], 500);
        }
    }

    /**
     * Map field names to their scores for PEWS.
     */
    private function getScoreMap()
    {
        return [
            // Perilaku
            'PERILAKU_TIDUR' => 1,
            'PERILAKU_IRITABEL' => 2,
            'PERILAKU_LETARGI' => 3, // Corrected typo
            // Cardiovasculer
            'CARDIO_PUCAT' => 1,
            'CARDIO_PUCAT2' => 2,
            'CARDIO_ABU' => 3,
            // Parameter Pernapasan (Perilaku)
            'PERILAKU_RR24' => 1,
            'PERILAKU_RR30' => 2,
            'PERILAKU_RR20' => 3,
            // Kondisi Lain
            'NEBULISASI' => 2,
            'MUNTAH' => 2,
            // Fields with 0 score don't need to be explicitly listed here,
            // but must be in getAllPewsFields for completeness.
        ];
    }

    /**
     * Get all possible PEWS fields to ensure completeness.
     * Based on the native PHP version.
     */
    private function getAllPewsFields()
    {
        return [
            'PERILAKU_BERMAIN', 'PERILAKU_TIDUR', 'PERILAKU_IRITABEL', 'PERILAKU_LETARGI',
            'CARDIO_PINK', 'CARDIO_PUCAT', 'CARDIO_PUCAT2', 'CARDIO_ABU',
            'PERILAKU_NORMAL', 'PERILAKU_RR24', 'PERILAKU_RR30', 'PERILAKU_RR20',
            'NEBULISASI', 'MUNTAH', // These are checkbox-like fields
            'KESIMPULAN_SKOR2', 'KESIMPULAN_SKOR3', 'KESIMPULAN_SKOR5', 'KESIMPULAN_SKOR7',
            'HENTI_JANTUNG'
        ];
    }

    /**
     * Store or update PEWS data.
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
                    // Pastikan TGL dan JAM selalu memiliki nilai, default ke waktu saat ini jika kosong
                    'TGL' => !empty($data['TGL']) ? Carbon::parse($data['TGL'])->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                    'JAM' => !empty($data['JAM']) ? Carbon::parse($data['JAM'])->format('H:i') : Carbon::now()->format('H:i'),
                    'NAMA_PPA' => $data['NAMA_PPA'] ?? $user, // Set NAMA_PPA di sini
                ];

                $scoreMap = $this->getScoreMap();
                $totalScore = 0;

                // Dapatkan daftar semua field yang mungkin dari getAllPewsFields
                $allPewsFields = $this->getAllPewsFields();

                // Inisialisasi semua field dengan '0' untuk memastikan tidak ada yang hilang
                foreach ($allPewsFields as $field) {
                    if (!isset($recordData[$field])) {
                        $recordData[$field] = '0';
                    }
                }

                // Proses data yang masuk dari form
                foreach ($allPewsFields as $field) {
                    $isChecked = isset($data[$field]) && $data[$field] == '1';
                    $recordData[$field] = $isChecked ? '1' : '0';
                    if ($isChecked && isset($scoreMap[$field])) {
                        $totalScore += $scoreMap[$field];
                    }
                }

                $recordData['TOTAL_SKOR'] = $totalScore; // Set TOTAL_SKOR di sini

                // Logic for conclusion based on score
                // Pastikan HENTI_JANTUNG diproses terlebih dahulu jika ada
                $recordData['HENTI_JANTUNG'] = (isset($data['HENTI_JANTUNG']) && $data['HENTI_JANTUNG'] == '1') ? '1' : '0';

                // Reset kesimpulan skor lainnya jika HENTI_JANTUNG aktif
                if ($recordData['HENTI_JANTUNG'] == '1') {
                    $recordData['KESIMPULAN_SKOR2'] = '0';
                    $recordData['KESIMPULAN_SKOR3'] = '0';
                    $recordData['KESIMPULAN_SKOR5'] = '0';
                    $recordData['KESIMPULAN_SKOR7'] = '0';
                } else {
                $recordData['KESIMPULAN_SKOR2'] = ($totalScore >= 0 && $totalScore <= 2) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR3'] = ($totalScore >= 3 && $totalScore <= 4) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR5'] = ($totalScore >= 5 && $totalScore <= 6) ? '1' : '0';
                $recordData['KESIMPULAN_SKOR7'] = ($totalScore >= 7) ? '1' : '0';
                }

                DB::connection('sqlsrv')->table('PEWS_IGD')->updateOrInsert(
                    ['NOPENDAFTARAN' => $noPendaftaran, 'COUNTER' => $counter],
                    $recordData
                );
            }

            DB::connection('sqlsrv')->commit();

            return response()->json(['status' => 'success', 'message' => 'Data PEWS berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error storing PEWS data: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    /**
     * Delete a PEWS record and re-order subsequent counters.
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
                ->table('PEWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counterToDelete)
                ->delete();

            if ($deleted === 0) {
                DB::connection('sqlsrv')->commit();
                return response()->json(['status' => 'success', 'message' => 'Data tidak ditemukan atau sudah dihapus.']);
            }

            $recordsToUpdate = DB::connection('sqlsrv')
                ->table('PEWS_IGD')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', '>', $counterToDelete)
                ->orderBy('COUNTER', 'asc')
                ->get();

            $currentCounter = $counterToDelete;
            foreach ($recordsToUpdate as $record) {
                DB::connection('sqlsrv')
                    ->table('PEWS_IGD')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $record->COUNTER)
                    ->update(['COUNTER' => $currentCounter]);
                $currentCounter++;
            }

            DB::connection('sqlsrv')->commit();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error deleting PEWS data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}