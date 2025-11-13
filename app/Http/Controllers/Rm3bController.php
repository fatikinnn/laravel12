<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class Rm3bController extends Controller
{
    /**
     * Memuat form RM3B dengan data yang ada.
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

        // --- CARA YANG BENAR ---
        // Ambil semua data pasien yang sudah dikirim oleh AJAX dari $request.
        // Tidak perlu query ulang ke database.
        // Kita bungkus dalam array agar konsisten dengan cara Anda mem-passing data.
        $patientDetails = $request->all();

        // Ambil data RM3A dan RM3B yang sudah ada
        // Sesuai native code, LEFT JOIN RM3A ke RM3B
        $rmData = DB::connection('sqlsrv')
            ->table('RM3A')
            ->leftJoin('RM3B', 'RM3A.NOPENDAFTARAN', '=', 'RM3B.NOPENDAFTARAN')
            ->where('RM3A.NOPENDAFTARAN', $noPendaftaran)
            ->select(
                'RM3A.*', 'RM3B.*'
            )
            ->first();

        // Ambil daftar dokter untuk dropdown DPJP
        $dokterList = DB::connection('sqlsrv')
            ->table('pemeriksa')
            ->where('ACTIVE', '1')
            ->where('NAMAPEMERIKSA', 'like', '%dr.%')
            ->orderBy('NAMAPEMERIKSA', 'asc')
            ->get();

        // Menggunakan Auth facade untuk konsistensi, bisa null jika sesi habis
        $user = session('user'); // Ambil data user dari session yang Anda buat

        $data = [
            'patient' => $patientDetails, // Gunakan data dari request, bukan dari query baru
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'rm3b' => $rmData,
            'user' => $user,
            'dokterList' => $dokterList,
        ];

        return view("rme.igd.forms.rm3b.index", $data);
    }

    /**
     * Menyimpan atau memperbarui data form RM3B dan RMIGD.
     */
    public function storeOrUpdate(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');

        // Daftar semua kolom dari form RM3B
        // Disesuaikan dengan native code
        $rm3bColumns = [
            'NOPENDAFTARAN', 'USER_ENTRY', 'TGLJAM_ENTRY', 'TGL_DATANG', 'JAM_DATANG',
            'GCS_E', 'GCS_V', 'GCS_M', 'KU_BAIK', 'KU_SEDANG', 'KU_BURUK', 'KESADARAN', 'TEDAR',
            'SUHU', 'BB', 'NADI', 'PERNAPASAN', 'ANAMNESA_AUTO', 'ANAMNESA_ALLO', 'keluhan_utama',
            'RIW_PENYAKIT', 'RPD_TIDAK', 'RPD_ADA', 'RPD_ADA_KET',
            'RPK_TIDAK', 'RPK_HIPERTENSI', 'RPK_DM', 'RPK_TBC', 'RPK_ADA', 'RPK_ADA_KET', 'SO2',
            'RA_TIDAK', 'RA_ADA', 'RA_ADA_KET', 'NYERI_TIDAK', 'NYERI_YA3B', 'NYERI_YA_KET',
            'VAS', 'WONG', 'FLACC', 'NIPS', 'RTL_RAJAL', 'RTL_RANAP', 'RTL_DPJP',
            'RTL_MGL_IDG', 'DIRUJUK_RS', 'DIRUJUK_RS_KET', 'DOKTER_IGD',
            'KEPALA', 'LEHER', 'JANTUNG', 'PARU', 'ABDOMEN', 'ANOGENITAL', 'EKSTREMITAS',
            'DIAGNOSIS_UTAMA', 'STAT_LOKAL', 'TERAPI_SMNTR'
        ];

        $checkboxColumns = [
            'KU_BAIK', 'KU_SEDANG', 'KU_BURUK', 'ANAMNESA_AUTO', 'ANAMNESA_ALLO',
            'RPD_TIDAK', 'RPD_ADA',
            'RPK_TIDAK', 'RPK_HIPERTENSI', 'RPK_DM', 'RPK_TBC', 'RPK_ADA',
            'RA_TIDAK', 'RA_ADA', 'NYERI_TIDAK', 'NYERI_YA3B',
            'VAS', 'WONG', 'FLACC', 'NIPS',
            'RTL_RAJAL', 'RTL_RANAP', 'RTL_MGL_IDG', 'DIRUJUK_RS'
        ];

        $data = [];
        foreach ($rm3bColumns as $column) {
            if (in_array($column, $checkboxColumns)) {
                $data[$column] = $request->has($column) ? 1 : 0;
            } else {
                $data[$column] = $request->input($column);
            }
        }

        // Handle Diagnosis Sekunder
        $diagnoSekund = $request->input('DIAGNO_SEKUND', []);
        for ($i = 1; $i <= 4; $i++) {
            $data['DIAGNO_SEKUND_' . $i] = $diagnoSekund[$i - 1] ?? '';
        }

        $ekstremitasAtas = $request->input('EKSREATAS', '');
        $ekstremitasBawah = $request->input('EKSTREBAWAH', '');
        $data['EKSTREMITAS'] = trim('Atas: ' . $ekstremitasAtas . '; Bawah: ' . $ekstremitasBawah);

        // Penanganan nilai khusus
        $loggedInUser = \Illuminate\Support\Facades\Auth::user();
        $sessionUser = session('user');

        // Prioritaskan user dari Auth, fallback ke session, lalu ke default
        $data['USER_ENTRY'] = $loggedInUser->username ?? $sessionUser['username'] ?? 'default_user';

        $data['TGLJAM_ENTRY'] = now();
        $data['TGL_DATANG'] = $request->filled('TGL_DATANG') ? Carbon::parse($request->input('TGL_DATANG'))->format('Y-m-d') : now()->format('Y-m-d');
        $data['JAM_DATANG'] = $request->filled('JAM_DATANG') ? Carbon::parse($request->input('JAM_DATANG'))->format('H:i:s') : now()->format('H:i:s');

        // Data untuk RMIGD
        $rmigdData = $this->prepareRmigdData($request, $data);

        try {
            $action = 'simpan'; // Default action

            DB::connection('sqlsrv')->transaction(function () use ($noPendaftaran, $data, $rmigdData, &$action) {
                // Cek RM3B
                $existingRm3b = DB::connection('sqlsrv')->table('RM3B')->where('NOPENDAFTARAN', $noPendaftaran)->first();
                if ($existingRm3b) {
                    $action = 'update';
                    DB::connection('sqlsrv')->table('RM3B')->where('NOPENDAFTARAN', $noPendaftaran)->update($data);
                } else {
                    $action = 'simpan';
                    DB::connection('sqlsrv')->table('RM3B')->insert($data);
                }

                // Cek RMIGD
                $existingRmigd = DB::connection('sqlsrv')->table('RMIGD')->where('NOPENDAFTARAN', $noPendaftaran)->first();
                if ($existingRmigd) {
                    DB::connection('sqlsrv')->table('RMIGD')->where('NOPENDAFTARAN', $noPendaftaran)->update($rmigdData);
                } else {
                    $rmigdData['NOPENDAFTARAN'] = $noPendaftaran;
                    $rmigdData['TANGGALRM'] = now();
                    DB::connection('sqlsrv')->table('RMIGD')->insert($rmigdData);
                }
            });

            $message = $action === 'update' ? 'Data berhasil diperbarui.' : 'Data berhasil disimpan.';
            return response()->json(['status' => 'success', 'message' => $message, 'action' => $action]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mempersiapkan data untuk tabel RMIGD dari data RM3B.
     */
    private function prepareRmigdData(Request $request, array $data): array
    {
        $rmigd = [];

        // Keadaan Datang
        if ($request->has('KU_BAIK')) $rmigd['KEADAANDATANG'] = 1;
        elseif ($request->has('KU_SEDANG')) $rmigd['KEADAANDATANG'] = 2;
        elseif ($request->has('KU_BURUK')) $rmigd['KEADAANDATANG'] = 3;
        else $rmigd['KEADAANDATANG'] = 0;

        // Kesadaran
        $kesadaranMap = [
            'COMA' => 6, 'SOPOROCOMATUS' => 4, 'SOMNOLEN' => 5,
            'DEILIRIUM' => 2, 'APATIS' => 3, 'COMPOSMENTIS' => 1,
        ];
        $rmigd['KESADARAN'] = $kesadaranMap[strtoupper(trim($request->input('KESADARAN')))] ?? 1;

        // GCS
        $gcs = [];
        if (!empty($data['GCS_E'])) $gcs[] = 'E:' . $data['GCS_E'];
        if (!empty($data['GCS_V'])) $gcs[] = 'V:' . $data['GCS_V'];
        if (!empty($data['GCS_M'])) $gcs[] = 'M:' . $data['GCS_M'];
        $rmigd['GCS'] = !empty($gcs) ? implode(' ', $gcs) : null;

        // Anamnesa
        $rmigd['ANAM_A'] = $request->has('ANAMNESA_AUTO') ? 1 : 0;
        $rmigd['ANAM_L'] = $request->has('ANAMNESA_ALLO') ? 1 : 0;

        // Riwayat Penyakit Dahulu
        $rmigd['PD_TD'] = $request->has('RPD_TIDAK') ? 1 : 0;
        $rmigd['PD_AD'] = $request->has('RPD_ADA') ? 1 : 0;
        $rmigd['PENY_DAHULU'] = $request->input('RPD_ADA_KET');

        // Riwayat Penyakit Keluarga
        $rpk = [];
        if ($request->has('RPK_HIPERTENSI')) $rpk[] = 'Hipertensi';
        if ($request->has('RPK_DM')) $rpk[] = 'Diabetes Melitus';
        if ($request->has('RPK_TBC')) $rpk[] = 'TBC';
        if ($request->has('RPK_ADA') && $request->filled('RPK_ADA_KET')) $rpk[] = $request->input('RPK_ADA_KET');
        $rmigd['PK_TD'] = $request->has('RPK_TIDAK') ? 1 : 0;
        $rmigd['PK_AD'] = !empty($rpk) ? 1 : 0;
        $rmigd['PENY_KELUARGA'] = implode(', ', $rpk);

        // Alergi
        $rmigd['PA_TD'] = $request->has('RA_TIDAK') ? 1 : 0;
        $rmigd['PA_AD'] = $request->has('RA_ADA') ? 1 : 0;
        $rmigd['ALERGIOBAT'] = $request->input('RA_ADA_KET');

        // Metode Nyeri
        $rmigd['MET_VAS'] = $request->has('VAS') ? 1 : 0;
        $rmigd['MET_WONG'] = $request->has('WONG') ? 1 : 0;
        $rmigd['MET_FLAC'] = $request->has('FLACC') ? 1 : 0;
        $rmigd['MET_NIPS'] = $request->has('NIPS') ? 1 : 0;

        // Pemeriksaan Fisik
        $rmigd['MATA'] = $request->input('KEPALA');
        $rmigd['THT'] = $request->input('KEPALA'); // Asumsi sama dengan kepala
        $rmigd['JANTUNG'] = $request->input('JANTUNG');
        $rmigd['PARU'] = $data['PARU'];
        $rmigd['THORAK'] = $data['PARU']; // Thorax disamakan dengan Paru
        $rmigd['ABDOMEN'] = $request->input('ABDOMEN');
        $rmigd['EKSTRINITAS'] = $data['EKSTREMITAS'];

        // RTL & Dokter
        if ($request->has('RTL_RAJAL')) $rmigd['NOINSTRUKSI'] = 2;
        elseif ($request->has('RTL_RANAP')) $rmigd['NOINSTRUKSI'] = 4;
        elseif ($request->has('RTL_MGL_IDG')) $rmigd['NOINSTRUKSI'] = 5;
        else $rmigd['NOINSTRUKSI'] = null;

        $rmigd['NORUJUK_RS'] = $request->has('DIRUJUK_RS') ? 1 : null;
        $rmigd['NAMARUJUK_RS'] = $request->input('DIRUJUK_RS_KET');
        $rmigd['DITERUSKANDR'] = $request->input('RTL_DPJP');
        $rmigd['DRPEMERIKSA'] = $request->input('DOKTER_IGD');

        // Ambil NOPEMERIKSA dan KDJABATAN
        $pemeriksa = DB::connection('sqlsrv')
            ->table('PEMERIKSA')
            ->where('NAMAPEMERIKSA', $request->input('DOKTER_IGD'))
            ->first();
        $rmigd['NOPEMERIKSA'] = $pemeriksa->NOPEMERIKSA ?? null;
        $rmigd['KDJABATAN'] = $pemeriksa->KDJABATAN ?? null;

        // Map langsung
        $rmigd['KELUHANUTAMA'] = $data['keluhan_utama'] ?? null;
        $rmigd['KELUHANTAMB'] = $request->input('RIW_PENYAKIT');
        $rmigd['RIWAYAT_PENY'] = $request->input('RIW_PENYAKIT');
        $rmigd['BB'] = is_numeric($data['BB']) ? $data['BB'] : null;
        $rmigd['T'] = $data['TEDAR'] ?? null;
        $rmigd['N'] = is_numeric($data['NADI']) ? $data['NADI'] : null;
        $rmigd['R'] = is_numeric($data['PERNAPASAN']) ? $data['PERNAPASAN'] : null;
        $rmigd['S'] = is_numeric($data['SUHU']) ? $data['SUHU'] : null;
        $rmigd['TINDAKAN'] = $request->input('TERAPI_SMNTR');
        $rmigd['PENGOBATAN'] = $request->input('TERAPI_SMNTR'); // Sesuai native, PENGOBATAN diisi dari TERAPI_SMNTR

        return $rmigd;
    }
}

?>