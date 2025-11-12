<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rm25aController extends Controller
{
    /**
     * Memuat data dan menampilkan view untuk form RM25A.
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

        $data = [
            'noPendaftaran' => $request->input('NoPendaftaran'),
            'norm' => $request->input('NoRM'),
            'user' => session('user'),
        ];

        return view('rme.igd.forms.rm25a.index', $data);
    }

    /**
     * Mengambil riwayat penilaian RM25A untuk pasien.
     */
    public function getHistory(Request $request)
    {
        $validator = Validator::make($request->all(), ['noPendaftaran' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'No Pendaftaran diperlukan.'], 400);
        }

        try {
            $history = DB::connection('sqlsrv')
                ->table('RM25A')
                ->where('NOPENDAFTARAN', $request->noPendaftaran)
                ->orderBy('TGL', 'asc')->orderBy('JAM', 'asc')
                ->get(['COUNTER', 'TGL', 'JAM', 'TOTAL_SKOR', 'NAMA_PETUGAS']);

            return response()->json(['status' => 'success', 'data' => $history]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil riwayat: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil detail entri RM25A untuk diedit.
     */
    public function getDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noPendaftaran' => 'required|string',
            'counter' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tidak valid.'], 400);
        }

        try {
            $detail = DB::connection('sqlsrv')
                ->table('RM25A')
                ->where('NOPENDAFTARAN', $request->noPendaftaran)
                ->where('COUNTER', $request->counter)
                ->first();

            if ($detail) {
                return response()->json(['status' => 'success', 'data' => $detail]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil detail: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menyimpan atau memperbarui data form RM25A.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'COUNTER' => 'nullable|integer',
            'TGL' => 'required|date',
            'JAM' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak valid.', 'errors' => $validator->errors()], 422);
        }

        DB::connection('sqlsrv')->beginTransaction();
        try {
            $noPendaftaran = $request->input('NOPENDAFTARAN');
            $counter = $request->input('COUNTER');
            $user = session('user')['username'] ?? 'SYSTEM';

            $toNull = fn($value) => $value === '' || $value === null ? null : $value;
            $toBit = fn($value) => $value == '1' || $value === true ? 1 : 0;

            $dataToSave = [
                'TGL' => Carbon::parse($request->input('TGL'))->format('Y-m-d'),
                'JAM' => Carbon::parse($request->input('JAM'))->format('H:i:s'),
                'UMUR_SKOR' => $toNull($request->input('UMUR_SKOR')),
                'JENISKELAMIN_SKOR' => $toNull($request->input('JENISKELAMIN_SKOR')),
                'DIAGNOSA_SKOR' => $toNull($request->input('DIAGNOSA_SKOR')),
                'GANGGUAN_KOGNI_SKOR' => $toNull($request->input('GANGGUAN_KOGNI_SKOR')),
                'FAKTOR_LINGK_SKOR' => $toNull($request->input('FAKTOR_LINGK_SKOR')),
                'TERHADAP_OPERASI_SKOR' => $toNull($request->input('TERHADAP_OPERASI_SKOR')),
                'PENGGUNAAN_OBAT_SKOR' => $toNull($request->input('PENGGUNAAN_OBAT_SKOR')),
                'TOTAL_SKOR' => $toNull($request->input('TOTAL_SKOR')),
                'RR_ORIENTASI' => $toBit($request->input('RR_ORIENTASI')),
                'RR_PASTIKAN_BEL' => $toBit($request->input('RR_PASTIKAN_BEL')),
                'RR_RODA_TT' => $toBit($request->input('RR_RODA_TT')),
                'RR_POSISIKAN_TT' => $toBit($request->input('RR_POSISIKAN_TT')),
                'RR_NAIKKAN_PAGAR' => $toBit($request->input('RR_NAIKKAN_PAGAR')),
                'RR_LAMPU_TIDUR' => $toBit($request->input('RR_LAMPU_TIDUR')),
                'RR_EDUKASI' => $toBit($request->input('RR_EDUKASI')),
                'RT_LAKUKAN_SEMUA' => $toBit($request->input('RT_LAKUKAN_SEMUA')),
                'RT_TANDA_SEGITIGA' => $toBit($request->input('RT_TANDA_SEGITIGA')),
                'RT_TANDA_RESIKO' => $toBit($request->input('RT_TANDA_RESIKO')),
                'RT_1JAM' => $toBit($request->input('RT_1JAM')),
                'RT_TEMPATKAN_PASIEN' => $toBit($request->input('RT_TEMPATKAN_PASIEN')),
                'RT_ALAT_BANTU' => $toBit($request->input('RT_ALAT_BANTU')),
                'RT_LIBATKAN_KELUARGA' => $toBit($request->input('RT_LIBATKAN_KELUARGA')),
                'NAMA_PETUGAS' => $user,
                'USER_ENTRY' => $user,
                'TGLJAM_ENTRY' => now()->format('Y-m-d H:i:s'),
            ];

            if (!empty($counter)) {
                // Operasi UPDATE
                DB::connection('sqlsrv')->table('RM25A')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $counter)
                    ->update($dataToSave);
                $message = 'Data RM25A berhasil diperbarui.';
            } else {
                // Operasi INSERT
                $lastCounter = DB::connection('sqlsrv')->table('RM25A')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->max('COUNTER');
                $newCounter = ($lastCounter ?? 0) + 1;

                $dataToSave['NOPENDAFTARAN'] = $noPendaftaran;
                $dataToSave['COUNTER'] = $newCounter;

                DB::connection('sqlsrv')->table('RM25A')->insert($dataToSave);
                $message = 'Data RM25A berhasil disimpan.';
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }
}