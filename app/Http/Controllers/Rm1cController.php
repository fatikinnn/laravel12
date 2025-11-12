<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rm1cController extends Controller
{
    /**
     * Memuat data dan menampilkan view untuk form RM1C.
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

        // Mengambil data yang sudah ada dari RM1C dan RM3B
        $row = DB::connection('sqlsrv')
            ->table('RM3B')
            ->leftJoin('RM1C', 'RM3B.NOPENDAFTARAN', '=', 'RM1C.NOPENDAFTARAN')
            ->where('RM3B.NOPENDAFTARAN', $noPendaftaran)
            ->select('RM3B.DIAGNOSIS_UTAMA', 'RM1C.*')
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
            ->map(function ($value, $key) {
                return trim($value);
            })->all();

        $data = [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'row' => $row ? (array) $row : [], // Konversi ke array agar konsisten dengan kode native
            'namaPasien' => $patientDetails['Nama Pasien'],
            'tanggalLahir' => $patientDetails['Tanggal Lahir'], // Format sudah d/m/Y
            'gender' => $patientDetails['Gender'],
            'dokterList' => $dokterList,
            'jabatanList' => $jabatanList,
        ];

        return view('rme.igd.forms.rm1c.index', $data);
    }

    /**
     * Menyimpan atau memperbarui data RM1C.
     */
    public function storeOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
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
            'BANGSAL' => $request->input('bangsal') ? 1 : 0,
            'ICU' => $request->input('icu') ? 1 : 0,
            'ISOLASI' => $request->input('isolasi') ? 1 : 0,
            'TGLMASUK' => $request->input('tglmasuk'),
            'JAMMASUK' => $request->input('jammasuk'),
            'TGL_ENTRY' => Carbon::now()->format('Y-m-d H:i:s'),
            // Field yang tidak ada di form, beri nilai default
            'CLINIC_PATH' => 0,
            'IGD' => 0, // Asumsi dari IGD
            'RANAP' => 0, // Asumsi untuk Rawat Inap
        ];

        try {
            // Cek apakah data sudah ada
            $existing = DB::connection('sqlsrv')->table('RM1c')->where('NOPENDAFTARAN', $noPendaftaran)->first();

            if ($existing) {
                // Update data
                DB::connection('sqlsrv')->table('RM1c')->where('NOPENDAFTARAN', $noPendaftaran)->update($data);
                $message = 'Data RM1C berhasil diperbarui.';
            } else {
                // Insert data baru
                $data['NOPENDAFTARAN'] = $noPendaftaran;
                $data['NORM'] = $request->input('NORM');
                DB::connection('sqlsrv')->table('RM1c')->insert($data);
                $message = 'Data RM1C berhasil disimpan.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            // Tangani error database
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}

?>