<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class McuController extends Controller
{
    /**
     * Menampilkan halaman monitoring MCU.
     */
    public function monitoringIndex(Request $request)
    {
        // Hanya mengembalikan view, data akan di-load oleh DataTables via AJAX.
        return view('monitoring-mcu.index');
    }

    /**
     * Menyediakan data untuk DataTables di halaman monitoring MCU.
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::connection('sqlsrv')
                ->table('MCU_NEW as m')
                ->join('DIP as d', 'm.NOPENDAFTARAN', '=', 'd.NOPENDAFTARAN')
                ->join('Kecamatan as k', 'd.NoKec', '=', 'k.NoKec')
                ->select(
                    'm.TGLJAM_ENTRY',
                    'd.NAMAPASIEN',
                    'd.NIK',
                    'd.TANGGALLAHIR',
                    DB::raw("d.Alamat + ' Rt.' + d.Rt + '/' + d.Rw + ' - ' + k.NamaKecamatan AS ALAMAT"),
                    DB::raw("CASE WHEN d.GENDER = 'P' THEN 'PEREMPUAN' ELSE 'LAKI-LAKI' END as JNKEL"),
                    'd.NORM',
                    'm.TB',
                    'm.BB',
                    'm.IMT',
                    'm.NADI',
                    'm.PEMERIKSAANFISIK',
                    'm.KESIMPULAN'
                )
                ; // Hapus orderBy dari sini

            return datatables()->of($data)->make(true);
        }
        return abort(404);
    }

    /**
     * Memuat form MCU dengan data yang ada dan daftar dokter.
     */
    public function load(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoPendaftaran' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response('Parameter NoPendaftaran tidak valid.', 400);
        }

        $noPendaftaran = $request->input('NoPendaftaran');

        // Ambil data MCU yang sudah ada
        $mcu = DB::connection('sqlsrv')->table('MCU_NEW')->where('NOPENDAFTARAN', $noPendaftaran)->first();

        $user = session('user');

        // Ambil daftar dokter
        $doctors = DB::connection('sqlsrv')
            ->table('pemeriksa')
            ->where('active', 1)
            ->where('namapemeriksa', 'like', '%dr. %')
            ->select('nopemeriksa', 'namapemeriksa')
            ->orderBy('namapemeriksa', 'asc')
            ->get();

        // Jika data MCU belum ada, set dokter pemeriksa dari user yang login
        if (!$mcu) {
            $mcu = new \stdClass(); // Buat objek kosong untuk konsistensi di view
            if (isset($user['namapemeriksa']) && !empty($user['namapemeriksa'])) {
                $mcu->DRPEMERIKSA = $user['namapemeriksa'];
                $mcu->NOPEMERIKSA = $user['nopemeriksa'] ?? '';
            }
        }

        $pemeriksaanFisik = [
            'kepala' => 'CA -/- SI -/-',
            'leher' => 'Jvp Normal',
            'jantung' => 'BJ 1 & 2 REGULER',
            'paru' => 'SDV +/+ WH -/- RH -/-',
            'abdomen' => 'Peristaltik Normal',
            'anogenital' => 'Dalam batas normal',
            'ekstremitas_atas' => 'Akral hangat +/+ Oedema -/-',
            'ekstremitas_bawah' => 'Akral hangat +/+ Oedema -/-',
        ];

        if (isset($mcu->PEMERIKSAANFISIK) && !empty($mcu->PEMERIKSAANFISIK)) {
            $lines = explode("\n", $mcu->PEMERIKSAANFISIK);
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $key = trim(strtolower($key));
                    $value = trim($value);
                    if ($key == 'kepala') $pemeriksaanFisik['kepala'] = $value;
                    if ($key == 'leher') $pemeriksaanFisik['leher'] = $value;
                    if ($key == 'jantung') $pemeriksaanFisik['jantung'] = $value;
                    if ($key == 'paru') $pemeriksaanFisik['paru'] = $value;
                    if ($key == 'abdomen') $pemeriksaanFisik['abdomen'] = $value;
                    if ($key == 'anogenital') $pemeriksaanFisik['anogenital'] = $value;
                    if ($key == 'ekstremitas atas') $pemeriksaanFisik['ekstremitas_atas'] = $value;
                    if ($key == 'ekstremitas bawah') $pemeriksaanFisik['ekstremitas_bawah'] = $value;
                }
            }
        }

        // Pecah diagnosis menjadi array jika ada
        $diagnoses = [];
        if (isset($mcu->DIAGNOSIS) && !empty($mcu->DIAGNOSIS)) {
            $diagnoses = array_map('trim', explode(',', $mcu->DIAGNOSIS));
        }

        $data = [
            'noPendaftaran' => $noPendaftaran,
            'mcu' => $mcu,
            'doctors' => $doctors,
            'diagnoses' => $diagnoses,
            'pemeriksaanFisik' => $pemeriksaanFisik,
            'user' => $user,
        ];

        return view("rme.igd.forms.mcu.index", $data);
    }

    /**
     * Menyimpan atau memperbarui data form MCU.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'DRPEMERIKSA' => 'required|string',
            'NOPEMERIKSA' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap. Dokter pemeriksa wajib diisi.'], 400);
        }

        $noPendaftaran = $request->input('NOPENDAFTARAN');

        $pemeriksaanFisik = "Kepala: " . $request->input('pemeriksaan_fisik_kepala', 'CA -/- SI -/-') . "\n" .
                            "Leher: " . $request->input('pemeriksaan_fisik_leher', 'Jvp Normal') . "\n" .
                            "Jantung: " . $request->input('pemeriksaan_fisik_jantung', 'BJ 1 & 2 REGULER') . "\n" .
                            "Paru: " . $request->input('pemeriksaan_fisik_paru', 'SDV +/+ WH -/- RH -/-') . "\n" .
                            "Abdomen: " . $request->input('pemeriksaan_fisik_abdomen', 'Peristaltik Normal') . "\n" .
                            "Anogenital: " . $request->input('pemeriksaan_fisik_anogenital', 'Dalam batas normal') . "\n" .
                            "Ekstremitas Atas: " . $request->input('pemeriksaan_fisik_ekstremitas_atas', 'Akral hangat +/+ Oedema -/-') . "\n" .
                            "Ekstremitas Bawah: " . $request->input('pemeriksaan_fisik_ekstremitas_bawah', 'Akral hangat +/+ Oedema -/-');

        // Proses diagnosis
        $diagnoses = $request->input('DIAGNOSIS', []);
        // Filter nilai kosong dan gabungkan dengan koma
        $diagnosisString = implode(', ', array_filter($diagnoses, function($value) {
            return !is_null($value) && $value !== '';
        }));

        // Kolom dari tabel MCU_NEW
        $columns = [
            'TD', 'NADI', 'SUHU', 'SP02', 'RR', 'TB', 'BB', 'IMT',
            'KELUHAN', 'PENUNJANG', 'RENCANATERAPI', 'KESIMPULAN', 
            'DRPEMERIKSA', 'NOPEMERIKSA'
        ];
        $data = [];
        foreach ($columns as $column) {
            // Gunakan null coalescing operator untuk default value
            $data[$column] = $request->input($column) ?? '';
        }

        // Jika PENUNJANG kosong, set default 'Terlampir'
        $data['PENUNJANG'] = $request->input('PENUNJANG') ?: 'Terlampir';

        // Set PEMERIKSAANFISIK secara manual
        $data['PEMERIKSAANFISIK'] = $pemeriksaanFisik;
        $data['DIAGNOSIS'] = $diagnosisString;

        // Tambahkan data user dan timestamp
        $sessionUser = session('user');
        $data['USER_ENTRY'] = $sessionUser['username'] ?? '';
        $data['TGLJAM_ENTRY'] = now();

        try {
            // Gunakan updateOrInsert untuk menyederhanakan logika
            DB::connection('sqlsrv')->table('MCU_NEW')->updateOrInsert(
                ['NOPENDAFTARAN' => $noPendaftaran], // Kondisi untuk mencari record
                $data  // Data untuk di-insert atau di-update
            );

            return response()->json(['status' => 'success', 'message' => 'Data MCU berhasil disimpan.']);

        } catch (\Exception $e) {
            // Kirim pesan error yang lebih deskriptif untuk debugging
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()], 500);
        }
    }
}