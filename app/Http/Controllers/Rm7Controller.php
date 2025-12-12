<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class Rm7Controller extends Controller
{
    /**
     * Daftar NORUANG untuk unit spesial (IGD, VK, IBS, PONEK) yang memiliki perlakuan khusus:
     * - Tidak memicu mutasi RANAP baru.
     * - Tidak mengubah status 'PAKAI' di RUANGINAP.
     */
    private const SPECIAL_NORUANG = ['313', '314', '315', '188', '189', '190', '191', '192', '193', '194', '195', '316', '317', '547', '548', '549', '550', '551', '552', '553', '554', '555', '556', '557', '558', '559', '560', '561'];

    /**
     * Load the main view for RM7, including transfer history.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = Session::get('user');
        $patientDetails = $request->all();

        // Ambil diagnosis utama dari RM3B
        $rm3b = DB::connection('sqlsrv')
            ->table('RM3B')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first(['DIAGNOSIS_UTAMA']);
        $patientDetails['DIAGNOSIS_UTAMA'] = $rm3b->DIAGNOSIS_UTAMA ?? '';

        // Ambil daftar dokter untuk dropdown DPJP
        $dokterList = DB::connection('sqlsrv')
            ->table('pemeriksa')
            ->where('active', '1')
            ->where('NAMAPEMERIKSA', 'like', '%dr. %')
            ->orderBy('NAMAPEMERIKSA', 'asc')
            ->get(['NAMAPEMERIKSA']);

        // Query dasar untuk mengambil SEMUA ruangan yang aktif, sesuai dengan query yang Anda berikan.
        $baseRuanganQuery = DB::connection('sqlsrv')
            ->table('KELASINAP as K')
            ->join('RUANGINAP as R', 'R.NoKelas', '=', 'K.NoKelas')
            ->where('R.AKTIFRUANG', 'Y')
            ->where('K.AKTIFKELAS', 1) // Sesuai query dari user
            ->select('K.NAMAKELAS', 'K.KODEKELAS', 'R.NORUANG', 'R.NAMARUANG')
            ->orderBy('K.NAMAKELAS')
            ->orderBy('R.NAMARUANG');

        // Ambil ruangan tujuan otomatis dari booking terakhir di RANAP
        $ruangTujuanOtomatis = '';
        $ranapTerakhir = DB::connection('sqlsrv')
            ->table('RANAP')
            ->where('CEKOUT', 'N') // Hanya ambil mutasi yang masih aktif (belum checkout)
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->orderBy('MUTASI', 'desc')
            ->first(['NOBANGSAL']);

        $noruangTujuanSaatIni = null;
        if ($ranapTerakhir && !empty(trim($ranapTerakhir->NOBANGSAL))) {
            $noruangTujuanSaatIni = trim($ranapTerakhir->NOBANGSAL);
            $ruangInfo = DB::connection('sqlsrv')
                ->table('RUANGINAP')
                ->where('NORUANG', $noruangTujuanSaatIni)
                ->first(['NAMARUANG']);
            $ruangTujuanOtomatis = $ruangInfo ? trim($ruangInfo->NAMARUANG) : '';
        }

        // Untuk dropdown "Asal Pasien" dan "Pindah Ke", keduanya akan menampilkan daftar ruangan yang sama
        // sesuai permintaan, yaitu semua ruangan aktif tanpa filter status 'PAKAI'.
        // Pengecekan ruangan terisi akan dilakukan oleh AJAX saat submit, jadi filter di sini dihilangkan.
        $ruanganAsalList = $baseRuanganQuery->get();
        $ruanganTujuanList = $ruanganAsalList; // Menggunakan hasil query yang sama agar list identik.


        return view('rme.igd.forms.rm7.index', compact('noPendaftaran', 'norm', 'user', 'patientDetails', 'ruanganAsalList', 'ruanganTujuanList', 'dokterList', 'ruangTujuanOtomatis'));
    }

    /**
     * Fetch the list of transfers for a patient.
     */
    public function history(Request $request)
    {
        $noPendaftaran = $request->input('noPendaftaran');
        try {
            $transfers = DB::connection('sqlsrv')
                ->table('rm7')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->select('NOTRANSFER', 'TGLJAM_ENTRY', 'TGLJAM_TERIMA', 'USER_ENTRY', 'ASAL_PASIEN_RUANGAN_TEXT', 'PINDAH_KE_RUANG_TEXT', 'PERAWAT_MENYERAHKAN', 'PERAWAT_MENERIMA')
                ->orderBy('NOTRANSFER', 'asc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $transfers]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch detailed data for a specific transfer.
     */
    public function detail(Request $request)
    {
        $noPendaftaran = $request->input('noPendaftaran');
        $noTransfer = $request->input('noTransfer');

        try {
            $main = DB::connection('sqlsrv')->table('rm7')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->first();
            $obatRumah = DB::connection('sqlsrv')->table('RM7_RUMAH')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->orderBy('COUNTER')->get();
            $obatUnit = DB::connection('sqlsrv')->table('RM7_UNIT')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->orderBy('COUNTER')->get();

            if (!$main) {
                return response()->json(['status' => 'error', 'message' => 'Data transfer tidak ditemukan.'], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'main' => $main,
                    'obat_rumah' => $obatRumah,
                    'obat_unit' => $obatUnit,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Check if a destination room is occupied and return patient info if it is.
     */
    public function checkRoomOccupancy(Request $request)
    {
        $noRuangTujuan = $request->input('noRuang'); // ID Ruangan
        $namaRuangTujuan = $request->input('namaRuang'); // Fallback Name
        $noPendaftaran = $request->input('noPendaftaran');

        if (empty($noRuangTujuan) && empty($namaRuangTujuan)) {
            return response()->json(['occupied' => false]);
        }

        try {
            // 1. Get room details from RUANGINAP
            $query = DB::connection('sqlsrv')->table('RUANGINAP');
            
            if (!empty($noRuangTujuan) && $noRuangTujuan !== 'Rawat Jalan') {
                $query->where('NORUANG', $noRuangTujuan);
            } else {
                $query->where('NAMARUANG', trim($namaRuangTujuan));
            }

            $ruangInfo = $query->first(['NORUANG', 'PAKAI', 'NAMARUANG']);

            if (!$ruangInfo) {
                // Room not found, can't be occupied
                return response()->json(['occupied' => false]);
            }

            $noruang = trim($ruangInfo->NORUANG);
            $namaRuangTujuan = trim($ruangInfo->NAMARUANG); // Update name for message accuracy

            // 2. Check if it's a special room
            $isSpecialRoom = in_array($noruang, self::SPECIAL_NORUANG) || str_contains($namaRuangTujuan, 'Rawat Jalan');
            if ($isSpecialRoom) {
                return response()->json(['occupied' => false]);
            }

            // 3. Check RANAP for active patients regardless of RUANGINAP.PAKAI status
            // Prioritas Cek: Data RANAP (CEKOUT='N'). Jika ada, berarti ruangan terisi,
            // meskipun status PAKAI di RUANGINAP mungkin 'N' (tidak sinkron).
            $query = DB::connection('sqlsrv')
                ->table('RANAP')
                ->where('NOBANGSAL', $noruang)
                ->where('CEKOUT', 'N');

            if (!empty($noPendaftaran)) {
                $query->where('NOPENDAFTARAN', '!=', $noPendaftaran);
            }

            $activeRegistrations = $query->orderBy('MUTASI', 'desc') // Good practice to order
                ->pluck('NOPENDAFTARAN');

            if ($activeRegistrations->isEmpty()) {
                return response()->json(['occupied' => false]);
            }

            // 4. Get patient names from DIP
            $patientData = DB::connection('sqlsrv')
                ->table('DIP')
                ->whereIn('NOPENDAFTARAN', $activeRegistrations)
                ->select('NAMAPASIEN', 'NORM')
                ->get();

            if ($patientData->isEmpty()) {
                return response()->json(['occupied' => false]);
            }

            // 5. Build the warning message
            $patientList = $patientData->map(fn($p) => trim($p->NAMAPASIEN) . ' (' . trim($p->NORM) . ')')->implode(', ');
            $message = "Peringatan: Ruangan <strong>'{$namaRuangTujuan}'</strong> sudah terisi oleh pasien: <strong>{$patientList}</strong>.<br><br>Pasien tersebut belum ter-cekout dari sistem. Mohon hubungi unit terkait atau bagian Administrasi.";

            return response()->json(['occupied' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['occupied' => false, 'error' => $e->getMessage()]);
        }
    }
    /**
     * Store a new transfer or update an existing one.
     */
    public function store(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $noTransfer = $request->input('NOTRANSFER');
        $user = Session::get('user.username', 'SYSTEM');

        // --- VALIDASI BACKEND: CEK KETERSEDIAAN RUANGAN DI RANAP ---
        // Memastikan ruangan tujuan benar-benar kosong di data RANAP sebelum menyimpan.
        // Ini mencegah penyimpanan jika validasi frontend terlewati.
        $pindahKeId = $request->input('PINDAH_KE_ID');
        $pindahKeText = $request->input('PINDAH_KE_RUANG_TEXT');

        if (!empty($pindahKeId) && $pindahKeId !== 'Rawat Jalan' && !str_contains($pindahKeText, 'Rawat Jalan')) {
            $ruangTujuanCek = DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $pindahKeId)->first(['NORUANG', 'NAMARUANG']);
            
            if ($ruangTujuanCek) {
                $noruangCek = trim($ruangTujuanCek->NORUANG);
                $isSpecial = in_array($noruangCek, self::SPECIAL_NORUANG);
                if (!$isSpecial) {
                    $occupant = DB::connection('sqlsrv')->table('RANAP')
                        ->where('NOBANGSAL', $noruangCek)
                        ->where('CEKOUT', 'N')
                        ->where('NOPENDAFTARAN', '!=', $noPendaftaran)
                        ->first(['NOPENDAFTARAN']);

                    if ($occupant) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Gagal Simpan: Ruangan '{$ruangTujuanCek->NAMARUANG}' masih terisi oleh pasien lain (Status RANAP belum Checkout). Mohon hubungi unit terkait."
                        ], 422);
                    }
                }
            }
        }

        DB::connection('sqlsrv')->beginTransaction();
        try {
            $isUpdate = !empty($noTransfer);
            $data = $request->except(['_token', 'obat_rumah', 'obat_unit', 'NORM', 'NYERI_GROUP', 'NOPENDAFTARAN', 'ASAL_PASIEN_ID', 'PINDAH_KE_ID']);

            // Optimasi: Logika NYERI_GROUP dipindahkan ke sini agar tidak duplikat
            $nyeriGroup = $request->input('NYERI_GROUP');
            if ($nyeriGroup === '1') {
                $data['NYERI_YA'] = 1;
                $data['NYERI_TIDAK'] = 0;
            } else {
                $data['NYERI_YA'] = 0;
                $data['NYERI_TIDAK'] = 1;
            }

            // Menangani nilai checkbox indikasi ICU
            $indikasiIcuFields = ['GANGGUAN_NAFAS', 'GANGGUAN_OT', 'INFEKSIBERAT', 'PASCAOPERASI', 'GANGGUAN_ELEKTROLIT'];
            foreach ($indikasiIcuFields as $field) {
                // Jika checkbox dicentang, nilainya akan '1'. Jika tidak, atur ke '0'.
                if ($request->has($field)) {
                    $data[$field] = 1;
                } else {
                    $data[$field] = 0;
                }
            }

            if ($isUpdate) {
                // --- LOGIKA UPDATE ---
                $existing = DB::connection('sqlsrv')->table('rm7')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->first();
                if (!$existing) {
                    DB::connection('sqlsrv')->rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Data transfer tidak ditemukan untuk diupdate.'], 404);
                }

                $isSender = trim($existing->PERAWAT_MENYERAHKAN) === $user; // Apakah user ini pengirim?
                $isReceiver = trim($existing->PERAWAT_MENERIMA) === $user; // Apakah user ini penerima yang sudah tercatat?
                $isNewReceiver = empty(trim($existing->PERAWAT_MENERIMA)) && !$isSender; // Apakah user ini penerima baru?

                // User berhak mengedit jika dia adalah pengirim, penerima, atau penerima baru.
                if ($isSender || $isReceiver || $isNewReceiver) {
                    if ($isNewReceiver) {
                        // Jika ini adalah penerimaan pertama kali, catat data penerima.
                        $data['PERAWAT_MENERIMA'] = $user;
                        $data['USER_TERIMA'] = $user;
                        $data['TGLJAM_TERIMA'] = Carbon::now();
                    } else if ($isSender) {
                        // Jika pengirim yang mengedit, update Tgl/Jam Entry
                        $data['USER_ENTRY'] = $user;
                        $data['TGLJAM_ENTRY'] = Carbon::now();
                    } else if ($isReceiver) {
                        // Jika penerima yang mengedit, update Tgl/Jam Terima
                        $data['USER_TERIMA'] = $user;
                        $data['TGLJAM_TERIMA'] = Carbon::now();
                    }
                } else {
                    // Jika user tidak berhak, kembalikan error.
                    DB::connection('sqlsrv')->rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki hak untuk mengedit data transfer ini.'], 403);
                }

                // --- LOGIKA UPDATE RMIGD SETELAH RM7 DI-UPDATE ---
                // Jika ini adalah transfer pertama (dari IGD) dan sudah ada perawat yang menerima,
                // update TANGGALKELUAR dan JAMKELUAR di RMIGD.
                $perawatMenerimaFinal = $data['PERAWAT_MENERIMA'] ?? $existing->PERAWAT_MENERIMA;
                $tanggalDiterima = $request->input('TANGGAL_DITERIMA');
                $jamDiterima = $request->input('JAM_DITERIMA');

                if ($noTransfer == 1 && !empty(trim($perawatMenerimaFinal)) && !empty($tanggalDiterima) && !empty($jamDiterima)) {
                    $jamKeluar = $jamDiterima . ':' . Carbon::now()->format('s');
                    DB::connection('sqlsrv')->table('RMIGD')
                        ->where('NOPENDAFTARAN', $noPendaftaran)
                        ->update(['TANGGALKELUAR' => $tanggalDiterima, 'JAMKELUAR' => $jamKeluar]);
                }
                // --- END LOGIKA UPDATE RMIGD ---

                DB::connection('sqlsrv')->table('rm7')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->update($data);

                // --- LOGIKA REVERT STATUS BED JIKA RUANGAN DIUBAH SAAT EDIT ---
                // Menggunakan NORUANG untuk akurasi, bukan NAMARUANG.
                $idRuangAsal = $request->input('ASAL_PASIEN_ID');
                $idRuangTujuan = $request->input('PINDAH_KE_ID');

                $asalBaruInfo = ($idRuangAsal && $idRuangAsal !== 'Rawat Jalan') 
                    ? DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $idRuangAsal)->first(['NORUANG'])
                    : DB::connection('sqlsrv')->table('RUANGINAP')->where('NAMARUANG', trim($request->input('ASAL_PASIEN_RUANGAN_TEXT')))->first(['NORUANG']);
                
                $tujuanBaruInfo = ($idRuangTujuan && $idRuangTujuan !== 'Rawat Jalan')
                    ? DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $idRuangTujuan)->first(['NORUANG'])
                    : DB::connection('sqlsrv')->table('RUANGINAP')->where('NAMARUANG', trim($request->input('PINDAH_KE_RUANG_TEXT')))->first(['NORUANG']);
                
                $asalLamaInfo = DB::connection('sqlsrv')->table('RUANGINAP')->where('NAMARUANG', trim($existing->ASAL_PASIEN_RUANGAN_TEXT))->first(['NORUANG']);
                $tujuanLamaInfo = DB::connection('sqlsrv')->table('RUANGINAP')->where('NAMARUANG', trim($existing->PINDAH_KE_RUANG_TEXT))->first(['NORUANG']);

                $asalBaruNoruang = $asalBaruInfo->NORUANG ?? null;
                $tujuanBaruNoruang = $tujuanBaruInfo->NORUANG ?? null;
                $asalLamaNoruang = $asalLamaInfo->NORUANG ?? null;
                $tujuanLamaNoruang = $tujuanLamaInfo->NORUANG ?? null;

                // 1. Jika ruangan ASAL diubah, kembalikan status ruangan asal yang LAMA menjadi 'Y' (terpakai), kecuali jika itu unit spesial.
                if ($asalBaruNoruang !== $asalLamaNoruang && $asalLamaNoruang) {
                    $isAsalLamaSpecial = in_array(trim($asalLamaNoruang), self::SPECIAL_NORUANG) || str_contains(trim($existing->ASAL_PASIEN_RUANGAN_TEXT), 'Rawat Jalan');
                    if (!$isAsalLamaSpecial) {
                        DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $asalLamaNoruang)->update(['PAKAI' => 'Y']); // Kembalikan jadi terisi
                    }
                }

                // 2. Jika ruangan TUJUAN diubah, kembalikan status ruangan tujuan yang LAMA menjadi 'N' (kosong), kecuali jika itu unit spesial.
                if ($tujuanBaruNoruang !== $tujuanLamaNoruang && $tujuanLamaNoruang) {
                    $isTujuanLamaSpecial = in_array(trim($tujuanLamaNoruang), self::SPECIAL_NORUANG) || str_contains(trim($existing->PINDAH_KE_RUANG_TEXT), 'Rawat Jalan');
                    if (!$isTujuanLamaSpecial) {
                        DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $tujuanLamaNoruang)->update(['PAKAI' => 'N']); // Kosongkan lagi
                    }
                }
                // --- End of Revert Logic ---

                $tglJamEntry = $existing->TGLJAM_ENTRY;
            } else {
                // --- LOGIKA CREATE ---
                $maxNoTransfer = DB::connection('sqlsrv')->table('rm7')->where('NOPENDAFTARAN', $noPendaftaran)->max('NOTRANSFER');
                $noTransfer = ($maxNoTransfer ?? 0) + 1;
                $tglJamEntry = Carbon::now();
                $data['NOTRANSFER'] = $noTransfer;
                $data['USER_ENTRY'] = $user;
                $data['TGLJAM_ENTRY'] = $tglJamEntry;
                $data['PERAWAT_MENYERAHKAN'] = $user; // User yang membuat adalah yang menyerahkan
                $data['PERAWAT_MENERIMA'] = ''; // Pastikan kosong saat pertama kali dibuat
                $data['NOPENDAFTARAN'] = $noPendaftaran; // Tambahkan NOPENDAFTARAN ke data insert

                DB::connection('sqlsrv')->table('rm7')->insert($data);
            }

            // --- LOGIKA UPDATE RANAP (DIPINDAHKAN KE SINI AGAR BERJALAN UNTUK CREATE & UPDATE) ---
            $namaRuangAsal = trim($request->input('ASAL_PASIEN_RUANGAN_TEXT'));
            $namaRuangTujuan = trim($request->input('PINDAH_KE_RUANG_TEXT'));
            $idRuangAsal = $request->input('ASAL_PASIEN_ID');
            $idRuangTujuan = $request->input('PINDAH_KE_ID');

            // Gunakan ID untuk lookup yang lebih akurat
            $ruangAsalInfo = ($idRuangAsal && $idRuangAsal !== 'Rawat Jalan')
                ? DB::connection('sqlsrv')->table('RUANGINAP as R')->join('KELASINAP as K', 'R.NoKelas', '=', 'K.NoKelas')->where('R.NORUANG', $idRuangAsal)->first(['R.NORUANG', 'K.KODEKELAS'])
                : DB::connection('sqlsrv')->table('RUANGINAP as R')->join('KELASINAP as K', 'R.NoKelas', '=', 'K.NoKelas')->where('R.NAMARUANG', $namaRuangAsal)->first(['R.NORUANG', 'K.KODEKELAS']);

            $ruangTujuanInfo = ($idRuangTujuan && $idRuangTujuan !== 'Rawat Jalan')
                ? DB::connection('sqlsrv')->table('RUANGINAP as R')->join('KELASINAP as K', 'R.NoKelas', '=', 'K.NoKelas')->where('R.NORUANG', $idRuangTujuan)->first(['R.NORUANG', 'K.KODEKELAS'])
                : DB::connection('sqlsrv')->table('RUANGINAP as R')->join('KELASINAP as K', 'R.NoKelas', '=', 'K.NoKelas')->where('R.NAMARUANG', $namaRuangTujuan)->first(['R.NORUANG', 'K.KODEKELAS']);
            
            // Pengecualian: Jika asal atau tujuan adalah unit khusus, jangan buat mutasi baru.
            $isRawatJalanTransfer = str_contains($namaRuangAsal, 'Rawat Jalan') || str_contains($namaRuangTujuan, 'Rawat Jalan');
            $isSpecialTransfer = (isset($ruangAsalInfo->NORUANG) && in_array(trim($ruangAsalInfo->NORUANG), self::SPECIAL_NORUANG)) ||
                                 (isset($ruangTujuanInfo->NORUANG) && in_array(trim($ruangTujuanInfo->NORUANG), self::SPECIAL_NORUANG)) ||
                                 $isRawatJalanTransfer;

            if ($ruangTujuanInfo) {
                $newNobangsal = $ruangTujuanInfo->NORUANG;
                $newKodekelas = trim((string)$ruangTujuanInfo->KODEKELAS);

                $ranapLama = DB::connection('sqlsrv')
                    ->table('RANAP')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->orderBy('MUTASI', 'desc')
                    ->first();

                $oldKodekelas = $ruangAsalInfo ? trim((string)$ruangAsalInfo->KODEKELAS) : ($ranapLama ? trim((string)$ranapLama->KELAS) : null);

                if ($ranapLama) {
                    $mutasiLama = $ranapLama->MUTASI;

                    // Kondisi: Buat mutasi baru HANYA jika kelas berbeda DAN BUKAN transfer dari/ke unit khusus.
                    if ($oldKodekelas != $newKodekelas && !$isSpecialTransfer) {
                        // SKENARIO 1: KELAS BERBEDA & BUKAN UNIT SPESIAL -> BUAT MUTASI BARU
                        $now = Carbon::now();
                        // a. Tutup mutasi lama
                        DB::connection('sqlsrv')->table('RANAP')
                            ->where('NOPENDAFTARAN', $noPendaftaran)
                            ->where('MUTASI', $mutasiLama)
                            ->update(['TANGGALKELUAR' => $now->format('Y-m-d'), 'JAMKELUAR' => $now, 'CEKOUT' => 'Y']);
                        // b. Insert mutasi baru
                        DB::connection('sqlsrv')->table('RANAP')->insert([
                            'NOPENDAFTARAN' => $noPendaftaran, 'TANGGALMASUK' => $now->format('Y-m-d'),
                            'JAMMASUK' => $now, 'MUTASI' => $mutasiLama + 1, 'CEKOUT' => 'N',
                            'NOBANGSAL' => $newNobangsal, 'KELAS' => $newKodekelas,
                            'NOPEMERIKSA' => $ranapLama->NOPEMERIKSA, 'NOSPRI' => $ranapLama->NOSPRI,
                            'KDTARIF' => $ranapLama->KDTARIF, 'NAMAUSER' => substr($user, 0, 25),
                            'KDJABATAN' => $ranapLama->KDJABATAN,
                        ]);
                    } else {
                        // SKENARIO 2: KELAS SAMA atau transfer melibatkan unit khusus -> UPDATE BIASA
                        DB::connection('sqlsrv')->table('RANAP')
                            ->where('NOPENDAFTARAN', $noPendaftaran)
                            ->where('MUTASI', $mutasiLama)
                            ->update(['NOBANGSAL' => $newNobangsal, 'KELAS' => $newKodekelas]);
                    }
                }
            }

            // --- LOGIKA BARU: UPDATE STATUS BED SEGERA ---
            // Logika ini dijalankan saat CREATE baru atau saat UPDATE oleh pengirim.
            // Tujuannya adalah untuk "memesan" bed tujuan. Penerima tidak mengubah status bed.
            $isSenderAction = !$isUpdate || ($isUpdate && (trim($existing->PERAWAT_MENYERAHKAN) === $user));

            if ($isSenderAction) {
                // Variable $ruangAsalInfo dan $ruangTujuanInfo sudah di-set di atas menggunakan ID
                $asalBaruInfo = $ruangAsalInfo;
                $tujuanBaruInfo = $ruangTujuanInfo;

                // Kosongkan bed asal yang BARU (set ke 'N'), kecuali jika itu unit spesial.
                if ($asalBaruInfo) {
                    $isAsalBaruSpecial = in_array(trim($asalBaruInfo->NORUANG), self::SPECIAL_NORUANG) || str_contains(trim($request->input('ASAL_PASIEN_RUANGAN_TEXT')), 'Rawat Jalan');
                    if (!$isAsalBaruSpecial) {
                        DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $asalBaruInfo->NORUANG)->update(['PAKAI' => 'N']);
                    }
                }
                // Tandai bed tujuan yang BARU sebagai terpakai (set ke 'Y'), kecuali jika itu unit spesial.
                if ($tujuanBaruInfo) {
                    $isTujuanBaruSpecial = in_array(trim($tujuanBaruInfo->NORUANG), self::SPECIAL_NORUANG) || str_contains(trim($request->input('PINDAH_KE_RUANG_TEXT')), 'Rawat Jalan');
                    if (!$isTujuanBaruSpecial) {
                        DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $tujuanBaruInfo->NORUANG)->update(['PAKAI' => 'Y']);
                    }
                }
            }

            // Proses Obat: Hapus yang lama, insert yang baru
            DB::connection('sqlsrv')->table('RM7_RUMAH')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->delete();
            if ($request->has('obat_rumah')) {
                $counter = 1;
                // Validasi backend untuk membatasi jumlah obat
                if (count($request->input('obat_rumah')) > 5) {
                    DB::connection('sqlsrv')->rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Jumlah maksimal obat dari rumah yang bisa ditambahkan adalah 5.'
                    ], 422); // 422 Unprocessable Entity
                }

                foreach ($request->input('obat_rumah') as $obat) {
                    if (empty($obat['NAMA_OBAT'])) continue;
                    DB::connection('sqlsrv')->table('RM7_RUMAH')->insert([
                        'NOPENDAFTARAN' => $noPendaftaran,
                        'NOTRANSFER' => $noTransfer,
                        'USER_ENTRY' => $user,
                        'TGLJAM_ENTRY' => $tglJamEntry,
                        'COUNTER' => $counter++,
                        'TANGGAL_DIBAWA' => $obat['TANGGAL_DIBAWA'] ?? null,
                        'NAMA_OBAT' => $obat['NAMA_OBAT'] ?? '',
                        'DOSIS' => $obat['DOSIS'] ?? '',
                        'JUMLAH' => $obat['JUMLAH'] ?? '',
                        'ALASAN_MINUM' => $obat['ALASAN_MINUM'] ?? '',
                        'RANAP_KET' => $obat['RANAP_KET'] ?? '',
                        'TELAAH_OBAT' => $obat['TELAAH_OBAT'] ?? '',
                    ]);
                }
            }

            DB::connection('sqlsrv')->table('RM7_UNIT')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->delete();
            if ($request->has('obat_unit')) {
                $counter = 1;
                // Validasi backend untuk membatasi jumlah obat
                if (count($request->input('obat_unit')) > 5) {
                    DB::connection('sqlsrv')->rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Jumlah maksimal obat yang dibawa ke unit adalah 5.'
                    ], 422); // 422 Unprocessable Entity
                }

                foreach ($request->input('obat_unit') as $obat) {
                    if (empty($obat['NAMA_OBAT'])) continue;
                    DB::connection('sqlsrv')->table('RM7_UNIT')->insert([
                        'NOPENDAFTARAN' => $noPendaftaran,
                        'NOTRANSFER' => $noTransfer,
                        'USER_ENTRY' => $user,
                        'TGLJAM_ENTRY' => $tglJamEntry,
                        'COUNTER' => $counter++,
                        'NAMA_OBAT' => $obat['NAMA_OBAT'] ?? '',
                        'JUMLAH' => $obat['JUMLAH'] ?? '',
                        'DOSIS' => $obat['DOSIS'] ?? '',
                    ]);
                }
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => 'Data transfer berhasil ' . ($isUpdate ? 'diperbarui.' : 'disimpan.')]);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a specific transfer record.
     */
    public function destroy(Request $request)
    {
        $noPendaftaran = $request->input('noPendaftaran');
        $noTransfer = $request->input('noTransfer');
        $user = Session::get('user.username', 'SYSTEM');

        DB::connection('sqlsrv')->beginTransaction();
        try {
            $transfer = DB::connection('sqlsrv')
                ->table('rm7')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('NOTRANSFER', $noTransfer)
                ->first();

            if (!$transfer) {
                DB::connection('sqlsrv')->rollBack();
                return response()->json(['status' => 'error', 'message' => 'Data transfer tidak ditemukan.'], 404);
            }

            // Cek otorisasi: hanya perawat yang menyerahkan atau yang sudah tercatat menerima yang boleh menghapus.
            $isSender = trim($transfer->PERAWAT_MENYERAHKAN) === $user;
            $isReceiver = trim($transfer->PERAWAT_MENERIMA) === $user;

            if (!$isSender && !$isReceiver) {
                DB::connection('sqlsrv')->rollBack();
                return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki hak untuk menghapus data ini.'], 403);
            }

            // --- LOGIKA REVERT STATUS BED & RANAP SAAT HAPUS ---
            $asalInfo = DB::connection('sqlsrv')->table('RUANGINAP')->where('NAMARUANG', trim($transfer->ASAL_PASIEN_RUANGAN_TEXT))->first(['NORUANG', 'NoKelas']);
            $tujuanInfo = DB::connection('sqlsrv')->table('RUANGINAP')->where('NAMARUANG', trim($transfer->PINDAH_KE_RUANG_TEXT))->first(['NORUANG']);

            // 1. Kembalikan status bed asal menjadi 'Y' (terisi), kecuali unit spesial.
            if ($asalInfo) {
                $isAsalSpecial = in_array(trim($asalInfo->NORUANG), self::SPECIAL_NORUANG) || str_contains(trim($transfer->ASAL_PASIEN_RUANGAN_TEXT), 'Rawat Jalan');
                if (!$isAsalSpecial) {
                    DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $asalInfo->NORUANG)->update(['PAKAI' => 'Y']);
                }
            }

            // 2. Kembalikan status bed tujuan menjadi 'N' (kosong), kecuali unit spesial.
            if ($tujuanInfo) {
                $isTujuanSpecial = in_array(trim($tujuanInfo->NORUANG), self::SPECIAL_NORUANG) || str_contains(trim($transfer->PINDAH_KE_RUANG_TEXT), 'Rawat Jalan');
                if (!$isTujuanSpecial) {
                    DB::connection('sqlsrv')->table('RUANGINAP')->where('NORUANG', $tujuanInfo->NORUANG)->update(['PAKAI' => 'N']);
                }
            }

            // 3. Kembalikan data RANAP ke kondisi sebelum transfer.
            $ranapTujuan = DB::connection('sqlsrv')->table('RANAP')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOBANGSAL', $tujuanInfo->NORUANG ?? null)->orderBy('MUTASI', 'desc')->first();
            if ($ranapTujuan) {
                $mutasiTujuan = $ranapTujuan->MUTASI;
                $mutasiAsal = $mutasiTujuan - 1;

                // Jika ada mutasi sebelumnya (mutasi > 1), hapus mutasi tujuan dan aktifkan kembali mutasi asal.
                if ($mutasiAsal > 0) {
                    DB::connection('sqlsrv')->table('RANAP')->where('NOPENDAFTARAN', $noPendaftaran)->where('MUTASI', $mutasiTujuan)->delete();
                    DB::connection('sqlsrv')->table('RANAP')->where('NOPENDAFTARAN', $noPendaftaran)->where('MUTASI', $mutasiAsal)->update(['CEKOUT' => 'N', 'TANGGALKELUAR' => null, 'JAMKELUAR' => null]);
                } else if ($asalInfo) {
                    // Jika ini adalah mutasi pertama, cukup update NOBANGSAL kembali ke ruangan asal.
                    $kelasAsal = DB::connection('sqlsrv')->table('KELASINAP')->where('NoKelas', $asalInfo->NoKelas)->first(['KODEKELAS']);
                    DB::connection('sqlsrv')->table('RANAP')->where('NOPENDAFTARAN', $noPendaftaran)->where('MUTASI', $mutasiTujuan)->update(['NOBANGSAL' => $asalInfo->NORUANG, 'KELAS' => $kelasAsal->KODEKELAS ?? null]);
                }
            }
            // --- END OF REVERT LOGIC ---

            // Hapus data terkait di tabel lain terlebih dahulu
            DB::connection('sqlsrv')->table('RM7_RUMAH')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->delete();
            DB::connection('sqlsrv')->table('RM7_UNIT')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->delete();
            DB::connection('sqlsrv')->table('rm7')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->delete();

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => 'Data transfer berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}
