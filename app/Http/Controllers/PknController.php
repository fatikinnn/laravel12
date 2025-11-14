<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PknController extends Controller
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
        $patientDetails = $request->all();

        $conn = $this->getOdbcConnection();

        $query = "SELECT * FROM PKN_IGD WHERE NOPENDAFTARAN = ?";
        $stmt = odbc_prepare($conn, $query);
        odbc_execute($stmt, [$noPendaftaran]);

        $data = odbc_fetch_array($stmt);

        if (!$data) {
            $data = [];
        }

        odbc_close($conn);

        return view('rme.igd.forms.pkn.index', [
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
        $checkQuery = "SELECT NOPENDAFTARAN FROM PKN_IGD WHERE NOPENDAFTARAN = ?";
        $checkStmt = odbc_prepare($conn, $checkQuery);
        odbc_execute($checkStmt, [$noPendaftaran]);
        $isUpdate = odbc_fetch_row($checkStmt);

        $fields = [
            // 0-6 Jam
            'BB_0JAM', 'PB_0JAM', 'LK_0JAM', 'DINI_0JAM', 'VITK1_0JAM', 'SALEP_0JAM', 'IMUNISASI_0JAM',
            'TGL_0JAM', 'JAM_0JAM', 'NOMORBATCH_0JAM', 'PPIA_0JAM', 'MASALAH_0JAM', 'DIRUJUK_0JAM', 'NAMATK_0JAM',
            // 6-48 Jam
            'MENYUSU_6JAM', 'TALIPUSAT_6JAM', 'VITK1_6JAM', 'SALEPMATA_6JAM', 'IMUNISASI_6JAM',
            'TGL_6JAM', 'JAM_6JAM', 'NOMORBATCH_6JAM', 'BB_6JAM', 'PB_6JAM', 'LK_6JAM', 'KONGENITAL_6JAM',
            'PPIA_6JAM', 'MASALAH_6JAM', 'DIRUJUK_6JAM', 'NAMATK_6JAM',
            // 3-7 Hari
            'MENYUSU_3HR', 'TALIPUSAT_3HR', 'TANDABAHAYA_3HR', 'KUNING_3HR', 'IMUNISASI_3HR',
            'TGL_3HR', 'JAM_3HR', 'NOMORBATCH_3HR', 'BB_3HR', 'PB_3HR', 'LK_3HR', 'KONGENITAL_3HR',
            'PPIA_3HR', 'MASALAH_3HR', 'DIRUJUK_3HR', 'NAMATK_3HR',
            // 8-28 Hari
            'MENYUSU_8HR', 'TALIPUSAT_8HR', 'TANDABAHAYA_8HR', 'KUNING_8HR', 'KEPALA_8HR',
            'TANGANKANAN_8HR', 'TANGANKIRI_8HR', 'LENGANKANAN_8HR', 'LENGAKKIRI_8HR', 'DADA_8HR',
            'PERUT_8HR', 'BETISKANAN_8HR', 'BETISKIRI_8HR', 'TELAPKAKIKANAN_8HR', 'TELAPAKKAKIKIRI_8HR',
            'PPIA_8HR', 'MASALAH_8HR', 'DIRUJUK_8HR', 'NAMATK_8HR'
        ];

        $checkboxes = [
            'DINI_0JAM', 'VITK1_0JAM', 'SALEP_0JAM', 'IMUNISASI_0JAM', 'MENYUSU_6JAM', 'TALIPUSAT_6JAM',
            'VITK1_6JAM', 'SALEPMATA_6JAM', 'IMUNISASI_6JAM', 'KONGENITAL_6JAM', 'MENYUSU_3HR', 'TALIPUSAT_3HR',
            'TANDABAHAYA_3HR', 'KUNING_3HR', 'IMUNISASI_3HR', 'KONGENITAL_3HR', 'MENYUSU_8HR', 'TALIPUSAT_8HR',
            'TANDABAHAYA_8HR', 'KUNING_8HR'
        ];

        $selectedParts = json_decode($request->input('selected_parts', '{}'), true);
        $bodyParts = [
            'KEPALA_8HR' => 'kepala', 'TANGANKANAN_8HR' => 'tangan_kanan', 'TANGANKIRI_8HR' => 'tangan_kiri',
            'LENGANKANAN_8HR' => 'lengan_kanan', 'LENGAKKIRI_8HR' => 'lengan_kiri', 'DADA_8HR' => 'dada',
            'PERUT_8HR' => 'perut', 'BETISKANAN_8HR' => 'paha_kanan', 'BETISKIRI_8HR' => 'paha_kiri',
            'TELAPKAKIKANAN_8HR' => 'kaki_kanan', 'TELAPAKKAKIKIRI_8HR' => 'kaki_kiri'
        ];

        $data = [];
        foreach ($fields as $field) {
            if (in_array($field, $checkboxes)) {
                $data[$field] = $request->has($field) ? 1 : 0;
            } elseif (array_key_exists($field, $bodyParts)) {
                $data[$field] = isset($selectedParts[$bodyParts[$field]]) && $selectedParts[$bodyParts[$field]] ? 1 : 0;
            } else {
                $data[$field] = $request->input($field) ?: null;
            }
        }

        if ($isUpdate) {
            $setClauses = [];
            $params = [];
            foreach ($data as $key => $value) {
                $setClauses[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $noPendaftaran;

            $query = "UPDATE PKN_IGD SET USER_ENTRY = ?, TGL_ENTRY = GETDATE(), " . implode(', ', $setClauses) . " WHERE NOPENDAFTARAN = ?";
            array_unshift($params, $user);
        } else {
            $columns = array_keys($data);
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $params = array_values($data);

            $query = "INSERT INTO PKN_IGD (NOPENDAFTARAN, USER_ENTRY, TGL_ENTRY, " . implode(', ', $columns) . ") VALUES (?, ?, GETDATE(), $placeholders)";
            array_unshift($params, $user);
            array_unshift($params, $noPendaftaran);
        }

        $stmt = odbc_prepare($conn, $query);
        if (!$stmt) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mempersiapkan statement: ' . odbc_errormsg($conn)], 500);
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
