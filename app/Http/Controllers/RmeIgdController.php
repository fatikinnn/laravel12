<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RmeIgdController extends Controller
{
    /**
     * Menampilkan halaman RME IGD.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Ambil role dari session
        $userAccess = session('user.access');

        // Daftar role yang diizinkan mengakses halaman ini
        $allowedAccess = ['DOKTER', 'DOKTER UMUM', 'PERAWAT', 'PELAYANAN', 'admin', 'BIDAN'];

        // Periksa apakah role pengguna ada dalam daftar yang diizinkan
        if (!in_array($userAccess, $allowedAccess)) {
            // Jika tidak diizinkan, redirect ke dashboard dengan pesan error
            return redirect()->route('dashboard')->with('swal-error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Hanya menampilkan view dasar, data akan dimuat via AJAX
        return view('rme.igd.index');
    }

    /**
     * Mencari riwayat kunjungan pasien berdasarkan NoRM (AJAX).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchVisits(Request $request)
    {
        $request->validate(['norm' => 'required|string|max:6']);
        $norm = $request->input('norm');

        // Menjalankan Stored Procedure CekAskepRmSP
        $visits = DB::connection('sqlsrv')->select('exec CekAskepRmSP ?', [$norm]);

        return response()->json($visits);
    }

    /**
     * Mengambil detail pasien berdasarkan NoPendaftaran (AJAX).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPatientDetails(Request $request)
    {
        $request->validate(['NoPendaftaran' => 'required|string']);
        $details = $this->getPatientDetailsData($request->input('NoPendaftaran'));

        if (isset($details['error'])) {
            return response()->json($details, 404);
        }

        return response()->json($details);
    }

    /**
     * Logika untuk mengambil dan memformat detail pasien.
     *
     * @param  string  $noPendaftaran
     * @return array
     */
    public function getPatientDetailsData($noPendaftaran)
    {
        // Menjalankan Stored Procedure CekAskepDaftarSP
        $patientData = DB::connection('sqlsrv')->select('exec CekAskepDaftarSP ?', [$noPendaftaran]);

        if (empty($patientData)) {
            return ['error' => 'Data pasien tidak ditemukan'];
        }

        $result = $patientData[0];
        return [
            'No Pendaftaran' => $noPendaftaran,
            'Nama Pasien'    => $result->NamaPasien,
            'Alamat'         => $result->Alamat,
            'Usia'           => $result->Usia,
            'Gender'         => trim($result->Gender),
            'Cara Masuk'     => trim($result->NamaMasuk) . " - " . trim($result->Perujuk),
            'Asuransi'       => $result->Asuransi,
            'Agama'          => $result->Agama,
            'Bangsal'        => trim($result->Bangsal) ?: '-',
            'No Peserta'     => $result->NOPESERTA,
            'Tanggal Masuk'  => Carbon::parse($result->TglMasuk)->format('d/m/Y'),
            'Tanggal Keluar' => ($tgl = $result->TglKeluar) ? Carbon::parse($tgl)->format('d/m/Y') : '-',
            'Status Inap'    => trim($result->StatusInap),
            'DPJP'           => trim($result->DPJP) ?: '-',
            'No Kunjungan'   => $result->NoKunjungan,
            'Tanggal Lahir'  => Carbon::parse($result->TanggalLahir)->format('d/m/Y'),
        ];
    }
}