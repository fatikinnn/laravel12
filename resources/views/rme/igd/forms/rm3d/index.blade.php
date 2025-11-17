@php
    function getValue($data, $key, $default = '') {
        // Pastikan $data adalah array sebelum digunakan
        return is_array($data) && isset($data[$key]) ? htmlspecialchars(trim($data[$key])) : $default;
    }
    function isChecked($data, $key, $value = 1) {
        return is_array($data) && isset($data[$key]) && trim($data[$key]) == $value ? 'checked' : '';
    }
@endphp

<div class="card-body">
    <form id="rm3dForm" action="{{ route('rme.igd.form.rm3d.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="USER_ENTRY" value="{{ $user['username'] ?? '' }}">

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs nav-justified" id="rm3dTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="rm3d1-tab" data-toggle="tab" href="#rm3d1" role="tab">RM3D.1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="rm3d2-tab" data-toggle="tab" href="#rm3d2" role="tab">RM3D.2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="rm3d3-tab" data-toggle="tab" href="#rm3d3" role="tab">RM3D.3</a>
            </li>
        </ul>

        <div class="tab-content" id="rm3dTabsContent">
            <!-- =========================================================================================== -->
            <!--                                         TAB RM3D.1                                          -->
            <!-- =========================================================================================== -->
            <div class="tab-pane fade show active" id="rm3d1" role="tabpanel">
                <div class="card card-outline card-success mt-3">
                    <div class="card-header"><h3 class="card-title">INFORMASI UMUM</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Tanggal/Jam Datang</label>
                            <input type="datetime-local" class="form-control" name="rm3d1_TGLJAMDATANG" value="{{ getValue($rm3d1, 'TGLJAMDATANG') ? \Carbon\Carbon::parse(getValue($rm3d1, 'TGLJAMDATANG'))->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">Cara Masuk & Rujukan</h3></div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Kolom Cara Masuk --}}
                            <div class="col-md-6 border-right">
                                <div class="form-group">
                                    <label>Cara Masuk</label>
                                    <div class="row">
                                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_CARMAS_IRJ" value="1" {{ isChecked($rm3d1, 'CARMAS_IRJ') }}><label class="form-check-label">IRJ</label></div></div>
                                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_CARMAS_IGD" value="1" {{ isChecked($rm3d1, 'CARMAS_IGD') }}><label class="form-check-label">IGD</label></div></div>
                                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_CARMAS_DOKTERPRIBADI" value="1" {{ isChecked($rm3d1, 'CARMAS_DOKTERPRIBADI') }}><label class="form-check-label">Dokter Pribadi</label></div></div>
                                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_CARMAS_PONEK" value="1" {{ isChecked($rm3d1, 'CARMAS_PONEK') }}><label class="form-check-label">Ponek</label></div></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_CARMAS_DIANTAROLEH" value="1" {{ isChecked($rm3d1, 'CARMAS_DIANTAROLEH') }}><label class="form-check-label">Datang sendiri, diantar oleh</label></div></div>
                                        <div class="col-md-12"><input type="text" class="form-control form-control-sm mt-1" name="rm3d1_CARMAS_DIANTAR_KET" placeholder="Keterangan" value="{{ getValue($rm3d1, 'CARMAS_DIANTAR_KET') }}"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- Kolom Rujukan --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rujukan</label>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" id="rm3d1_RUJUKAN_CHECK" name="rm3d1_RUJUKAN" value="1" {{ isChecked($rm3d1, 'RUJUKAN') }}><label class="form-check-label" for="rm3d1_RUJUKAN_CHECK">Rujukan dari:</label></div>
                                    <div id="rujukan-details-container" class="pl-4 mt-2" style="display: {{ isChecked($rm3d1, 'RUJUKAN') ? 'block' : 'none' }};">
                                        <div class="row mb-2 rujukan-item"><div class="col-md-4"><div class="form-check"><input class="form-check-input rujukan-source-check" type="checkbox" name="rm3d1_RUJUKAN_PUSKES" value="1" {{ isChecked($rm3d1, 'RUJUKAN_PUSKES') }}><label class="form-check-label">Puskesmas</label></div></div><div class="col-md-8"><input type="text" class="form-control form-control-sm rujukan-keterangan" name="rm3d1_RUJUKAN_PUSKES_KET" placeholder="Keterangan" value="{{ getValue($rm3d1, 'RUJUKAN_PUSKES_KET') }}"></div></div>
                                        <div class="row mb-2 rujukan-item"><div class="col-md-4"><div class="form-check"><input class="form-check-input rujukan-source-check" type="checkbox" name="rm3d1_RUJUKAN_BIDAN" value="1" {{ isChecked($rm3d1, 'RUJUKAN_BIDAN') }}><label class="form-check-label">Bidan</label></div></div><div class="col-md-8"><input type="text" class="form-control form-control-sm rujukan-keterangan" name="rm3d1_RUJUKAN_BIDAN_KET" placeholder="Keterangan" value="{{ getValue($rm3d1, 'RUJUKAN_BIDAN_KET') }}"></div></div>
                                        <div class="row mb-2 rujukan-item"><div class="col-md-4"><div class="form-check"><input class="form-check-input rujukan-source-check" type="checkbox" name="rm3d1_RUJUKAN_RB" value="1" {{ isChecked($rm3d1, 'RUJUKAN_RB') }}><label class="form-check-label">RB</label></div></div><div class="col-md-8"><input type="text" class="form-control form-control-sm rujukan-keterangan" name="rm3d1_RUJUKAN_RB_KET" placeholder="Keterangan" value="{{ getValue($rm3d1, 'RUJUKAN_RB_KET') }}"></div></div>
                                        <div class="row mb-2 rujukan-item"><div class="col-md-4"><div class="form-check"><input class="form-check-input rujukan-source-check" type="checkbox" name="rm3d1_RUJUKAN_DOKTER" value="1" {{ isChecked($rm3d1, 'RUJUKAN_DOKTER') }}><label class="form-check-label">Dokter</label></div></div><div class="col-md-8"><input type="text" class="form-control form-control-sm rujukan-keterangan" name="rm3d1_RUJUKAN_DOKTER_KET" placeholder="Keterangan" value="{{ getValue($rm3d1, 'RUJUKAN_DOKTER_KET') }}"></div></div>
                                        <div class="row mb-2 rujukan-item"><div class="col-md-4"><div class="form-check"><input class="form-check-input rujukan-source-check" type="checkbox" name="rm3d1_RUJUKAN_RS" value="1" {{ isChecked($rm3d1, 'RUJUKAN_RS') }}><label class="form-check-label">RS</label></div></div><div class="col-md-8"><input type="text" class="form-control form-control-sm rujukan-keterangan" name="rm3d1_RUJUKAN_RS_KET" placeholder="Keterangan" value="{{ getValue($rm3d1, 'RUJUKAN_RS_KET') }}"></div></div>
                                        <div class="row rujukan-item"><div class="col-md-4"><div class="form-check"><input class="form-check-input rujukan-source-check" type="checkbox" name="rm3d1_DIKIRIMPOLISI" value="1" {{ isChecked($rm3d1, 'DIKIRIMPOLISI') }}><label class="form-check-label">Dikirim Polisi</label></div></div><div class="col-md-8"><input type="text" class="form-control form-control-sm rujukan-keterangan" name="rm3d1_DIKIRIMPOLISI_KET" placeholder="Keterangan" value="{{ getValue($rm3d1, 'DIKIRIMPOLISI_KET') }}"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">ANAMNESA</h3></div>
                    <div class="card-body">
                        <div class="form-group"><label>Keluhan Utama</label><textarea class="form-control" name="rm3d1_KELUHANUTAMA" rows="3">{{ getValue($rm3d1, 'KELUHANUTAMA') }}</textarea></div>
                        
                        <table class="table table-bordered">
                            <tbody>
                                <tr class="bg-light"><th colspan="2">1. Riwayat Keluarga Berencana</th></tr>
                                <tr>
                                    <td width="30%">Jenis KB</td>
                                    <td id="jenis-kb-container">
                                        <div class="form-check form-check-inline"><input class="form-check-input jenis-kb-check" type="checkbox" name="rm3d1_SUNTIK" value="1" {{ isChecked($rm3d1, 'SUNTIK') }}><label class="form-check-label">Suntik</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input jenis-kb-check" type="checkbox" name="rm3d1_PIL" value="1" {{ isChecked($rm3d1, 'PIL') }}><label class="form-check-label">Pil</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input jenis-kb-check" type="checkbox" name="rm3d1_AKDR" value="1" {{ isChecked($rm3d1, 'AKDR') }}><label class="form-check-label">AKDR</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input jenis-kb-check" type="checkbox" name="rm3d1_MOW" value="1" {{ isChecked($rm3d1, 'MOW') }}><label class="form-check-label">MOW</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input jenis-kb-check" type="checkbox" name="rm3d1_PERDARAHAN" value="1" {{ isChecked($rm3d1, 'PERDARAHAN') }}><label class="form-check-label">Perdarahan</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input jenis-kb-check" type="checkbox" name="rm3d1_PID" value="1" {{ isChecked($rm3d1, 'PID') }}><label class="form-check-label">PID</label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input jenis-kb-check" type="checkbox" id="rm3d1_LAINLAIN_CHECK" name="rm3d1_LAINLAIN" value="1" {{ isChecked($rm3d1, 'LAINLAIN') }}>
                                            <label class="form-check-label" for="rm3d1_LAINLAIN_CHECK">Lain-lain</label>
                                        </div>
                                        <div id="kb-lainlain-details" class="mt-2" style="display: {{ isChecked($rm3d1, 'LAINLAIN') ? 'block' : 'none' }};">
                                            <input type="text" class="form-control form-control-sm" name="rm3d1_LAINLAIN_KET" placeholder="Sebutkan jenis KB lain..." value="{{ getValue($rm3d1, 'LAINLAIN_KET') }}">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="kb-details-row" style="display: none;">
                                    <td>Lamanya</td>
                                    <td><input type="text" class="form-control" name="rm3d1_LAMANYA" value="{{ getValue($rm3d1, 'LAMANYA') }}"></td>
                                </tr>
                                <tr class="kb-details-row" style="display: none;">
                                    <td>Komplikasi</td>
                                    <td><input type="text" class="form-control" name="rm3d1_KOMPLIKASI" value="{{ getValue($rm3d1, 'KOMPLIKASI') }}"></td>
                                </tr>

                                <tr class="bg-light"><th colspan="2">2. Riwayat Penyakit/Operasi</th></tr>
                                <tr>
                                    <td>Penyakit yang pernah diderita</td>
                                    <td>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_DM" value="1" {{ isChecked($rm3d1, 'DM') }}><label class="form-check-label">DM</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_HPETITIS" value="1" {{ isChecked($rm3d1, 'HPETITIS') }}><label class="form-check-label">Hepatitis</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_STROKE" value="1" {{ isChecked($rm3d1, 'STROKE') }}><label class="form-check-label">Stroke</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_GINJAL" value="1" {{ isChecked($rm3d1, 'GINJAL') }}><label class="form-check-label">Ginjal</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_HYPERTENSI" value="1" {{ isChecked($rm3d1, 'HYPERTENSI') }}><label class="form-check-label">Hipertensi</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_TBC" value="1" {{ isChecked($rm3d1, 'TBC') }}><label class="form-check-label">TBC</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_JANTUNG" value="1" {{ isChecked($rm3d1, 'JANTUNG') }}><label class="form-check-label">Jantung</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_KEGANASAN" value="1" {{ isChecked($rm3d1, 'KEGANASAN') }}><label class="form-check-label">Keganasan</label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="rm3d1_RIW_LAINLAIN_CHECK" name="rm3d1_RIW_LAINLAIN" value="1" {{ isChecked($rm3d1, 'RIW_LAINLAIN') }}>
                                            <label class="form-check-label" for="rm3d1_RIW_LAINLAIN_CHECK">Lain-lain</label>
                                        </div>
                                        <div id="riwayat-lainlain-details" class="mt-2" style="display: {{ isChecked($rm3d1, 'RIW_LAINLAIN') ? 'block' : 'none' }};">
                                            <input type="text" class="form-control form-control-sm" name="rm3d1_RIWLAINLAIN_KET" placeholder="Sebutkan penyakit lain..." value="{{ getValue($rm3d1, 'RIWLAINLAIN_KET') }}">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pernah Dirawat</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input dirawat-option" type="checkbox" id="rm3d1_DIRAWAT_YA_CHECK" name="rm3d1_DIRAWAT_YA" value="1" {{ isChecked($rm3d1, 'DIRAWAT_YA') }}>
                                            <label class="form-check-label" for="rm3d1_DIRAWAT_YA_CHECK">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input dirawat-option" type="checkbox" id="rm3d1_DIRAWAT_TDK_CHECK" name="rm3d1_DIRAWAT_TDK" value="1" {{ isChecked($rm3d1, 'DIRAWAT_TDK') }}>
                                            <label class="form-check-label" for="rm3d1_DIRAWAT_TDK_CHECK">Tidak</label>
                                        </div>
                                        <div id="dirawat-details-container" style="display: {{ isChecked($rm3d1, 'DIRAWAT_YA') ? 'block' : 'none' }};">
                                            <input type="text" class="form-control form-control-sm mt-1" name="rm3d1_DRWTYA_KAPAN" placeholder="Kapan" value="{{ getValue($rm3d1, 'DRWTYA_KAPAN') }}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="rm3d1_DRWTYA_DIMANA" placeholder="Dimana" value="{{ getValue($rm3d1, 'DRWTYA_DIMANA') }}">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pernah Operasi</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input operasi-option" type="checkbox" id="rm3d1_OPERASI_YA_CHECK" name="rm3d1_OPERASI_YA" value="1" {{ isChecked($rm3d1, 'OPERASI_YA') }}>
                                            <label class="form-check-label" for="rm3d1_OPERASI_YA_CHECK">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input operasi-option" type="checkbox" id="rm3d1_OPERASI_TIDAK_CHECK" name="rm3d1_OPERASI_TIDAK" value="1" {{ isChecked($rm3d1, 'OPERASI_TIDAK') }}>
                                            <label class="form-check-label" for="rm3d1_OPERASI_TIDAK_CHECK">Tidak</label>
                                        </div>
                                        <div id="operasi-details-container" style="display: {{ isChecked($rm3d1, 'OPERASI_YA') ? 'block' : 'none' }};">
                                            <input type="text" class="form-control form-control-sm mt-1" name="rm3d1_OPYA_KAPAN" placeholder="Kapan" value="{{ getValue($rm3d1, 'OPYA_KAPAN') }}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="rm3d1_OPYA_DIMANA" placeholder="Dimana" value="{{ getValue($rm3d1, 'OPYA_DIMANA') }}">
                                        </div>
                                    </td>
                                </tr>

                                <tr class="bg-light"><th colspan="2">3. Riwayat Ginekologi</th></tr>
                                <tr>
                                    <td>Kondisi Ginekologi</td>
                                    <td>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_INFERTILITAS" value="1" {{ isChecked($rm3d1, 'INFERTILITAS') }}><label class="form-check-label">Infertilitas</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_INFEKSIVIRUS" value="1" {{ isChecked($rm3d1, 'INFEKSIVIRUS') }}><label class="form-check-label">Infeksi Virus</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_PMS" value="1" {{ isChecked($rm3d1, 'PMS') }}><label class="form-check-label">PMS</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_KRONIS" value="1" {{ isChecked($rm3d1, 'KRONIS') }}><label class="form-check-label">Kronis</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_ENDOMETRIOSIS" value="1" {{ isChecked($rm3d1, 'ENDOMETRIOSIS') }}><label class="form-check-label">Endometriosis</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_MIOMA" value="1" {{ isChecked($rm3d1, 'MIOMA') }}><label class="form-check-label">Mioma</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_POLIP" value="1" {{ isChecked($rm3d1, 'POLIP') }}><label class="form-check-label">Polip</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_KANKER" value="1" {{ isChecked($rm3d1, 'KANKER') }}><label class="form-check-label">Kanker</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_PERKOSAAN" value="1" {{ isChecked($rm3d1, 'PERKOSAAN') }}><label class="form-check-label">Perkosaan</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_OPERASIKANDUNGAN" value="1" {{ isChecked($rm3d1, 'OPERASIKANDUNGAN') }}><label class="form-check-label">Operasi Kandungan</label></div>
                                    </td>
                                </tr>

                                <tr class="bg-light"><th colspan="2">4. Terapi Komplementer</th></tr>
                                <tr>
                                    <td>Jenis Terapi</td>
                                    <td>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_JAMU" value="1" {{ isChecked($rm3d1, 'JAMU') }}><label class="form-check-label">Jamu</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_AKUPUNTUR" value="1" {{ isChecked($rm3d1, 'AKUPUNTUR') }}><label class="form-check-label">Akupuntur</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_PIJAT" value="1" {{ isChecked($rm3d1, 'PIJAT') }}><label class="form-check-label">Pijat</label></div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="rm3d1_TERAPI_LAINLAIN_CHECK" name="rm3d1_TERAPI_LAINLAIN" value="1" {{ isChecked($rm3d1, 'TERAPI_LAINLAIN') }}>
                                            <label class="form-check-label" for="rm3d1_TERAPI_LAINLAIN_CHECK">Lain-lain</label>
                                        </div>
                                        <div id="terapi-lainlain-details" class="mt-2" style="display: {{ isChecked($rm3d1, 'TERAPI_LAINLAIN') ? 'block' : 'none' }};">
                                            <input type="text" class="form-control form-control-sm" placeholder="Sebutkan terapi lain..." name="rm3d1_TERAPILAIN_KET" value="{{ getValue($rm3d1, 'TERAPILAIN_KET') }}">
                                        </div>
                                    </td>
                                </tr>

                                <tr class="bg-light"><th colspan="2">5. Riwayat Alergi</th></tr>
                                <tr>
                                    <td>Alergi</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input alergi-option" type="checkbox" id="rm3d1_RA_YA_CHECK" name="rm3d1_RA_ADA" value="1" {{ isChecked($rm3d1, 'RA_ADA') }}>
                                            <label class="form-check-label" for="rm3d1_RA_YA_CHECK">Ada</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input alergi-option" type="checkbox" id="rm3d1_RA_TIDAK_CHECK" name="rm3d1_RA_TIDAK" value="1" {{ isChecked($rm3d1, 'RA_TIDAK') }}>
                                            <label class="form-check-label" for="rm3d1_RA_TIDAK_CHECK">Tidak Ada</label>
                                        </div>
                                        <div id="alergi-details-container" class="mt-2" style="display: {{ isChecked($rm3d1, 'RA_ADA') ? 'block' : 'none' }};">
                                            <input type="text" class="form-control form-control-sm" name="rm3d1_RA_ADAKET" placeholder="Jelaskan alergi..." value="{{ getValue($rm3d1, 'RA_ADAKET') }}">
                                        </div>
                                    </td>
                                </tr>

                                <tr class="bg-light"><th colspan="2">6. Kebiasaan</th></tr>
                                <tr>
                                    <td>Rokok</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input rokok-option" type="checkbox" id="rm3d1_MEROKOK_YA_CHECK" name="rm3d1_MEROKOK_YA" value="1" {{ isChecked($rm3d1, 'MEROKOK_YA') }}>
                                            <label class="form-check-label" for="rm3d1_MEROKOK_YA_CHECK">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input rokok-option" type="checkbox" id="rm3d1_MEROKOK_TIDAK_CHECK" name="rm3d1_MEROKOK_TIDAK" value="1" {{ isChecked($rm3d1, 'MEROKOK_TIDAK') }}>
                                            <label class="form-check-label" for="rm3d1_MEROKOK_TIDAK_CHECK">Tidak</label>
                                        </div>
                                        <div id="merokok-details-container" style="display: {{ isChecked($rm3d1, 'MEROKOK_YA') ? 'block' : 'none' }};">
                                            <input type="text" class="form-control form-control-sm mt-1" name="rm3d1_MEROKOK_BATANG" value="{{ getValue($rm3d1, 'MEROKOK_BATANG') }}" placeholder="Jumlah batang/hari">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Obat Tidur</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input obattidur-option" type="checkbox" id="rm3d1_OBATIDUR_YA_CHECK" name="rm3d1_OBATIDUR_YA" value="1" {{ isChecked($rm3d1, 'OBATIDUR_YA') }}>
                                            <label class="form-check-label" for="rm3d1_OBATIDUR_YA_CHECK">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input obattidur-option" type="checkbox" id="rm3d1_OBATIDUR_TIDAK_CHECK" name="rm3d1_OBATIDUR_TIDAK" value="1" {{ isChecked($rm3d1, 'OBATIDUR_TIDAK') }}>
                                            <label class="form-check-label" for="rm3d1_OBATIDUR_TIDAK_CHECK">Tidak</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Olahraga</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input olahraga-option" type="checkbox" id="rm3d1_OLAHRAGA_YA_CHECK" name="rm3d1_OLAHRAGA_YA" value="1" {{ isChecked($rm3d1, 'OLAHRAGA_YA') }}>
                                            <label class="form-check-label" for="rm3d1_OLAHRAGA_YA_CHECK">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input olahraga-option" type="checkbox" id="rm3d1_OLAHRAGA_TDK_CHECK" name="rm3d1_OLAHRAGA_TDK" value="1" {{ isChecked($rm3d1, 'OLAHRAGA_TDK') }}>
                                            <label class="form-check-label" for="rm3d1_OLAHRAGA_TDK_CHECK">Tidak</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alkohol</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input alkohol-option" type="checkbox" id="rm3d1_ALKOHOL_YA_CHECK" name="rm3d1_ALKOHOL_YA" value="1" {{ isChecked($rm3d1, 'ALKOHOL_YA') }}>
                                            <label class="form-check-label" for="rm3d1_ALKOHOL_YA_CHECK">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input alkohol-option" type="checkbox" id="rm3d1_ALKOHOL_TDK_CHECK" name="rm3d1_ALKOHOL_TDK" value="1" {{ isChecked($rm3d1, 'ALKOHOL_TDK') }}>
                                            <label class="form-check-label" for="rm3d1_ALKOHOL_TDK_CHECK">Tidak</label>
                                        </div>
                                        <div id="alkohol-details-container" style="display: {{ isChecked($rm3d1, 'ALKOHOL_YA') ? 'block' : 'none' }};">
                                            <input type="text" class="form-control form-control-sm mt-1" name="rm3d1_ALKOHOLYA_GELAS" value="{{ getValue($rm3d1, 'ALKOHOLYA_GELAS') }}" placeholder="Jumlah gelas/hari">
                                        </div>
                                    </td>
                                </tr>

                                <tr class="bg-light"><th colspan="2">7. Riwayat Menstruasi</th></tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label>Umur Menarche</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><div class="input-group-text"><input type="checkbox" id="rm3d1_UMRMENARRCHE_CHECK" name="rm3d1_UMRMENARRCHE" value="1" {{ isChecked($rm3d1, 'UMRMENARRCHE') }}></div></div>
                                                    <input type="text" class="form-control" id="rm3d1_UMRMENARRCHE_LAMA" name="rm3d1_UMRMENARRCHE_LAMA" value="{{ getValue($rm3d1, 'UMRMENARRCHE_LAMA') }}" placeholder="... tahun" style="display: {{ isChecked($rm3d1, 'UMRMENARRCHE') ? 'block' : 'none' }};">
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group"><label>Lama Haid</label><input type="text" class="form-control" name="rm3d1_LAMA_HAID" value="{{ getValue($rm3d1, 'LAMA_HAID') }}" placeholder="... hari"></div>
                                            <div class="col-md-4 form-group"><label>Banyak Pembalut</label><input type="text" class="form-control" name="rm3d1_BYK_PEMBALUT" value="{{ getValue($rm3d1, 'BYK_PEMBALUT') }}" placeholder="... /hari"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label>HPHT</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><div class="input-group-text"><input type="checkbox" id="rm3d1_HPHT_CHECK" name="rm3d1_HPHT" value="1" {{ isChecked($rm3d1, 'HPHT') }}></div></div>
                                                    @php
                                                        $hphtValue = getValue($rm3d1, 'HPHT_KET');
                                                        $formattedHpht = '';
                                                        if ($hphtValue) {
                                                            try {
                                                                $formattedHpht = \Carbon\Carbon::createFromFormat('d/m/Y', $hphtValue)->format('Y-m-d');
                                                            } catch (\Exception $e) {
                                                                $formattedHpht = \Carbon\Carbon::parse($hphtValue)->format('Y-m-d');
                                                            }
                                                        }
                                                    @endphp
                                                    <input type="date" class="form-control" id="rm3d1_HPHT_KET" name="rm3d1_HPHT_KET" value="{{ $formattedHpht }}" style="display: {{ isChecked($rm3d1, 'HPHT') ? 'block' : 'none' }};">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gangguan Menstruasi</td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_DISMENORROE" value="1" {{ isChecked($rm3d1, 'DISMENORROE') }}><label class="form-check-label">Dismenorroe</label></div></div>
                                            <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_SPOOTING" value="1" {{ isChecked($rm3d1, 'SPOOTING') }}><label class="form-check-label">Spooting</label></div></div>
                                            <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_MENORHAGIA" value="1" {{ isChecked($rm3d1, 'MENORHAGIA') }}><label class="form-check-label">Menorhagia</label></div></div>
                                            <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_PREMENS" value="1" {{ isChecked($rm3d1, 'PREMENS') }}><label class="form-check-label">Premens</label></div></div>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="bg-light"><th colspan="2">8. Riwayat Perkawinan</th></tr>
                                <tr><td>Menikah Berapa Kali</td><td><input type="text" class="form-control" name="rm3d1_MENIKAHBRPKALI" value="{{ getValue($rm3d1, 'MENIKAHBRPKALI') }}"></td></tr>
                                <tr>
                                    <td class="align-middle">Perkawinan 1</td>
                                    <td>
                                        <div class="row align-items-center">
                                            <div class="col-md-3"><label class="form-label mb-0">Usia Kawin</label><input type="text" class="form-control form-control-sm" name="rm3d1_USIAKWN_1" value="{{ getValue($rm3d1, 'USIAKWN_1') }}"></div>
                                            <div class="col-md-9">
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-1" type="checkbox" name="rm3d1_MSHMENIKAH_1" value="1" {{ isChecked($rm3d1, 'MSHMENIKAH_1') }}><label class="form-check-label">Masih Menikah</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-1" type="checkbox" name="rm3d1_CERAI_1" value="1" {{ isChecked($rm3d1, 'CERAI_1') }}><label class="form-check-label">Cerai</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-1" type="checkbox" name="rm3d1_MENINGGAL_1" value="1" {{ isChecked($rm3d1, 'MENINGGAL_1') }}><label class="form-check-label">Meninggal</label></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">Perkawinan 2</td>
                                    <td>
                                        <div class="row align-items-center">
                                            <div class="col-md-3"><label class="form-label mb-0">Usia Kawin</label><input type="text" class="form-control form-control-sm" name="rm3d1_USIAKWN_2" value="{{ getValue($rm3d1, 'USIAKWN_2') }}"></div>
                                            <div class="col-md-9">
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-2" type="checkbox" name="rm3d1_MSHMENIKAH_2" value="1" {{ isChecked($rm3d1, 'MSHMENIKAH_2') }}><label class="form-check-label">Masih Menikah</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-2" type="checkbox" name="rm3d1_CERAI_2" value="1" {{ isChecked($rm3d1, 'CERAI_2') }}><label class="form-check-label">Cerai</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-2" type="checkbox" name="rm3d1_MENINGGAL_2" value="1" {{ isChecked($rm3d1, 'MENINGGAL_2') }}><label class="form-check-label">Meninggal</label></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">Perkawinan 3</td>
                                    <td>
                                        <div class="row align-items-center">
                                            <div class="col-md-3"><label class="form-label mb-0">Usia Kawin</label><input type="text" class="form-control form-control-sm" name="rm3d1_USIAKWN_3" value="{{ getValue($rm3d1, 'USIAKWN_3') }}"></div>
                                            <div class="col-md-9">
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-3" type="checkbox" name="rm3d1_MSHMENIKAH_3" value="1" {{ isChecked($rm3d1, 'MSHMENIKAH_3') }}><label class="form-check-label">Masih Menikah</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-3" type="checkbox" name="rm3d1_CERAI_3" value="1" {{ isChecked($rm3d1, 'CERAI_3') }}><label class="form-check-label">Cerai</label></div>
                                                <div class="form-check form-check-inline"><input class="form-check-input perkawinan-status-3" type="checkbox" name="rm3d1_MENINGGAL_3" value="1" {{ isChecked($rm3d1, 'MENINGGAL_3') }}><label class="form-check-label">Meninggal</label></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">Keadaan Umum & Penilaian Nyeri</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group"><label>Kesadaran</label><input type="text" class="form-control form-control-sm" name="rm3d1_KEDASADARAN" value="{{ getValue($rm3d1, 'KEDASADARAN') }}"></div>
                            <div class="col-md-4 form-group">
                                <label>GCS (E/M/V)</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" name="rm3d1_GCS_E" placeholder="E" value="{{ getValue($rm3d1, 'GCS_E') }}">
                                    <input type="text" class="form-control" name="rm3d1_GCS_M" placeholder="M" value="{{ getValue($rm3d1, 'GCS_M') }}">
                                    <input type="text" class="form-control" name="rm3d1_GCS_V" placeholder="V" value="{{ getValue($rm3d1, 'GCS_V') }}">
                                </div>
                            </div>
                            <div class="col-md-4 form-group"><label>TD</label><input type="text" class="form-control form-control-sm" name="rm3d1_TD" placeholder="mmHg" value="{{ getValue($rm3d1, 'TD') }}"></div>
                            <div class="col-md-4 form-group"><label>Nadi</label><input type="text" class="form-control form-control-sm" name="rm3d1_NADI" placeholder="x/menit" value="{{ getValue($rm3d1, 'NADI') }}"></div>
                            <div class="col-md-4 form-group"><label>Suhu</label><input type="text" class="form-control form-control-sm" name="rm3d1_SUHU" placeholder="°C" value="{{ getValue($rm3d1, 'SUHU') }}"></div>
                            <div class="col-md-4 form-group"><label>Nafas</label><input type="text" class="form-control form-control-sm" name="rm3d1_NAFAS" placeholder="x/menit" value="{{ getValue($rm3d1, 'NAFAS') }}"></div>
                            <div class="col-md-4 form-group"><label>SpO2</label><input type="text" class="form-control form-control-sm" name="rm3d1_SP02" placeholder="%" value="{{ getValue($rm3d1, 'SP02') }}"></div>
                            <div class="col-md-4 form-group"><label>LILA</label><input type="text" class="form-control form-control-sm" name="rm3d1_LILA" placeholder="cm" value="{{ getValue($rm3d1, 'LILA') }}"></div>
                            <div class="col-md-4 form-group"><label>IMT</label><input type="text" class="form-control form-control-sm" name="rm3d1_IMT" value="{{ getValue($rm3d1, 'IMT') }}"></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Bio. Dapat berinteraksi?</label>
                                <div>
                                    <div class="form-check form-check-inline"><input class="form-check-input bio-option" type="checkbox" id="rm3d1_BIO_YA" name="rm3d1_BIO_YA" value="1" {{ isChecked($rm3d1, 'BIO_YA') }}><label class="form-check-label" for="rm3d1_BIO_YA">Ya</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input bio-option" type="checkbox" id="rm3d1_BIO_TDK" name="rm3d1_BIO_TDK" value="1" {{ isChecked($rm3d1, 'BIO_TDK') }}><label class="form-check-label" for="rm3d1_BIO_TDK">Tidak</label></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Psikososial (Kecemasan)</label>
                                <div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_PSIKO_SEDANG" value="1" {{ isChecked($rm3d1, 'PSIKO_SEDANG') }}><label class="form-check-label">Sedang</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_PSIKO_BERAT" value="1" {{ isChecked($rm3d1, 'PSIKO_BERAT') }}><label class="form-check-label">Berat</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d1_PSIKO_PANIK" value="1" {{ isChecked($rm3d1, 'PSIKO_PANIK') }}><label class="form-check-label">Panik</label></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Spiritual. Dapat beribadah?</label>
                                <div>
                                    <div class="form-check form-check-inline"><input class="form-check-input spirit-option" type="checkbox" id="rm3d1_SPIRIT_YA" name="rm3d1_SPIRIT_YA" value="1" {{ isChecked($rm3d1, 'SPIRIT_YA') }}><label class="form-check-label" for="rm3d1_SPIRIT_YA">Ya</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input spirit-option" type="checkbox" id="rm3d1_SPIRIT_TDK" name="rm3d1_SPIRIT_TDK" value="1" {{ isChecked($rm3d1, 'SPIRIT_TDK') }}><label class="form-check-label" for="rm3d1_SPIRIT_TDK">Tidak</label></div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <label>Penilaian Tingkat Nyeri</label>
                        <div class="form-group">
                            <label class="form-label">Apakah Terdapat Keluhan Nyeri?</label>
                            <div class="form-check form-check-inline"><input class="form-check-input nyeri-option" type="checkbox" name="rm3d1_NYERI_YA" id="rm3d1_NYERI_YA" value="1" {{ isChecked($rm3d1, 'NYERI_YA') }}><label class="form-check-label" for="rm3d1_NYERI_YA">Ya</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input nyeri-option" type="checkbox" name="rm3d1_NYERI_TDK" id="rm3d1_NYERI_TDK" value="1" {{ isChecked($rm3d1, 'NYERI_TDK') }}><label class="form-check-label" for="rm3d1_NYERI_TDK">Tidak</label></div>
                        </div>
                        <div class="nyeri-details pl-4" style="display: {{ isChecked($rm3d1, 'NYERI_YA') ? 'block' : 'none' }};">
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d1_METODE_VAS" value="1" {{ isChecked($rm3d1, 'METODE_VAS') }}><label class="form-check-label">Metode VAS</label></div>
                            <div class="row mt-2">
                                <div class="col-md-3"><label class="form-label">Skor Nyeri (0-10)</label><input type="number" class="form-control skor-nyeri" name="rm3d1_SKOR_NYERI" min="0" max="10" value="{{ getValue($rm3d1, 'SKOR_NYERI') }}"></div>
                                <div class="col-md-3"><label class="form-label">Skor Kategori</label><input type="text" class="form-control kategori-nyeri" name="rm3d1_SKOR_KATEGORI" value="{{ getValue($rm3d1, 'SKOR_KATEGORI') }}" readonly></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =========================================================================================== -->
            <!--                                         TAB RM3D.2                                          -->
            <!-- =========================================================================================== -->
            <div class="tab-pane fade" id="rm3d2" role="tabpanel">
                <div class="card card-outline card-success mt-3">
                    <div class="card-header"><h3 class="card-title">KEBIDANAN</h3></div>
                    <div class="card-body">
                        <h5 class="font-weight-bold">Abdomen</h5>
                        <h6>1. Inspeksi</h6>
                        <div class="form-group">
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_INS_MEMBESAR" value="1" {{ isChecked($rm3d2, 'INS_MEMBESAR') }}><label class="form-check-label">Membesar</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_INS_MELEBAR" value="1" {{ isChecked($rm3d2, 'INS_MELEBAR') }}><label class="form-check-label">Melebar</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_INS_VENA" value="1" {{ isChecked($rm3d2, 'INS_VENA') }}><label class="form-check-label">Vena</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_INS_ALBA" value="1" {{ isChecked($rm3d2, 'INS_ALBA') }}><label class="form-check-label">Alba</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_INS_NIGRA" value="1" {{ isChecked($rm3d2, 'INS_NIGRA') }}><label class="form-check-label">Nigra</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_INS_LIVIDE" value="1" {{ isChecked($rm3d2, 'INS_LIVIDE') }}><label class="form-check-label">Livide</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_INS_STRIALEALBICAN" value="1" {{ isChecked($rm3d2, 'INS_STRIALEALBICAN') }}><label class="form-check-label">Striae Albican</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_INS_BELASOP" value="1" {{ isChecked($rm3d2, 'INS_BELASOP') }}><label class="form-check-label">Bekas Op</label></div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="rm3d2_INS_LAINLAIN_CHECK" name="rm3d2_INS_LAINLAIN" value="1" {{ isChecked($rm3d2, 'INS_LAINLAIN') }}>
                                <label class="form-check-label" for="rm3d2_INS_LAINLAIN_CHECK">Lain-lain</label>
                            </div>
                            <div id="inspeksi-lainlain-details" class="mt-2" style="display: {{ isChecked($rm3d2, 'INS_LAINLAIN') ? 'block' : 'none' }};">
                                <input type="text" class="form-control form-control-sm" name="rm3d2_INS_LAINLAIN_KET" placeholder="Sebutkan inspeksi lain..." value="{{ getValue($rm3d2, 'INS_LAINLAIN_KET') }}">
                            </div>
                        </div>
                        <hr>
                        <h6>2. Palpasi</h6>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>TFU</label><input type="text" class="form-control form-control-sm" name="rm3d2_TFU" value="{{ getValue($rm3d2, 'TFU') }}"></div>
                            <div class="col-md-6 form-group"><label>Berat Janin</label><input type="text" class="form-control form-control-sm" name="rm3d2_BERATJANIN" value="{{ getValue($rm3d2, 'BERATJANIN') }}"></div>
                            <div class="col-md-12 form-group">
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_NYERITEKAN" value="1" {{ isChecked($rm3d2, 'NYERITEKAN') }}><label class="form-check-label">Nyeri Tekan</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_OSBORN" value="1" {{ isChecked($rm3d2, 'OSBORN') }}><label class="form-check-label">Osborn</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_CEKUNGPERUT" value="1" {{ isChecked($rm3d2, 'CEKUNGPERUT') }}><label class="form-check-label">Cekung Perut</label></div>
                            </div>
                            <div class="col-md-6 form-group"><label>Leopold I</label><input type="text" class="form-control form-control-sm" name="rm3d2_LEOPOLD_1" value="{{ getValue($rm3d2, 'LEOPOLD_1') }}"></div>
                            <div class="col-md-6 form-group"><label>Leopold II</label><input type="text" class="form-control form-control-sm" name="rm3d2_LEOPOLD_2" value="{{ getValue($rm3d2, 'LEOPOLD_2') }}"></div>
                            <div class="col-md-6 form-group"><label>Leopold III</label><input type="text" class="form-control form-control-sm" name="rm3d2_LEOPOLD_3" value="{{ getValue($rm3d2, 'LEOPOLD_3') }}"></div>
                            <div class="col-md-6 form-group"><label>Leopold IV</label><input type="text" class="form-control form-control-sm" name="rm3d2_LEOPOLD_4" value="{{ getValue($rm3d2, 'LEOPOLD_4') }}"></div>
                        </div>
                        <hr>
                        <h6>3. Auskultasi</h6>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>DJJ / menit</label><input type="text" class="form-control form-control-sm" name="rm3d2_AUS_DJJ" value="{{ getValue($rm3d2, 'AUS_DJJ') }}"></div>
                            <div class="col-md-4 form-group align-self-end"><div class="form-check form-check-inline"><input class="form-check-input aus-djj-opt" type="checkbox" name="rm3d2_AUS_TERATUR" value="1" {{ isChecked($rm3d2, 'AUS_TERATUR') }}><label class="form-check-label">Teratur</label></div><div class="form-check form-check-inline"><input class="form-check-input aus-djj-opt" type="checkbox" name="rm3d2_AUS_TDK" value="1" {{ isChecked($rm3d2, 'AUS_TDK') }}><label class="form-check-label">Tidak Teratur</label></div></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4 form-group"><label>Histiap / menit</label><input type="text" class="form-control form-control-sm" name="rm3d2_HISTIAP" value="{{ getValue($rm3d2, 'HISTIAP') }}"></div>
                            <div class="col-md-4 form-group align-self-end"><div class="form-check form-check-inline"><input class="form-check-input aus-histiap-opt" type="checkbox" name="rm3d2_HISTIAP_TERATUR" value="1" {{ isChecked($rm3d2, 'HISTIAP_TERATUR') }}><label class="form-check-label">Teratur</label></div><div class="form-check form-check-inline"><input class="form-check-input aus-histiap-opt" type="checkbox" name="rm3d2_HISTIAP_TDK" value="1" {{ isChecked($rm3d2, 'HISTIAP_TDK') }}><label class="form-check-label">Tidak Teratur</label></div></div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">KANDUNGAN</h3></div>
                    <div class="card-body">
                        <div class="form-group"><label>Anogenital</label><textarea class="form-control" name="rm3d2_ANOGENITAL" rows="3">{{ getValue($rm3d2, 'ANOGENITAL') }}</textarea></div>
                        <hr>
                        <h5 class="font-weight-bold">Pengeluaran Per Vulva</h5>
                        <div class="form-group">
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_DARAH" value="1" {{ isChecked($rm3d2, 'DARAH') }}><label class="form-check-label">Darah</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_LENDIR" value="1" {{ isChecked($rm3d2, 'LENDIR') }}><label class="form-check-label">Lendir</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_AIRKETUBAN" value="1" {{ isChecked($rm3d2, 'AIRKETUBAN') }}><label class="form-check-label">Air Ketuban</label></div>
                        </div>
                        <hr>
                        <h5 class="font-weight-bold">Inspekulo</h5>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>Vagina</label><input type="text" class="form-control form-control-sm" name="rm3d2_VAGINA" value="{{ getValue($rm3d2, 'VAGINA') }}"></div>
                            <div class="col-md-4 form-group"><label>Portio</label><input type="text" class="form-control form-control-sm" name="rm3d2_PORTIO" value="{{ getValue($rm3d2, 'PORTIO') }}"></div>
                            <div class="col-md-8 form-group"><label>Vaginal Toucher</label><input type="text" class="form-control form-control-sm" name="rm3d2_VAGINAL_TOUCHER" value="{{ getValue($rm3d2, 'VAGINAL_TOUCHER') }}"></div>
                            <div class="col-md-8 form-group"><label>Kesan Panggul</label><input type="text" class="form-control form-control-sm" name="rm3d2_KESANPANGGUL" value="{{ getValue($rm3d2, 'KESANPANGGUL') }}"></div>
                            <div class="col-md-8 form-group"><label>Imbang Veto</label><input type="text" class="form-control form-control-sm" name="rm3d2_IMBANGVETO" value="{{ getValue($rm3d2, 'IMBANGVETO') }}"></div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">NIFAS</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 form-group"><label>TFU</label><input type="text" class="form-control form-control-sm" name="rm3d2_NIFAS_TFU" value="{{ getValue($rm3d2, 'NIFAS_TFU') }}"></div>
                            <div class="col-md-3 form-group"><label>Uterus</label><input type="text" class="form-control form-control-sm" name="rm3d2_NIFAS_UTERUS" value="{{ getValue($rm3d2, 'NIFAS_UTERUS') }}"></div>
                            <div class="col-md-3 form-group"><label>Lochea</label><input type="text" class="form-control form-control-sm" name="rm3d2_NIFAS_LOCHEA" value="{{ getValue($rm3d2, 'NIFAS_LOCHEA') }}"></div>
                            <div class="col-md-3 form-group"><label>Luka jalan lahir</label><input type="text" class="form-control form-control-sm" name="rm3d2_NIFAS_LUKA" value="{{ getValue($rm3d2, 'NIFAS_LUKA') }}"></div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">RIWAYAT PERSALINAN SEBELUMNYA</h3>
                        <div class="card-tools"><button type="button" class="btn btn-sm btn-success" id="btnAddRm16a1"><i class="fas fa-plus"></i> Tambah</button></div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="rm16a1Table" style="min-width: 1200px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tahun</th><th>Partus</th><th>Umur</th><th>Jenis</th><th>Penolong</th>
                                        <th>Penyulit</th><th>Nifas</th><th>BBPB</th><th>JK</th><th>Keadaan</th><th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="rm16a1Body">
                                    @foreach ($rm16a1detil as $index => $item)
                                    <tr class="rm16a1-row">
                                        <input type="hidden" name="rm16a1detil[{{ $index }}][COUNTER]" value="{{ getValue($item, 'COUNTER') }}">
                                        <td><input type="datetime-local" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][TAHUN]" value="{{ getValue($item, 'TAHUN') ? \Carbon\Carbon::parse(getValue($item, 'TAHUN'))->format('Y-m-d\TH:i') : '' }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][PARTUS]" value="{{ getValue($item, 'PARTUS') }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][UMUR]" value="{{ getValue($item, 'UMUR') }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][JENIS]" value="{{ getValue($item, 'JENIS') }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][PENOLONG]" value="{{ getValue($item, 'PENOLONG') }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][PENYULIT]" value="{{ getValue($item, 'PENYULIT') }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][NIFAS]" value="{{ getValue($item, 'NIFAS') }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][BBPB]" value="{{ getValue($item, 'BBPB') }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][JK]" value="{{ getValue($item, 'JK') }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[{{ $index }}][KEADAAN]" value="{{ getValue($item, 'KEADAAN') }}"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm btnRemoveRm16a1"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">RIWAYAT HAMIL SEKARANG</h3></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 form-group"><label>G</label><input type="text" class="form-control form-control-sm" name="rm3d2_G" value="{{ getValue($rm3d2, 'G') }}"></div>
                            <div class="col-md-2 form-group"><label>P</label><input type="text" class="form-control form-control-sm" name="rm3d2_P" value="{{ getValue($rm3d2, 'P') }}"></div>
                            <div class="col-md-2 form-group"><label>A</label><input type="text" class="form-control form-control-sm" name="rm3d2_A" value="{{ getValue($rm3d2, 'A') }}"></div>
                            <div class="col-md-2 form-group"><label>Hidup</label><input type="text" class="form-control form-control-sm" name="rm3d2_HIDUP" value="{{ getValue($rm3d2, 'HIDUP') }}"></div>
                            <div class="col-md-4 form-group"><label>HPL</label><input type="text" class="form-control form-control-sm" name="rm3d2_HPL" value="{{ getValue($rm3d2, 'HPL') }}"></div>
                        </div>
                        <hr>
                        <h5 class="font-weight-bold">Hamil Muda</h5>
                        <div class="form-group">
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_HM_MUAL" value="1" {{ isChecked($rm3d2, 'HM_MUAL') }}><label class="form-check-label">Mual</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_HM_MUNTAH" value="1" {{ isChecked($rm3d2, 'HM_MUNTAH') }}><label class="form-check-label">Muntah</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_HM_PENDARAHAN" value="1" {{ isChecked($rm3d2, 'HM_PENDARAHAN') }}><label class="form-check-label">Perdarahan</label></div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="rm3d2_HM_LAINLAIN_CHECK" name="rm3d2_HM_LAINLAIN" value="1" {{ isChecked($rm3d2, 'HM_LAINLAIN') }}>
                                <label class="form-check-label" for="rm3d2_HM_LAINLAIN_CHECK">Lain-lain</label>
                            </div>
                            <div id="hm-lainlain-details" class="mt-2" style="display: {{ isChecked($rm3d2, 'HM_LAINLAIN') ? 'block' : 'none' }};">
                                <input type="text" class="form-control form-control-sm" name="rm3d2_HM_LAINLAIN_KET" placeholder="Sebutkan keluhan lain..." value="{{ getValue($rm3d2, 'HM_LAINLAIN_KET') }}">
                            </div>
                        </div>
                        <hr>
                        <h5 class="font-weight-bold">Hamil Tua</h5>
                        <div class="form-group">
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_HT_PUSING" value="1" {{ isChecked($rm3d2, 'HT_PUSING') }}><label class="form-check-label">Pusing</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_HT_SAKITKEPALA" value="1" {{ isChecked($rm3d2, 'HT_SAKITKEPALA') }}><label class="form-check-label">Sakit Kepala</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d2_HT_PERDARAHAN" value="1" {{ isChecked($rm3d2, 'HT_PERDARAHAN') }}><label class="form-check-label">Perdarahan</label></div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="rm3d2_HT_LAINLAIN_CHECK" name="rm3d2_HT_LAINLAIN" value="1" {{ isChecked($rm3d2, 'HT_LAINLAIN') }}>
                                <label class="form-check-label" for="rm3d2_HT_LAINLAIN_CHECK">Lain-lain</label>
                            </div>
                            <div id="ht-lainlain-details" class="mt-2" style="display: {{ isChecked($rm3d2, 'HT_LAINLAIN') ? 'block' : 'none' }};">
                                <input type="text" class="form-control form-control-sm" name="rm3d2_HT_LAINLAIN_KET" placeholder="Sebutkan keluhan lain..." value="{{ getValue($rm3d2, 'HT_LAINLAIN_KET') }}">
                            </div>
                        </div>
                        <hr>
                        <h5 class="font-weight-bold">ANC</h5>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>ANC</label><input type="text" class="form-control form-control-sm" name="rm3d2_ANC" value="{{ getValue($rm3d2, 'ANC') }}"></div>
                            <div class="col-md-4 form-group align-self-end"><div class="form-check form-check-inline"><input class="form-check-input anc-opt" type="checkbox" name="rm3d2_ANC_TERAKTUR" value="1" {{ isChecked($rm3d2, 'ANC_TERAKTUR') }}><label class="form-check-label">Teratur</label></div><div class="form-check form-check-inline"><input class="form-check-input anc-opt" type="checkbox" name="rm3d2_ANC_TDK" value="1" {{ isChecked($rm3d2, 'ANC_TDK') }}><label class="form-check-label">Tidak Teratur</label></div></div>
                            <div class="col-md-4 form-group align-self-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d2_ANC_IMUNISASI" value="1" {{ isChecked($rm3d2, 'ANC_IMUNISASI') }}><label class="form-check-label">Imunisasi</label></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =========================================================================================== -->
            <!--                                         TAB RM3D.3                                          -->
            <!-- =========================================================================================== -->
            <div class="tab-pane fade" id="rm3d3" role="tabpanel">
                <div class="card card-outline card-success mt-3">
                    <div class="card-header"><h3 class="card-title">DATA PSIKOLOGIS</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_DENIAL" value="1" {{ isChecked($rm3d3, 'DENIAL') }}><label class="form-check-label">Denial</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_TIDAKSEMANGAT" value="1" {{ isChecked($rm3d3, 'TIDAKSEMANGAT') }}><label class="form-check-label">Tidak Semangat</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_TERTEKAN" value="1" {{ isChecked($rm3d3, 'TERTEKAN') }}><label class="form-check-label">Tertekan</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_MARAH" value="1" {{ isChecked($rm3d3, 'MARAH') }}><label class="form-check-label">Marah</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_SUSAHTIDUR" value="1" {{ isChecked($rm3d3, 'SUSAHTIDUR') }}><label class="form-check-label">Susah Tidur</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_BARGAINING" value="1" {{ isChecked($rm3d3, 'BARGAINING') }}><label class="form-check-label">Bargaining</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_LELAH" value="1" {{ isChecked($rm3d3, 'LELAH') }}><label class="form-check-label">Lelah</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_SUITBICARA" value="1" {{ isChecked($rm3d3, 'SUITBICARA') }}><label class="form-check-label">Sulit Bicara</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_DEPRESI" value="1" {{ isChecked($rm3d3, 'DEPRESI') }}><label class="form-check-label">Depresi</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_SULITKONSEN" value="1" {{ isChecked($rm3d3, 'SULITKONSEN') }}><label class="form-check-label">Sulit Konsentrasi</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_MERASASALAH" value="1" {{ isChecked($rm3d3, 'MERASASALAH') }}><label class="form-check-label">Merasa Salah</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_ACCEPTION" value="1" {{ isChecked($rm3d3, 'ACCEPTION') }}><label class="form-check-label">Acception</label></div>
                        </div>
                        <hr>
                        <label>Support System</label>
                        <div class="form-group">
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_SUPPORTSYSTEM" value="1" {{ isChecked($rm3d3, 'SUPPORTSYSTEM') }}><label class="form-check-label">Suami</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_ORANGTUA" value="1" {{ isChecked($rm3d3, 'ORANGTUA') }}><label class="form-check-label">Orang Tua</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_MERTUA" value="1" {{ isChecked($rm3d3, 'MERTUA') }}><label class="form-check-label">Mertua</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_ANAK" value="1" {{ isChecked($rm3d3, 'ANAK') }}><label class="form-check-label">Anak</label></div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="rm3d3_KELUARGALAIN_CHECK" name="rm3d3_KELUARGALAIN" value="1" {{ isChecked($rm3d3, 'KELUARGALAIN') }}>
                                <label class="form-check-label" for="rm3d3_KELUARGALAIN_CHECK">Keluarga Lain</label>
                            </div>
                            <div id="keluarga-lain-details" class="mt-2" style="display: {{ isChecked($rm3d3, 'KELUARGALAIN') ? 'block' : 'none' }};">
                                <input type="text" class="form-control form-control-sm" name="rm3d3_KELUARGA_KET" placeholder="Sebutkan..." value="{{ getValue($rm3d3, 'KELUARGA_KET') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">BUDAYA PASIEN</h3></div>
                    <div class="card-body">
                        <div class="form-group"><label>Kebiasaan Pasien Saat Sakit (Pola Aktivitas dan Istirahat)</label><textarea class="form-control form-control-sm" name="rm3d3_KEBIASAANPSN" rows="2">{{ getValue($rm3d3, 'KEBIASAANPSN') }}</textarea></div>
                        <hr>
                        <div class="form-group"><label>Pola Komunikasi</label>
                            <div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_NORMAL" value="1" {{ isChecked($rm3d3, 'NORMAL') }}><label class="form-check-label">Normal</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_INTROVERT" value="1" {{ isChecked($rm3d3, 'INTROVERT') }}><label class="form-check-label">Introvert</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_EKSTROVERT" value="1" {{ isChecked($rm3d3, 'EKSTROVERT') }}><label class="form-check-label">Ekstrovert</label></div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="rm3d3_KOM_LAINLAIN_CHECK" name="rm3d3_KOM_LAINLAIN" value="1" {{ isChecked($rm3d3, 'KOM_LAINLAIN') }}>
                                    <label class="form-check-label" for="rm3d3_KOM_LAINLAIN_CHECK">Lain-lain</label>
                                </div>
                                <div id="komunikasi-lainlain-details" class="mt-2" style="display: {{ isChecked($rm3d3, 'KOM_LAINLAIN') ? 'block' : 'none' }};">
                                    <input type="text" class="form-control form-control-sm" name="rm3d3_KOM_LAINLAIN_KET" placeholder="Sebutkan..." value="{{ getValue($rm3d3, 'KOM_LAINLAIN_KET') }}">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group"><label>Pola Makan</label>
                            <div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_SEHAT" value="1" {{ isChecked($rm3d3, 'SEHAT') }}><label class="form-check-label">Sehat</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_TDKSEHAT" value="1" {{ isChecked($rm3d3, 'TDKSEHAT') }}><label class="form-check-label">Tidak Sehat</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_MAKAN_NASI" value="1" {{ isChecked($rm3d3, 'MAKAN_NASI') }}><label class="form-check-label">Makan Nasi</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_SELAIN_NASI" value="1" {{ isChecked($rm3d3, 'SELAIN_NASI') }}><label class="form-check-label">Selain Nasi</label></div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group"><label>Pantang Makan</label>
                            <div>
                                <div class="form-check form-check-inline"><input class="form-check-input pantang-makan-opt" type="checkbox" id="rm3d3_PANTANG_ADA_CHECK" name="rm3d3_PANTANG_TDK2" value="1" {{ isChecked($rm3d3, 'PANTANG_TDK2') }}><label class="form-check-label" for="rm3d3_PANTANG_ADA_CHECK">Ada</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input pantang-makan-opt" type="checkbox" id="rm3d3_PANTANG_TIDAK_CHECK" name="rm3d3_PANTANG_TDK" value="1" {{ isChecked($rm3d3, 'PANTANG_TDK') }}><label class="form-check-label" for="rm3d3_PANTANG_TIDAK_CHECK">Tidak Ada</label></div>
                            </div>
                            <div id="pantang-makan-details" class="mt-2" style="display: {{ isChecked($rm3d3, 'PANTANG_TDK2') ? 'block' : 'none' }};">
                                <input type="text" class="form-control form-control-sm" name="rm3d3_PANTANG_TDK2_KET" placeholder="Sebutkan pantangan..." value="{{ getValue($rm3d3, 'PANTANG_TDK2_KET') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">KEBUTUHAN BELAJAR / EDUKASI</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Apakah Membutuhkan Edukasi?</label>
                            <div>
                                <div class="form-check form-check-inline"><input class="form-check-input edukasi-opt" type="checkbox" id="rm3d3_EDUKASI_YA_CHECK" name="rm3d3_EDUKASI_YA" value="1" {{ isChecked($rm3d3, 'EDUKASI_YA') }}><label class="form-check-label" for="rm3d3_EDUKASI_YA_CHECK">Ya</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input edukasi-opt" type="checkbox" id="rm3d3_EDUKASI_TDK_CHECK" name="rm3d3_EDUKASI_TDK" value="1" {{ isChecked($rm3d3, 'EDUKASI_TDK') }}><label class="form-check-label" for="rm3d3_EDUKASI_TDK_CHECK">Tidak</label></div>
                            </div>
                        </div>
                        <div id="edukasi-details-container" style="display: {{ isChecked($rm3d3, 'EDUKASI_YA') ? 'block' : 'none' }};">
                            <hr>
                            <div class="form-group"><label>Bahasa Sehari-hari</label>
                                <div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_BAHASA_INDO" value="1" {{ isChecked($rm3d3, 'BAHASA_INDO') }}><label class="form-check-label">Indonesia</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rm3d3_BAHASA_JAWA" value="1" {{ isChecked($rm3d3, 'BAHASA_JAWA') }}><label class="form-check-label">Jawa</label></div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="rm3d3_BAHASALAIN_CHECK" name="rm3d3_BAHASALAIN" value="1" {{ isChecked($rm3d3, 'BAHASALAIN') }}>
                                        <label class="form-check-label" for="rm3d3_BAHASALAIN_CHECK">Lain-lain</label>
                                    </div>
                                    <div id="bahasa-lain-details" class="mt-2" style="display: {{ isChecked($rm3d3, 'BAHASALAIN') ? 'block' : 'none' }};">
                                        <input type="text" class="form-control form-control-sm" name="rm3d3_BAHASALAIN_KET" placeholder="Sebutkan bahasa..." value="{{ getValue($rm3d3, 'BAHASALAIN_KET') }}">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group"><label>Materi Edukasi</label>
                                <div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d3_ORIENTASIRUANG" value="1" {{ isChecked($rm3d3, 'ORIENTASIRUANG') }}><label class="form-check-label">Orientasi Ruang</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d3_TIMPERAWATAN" value="1" {{ isChecked($rm3d3, 'TIMPERAWATAN') }}><label class="form-check-label">Tim Yang Memberi Perawatan, Pengobatan, dan Konsultasi</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d3_PENGERTIANPENY" value="1" {{ isChecked($rm3d3, 'PENGERTIANPENY') }}><label class="form-check-label">Pengertian Tentang Diagnosis Penyakit, Penyebab Penyakit, Tanda dan Gejala Penyakit</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d3_OBATDIDAPAT" value="1" {{ isChecked($rm3d3, 'OBATDIDAPAT') }}><label class="form-check-label">Obat - Obatan Yang Didapat</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d3_PENGGUALAT" value="1" {{ isChecked($rm3d3, 'PENGGUALAT') }}><label class="form-check-label">Penggunaan Peralatan Medis Yang Aman dan Efektif Program</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d3_DIETNUTRISI" value="1" {{ isChecked($rm3d3, 'DIETNUTRISI') }}><label class="form-check-label">Diet dan Nutrisi</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d3_PROGRAMREHAT" value="1" {{ isChecked($rm3d3, 'PROGRAMREHAT') }}><label class="form-check-label">Program Rehabilitasi Medik, Fisioterapi/Terapi Wicara</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="rm3d3_MANAJEMENNYERI" value="1" {{ isChecked($rm3d3, 'MANAJEMENNYERI') }}><label class="form-check-label">Manajemen Nyeri</label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success">
                    <div class="card-header"><h3 class="card-title">DIAGNOSA, RESIKO, RENCANA, TINDAKAN</h3></div>
                    <div class="card-body">
                        <div class="form-group"><label>Diagnosa Kebidanan</label><textarea class="form-control" name="rm3d3_DIAGNOSAKEBIDANAN" rows="3">{{ getValue($rm3d3, 'DIAGNOSAKEBIDANAN') }}</textarea></div>
                        <div class="form-group"><label>Resiko Diagnosa</label><textarea class="form-control" name="rm3d3_RESIKODIAGNOSA" rows="3">{{ getValue($rm3d3, 'RESIKODIAGNOSA') }}</textarea></div>
                        <div class="form-group"><label>Rencana Kebutuhan</label><textarea class="form-control" name="rm3d3_RENCANAKEBUTUHAN" rows="3">{{ getValue($rm3d3, 'RENCANAKEBUTUHAN') }}</textarea></div>
                        <div class="form-group"><label>Tindakan</label><textarea class="form-control" name="rm3d3_TINDAKAN" rows="3">{{ getValue($rm3d3, 'TINDAKAN') }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-4 text-left">
            <button type="submit" name="submit_rm3d" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Semua Data</button>
            <button type="button" class="btn btn-outline-danger" id="btn-reset-rm3d"><i class="fas fa-times-circle mr-1"></i> Batal</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // --- Konfigurasi AJAX untuk menyertakan CSRF token ---
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // --- Logika Penilaian Nyeri ---
    function handleNyeriCheck() {
        if ($('#rm3d1_NYERI_YA').is(':checked')) {
            $('.nyeri-details').slideDown();
        } else {
            $('.nyeri-details').slideUp();
            $('.nyeri-details').find('input[type="checkbox"]').prop('checked', false);
            $('.nyeri-details').find('input[type="number"], input[type="text"]').val('');
        }
    }

    $('.nyeri-option').on('change', function() {
        if ($(this).is(':checked')) {
            $('.nyeri-option').not(this).prop('checked', false);
        }
        handleNyeriCheck();
    });

    $('.skor-nyeri').on('input', function() {
        var skor = parseInt($(this).val()) || 0;
        var kategori = '';
        if (skor >= 1 && skor <= 4) {
            kategori = 'Ringan';
        } else if (skor >= 5 && skor <= 7) {
            kategori = 'Sedang';
        } else if (skor >= 8 && skor <= 10) {
            kategori = 'Berat';
        }
        $('.kategori-nyeri').val(kategori);
    });
    handleNyeriCheck();
    $('.skor-nyeri').trigger('input');

    // --- Logika Riwayat Persalinan (RM16A1DETIL) ---
    // ... (kode ini tidak berubah)

    // --- Logika Dinamis untuk Bagian Rujukan ---
    function handleRujukanDetails() {
        if ($('#rm3d1_RUJUKAN_CHECK').is(':checked')) {
            $('#rujukan-details-container').slideDown();
        } else {
            $('#rujukan-details-container').slideUp(function() {
                // Saat disembunyikan, reset semua isian di dalamnya
                $(this).find('.rujukan-source-check').prop('checked', false);
                $(this).find('.rujukan-keterangan').val('').hide();
            });
        }
    }

    function handleRujukanKeterangan(checkbox) {
        const keteranganInput = $(checkbox).closest('.rujukan-item').find('.rujukan-keterangan');
        if ($(checkbox).is(':checked')) {
            keteranganInput.slideDown();
        } else {
            keteranganInput.slideUp(function() {
                $(this).val(''); // Hapus nilai setelah animasi selesai
            });
        }
    }

    // Event listener untuk checkbox utama "Rujukan dari:"
    $('#rm3d1_RUJUKAN_CHECK').on('change', handleRujukanDetails);

    // Event listener untuk setiap checkbox sumber rujukan
    $('.rujukan-source-check').on('change', function() {
        handleRujukanKeterangan(this);
    });

    // Inisialisasi saat form dimuat
    $('.rujukan-source-check').each(function() {
        $(this).closest('.rujukan-item').find('.rujukan-keterangan').toggle($(this).is(':checked'));
    });

    $('#btnAddRm16a1').on('click', function() {
        let newIndex = $('#rm16a1Table tbody tr').length;
        let maxCounter = 0;
        $('input[name^="rm16a1detil["][name$="][COUNTER]"]').each(function() {
            let currentVal = parseInt($(this).val());
            if (currentVal > maxCounter) maxCounter = currentVal;
        });
        let newCounter = maxCounter + 1;
        const now = new Date().toISOString().slice(0, 16);

        const newRow = `
            <tr class="rm16a1-row">
                <input type="hidden" name="rm16a1detil[${newIndex}][COUNTER]" value="${newCounter}">
                <td><input type="datetime-local" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][TAHUN]" value="${now}"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][PARTUS]"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][UMUR]"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][JENIS]"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][PENOLONG]"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][PENYULIT]"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][NIFAS]"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][BBPB]"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][JK]"></td>
                <td><input type="text" class="form-control form-control-sm" name="rm16a1detil[${newIndex}][KEADAAN]"></td>
                <td><button type="button" class="btn btn-danger btn-sm btnRemoveRm16a1"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        $('#rm16a1Body').append(newRow);
    });

    $('#rm16a1Table').on('click', '.btnRemoveRm16a1', function() {
        $(this).closest('tr').remove();
    });

    // --- Logika Dinamis untuk Bagian Pernah Dirawat ---
    function handleDirawatToggle() {
        if ($('#rm3d1_DIRAWAT_YA_CHECK').is(':checked')) {
            $('#dirawat-details-container').slideDown();
            $('#rm3d1_DIRAWAT_TDK_CHECK').prop('checked', false);
        } else {
            $('#dirawat-details-container').slideUp(function() {
                $(this).find('input[type="text"]').val(''); // Clear inputs
            });
            // Jika 'Tidak' dicentang, pastikan 'Ya' tidak dicentang
            if ($('#rm3d1_DIRAWAT_TDK_CHECK').is(':checked')) {
                $('#rm3d1_DIRAWAT_YA_CHECK').prop('checked', false);
            }
        }
    }

    $('#rm3d1_DIRAWAT_YA_CHECK').on('change', function() {
        if ($(this).is(':checked')) {
            $('#rm3d1_DIRAWAT_TDK_CHECK').prop('checked', false);
        }
        handleDirawatToggle();
    });
    $('#rm3d1_DIRAWAT_TDK_CHECK').on('change', function() {
        if ($(this).is(':checked')) {
            $('#rm3d1_DIRAWAT_YA_CHECK').prop('checked', false);
        }
        handleDirawatToggle();
    });
    handleDirawatToggle(); // Inisialisasi saat form dimuat

    // --- Logika Dinamis untuk Bagian Pernah Operasi ---
    function handleOperasiToggle() {
        if ($('#rm3d1_OPERASI_YA_CHECK').is(':checked')) {
            $('#operasi-details-container').slideDown();
            $('#rm3d1_OPERASI_TIDAK_CHECK').prop('checked', false);
        } else {
            $('#operasi-details-container').slideUp(function() {
                $(this).find('input[type="text"]').val(''); // Clear inputs
            });
            if ($('#rm3d1_OPERASI_TIDAK_CHECK').is(':checked')) {
                $('#rm3d1_OPERASI_YA_CHECK').prop('checked', false);
            }
        }
    }

    $('#rm3d1_OPERASI_YA_CHECK').on('change', function() { handleOperasiToggle(); });
    $('#rm3d1_OPERASI_TIDAK_CHECK').on('change', function() { handleOperasiToggle(); });
    handleOperasiToggle(); // Inisialisasi saat form dimuat

    // --- Logika Dinamis untuk Bagian Rokok ---
    function handleRokokToggle() {
        if ($('#rm3d1_MEROKOK_YA_CHECK').is(':checked')) {
            $('#merokok-details-container').slideDown();
            $('#rm3d1_MEROKOK_TIDAK_CHECK').prop('checked', false);
        } else {
            $('#merokok-details-container').slideUp(function() {
                $(this).find('input[type="text"]').val(''); // Clear inputs
            });
            if ($('#rm3d1_MEROKOK_TIDAK_CHECK').is(':checked')) {
                $('#rm3d1_MEROKOK_YA_CHECK').prop('checked', false);
            }
        }
    }
    $('#rm3d1_MEROKOK_YA_CHECK').on('change', function() {
        if ($(this).is(':checked')) { $('#rm3d1_MEROKOK_TIDAK_CHECK').prop('checked', false); }
        handleRokokToggle();
    });
    $('#rm3d1_MEROKOK_TIDAK_CHECK').on('change', function() {
        if ($(this).is(':checked')) { $('#rm3d1_MEROKOK_YA_CHECK').prop('checked', false); }
        handleRokokToggle();
    });
    handleRokokToggle(); // Inisialisasi saat form dimuat

    // --- Logika Dinamis untuk Bagian Obat Tidur ---
    function handleObatTidurToggle() {
        if ($('#rm3d1_OBATIDUR_YA_CHECK').is(':checked')) {
            $('#rm3d1_OBATIDUR_TIDAK_CHECK').prop('checked', false);
        } else if ($('#rm3d1_OBATIDUR_TIDAK_CHECK').is(':checked')) {
            $('#rm3d1_OBATIDUR_YA_CHECK').prop('checked', false);
        }
    }
    $('#rm3d1_OBATIDUR_YA_CHECK').on('change', handleObatTidurToggle);
    $('#rm3d1_OBATIDUR_TIDAK_CHECK').on('change', handleObatTidurToggle);
    handleObatTidurToggle(); // Inisialisasi saat form dimuat

    // --- Logika Dinamis untuk Bagian Olahraga ---
    function handleOlahragaToggle() {
        if ($('#rm3d1_OLAHRAGA_YA_CHECK').is(':checked')) {
            $('#rm3d1_OLAHRAGA_TDK_CHECK').prop('checked', false);
        } else if ($('#rm3d1_OLAHRAGA_TDK_CHECK').is(':checked')) {
            $('#rm3d1_OLAHRAGA_YA_CHECK').prop('checked', false);
        }
    }
    $('#rm3d1_OLAHRAGA_YA_CHECK').on('change', handleOlahragaToggle);
    $('#rm3d1_OLAHRAGA_TDK_CHECK').on('change', handleOlahragaToggle);
    handleOlahragaToggle(); // Inisialisasi saat form dimuat

    // --- Logika Dinamis untuk Bagian Alkohol ---
    function handleAlkoholToggle() {
        if ($('#rm3d1_ALKOHOL_YA_CHECK').is(':checked')) {
            $('#alkohol-details-container').slideDown();
            $('#rm3d1_ALKOHOL_TDK_CHECK').prop('checked', false);
        } else {
            $('#alkohol-details-container').slideUp(function() {
                $(this).find('input[type="text"]').val(''); // Clear inputs
            });
            if ($('#rm3d1_ALKOHOL_TDK_CHECK').is(':checked')) {
                $('#rm3d1_ALKOHOL_YA_CHECK').prop('checked', false);
            }
        }
    }
    $('#rm3d1_ALKOHOL_YA_CHECK').on('change', function() {
        if ($(this).is(':checked')) { $('#rm3d1_ALKOHOL_TDK_CHECK').prop('checked', false); }
        handleAlkoholToggle();
    });
    $('#rm3d1_ALKOHOL_TDK_CHECK').on('change', function() {
        if ($(this).is(':checked')) { $('#rm3d1_ALKOHOL_YA_CHECK').prop('checked', false); }
        handleAlkoholToggle();
    });
    handleAlkoholToggle(); // Inisialisasi saat form dimuat

    // --- Logika Dinamis untuk Riwayat Menstruasi ---
    function handleMenstruasiToggle(checkboxId, inputId) {
        const checkbox = $(checkboxId);
        const input = $(inputId);
        if (checkbox.is(':checked')) {
            input.slideDown();
        } else {
            input.slideUp(function() {
                $(this).val('');
            });
        }
    }

    // Event listener untuk Umur Menarche
    $('#rm3d1_UMRMENARRCHE_CHECK').on('change', () => handleMenstruasiToggle('#rm3d1_UMRMENARRCHE_CHECK', '#rm3d1_UMRMENARRCHE_LAMA'));
    handleMenstruasiToggle('#rm3d1_UMRMENARRCHE_CHECK', '#rm3d1_UMRMENARRCHE_LAMA'); // Inisialisasi

    // Event listener untuk HPHT
    $('#rm3d1_HPHT_CHECK').on('change', () => handleMenstruasiToggle('#rm3d1_HPHT_CHECK', '#rm3d1_HPHT_KET'));
    handleMenstruasiToggle('#rm3d1_HPHT_CHECK', '#rm3d1_HPHT_KET'); // Inisialisasi

    // --- Logika Dinamis untuk Bio & Spiritual ---
    function handleExclusiveCheck(className) {
        $(`.${className}`).on('change', function() {
            if ($(this).is(':checked')) {
                $(`.${className}`).not(this).prop('checked', false);
            }
        });
        // Inisialisasi
        const checked = $(`.${className}:checked`);
        if (checked.length > 1) {
            checked.not(checked.first()).prop('checked', false);
        }
    }

    handleExclusiveCheck('bio-option');
    handleExclusiveCheck('spirit-option');

    // --- Logika Dinamis untuk Riwayat Perkawinan ---
    function handlePerkawinanStatus(className) {
        $(`.${className}`).on('change', function() {
            if ($(this).is(':checked')) {
                $(`.${className}`).not(this).prop('checked', false);
            }
        });
    }
    handlePerkawinanStatus('perkawinan-status-1');
    handlePerkawinanStatus('perkawinan-status-2');
    handlePerkawinanStatus('perkawinan-status-3');
    $('.perkawinan-status-1, .perkawinan-status-2, .perkawinan-status-3').trigger('change'); // Inisialisasi

    // --- Logika Dinamis untuk Riwayat KB ---
    function handleKbDetails() {
        const anyKbChecked = $('.jenis-kb-check:checked').length > 0;
        if (anyKbChecked) {
            $('.kb-details-row').slideDown();
        } else {
            $('.kb-details-row').slideUp(function() {
                $(this).find('input').val(''); // Kosongkan input saat disembunyikan
            });
        }
    }

    function handleKbLainlain() {
        if ($('#rm3d1_LAINLAIN_CHECK').is(':checked')) {
            $('#kb-lainlain-details').slideDown();
        } else {
            $('#kb-lainlain-details').slideUp(function() {
                $(this).find('input').val('');
            });
        }
    }

    // Event listener untuk semua checkbox jenis KB
    $('.jenis-kb-check').on('change', handleKbDetails);
    // Event listener khusus untuk checkbox "Lain-lain"
    $('#rm3d1_LAINLAIN_CHECK').on('change', handleKbLainlain);

    // Inisialisasi saat form dimuat
    handleKbDetails();
    handleKbLainlain();

    // --- Logika Dinamis untuk Riwayat Penyakit, Terapi, dan Alergi ---
    function handleDynamicInput(checkboxId, containerId) {
        const checkbox = $(checkboxId);
        const container = $(containerId);
        
        function toggle() {
            if (checkbox.is(':checked')) {
                container.slideDown();
            } else {
                container.slideUp(function() {
                    $(this).find('input').val('');
                });
            }
        }
        
        checkbox.on('change', toggle);
        toggle(); // Inisialisasi
    }

    handleDynamicInput('#rm3d1_RIW_LAINLAIN_CHECK', '#riwayat-lainlain-details');
    handleDynamicInput('#rm3d1_TERAPI_LAINLAIN_CHECK', '#terapi-lainlain-details');

    // Logika khusus untuk Alergi (Ya/Tidak)
    function handleAlergiToggle() {
        if ($('#rm3d1_RA_YA_CHECK').is(':checked')) {
            $('#alergi-details-container').slideDown();
            $('#rm3d1_RA_TIDAK_CHECK').prop('checked', false);
        } else {
            $('#alergi-details-container').slideUp(function() {
                $(this).find('input').val('');
            });
        }
    }

    $('#rm3d1_RA_YA_CHECK').on('change', function() {
        if ($(this).is(':checked')) {
            $('#rm3d1_RA_TIDAK_CHECK').prop('checked', false);
        }
        handleAlergiToggle();
    });

    $('#rm3d1_RA_TIDAK_CHECK').on('change', function() {
        if ($(this).is(':checked')) {
            $('#rm3d1_RA_YA_CHECK').prop('checked', false);
        }
        handleAlergiToggle();
    });
    handleAlergiToggle(); // Inisialisasi

    // --- Logika Dinamis untuk RM3D.2 ---
    handleDynamicInput('#rm3d2_INS_LAINLAIN_CHECK', '#inspeksi-lainlain-details');
    handleDynamicInput('#rm3d2_HM_LAINLAIN_CHECK', '#hm-lainlain-details');
    handleDynamicInput('#rm3d2_HT_LAINLAIN_CHECK', '#ht-lainlain-details');

    handleExclusiveCheck('aus-djj-opt');
    handleExclusiveCheck('aus-histiap-opt');
    handleExclusiveCheck('anc-opt');

    // --- Logika Dinamis untuk RM3D.3 ---
    handleDynamicInput('#rm3d3_KELUARGALAIN_CHECK', '#keluarga-lain-details');
    handleDynamicInput('#rm3d3_KOM_LAINLAIN_CHECK', '#komunikasi-lainlain-details');
    handleDynamicInput('#rm3d3_PANTANG_ADA_CHECK', '#pantang-makan-details');
    handleDynamicInput('#rm3d3_BAHASALAIN_CHECK', '#bahasa-lain-details');
    handleDynamicInput('#rm3d3_EDUKASI_YA_CHECK', '#edukasi-details-container');

    handleExclusiveCheck('pantang-makan-opt');
    handleExclusiveCheck('edukasi-opt');


    // --- Logika Tombol Batal ---
    $('#btn-reset-rm3d').on('click', function() {
        Swal.fire({
            title: 'Yakin ingin batal?',
            text: "Semua perubahan akan dibatalkan dan form akan di-reset.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form-selector').trigger('change'); // Reload the form
            }
        });
    });

    // --- AJAX Form Submission ---
    $('#rm3dForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        
        var data = form.serialize(); // Cukup gunakan serialize(), Laravel akan menangani nilai checkbox
        var button = form.find('button[type="submit"]');
        var originalButtonText = button.html();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => {
                        $('#form-selector').trigger('change');
                    });
                } else {
                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            },
            complete: function() {
                button.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script>