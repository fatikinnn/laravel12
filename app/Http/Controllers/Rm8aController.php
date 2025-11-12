<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Rm8aController extends Controller
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
            'user' => session('user'), // Menggunakan session('user') agar konsisten
        ];

        return view('rme.igd.forms.rm8a.index', $data);
    }

    public function history(Request $request)
    {
        $validator = Validator::make($request->all(), ['noPendaftaran' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'No Pendaftaran diperlukan.'], 400);
        }

        $history = DB::connection('sqlsrv')
            ->table('RM8A')
            ->where('NOPENDAFTARAN', $request->noPendaftaran)
            ->orderBy('TGL', 'asc')->orderBy('JAM', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $history]);
    }

    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noPendaftaran' => 'required',
            'counter' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tidak valid.'], 400);
        }

        $detail = DB::connection('sqlsrv')
            ->table('RM8A')
            ->where('NOPENDAFTARAN', $request->noPendaftaran)
            ->where('COUNTER', $request->counter)
            ->first();

        if (!$detail) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $detail]);
    }

    public function store(Request $request)
    {
        // 1. Validasi semua input yang masuk
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'COUNTER' => 'nullable|integer',
            'HARI' => 'required|string',
            'TGL' => 'required|date',
            'JAM' => 'required|date_format:H:i',
            'PENERIMA_EDUKASI' => 'required|string',
            'METODE' => 'required|string',
            'MATERI_EDUKASI' => 'required|string',
            'ISI_PEND_KESEHATAN' => 'nullable|string',
            'PEMBERI_EDUKASI' => 'required|string',
            'EVALUASI_RESPON' => 'required|string',
            'NAMA_PENERIMA_EDUKASI' => 'nullable|string',
            'TTD_PENERIMA' => 'nullable|string', // Base64 string
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak valid.', 'errors' => $validator->errors()], 422);
        }

        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $counter = $request->input('COUNTER');
        // Menggunakan session('user') agar konsisten dengan form lain
        $user = session('user');
        $username = $user['username'] ?? 'default_user';
        $now = Carbon::now();

        // 2. Siapkan data untuk disimpan
        $data = $request->only([
            'HARI', 'TGL', 'JAM', 'PENERIMA_EDUKASI', 'METODE', 'MATERI_EDUKASI',
            'ISI_PEND_KESEHATAN', 'PEMBERI_EDUKASI', 'EVALUASI_RESPON',
            'NAMA_PENERIMA_EDUKASI'
        ]);

        // Selalu update user dan waktu entry
        $data['USER_ENTRY'] = $username;
        $data['TGLJAM_ENTRY'] = $now->format('Y-m-d H:i:s');
        $data['NAMA_PEMBERI_EDUKASI'] = $username; // Nama pemberi edukasi diisi otomatis dari user login

        // 3. Handle TTD (Tanda Tangan)
        if ($request->has('TTD_PENERIMA') && !empty($request->input('TTD_PENERIMA'))) {
            $base64Image = $request->input('TTD_PENERIMA');
            if (strpos($base64Image, 'data:image') === 0) {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                // Simpan sebagai data biner mentah. Ini lebih efisien daripada hex string.
                // Laravel akan menangani binding data biner ini dengan benar.
                $data['TTD_PENERIMA'] = $imageData;
            }
        } else {
            $data['TTD_PENERIMA'] = null;
        }

        try {
            // 4. Logika Insert atau Update
            if (empty($counter)) {
                // Jika INSERT baru, tentukan COUNTER berikutnya
                $nextCounter = DB::connection('sqlsrv')->table('RM8A')
                                 ->where('NOPENDAFTARAN', $noPendaftaran)
                                 ->max('COUNTER') + 1;
                $data['COUNTER'] = $nextCounter;
                $data['NOPENDAFTARAN'] = $noPendaftaran;
                DB::connection('sqlsrv')->table('RM8A')->insert($data);
                $message = 'Data berhasil disimpan.';
            } else {
                // Jika UPDATE, gunakan counter yang ada
                DB::connection('sqlsrv')->table('RM8A')->where('NOPENDAFTARAN', $noPendaftaran)->where('COUNTER', $counter)->update($data);
                $message = 'Data berhasil diperbarui.';
            }
            return response()->json(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function showImage($noPendaftaran, $counter)
    {
        // Ambil data 'image' apa adanya. Konversi akan ditangani di PHP.
        // Ini lebih kompatibel dengan SQL Server 2000 yang tidak mendukung CONVERT(VARCHAR(MAX),...).
        $imageData = DB::connection('sqlsrv')
            ->table('RM8A')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->where('COUNTER', $counter)
            ->value('TTD_PENERIMA');

        if (empty($imageData)) {
            return $this->servePlaceholder();
        }

        // Driver database untuk SQL Server sering mengembalikan tipe data 'image'
        // sebagai string heksadesimal yang diawali dengan '0x'.
        // Jika tidak, mungkin sudah dalam bentuk biner.
        $binaryData = $imageData;
        if (strpos($imageData, '0x') === 0) {
            $binaryData = hex2bin(substr($imageData, 2));
        } elseif (ctype_xdigit($imageData)) {
            // Fallback jika driver mengembalikan hex string tanpa '0x'
            // Ini jarang terjadi, tapi menambahkan ketahanan.
            $binaryData = hex2bin($imageData);
        }

        // Buat respons dengan data biner dan atur header Content-Type yang benar.
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