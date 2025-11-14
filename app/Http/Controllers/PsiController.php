<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PsiController extends Controller
{
    protected function getOdbcConnection()
    {
        $dsn = "SATUSEHAT";
        $user = "SATUSEHAT";
        $password = "satusehatMB2023";
        $conn = odbc_connect($dsn, $user, $password);

        if (!$conn) {
            // In a real app, you'd want to handle this more gracefully
            die("Koneksi ODBC gagal: " . odbc_errormsg());
        }
        return $conn;
    }

    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $patientDetails = $request->all(); // All patient details sent from the main page

        $conn = $this->getOdbcConnection();

        $query = "SELECT TOP 1 * FROM PSI WHERE NOPENDAFTARAN = ? ORDER BY NOPENDAFTARAN DESC";
        $stmt = odbc_prepare($conn, $query);
        odbc_execute($stmt, [$noPendaftaran]);

        $data = odbc_fetch_array($stmt);

        if (!$data) {
            $data = [];
        }

        odbc_close($conn);

        return view('rme.igd.forms.psi.index', [
            'data' => $data,
            'patient' => $patientDetails,
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm
        ]);
    }

    public function store(Request $request)
    {
        $conn = $this->getOdbcConnection();
        $user = session('user.username', 'SYSTEM');

        $noPendaftaran = $request->input('NOPENDAFTARAN');

        // Check if data exists
        $checkQuery = "SELECT NOPENDAFTARAN FROM PSI WHERE NOPENDAFTARAN = ?";
        $checkStmt = odbc_prepare($conn, $checkQuery);
        odbc_execute($checkStmt, [$noPendaftaran]);
        $isUpdate = odbc_fetch_row($checkStmt);

        if ($isUpdate) {
            // UPDATE
            $query = "UPDATE PSI SET 
                        USER_ENTRY = ?,
                        TGLJAM_ENTRY = GETDATE(),
                        USIA = ?,
                        LAKILAKI = ?,
                        PEREMPUAN = ?,
                        PANTI_WERDA_10 = ?,
                        KEGANASAN_30 = ?,
                        PENYAKIT_HATI_20 = ?,
                        PENYAKIT_JANTUNG_10 = ?,
                        PENYAKIT_SEREBRO_10 = ?,
                        PENYAKIT_GINJAL_10 = ?,
                        GANGGUAN_KESADARAN_20 = ?,
                        FREKUEN_NAFAS_20 = ?,
                        TD_SISTOLIK_20 = ?,
                        SUHU_TUBUH_15 = ?,
                        NADI_10 = ?,
                        PH_30 = ?,
                        UREUM_20 = ?,
                        NATRIUM_20 = ?,
                        GLUKOSA_10 = ?,
                        HEMATOKRIT_10 = ?,
                        DARAHARTERI_10 = ?,
                        EFUSI_PLEURA_10 = ?,
                        JUMLAH = ?,
                        KESIMPULAN = ?,
                        OPSI_PAO2 = ?,
                        OPSI_RADIOLOGI = ?,
                        OPSI_TEKANANDIASTOLIK = ?
                      WHERE NOPENDAFTARAN = ?";
        } else {
            // INSERT
            $query = "INSERT INTO PSI (
                        NOPENDAFTARAN, USER_ENTRY, TGLJAM_ENTRY, USIA, LAKILAKI, PEREMPUAN,
                        PANTI_WERDA_10, KEGANASAN_30, PENYAKIT_HATI_20, PENYAKIT_JANTUNG_10,
                        PENYAKIT_SEREBRO_10, PENYAKIT_GINJAL_10, GANGGUAN_KESADARAN_20,
                        FREKUEN_NAFAS_20, TD_SISTOLIK_20, SUHU_TUBUH_15, NADI_10, PH_30,
                        UREUM_20, NATRIUM_20, GLUKOSA_10, HEMATOKRIT_10, DARAHARTERI_10,
                        EFUSI_PLEURA_10, JUMLAH, KESIMPULAN, OPSI_PAO2, OPSI_RADIOLOGI,
                        OPSI_TEKANANDIASTOLIK
                      ) VALUES (
                        ?, ?, GETDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                      )";
        }

        $stmt = odbc_prepare($conn, $query);

        $params = [
            $request->input('USIA'),
            $request->has('GENDER') && $request->input('GENDER') == 'L' ? 1 : 0,
            $request->has('GENDER') && $request->input('GENDER') == 'P' ? 1 : 0,
            $request->has('PANTI_WERDA_10') ? 1 : 0,
            $request->has('KEGANASAN_30') ? 1 : 0,
            $request->has('PENYAKIT_HATI_20') ? 1 : 0,
            $request->has('PENYAKIT_JANTUNG_10') ? 1 : 0,
            $request->has('PENYAKIT_SEREBRO_10') ? 1 : 0,
            $request->has('PENYAKIT_GINJAL_10') ? 1 : 0,
            $request->has('GANGGUAN_KESADARAN_20') ? 1 : 0,
            $request->has('FREKUEN_NAFAS_20') ? 1 : 0,
            $request->has('TD_SISTOLIK_20') ? 1 : 0,
            $request->has('SUHU_TUBUH_15') ? 1 : 0,
            $request->has('NADI_10') ? 1 : 0,
            $request->has('PH_30') ? 1 : 0,
            $request->has('UREUM_20') ? 1 : 0,
            $request->has('NATRIUM_20') ? 1 : 0,
            $request->has('GLUKOSA_10') ? 1 : 0,
            $request->has('HEMATOKRIT_10') ? 1 : 0,
            $request->has('DARAHARTERI_10') ? 1 : 0,
            $request->has('EFUSI_PLEURA_10') ? 1 : 0,
            $request->input('JUMLAH'),
            $request->input('KESIMPULAN'),
            $request->has('OPSI_PAO2') ? 1 : 0,
            $request->has('OPSI_RADIOLOGI') ? 1 : 0,
            $request->has('OPSI_TEKANANDIASTOLIK') ? 1 : 0,
        ];

        if ($isUpdate) {
            array_unshift($params, $user);
            $params[] = $noPendaftaran;
        } else {
            array_unshift($params, $user);
            array_unshift($params, $noPendaftaran);
        }

        $result = odbc_execute($stmt, $params);

        if (!$result) {
            $error = odbc_errormsg($conn);
            odbc_close($conn);
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $error], 500);
        }

        odbc_close($conn);

        return response()->json([
            'status' => 'success',
            'message' => $isUpdate ? 'Data berhasil diperbarui' : 'Data berhasil disimpan'
        ]);
    }
}