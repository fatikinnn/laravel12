@php
    // Helper untuk mendapatkan nilai dari data yang ada atau memberikan default
    function getValueRm28($data, $key, $default = '') {
        return data_get($data, $key, $default);
    }

    function isCheckedRm28($data, $key) {
        return data_get($data, $key) == 1 ? 'checked' : '';
    }

    $isUpdate = !empty($row_rm28a['NOPENDAFTARAN_RM28A']) || !empty($row_rm28b['NOPENDAFTARAN']);
    $submitButtonText = $isUpdate ? "Update" : "Simpan";
@endphp

<div class="card-body">
    <form id="rm28Form" action="{{ route('rme.igd.form.rm28.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">

        {{-- ==================================================================================================================== --}}
        {{--                                                     BAGIAN A                                                         --}}
        {{-- ==================================================================================================================== --}}
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">BAGIAN A - ASESMEN NEONATUS</h3>
            </div>
            <div class="card-body">
                <!-- KELUHAN UTAMA -->
                <div class="form-group">
                    <label for="keluhan_utama">Keluhan Utama</label>
                    <textarea class="form-control" id="keluhan_utama" name="keluhan_utama" rows="3" placeholder="Masukkan Keluhan Utama">{{ getValueRm28($row_rm28a, 'KELUHAN_UTAMA') }}</textarea>
                </div>

                <!-- FAKTOR RESIKO IBU -->
                <div class="card card-outline card-success mt-4">
                    <div class="card-header"><h3 class="card-title">Faktor Resiko Ibu</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Prenatal Obstetri Ibu:</label>
                                    <div class="form-group">
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="pre_hipereme" name="pre_hipereme" {{ isCheckedRm28($row_rm28a, 'PRE_HIPEREME') }}><label class="font-weight-normal ml-2" for="pre_hipereme">Hiperemesis</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="pre_hiperten" name="pre_hiperten" {{ isCheckedRm28($row_rm28a, 'PRE_HIPERTEN') }}><label class="font-weight-normal ml-2" for="pre_hiperten">Hipertensi</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="pre_pend" name="pre_pend" {{ isCheckedRm28($row_rm28a, 'PRE_PEND') }}><label class="font-weight-normal ml-2" for="pre_pend">Pendarahan</label></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="icheck-primary"><input type="checkbox" id="pre_inf" name="pre_inf" {{ isCheckedRm28($row_rm28a, 'PRE_INF') }}><label class="font-weight-normal ml-2" for="pre_inf">Infeksi, bila ada sebutkan...</label></div>
                                    </div>
                                    <div class="form-group ml-4" id="pre_ket_inf_CONTAINER" style="display: {{ isCheckedRm28($row_rm28a, 'PRE_INF') ? 'block' : 'none' }};"><input type="text" class="form-control form-control-sm" id="pre_ket_inf" name="pre_ket_inf" placeholder="Keterangan Infeksi" value="{{ getValueRm28($row_rm28a, 'PRE_KET_INF') }}"></div>
                                    <div class="form-group">
                                        <div class="icheck-primary"><input type="checkbox" id="pre_min_obat" name="pre_min_obat" {{ isCheckedRm28($row_rm28a, 'PRE_MIN_OBAT') }}><label class="font-weight-normal ml-2" for="pre_min_obat">Minum Obat, bila ada sebutkan...</label></div>
                                    </div>
                                    <div class="form-group ml-4" id="pre_ket_obat_CONTAINER" style="display: {{ isCheckedRm28($row_rm28a, 'PRE_MIN_OBAT') ? 'block' : 'none' }};"><input type="text" class="form-control form-control-sm" id="pre_ket_obat" name="pre_ket_obat" placeholder="Keterangan Obat" value="{{ getValueRm28($row_rm28a, 'PRE_KET_OBAT') }}"></div>
                                    <div class="form-group">
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="pre_rokok" name="pre_rokok" {{ isCheckedRm28($row_rm28a, 'PRE_ROKOK') }}><label class="font-weight-normal ml-2" for="pre_rokok">Merokok</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="pre_jamu" name="pre_jamu" {{ isCheckedRm28($row_rm28a, 'PRE_JAMU') }}><label class="font-weight-normal ml-2" for="pre_jamu">Minum Jamu</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="pre_alko" name="pre_alko" {{ isCheckedRm28($row_rm28a, 'PRE_ALKO') }}><label class="font-weight-normal ml-2" for="pre_alko">Minum Alkohol</label></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Riwayat Obstetri Ibu (G/P/A):</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">G</span></div>
                                        <input type="number" class="form-control" id="ro_g" name="ro_g" placeholder="G" min="1" max="10" value="{{ getValueRm28($row_rm28a, 'RO_G') }}">
                                        <div class="input-group-prepend"><span class="input-group-text">P</span></div>
                                        <input type="number" class="form-control" id="ro_p" name="ro_p" placeholder="P" min="0" max="10" value="{{ getValueRm28($row_rm28a, 'RO_P') }}">
                                        <div class="input-group-prepend"><span class="input-group-text">A</span></div>
                                        <input type="number" class="form-control" id="ro_a" name="ro_a" placeholder="A" min="0" max="10" value="{{ getValueRm28($row_rm28a, 'RO_A') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Status Gizi Ibu:</label>
                                    <div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="sg_normal" name="sg_normal" value="1" {{ isCheckedRm28($row_rm28a, 'SG_NORMAL') }}><label class="font-weight-normal ml-2" for="sg_normal">Normal</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="sg_malnut" name="sg_malnut" value="1" {{ isCheckedRm28($row_rm28a, 'SG_MALNUT') }}><label class="font-weight-normal ml-2" for="sg_malnut">Malnutrisi</label></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Perinatal:</label>
                                    <div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="peri_asf" name="peri_asf" value="1" {{ isCheckedRm28($row_rm28a, 'PERI_ASF') }}><label class="font-weight-normal ml-2" for="peri_asf">Asfiksia</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="peri_normal" name="peri_normal" value="1" {{ isCheckedRm28($row_rm28a, 'PERI_NORMAL') }}><label class="font-weight-normal ml-2" for="peri_normal">Normal</label></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Postnatal:</label>
                                    <div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="post_riw" name="post_riw" value="1" {{ isCheckedRm28($row_rm28a, 'POST_RIW') }}><label class="font-weight-normal ml-2" for="post_riw">Riwayat sakit berat</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="post_kej" name="post_kej" value="1" {{ isCheckedRm28($row_rm28a, 'POST_KEJ') }}><label class="font-weight-normal ml-2" for="post_kej">Kejang</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="post_tra" name="post_tra" value="1" {{ isCheckedRm28($row_rm28a, 'POST_TRA') }}><label class="font-weight-normal ml-2" for="post_tra">Trauma</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="post_lain" name="post_lain" value="1" {{ isCheckedRm28($row_rm28a, 'POST_LAIN') }}><label class="font-weight-normal ml-2" for="post_lain">Kelainan lain</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="post_normal" name="post_normal" value="1" {{ isCheckedRm28($row_rm28a, 'POST_NORMAL') }}><label class="font-weight-normal ml-2" for="post_normal">Normal</label></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Hospitalisasi:</label>
                                    <div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="hos_0" name="hos_0" value="1" {{ isCheckedRm28($row_rm28a, 'HOS_0') }}><label class="font-weight-normal ml-2" for="hos_0">Belum pernah</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="hos_1" name="hos_1" value="1" {{ isCheckedRm28($row_rm28a, 'HOS_1') }}><label class="font-weight-normal ml-2" for="hos_1">1x</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="hos_2" name="hos_2" value="1" {{ isCheckedRm28($row_rm28a, 'HOS_2') }}><label class="font-weight-normal ml-2" for="hos_2">2x</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="hos_3" name="hos_3" value="1" {{ isCheckedRm28($row_rm28a, 'HOS_3') }}><label class="font-weight-normal ml-2" for="hos_3">3x</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="hos_4" name="hos_4" value="1" {{ isCheckedRm28($row_rm28a, 'HOS_4') }}><label class="font-weight-normal ml-2" for="hos_4">4x</label></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIWAYAT KELAHIRAN -->
                <div class="card card-outline card-success mt-4">
                    <div class="card-header"><h3 class="card-title">Riwayat Kelahiran</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label for="rk_usia">Usia (Minggu)</label><input type="text" class="form-control" id="rk_usia" name="rk_usia" value="{{ getValueRm28($row_rm28a, 'RK_USIA') }}" placeholder="Usia dalam minggu"></div>
                                <div class="form-group">
                                    <label>Tempat Lahir:</label>
                                    <div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="rk_dlm_rs" name="rk_dlm_rs" value="1" {{ isCheckedRm28($row_rm28a, 'RK_DLM_RS') }}><label class="font-weight-normal ml-2" for="rk_dlm_rs">Dalam RS</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="rk_luar_rs" name="rk_luar_rs" value="1" {{ isCheckedRm28($row_rm28a, 'RK_LUAR_RS') }}><label class="font-weight-normal ml-2" for="rk_luar_rs">Luar RS</label></div>
                                    </div>
                                </div>
                                <div class="form-group"><label>Jenis Persalinan / Penyulit</label><div class="input-group"><select class="form-control" id="jp" name="jp"><option value="">Pilih Jenis Persalinan</option><option value="Partus Normal" {{ getValueRm28($row_rm28a, 'JP') == 'Partus Normal' ? 'selected' : '' }}>Partus Normal</option><option value="Sectio Caesariam" {{ getValueRm28($row_rm28a, 'JP') == 'Sectio Caesariam' ? 'selected' : '' }}>Sectio Caesaria</option><option value="Vacum Extraksi" {{ getValueRm28($row_rm28a, 'JP') == 'Vacum Extraksi' ? 'selected' : '' }}>Vacum Extraksi</option></select><div class="input-group-prepend"><span class="input-group-text">/</span></div><input type="text" class="form-control" id="jp_penyulit" name="jp_penyulit" placeholder="Penyulit" value="{{ getValueRm28($row_rm28a, 'JP_PENYULIT') }}"></div></div>
                                <div class="form-group"><label for="do">Ditolong Oleh</label><select class="form-control" id="do" name="do"><option value="">Pilih Penolong</option><option value="Dokter Spesialis" {{ getValueRm28($row_rm28a, 'DO') == 'Dokter Spesialis' ? 'selected' : '' }}>Dokter Spesialis</option><option value="Dokter" {{ getValueRm28($row_rm28a, 'DO') == 'Dokter' ? 'selected' : '' }}>Dokter</option><option value="Bidan" {{ getValueRm28($row_rm28a, 'DO') == 'Bidan' ? 'selected' : '' }}>Bidan</option><option value="Perawat" {{ getValueRm28($row_rm28a, 'DO') == 'Perawat' ? 'selected' : '' }}>Perawat</option><option value="Dukun Bayi" {{ getValueRm28($row_rm28a, 'DO') == 'Dukun Bayi' ? 'selected' : '' }}>Dukun Bayi</option></select></div>
                                <div class="row"><div class="col-md-6"><div class="form-group"><label for="tgl_lahir">Tanggal Lahir</label><input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="{{ !empty($row_rm28a['TGL_LAHIR']) ? \Carbon\Carbon::parse($row_rm28a['TGL_LAHIR'])->format('Y-m-d') : '' }}"></div></div><div class="col-md-6"><div class="form-group"><label for="jam_lahir">Jam Lahir</label><input type="time" class="form-control" id="jam_lahir" name="jam_lahir" value="{{ !empty($row_rm28a['JAM_LAHIR']) ? \Carbon\Carbon::parse($row_rm28a['JAM_LAHIR'])->format('H:i') : '' }}"></div></div></div>
                            </div>
                            <div class="col-md-6">
                                <div class="row"><div class="col-md-4"><div class="form-group"><label for="bb_lahir">BB Lahir</label><div class="input-group"><input type="text" class="form-control" id="bb_lahir" name="bb_lahir" value="{{ getValueRm28($row_rm28a, 'BB_LAHIR') }}"><div class="input-group-append"><span class="input-group-text">gr</span></div></div></div></div><div class="col-md-4"><div class="form-group"><label for="pb_lahir">PB Lahir</label><div class="input-group"><input type="text" class="form-control" id="pb_lahir" name="pb_lahir" value="{{ getValueRm28($row_rm28a, 'PB_LAHIR') }}"><div class="input-group-append"><span class="input-group-text">cm</span></div></div></div></div><div class="col-md-4"><div class="form-group"><label for="lk_lahir">LK Lahir</label><input type="text" class="form-control" id="lk_lahir" name="lk_lahir" value="{{ getValueRm28($row_rm28a, 'LK_LAHIR') }}"></div></div></div>
                                <div class="row"><div class="col-md-6"><div class="form-group"><label for="kk_tgl">Tgl Ketuban Pecah</label><input type="date" class="form-control" id="kk_tgl" name="kk_tgl" value="{{ !empty($row_rm28a['KK_TGL']) ? \Carbon\Carbon::parse($row_rm28a['KK_TGL'])->format('Y-m-d') : '' }}"></div></div><div class="col-md-6"><div class="form-group"><label for="kk_jam">Jam Ketuban Pecah</label><input type="time" class="form-control" id="kk_jam" name="kk_jam" value="{{ !empty($row_rm28a['KK_JAM']) ? \Carbon\Carbon::parse($row_rm28a['KK_JAM'])->format('H:i') : '' }}"></div></div></div>
                                <div class="form-group"><label for="kk_war">Warna Ketuban</label><input type="text" class="form-control" id="kk_war" name="kk_war" value="{{ getValueRm28($row_rm28a, 'KK_WAR') }}" placeholder="Warna ketuban"></div>
                                <div class="form-group">
                                    <label>Ketuban Pecah:</label>
                                    <div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="kp_ats_24" name="kp_ats_24" {{ isCheckedRm28($row_rm28a, 'KP_ATS_24') }}><label class="font-weight-normal ml-2" for="kp_ats_24">&gt; 24 Jam</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="kp_bwh_24" name="kp_bwh_24" {{ isCheckedRm28($row_rm28a, 'KP_BWH_24') }}><label class="font-weight-normal ml-2" for="kp_bwh_24">&lt; 24 Jam</label></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Jumlah Ketuban:</label>
                                    <div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="jk_nor" name="jk_nor" {{ isCheckedRm28($row_rm28a, 'JK_NOR') }}><label class="font-weight-normal ml-2" for="jk_nor">Normal</label></div>
                                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" id="jk_olig" name="jk_olig" {{ isCheckedRm28($row_rm28a, 'JK_OLIG') }}><label class="font-weight-normal ml-2" for="jk_olig">Oligohidramnion</label></div>
                                        <div class="icheck-primary d-inline"><input type="checkbox" id="jk_poli" name="jk_poli" {{ isCheckedRm28($row_rm28a, 'JK_POLI') }}><label class="font-weight-normal ml-2" for="jk_poli">Polihidramnion</label></div>
                                    </div>
                                </div>
                                <div class="form-group"><label>Apgar Score (1' / 5' / 10'):</label><div class="input-group"><input type="text" class="form-control text-center" id="as_1" name="as_1" placeholder="1'" value="{{ getValueRm28($row_rm28a, 'AS_1') }}"><div class="input-group-prepend"><span class="input-group-text">/</span></div><input type="text" class="form-control text-center" id="as_2" name="as_2" placeholder="5'" value="{{ getValueRm28($row_rm28a, 'AS_2') }}"><div class="input-group-prepend"><span class="input-group-text">/</span></div><input type="text" class="form-control text-center" id="as_3" name="as_3" placeholder="10'" value="{{ getValueRm28($row_rm28a, 'AS_3') }}"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PEMERIKSAAN FISIK & TANDA VITAL -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-outline card-success mt-4">
                            <div class="card-header"><h3 class="card-title">Pemeriksaan Fisik</h3></div>
                            <div class="card-body">
                                @php
                                    $kesan = '';
                                    if (!empty($row_rm28a['PACS1_MENINGGAL'])) $kesan = 'Meninggal';
                                    elseif (!empty($row_rm28a['PACS2_SADAR'])) $kesan = 'Kesadaran Penuh';
                                    elseif (!empty($row_rm28a['PACS3_RESPON'])) $kesan = 'Respon Suara';
                                    elseif (!empty($row_rm28a['PACS4_RESPON'])) $kesan = 'Respon Nyeri';
                                @endphp
                                <div class="form-group"><label for="pf_kes">Kesadaran</label><input type="text" class="form-control" id="pf_kes" name="pf_kes" value="{{ $kesan }}" readonly></div>
                                <div class="form-group"><label for="pf_jk">Jenis Kelamin</label><input type="text" class="form-control" value="{{ $gender == 'L' ? 'Laki-laki' : ($gender == 'P' ? 'Perempuan' : '') }}" readonly><input type="hidden" name="pf_jk" value="{{ $gender }}"></div>
                                <div class="row">
                                    <div class="col-md-6"><div class="form-group"><label for="pf_bb">Berat Badan</label><div class="input-group"><input type="text" class="form-control" id="pf_bb" name="pf_bb" value="{{ getValueRm28($row_rm28a, 'PACS4_BB') }}" readonly><div class="input-group-append"><span class="input-group-text">kg</span></div></div></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="pf_pb">Panjang Badan</label><div class="input-group"><input type="text" class="form-control" id="pf_pb" name="pf_pb" value="{{ getValueRm28($row_rm28a, 'PACS4_TB') }}" readonly><div class="input-group-append"><span class="input-group-text">cm</span></div></div></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"><div class="form-group"><label for="pf_lk">Lingkar Kepala</label><input type="text" class="form-control" id="pf_lk" name="pf_lk" value="{{ getValueRm28($row_rm28a, 'PF_LK') }}"></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="pf_kk">Kelainan Kongenital</label><input type="text" class="form-control" id="pf_kk" name="pf_kk" value="{{ getValueRm28($row_rm28a, 'PF_KK') }}"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-outline card-success mt-4">
                            <div class="card-header"><h3 class="card-title">Tanda Vital</h3></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6"><div class="form-group"><label for="tv_s">Suhu</label><div class="input-group"><input type="text" class="form-control" id="tv_s" name="tv_s" value="{{ getValueRm28($row_rm28a, 'PACS2_TEMP') }}" readonly><div class="input-group-append"><span class="input-group-text">°C</span></div></div></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="tv_hr">Nadi</label><div class="input-group"><input type="text" class="form-control" id="tv_hr" name="tv_hr" value="{{ getValueRm28($row_rm28a, 'PACS1_NADI') }}" readonly><div class="input-group-append"><span class="input-group-text">x/menit</span></div></div></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"><div class="form-group"><label for="tv_rr">Pernafasan</label><div class="input-group"><input type="text" class="form-control" id="tv_rr" name="tv_rr" value="{{ getValueRm28($row_rm28a, 'PACS2_NAFAS') }}" readonly><div class="input-group-append"><span class="input-group-text">x/menit</span></div></div></div></div>
                                    <div class="col-md-6"><div class="form-group"><label for="tv_o2">Saturasi Oksigen</label><div class="input-group"><input type="text" class="form-control" id="tv_o2" name="tv_o2" value="{{ getValueRm28($row_rm28a, 'PACS3_SATURASI') }}" readonly><div class="input-group-append"><span class="input-group-text">%</span></div></div></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIWAYAT TINDAKAN -->
                <div class="form-group mt-3"><label for="riw_tind">Riwayat Tindakan</label><textarea class="form-control" id="riw_tind" name="riw_tind" rows="3" placeholder="Masukkan Riwayat Tindakan">{{ getValueRm28($row_rm28a, 'RIW_TIND') }}</textarea></div>

                <!-- IMUNISASI -->
                <div class="card card-outline card-success mt-4">
                    <div class="card-header"><h3 class="card-title">Imunisasi</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="HEPATITISB" name="HEPATITISB" {{ isCheckedRm28($row_rm28a, 'HEPATITISB') }}><label class="font-weight-normal ml-2" for="HEPATITISB">Hepatitis B</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="POLIO" name="POLIO" {{ isCheckedRm28($row_rm28a, 'POLIO') }}><label class="font-weight-normal ml-2" for="POLIO">Polio</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="BCG" name="BCG" {{ isCheckedRm28($row_rm28a, 'BCG') }}><label class="font-weight-normal ml-2" for="BCG">BCG</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="DTP" name="DTP" {{ isCheckedRm28($row_rm28a, 'DTP') }}><label class="font-weight-normal ml-2" for="DTP">DTP</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="HIB" name="HIB" {{ isCheckedRm28($row_rm28a, 'HIB') }}><label class="font-weight-normal ml-2" for="HIB">Hib</label></div></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="PCV" name="PCV" {{ isCheckedRm28($row_rm28a, 'PCV') }}><label class="font-weight-normal ml-2" for="PCV">PCV</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="ROTAVIRUS" name="ROTAVIRUS" {{ isCheckedRm28($row_rm28a, 'ROTAVIRUS') }}><label class="font-weight-normal ml-2" for="ROTAVIRUS">Rotavirus</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="INFLUEN" name="INFLUEN" {{ isCheckedRm28($row_rm28a, 'INFLUEN') }}><label class="font-weight-normal ml-2" for="INFLUEN">Influenza</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="MR" name="MR" {{ isCheckedRm28($row_rm28a, 'MR') }}><label class="font-weight-normal ml-2" for="MR">MR/MMR</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="JE" name="JE" {{ isCheckedRm28($row_rm28a, 'JE') }}><label class="font-weight-normal ml-2" for="JE">JE</label></div></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="VARISELA" name="VARISELA" {{ isCheckedRm28($row_rm28a, 'VARISELA') }}><label class="font-weight-normal ml-2" for="VARISELA">Varisela</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="HEPATITISA" name="HEPATITISA" {{ isCheckedRm28($row_rm28a, 'HEPATITISA') }}><label class="font-weight-normal ml-2" for="HEPATITISA">Hepatitis A</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="TIFOID" name="TIFOID" {{ isCheckedRm28($row_rm28a, 'TIFOID') }}><label class="font-weight-normal ml-2" for="TIFOID">Tifoid</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="HPV" name="HPV" {{ isCheckedRm28($row_rm28a, 'HPV') }}><label class="font-weight-normal ml-2" for="HPV">HPV</label></div></div>
                                <div class="form-group"><div class="icheck-primary"><input type="checkbox" id="DENGUE" name="DENGUE" {{ isCheckedRm28($row_rm28a, 'DENGUE') }}><label class="font-weight-normal ml-2" for="DENGUE">Dengue</label></div></div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <div class="icheck-primary"><input type="checkbox" id="IMUN_LAIN" name="IMUN_LAIN" {{ isCheckedRm28($row_rm28a, 'IMUN_LAIN') }}><label class="font-weight-normal ml-2" for="IMUN_LAIN">Lainnya</label></div>
                            <div class="col-md-4 mt-2 pl-4"><input type="text" class="form-control form-control-sm" id="IMUN_LAIN_KET" name="IMUN_LAIN_KET" placeholder="Keterangan imunisasi lainnya" value="{{ getValueRm28($row_rm28a, 'IMUN_LAIN_KET') }}" style="display: {{ isCheckedRm28($row_rm28a, 'IMUN_LAIN') ? 'block' : 'none' }};"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================================================================================================================== --}}
        {{--                                                     BAGIAN B                                                         --}}
        {{-- ==================================================================================================================== --}}
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">BAGIAN B - IDENTIFIKASI BAYI DAN ASESMEN NYERI</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nyeri</label>
                    <div>
                        <div class="icheck-primary d-inline mr-3"><input type="checkbox" name="NYERI_YA" id="NYERI_YA" value="1" {{ isCheckedRm28($row_rm28b, 'NYERI_YA') }}><label class="font-weight-normal ml-2" for="NYERI_YA">Ya</label></div>
                        <div class="icheck-primary d-inline"><input type="checkbox" name="NYERI_TIDAK" id="NYERI_TIDAK" value="1" {{ isCheckedRm28($row_rm28b, 'NYERI_TIDAK') }}><label class="font-weight-normal ml-2" for="NYERI_TIDAK">Tidak</label></div>
                    </div>
                </div>
            <div id="asesmenNyeriContainer" class="table-responsive mb-4" style="display: {{ isCheckedRm28($row_rm28b, 'NYERI_YA') ? 'block' : 'none' }};">
                <table class="table table-bordered">
                    <thead class="table-info"><tr><th>Parameter</th><th></th><th></th><th>Pilihan</th></tr></thead>
                    <tbody>
                        <tr><td rowspan="2">Ekspresi Wajah</td><td>Otot-otot relaks</td><td>Wajah tenang, ekspresi netral</td><td><div class="icheck-primary"><input type="checkbox" name="EKSPRESI0" id="EKSPRESI0" value="1" {{ isCheckedRm28($row_rm28b, 'EKSPRESI0') }}><label class="font-weight-normal ml-2" for="EKSPRESI0">0</label></div></td></tr>
                        <tr><td>Meringis</td><td>Otot wajah tegang, alis berkerut, dagu dan rahang tegang</td><td><div class="icheck-primary"><input type="checkbox" name="EKSPRESI1" id="EKSPRESI1" value="1" {{ isCheckedRm28($row_rm28b, 'EKSPRESI1') }}><label class="font-weight-normal ml-2" for="EKSPRESI1">1</label></div></td></tr>
                        <tr><td rowspan="3">Menangis</td><td>Tidak menangis</td><td>Tenang, tidak menangis</td><td><div class="icheck-primary"><input type="checkbox" name="MENANGIS0" id="MENANGIS0" value="1" {{ isCheckedRm28($row_rm28b, 'MENANGIS0') }}><label class="ml-2" for="MENANGIS0">0</label></div></td></tr>
                        <tr><td>Mengerang/Menangis pelan</td><td>Merengek ringan, kadang-kadang</td><td><div class="icheck-primary"><input type="checkbox" name="MENANGIS1" id="MENANGIS1" value="1" {{ isCheckedRm28($row_rm28b, 'MENANGIS1') }}><label class="font-weight-normal ml-2" for="MENANGIS1">1</label></div></td></tr>
                        <tr><td>Menangis keras</td><td>Berteriak kencang, menangis, melengking, terus menerus<br><small>Catatan : menangis lirih mungkin dinilai jika bayi diintubasi yang dibuktikan melalui gerakan mulut dan wajah yang jelas</small></td><td><div class="icheck-primary"><input type="checkbox" name="MENANGIS2" id="MENANGIS2" value="1" {{ isCheckedRm28($row_rm28b, 'MENANGIS2') }}><label class="font-weight-normal ml-2" for="MENANGIS2">2</label></div></td></tr>
                        <tr><td rowspan="2">Pola Bernafas</td><td>Bernafas Relaks</td><td>Pola bernafas Bayi yang normal</td><td><div class="icheck-primary"><input type="checkbox" name="BERNAFAS0" id="BERNAFAS0" value="1" {{ isCheckedRm28($row_rm28b, 'BERNAFAS0') }}><label class="font-weight-normal ml-2" for="BERNAFAS0">0</label></div></td></tr>
                        <tr><td>Perubahan pola bernafas</td><td>Tidak teratur, lebih cepat dari biasanya, Tersedak, Nafas Tertahan</td><td><div class="icheck-primary"><input type="checkbox" name="BERNAFAS1" id="BERNAFAS1" value="1" {{ isCheckedRm28($row_rm28b, 'BERNAFAS1') }}><label class="font-weight-normal ml-2" for="BERNAFAS1">1</label></div></td></tr>
                        <tr><td rowspan="2">Lengan</td><td>Relaks</td><td>Tidak ada kekuatan otot, gerakan tangan acak sekali-sekali</td><td><div class="icheck-primary"><input type="checkbox" name="LENGAN0" id="LENGAN0" value="1" {{ isCheckedRm28($row_rm28b, 'LENGAN0') }}><label class="font-weight-normal ml-2" for="LENGAN0">0</label></div></td></tr>
                        <tr><td>Fleksi/Extensi</td><td>Tegang, lengan lurus, kaku, dan/atau ekstensi</td><td><div class="icheck-primary"><input type="checkbox" name="LENGAN1" id="LENGAN1" value="1" {{ isCheckedRm28($row_rm28b, 'LENGAN1') }}><label class="font-weight-normal ml-2" for="LENGAN1">1</label></div></td></tr>
                        <tr><td rowspan="2">Kaki</td><td>Relaks</td><td>Tidak ada kekuatan otot, gerakan tangan acak sekali-sekali</td><td><div class="icheck-primary"><input type="checkbox" name="KAKI0" id="KAKI0" value="1" {{ isCheckedRm28($row_rm28b, 'KAKI0') }}><label class="font-weight-normal ml-2" for="KAKI0">0</label></div></td></tr>
                        <tr><td>Fleksi/Extensi</td><td>Tegang. kaki lurus, kaku, dan/atau ekstensi cepat ekstensi, fleksi</td><td><div class="icheck-primary"><input type="checkbox" name="KAKI1" id="KAKI1" value="1" {{ isCheckedRm28($row_rm28b, 'KAKI1') }}><label class="font-weight-normal ml-2" for="KAKI1">1</label></div></td></tr>
                        <tr><td rowspan="2">Keadaan Kesadaran/Kewaspadaan</td><td>Tidur/bangun</td><td>Tenang, tidur damai atau gerakan kaki acak yang terjaga</td><td><div class="icheck-primary"><input type="checkbox" name="KESADARAN0" id="KESADARAN0" value="1" {{ isCheckedRm28($row_rm28b, 'KESADARAN0') }}><label class="font-weight-normal ml-2" for="KESADARAN0">0</label></div></td></tr>
                        <tr><td>Rewel</td><td>Terjaga, gelisah, dan meronta-ronta</td><td><div class="icheck-primary"><input type="checkbox" name="KESADARAN1" id="KESADARAN1" value="1" {{ isCheckedRm28($row_rm28b, 'KESADARAN1') }}><label class="font-weight-normal ml-2" for="KESADARAN1">1</label></div></td></tr>
                        <tr><td>Jumlah Nilai</td><td colspan="2">Total skor dari semua parameter</td><td><input type="text" class="form-control" id="JUMLAH_NILAI" name="JUMLAH_NILAI" value="{{ getValueRm28($row_rm28b, 'JUMLAH_NILAI') }}" readonly></td></tr>
                        <tr><td colspan="4"><small>Skor 0-2 (Tidak nyeri s.d nyeri ringan) : Tidak perlu intervensi<br>3-4 (Nyeri ringan s.d nyeri sedang) : Intervensi non farmakologis dengan penilaian ulang 30 menit<br>&gt;4 (nyeri hebat) : Intervensi non farmakologis dan boleh juga intervensi farmakologis dengan penilaian ulang 30 menit</small></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Data Bayi</h3></div>
                <div class="card-body">
                    <div class="row"><div class="col-md-6"><div class="form-group"><label for="NAMA_IBU">Nama Ibu</label><input type="text" class="form-control" id="NAMA_IBU" name="NAMA_IBU" value="{{ getValueRm28($row_rm28b, 'NAMA_IBU') }}"></div></div><div class="col-md-6"><div class="form-group"><label for="NAMA_AYAH">Nama Ayah</label><input type="text" class="form-control" id="NAMA_AYAH" name="NAMA_AYAH" value="{{ getValueRm28($row_rm28b, 'NAMA_AYAH') }}"></div></div></div>
                    <div class="row"><div class="col-md-6"><div class="form-group"><label for="NORM_IBU">No.RM Ibu</label><input type="text" class="form-control" id="NORM_IBU" name="NORM_IBU" value="{{ getValueRm28($row_rm28b, 'NORM_IBU') }}"></div></div><div class="col-md-6"><div class="form-group"><label for="NAMA_BAYI">Nama Bayi</label><input type="text" class="form-control" id="NAMA_BAYI" name="NAMA_BAYI" value="{{ htmlspecialchars(preg_replace('/\b(Sdr|Ny|Tn|By|,)\.?\s*/i', '', $namaPasien)) }}" readonly></div></div></div>
                    <div class="row"><div class="col-md-6"><div class="form-group"><label for="NORM_BAYI">No.RM Bayi</label><input type="text" class="form-control" id="NORM_BAYI" name="NORM_BAYI" value="{{ $norm }}" readonly></div></div><div class="col-md-6"><div class="form-group"><label for="TGL_LAHIR_BAYI">Tanggal Lahir Bayi</label><input type="date" class="form-control" name="TGL_LAHIR_BAYI" value="{{ !empty($row_rm28b['TGL_LAHIR_BAYI']) ? \Carbon\Carbon::parse($row_rm28b['TGL_LAHIR_BAYI'])->format('Y-m-d') : '' }}"></div></div></div>
                    <div class="row"><div class="col-md-6"><div class="form-group"><label for="JAM_LAHIR_BAYI">Jam Lahir Bayi</label><input type="time" class="form-control" name="JAM_LAHIR_BAYI" step="1" value="{{ !empty($row_rm28b['JAM_LAHIR_BAYI']) ? \Carbon\Carbon::parse($row_rm28b['JAM_LAHIR_BAYI'])->format('H:i:s') : '' }}"></div></div><div class="col-md-6"><div class="form-group"><label for="JK_BAYI">Jenis Kelamin Bayi</label><select class="form-control" id="JK_BAYI" name="JK_BAYI"><option value="L" {{ $gender == 'L' ? 'selected' : '' }}>Laki-laki</option><option value="P" {{ $gender == 'P' ? 'selected' : '' }}>Perempuan</option></select></div></div></div>
                </div>
            </div>

            <div class="card card-outline card-success mt-4">
                <div class="card-header"><h3 class="card-title">Identifikasi Bayi</h3></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4"><div class="card h-100"><div class="card-body"><label for="CAP_JARI_TGN">Cap Jari Tangan Ibu</label><input type="file" class="form-control mb-2" id="CAP_JARI_TGN" name="CAP_JARI_TGN" accept="image/jpeg,image/jpg" onchange="previewImage(this, 'preview-CAP_JARI_TGN', 'Cap Jari Tangan Ibu')"><div id="preview-CAP_JARI_TGN" class="preview-container">@if(!empty($row_rm28b['CAP_JARI_TGN']))<a href="{{ route('rme.igd.form.rm28.showImage', ['noPendaftaran' => $noPendaftaran, 'field' => 'CAP_JARI_TGN']) }}?v={{ time() }}" data-lightbox="cap-images" data-title="Cap Jari Tangan Ibu"><img src="{{ route('rme.igd.form.rm28.showImage', ['noPendaftaran' => $noPendaftaran, 'field' => 'CAP_JARI_TGN']) }}?v={{ time() }}" class="image-preview"></a>@endif</div></div></div></div>
                        <div class="col-md-4"><div class="card h-100"><div class="card-body"><label for="CAP_KAKI_KIRI">Cap Kaki Kiri Bayi</label><input type="file" class="form-control mb-2" id="CAP_KAKI_KIRI" name="CAP_KAKI_KIRI" accept="image/jpeg,image/jpg" onchange="previewImage(this, 'preview-CAP_KAKI_KIRI', 'Cap Kaki Kiri Bayi')"><div id="preview-CAP_KAKI_KIRI" class="preview-container">@if(!empty($row_rm28b['CAP_KAKI_KIRI']))<a href="{{ route('rme.igd.form.rm28.showImage', ['noPendaftaran' => $noPendaftaran, 'field' => 'CAP_KAKI_KIRI']) }}?v={{ time() }}" data-lightbox="cap-images" data-title="Cap Kaki Kiri Bayi"><img src="{{ route('rme.igd.form.rm28.showImage', ['noPendaftaran' => $noPendaftaran, 'field' => 'CAP_KAKI_KIRI']) }}?v={{ time() }}" class="image-preview"></a>@endif</div></div></div></div>
                        <div class="col-md-4"><div class="card h-100"><div class="card-body"><label for="CAP_KAKI_KANAN">Cap Kaki Kanan Bayi</label><input type="file" class="form-control mb-2" id="CAP_KAKI_KANAN" name="CAP_KAKI_KANAN" accept="image/jpeg,image/jpg" onchange="previewImage(this, 'preview-CAP_KAKI_KANAN', 'Cap Kaki Kanan Bayi')"><div id="preview-CAP_KAKI_KANAN" class="preview-container">@if(!empty($row_rm28b['CAP_KAKI_KANAN']))<a href="{{ route('rme.igd.form.rm28.showImage', ['noPendaftaran' => $noPendaftaran, 'field' => 'CAP_KAKI_KANAN']) }}?v={{ time() }}" data-lightbox="cap-images" data-title="Cap Kaki Kanan Bayi"><img src="{{ route('rme.igd.form.rm28.showImage', ['noPendaftaran' => $noPendaftaran, 'field' => 'CAP_KAKI_KANAN']) }}?v={{ time() }}" class="image-preview"></a>@endif</div></div></div></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary mt-4">
                <div class="card-header"><h3 class="card-title">Data Medis</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label for="DPJP">DPJP</label><input type="text" class="form-control" id="DPJP" name="DPJP" value="{{ $dpjp }}" readonly></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="PERAWAT_BAYI">Perawat Bayi</label><select class="form-control" id="PERAWAT_BAYI" name="PERAWAT_BAYI"><option value="">-- Pilih Perawat Bayi --</option>@foreach($perawatList as $perawat)<option value="{{ trim($perawat) }}" {{ getValueRm28($row_rm28b, 'PERAWAT_BAYI') == trim($perawat) ? 'selected' : '' }}>{{ trim($perawat) }}</option>@endforeach</select></div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>{{ $submitButtonText }}</button>
            <button type="button" class="btn btn-outline-danger" id="reset-rm28"><i class="fas fa-times-circle mr-2"></i>Batal / Reset</button>
        </div>
    </form>
</div>

<style>
    .image-preview { width: 100px; height: 100px; object-fit: cover; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.125rem; background: #f8f9fa; cursor: pointer; transition: transform 0.2s; }
    .image-preview:hover { transform: scale(1.1); }
</style>

<script>
$(document).ready(function() {
    // Fungsi untuk toggle field berdasarkan checkbox
    function toggleField(checkbox, fieldContainer) {
        if ($(checkbox).is(":checked")) {
            $(fieldContainer).slideDown();
        } else {
            $(fieldContainer).slideUp();
            $(fieldContainer).find("input").val("");
        }
    }

    // Toggle untuk Keterangan Infeksi dan Obat
    $("#pre_inf").on('change', function() { toggleField(this, "#pre_ket_inf_CONTAINER"); }).trigger('change');
    $("#pre_min_obat").on('change', function() { toggleField(this, "#pre_ket_obat_CONTAINER"); }).trigger('change');

    // Toggle untuk Imunisasi Lainnya
    $('#IMUN_LAIN').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('#IMUN_LAIN_KET').toggle(isChecked);
        if (!isChecked) $('#IMUN_LAIN_KET').val('');
    }).trigger('change');

    // Logika untuk checkbox eksklusif
    function setupExclusiveCheckbox(group) {
        group.forEach(id => {
            $(id).on('change', function() {
                if ($(this).is(':checked')) {
                    group.forEach(otherId => {
                        if (otherId !== id) $(otherId).prop('checked', false);
                    });
                }
            });
        });
    }
    setupExclusiveCheckbox(['#sg_normal', '#sg_malnut']);
    setupExclusiveCheckbox(['#peri_asf', '#peri_normal']);
    setupExclusiveCheckbox(['#rk_dlm_rs', '#rk_luar_rs']);
    setupExclusiveCheckbox(['#kp_ats_24', '#kp_bwh_24']);
    setupExclusiveCheckbox(['#jk_nor', '#jk_olig', '#jk_poli']);
    setupExclusiveCheckbox(['#hos_0', '#hos_1', '#hos_2', '#hos_3', '#hos_4']);
    setupExclusiveCheckbox(['#NYERI_YA', '#NYERI_TIDAK']);

    // Logika untuk Postnatal Normal
    $('#post_normal').on('change', function() {
        const isDisabled = $(this).is(':checked');
        ['#post_riw', '#post_kej', '#post_tra', '#post_lain'].forEach(id => {
            $(id).prop('disabled', isDisabled);
            if (isDisabled) $(id).prop('checked', false);
        });
    }).trigger('change');

    // Toggle Asesmen Nyeri
    $('#NYERI_YA, #NYERI_TIDAK').on('change', function() {
        if ($('#NYERI_YA').is(':checked')) {
            $('#asesmenNyeriContainer').slideDown();
        } else {
            $('#asesmenNyeriContainer').slideUp();
            $('#asesmenNyeriContainer input[type="checkbox"]').prop('checked', false);
            $('#JUMLAH_NILAI').val('');
        }
    }).trigger('change');

    // Kalkulasi Skor Nyeri
    function calculateNyeriTotal() {
        let total = 0;
        if ($('#EKSPRESI1').is(':checked')) total += 1;
        if ($('#MENANGIS1').is(':checked')) total += 1;
        if ($('#MENANGIS2').is(':checked')) total += 2;
        if ($('#BERNAFAS1').is(':checked')) total += 1;
        if ($('#LENGAN1').is(':checked')) total += 1;
        if ($('#KAKI1').is(':checked')) total += 1;
        if ($('#KESADARAN1').is(':checked')) total += 1;
        $('#JUMLAH_NILAI').val(total);
    }

    const nyeriCheckboxGroups = [
        ['#EKSPRESI0', '#EKSPRESI1'], ['#MENANGIS0', '#MENANGIS1', '#MENANGIS2'],
        ['#BERNAFAS0', '#BERNAFAS1'], ['#LENGAN0', '#LENGAN1'],
        ['#KAKI0', '#KAKI1'], ['#KESADARAN0', '#KESADARAN1']
    ];
    nyeriCheckboxGroups.forEach(group => {
        $(group.join(', ')).on('change', function() { // NOSONAR
            if ($(this).is(':checked')) {
                group.forEach(id => { if (id !== `#${this.id}`) $(id).prop('checked', false); });
            }
            calculateNyeriTotal();
        });
    });
    calculateNyeriTotal(); // Initial calculation

    // AJAX Submission
    $('#rm28Form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var formData = new FormData(this);
        var button = form.find('button[type="submit"]');
        var originalButtonText = button.html();

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success');
                    button.html('<i class="fas fa-save"></i> Update');
                } else {
                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            },
            complete: function() {
                button.prop('disabled', false).html(button.html().includes('Update') ? '<i class="fas fa-save"></i> Update' : originalButtonText);
            }
        });
    });

    // Tombol Reset
    $('#reset-rm28').on('click', function() {
        Swal.fire({
            title: 'Yakin ingin batal?',
            text: "Semua isian pada formulir ini akan dikosongkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#rm28Form')[0].reset();
                $('.preview-container').html('');
                $('#rm28Form').find('input[type="checkbox"]').trigger('change');
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });
});

// Fungsi preview gambar global
function previewImage(input, previewId, title) {
    const previewContainer = document.getElementById(previewId);
    const file = input.files[0];
    previewContainer.innerHTML = '';

    if (!file) return;

    const validExtensions = ['image/jpeg', 'image/jpg'];
    if (!validExtensions.includes(file.type)) {
        Swal.fire('Format Tidak Valid', 'Hanya file JPEG/JPG yang diperbolehkan.', 'error');
        input.value = '';
        return;
    }

    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        Swal.fire('Ukuran Terlalu Besar', 'Ukuran file maksimal 2MB.', 'error');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'image-preview';
        const link = document.createElement('a');
        link.href = e.target.result;
        link.setAttribute('data-lightbox', 'cap-images-preview');
        link.setAttribute('data-title', title);
        link.appendChild(img);
        previewContainer.appendChild(link);
    }
    reader.readAsDataURL(file);
}
</script>