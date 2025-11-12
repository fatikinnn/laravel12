<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Rm28Controller extends Controller
{
    /**
     * Memuat data dan menampilkan view untuk form RM28 (A+B).
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

        // Mengambil data pasien dasar
        $rmeIgdController = new RmeIgdController();
        $patientDetails = $rmeIgdController->getPatientDetailsData($noPendaftaran);
        if (isset($patientDetails['error'])) {
            return response('Data pasien tidak ditemukan.', 404);
        }

        // Query untuk mengambil data RM28A (join dengan RM3A untuk data awal)
        $row_rm28a = DB::connection('sqlsrv')
            ->table('RM3A')
            ->leftJoin('RM28A', 'RM3A.NOPENDAFTARAN', '=', 'RM28A.NOPENDAFTARAN')
            ->where('RM3A.NOPENDAFTARAN', $noPendaftaran)
            ->select('RM3A.*', 'RM28A.*', 'RM28A.NOPENDAFTARAN as NOPENDAFTARAN_RM28A')
            ->first();

        // Query untuk mengambil data RM28B
        $row_rm28b = DB::connection('sqlsrv')
            ->table('RM28B')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // Konversi data gambar dari hex string (jika ada)
        if ($row_rm28b) {
            $imageFields = ['CAP_JARI_TGN', 'CAP_KAKI_KIRI', 'CAP_KAKI_KANAN'];
            foreach ($imageFields as $field) {
                // Periksa apakah data ada dan merupakan hex string (dimulai dengan '0x')
                if (!empty($row_rm28b->$field) && is_string($row_rm28b->$field) && strpos($row_rm28b->$field, '0x') === 0) {
                    // Konversi hex string (setelah '0x') menjadi data biner mentah
                    $row_rm28b->$field = hex2bin(substr($row_rm28b->$field, 2));
                }
            }
        }


        // Mengambil daftar perawat
        $perawatList = DB::connection('sqlsrv')
            ->table('ruser')
            ->whereIn('kodegroup', [10, 28, 29, 30, 31, 33, 38, 45, 47, 48])
            ->pluck('namauser');

        $data = [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'row_rm28a' => $row_rm28a ? (array) $row_rm28a : [],
            'row_rm28b' => $row_rm28b ? (array) $row_rm28b : [],
            'namaPasien' => $patientDetails['Nama Pasien'],
            'gender' => $patientDetails['Gender'],
            'dpjp' => $patientDetails['DPJP'],
            'perawatList' => $perawatList,
        ];

        return view('rme.igd.forms.rm28.index', $data);
    }

    /**
     * Menyimpan atau memperbarui data RM28 (A+B).
     */
    public function storeOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
            'NORM' => 'required|string',
            'CAP_JARI_TGN' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'CAP_KAKI_KIRI' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'CAP_KAKI_KANAN' => 'nullable|image|mimes:jpeg,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $user = Auth::user();
        $username = $user ? $user->username : 'default_user';
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // Data untuk RM28A
        $data_rm28a = [
            'USER_ENTRY' => $username, 'TGLJAM_ENTRY' => $now,
            'KELUHAN_UTAMA' => $request->input('keluhan_utama'),
            'PRE_HIPEREME' => $request->has('pre_hipereme') ? 1 : 0,
            'PRE_HIPERTEN' => $request->has('pre_hiperten') ? 1 : 0,
            'PRE_PEND' => $request->has('pre_pend') ? 1 : 0,
            'PRE_INF' => $request->has('pre_inf') ? 1 : 0,
            'PRE_KET_INF' => $request->input('pre_ket_inf'),
            'PRE_MIN_OBAT' => $request->has('pre_min_obat') ? 1 : 0,
            'PRE_KET_OBAT' => $request->input('pre_ket_obat'),
            'PRE_ROKOK' => $request->has('pre_rokok') ? 1 : 0,
            'PRE_JAMU' => $request->has('pre_jamu') ? 1 : 0,
            'PRE_ALKO' => $request->has('pre_alko') ? 1 : 0,
            'RO_G' => $request->input('ro_g'), 'RO_P' => $request->input('ro_p'), 'RO_A' => $request->input('ro_a'),
            'SG_NORMAL' => $request->has('sg_normal') ? 1 : 0, 'SG_MALNUT' => $request->has('sg_malnut') ? 1 : 0,
            'PERI_ASF' => $request->has('peri_asf') ? 1 : 0, 'PERI_NORMAL' => $request->has('peri_normal') ? 1 : 0,
            'POST_RIW' => $request->has('post_riw') ? 1 : 0, 'POST_KEJ' => $request->has('post_kej') ? 1 : 0,
            'POST_TRA' => $request->has('post_tra') ? 1 : 0, 'POST_LAIN' => $request->has('post_lain') ? 1 : 0,
            'POST_NORMAL' => $request->has('post_normal') ? 1 : 0,
            'HOS_0' => $request->has('hos_0') ? 1 : 0, 'HOS_1' => $request->has('hos_1') ? 1 : 0,
            'HOS_2' => $request->has('hos_2') ? 1 : 0, 'HOS_3' => $request->has('hos_3') ? 1 : 0, 'HOS_4' => $request->has('hos_4') ? 1 : 0,
            'RK_USIA' => $request->input('rk_usia'), 'RK_DLM_RS' => $request->has('rk_dlm_rs') ? 1 : 0, 'RK_LUAR_RS' => $request->has('rk_luar_rs') ? 1 : 0,
            'JP' => $request->input('jp'), 'JP_PENYULIT' => $request->input('jp_penyulit'), 'DO' => $request->input('do'),
            'TGL_LAHIR' => $request->input('tgl_lahir'), 'JAM_LAHIR' => $request->input('jam_lahir'),
            'BB_LAHIR' => $request->input('bb_lahir'), 'PB_LAHIR' => $request->input('pb_lahir'), 'LK_LAHIR' => $request->input('lk_lahir'),
            'KK_TGL' => $request->input('kk_tgl'), 'KK_JAM' => $request->input('kk_jam'), 'KK_WAR' => $request->input('kk_war'),
            'KP_ATS_24' => $request->has('kp_ats_24') ? 1 : 0, 'KP_BWH_24' => $request->has('kp_bwh_24') ? 1 : 0,
            'JK_NOR' => $request->has('jk_nor') ? 1 : 0, 'JK_OLIG' => $request->has('jk_olig') ? 1 : 0, 'JK_POLI' => $request->has('jk_poli') ? 1 : 0,
            'AS_1' => $request->input('as_1'), 'AS_2' => $request->input('as_2'), 'AS_3' => $request->input('as_3'),
            'PF_KES' => $request->input('pf_kes'), 'PF_JK' => $request->input('pf_jk'), 'PF_BB' => $request->input('pf_bb'),
            'PF_KK' => $request->input('pf_kk'), 'PF_PB' => $request->input('pf_pb'), 'PF_LK' => $request->input('pf_lk'),
            'TV_S' => $request->input('tv_s'), 'TV_HR' => $request->input('tv_hr'), 'TV_RR' => $request->input('tv_rr'), 'TV_O2' => $request->input('tv_o2'),
            'RIW_TIND' => $request->input('riw_tind'),
            'HEPATITISB' => $request->has('HEPATITISB') ? 1 : 0, 'POLIO' => $request->has('POLIO') ? 1 : 0, 'BCG' => $request->has('BCG') ? 1 : 0,
            'DTP' => $request->has('DTP') ? 1 : 0, 'HIB' => $request->has('HIB') ? 1 : 0, 'PCV' => $request->has('PCV') ? 1 : 0,
            'ROTAVIRUS' => $request->has('ROTAVIRUS') ? 1 : 0, 'INFLUEN' => $request->has('INFLUEN') ? 1 : 0, 'MR' => $request->has('MR') ? 1 : 0,
            'JE' => $request->has('JE') ? 1 : 0, 'VARISELA' => $request->has('VARISELA') ? 1 : 0, 'HEPATITISA' => $request->has('HEPATITISA') ? 1 : 0,
            'TIFOID' => $request->has('TIFOID') ? 1 : 0, 'HPV' => $request->has('HPV') ? 1 : 0, 'DENGUE' => $request->has('DENGUE') ? 1 : 0,
            'IMUN_LAIN' => $request->has('IMUN_LAIN') ? 1 : 0, 'IMUN_LAIN_KET' => $request->input('IMUN_LAIN_KET'),
        ];

        // Data untuk RM28B
        $data_rm28b = [
            'USER_ENTRY' => $username, 'TGLJAM_ENTRY' => $now,
            'NYERI_YA' => $request->has('NYERI_YA') ? 1 : 0, 'NYERI_TIDAK' => $request->has('NYERI_TIDAK') ? 1 : 0,
            'EKSPRESI0' => $request->has('EKSPRESI0') ? 1 : 0, 'EKSPRESI1' => $request->has('EKSPRESI1') ? 1 : 0,
            'MENANGIS0' => $request->has('MENANGIS0') ? 1 : 0, 'MENANGIS1' => $request->has('MENANGIS1') ? 1 : 0, 'MENANGIS2' => $request->has('MENANGIS2') ? 1 : 0,
            'BERNAFAS0' => $request->has('BERNAFAS0') ? 1 : 0, 'BERNAFAS1' => $request->has('BERNAFAS1') ? 1 : 0,
            'LENGAN0' => $request->has('LENGAN0') ? 1 : 0, 'LENGAN1' => $request->has('LENGAN1') ? 1 : 0,
            'KAKI0' => $request->has('KAKI0') ? 1 : 0, 'KAKI1' => $request->has('KAKI1') ? 1 : 0,
            'KESADARAN0' => $request->has('KESADARAN0') ? 1 : 0, 'KESADARAN1' => $request->has('KESADARAN1') ? 1 : 0,
            'JUMLAH_NILAI' => $request->input('JUMLAH_NILAI'),
            'NAMA_IBU' => $request->input('NAMA_IBU'), 'NAMA_AYAH' => $request->input('NAMA_AYAH'), 'NORM_IBU' => $request->input('NORM_IBU'),
            'NAMA_BAYI' => $request->input('NAMA_BAYI'), 'NORM_BAYI' => $request->input('NORM_BAYI'),
            'TGL_LAHIR_BAYI' => $request->input('TGL_LAHIR_BAYI'), 'JAM_LAHIR_BAYI' => $request->input('JAM_LAHIR_BAYI'),
            'JK_BAYI' => $request->input('JK_BAYI'), 'DPJP' => $request->input('DPJP'), 'PERAWAT_BAYI' => $request->input('PERAWAT_BAYI'),
        ];

        // Handle file uploads
        if ($request->hasFile('CAP_JARI_TGN')) {
            $data_rm28b['CAP_JARI_TGN'] = $this->resizeAndGetBinary($request->file('CAP_JARI_TGN'));
        }
        if ($request->hasFile('CAP_KAKI_KIRI')) {
            $data_rm28b['CAP_KAKI_KIRI'] = $this->resizeAndGetBinary($request->file('CAP_KAKI_KIRI'));
        }
        if ($request->hasFile('CAP_KAKI_KANAN')) {
            $data_rm28b['CAP_KAKI_KANAN'] = $this->resizeAndGetBinary($request->file('CAP_KAKI_KANAN'));
        }

        try {
            DB::connection('sqlsrv')->transaction(function () use ($noPendaftaran, $request, $data_rm28a, $data_rm28b) {
                // Proses RM28A
                $existing_rm28a = DB::connection('sqlsrv')->table('RM28A')->where('NOPENDAFTARAN', $noPendaftaran)->first();
                if ($existing_rm28a) {
                    DB::connection('sqlsrv')->table('RM28A')->where('NOPENDAFTARAN', $noPendaftaran)->update($data_rm28a);
                } else {
                    $data_rm28a['NOPENDAFTARAN'] = $noPendaftaran;
                    DB::connection('sqlsrv')->table('RM28A')->insert($data_rm28a);
                }

                // Proses RM28B
                $existing_rm28b = DB::connection('sqlsrv')->table('RM28B')->where('NOPENDAFTARAN', $noPendaftaran)->first();
                if ($existing_rm28b) {
                    DB::connection('sqlsrv')->table('RM28B')->where('NOPENDAFTARAN', $noPendaftaran)->update($data_rm28b);
                } else {
                    $data_rm28b['NOPENDAFTARAN'] = $noPendaftaran;
                    $data_rm28b['NORM'] = $request->input('NORM');
                    DB::connection('sqlsrv')->table('RM28B')->insert($data_rm28b);
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Data RM28 berhasil disimpan/diperbarui.']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Resize image and return its binary content for SQL Server.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxWidth
     * @return \Illuminate\Database\Query\Expression
     */
    private function resizeAndGetBinary($file, $maxWidth = 150)
    {
        if (!$file) {
            return null;
        }

        $image = imagecreatefromjpeg($file->getRealPath());
        if (!$image) {
            return null;
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        $ratio = $originalHeight / $originalWidth;
        $newWidth = $maxWidth;
        $newHeight = (int)($newWidth * $ratio);

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled(
            $resizedImage,
            $image,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );

        // Capture output to a variable
        ob_start();
        imagejpeg($resizedImage, null, 90);
        $imageData = ob_get_clean();

        imagedestroy($image);
        imagedestroy($resizedImage);

        // Convert to hex string for SQL Server varbinary
        $hex = '0x' . bin2hex($imageData);

        // Return as a raw expression to prevent Laravel from quoting it
        return DB::raw($hex);
    }

    /**
     * Menampilkan gambar dari database.
     */
    public function showImage($noPendaftaran, $field)
    {
        // Validasi nama field untuk keamanan
        $allowedFields = ['CAP_JARI_TGN', 'CAP_KAKI_KIRI', 'CAP_KAKI_KANAN'];
        if (!in_array($field, $allowedFields)) {
            return $this->servePlaceholder();
        }

        // Gunakan nama field langsung sebagai nama kolom
        $columnName = $field;

        $imageData = DB::connection('sqlsrv')
            ->table('RM28B')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->value($columnName);

        if (!empty($imageData) && is_string($imageData) && strpos($imageData, '0x') === 0) {
        if (empty($imageData)) {
            return $this->servePlaceholder();
        }

        // Periksa apakah data adalah hex string dari SQL Server
        if (is_string($imageData) && strpos($imageData, '0x') === 0) {
            // Konversi hex string (setelah '0x') menjadi data biner mentah
            $binaryData = hex2bin(substr($imageData, 2));
            return response($binaryData, 200)->header('Content-Type', 'image/jpeg');
        } else {
            // Jika sudah biner, gunakan langsung
            $binaryData = $imageData;
        }

        // Jika tidak ada gambar, sajikan gambar placeholder
        return $this->servePlaceholder();
        // Buat response dengan data gambar dan header yang benar
        $response = Response::make($binaryData, 200);
        $response->header('Content-Type', 'image/jpeg');
        return $response;
    }

    /**
     * Helper untuk menyajikan gambar placeholder.
     */
    private function servePlaceholder()
    {
        // Pastikan Anda memiliki file no-image.png di folder public/images/
        $path = public_path('images/no-image.png');
        if (!file_exists($path)) {
            return response('Image not found.', 404);
        }
        return response()->file($path);
    }

    
}