@php
    // Helper untuk mendapatkan nilai dari data yang ada atau memberikan default
    function getValue($data, $key, $default = '') {
        if (is_object($data) && isset($data->$key) && $data->$key !== '') {
            // Format tanggal dan waktu
            if (in_array($key, ['TGL_DATANG']) && !empty($data->$key)) {
                return \Carbon\Carbon::parse($data->$key)->format('Y-m-d');
            }
            if (in_array($key, ['JAM_DATANG']) && !empty($data->$key)) {
                return \Carbon\Carbon::parse($data->$key)->format('H:i');
            }
            return is_string($data->$key) ? trim($data->$key) : $data->$key;
        }
        return $default;
    }

    function isChecked($data, $key) {
        return is_object($data) && isset($data->$key) && in_array($data->$key, [1, '1', true], true);
    }

    $isUpdate = !empty($rm3b->NOPENDAFTARAN);
    $submitButtonText = $isUpdate ? "Update" : "Simpan";

    // Logika Kondisi Umum dari native
    $ku_baik_checked = isChecked($rm3b, 'KU_BAIK');
    $ku_sedang_checked = isChecked($rm3b, 'KU_SEDANG');
    $ku_buruk_checked = isChecked($rm3b, 'KU_BURUK');
    if (!$ku_baik_checked && !$ku_sedang_checked && !$ku_buruk_checked) {
        $ku_sedang_checked = true; // Default ke Sedang
    }

    // Logika untuk data PARU
    $paruValue = getValue($rm3b, 'PARU', 'SDV +/+ WH -/- RH -/-');

    // Logika untuk memisahkan data EKSTREMITAS
    $ekstremitas_atas = 'Akral hangat +/+ Oedema -/-';
    $ekstremitas_bawah = 'Akral hangat +/+ Oedema -/-';
    $ekstremitas_gabungan = getValue($rm3b, 'EKSTREMITAS', '');
    if (!empty($ekstremitas_gabungan)) {
        $pos = strpos($ekstremitas_gabungan, '; Bawah:');
        if ($pos !== false) {
            $ekstremitas_atas = trim(substr($ekstremitas_gabungan, 6, $pos - 6));
            $ekstremitas_bawah = trim(substr($ekstremitas_gabungan, $pos + 9));
        } else {
            $ekstremitas_atas = str_replace('Atas: ', '', $ekstremitas_gabungan);
        }
    }

    // Logika untuk menentukan nilai Tanda Vital & Keluhan Utama dengan fallback
    $tedarValue = getValue($rm3b, 'TEDAR');
    if (empty($tedarValue) && getValue($rm3b, 'PACS1_TD_SIS') && getValue($rm3b, 'PACS1_TD_DIA')) {
        $tedarValue = getValue($rm3b, 'PACS1_TD_SIS') . '/' . getValue($rm3b, 'PACS1_TD_DIA');
    }
    $suhuValue = getValue($rm3b, 'SUHU', getValue($rm3b, 'PACS2_TEMP'));
    $bbValue = getValue($rm3b, 'BB', getValue($rm3b, 'PACS4_BB'));
    $nadiValue = getValue($rm3b, 'NADI', getValue($rm3b, 'PACS1_NADI'));
    $pernapasanValue = getValue($rm3b, 'PERNAPASAN', getValue($rm3b, 'PACS2_NAFAS'));
    $keluhanUtamaValue = getValue($rm3b, 'keluhan_utama', getValue($rm3b, 'KELUHAN'));
    $so2Value = getValue($rm3b, 'SO2', getValue($rm3b, 'PACS3_SATURASI'));

    // Logika untuk RTL_DPJP agar nilai dari DB selalu terpilih jika ada
    $rm3b_dpjp_value = getValue($rm3b, 'RTL_DPJP');
    $is_rm3b_dpjp_in_list = false;
    foreach ($dokterList as $dokter) {
        if (strtolower($dokter->NAMAPEMERIKSA) == strtolower($rm3b_dpjp_value)) {
            $is_rm3b_dpjp_in_list = true;
            break;
        }
    }
@endphp

<form id="form-rm3b-submit" action="{{ route('rme.igd.form.rm3b.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">ASESMEN MEDIS GAWAT DARURAT</h3>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-header bg-danger text-white"><h5 class="card-title">Formulir Pemeriksaan Pasien</h5></div>
                <div class="card-body">
                    <div class="row">
                        {{-- Keterangan Kedatangan --}}
                        <div class="col-md-4 border-end">
                            <label class="form-label fw-bold">Keterangan Kedatangan</label>
                            <input type="hidden" name="NORM" value="{{ getValue($rm3b, 'NORM', $norm) }}">
                            <input type="hidden" name="NOPENDAFTARAN" value="{{ getValue($rm3b, 'NOPENDAFTARAN', $noPendaftaran) }}">
                            <input type="hidden" name="USER_ENTRY" value="{{ getValue($rm3b, 'USER_ENTRY', $user->username ?? '') }}">
                            <input type="hidden" name="TGLJAM_ENTRY" value="{{ now()->toDateTimeString() }}">

                            <div class="form-group mb-3">
                                <label for="TGL_DATANG">Tanggal Datang</label>
                                <input type="date" class="form-control" id="TGL_DATANG" name="TGL_DATANG" value="{{ getValue($rm3b, 'TGL_DATANG', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="JAM_DATANG">Jam Datang</label>
                                <input type="time" class="form-control" id="JAM_DATANG" name="JAM_DATANG" value="{{ getValue($rm3b, 'JAM_DATANG', now()->format('H:i')) }}" required>
                            </div>
                        </div>

                        {{-- Data Obyektif --}}
                        <div class="col-md-4 border-end">
                            <label class="form-label fw-bold">Data Obyektif</label>
                            <div class="mb-3">
                                <label class="form-label">Kondisi Umum:</label>
                                <div class="form-check"><input class="form-check-input exclusive-check" type="checkbox" id="KU_BAIK" name="KU_BAIK" @if($ku_baik_checked) checked @endif data-group="kondisi-umum"><label class="form-check-label" for="KU_BAIK">Baik</label></div>
                                <div class="form-check"><input class="form-check-input exclusive-check" type="checkbox" id="KU_SEDANG" name="KU_SEDANG" @if($ku_sedang_checked) checked @endif data-group="kondisi-umum"><label class="form-check-label" for="KU_SEDANG">Sedang</label></div>
                                <div class="form-check mb-3"><input class="form-check-input exclusive-check" type="checkbox" id="KU_BURUK" name="KU_BURUK" @if($ku_buruk_checked) checked @endif data-group="kondisi-umum"><label class="form-check-label" for="KU_BURUK">Buruk</label></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="GCS_E">GCS Eye</label>
                                <div class="input-group">
                                    <span class="input-group-text">E</span>
                                    <input type="number" class="form-control gcs-input" id="GCS_E" name="GCS_E" min="1" max="4" placeholder="Eye" value="{{ getValue($rm3b, 'GCS_E', '4') }}">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="GCS_V">GCS Verbal</label>
                                <div class="input-group">
                                    <span class="input-group-text">V</span>
                                    <input type="number" class="form-control gcs-input" id="GCS_V" name="GCS_V" min="1" max="5" placeholder="Verbal" value="{{ getValue($rm3b, 'GCS_V', '5') }}">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="GCS_M">GCS Motorik</label>
                                <div class="input-group">
                                    <span class="input-group-text">M</span>
                                    <input type="number" class="form-control gcs-input" id="GCS_M" name="GCS_M" min="1" max="6" placeholder="Motorik" value="{{ getValue($rm3b, 'GCS_M', '6') }}">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="KESADARAN">Kesadaran</label>
                                <input type="text" class="form-control" id="KESADARAN" name="KESADARAN" placeholder="Kesadaran" value="{{ getValue($rm3b, 'KESADARAN') }}" readonly>
                            </div>
                        </div>

                        {{-- Tanda Vital --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanda Vital</label>
                            <div class="form-group mb-3">
                                <label for="TEDAR">Tekanan Darah</label>
                                <div class="input-group">
                                    <input 
                                        type="text" class="form-control" id="TEDAR" name="TEDAR" 
                                        placeholder="Tekanan Darah" value="{{ $tedarValue }}"
                                    >
                                    <span class="input-group-text">mmHg</span>
                                </div>
                            </div>
                            <div class="form-group mb-3"><label for="SUHU">Suhu</label><div class="input-group"><input type="text" class="form-control decimal-input" id="SUHU" name="SUHU" placeholder="Suhu" value="{{ $suhuValue }}"><span class="input-group-text">°C</span></div></div>
                            <div class="form-group mb-3"><label for="BB">Berat Badan</label><div class="input-group"><input type="text" class="form-control decimal-input" id="BB" name="BB" placeholder="Berat Badan" value="{{ $bbValue }}"><span class="input-group-text">Kg</span></div></div>
                            <div class="form-group mb-3"><label for="NADI">Nadi</label><div class="input-group"><input type="number" class="form-control" id="NADI" name="NADI" placeholder="Nadi" value="{{ $nadiValue }}"><span class="input-group-text">x/menit</span></div></div>
                            <div class="form-group mb-3"><label for="PERNAPASAN">Pernapasan</label><div class="input-group"><input type="number" class="form-control" id="PERNAPASAN" name="PERNAPASAN" placeholder="Pernapasan" value="{{ $pernapasanValue }}"><span class="input-group-text">x/menit</span></div></div>
                            <div class="form-group mb-3"><label for="SO2">Saturasi Oksigen (SO2)</label><div class="input-group"><input type="number" class="form-control" id="SO2" name="SO2" placeholder="SO2" value="{{ $so2Value }}"><span class="input-group-text">%</span></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4"> <!-- Asesmen Medis -->
                <div class="card-header bg-danger text-white"><h5 class="card-title mb-0">Asesmen Medis</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3"><label class="form-label fw-bold">Anamnesa:</label><div class="form-check"><input class="form-check-input" type="checkbox" id="ANAMNESA_AUTO" name="ANAMNESA_AUTO" @if(isChecked($rm3b, 'ANAMNESA_AUTO')) checked @endif><label class="form-check-label" for="ANAMNESA_AUTO">Anamnesa Auto</label></div><div class="form-check"><input class="form-check-input" type="checkbox" id="ANAMNESA_ALLO" name="ANAMNESA_ALLO" @if(isChecked($rm3b, 'ANAMNESA_ALLO')) checked @endif><label class="form-check-label" for="ANAMNESA_ALLO">Anamnesa Allo</label></div></div>
                            <div class="mb-3"><label for="keluhan_utama" class="form-label fw-bold">Keluhan Utama:</label><textarea class="form-control" id="keluhan_utama" name="keluhan_utama" rows="4">{{ $keluhanUtamaValue }}</textarea></div>
                            <div class="mb-3"><label for="RIW_PENYAKIT" class="form-label fw-bold">Riwayat Penyakit Sekarang:</label><textarea class="form-control" id="RIW_PENYAKIT" name="RIW_PENYAKIT" rows="4">{{ getValue($rm3b, 'RIW_PENYAKIT') }}</textarea></div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Riwayat Penyakit Dahulu:</label>
                                <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="RPD_TIDAK" name="RPD_TIDAK" @if(isChecked($rm3b, 'RPD_TIDAK')) checked @endif data-group="riwayat-penyakit-dahulu"><label class="form-check-label" for="RPD_TIDAK">Tidak Ada</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="RPD_ADA" name="RPD_ADA" @if(isChecked($rm3b, 'RPD_ADA')) checked @endif data-group="riwayat-penyakit-dahulu"><label class="form-check-label" for="RPD_ADA">Ada</label></div>
                                <div class="mt-2" id="rpd_ada_ket_container" 
                                    style="display: {{ isChecked($rm3b, 'RPD_ADA') ? 'block' : 'none' }};">
                                <label for="RPD_ADA_KET" class="form-label">Keterangan:</label>
                                <textarea class="form-control" id="RPD_ADA_KET" name="RPD_ADA_KET" rows="3">{{ getValue($rm3b, 'RPD_ADA_KET') }}</textarea>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Riwayat Penyakit Keluarga:</label>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="RPK_TIDAK" name="RPK_TIDAK" @if(isChecked($rm3b, 'RPK_TIDAK')) checked @endif><label class="form-check-label" for="RPK_TIDAK">Tidak Ada</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="RPK_ADA" name="RPK_ADA" @if(isChecked($rm3b, 'RPK_ADA')) checked @endif><label class="form-check-label" for="RPK_ADA">Ada</label></div>
                                <div class="mt-2" id="rpk_ada_ket_container" 
                                    style="display: {{ isChecked($rm3b, 'RPK_ADA') ? 'block' : 'none' }};">
                                <label for="RPK_ADA_KET" class="form-label">Keterangan:</label>
                                <textarea class="form-control" id="RPK_ADA_KET" name="RPK_ADA_KET" rows="3">{{ getValue($rm3b, 'RPK_ADA_KET') }}</textarea>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Riwayat Alergi:</label>
                                <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="RA_TIDAK" name="RA_TIDAK" @if(isChecked($rm3b, 'RA_TIDAK')) checked @endif data-group="riwayat-alergi"><label class="form-check-label" for="RA_TIDAK">Tidak Ada</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="RA_ADA" name="RA_ADA" @if(isChecked($rm3b, 'RA_ADA')) checked @endif data-group="riwayat-alergi"><label class="form-check-label" for="RA_ADA">Ada</label></div>
                                <div class="mt-2" id="RA_ADA_KET_CONTAINER" 
                                    style="display: {{ isChecked($rm3b, 'RA_ADA') ? 'block' : 'none' }};">
                                <label for="RA_ADA_KET" class="form-label">Keterangan:</label>
                                <textarea class="form-control" id="RA_ADA_KET" name="RA_ADA_KET" rows="3">{{ getValue($rm3b, 'RA_ADA_KET') }}</textarea>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Penilaian Tingkat Nyeri</label>
                                <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="NYERI_TIDAK" name="NYERI_TIDAK" @if(isChecked($rm3b, 'NYERI_TIDAK')) checked @endif data-group="nyeri"><label class="form-check-label" for="NYERI_TIDAK">Tidak Ada</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="NYERI_YA3B" name="NYERI_YA3B" @if(isChecked($rm3b, 'NYERI_YA3B')) checked @endif data-group="nyeri"><label class="form-check-label" for="NYERI_YA3B">Ya</label></div>
                                <div id="skoringNyeriForm" style="display: {{ isChecked($rm3b, 'NYERI_YA3B') ? 'block' : 'none' }};">
                                    <div class="mt-2"><label class="form-label fw-bold">Skoring Nyeri:</label>
                                        <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="VAS" name="VAS" @if(isChecked($rm3b, 'VAS')) checked @endif data-group="metode-nyeri"><label class="form-check-label" for="VAS">VAS</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="WONG" name="WONG" @if(isChecked($rm3b, 'WONG')) checked @endif data-group="metode-nyeri"><label class="form-check-label" for="WONG">Wong-Baker</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="FLACC" name="FLACC" @if(isChecked($rm3b, 'FLACC')) checked @endif data-group="metode-nyeri"><label class="form-check-label" for="FLACC">FLACC</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input exclusive-check" type="checkbox" id="NIPS" name="NIPS" @if(isChecked($rm3b, 'NIPS')) checked @endif data-group="metode-nyeri"><label class="form-check-label" for="NIPS">NIPS</label></div>
                                    </div>
                                    <div class="mt-2 mb-3 d-flex align-items-center"><label for="NYERI_YA_KET" class="form-label me-2 mb-0">Skala Nyeri:</label><div class="input-group" style="width: 150px;"><input type="number" class="form-control text-center" id="NYERI_YA_KET" name="NYERI_YA_KET" min="1" max="10" value="{{ getValue($rm3b, 'NYERI_YA_KET') }}"><span class="input-group-text">1-10</span></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4"> <!-- Pemeriksaan Fisik -->
                <div class="card-header bg-danger text-white"><h5 class="card-title mb-0">Pemeriksaan Fisik</h5></div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-12"><h5 class="border-bottom pb-2 mb-3">Pemeriksaan Umum</h5></div>
                        <div class="col-md-6"><label for="KEPALA" class="form-label fw-bold">Kepala</label><textarea class="form-control" id="KEPALA" name="KEPALA" rows="2">{{ getValue($rm3b, 'KEPALA', 'CA -/- SI -/-') }}</textarea></div>
                        <div class="col-md-6"><label for="LEHER" class="form-label fw-bold">Leher</label><textarea class="form-control" id="LEHER" name="LEHER" rows="2">{{ getValue($rm3b, 'LEHER', 'Jvp Normal') }}</textarea></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12"><h5 class="border-bottom pb-2 mb-3">Thorax</h5></div>
                        <div class="col-md-6"><label for="JANTUNG" class="form-label">Jantung</label><textarea class="form-control" id="JANTUNG" name="JANTUNG" rows="2">{{ getValue($rm3b, 'JANTUNG', 'BJ 1 & 2 REGULER') }}</textarea></div>
                        <div class="col-md-6"><label for="PARU" class="form-label">Paru</label><textarea class="form-control" id="PARU" name="PARU" rows="2">{{ $paruValue }}</textarea></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12"><h5 class="border-bottom pb-2 mb-3">Abdomen & Ekstremitas</h5></div>
                        <div class="col-md-6"><label for="ABDOMEN" class="form-label">Abdomen</label><textarea class="form-control" id="ABDOMEN" name="ABDOMEN" rows="2">{{ getValue($rm3b, 'ABDOMEN', 'Peristaltik Normal') }}</textarea></div>
                        <div class="col-md-6"><label for="ANOGENITAL" class="form-label">Anogenital</label><textarea class="form-control" id="ANOGENITAL" name="ANOGENITAL" rows="2">{{ getValue($rm3b, 'ANOGENITAL', 'Dalam batas normal') }}</textarea></div>
                        <div class="col-md-6"><label for="EKSREATAS" class="form-label">Ekstremitas Atas</label><textarea class="form-control" id="EKSREATAS" name="EKSREATAS" rows="2">{{ $ekstremitas_atas }}</textarea></div>
                        <div class="col-md-6"><label for="EKSTREBAWAH" class="form-label">Ekstremitas Bawah</label><textarea class="form-control" id="EKSTREBAWAH" name="EKSTREBAWAH" rows="2">{{ $ekstremitas_bawah }}</textarea></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-danger text-white"><h5 class="card-title mb-0">Diagnosis & Rencana</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3"><label for="DIAGNOSIS_UTAMA" class="form-label fw-bold">Diagnosis Utama:</label><input type="text" class="form-control" id="DIAGNOSIS_UTAMA" name="DIAGNOSIS_UTAMA" placeholder="Masukkan Diagnosis Utama" value="{{ getValue($rm3b, 'DIAGNOSIS_UTAMA') }}"></div>
                            <div id="diagnosis-sekunder-container">
                                <label class="form-label fw-bold">Diagnosis Sekunder:</label>
                                @php
                                    $diagnosisSekunder = array_filter([getValue($rm3b, 'DIAGNO_SEKUND_1'), getValue($rm3b, 'DIAGNO_SEKUND_2'), getValue($rm3b, 'DIAGNO_SEKUND_3'), getValue($rm3b, 'DIAGNO_SEKUND_4')]);
                                    if (empty($diagnosisSekunder)) { $diagnosisSekunder[] = ''; }
                                @endphp
                                @foreach ($diagnosisSekunder as $diag)
                                <div class="mb-3 d-flex align-items-center diagnosis-sekunder-input-group">
                                    <div class="input-group"><input type="text" class="form-control" name="DIAGNO_SEKUND[]" placeholder="Masukkan Diagnosis Sekunder" value="{{ $diag }}">
                                    @if ($loop->first)
                                    <button type="button" class="btn btn-success" onclick="tambahInput(this)">+</button>
                                    @else
                                    <button type="button" class="btn btn-danger" onclick="hapusInput(this)">-</button>
                                    @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3"><label for="TERAPI_SMNTR" class="form-label fw-bold">Terapi Sementara:</label><textarea class="form-control" id="TERAPI_SMNTR" name="TERAPI_SMNTR" rows="5" placeholder="Masukkan Terapi Sementara">{{ getValue($rm3b, 'TERAPI_SMNTR') }}</textarea></div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Rencana Tindak Lanjut (RTL):</label>
                                <div class="form-check"><input class="form-check-input exclusive-check" type="checkbox" id="RTL_RAJAL" name="RTL_RAJAL" @if(isChecked($rm3b, 'RTL_RAJAL')) checked @endif data-group="rtl"><label class="form-check-label" for="RTL_RAJAL">Rawat Jalan</label></div>
                                <div class="form-check">
                                    <input class="form-check-input exclusive-check" type="checkbox" id="RTL_RANAP" name="RTL_RANAP" @if(isChecked($rm3b, 'RTL_RANAP')) checked @endif data-group="rtl"><label class="form-check-label" for="RTL_RANAP">Rawat Inap</label>
                                    <div class="mt-2" id="dpjpContainer" style="display: {{ isChecked($rm3b, 'RTL_RANAP') ? 'block' : 'none' }};"><label for="RTL_DPJP" class="form-label">DPJP Rawat Inap:</label>
                                        <select class="form-control" id="RTL_DPJP" name="RTL_DPJP">
                                            <option value="">-- Pilih DPJP --</option>
                                            @if (!empty($rm3b_dpjp_value) && !$is_rm3b_dpjp_in_list)
                                                <option value="{{ $rm3b_dpjp_value }}" selected>{{ $rm3b_dpjp_value }}</option>
                                            @endif
                                            @foreach ($dokterList as $dokter)
                                                <option value="{{ $dokter->NAMAPEMERIKSA }}" @if(strtolower($rm3b_dpjp_value) == strtolower($dokter->NAMAPEMERIKSA)) selected @endif>{{ $dokter->NAMAPEMERIKSA }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-check"><input class="form-check-input exclusive-check" type="checkbox" id="RTL_MGL_IDG" name="RTL_MGL_IDG" @if(isChecked($rm3b, 'RTL_MGL_IDG')) checked @endif data-group="rtl"><label class="form-check-label" for="RTL_MGL_IDG">Meninggal di IGD</label></div>
                                <div class="form-check"><input class="form-check-input exclusive-check" type="checkbox" id="DIRUJUK_RS" name="DIRUJUK_RS" @if(isChecked($rm3b, 'DIRUJUK_RS')) checked @endif data-group="rtl"><label class="form-check-label" for="DIRUJUK_RS">Dirujuk</label></div>
                                <div id="keteranganRujukanContainer" class="mt-2" style="display: {{ isChecked($rm3b, 'DIRUJUK_RS') ? 'block' : 'none' }};"><label for="DIRUJUK_RS_KET" class="form-label">Keterangan Rujukan:</label><input type="text" class="form-control" id="DIRUJUK_RS_KET" name="DIRUJUK_RS_KET" placeholder="Masukkan keterangan RS" value="{{ getValue($rm3b, 'DIRUJUK_RS_KET') }}"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="DOKTER_IGD" class="form-label">Dokter IGD:</label>
                            <input type="text" class="form-control" id="DOKTER_IGD" name="DOKTER_IGD" value="{{ getValue($rm3b, 'DOKTER_IGD', $user['namapemeriksa'] ?? ($user['username'] ?? '')) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions ">
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save"></i> {{ $submitButtonText }}</button>
                <button type="button" class="btn btn-outline-danger shadow-sm" id="reset-rm3b"><i class="fas fa-times-circle"></i> Batal / Reset</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // =================================================================================
    // INISIALISASI & HELPER
    // =================================================================================

    // Ambil data pasien yang dikirim dari controller
    const patientDetails = @json($patient ?? []);

    // Cek apakah ini mode update (data rm3b sudah ada)
    const isUpdateMode = {{ $isUpdate ? 'true' : 'false' }};

    // Fungsi untuk checkbox eksklusif
    function exclusiveCheckbox(groupName) {
        $(`input.exclusive-check[data-group="${groupName}"]`).on('change', function() {
            if ($(this).is(':checked')) {
                $(`input.exclusive-check[data-group="${groupName}"]`).not(this).prop('checked', false);
            }
        });
    }

    // Inisialisasi checkbox eksklusif
    exclusiveCheckbox('kondisi-umum');
    exclusiveCheckbox('riwayat-penyakit-dahulu');
    exclusiveCheckbox('riwayat-alergi');
    exclusiveCheckbox('nyeri');
    exclusiveCheckbox('metode-nyeri');;
    exclusiveCheckbox('rtl');

    // Validasi dan kalkulasi GCS
    function calculateGCS() {
        const gcsE = parseInt($('#GCS_E').val()) || 0;
        const gcsV = parseInt($('#GCS_V').val()) || 0;
        const gcsM = parseInt($('#GCS_M').val()) || 0;
        const totalGCS = gcsE + gcsV + gcsM;

        let kesadaran = '';
        if (totalGCS === 0) kesadaran = '';
        else if (totalGCS === 3) kesadaran = 'Coma';
        else if (totalGCS >= 4 && totalGCS <= 6) kesadaran = 'Soporocomatous';
        else if (totalGCS >= 7 && totalGCS <= 9) kesadaran = 'Somnolen';
        else if (totalGCS >= 10 && totalGCS <= 11) kesadaran = 'Delirium';
        else if (totalGCS >= 12 && totalGCS <= 13) kesadaran = 'Apatis';
        else if (totalGCS <= 15) kesadaran = 'Composmentis';

        $('#KESADARAN').val(kesadaran);
    }

    $('.gcs-input').on('input', function() {
        const min = parseInt($(this).attr('min'));
        const max = parseInt($(this).attr('max'));
        let value = parseInt($(this).val());
        if (isNaN(value)) $(this).val('');
        if (value > max) $(this).val(max);
        calculateGCS();
    });
    calculateGCS(); // Initial call

    // Format Tekanan Darah
    $('#TEDAR').on('input', function(e) {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val.length > 3) {
            this.value = val.substring(0, 3) + '/' + val.substring(3, 6);
        } else {
            this.value = val;
        }
    });

    // Riwayat Penyakit Dahulu (RPD)
    function handleRpd() {
        if ($('#RPD_ADA').is(':checked')) {
            $('#rpd_ada_ket_container').slideDown(200);
        } else {
            $('#rpd_ada_ket_container').slideUp(200);
            $('#RPD_ADA_KET').val('');
        }
    }
    $('input[data-group="riwayat-penyakit-dahulu"]').on('change', handleRpd);
    handleRpd(); // Initial call

    // Riwayat Penyakit Keluarga (RPK)
    function handleRpk() {
        if ($('#RPK_ADA').is(':checked')) {
            $('#rpk_ada_ket_container').slideDown(200);
        } else {
            $('#rpk_ada_ket_container').slideUp(200);
            $('#RPK_ADA_KET').val('');
        }
    }
    // Event listener untuk RPK
    $('#RPK_ADA, #RPK_TIDAK').on('change', handleRpk);
    handleRpk(); // Initial call

    // Riwayat Alergi
    function handleAlergi() {
        if ($('#RA_ADA').is(':checked')) {
            $('#RA_ADA_KET_CONTAINER').slideDown(300);
        } else {
            $('#RA_ADA_KET_CONTAINER').slideUp(300);
            $('#RA_ADA_KET').val('');
        }
    }
    $('input[data-group="riwayat-alergi"]').on('change', handleAlergi);
    handleAlergi(); // Initial call

    // Penilaian Nyeri
    function handleNyeri() {
        if ($('#NYERI_YA3B').is(':checked')) {
            $('#skoringNyeriForm').slideDown(300);
        } else {
            $('#skoringNyeriForm').slideUp(300);
            $('input[data-group="metode-nyeri"]').prop('checked', false);
            $('#NYERI_YA_KET').val('');
        }
    }
    $('input[data-group="nyeri"]').on('change', handleNyeri);
    handleNyeri(); // Initial call

    // Rencana Tindak Lanjut (RTL)
    function handleRtl(isInitialLoad = false) {
        // Logika untuk Opsi "Dirujuk"
        if ($('#DIRUJUK_RS').is(':checked')) {
            $('#keteranganRujukanContainer').slideDown(300);
        } else {
            $('#keteranganRujukanContainer').slideUp(300);
            if (!isInitialLoad) {
                $('#DIRUJUK_RS_KET').val('');
            }
        }

        // Logika untuk Opsi "Rawat Inap"
        if ($('#RTL_RANAP').is(':checked')) {
            $('#dpjpContainer').slideDown(300);

            // --- IMPLEMENTASI BARU ---
            // Jika ini BUKAN mode update (form baru) dan checkbox baru saja dicentang,
            // coba isi otomatis DPJP dari detail pasien.
            if (!isUpdateMode && !isInitialLoad) {
                if (patientDetails && patientDetails.DPJP) {
                    const dpjpValue = patientDetails.DPJP;
                    const $dpjpSelect = $('#RTL_DPJP'); // Pastikan ID ini sesuai dengan select DPJP Anda

                    if ($dpjpSelect.find(`option[value="${dpjpValue}"]`).length > 0) {
                        $dpjpSelect.val(dpjpValue);
                    }
                }
            }

        } else {
            $('#dpjpContainer').slideUp(300);
            if (!isInitialLoad) {
                $('#RTL_DPJP').val(''); // Kosongkan nilai DPJP jika ranap tidak dicentang
            }
        }
    }

    $('input[data-group="rtl"]').on('change', () => handleRtl(false));
    handleRtl(true); // Panggil saat halaman dimuat dengan flag isInitialLoad = true

    // Diagnosis Sekunder
    window.tambahInput = function(button) {
        const container = $('#diagnosis-sekunder-container');
        if (container.find('.diagnosis-sekunder-input-group').length >= 4) {
            Swal.fire('Info', 'Maksimal 4 diagnosis sekunder.', 'info');
            return;
        }
        const newRow = `
            <div class="mb-3 d-flex align-items-center diagnosis-sekunder-input-group">
                <div class="input-group"><input type="text" class="form-control" name="DIAGNO_SEKUND[]" placeholder="Masukkan Diagnosis Sekunder">
                <button type="button" class="btn btn-danger" onclick="hapusInput(this)">-</button></div>
            </div>`;
        container.append(newRow);
    }

    window.hapusInput = function(button) {
        $(button).closest('.d-flex').remove();
    }

    // =================================================================================
    // AJAX SUBMISSION
    // =================================================================================
    $('#form-rm3b-submit').on('submit', function(e) {
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
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, showConfirmButton: false, timer: 1500 })
                        .then(() => {
                        // Cek apakah ada data untuk WhatsApp
                        if (response.wa_data && response.wa_data.doctors) {
                            let doctorsOptions = '<option value="">-- Pilih Dokter --</option>';
                            response.wa_data.doctors.forEach(doctor => {
                                doctorsOptions += `<option value="${doctor.phone}">${doctor.name}</option>`;
                            });
                            
                            Swal.fire({
                                title: 'Kirim Laporan via WhatsApp?',
                                html: `<p>Pilih dokter yang akan dikirimi laporan:</p>
                                       <select id="swal-doctor-select" class="form-control">${doctorsOptions}</select>`,
                                icon: 'question',
                                showCancelButton: true,
                                showConfirmButton: true,
                                confirmButtonText: 'Kirim WA',
                                cancelButtonText: 'Lewati',
                                allowOutsideClick: false, // Mencegah dialog tertutup saat klik di luar
                                preConfirm: () => {
                                    const selectedPhone = $('#swal-doctor-select').val();
                                    if (!selectedPhone) {
                                        Swal.showValidationMessage('Anda harus memilih dokter terlebih dahulu.');
                                        return false;
                                    }
                                    // Kirim WA tapi jangan tutup dialog
                                    const waMessageEncoded = encodeURIComponent(response.wa_data.message);
                                    const waUrl = `https://web.whatsapp.com/send?phone=${selectedPhone}&text=${waMessageEncoded}`;
                                    window.open(waUrl, '_blank');
                                    
                                    // Kembalikan false agar dialog tidak tertutup
                                    return false; 
                                }
                            }).then((result) => {
                                // Dialog ini hanya akan masuk ke sini jika tombol "Lewati" (cancel) ditekan
                                if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                                    // Muat ulang form setelah dialog WA selesai (dilewati)
                                    $('#form-selector').trigger('change');
                                }
                            });
                        } else {
                            // Jika tidak ada data WA, langsung muat ulang form
                            $('#form-selector').trigger('change');
                        }
                    });
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
                // Karena form akan dimuat ulang pada saat sukses, kita tidak perlu
                // mengaktifkan kembali tombol secara manual. Jika terjadi error,
                // tombol akan diaktifkan kembali.
                if (status !== 'success') {
                    button.prop('disabled', false).html(originalButtonText);
                }
            }
        });
    });

    // Tombol Reset
    $('#reset-rm3b').on('click', function() {
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
                $('#form-rm3b-submit')[0].reset();
                // Trigger change untuk reset semua state JS
                $('#form-rm3b-submit').find('input, textarea').trigger('input').trigger('change');
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });
});
</script>