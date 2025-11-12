<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CpptController extends Controller
{
    /**
     * Memuat view utama untuk form CPPT.
     */
    public function load(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoPendaftaran' => 'required|string',
            'NoRM' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response('Parameter tidak valid.', 400);
        }

        // Menggunakan Auth facade untuk konsistensi, bisa null jika sesi habis
        $user = \Illuminate\Support\Facades\Auth::user();

        $data = [
            'noPendaftaran' => $request->input('NoPendaftaran'),
            'norm' => $request->input('NoRM'),
            'user' => $user,
        ];

        return view("rme.igd.forms.cppt.index", $data);
    }

    /**
     * Mengambil riwayat CPPT pasien.
     */
    public function getHistory(Request $request)
    {
        $validator = Validator::make($request->all(), ['noPendaftaran' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'No Pendaftaran diperlukan.'], 400);
        }

        try {
            $history = DB::connection('sqlsrv')
                ->select('exec LapVisitDokter_fatikin ?', [$request->noPendaftaran]);

            // Membersihkan data sebelum dikirim
            $cleanedHistory = array_map(function($item) {
                // Konversi ke UTF-8 untuk mencegah error JSON encoding
                $toUtf8 = fn($str) => mb_convert_encoding($str ?? '', 'UTF-8', 'ISO-8859-1');

                return [
                    'TanggalVisit' => trim($item->TanggalVisit),
                    'Profesi' => $toUtf8(trim($item->Profesi)),
                    'Perjalanan' => $toUtf8(trim($item->Perjalanan)), // SOAP
                    'Pengobatan' => $toUtf8(trim($item->Pengobatan)), // Instruksi/Plan
                    'UserEntry' => $toUtf8(trim($item->UserEntry)),
                ];
            }, $history);

            return response()->json(['status' => 'success', 'data' => $cleanedHistory]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil riwayat: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil detail entri CPPT untuk diedit.
     */
    public function getDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noPendaftaran' => 'required|string',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'nullable|string', // Jam dibuat opsional
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tidak valid.'], 400);
        }

        try {
            $query = DB::connection('sqlsrv')
                ->table('CPPTPERAWAT')
                ->where('NOPENDAFTARAN', $request->noPendaftaran)
                ->where('Tanggal', $request->tanggal); // Meniru logika native

            $detail = null;
            $rawTime = trim($request->input('jam'));

            // Langkah 1: Coba cari dengan waktu yang spesifik jika valid
            if (!empty($rawTime)) {
                $detail = (clone $query)->whereRaw('LTRIM(RTRIM(JAM)) = ?', [$rawTime])->first(); // Handle spasi di kolom CHAR
            }

            // Langkah 2: Jika tidak ketemu (atau waktu tidak valid), cari data pertama pada hari itu.
            // Ini memastikan form tetap terisi meskipun jam dari riwayat tidak lengkap/rusak.
            if (!$detail) {
                $detail = $query->orderBy('JAM', 'asc')->first();
            }

            if ($detail) {
                return response()->json(['status' => 'success', 'data' => $detail]);
            } else {
                // Hanya gagal jika SAMA SEKALI tidak ada data pada tanggal tersebut.
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil detail: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Memeriksa apakah data CPPT sudah ada.
     */
    public function checkData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noPendaftaran' => 'required|string',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'nullable|string', // Jam dibuat opsional
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tidak valid.'], 400);
        }

        try {
            $rawTime = trim($request->input('jam'));
            if (empty($rawTime)) {
                return response()->json(['status' => 'not_exists']); // Jika jam tidak valid, anggap tidak ada
            }

            $count = DB::connection('sqlsrv')
                ->table('CPPTPERAWAT')
                ->where('NOPENDAFTARAN', $request->noPendaftaran)
                ->where('Tanggal', $request->tanggal) // Meniru logika native
                ->whereRaw('LTRIM(RTRIM(JAM)) = ?', [$rawTime]) // Handle spasi di kolom CHAR
                ->count();

            return response()->json(['status' => $count > 0 ? 'exists' : 'not_exists']);        } catch (\Exception $e) {            return response()->json(['status' => 'error', 'message' => 'Gagal memeriksa data: ' . $e->getMessage()], 500);        }    }

    /**
     * Menyimpan atau memperbarui data CPPT.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'TANGGAL' => 'required|date',
            'JAM' => 'required|string', // Ubah validasi agar lebih fleksibel
            'CATATAN' => 'nullable|string',
            'KESADARAN' => 'nullable|string', 'SKOR' => 'nullable|string',
            'BB' => 'nullable|string', 'TB' => 'nullable|string', 'PERORAL' => 'nullable|string',
            'PARENTERAL' => 'nullable|string', 'MLAIN' => 'nullable|string', 'URIN' => 'nullable|string',
            'MUNTAH' => 'nullable|string', 'KLAIN' => 'nullable|string',
            'PEMERIKSAAN' => 'nullable|string',
            'INSTRUKSI' => 'nullable|string',
            'is_update' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap.', 'errors' => $validator->errors()], 422);
        }

        try {
            // Mengambil username dengan fallback untuk mencegah error sesi habis
            $loggedInUser = \Illuminate\Support\Facades\Auth::user();
            $sessionUser = session('user');

            // Prioritaskan user dari Auth, fallback ke session, lalu ke default
            $username = $loggedInUser->username ?? $sessionUser['username'] ?? 'default_user';

            // Format ulang jam sebelum dikirim ke Stored Procedure untuk memastikan konsistensi
            $formattedJam = $this->formatTimeInput($request->input('JAM'));

            $params = [
                $request->input('NOPENDAFTARAN'),
                $request->input('TANGGAL'),
                $formattedJam,
                $request->input('KESADARAN'),
                $request->input('SKOR'),
                $request->input('BB'),
                $request->input('TB'),
                $request->input('PERORAL'),
                $request->input('PARENTERAL'),
                $request->input('MLAIN'),
                $request->input('URIN'),
                $request->input('MUNTAH'),
                $request->input('KLAIN'),
                $request->input('CATATAN'),
                $request->input('PEMERIKSAAN'),
                $request->input('INSTRUKSI'),
                $username
            ];

            DB::connection('sqlsrv')->statement('exec SavecpptinapSP_fat ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?', $params);

            $isUpdate = $request->input('is_update') === 'true';
            $message = $isUpdate ? 'Data CPPT berhasil diperbarui.' : 'Data CPPT berhasil disimpan.';

            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Membersihkan dan memformat string waktu menjadi format H:i:s.
     *
     * @param string|null $timeString
     * @return string|null
     */
    private function formatTimeInput($timeString)
    {
        if (empty(trim($timeString))) {
            return null;
        }

        // Pisahkan jam, menit, detik
        $parts = explode(':', trim($timeString));
        
        // Jika formatnya aneh (misal tidak ada ':'), kembalikan null
        if (count($parts) < 2) {
            return null;
        }

        // Pastikan setiap bagian memiliki 2 digit dengan padding '0' di depan
        $hour = isset($parts[0]) && is_numeric($parts[0]) ? str_pad($parts[0], 2, '0', STR_PAD_LEFT) : '00';
        $minute = isset($parts[1]) && is_numeric($parts[1]) ? str_pad($parts[1], 2, '0', STR_PAD_LEFT) : '00';
        $second = isset($parts[2]) && is_numeric($parts[2]) ? str_pad($parts[2], 2, '0', STR_PAD_LEFT) : '00';

        return "{$hour}:{$minute}:{$second}";
    }
}