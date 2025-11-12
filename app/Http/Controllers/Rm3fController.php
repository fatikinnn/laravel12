<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Rm3fController extends Controller
{
    /**
     * Memuat data dan menampilkan view untuk form RM3F.
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
        $user = Auth::user(); // Bisa null jika sesi habis

        // Query untuk mengambil data gabungan dari RM3A, RM3F, dan RM3B
        $row = DB::connection('sqlsrv')
            ->table('RM3A')
            ->leftJoin('RM3F', 'RM3A.NOPENDAFTARAN', '=', 'RM3F.NOPENDAFTARAN')
            ->leftJoin('RM3B', 'RM3A.NOPENDAFTARAN', '=', 'RM3B.NOPENDAFTARAN')
            ->where('RM3A.NOPENDAFTARAN', $noPendaftaran)
            ->select('RM3A.*', 'RM3F.*', 'RM3B.*', 'RM3F.NOPENDAFTARAN as NOPENDAFTARAN_RM3F') // Alias untuk cek keberadaan data RM3F
            ->first();

        $data = [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'row' => $row ? (array) $row : [],
            'user' => $user,
        ];

        return view('rme.igd.forms.rm3f.index', $data);
    }

    /**
     * Menyimpan atau memperbarui data RM3F.
     */
    public function storeOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'NORM' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak lengkap.', 'errors' => $validator->errors()], 422);
        }

        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $loggedInUser = Auth::user(); // Bisa null jika sesi habis
        $sessionUser = session('user'); // Ambil dari session sebagai fallback

        // Prioritaskan user dari Auth, fallback ke session, lalu ke default
        $username = $loggedInUser->username ?? $sessionUser['username'] ?? 'default_user';

        $data = [
            // Menggunakan username yang sudah dipastikan ada
            'USER_ENTRY' => $username,
            'TGLJAM_ENTRY' => Carbon::now()->format('Y-m-d H:i:s'),
            'KELUHAN' => $request->input('KELUHAN'),
            'RIWAYAT_PS' => $request->input('RIWAYAT_PS'),
            'RIWAYAT_PD' => $request->input('RIWAYAT_PD'),

            // Checkbox fields
            'TEKANAN_INTRAK_NRML' => $request->has('TEKANAN_INTRAK_NRML') ? 1 : 0,
            'TEKANAN_INTRAK_SK' => $request->has('TEKANAN_INTRAK_SK') ? 1 : 0,
            'TEKANAN_INTRAK_MNTH' => $request->has('TEKANAN_INTRAK_MNTH') ? 1 : 0,
            'TEKANAN_INTRAK_PSNG' => $request->has('TEKANAN_INTRAK_PSNG') ? 1 : 0,
            'TEKANAN_INTRAK_BING' => $request->has('TEKANAN_INTRAK_BING') ? 1 : 0,
            'PUPIL_NORMAL' => $request->has('PUPIL_NORMAL') ? 1 : 0,
            'PUPIL_MIOSIS' => $request->has('PUPIL_MIOSIS') ? 1 : 0,
            'PUPIL_MIDRIASIS' => $request->has('PUPIL_MIDRIASIS') ? 1 : 0,
            'PUPIL_ISOKOR' => $request->has('PUPIL_ISOKOR') ? 1 : 0,
            'PUPIL_ANISOKOR' => $request->has('PUPIL_ANISOKOR') ? 1 : 0,
            'NEURO_NORMAL' => $request->has('NEURO_NORMAL') ? 1 : 0,
            'NEURO_SPASMA' => $request->has('NEURO_SPASMA') ? 1 : 0,
            'NEURO_MOTORIK' => $request->has('NEURO_MOTORIK') ? 1 : 0,
            'NEURO_SENSORIK' => $request->has('NEURO_SENSORIK') ? 1 : 0,
            'NEURO_KERUSAKAN' => $request->has('NEURO_KERUSAKAN') ? 1 : 0,
            'NEURO_BENTUK' => $request->has('NEURO_BENTUK') ? 1 : 0,
            'NEURO_TINGKAT_KESAD' => $request->has('NEURO_TINGKAT_KESAD') ? 1 : 0,
            'NEURO_FRAKTUR' => $request->has('NEURO_FRAKTUR') ? 1 : 0,
            'INTEGUMENT_NORMAL' => $request->has('INTEGUMENT_NORMAL') ? 1 : 0,
            'INTEGUMENT_BAKAR' => $request->has('INTEGUMENT_BAKAR') ? 1 : 0,
            'INTEGUMENT_ROBEK' => $request->has('INTEGUMENT_ROBEK') ? 1 : 0,
            'INTEGUMENT_LECET' => $request->has('INTEGUMENT_LECET') ? 1 : 0,
            'INTEGUMENT_DEKUBITUS' => $request->has('INTEGUMENT_DEKUBITUS') ? 1 : 0,
            'INTEGUMENT_GANGREN' => $request->has('INTEGUMENT_GANGREN') ? 1 : 0,
            'TURGOR_KULIT_BAIK' => $request->has('TURGOR_KULIT_BAIK') ? 1 : 0,
            'TURGOR_KULIT_LEMBAB' => $request->has('TURGOR_KULIT_LEMBAB') ? 1 : 0,
            'EDEMA_TIDAK_ADA' => $request->has('EDEMA_TIDAK_ADA') ? 1 : 0,
            'EDEMA_EXTRIMITAS' => $request->has('EDEMA_EXTRIMITAS') ? 1 : 0,
            'EDEMA_TUBUH' => $request->has('EDEMA_TUBUH') ? 1 : 0,
            'EDEMA_ASCITES' => $request->has('EDEMA_ASCITES') ? 1 : 0,
            'EDEMA_PALPEBRA' => $request->has('EDEMA_PALPEBRA') ? 1 : 0,
            'MUKOSA_KERING' => $request->has('MUKOSA_KERING') ? 1 : 0,
            'MUKOSA_LEMBAB' => $request->has('MUKOSA_LEMBAB') ? 1 : 0,
            'PERDARAHAN_TDK_ADA' => $request->has('PERDARAHAN_TDK_ADA') ? 1 : 0,
            'PERDARAHAN' => $request->has('PERDARAHAN') ? 1 : 0,
            'PERDARAHAN_JUMLAH' => $request->input('PERDARAHAN_JUMLAH'),
            'PERDARAHAN_WARNA' => $request->input('PERDARAHAN_WARNA'),
            'INTOKSIKASI_TIDAK' => $request->has('INTOKSIKASI_TIDAK') ? 1 : 0,
            'INTOKSIKASI_MAKANAN' => $request->has('INTOKSIKASI_MAKANAN') ? 1 : 0,
            'INTOKSIKASI_GIGIT' => $request->has('INTOKSIKASI_GIGIT') ? 1 : 0,
            'INTOKSIKASI_ZAT' => $request->has('INTOKSIKASI_ZAT') ? 1 : 0,
            'INTOKSIKASI_GAS' => $request->has('INTOKSIKASI_GAS') ? 1 : 0,
            'INTOKSIKASI_OBAT' => $request->has('INTOKSIKASI_OBAT') ? 1 : 0,
            'ELIMINASI_FREKUENSI' => $request->input('ELIMINASI_FREKUENSI'),
            'ELIMINASI_KONSISTENSI' => $request->input('ELIMINASI_KONSISTENSI'),
            'ELIMINASI_WARNA' => $request->input('ELIMINASI_WARNA'),
            'URINE' => $request->input('URINE'),
            'RIWAYAT_ALERGI_ADA' => $request->has('RIWAYAT_ALERGI_ADA') ? 1 : 0,
            'RIWAYAT_ALERGI_ADA_KET' => $request->input('RIWAYAT_ALERGI_ADA_KET'),
            'RIWAYAT_ALERGI_TIDAK' => $request->has('RIWAYAT_ALERGI_TIDAK') ? 1 : 0,
            'AIRWAY_SUMBAT' => $request->has('AIRWAY_SUMBAT') ? 1 : 0,
            'AIRWAY_BENDA' => $request->has('AIRWAY_BENDA') ? 1 : 0,
            'AIRWAY_DARAH' => $request->has('AIRWAY_DARAH') ? 1 : 0,
            'AIRWAY_SPUTUM' => $request->has('AIRWAY_SPUTUM') ? 1 : 0,
            'AIRWAY_LENDIR' => $request->has('AIRWAY_LENDIR') ? 1 : 0,
            'AIRWAY_BRONCHOSPASME' => $request->has('AIRWAY_BRONCHOSPASME') ? 1 : 0,
            'AIRWAY_NORMAL' => $request->has('AIRWAY_NORMAL') ? 1 : 0,
            'AIRWAY_LAIN' => $request->has('AIRWAY_LAIN') ? 1 : 0,
            'AIRWAY_KET_LAIN' => $request->input('AIRWAY_KET_LAIN'),
            'BREATH_SESAK_TIDAK' => $request->has('BREATH_SESAK_TIDAK') ? 1 : 0,
            'BREATH_SESAK_YA' => $request->has('BREATH_SESAK_YA') ? 1 : 0,
            'BREATH_SESAK_TERATUR' => $request->has('BREATH_SESAK_TERATUR') ? 1 : 0,
            'BREATH_SESAK_TDK_TERATUR' => $request->has('BREATH_SESAK_TDK_TERATUR') ? 1 : 0,
            'BREATH_SESAK_TNP_AKTIF' => $request->has('BREATH_SESAK_TNP_AKTIF') ? 1 : 0,
            'BREATH_SESAK_AKTIF' => $request->has('BREATH_SESAK_AKTIF') ? 1 : 0,
            'BREATH_SESAK_OTOT' => $request->has('BREATH_SESAK_OTOT') ? 1 : 0,
            'BREATH_FREKUENSI' => $request->input('BREATH_FREKUENSI'),
            'CIRCU_TD' => $request->input('CIRCU_TD'),
            'CIRCU_NADI' => $request->input('CIRCU_NADI'),
            'CIRCU_NADI_KUAT' => $request->has('CIRCU_NADI_KUAT') ? 1 : 0,
            'CIRCU_NADI_LEMAH' => $request->has('CIRCU_NADI_LEMAH') ? 1 : 0,
            'CIRCU_NADI_TERATUR' => $request->has('CIRCU_NADI_TERATUR') ? 1 : 0,
            'CIRCU_NADI_TDK_TERATUR' => $request->has('CIRCU_NADI_TDK_TERATUR') ? 1 : 0,
            'CIRCU_SATURASI' => $request->input('CIRCU_SATURASI'),
            'CIRCU_LBH2DTK' => $request->has('CIRCU_LBH2DTK') ? 1 : 0,
            'CIRCU_KRNG2DTK' => $request->has('CIRCU_KRNG2DTK') ? 1 : 0,
            'CIRCU_HANGAT' => $request->has('CIRCU_HANGAT') ? 1 : 0,
            'CIRCU_DINGIN' => $request->has('CIRCU_DINGIN') ? 1 : 0,
            'CIRCU_BAIK' => $request->has('CIRCU_BAIK') ? 1 : 0,
            'CIRCU_PUCAT' => $request->has('CIRCU_PUCAT') ? 1 : 0,
            'CIRCU_CYANOSIS' => $request->has('CIRCU_CYANOSIS') ? 1 : 0,
            'CIRCU_OEDEM' => $request->has('CIRCU_OEDEM') ? 1 : 0,
            'CIRCU_KURANG' => $request->has('CIRCU_KURANG') ? 1 : 0,
            'CIRCU_IKRETIK' => $request->has('CIRCU_IKRETIK') ? 1 : 0,
            'CIRCU_NORMAL' => $request->has('CIRCU_NORMAL') ? 1 : 0,
            'DISAB_KESADARAN' => $request->input('DISAB_KESADARAN'),
            'DISAB_GCS_E' => $request->input('DISAB_GCS_E'),
            'DISAB_GCS_V' => $request->input('DISAB_GCS_V'),
            'DISAB_GCS_M' => $request->input('DISAB_GCS_M'),
            'DISAB_PUPIL' => $request->input('DISAB_PUPIL'),
            'DISAB_PUPIL_ISOKOR' => $request->has('DISAB_PUPIL_ISOKOR') ? 1 : 0,
            'DISAB_PUPIL_ANISOKOR' => $request->has('DISAB_PUPIL_ANISOKOR') ? 1 : 0,
            'EXPOSURE_LUKA' => $request->input('EXPOSURE_LUKA'),
            'EXPOSURE_LUKA_ADA' => $request->has('EXPOSURE_LUKA_ADA') ? 1 : 0,
            'EXPOSURE_LUKA_TIDKA' => $request->has('EXPOSURE_LUKA_TIDKA') ? 1 : 0,
            'EXPOSURE_KEDALAMAN' => $request->input('EXPOSURE_KEDALAMAN'),
            'EXPOSURE_PERDARAHAN' => $request->input('EXPOSURE_PERDARAHAN'),
            'EXPOSURE_FRAKTUR' => $request->input('EXPOSURE_FRAKTUR'),
            'EXPOSURE_LOKASI' => $request->input('EXPOSURE_LOKASI'),
            'NYERI_YA' => $request->has('NYERI_YA') ? 1 : 0,
            'NYERI_TIDAK' => $request->has('NYERI_TIDAK') ? 1 : 0,
            'SKALA_NYERI' => $request->input('SKALA_NYERI'),
            'METODE_VAS' => $request->has('METODE_VAS') ? 1 : 0,
            'METODE_WONGBAKER' => $request->has('METODE_WONGBAKER') ? 1 : 0,
            'METODE_FLACC' => $request->has('METODE_FLACC') ? 1 : 0,
            'METODE_NIPS' => $request->has('METODE_NIPS') ? 1 : 0,
            'METODE_SKOR' => $request->has('METODE_SKOR') ? 1 : 0,
            'METODE_KATEGORI' => $request->has('METODE_KATEGORI') ? 1 : 0,
            'NYERI_PINDAH_TIDAK' => $request->has('NYERI_PINDAH_TIDAK') ? 1 : 0,
            'NYERI_PINDAH_YA' => $request->has('NYERI_PINDAH_YA') ? 1 : 0,
            'LAMA_NYERI_AKUT' => $request->has('LAMA_NYERI_AKUT') ? 1 : 0,
            'LAMA_NYERI_KRONIK' => $request->has('LAMA_NYERI_KRONIK') ? 1 : 0,
            'RASA_TAJAM' => $request->has('RASA_TAJAM') ? 1 : 0,
            'RASA_DISUTUK' => $request->has('RASA_DISUTUK') ? 1 : 0,
            'RASA_BERDENYUT' => $request->has('RASA_BERDENYUT') ? 1 : 0,
            'RASA_TUMPUL' => $request->has('RASA_TUMPUL') ? 1 : 0,
            'RASA_DIPUKUL' => $request->has('RASA_DIPUKUL') ? 1 : 0,
            'RASA_DITIKAM' => $request->has('RASA_DITIKAM') ? 1 : 0,
            'RASA_DITARIK' => $request->has('RASA_DITARIK') ? 1 : 0,
            'RASA_DIBAKAR' => $request->has('RASA_DIBAKAR') ? 1 : 0,
            'RASA_KRAM' => $request->has('RASA_KRAM') ? 1 : 0,
            'NYERI_1JAM' => $request->has('NYERI_1JAM') ? 1 : 0,
            'NYERI_3JAM' => $request->has('NYERI_3JAM') ? 1 : 0,
            'NYERI_KRNG30MNT' => $request->has('NYERI_KRNG30MNT') ? 1 : 0,
            'NYERI_LBH30MNT' => $request->has('NYERI_LBH30MNT') ? 1 : 0,
            'NYERI_KOMPRES' => $request->has('NYERI_KOMPRES') ? 1 : 0,
            'NYERI_AKTIFITAS' => $request->has('NYERI_AKTIFITAS') ? 1 : 0,
            'NYERI_LAIN' => $request->has('NYERI_LAIN') ? 1 : 0,
            'NYERI_LAIN_KET' => $request->input('NYERI_LAIN_KET'),
            'DIAGNOSA_KPRWTN' => $request->input('DIAGNOSA_KPRWTN'),
            'RENCANA_KPRWTN' => $request->input('RENCANA_KPRWTN'),
            'TINDAKAN' => $request->input('TINDAKAN'),
            'EVALUASI' => $request->input('EVALUASI'),
            'OKSIGENISASI' => $request->has('OKSIGENISASI') ? 1 : 0,
            'NEBULIZER' => $request->has('NEBULIZER') ? 1 : 0,
            'IVFD' => $request->has('IVFD') ? 1 : 0,
            'EKG' => $request->has('EKG') ? 1 : 0,
            'TRANSFUSI' => $request->has('TRANSFUSI') ? 1 : 0,
            'NGT' => $request->has('NGT') ? 1 : 0,
            'DC_SHOCK' => $request->has('DC_SHOCK') ? 1 : 0,
            'EKSPLORASI' => $request->has('EKSPLORASI') ? 1 : 0,
            'BILAS_LAMBUNG' => $request->has('BILAS_LAMBUNG') ? 1 : 0,
            'MENYIAPKAN_LAB' => $request->has('MENYIAPKAN_LAB') ? 1 : 0,
            'IRIGASI_MATA' => $request->has('IRIGASI_MATA') ? 1 : 0,
            'KATETER' => $request->has('KATETER') ? 1 : 0,
            'LAINNYA' => $request->has('LAINNYA') ? 1 : 0,
            'LAINNYA_KET' => $request->input('LAINNYA_KET'),
            'OBAT_ORAL' => $request->has('OBAT_ORAL') ? 1 : 0,
            'OBAT_ORAL_KET' => $request->input('OBAT_ORAL_KET'),
            'OBAT_PARENTERAL' => $request->has('OBAT_PARENTERAL') ? 1 : 0,
            'OBAT_PARENTERAL_KET' => $request->input('OBAT_PARENTERAL_KET'),
            'OBAT_LAIN' => $request->has('OBAT_LAIN') ? 1 : 0,
            'OBAT_LAIN_KET' => $request->input('OBAT_LAIN_KET'),
        ];

        try {
            $existing = DB::connection('sqlsrv')->table('RM3F')->where('NOPENDAFTARAN', $noPendaftaran)->first();

            if ($existing) {
                DB::connection('sqlsrv')->table('RM3F')->where('NOPENDAFTARAN', $noPendaftaran)->update($data);
                $message = 'Data RM3F berhasil diperbarui.';
            } else {
                $data['NOPENDAFTARAN'] = $noPendaftaran;
                $data['NORM'] = $request->input('NORM');
                DB::connection('sqlsrv')->table('RM3F')->insert($data);
                $message = 'Data RM3F berhasil disimpan.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}