<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class Rm7Controller extends Controller
{
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

        // Ambil data ruangan untuk dropdown
        // Sesuai permintaan, query diubah untuk mendapatkan detail lebih lengkap
        $ruangan = DB::connection('sqlsrv')
            ->table('KELASINAP as K')
            ->join('RUANGINAP as R', 'R.NoKelas', '=', 'K.NoKelas')
            ->where('R.AKTIFRUANG', 'Y')
            ->where('K.AKTIFKELAS', 1)
            ->select('K.NAMAKELAS', 'K.KODEKELAS', 'R.NORUANG', 'R.NAMARUANG')
            ->orderBy('K.NAMAKELAS')
            ->orderBy('R.NAMARUANG')
            ->get();

        return view('rme.igd.forms.rm7.index', compact('noPendaftaran', 'norm', 'user', 'patientDetails', 'ruangan', 'dokterList'));
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
     * Store a new transfer or update an existing one.
     */
    public function store(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $noTransfer = $request->input('NOTRANSFER');
        $user = Session::get('user.username', 'SYSTEM');

        DB::connection('sqlsrv')->beginTransaction();
        try {
            $isUpdate = !empty($noTransfer);
            $data = $request->except(['_token', 'obat_rumah', 'obat_unit', 'NORM', 'NYERI_GROUP', 'NOPENDAFTARAN']);

            // Optimasi: Logika NYERI_GROUP dipindahkan ke sini agar tidak duplikat
            $nyeriGroup = $request->input('NYERI_GROUP');
            if ($nyeriGroup === '1') {
                $data['NYERI_YA'] = 1;
                $data['NYERI_TIDAK'] = 0;
            } else {
                $data['NYERI_YA'] = 0;
                $data['NYERI_TIDAK'] = 1;
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

                    // --- LOGIKA UPDATE RANAP (SESUAI NATIVE CODE) ---
                    $namaRuangTujuan = $request->input('PINDAH_KE_RUANG_TEXT');
                    if ($namaRuangTujuan) {
                        $ruangTujuanInfo = DB::connection('sqlsrv')
                            ->table('RUANGINAP as R')
                            ->join('KELASINAP as K', 'R.NoKelas', '=', 'K.NoKelas')
                            ->where('R.NAMARUANG', $namaRuangTujuan)
                            ->first(['R.NORUANG', 'K.KODEKELAS']);

                        if ($ruangTujuanInfo) {
                            $newNobangsal = $ruangTujuanInfo->NORUANG;
                            $newKodekelas = $ruangTujuanInfo->KODEKELAS;

                            $ranapLama = DB::connection('sqlsrv')->table('RANAP')
                                ->where('NOPENDAFTARAN', $noPendaftaran)
                                ->orderBy('MUTASI', 'desc')
                                ->first();

                            if ($ranapLama) {
                                $kelasLama = trim($ranapLama->KELAS);
                                $mutasiLama = $ranapLama->MUTASI;

                                if ($kelasLama == $newKodekelas) {
                                    // SKENARIO 1: KELAS SAMA, HANYA PINDAH RUANGAN
                                    DB::connection('sqlsrv')->table('RANAP')
                                        ->where('NOPENDAFTARAN', $noPendaftaran)
                                        ->where('MUTASI', $mutasiLama)
                                        ->update(['NOBANGSAL' => $newNobangsal, 'KELAS' => $newKodekelas]);
                                } else {
                                    // SKENARIO 2: KELAS BERBEDA, BUAT MUTASI BARU
                                    $now = Carbon::now();

                                    // a. Tutup mutasi lama
                                    DB::connection('sqlsrv')->table('RANAP')
                                        ->where('NOPENDAFTARAN', $noPendaftaran)
                                        ->where('MUTASI', $mutasiLama)
                                        ->update(['TANGGALKELUAR' => $now->format('Y-m-d'), 'JAMKELUAR' => $now, 'CEKOUT' => 'Y']);

                                    // b. Insert mutasi baru
                                    DB::connection('sqlsrv')->table('RANAP')->insert([
                                        'NOPENDAFTARAN' => $noPendaftaran,
                                        'TANGGALMASUK' => $now->format('Y-m-d'),
                                        'JAMMASUK' => $now,
                                        'MUTASI' => $mutasiLama + 1,
                                        'CEKOUT' => 'N',
                                        'NOBANGSAL' => $newNobangsal,
                                        'KELAS' => $newKodekelas,
                                        'NOPEMERIKSA' => $ranapLama->NOPEMERIKSA,
                                        'NOSPRI' => $ranapLama->NOSPRI,
                                        'KDTARIF' => $ranapLama->KDTARIF,
                                        'NAMAUSER' => substr($user, 0, 25),
                                        'KDJABATAN' => $ranapLama->KDJABATAN,
                                    ]);
                                }
                            }
                        }
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

            // Proses Obat: Hapus yang lama, insert yang baru
            DB::connection('sqlsrv')->table('RM7_RUMAH')->where('NOPENDAFTARAN', $noPendaftaran)->where('NOTRANSFER', $noTransfer)->delete();
            if ($request->has('obat_rumah')) {
                $counter = 1;
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
}
