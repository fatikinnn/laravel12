<?php

namespace App\Http\Controllers\VisiteDokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisiteDokterController extends Controller
{
    /**
     * Menampilkan halaman Visite Dokter dan mengirimkan daftar dokter.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Query untuk mendapatkan daftar dokter
        $sqlDokter = "SELECT NamaPemeriksa, NoPemeriksa FROM Pemeriksa WHERE Active = 1 AND KdJabatan NOT IN(40,41,43,44) ORDER BY NamaPemeriksa";
        
        try {
            $doctors = DB::connection('sqlsrv')->select($sqlDokter);
        } catch (\Exception $e) {
            // Jika gagal mengambil data dokter, kirim array kosong dan mungkin log error
            // Log::error("Gagal mengambil daftar dokter: " . $e->getMessage());
            $doctors = [];
        }

        return view('visite-dokter.index', compact('doctors'));
    }

    /**
     * Mencari pasien rawat inap berdasarkan No.RM, Nama, atau Nama Dokter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPatients(Request $request)
    {
        $searchTerm = $request->input('search', '');

        // Jika tidak ada parameter pencarian, kembalikan array kosong
        if (empty($searchTerm)) {
            return response()->json([]);
        }

        try {
            // Menjalankan Stored Procedure CekPasienInapNew_FATIKIN
            $patients = DB::connection('sqlsrv')->select(
                'EXEC CekPasienInapNew_FATIKIN @Search = ?',
                [$searchTerm]
            );

            return response()->json($patients);
        } catch (\Exception $e) {
            // Log error dan kirim response error
            // Log::error('Error saat mencari pasien inap: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mencari data pasien.'], 500);
        }
    }

    /**
     * Mengambil detail pasien menggunakan SP LoadPasien.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPatientDetails(Request $request)
    {
        $norm = $request->input('norm');

        if (!$norm) {
            return response()->json(['error' => 'No. Rekam Medis tidak disediakan.'], 400);
        }

        try {
            // Menjalankan Stored Procedure LoadPasien
            // Kita gunakan selectOne karena SP ini diharapkan mengembalikan satu baris data
            $patientDetails = DB::connection('sqlsrv')->selectOne(
                'EXEC LoadPasien @NoRM = ?',
                [$norm]
            );

            return response()->json($patientDetails);
        } catch (\Exception $e) {
            // Log error dan kirim response error
            // Log::error('Error saat mengambil detail pasien: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil detail pasien.'], 500);
        }
    }
}