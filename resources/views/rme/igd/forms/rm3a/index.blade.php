@php
    // Helper function to get value from existing data or provide a default
    function getValue($data, $key, $default = '') {
        // For checkbox, check if value is 1 or true, and return 'checked' string
        if (is_object($data) && property_exists($data, $key) && in_array($data->$key, [1, '1', true], true) && strpos($key, '_SKOR') === false && strpos($key, 'WORT_') !== false) {
            return 'checked';
        }
        // For text/date/time, return the value if it exists and is not null
        if (is_object($data) && property_exists($data, $key) && $data->$key !== null) {
            // Format date and time
            if (in_array($key, ['TGL_TRIAGE', 'TGLKEJADIAN']) && !empty($data->$key)) {
                return \Carbon\Carbon::parse($data->$key)->format('Y-m-d');
            }
            if (in_array($key, ['JAM_TRIAGE', 'JAMKEJADIAN']) && !empty($data->$key)) {
                return \Carbon\Carbon::parse($data->$key)->format('H:i');
            }
            return trim($data->$key);
        }
        // Return default if no data
        return $default;
    }

    $isUpdate = !empty($rm3a);
    $submitButtonText = $isUpdate ? "Update" : "Simpan";

    // Data Pasien untuk JS
    $patientAge = $patient->UMUR ?? 0;
    $patientGender = $patient->GENDER ?? 'L';
@endphp

<form id="form-rm3a-submit" action="{{ route('rme.igd.form.rm3a.store') }}" method="POST">
    @csrf
    <div id="form-rm3a">
        <div class="card card-danger">
            <div class="card-header"><h5 class="mb-0">TRIAGE</h5></div>
            <div class="card-body">
            {{-- Hidden Inputs --}}
            <input type="hidden" name="NORM" value="{{ $norm }}">
            <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="TGL_TRIAGE">Tanggal Triage</label>
                            <input type="date" class="form-control" id="TGL_TRIAGE" name="TGL_TRIAGE" value="{{ getValue($rm3a, 'TGL_TRIAGE', now()->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="JAM_TRIAGE">Jam Triage</label>
                            <input type="time" class="form-control" id="JAM_TRIAGE" name="JAM_TRIAGE" value="{{ getValue($rm3a, 'JAM_TRIAGE', now()->format('H:i')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="PETUGAS">Petugas</label>
                            <input type="text" class="form-control" id="PETUGAS" name="PETUGAS" value="{{ getValue($rm3a, 'PETUGAS', $user['username'] ?? ($user['namapemeriksa'] ?? '')) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="KELUHAN">Keluhan</label>
                            <textarea class="form-control" id="KELUHAN" name="KELUHAN" maxlength="250" style="height: 100px;">{{ getValue($rm3a, 'KELUHAN') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COVID --}}
        <input type="hidden" id="COV_USIA_KAJI" name="COV_USIA_KAJI" class="form-control" value="{{ getValue($rm3a, 'COV_USIA_KAJI', '>= 44 Tahun') }}" readonly>
        <input type="hidden" id="COV_MAKS_KAJI" name="COV_MAKS_KAJI" class="form-control" value="{{ getValue($rm3a, 'COV_MAKS_KAJI', '>= 37,8 C(100 F)') }}" readonly>
        <input type="hidden" id="COV_JK_KAJI" name="COV_JK_KAJI" class="form-control" value="{{ getValue($rm3a, 'COV_JK_KAJI', 'Perempuan') }}" readonly>
        <input type="hidden" id="COV_GEJA_KAJI" name="COV_GEJA_KAJI" class="form-control" value="{{ getValue($rm3a, 'COV_GEJA_KAJI', '>= 1 gejala') }}" readonly>
        <input type="hidden" id="COV_RASIO_KAJI" name="COV_RASIO_KAJI" class="form-control" value="{{ getValue($rm3a, 'COV_RASIO_KAJI', '>= 5,8') }}" readonly>
        <input type="hidden" id="COV_USIA_SKOR" name="COV_USIA_SKOR" class="form-control" value="{{ getValue($rm3a, 'COV_USIA_SKOR', '1') }}" readonly>
        <input type="hidden" id="COV_MAKS_SKOR" name="COV_MAKS_SKOR" class="form-control" value="{{ getValue($rm3a, 'COV_MAKS_SKOR', '1') }}" readonly>
        <input type="hidden" id="COV_JK_SKOR" name="COV_JK_SKOR" class="form-control" value="{{ getValue($rm3a, 'COV_JK_SKOR', '1') }}" readonly>
        <input type="hidden" id="COV_GEJA_SKOR" name="COV_GEJA_SKOR" class="form-control" value="{{ getValue($rm3a, 'COV_GEJA_SKOR', '1') }}" readonly>
        <input type="hidden" id="COV_RASIO_SKOR" name="COV_RASIO_SKOR" class="form-control" value="{{ getValue($rm3a, 'COV_RASIO_SKOR', '1') }}" readonly>
        <input type="hidden" id="COV_RASIO_KAJI" name="COV_RASIO_KAJI" class="form-control" value="{{ is_object($rm3a) && property_exists($rm3a, 'COV_RASIO_KAJI') ? $rm3a->COV_RASIO_KAJI : '>= 5,8' }}" readonly>
        <input type="hidden" id="COV_USIA_SKOR" name="COV_USIA_SKOR" class="form-control" value="{{ is_object($rm3a) && property_exists($rm3a, 'COV_USIA_SKOR') ? $rm3a->COV_USIA_SKOR : '1' }}" readonly>
        <input type="hidden" id="COV_MAKS_SKOR" name="COV_MAKS_SKOR" class="form-control" value="{{ is_object($rm3a) && property_exists($rm3a, 'COV_MAKS_SKOR') ? $rm3a->COV_MAKS_SKOR : '1' }}" readonly>
        <input type="hidden" id="COV_JK_SKOR" name="COV_JK_SKOR" class="form-control" value="{{ is_object($rm3a) && property_exists($rm3a, 'COV_JK_SKOR') ? $rm3a->COV_JK_SKOR : '1' }}" readonly>
        <input type="hidden" id="COV_GEJA_SKOR" name="COV_GEJA_SKOR" class="form-control" value="{{ is_object($rm3a) && property_exists($rm3a, 'COV_GEJA_SKOR') ? $rm3a->COV_GEJA_SKOR : '1' }}" readonly>
        <input type="hidden" id="COV_RASIO_SKOR" name="COV_RASIO_SKOR" class="form-control" value="{{ is_object($rm3a) && property_exists($rm3a, 'COV_RASIO_SKOR') ? $rm3a->COV_RASIO_SKOR : '1' }}" readonly>

        <div class="card card-danger">
            <div class="card-header"><h5 class="mb-0">TANDA-TANDA VITAL</h5></div>
            <div class="card-body">
                <div class="row">
                    {{-- Kesadaran --}}
                    <div class="col-lg-3 col-md-4 mb-3 mb-lg-0">
                        <div class="form-group h-100 p-3 bg-light rounded border">
                            <h6 class="font-weight-bold mb-2">Kesadaran:</h6>
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="PACS2_SADAR" name="PACS2_SADAR" value="1" {{ getValue($rm3a, 'PACS2_SADAR') }}><label class="form-check-label" for="PACS2_SADAR">Kesadaran Penuh</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="PACS3_RESPON" name="PACS3_RESPON" value="1" {{ getValue($rm3a, 'PACS3_RESPON') }}><label class="form-check-label" for="PACS3_RESPON">Respons Suara</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" id="PACS4_RESPON" name="PACS4_RESPON" value="1" {{ getValue($rm3a, 'PACS4_RESPON') }}><label class="form-check-label" for="PACS4_RESPON">Respons Nyeri</label></div>
                            <hr>
                            <div class="form-check text-danger"><input class="form-check-input" type="checkbox" id="PACS1_MENINGGAL" name="PACS1_MENINGGAL" value="1" {{ getValue($rm3a, 'PACS1_MENINGGAL') }}><label class="form-check-label font-weight-bold" for="PACS1_MENINGGAL">Meninggal</label></div>
                        </div>
                    </div>

                    {{-- Vital Signs & Lainnya --}}
                    <div class="col-lg-9 col-md-8">
                        <div class="row">
                            {{-- Tekanan Darah --}}
                            <div class="col-lg-4 col-md-12">
                                <div class="form-group mb-3">
                                    <label for="PACS1_TD_SIS">Tekanan Darah</label>
                                    <div class="input-group">
                                        <input type="text" id="PACS1_TD_SIS" name="PACS1_TD_SIS" class="form-control text-center" value="{{ getValue($rm3a, 'PACS1_TD_SIS') }}" maxlength="3" placeholder="SYS">
                                        <span class="input-group-text">/</span>
                                        <input type="text" id="PACS1_TD_DIA" name="PACS1_TD_DIA" class="form-control text-center" value="{{ getValue($rm3a, 'PACS1_TD_DIA') }}" maxlength="3" placeholder="DIA">
                                        <span class="input-group-text">mmHg</span>
                                    </div>
                                </div>
                            </div>
                            {{-- TTV Lainnya --}}
                            <div class="col-lg-4 col-md-6"><div class="form-group mb-3"><label for="PACS1_NADI">Nadi</label><div class="input-group"><input type="text" id="PACS1_NADI" name="PACS1_NADI" class="form-control" value="{{ getValue($rm3a, 'PACS1_NADI') }}" maxlength="3" placeholder="..."><span class="input-group-text">x/mnt</span></div></div></div>
                            <div class="col-lg-4 col-md-6"><div class="form-group mb-3"><label for="PACS2_NAFAS">Pernafasan</label><div class="input-group"><input type="text" id="PACS2_NAFAS" name="PACS2_NAFAS" class="form-control" value="{{ getValue($rm3a, 'PACS2_NAFAS') }}" maxlength="3" placeholder="..."><span class="input-group-text">x/mnt</span></div></div></div>
                            <div class="col-lg-4 col-md-6"><div class="form-group mb-3"><label for="PACS2_TEMP">Suhu</label><div class="input-group"><input type="text" id="PACS2_TEMP" name="PACS2_TEMP" class="form-control decimal-input" value="{{ getValue($rm3a, 'PACS2_TEMP') }}" maxlength="4" placeholder="..."><span class="input-group-text">°C</span></div></div></div>
                            <div class="col-lg-4 col-md-6"><div class="form-group mb-3"><label for="PACS3_SATURASI">Saturasi O₂</label><div class="input-group"><input type="text" id="PACS3_SATURASI" name="PACS3_SATURASI" class="form-control" value="{{ getValue($rm3a, 'PACS3_SATURASI') }}" maxlength="3" placeholder="..."><span class="input-group-text">%</span></div></div></div>
                            <div class="col-lg-4 col-md-6"><div class="form-group mb-3"><label for="PACS3_NYERI">Nyeri (VAS)</label><div class="input-group"><input type="text" id="PACS3_NYERI" name="PACS3_NYERI" class="form-control" value="{{ getValue($rm3a, 'PACS3_NYERI') }}" maxlength="2" placeholder="..."><span class="input-group-text">Skor</span></div></div></div>
                            {{-- Antropometri --}}
                            <div class="col-lg-4 col-md-6"><div class="form-group mb-3"><label for="PACS4_BB">Berat Badan</label><div class="input-group"><input type="text" id="PACS4_BB" name="PACS4_BB" class="form-control decimal-input" value="{{ getValue($rm3a, 'PACS4_BB') }}" maxlength="5" placeholder="..."><span class="input-group-text">Kg</span></div></div></div>
                            <div class="col-lg-4 col-md-6"><div class="form-group mb-3"><label for="PACS4_TB">Tinggi Badan</label><div class="input-group"><input type="text" id="PACS4_TB" name="PACS4_TB" class="form-control decimal-input" value="{{ getValue($rm3a, 'PACS4_TB') }}" maxlength="5" placeholder="..."><span class="input-group-text">cm</span></div></div></div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="BMI">BMI</label>
                                    <div class="input-group">
                                        <input type="text" id="BMI" name="BMI" class="form-control" value="{{ getValue($rm3a, 'BMI') }}" placeholder="..." readonly>
                                    <span class="input-group-text" id="BMI_Category" style="min-width: 60px;">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-danger">
            <div class="card-header"><h5 class="card-title">WORTHING PSYCHOLOGICAL SCORING SYSTEM (WPSS)</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr><th>Tanda Vital</th><th>Skor 0</th><th>Skor 1</th><th>Skor 2</th><th>Skor 3</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Kesadaran</td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR0_SADAR" name="WORT_SKOR0_SADAR" value="1" {{ getValue($rm3a, 'WORT_SKOR0_SADAR') }}> <label>Sadar Penuh</label></div></td>
                                <td></td><td></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR3_SELAIN" name="WORT_SKOR3_SELAIN" value="1" {{ getValue($rm3a, 'WORT_SKOR3_SELAIN') }}> <label>Selain Sadar Penuh</label></div></td>
                            </tr>
                            <tr>
                                <td>Tekanan Darah Sistolik</td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR0_100" name="WORT_SKOR0_100" value="1" {{ getValue($rm3a, 'WORT_SKOR0_100') }}> <label>≥</label> 100</div></td>
                                <td></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR2_99" name="WORT_SKOR2_99" value="1" {{ getValue($rm3a, 'WORT_SKOR2_99') }}> <label>≤</label> 99</div></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Nadi</td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR0_101" name="WORT_SKOR0_101" value="1" {{ getValue($rm3a, 'WORT_SKOR0_101') }}> <label>≤</label> 101</div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR1_102" name="WORT_SKOR1_102" value="1" {{ getValue($rm3a, 'WORT_SKOR1_102') }}> <label>≥</label> 102</div></td>
                                <td></td><td></td>
                            </tr>
                            <tr>
                                <td>Respirasi</td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR0_19" name="WORT_SKOR0_19" value="1" {{ getValue($rm3a, 'WORT_SKOR0_19') }}> <label>≤</label> 19</div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR1_20" name="WORT_SKOR1_20" value="1" {{ getValue($rm3a, 'WORT_SKOR1_20') }}> <label>20 - 21</label></div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR2_22" name="WORT_SKOR2_22" value="1" {{ getValue($rm3a, 'WORT_SKOR2_22') }}> <label>≥</label> 22</div></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Temperatur</td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR0_35" name="WORT_SKOR0_35" value="1" {{ getValue($rm3a, 'WORT_SKOR0_35') }}> <label>≥</label> 35.3</div></td>
                                <td></td><td></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR3_35" name="WORT_SKOR3_35" value="1" {{ getValue($rm3a, 'WORT_SKOR3_35') }}> <label><</label> 35.3</div></td>
                            </tr>
                            <tr>
                                <td>Saturasi O2</td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR0_96" name="WORT_SKOR0_96" value="1" {{ getValue($rm3a, 'WORT_SKOR0_96') }}> <label>96 - 100</label></div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR1_94" name="WORT_SKOR1_94" value="1" {{ getValue($rm3a, 'WORT_SKOR1_94') }}> <label>94 - 95</label></div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR2_92" name="WORT_SKOR2_92" value="1" {{ getValue($rm3a, 'WORT_SKOR2_92') }}> <label>92 - 93</label></div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR3_92" name="WORT_SKOR3_92" value="1" {{ getValue($rm3a, 'WORT_SKOR3_92') }}> <label><</label> 92</div></td>
                            </tr>
                            <tr class="font-weight-bold">
                                <td>KEPUTUSAN <span id="totalScoreDisplay" class="badge bg-info ml-2"></span></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR0_TOT" name="WORT_SKOR0_TOT" value="1" {{ getValue($rm3a, 'WORT_SKOR0_TOT') }}> <label class="form-check-label text-danger font-weight-bold">RESUSITASI</label></div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR1_TOT" name="WORT_SKOR1_TOT" value="1" {{ getValue($rm3a, 'WORT_SKOR1_TOT') }}> <label class="form-check-label text-warning font-weight-bold">NON RESUSITASI</label></div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR3_TOT" name="WORT_SKOR3_TOT" value="1" {{ getValue($rm3a, 'WORT_SKOR3_TOT') }}> <label class="form-check-label text-success font-weight-bold">OBSERVASI</label></div></td>
                                <td><div class="form-check d-inline-block"><input class="form-check-input" type="checkbox" id="WORT_SKOR2_TOT" name="WORT_SKOR2_TOT" value="1" {{ getValue($rm3a, 'WORT_SKOR2_TOT') }}> <label class="form-check-label text-dark font-weight-bold">DOA</label></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group mt-3">
                    <label for="CAT_KHUSUS">Catatan Khusus</label>
                    <textarea class="form-control" id="CAT_KHUSUS" name="CAT_KHUSUS" maxlength="255" style="height: 100px;">{{ getValue($rm3a, 'CAT_KHUSUS') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card card-danger">
            <div class="card-header"><h5 class="card-title">KONTAK AWAL PASIEN</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <h5>Cara Masuk</h5><hr class="mt-0">
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="CARMAS_JALAN" name="CARMAS_JALAN" value="1" {{ getValue($rm3a, 'CARMAS_JALAN') }}><label class="form-check-label" for="CARMAS_JALAN">Jalan</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="CARMAS_KURSI" name="CARMAS_KURSI" value="1" {{ getValue($rm3a, 'CARMAS_KURSI') }}><label class="form-check-label" for="CARMAS_KURSI">Kursi Roda</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="CARMAS_BRAND" name="CARMAS_BRAND" value="1" {{ getValue($rm3a, 'CARMAS_BRAND') }}><label class="form-check-label" for="CARMAS_BRAND">Brankar</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="CARMAS_GENDONG" name="CARMAS_GENDONG" value="1" {{ getValue($rm3a, 'CARMAS_GENDONG') }}><label class="form-check-label" for="CARMAS_GENDONG">Digendong</label></div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <h5>Kedatangan</h5><hr class="mt-0">
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="DATANG_SDR" name="DATANG_SDR" value="1" {{ getValue($rm3a, 'DATANG_SDR') }}><label class="form-check-label" for="DATANG_SDR">Datang sendiri</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="DATANG_POL" name="DATANG_POL" value="1" {{ getValue($rm3a, 'DATANG_POL') }}><label class="form-check-label" for="DATANG_POL">Diantar Polisi</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" id="DATANG_RUJUKAN" name="DATANG_RUJUKAN" value="1" {{ getValue($rm3a, 'DATANG_RUJUKAN') }}><label class="form-check-label" for="DATANG_RUJUKAN">Rujukan</label></div>
                        <input type="text" class="form-control form-control-sm mt-1" id="DATANG_RUKETERANGAN" name="DATANG_RUKETERANGAN" value="{{ getValue($rm3a, 'DATANG_RUKETERANGAN') }}" maxlength="50" placeholder="Rujukan dari..." style="display: none;">
                        <div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="DATANG_JEMPUT" name="DATANG_JEMPUT" value="1" {{ getValue($rm3a, 'DATANG_JEMPUT') }}><label class="form-check-label" for="DATANG_JEMPUT">Dijemput</label></div>
                        <input type="text" class="form-control form-control-sm mt-1" id="DATANG_JEMKETERANGAN" name="DATANG_JEMKETERANGAN" value="{{ getValue($rm3a, 'DATANG_JEMKETERANGAN') }}" maxlength="50" placeholder="Dijemput oleh..." style="display: none;">
                    </div>
                    <div class="col-lg-4 col-md-12 mb-3">
                        <h5>Sudah Terpasang (dari luar RS)</h5><hr class="mt-0">
                        <textarea class="form-control" id="TERPASANG" name="TERPASANG" maxlength="150" rows="4" placeholder="Contoh: Infus, Oksigen, dll">{{ getValue($rm3a, 'TERPASANG') }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5>Kendaraan</h5><hr class="mt-0">
                            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="KEND_AMBL" name="KEND_AMBL" value="1" {{ getValue($rm3a, 'KEND_AMBL') }}><label class="form-check-label" for="KEND_AMBL">Ambulance</label></div>
                            <div class="form-group"><label for="KEND_LAIN">Kendaraan Lain (selain Ambulance)</label><input type="text" class="form-control" id="KEND_LAIN" name="KEND_LAIN" value="{{ getValue($rm3a, 'KEND_LAIN') }}" maxlength="100" placeholder="Contoh: Mobil Pribadi"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5>Identitas Pengantar</h5><hr class="mt-0">
                            <div class="form-group mb-2"><label for="IDPENGANTAR_NAMA">Nama Pengantar</label><input type="text" class="form-control" id="IDPENGANTAR_NAMA" name="IDPENGANTAR_NAMA" value="{{ getValue($rm3a, 'IDPENGANTAR_NAMA') }}" maxlength="50" placeholder="Masukkan Nama Pengantar"></div>
                            <div class="form-group"><label for="IDNOTELP">No Telp</label><input type="text" class="form-control" id="IDNOTELP" name="IDNOTELP" value="{{ getValue($rm3a, 'IDNOTELP') }}" maxlength="15" placeholder="Masukkan No Telp"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-danger">
            <div class="card-header"><h5 class="card-title">KASUS</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-auto"><div class="form-check"><input class="form-check-input" type="checkbox" id="KASUS_NONTRAU" name="KASUS_NONTRAU" value="1" {{ getValue($rm3a, 'KASUS_NONTRAU') }}><label class="form-check-label" for="KASUS_NONTRAU">Kasus Non Trauma</label></div></div>
                    <div class="col-md-auto"><div class="form-check"><input class="form-check-input" type="checkbox" id="KASUS_TRAUMA" name="KASUS_TRAUMA" value="1" {{ getValue($rm3a, 'KASUS_TRAUMA') }}><label class="form-check-label" for="KASUS_TRAUMA">Kasus Trauma</label></div></div>
                </div>
                <div id="trauma-details" class="{{ getValue($rm3a, 'KASUS_TRAUMA') ? '' : 'd-none' }}">
                    <hr>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group mb-3"><label>Mekanisme Trauma</label><input type="text" class="form-control" name="KASUS_TRAUMALAIN" value="{{ getValue($rm3a, 'KASUS_TRAUMALAIN') }}" placeholder="Misal: Patah Tulang"></div></div>
                        <div class="col-md-6"><div class="form-group mb-3"><label>Keterangan Lainnya</label><input type="text" class="form-control" name="KASUS_TRAUMAKET" value="{{ getValue($rm3a, 'KASUS_TRAUMAKET') }}" placeholder="Deskripsi tambahan"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mt-3">Jenis Kecelakaan</h5><hr class="mt-0">
                            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="KLL_TUNGGAL" value="1" {{ getValue($rm3a, 'KLL_TUNGGAL') }}><label class="form-check-label">KLL Tunggal</label></div>
                            <div class="row g-3">
                                <div class="col-md-6"><div class="form-group"><label>KLL Lain</label><input type="text" class="form-control" name="KLL_LAIN" value="{{ getValue($rm3a, 'KLL_LAIN') }}"></div></div>
                                <div class="col-md-6"><div class="form-group"><label>Tanggal Kejadian</label><input type="date" class="form-control" name="TGLKEJADIAN" value="{{ getValue($rm3a, 'TGLKEJADIAN') }}"></div></div>
                                <div class="col-md-6"><div class="form-group"><label>Tempat Kejadian</label><input type="text" class="form-control" name="TKEJADIAN" value="{{ getValue($rm3a, 'TKEJADIAN') }}"></div></div>
                                <div class="col-md-6"><div class="form-group"><label>Jam Kejadian</label><input type="time" class="form-control" name="JAMKEJADIAN" value="{{ getValue($rm3a, 'JAMKEJADIAN') }}"></div></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mt-3">Jatuh / Luka</h5><hr class="mt-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" id="JATUH_TINGGI" name="JATUH_TINGGI" value="1" {{ getValue($rm3a, 'JATUH_TINGGI') }}><label class="form-check-label" for="JATUH_TINGGI">Jatuh dari Ketinggian</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" id="JATUH_LUKA" name="JATUH_LUKA" value="1" {{ getValue($rm3a, 'JATUH_LUKA') }}><label class="form-check-label" for="JATUH_LUKA">Luka Bakar</label></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label for="JATUH_KETERANGAN">Jelaskan</label><textarea class="form-control" id="JATUH_KETERANGAN" name="JATUH_KETERANGAN" maxlength="100" rows="2" placeholder="Jelaskan lebih detail...">{{ getValue($rm3a, 'JATUH_KETERANGAN') }}</textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $submitButtonText }}</button>
            <button type="button" class="btn btn-outline-danger d-none" id="reset-rm3a"><i class="fas fa-times-circle"></i> Batal / Reset</button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // =================================================================================
    // REPLIKASI FUNGSI DARI KODE NATIVE
    // =================================================================================

    // --- Auto-split Tekanan Darah ---
    // Dihapus karena TD Sistolik dan Diastolik sudah dipisah
    // Hanya validasi angka
    $('#PACS1_TD_SIS, #PACS1_TD_DIA, #PACS1_NADI, #PACS2_NAFAS, #PACS3_SATURASI, #PACS3_NYERI').on('keypress', function(e) {
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });

    // --- Kalkulasi BMI ---
    function calculateBMI() {
        const beratBadan = parseFloat($('#PACS4_BB').val());
        const tinggiBadanCm = parseFloat($('#PACS4_TB').val().replace(',', '.'));
        const inputBMI = $('#BMI');
        const bmiCategory = $('#BMI_Category');

        if (!beratBadan || !tinggiBadanCm || beratBadan <= 0 || tinggiBadanCm <= 0) {
            inputBMI.val('');
            bmiCategory.text('');
            return;
        }

        const tinggiBadanM = tinggiBadanCm / 100;
        const bmi = (beratBadan / (tinggiBadanM * tinggiBadanM)).toFixed(2);
        inputBMI.val(bmi);

        let kategori = '';
        if (bmi < 18.5) kategori = 'Kurus';
        else if (bmi >= 18.5 && bmi <= 24.9) kategori = 'Normal';
        else if (bmi >= 25.0 && bmi <= 29.9) kategori = 'Berat Badan Berlebih';
        else if (bmi >= 30.0) kategori = 'Obesitas';
        bmiCategory.text(kategori);
    }
    $('#PACS4_BB, #PACS4_TB').on('input', calculateBMI);
    calculateBMI(); // Initial calculation on load

    // Validasi input desimal untuk TB
    $('.decimal-input').on('keyup', function() {
        let value = $(this).val();
        value = value.replace(/,/g, '.'); // Ganti koma dengan titik
        value = value.replace(/[^0-9.]/g, ''); // Hanya izinkan angka dan titik
        const dots = value.split('.').length - 1;
        if (dots > 1) {
            value = value.substring(0, value.lastIndexOf('.'));
        }
        $(this).val(value);
    });

    // Logic untuk checkbox eksklusif (hanya satu yang bisa dipilih)
    function exclusiveCheckbox(group) {
        $(group).on('change', function() {
            if ($(this).is(':checked')) {
                $(group).not(this).prop('checked', false);
            }
        });
    }

    exclusiveCheckbox('input[name="PACS2_SADAR"], input[name="PACS3_RESPON"], input[name="PACS4_RESPON"], input[name="PACS1_MENINGGAL"]');
    exclusiveCheckbox('input[name="CARMAS_JALAN"], input[name="CARMAS_BRAND"], input[name="CARMAS_KURSI"], input[name="CARMAS_GENDONG"]');
    exclusiveCheckbox('input[name="DATANG_SDR"], input[name="DATANG_POL"], input[name="DATANG_RUJUKAN"], input[name="DATANG_JEMPUT"]');
    exclusiveCheckbox('input[name="KASUS_NONTRAU"], input[name="KASUS_TRAUMA"]');
    exclusiveCheckbox('input[name="JATUH_TINGGI"], input[name="JATUH_LUKA"]');

    // --- Logika Tampilan Kondisional ---
    function toggleVisibility(checkboxId, containerId) {
        const checkbox = $(checkboxId);
        const container = $(containerId);

        function toggle() {
            if (checkbox.is(':checked')) {
                container.slideDown(200);
            } else {
                container.slideUp(200);
                container.val(''); // Kosongkan input saat disembunyikan
            }
        }
        checkbox.on('change', toggle);
        toggle(); // Panggil saat load untuk set state awal
    }
    toggleVisibility('#DATANG_RUJUKAN', '#DATANG_RUKETERANGAN');
    toggleVisibility('#DATANG_JEMPUT', '#DATANG_JEMKETERANGAN');

    // Tampilkan/sembunyikan detail trauma
    $('input[name="KASUS_TRAUMA"], input[name="KASUS_NONTRAU"]').on('change', function() {
        if ($('#KASUS_TRAUMA').is(':checked')) {
            $('#trauma-details').removeClass('d-none').hide().slideDown(300);
        } else {
            $('#trauma-details').slideUp(300, function() { $(this).addClass('d-none'); });
        }
    });

    // --- Logika WPSS (Worthing Psychological Scoring System) ---
    function updateTotalScore() {
        let totalScore = 0;
        let isAnyCheckboxChecked = false;
        
        const scoreMap = {
            'WORT_SKOR3_SELAIN': 3, 'WORT_SKOR0_SADAR': 0,
            'WORT_SKOR2_99': 2, 'WORT_SKOR0_100': 0,
            'WORT_SKOR1_102': 1, 'WORT_SKOR0_101': 0,
            'WORT_SKOR2_22': 2, 'WORT_SKOR1_20': 1, 'WORT_SKOR0_19': 0,
            'WORT_SKOR3_35': 3, 'WORT_SKOR0_35': 0,
            'WORT_SKOR3_92': 3, 'WORT_SKOR2_92': 2, 'WORT_SKOR1_94': 1, 'WORT_SKOR0_96': 0
        };

        $('input[id^="WORT_SKOR"]:checked').not('[id$="_TOT"]').each(function() {
            const score = scoreMap[this.id];
            if (typeof score !== 'undefined') {
                totalScore += score;
                isAnyCheckboxChecked = true;
            }
        });

        $("#totalScoreDisplay").text(isAnyCheckboxChecked ? totalScore : "");

        $('#WORT_SKOR0_TOT, #WORT_SKOR1_TOT, #WORT_SKOR2_TOT, #WORT_SKOR3_TOT').prop('checked', false);
        if (isAnyCheckboxChecked) {
            if (totalScore >= 5) $('#WORT_SKOR0_TOT').prop('checked', true);
            else if (totalScore >= 2) $('#WORT_SKOR1_TOT').prop('checked', true); // 2-4
            else if (totalScore >= 0) $('#WORT_SKOR3_TOT').prop('checked', true); // 0-1
        }
    }

    function updateWpssChecks() {
        if ($('#PACS1_MENINGGAL').is(':checked')) {
            $('input[id^="WORT_"]').not('#WORT_SKOR2_TOT').prop('checked', false).prop('disabled', true);
            $('#WORT_SKOR2_TOT').prop('checked', true).prop('disabled', false);
            $('#totalScoreDisplay').text('');
            return;
        }

        $('input[id^="WORT_"]').prop('disabled', false);

        // Kesadaran
        if ($('#PACS2_SADAR').is(':checked')) {
            $('#WORT_SKOR0_SADAR').prop('checked', true);
            $('#WORT_SKOR3_SELAIN').prop('checked', false);
        } else if ($('#PACS3_RESPON').is(':checked') || $('#PACS4_RESPON').is(':checked')) {
            $('#WORT_SKOR3_SELAIN').prop('checked', true);
            $('#WORT_SKOR0_SADAR').prop('checked', false);
        } else {
            $('#WORT_SKOR0_SADAR').prop('checked', false);
            $('#WORT_SKOR3_SELAIN').prop('checked', false);
        }

        // TD Sistolik
        let tdSistolik = parseFloat($('#PACS1_TD_SIS').val());
        $('#WORT_SKOR0_100, #WORT_SKOR2_99').prop('checked', false);
        if (!isNaN(tdSistolik)) {
            if (tdSistolik >= 100) $('#WORT_SKOR0_100').prop('checked', true);
            else if (tdSistolik <= 99) $('#WORT_SKOR2_99').prop('checked', true);
        }

        // Nadi
        let nadi = parseFloat($('#PACS1_NADI').val());
        $('#WORT_SKOR0_101, #WORT_SKOR1_102').prop('checked', false);
        if (!isNaN(nadi)) {
            if (nadi <= 101) $('#WORT_SKOR0_101').prop('checked', true);
            else if (nadi >= 102) $('#WORT_SKOR1_102').prop('checked', true);
        }

        // Respirasi
        let respirasi = parseFloat($('#PACS2_NAFAS').val());
        $('#WORT_SKOR0_19, #WORT_SKOR1_20, #WORT_SKOR2_22').prop('checked', false);
        if (!isNaN(respirasi)) {
            if (respirasi <= 19) $('#WORT_SKOR0_19').prop('checked', true);
            else if (respirasi >= 20 && respirasi <= 21) $('#WORT_SKOR1_20').prop('checked', true);
            else if (respirasi >= 22) $('#WORT_SKOR2_22').prop('checked', true);
        }

        // Temperatur
        let temp = parseFloat($('#PACS2_TEMP').val());
        $('#WORT_SKOR0_35, #WORT_SKOR3_35').prop('checked', false);
        if (!isNaN(temp)) {
            if (temp >= 35.3) $('#WORT_SKOR0_35').prop('checked', true);
            else if (temp < 35.3) $('#WORT_SKOR3_35').prop('checked', true);
        }

        // Saturasi
        let saturasi = parseFloat($('#PACS3_SATURASI').val());
        $('#WORT_SKOR0_96, #WORT_SKOR1_94, #WORT_SKOR2_92, #WORT_SKOR3_92').prop('checked', false);
        if (!isNaN(saturasi)) {
            if (saturasi >= 96) $('#WORT_SKOR0_96').prop('checked', true);
            else if (saturasi >= 94 && saturasi <= 95) $('#WORT_SKOR1_94').prop('checked', true);
            else if (saturasi >= 92 && saturasi <= 93) $('#WORT_SKOR2_92').prop('checked', true);
            else if (saturasi < 92) $('#WORT_SKOR3_92').prop('checked', true);
        }

        updateTotalScore();
    }

    // Event listener untuk semua input vital signs dan checkbox kesadaran
    const vitalSignInputs = '#PACS2_SADAR, #PACS3_RESPON, #PACS4_RESPON, #PACS1_TD_SIS, #PACS1_NADI, #PACS2_NAFAS, #PACS2_TEMP, #PACS3_SATURASI';
    $(vitalSignInputs).on('change input', updateWpssChecks);

    // Event listener untuk checkbox WPSS manual
    $('input[id^="WORT_SKOR"]').not('[id$="_TOT"]').on('change', updateTotalScore);

    // Fungsi terpusat untuk menangani status "Meninggal"
    function handleMeninggalState() {
        const isMeninggalChecked = $('#PACS1_MENINGGAL').is(':checked');
        const fieldsToToggle = $('#PACS1_TD_SIS, #PACS1_TD_DIA, #PACS1_NADI, #PACS2_NAFAS, #PACS2_TEMP, #PACS3_SATURASI, #PACS3_NYERI, #PACS4_BB, #PACS4_TB');
        const wpssCheckboxes = $('input[id^="WORT_"]').not('#WORT_SKOR2_TOT');

        if (isMeninggalChecked) {
            fieldsToToggle.val('').prop('disabled', true).removeClass('is-invalid');
            wpssCheckboxes.prop('checked', false).prop('disabled', true);
            $('#WORT_SKOR2_TOT').prop('checked', true).prop('disabled', false);
            $('#totalScoreDisplay').text('');
            $('#BMI, #BMI_Category').text('');
        } else {
            // Hanya aktifkan jika tidak ada checkbox kesadaran lain yang dipilih
            fieldsToToggle.prop('disabled', false);
            wpssCheckboxes.prop('disabled', false);
            $('#WORT_SKOR2_TOT').prop('checked', false);
            updateWpssChecks(); // Recalculate
        }
    }

    // Panggil fungsi handleMeninggalState setiap kali ada perubahan pada grup checkbox kesadaran
    $('input[name="PACS2_SADAR"], input[name="PACS3_RESPON"], input[name="PACS4_RESPON"], input[name="PACS1_MENINGGAL"]').on('change', handleMeninggalState);

    // Panggil fungsi saat load untuk inisialisasi
    calculateBMI();
    handleMeninggalState(); // Panggil saat load untuk set state awal

    // --- Logika Tombol Reset ---
    $('#reset-rm3a').on('click', function() {
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
                $('#form-rm3a-submit')[0].reset();
                // Trigger event change untuk reset semua kalkulasi
                $('#form-rm3a-submit input[type="text"], #form-rm3a-submit textarea').val('');
                $('#form-rm3a-submit input[type="checkbox"], #form-rm3a-submit input[type="radio"]').prop('checked', false);
                $('#form-rm3a-submit').find('input, textarea').trigger('input').trigger('change');
                calculateBMI();
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });

    // --- Logika Submit AJAX ---
    $('#form-rm3a-submit').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
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
                    Swal.fire('Berhasil!', response.message, 'success');
                    // Memicu event 'change' pada dropdown di halaman utama untuk memuat ulang form.
                    $('#form-selector').trigger('change');
                } else {
                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                }
            },
            error: function(xhr) {
                var errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            },
            complete: function(xhr, status) {
                // Hanya aktifkan kembali tombol jika terjadi error,
                // karena saat sukses, form akan dimuat ulang seluruhnya.
                if (status !== 'success') {
                    button.prop('disabled', false).html(originalButtonText);
                }
            }
        });
    });
});
</script>