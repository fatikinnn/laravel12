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
     * Metode ini bersifat public agar bisa dipanggil oleh controller form lain.
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
        $formStatuses = $this->getFormFillStatuses($noPendaftaran);

        return [
            'No Pendaftaran' => $noPendaftaran,
            'Nama Pasien'    => $result->NamaPasien,
            'Alamat'         => $result->Alamat,
            'Usia'           => $result->Usia,
            'Gender'         => trim($result->Gender),
            'Cara Masuk'     => trim($result->NamaMasuk) . " - " . trim($result->Perujuk), // Tetap sama
            'Asuransi'       => $result->Asuransi,
            'Agama'          => $result->Agama,
            'Bangsal'        => trim($result->Bangsal) ?: '-',
            'No Peserta'     => $result->NOPESERTA,
            'Tanggal Masuk'  => Carbon::parse($result->TglMasuk)->format('d/m/Y'),
            'Tanggal Keluar' => ($tgl = $result->TglKeluar) ? Carbon::parse($tgl)->format('d/m/Y') : '-',
            'Status Inap'    => trim($result->StatusInap), // Tetap sama
            'DPJP'           => trim($result->DPJP) ?: '-',
            'No Kunjungan'   => $result->NoKunjungan,
            'Tanggal Lahir'  => Carbon::parse($result->TanggalLahir)->format('d/m/Y'),
            'form_statuses'  => $formStatuses, // Menambahkan status form ke response
        ];
    }

    
    /**
     * Memeriksa status pengisian semua form RME untuk seorang pasien.
     *
     * @param  string  $noPendaftaran
     * @return array
     */
    private function getFormFillStatuses($noPendaftaran)
    {
        // Peta antara nama form (value di <option>) dan tabel-tabel database terkait.
        // Ini adalah satu-satunya tempat yang perlu Anda perbarui jika ada form baru atau perubahan tabel.
        $formTableMap = [
            'rm3a' => ['RM3A'],
            'rm3b' => ['RM3B', 'RMIGD'],
            'rm1c' => ['RM1C'],
            'rm5' => ['RM5'],
            'rm3d' => ['RM3D1', 'RM3D2', 'RM3D3'], // Contoh form dengan banyak tabel
            'rm3f' => ['RM3F'],
            'rm28' => ['RM28A', 'RM28B'],
            'rm6a_dewasa' => ['RM6A_DEWASA'],
            'rm6a' => ['RM6A'],
            'rm26' => ['RM26'],
            'rm29' => ['RM29'],
            'rm4a' => ['RM4A'],
            'rm6b' => ['RM6B'],
            'rm25a' => ['RM25A'],
            'rm8a' => ['RM8A'],
            'rm48a' => ['RM48A'],
            'rm60' => ['RM60'],
            'rm17' => ['RM17'],
            'rm16b' => ['RM16B'],
            'rm9a3' => ['RM9A3'],
            'cppt' => ['CPPTPERAWAT'],
            'rm18' => ['RM18'],
            'rm24d' => ['RM24D'],
            'rm7' => ['RM7'],
            'rm57' => ['RM57'],
            'fotopenunjang' => ['HASILUSG', 'HASILCTG', 'HASILECHO'], // Contoh untuk upload foto
            // Tambahkan form lain di sini...
        ];

        $statuses = [];
        foreach ($formTableMap as $formKey => $tables) {
            $isFilled = false;
            foreach ($tables as $table) {
                // Cek apakah ada setidaknya satu baris data di salah satu tabel terkait.
                if (DB::connection('sqlsrv')->table($table)->where('NOPENDAFTARAN', $noPendaftaran)->exists()) {
                    $isFilled = true;
                    break; // Jika sudah ketemu, tidak perlu cek tabel lain untuk form ini
                }
            }
            $statuses[$formKey] = $isFilled;
        }

        return $statuses;
    }
}