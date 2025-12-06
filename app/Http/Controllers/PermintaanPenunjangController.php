<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermintaanPenunjangController extends Controller
{
    /**
     * Load the lab request form.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $labGroups = [];
        $radGroups = [];
        $doctors = [];
        $diagnostikDefault = '';
        
        try {
            // Ambil data grup lab dari SP
            $labGroups = DB::connection('sqlsrv')->select('EXEC LabGroupSP');
        } catch (\Exception $e) {
            Log::error("Gagal mengambil LabGroupSP: " . $e->getMessage());
            // Biarkan $labGroups kosong, form akan tetap tampil
        }

        try {
            // Ambil data grup radiologi dari SP
            $radGroups = DB::connection('sqlsrv')->select('EXEC RadGroupSP');
        } catch (\Exception $e) {
            Log::error("Gagal mengambil RadGroupSP: " . $e->getMessage());
        }

        try {
            // Fetch list of doctors
            $doctors = DB::connection('sqlsrv')->select("SELECT NAMAPEMERIKSA, NOPEMERIKSA FROM PEMERIKSA WHERE ACTIVE = 1 AND NAMAPEMERIKSA LIKE '%dr.%' ORDER BY NAMAPEMERIKSA");
        } catch (\Exception $e) {
            Log::error("Gagal mengambil daftar dokter: " . $e->getMessage());
            // Biarkan $doctors kosong, form akan tetap tampil
        }

        try {
            // Ambil diagnosis utama dari RM3B sebagai nilai default
            $rm3bData = DB::connection('sqlsrv')->table('RM3B')->where('NOPENDAFTARAN', $noPendaftaran)->value('DIAGNOSIS_UTAMA');
            if ($rm3bData) {
                $diagnostikDefault = $rm3bData;
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengambil diagnosis dari RM3B: " . $e->getMessage());
        }
        $selectedLabServices = [];
        
        return view('rme.igd.forms.penunjang.permintaan-penunjang.index', [
            'doctors' => $doctors,
            'labGroups' => $labGroups,
            'radGroups' => $radGroups,
            'noPendaftaran' => $noPendaftaran,
            'diagnostikDefault' => $diagnostikDefault,
        ]);
    }

    /**
     * Get lab services based on group.
     */
    public function getLabServices(Request $request)
    {
        $groupCode = $request->input('group_code', 'L0');
        $services = DB::connection('sqlsrv')->select('EXEC CekPenunjang ?', [$groupCode]);
        return response()->json($services);
    }

    /**
     * Store or update the lab request form data.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string|max:18',
            'DRPEMOHON' => 'nullable|string|max:35',
            'EKG' => 'nullable|string|max:100',
            'USG' => 'nullable|string|max:100',
            'LABORATORIUM' => 'nullable|array', // Diubah menjadi array
            'RADIOLOGI' => 'nullable|array',
            'DIAGNOSTIK' => 'nullable|string|max:100',
            'LAB_LUAR' => 'nullable|string|max:250',
            'RONTG_LUAR' => 'nullable|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 400);
        }

        try {
            $noPendaftaran = $request->input('NOPENDAFTARAN');
            $now = now();

            // Mengambil user dengan fallback yang lebih aman untuk mencegah error sesi habis
            $loggedInUser = Auth::user();
            $sessionUser = session('user');

            // Prioritaskan user dari Auth, fallback ke session, lalu ke default
            $username = optional($loggedInUser)->username ?? ($sessionUser['username'] ?? null);
            $kdunit = optional($loggedInUser)->kdunit ?? ($sessionUser['kdunit'] ?? null);

            // Ambil data PDIAGNOSTIK yang sudah ada
            $existingData = DB::connection('sqlsrv')->table('PDIAGNOSTIK')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('KDUNIT', $kdunit)
                ->where('TANGGAL', $now->format('Y-m-d'))
                ->first();

            // Panggil SP CekPenunjang untuk mendapatkan semua layanan sebagai master lookup
            // 'L0' adalah kode untuk "All Pemeriksaan"
            $allPossibleServices = DB::connection('sqlsrv')->select('EXEC CekPenunjang ?', ['L0']);
            $serviceNameToNumberMap = [];
            foreach ($allPossibleServices as $service) {
                $serviceNameToNumberMap[$service->Layanan] = $service->Nomor;
            }

            // 1. Proses layanan lab baru yang dipilih dari form
            $newLabServicesWithKeys = $request->input('LABORATORIUM', []);
            $allLabServices = [];
            foreach ($newLabServicesWithKeys as $service) {
                // Format value: "NoPemeriksaan::NamaLayanan"
                $parts = explode('::', $service, 2);
                if (count($parts) === 2) {
                    // Gunakan NoPemeriksaan sebagai key untuk sorting dan mencegah duplikat
                    $allLabServices[$parts[0]] = $parts[1];
                }
            }

            // 2. Gabungkan dengan layanan yang sudah ada (jika ada)
            if ($existingData && !empty(trim($existingData->LABORATORIUM))) {
                $existingServiceNames = array_map('trim', explode(', ', $existingData->LABORATORIUM));
                foreach ($existingServiceNames as $name) {
                    // Jika layanan lama tidak ada di pilihan baru, tambahkan kembali
                    if (!in_array($name, $allLabServices) && isset($serviceNameToNumberMap[$name])) {
                        $nomor = $serviceNameToNumberMap[$name];
                        $allLabServices[$nomor] = $name;
                    }
                }
            }

            // Urutkan berdasarkan key (NoPemeriksaan) secara numerik
            ksort($allLabServices, SORT_NUMERIC);

            // Filter nilai kosong dan gabungkan kembali menjadi string
            // Ambil hanya valuenya (nama layanan) setelah diurutkan
            $labString = implode(', ', array_filter(array_values($allLabServices)));

            // --- LOGIKA BARU UNTUK RADIOLOGI ---
            // Panggil SP CekPenunjang untuk mendapatkan semua layanan radiologi
            $allPossibleRadServices = DB::connection('sqlsrv')->select('EXEC CekPenunjang ?', ['R0']);
            $radServiceNameToNumberMap = [];
            foreach ($allPossibleRadServices as $service) {
                $radServiceNameToNumberMap[$service->Layanan] = $service->Nomor;
            }

            // 1. Proses layanan radiologi baru yang dipilih dari form
            $newRadServicesWithKeys = $request->input('RADIOLOGI', []);
            $allRadServices = [];
            foreach ($newRadServicesWithKeys as $service) {
                $parts = explode('::', $service, 2);
                if (count($parts) === 2) {
                    $allRadServices[$parts[0]] = $parts[1];
                }
            }

            // 2. Gabungkan dengan layanan yang sudah ada (jika ada)
            if ($existingData && !empty(trim($existingData->RONTGEN))) {
                $existingServiceNames = array_map('trim', explode(', ', $existingData->RONTGEN));
                foreach ($existingServiceNames as $name) {
                    if (!in_array($name, $allRadServices) && isset($radServiceNameToNumberMap[$name])) {
                        $nomor = $radServiceNameToNumberMap[$name];
                        $allRadServices[$nomor] = $name;
                    }
                }
            }

            // Urutkan berdasarkan key (NoLayanan) secara numerik
            ksort($allRadServices, SORT_NUMERIC);

            // Ambil hanya valuenya (nama layanan) setelah diurutkan
            $radString = implode(', ', array_filter(array_values($allRadServices)));

            DB::connection('sqlsrv')->table('PDIAGNOSTIK')->updateOrInsert(
                [
                    'NOPENDAFTARAN' => $noPendaftaran,
                    'KDUNIT' => $kdunit,
                    'TANGGAL' => $now->format('Y-m-d'),
                ],
                [
                    'DRPEMOHON' => $request->input('DRPEMOHON'),
                    'RONTGEN' => $radString,
                    'EKG' => $request->input('EKG'),
                    'USG' => $request->input('USG'),
                    'LABORATORIUM' => $labString,
                    'DIAGNOSTIK' => $request->input('DIAGNOSTIK'),
                    'USERENTRY' => $username,
                    'LAB_LUAR' => $request->input('LAB_LUAR'),
                    'RONTG_LUAR' => $request->input('RONTG_LUAR'),
                    'TGLENTRY' => $now,
                    'CEKDATA' => $request->input('CEKDATA', 'N'),
                    'TGLCEK' => null,
                    'CEKDATA2' => $request->input('CEKDATA2', 'N'),
                    'TGLCEK2' => null,
                ]
            );

            // --- LOGIKA UNTUK INSERT/UPDATE P3DIAGNOSA UNTUK LAB ---
            if (!empty($allLabServices)) {
                foreach ($allLabServices as $noPemeriksaan => $pemeriksaan) {
                    // Pastikan key adalah nomor pemeriksaan yang valid
                    if (!is_numeric($noPemeriksaan)) {
                        continue;
                    }

                    $searchCriteria = [
                        'NOPENDAFTARAN' => $noPendaftaran,
                        'TANGGAL' => $now->format('Y-m-d'),
                        'KDUNIT' => $kdunit,
                        'NOPEMERIKSAAN' => $noPemeriksaan,
                    ];

                    // Cek apakah data sudah ada
                    $existingRecord = DB::connection('sqlsrv')->table('P3DIAGNOSA')->where($searchCriteria)->first();

                    // Tentukan nilai UPDATED. Jika record ada dan UPDATED='Y', pertahankan 'Y'. Jika tidak, set 'N'.
                    $updatedValue = ($existingRecord && $existingRecord->UPDATED === 'Y') ? 'Y' : 'N';

                    DB::connection('sqlsrv')->table('P3DIAGNOSA')->updateOrInsert($searchCriteria, [
                        'PEMERIKSAAN' => $pemeriksaan,
                        'KODE' => 'L', // Sesuai permintaan untuk laborat
                        'UPDATED' => $updatedValue,
                        'NOMORKWITANSI' => '',
                    ]);
                }
            }

            // --- LOGIKA BARU UNTUK INSERT/UPDATE P3DIAGNOSA UNTUK RADIOLOGI ---
            if (!empty($allRadServices)) {
                foreach ($allRadServices as $noLayanan => $pemeriksaan) {
                    if (!is_numeric($noLayanan)) {
                        continue;
                    }

                    $searchCriteria = [
                        'NOPENDAFTARAN' => $noPendaftaran,
                        'TANGGAL' => $now->format('Y-m-d'),
                        'KDUNIT' => $kdunit,
                        'NOPEMERIKSAAN' => $noLayanan,
                    ];

                    // Cek apakah data sudah ada
                    $existingRecord = DB::connection('sqlsrv')->table('P3DIAGNOSA')->where($searchCriteria)->first();

                    // Tentukan nilai UPDATED. Jika record ada dan UPDATED='Y', pertahankan 'Y'. Jika tidak, set 'N'.
                    $updatedValue = ($existingRecord && $existingRecord->UPDATED === 'Y') ? 'Y' : 'N';

                    DB::connection('sqlsrv')->table('P3DIAGNOSA')->updateOrInsert($searchCriteria, [
                        'PEMERIKSAAN' => $pemeriksaan,
                        'KODE' => 'R', // 'R' untuk Radiologi
                        'URAIAN_RAD' => $radString, // Sesuai permintaan
                        'UPDATED' => $updatedValue,
                        'NOMORKWITANSI' => '',
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data Permintaan Penunjang berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving lab request: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }
}