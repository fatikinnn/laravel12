<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rm80Controller extends Controller
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

        $data = [
            'noPendaftaran' => $request->input('NoPendaftaran'),
            'norm' => $request->input('NoRM'),
            'user' => session('user'),
            'data' => DB::connection('sqlsrv')->table('RM80')->where('NOPENDAFTARAN', $request->input('NoPendaftaran'))->first(),
        ];

        return view('rme.igd.forms.rm80.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string|max:17',
            'HARI' => 'nullable|string',
            'TANGGAL' => 'nullable|date',
            'BULAN' => 'nullable|string',
            'TAHUN' => 'nullable|string',
            'JAM' => 'nullable|date_format:H:i',
            'NM_BERTANDA' => 'nullable|string',
            'JAB_BERTANDA' => 'nullable|string',
            'NM_PIHAK' => 'nullable|string',
            'JAB_PIHAK' => 'nullable|string',
            'NM_BAYI' => 'nullable|string',
            'TGLLAHIR_BAYI' => 'nullable|date',
            'BB_BATI' => 'nullable|string',
            'PANJANG_LAHIR' => 'nullable|string',
            'SURAT_KETLAHIR' => 'nullable|string',
            'KEADAAN' => 'nullable|string',
            'TTD_PENERIMA' => 'nullable|string',
            'TTD_MENYERAHKAN' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak valid.', 'errors' => $validator->errors()], 422);
        }

        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $user = session('user');
        $username = $user['username'] ?? 'default_user';
        $now = Carbon::now();

        // NORM tidak ada di tabel RM80, jadi kita kecualikan.
        $data = $request->except(['_token', 'NORM', 'TTD_PENERIMA', 'TTD_MENYERAHKAN']);
        $data['USER_ENTRY'] = $username;
        $data['TGLJAM_ENTRY'] = $now->format('Y-m-d H:i:s');

        // Handle TTD Penerima
        if ($request->has('TTD_PENERIMA') && !empty($request->input('TTD_PENERIMA'))) {
            $base64Image = $request->input('TTD_PENERIMA');
            if (strpos($base64Image, 'data:image') === 0) {
                // Decode base64 dan simpan sebagai data biner mentah (seperti RM8A)
                $data['TTD_PENERIMA'] = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            }
        }

        // Handle TTD Menyerahkan
        if ($request->has('TTD_MENYERAHKAN') && !empty($request->input('TTD_MENYERAHKAN'))) {
            $base64Image = $request->input('TTD_MENYERAHKAN');
            if (strpos($base64Image, 'data:image') === 0) {
                // Decode base64 dan simpan sebagai data biner mentah (seperti RM8A)
                $data['TTD_MENYERAHKAN'] = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            }
        }

        try {
            $existingData = DB::connection('sqlsrv')->table('RM80')->where('NOPENDAFTARAN', $noPendaftaran)->first();

            if ($existingData) {
                DB::connection('sqlsrv')->table('RM80')->where('NOPENDAFTARAN', $noPendaftaran)->update($data);
                $message = 'Data berhasil diperbarui.';
            } else {
                DB::connection('sqlsrv')->table('RM80')->insert($data);
                $message = 'Data berhasil disimpan.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function showImage($noPendaftaran, $field)
    {
        if (!in_array($field, ['TTD_PENERIMA', 'TTD_MENYERAHKAN'])) {
            return $this->servePlaceholder();
        }

        $imageData = DB::connection('sqlsrv')
            ->table('RM80')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->value($field);

        if (empty($imageData)) {
            return $this->servePlaceholder();
        }

        // Menangani data dari kolom IMAGE yang sering dikembalikan sebagai hex string oleh driver
        $binaryData = $imageData;
        if (strpos($imageData, '0x') === 0) {
            // Jika string diawali '0x', konversi dari hex ke biner
            $binaryData = hex2bin(substr($imageData, 2));
        } elseif (ctype_xdigit($imageData) && strpos($imageData, 'data:image') !== 0) {
            // Fallback jika hanya hex string tanpa '0x'
            $binaryData = hex2bin($imageData);
        }

        return response($binaryData, 200, [
            'Content-Type' => 'image/jpeg', // Menyamakan dengan RM8A yang menggunakan JPEG
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