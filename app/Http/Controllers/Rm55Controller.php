<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rm55Controller extends Controller
{
    public function load(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoPendaftaran' => 'required|string',
            'NoRM' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response('Parameter tidak valid.', 400);
        }

        $pasien = $request->all();

        // Ambil data yang sudah ada dari database
        $existingData = DB::connection('sqlsrv')
            ->table('RM55')
            ->where('NOPENDAFTARAN', $request->input('NoPendaftaran'))
            ->first();

        // Ambil semua dokter untuk dropdown
        $dokterList = DB::connection('sqlsrv')
            ->table('PEMERIKSA')
            ->select('NOPEMERIKSA', 'NAMAPEMERIKSA')
            ->where('ACTIVE', 1)
            ->where('NAMAPEMERIKSA', 'like', '%dr. %')
            ->orderBy('NAMAPEMERIKSA', 'asc')
            ->get();

        // Tentukan NOPEMERIKSA yang akan dipilih
        // Prioritas: data yang sudah tersimpan di RM55, fallback ke data DPJP dari pendaftaran.
        $selectedNopemeriksa = $existingData->NOPEMERIKSA ?? null;
        if (!$selectedNopemeriksa && !empty($pasien['DPJP'])) {
            $defaultDpjp = $dokterList->firstWhere('NAMAPEMERIKSA', trim($pasien['DPJP']));
            $selectedNopemeriksa = $defaultDpjp->NOPEMERIKSA ?? null;
        }

        $data = [
            'noPendaftaran' => $request->input('NoPendaftaran'),
            'norm' => $request->input('NoRM'),
            'user' => session('user'),
            'pasien' => $pasien, // Meneruskan semua data pasien
            'data' => $existingData, // Kirim data yang ada ke view
            'dokterList' => $dokterList,
            'selectedNopemeriksa' => $selectedNopemeriksa,
        ];

        return view('rme.igd.forms.rm55.index', $data);
    }

    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noPendaftaran' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'No Pendaftaran diperlukan.'], 400);
        }

        $data = DB::connection('sqlsrv')
            ->table('RM55')
            ->where('NOPENDAFTARAN', $request->noPendaftaran)
            ->first();

        if ($data) {
            // Kirim flag boolean untuk menandakan keberadaan tanda tangan
            $data->TTD_PENERIMA = !empty($data->TTD_PENERIMA);
            return response()->json(['status' => 'success', 'data' => $data]);
        }

        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'NM_IBU' => 'nullable|string|max:100',
            'NM_AYAH' => 'nullable|string|max:100',
            'JENKEL' => 'nullable|string|max:1',
            'TGLJAMLAHIR' => 'nullable|date',
            'BB' => 'nullable|string|max:25',
            'PB' => 'nullable|string|max:25',
            'DPJP' => 'nullable|string|max:100',
            'NOPEMERIKSA' => 'nullable|string|max:5',
            'PETUGAS_RNIFAS' => 'nullable|string|max:100',
            'NM_PENERIMA' => 'nullable|string|max:100',
            'TTD_PENERIMA' => 'nullable|string', // Base64
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak valid.', 'errors' => $validator->errors()], 422);
        }

        $user = session('user');
        $username = $user['username'] ?? 'default_user';
        $now = Carbon::now();

        $data = $request->except(['_token', 'TTD_PENERIMA', 'NORM', 'dpjp_text']);

        // Format TGLJAMLAHIR dari datetime-local ke format string yang diinginkan
        if ($request->filled('TGLJAMLAHIR')) {
            $data['TGLJAMLAHIR'] = Carbon::parse($request->input('TGLJAMLAHIR'))->format('Y-m-d H:i:s');
        } else {
            $data['TGLJAMLAHIR'] = null;
        }

        // Ambil nama DPJP dari input tersembunyi dan masukkan ke kolom DPJP
        if ($request->has('dpjp_text')) {
            $data['DPJP'] = $request->input('dpjp_text');
        }

        // Handle Tanda Tangan
        if ($request->has('TTD_PENERIMA') && !empty($request->input('TTD_PENERIMA'))) {
            $base64Image = $request->input('TTD_PENERIMA');
            if (strpos($base64Image, 'data:image') === 0) {
                // Decode base64 menjadi data biner mentah
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                $data['TTD_PENERIMA'] = $imageData;
            } else {
                unset($data['TTD_PENERIMA']); // Jangan proses jika bukan base64 image
            }
        } else {
            // Jika input TTD kosong, jangan set null agar tidak menimpa data yang ada jika tidak diubah
            unset($data['TTD_PENERIMA']);
        }

        // Pisahkan data tanda tangan dari data lainnya
        $signatureData = null;
        if (isset($data['TTD_PENERIMA'])) {
            $signatureData = ['TTD_PENERIMA' => $data['TTD_PENERIMA']];
            unset($data['TTD_PENERIMA']);
        }

        try {
            $noPendaftaran = $request->input('NOPENDAFTARAN');
            $existing = DB::connection('sqlsrv')->table('RM55')->where('NOPENDAFTARAN', $noPendaftaran)->first();

            if ($existing) {
                // Update: Hanya update data yang dikirim
                DB::connection('sqlsrv')->table('RM55')->where('NOPENDAFTARAN', $noPendaftaran)->update($data);
                // Update tanda tangan secara terpisah jika ada
                if ($signatureData) {
                    DB::connection('sqlsrv')->table('RM55')->where('NOPENDAFTARAN', $noPendaftaran)->update($signatureData);
                }
                $message = 'Data berhasil diperbarui.';
            } else {
                // Insert: Tambahkan data user dan waktu entry
                $data['USER_ENTRY'] = $username;
                $data['TGLJAM_ENTRY'] = $now->format('Y-m-d H:i:s');
                $data['NOPENDAFTARAN'] = $noPendaftaran;
                DB::connection('sqlsrv')->table('RM55')->insert($data);
                // Insert tanda tangan secara terpisah jika ada
                if ($signatureData) {
                    DB::connection('sqlsrv')->table('RM55')->where('NOPENDAFTARAN', $noPendaftaran)->update($signatureData);
                }
                $message = 'Data berhasil disimpan.';
            }


            return response()->json(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function showImage($noPendaftaran)
    {
        $imageData = DB::connection('sqlsrv')
            ->table('RM55')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->value('TTD_PENERIMA');

        if (empty($imageData)) {
            return $this->servePlaceholder();
        }

        // Driver database untuk SQL Server sering mengembalikan tipe data 'image'
        // sebagai string heksadesimal yang diawali dengan '0x'.
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

    private function servePlaceholder()
    {
        $path = public_path('images/no-image.png');
        if (!file_exists($path)) {
            return response('Placeholder not found.', 404);
        }
        return response()->file($path);
    }
}