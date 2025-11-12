<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rm5Controller extends Controller
{
    /**
     * Memuat data dan menampilkan view untuk form RM5.
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

        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');

        // Mengambil data pasien dasar dari RmeIgdController untuk konsistensi
        $rmeIgdController = new RmeIgdController();
        $patientDetails = $rmeIgdController->getPatientDetailsData($noPendaftaran);

        if (isset($patientDetails['error'])) {
            return response('Data pasien tidak ditemukan.', 404);
        }

        // Mengambil data yang sudah ada dari RM5
        $row = DB::connection('sqlsrv')
            ->table('RM5')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // Mengambil daftar dokter
        $dokterList = DB::connection('sqlsrv')
            ->table('pemeriksa')
            ->where('ACTIVE', '1')
            ->where('NAMAPEMERIKSA', 'like', '%dr.%')
            ->get(['NAMAPEMERIKSA', 'KDJABATAN']);

        // Mengambil daftar jabatan (key = KDJABATAN, value = NAMAJABATAN)
        $jabatanList = DB::connection('sqlsrv')
            ->table('jabpemeriksa')
            ->pluck('NAMAJABATAN', 'KDJABATAN')
            ->map(fn ($value) => trim($value))
            ->all();

        $data = [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'row' => $row ? (array) $row : [],
            'namaPasien' => $patientDetails['Nama Pasien'],
            'tanggalLahir' => $patientDetails['Tanggal Lahir'], // Format d/m/Y
            'gender' => $patientDetails['Gender'],
            'dokterList' => $dokterList,
            'jabatanList' => $jabatanList,
        ];

        return view('rme.igd.forms.rm5.index', $data);
    }

    /**
     * Menyimpan atau memperbarui data RM5.
     */
    public function storeOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'NORM' => 'required|string',
            'namadokter' => 'required|string',
            'diag_masuk' => 'nullable|string',
            'alasan' => 'nullable|string',
            'spesialisasi' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap.', 'errors' => $validator->errors()], 422);
        }

        $noPendaftaran = $request->input('NOPENDAFTARAN');

        $data = [
            'NAMADOKTER' => $request->input('namadokter'),
            'JAB_UMUM' => $request->input('jab_umum') ? 1 : 0,
            'JAB_SPES' => $request->input('jab_spes') ? 1 : 0,
            'KET_SPES' => $request->input('ket_spes'),
            'NAMAPASIEN' => $request->input('namapasien'),
            'TGLLAHIR' => Carbon::parse($request->input('tgllahir'))->format('Y-m-d'),
            'JK' => $request->input('jk'),
            'DIAG_MASUK' => $request->input('diag_masuk'),
            'ALASAN' => $request->input('alasan'),
            'SPESIALISASI' => $request->input('spesialisasi'),
            'TGLMASUK' => $request->input('tglmasuk'),
            'JAMMASUK' => $request->input('jammasuk'),
            'TGL_ENTRY' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        try {
            $existing = DB::connection('sqlsrv')->table('RM5')->where('NOPENDAFTARAN', $noPendaftaran)->first();

            if ($existing) {
                DB::connection('sqlsrv')->table('RM5')->where('NOPENDAFTARAN', $noPendaftaran)->update($data);
                $message = 'Data RM5 berhasil diperbarui.';
            } else {
                $data['NOPENDAFTARAN'] = $noPendaftaran;
                $data['NORM'] = $request->input('NORM');
                DB::connection('sqlsrv')->table('RM5')->insert($data);
                $message = 'Data RM5 berhasil disimpan.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
