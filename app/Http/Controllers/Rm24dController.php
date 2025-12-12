<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class Rm24dController extends Controller
{
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        $perawatBidan = DB::connection('sqlsrv')
            ->table('satusehatlogin')
            ->select('Username')
            ->where('isactive', 1)
            ->whereIn('access', ['perawat', 'bidan'])
            ->orderBy('Username', 'asc')
            ->get();

        return view('rme.igd.forms.rm24d.index', compact('noPendaftaran', 'norm', 'user', 'perawatBidan'));
    }

    public function history(Request $request)
    {
        try {
            $noPendaftaran = $request->input('noPendaftaran');

            $history = DB::connection('sqlsrv')
                ->table('RM24D')
                ->select(
                    'COUNTER', 'TGLSHIT', 'FOKUS', 'DIAGNOSA_PRWT', 'TUJUAN', 'RENCANA',
                    'TINDAKAN', 'JAM', 'EVALUASI', 'NM_SERAH', 'NM_TERIMA'
                )
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('COUNTER', 'asc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $history]);
        } catch (\Exception $e) {
            Log::error('Error loading RM24D history: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat riwayat.'], 500);
        }
    }

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

            $maxCounterResult = DB::connection('sqlsrv')
                ->table('RM24D')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->max('COUNTER');
            $maxCounter = $maxCounterResult ?: 0;

            foreach ($monitoringData as $item) {
                $counter = isset($item['COUNTER']) ? intval($item['COUNTER']) : 0;

                $tglShit = null;
                if (!empty($item['TGLSHIT'])) {
                    try {
                        $tglShit = Carbon::parse($item['TGLSHIT'])->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $tglShit = null;
                    }
                }


                $dataToUpsert = [
                    'TGLENTRI' => now(),
                    'USERENTRI' => $user,
                    'TGLSHIT' => $tglShit,
                    'FOKUS' => $item['FOKUS'] ?? null,
                    'DIAGNOSA_PRWT' => $item['DIAGNOSA_PRWT'] ?? null,
                    'TUJUAN' => $item['TUJUAN'] ?? null,
                    'RENCANA' => $item['RENCANA'] ?? null,
                    'JAM' => $item['JAM'] ?? null,
                    'TINDAKAN' => $item['TINDAKAN'] ?? null,
                    'EVALUASI' => $item['EVALUASI'] ?? null,
                    'NM_SERAH' => $item['NM_SERAH'] ?? null,
                    'NM_TERIMA' => $item['NM_TERIMA'] ?? null,
                ];

                // Handle TTD (Tanda Tangan) like in RM8A
                if (!empty($item['TTD_TERIMA']) && strpos($item['TTD_TERIMA'], 'data:image') === 0) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $item['TTD_TERIMA']));
                    // Laravel's query builder handles binary data binding correctly.
                    $dataToUpsert['TTD_TERIMA'] = $imageData;
                } else {
                    // Ensure TTD_TERIMA is not set if it's empty, to avoid errors on update
                    // If you want to clear it on update, set it to null: $dataToUpsert['TTD_TERIMA'] = null;
                }

                if ($counter == 0) { // Insert
                    $maxCounter++;
                    $dataToUpsert['NOPENDAFTARAN'] = $noPendaftaran;
                    $dataToUpsert['COUNTER'] = $maxCounter;
                    DB::connection('sqlsrv')->table('RM24D')->insert($dataToUpsert);
                } else { // Update
                    DB::connection('sqlsrv')
                        ->table('RM24D')
                        ->where('NOPENDAFTARAN', $noPendaftaran)
                        ->where('COUNTER', $counter)
                        ->update($dataToUpsert);
                }
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error('Error saving RM24D data: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $counter = $request->input('counter');

            if (empty($noPendaftaran) || empty($counter)) {
                return response()->json(['status' => 'error', 'message' => 'Parameter tidak lengkap.'], 400);
            }

            DB::connection('sqlsrv')
                ->table('RM24D')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counter)
                ->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error deleting RM24D data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data.'], 500);
        }
    }

    public function showSignature($noPendaftaran, $counter)
    {
        $imageData = DB::connection('sqlsrv')
            ->table('RM24D')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->where('COUNTER', $counter)
            ->value('TTD_TERIMA');

        if (empty($imageData)) {
            return $this->servePlaceholder();
        }

        // SQL Server sering mengembalikan data 'image' sebagai hex string yang diawali '0x'.
        $binaryData = $imageData;
        if (is_string($imageData) && strpos($imageData, '0x') === 0) {
            $binaryData = hex2bin(substr($imageData, 2));
        } elseif (is_string($imageData) && ctype_xdigit($imageData)) {
            // Fallback jika driver mengembalikan hex string tanpa '0x'
            $binaryData = hex2bin($imageData);
        }

        return response($binaryData, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function processSignature($base64Data)
    {
        if (empty($base64Data) || strpos($base64Data, 'data:image/') !== 0) {
            return null;
        }

        $binaryData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
        if ($binaryData === false) return null;

        $sourceImage = @imagecreatefromstring($binaryData);
        if ($sourceImage === false) return null;

        $newWidth = 100;
        $newHeight = 100;

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($resizedImage, 255, 255, 255);
        imagefill($resizedImage, 0, 0, $white);

        imagecopyresampled(
            $resizedImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            imagesx($sourceImage), imagesy($sourceImage)
        );

        ob_start();
        imagejpeg($resizedImage, null, 90);
        $resizedBinaryData = ob_get_clean();

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        return $resizedBinaryData;
    }

    private function servePlaceholder()
    {
        $path = public_path('');
        if (!file_exists($path)) {
            return response('Placeholder not found.', 404);
        }
        return response()->file($path);
    }
}
