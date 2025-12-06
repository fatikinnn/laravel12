<?php

namespace App\Http\Controllers;
use App\Http\Controllers\RmeIgdController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class Rm48aController extends Controller
{
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        // Mengambil data pasien dasar dari RmeIgdController untuk konsistensi
        $rmeIgdController = new RmeIgdController();
        $patientDetails = $rmeIgdController->getPatientDetailsData($noPendaftaran);
        if (isset($patientDetails['error'])) {
            return response('Data pasien tidak ditemukan.', 404);
        }

        // Ambil daftar dokter
        $dokterList = DB::connection('sqlsrv') // Use the standard 'sqlsrv' connection
            ->table('pemeriksa')
            ->where('ACTIVE', '1')
            ->where('NAMAPEMERIKSA', 'like', '%dr.%')
            ->orderBy('NAMAPEMERIKSA', 'asc')
            ->get(['NAMAPEMERIKSA']);

        return view('rme.igd.forms.rm48a.index', compact('noPendaftaran', 'norm', 'user', 'patientDetails', 'dokterList'));
    }

    public function history(Request $request)
    {
        try {
            $noPendaftaran = $request->input('noPendaftaran');
            $history = DB::connection('sqlsrv') // Use the standard 'sqlsrv' connection
                ->table('rm48a')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('COUNTER', 'asc')
                ->get([
                    'COUNTER', 'TANGGAL', 'JAM', 'PERSETUJUAN_TINDAKAN',
                    'TINDAKAN', 'YANGMENYATAKAN', 'DOKTER'
                ]);

            return response()->json(['status' => 'success', 'data' => $history]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $noPendaftaran = $request->input('noPendaftaran');
            $counter = $request->input('counter');

            $detail = DB::connection('sqlsrv') // Use the standard 'sqlsrv' connection
                ->table('rm48a')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counter)
                ->first();

            if (!$detail) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            }

            // Helper function to convert image data to base64
            $toBase64 = function ($imageData) {
                if (empty($imageData)) {
                    return null;
                }
                $binaryData = $imageData;

                // Driver SQL Server sering mengembalikan data 'image' sebagai hex string
                if (strpos($imageData, '0x') === 0) {
                    $binaryData = hex2bin(substr($imageData, 2));
                } elseif (is_string($imageData) && ctype_xdigit($imageData)) {
                    // Fallback jika driver mengembalikan hex string tanpa '0x'
                    // Ini penting untuk kompatibilitas dengan SQL Server 2000
                    $binaryData = hex2bin($imageData);
                }

                return 'data:image/jpeg;base64,' . base64_encode($binaryData);
            };

            $detail->SAKSI_1_BASE64 = $toBase64($detail->SAKSI_1);
            $detail->TTD_YANGMENYATAKAN_BASE64 = $toBase64($detail->TTD_YANGMENYATAKAN);

            return response()->json(['status' => 'success', 'data' => $detail]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $norm = $request->input('NORM');
        $user = session('user'); // Use session to get user data
        $formData = $request->all();

        DB::connection('sqlsrv')->beginTransaction(); // Use the standard 'sqlsrv' connection
        try {
            $counter = $formData['COUNTER'] ? (int)$formData['COUNTER'] : 0;

            // Prepare data for DB
            $dataToSave = [
                'NOPENDAFTARAN' => $noPendaftaran,
                'USER_ENTRY' => $user['username'] ?? 'SYSTEM', // Access username as an array key
                'TGLJAM_ENTRY' => now(),
                'TANGGAL' => $formData['TANGGAL'] ?? null,
                'JAM' => $formData['JAM'] ?? null,
                'PERSETUJUAN_TINDAKAN' => $formData['PERSETUJUAN_TINDAKAN'] ?? null,
                'YANG_BERTANDA_NAMA' => $formData['YANG_BERTANDA_NAMA'] ?? null,
                'YANG_BERTANDA_USIA' => $formData['YANG_BERTANDA_USIA'] ?? null,
                'YANG_BERTANDA_JK' => $formData['YANG_BERTANDA_JK'] ?? null,
                'YANG_BERTANDA_ALAMAT' => $formData['YANG_BERTANDA_ALAMAT'] ?? null,
                'TINDAKAN' => $formData['TINDAKAN'] ?? null,
                'TERHADAP' => $formData['TERHADAP'] ?? null,
                'NAMA_PASIEN' => $formData['NAMA_PASIEN'] ?? null,
                'USIA_PASIEN' => $formData['USIA_PASIEN'] ?? null,
                'JK_PASIEN' => $formData['JK_PASIEN'] ?? null,
                'ALAMAT_PASIEN' => $formData['ALAMAT_PASIEN'] ?? null,
                'DOKTER' => $formData['DOKTER'] ?? null,
                'NAMA_SAKSI_1' => $formData['NAMA_SAKSI_1'] ?? null,
                'YANGMENYATAKAN' => $formData['YANGMENYATAKAN'] ?? null,
            ];

            // Process signatures
            $this->processSignature($request, 'SAKSI_1', $dataToSave);
            $this->processSignature($request, 'TTD_YANGMENYATAKAN', $dataToSave);

            if (!empty($counter)) { // Update
                DB::connection('sqlsrv') // Use the standard 'sqlsrv' connection
                    ->table('rm48a')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $counter)
                    ->update($dataToSave);
                $message = 'Data berhasil diperbarui.';
            } else { // Insert
                $maxCounter = DB::connection('sqlsrv') // Use the standard 'sqlsrv' connection
                    ->table('rm48a')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->max('COUNTER');
                $dataToSave['COUNTER'] = $maxCounter + 1;
                $dataToSave['NOPENDAFTARAN'] = $noPendaftaran; // Ensure NOPENDAFTARAN is in the insert array
                DB::connection('sqlsrv')->table('rm48a')->insert($dataToSave); // Use the standard 'sqlsrv' connection
                $message = 'Data berhasil disimpan.';
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage() . ' on line ' . $e->getLine()], 500);
        }
    }

    private function processSignature(Request $request, $fieldName, &$dataToSave)
    {
        if ($request->has($fieldName) && !empty($request->input($fieldName))) {
            $base64Image = $request->input($fieldName);
            // Menyesuaikan dengan format JPEG dari client-side
            if (strpos($base64Image, 'data:image/jpeg;base64,') === 0) {
                $imageData = base64_decode(str_replace('data:image/jpeg;base64,', '', $base64Image));
                $dataToSave[$fieldName] = $imageData;
            } else if (strpos($base64Image, 'data:image') === 0) { // Fallback untuk format lain
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                $dataToSave[$fieldName] = $imageData;
            }
        } else {
            $dataToSave[$fieldName] = null;
        }
    }

    public function showImage($noPendaftaran, $counter, $field)
    {
        // Validasi nama field untuk keamanan
        if (!in_array($field, ['SAKSI_1', 'TTD_YANGMENYATAKAN'])) {
            abort(400, 'Invalid field name.');
        }

        $imageData = DB::connection('sqlsrv')
            ->table('rm48a')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->where('COUNTER', $counter)
            ->value($field);

        if (empty($imageData)) {
            return $this->servePlaceholder();
        }

        // Driver database untuk SQL Server sering mengembalikan tipe data 'image'
        // sebagai string heksadesimal yang diawali dengan '0x'.
        $binaryData = $imageData;
        if (strpos($imageData, '0x') === 0) {
            $binaryData = hex2bin(substr($imageData, 2));
        } elseif (ctype_xdigit($imageData)) {
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
    
    private function servePlaceholder()
    {
        // Menggunakan gambar placeholder yang sama seperti di form lain untuk konsistensi
        $path = public_path('img/no-signature.jpg');
        if (!file_exists($path)) {
            return response('Placeholder not found.', 404);
        }
        return response()->file($path);
    }
}