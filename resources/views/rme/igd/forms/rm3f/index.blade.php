@php
    // Helper untuk mendapatkan nilai dari data yang ada atau memberikan default
    function getValueRm3f($data, $key, $default = '') {
        return data_get($data, $key, $default);
    }

    function isCheckedRm3f($data, $key) {
        return data_get($data, $key) == 1 ? 'checked' : '';
    }

    $isUpdate = !empty($row['NOPENDAFTARAN_RM3F']);
    $submitButtonText = $isUpdate ? "Update" : "Simpan";
@endphp

<div class="card-body">
    <form id="form-rm3f-submit" action="{{ route('rme.igd.form.rm3f.store') }}" method="POST">
        @csrf
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">

        <!-- ASESMEN AWAL -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">ASESMEN AWAL</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="KELUHAN" class="form-label fw-bold">Keluhan Utama:</label>
                    <textarea id="KELUHAN" name="KELUHAN" class="form-control" rows="2">{{ getValueRm3f($row, 'KELUHAN', '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="RIWAYAT_PS" class="form-label fw-bold">Riwayat Penyakit Sekarang:</label>
                    <textarea id="RIWAYAT_PS" name="RIWAYAT_PS" class="form-control" rows="2">{{ getValueRm3f($row, 'RIWAYAT_PS', '') }}</textarea>
                </div>
                <div>
                    <label for="RIWAYAT_PD" class="form-label fw-bold">Riwayat Penyakit Dahulu:</label>
                    <textarea id="RIWAYAT_PD" name="RIWAYAT_PD" class="form-control" rows="2">{{ getValueRm3f($row, 'RIWAYAT_PD', '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- SISTEM TUBUH -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">SISTEM TUBUH</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Tekanan Intrakranial -->
                    <div class="col-md-6 mb-3">
                        <fieldset class="border p-3 rounded h-100">
                            <legend class="fs-6 fw-bold w-auto px-2">Tekanan Intrakranial:</legend>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="TEKANAN_INTRAK_NRML" value="1" id="TEKANAN_INTRAK_NRML" {{ isCheckedRm3f($row, 'TEKANAN_INTRAK_NRML') }}><label class="form-check-label" for="TEKANAN_INTRAK_NRML">Normal</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="TEKANAN_INTRAK_SK" value="1" id="TEKANAN_INTRAK_SK" {{ isCheckedRm3f($row, 'TEKANAN_INTRAK_SK') }}><label class="form-check-label" for="TEKANAN_INTRAK_SK">Sakit Kepala</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="TEKANAN_INTRAK_MNTH" value="1" id="TEKANAN_INTRAK_MNTH" {{ isCheckedRm3f($row, 'TEKANAN_INTRAK_MNTH') }}><label class="form-check-label" for="TEKANAN_INTRAK_MNTH">Muntah</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="TEKANAN_INTRAK_PSNG" value="1" id="TEKANAN_INTRAK_PSNG" {{ isCheckedRm3f($row, 'TEKANAN_INTRAK_PSNG') }}><label class="form-check-label" for="TEKANAN_INTRAK_PSNG">Penglihatan Kabur</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="TEKANAN_INTRAK_BING" value="1" id="TEKANAN_INTRAK_BING" {{ isCheckedRm3f($row, 'TEKANAN_INTRAK_BING') }}><label class="form-check-label" for="TEKANAN_INTRAK_BING">Bingung</label></div>
                        </fieldset>
                    </div>
                    <!-- Pupil -->
                    <div class="col-md-6 mb-3">
                        <fieldset class="border p-3 rounded h-100">
                            <legend class="fs-6 fw-bold w-auto px-2">Pupil:</legend>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="PUPIL_NORMAL" value="1" id="PUPIL_NORMAL" {{ isCheckedRm3f($row, 'PUPIL_NORMAL') }}><label class="form-check-label" for="PUPIL_NORMAL">Normal</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="PUPIL_MIOSIS" value="1" id="PUPIL_MIOSIS" {{ isCheckedRm3f($row, 'PUPIL_MIOSIS') }}><label class="form-check-label" for="PUPIL_MIOSIS">Miosis</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="PUPIL_MIDRIASIS" value="1" id="PUPIL_MIDRIASIS" {{ isCheckedRm3f($row, 'PUPIL_MIDRIASIS') }}><label class="form-check-label" for="PUPIL_MIDRIASIS">Midriasis</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="PUPIL_ISOKOR" value="1" id="PUPIL_ISOKOR" {{ isCheckedRm3f($row, 'PUPIL_ISOKOR') }}><label class="form-check-label" for="PUPIL_ISOKOR">Isokor</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="PUPIL_ANISOKOR" value="1" id="PUPIL_ANISOKOR" {{ isCheckedRm3f($row, 'PUPIL_ANISOKOR') }}><label class="form-check-label" for="PUPIL_ANISOKOR">Anisokor</label></div>
                        </fieldset>
                    </div>
                    <!-- Neuro Sensorik/Muskuloskeletal -->
                    <div class="col-md-12 mb-3">
                        <fieldset class="border p-3 rounded">
                            <legend class="fs-6 fw-bold w-auto px-2">Neuro Sensorik/Muskuloskeletal:</legend>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="NEURO_NORMAL" value="1" id="NEURO_NORMAL" {{ isCheckedRm3f($row, 'NEURO_NORMAL') }}><label class="form-check-label" for="NEURO_NORMAL">Normal</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="NEURO_SPASMA" value="1" id="NEURO_SPASMA" {{ isCheckedRm3f($row, 'NEURO_SPASMA') }}><label class="form-check-label" for="NEURO_SPASMA">Spasme Otot</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="NEURO_MOTORIK" value="1" id="NEURO_MOTORIK" {{ isCheckedRm3f($row, 'NEURO_MOTORIK') }}><label class="form-check-label" for="NEURO_MOTORIK">Perubahan Motorik</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="NEURO_SENSORIK" value="1" id="NEURO_SENSORIK" {{ isCheckedRm3f($row, 'NEURO_SENSORIK') }}><label class="form-check-label" for="NEURO_SENSORIK">Perubahan Sensorik</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="NEURO_KERUSAKAN" value="1" id="NEURO_KERUSAKAN" {{ isCheckedRm3f($row, 'NEURO_KERUSAKAN') }}><label class="form-check-label" for="NEURO_KERUSAKAN">Kerusakan Jaringan/luka</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="NEURO_BENTUK" value="1" id="NEURO_BENTUK" {{ isCheckedRm3f($row, 'NEURO_BENTUK') }}><label class="form-check-label" for="NEURO_BENTUK">Perubahan Bentuk</label></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="NEURO_TINGKAT_KESAD" value="1" id="NEURO_TINGKAT_KESAD" {{ isCheckedRm3f($row, 'NEURO_TINGKAT_KESAD') }}><label class="form-check-label" for="NEURO_TINGKAT_KESAD">Penurunan Tingkat Kesadaran</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="NEURO_FRAKTUR" value="1" id="NEURO_FRAKTUR" {{ isCheckedRm3f($row, 'NEURO_FRAKTUR') }}><label class="form-check-label" for="NEURO_FRAKTUR">Fraktur/Dislokasi/Luksaslo</label></div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <!-- Integument -->
                    <div class="col-md-6 mb-3">
                        <fieldset class="border p-3 rounded h-100">
                            <legend class="fs-6 fw-bold w-auto px-2">Integument:</legend>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="INTEGUMENT_NORMAL" value="1" id="INTEGUMENT_NORMAL" {{ isCheckedRm3f($row, 'INTEGUMENT_NORMAL') }}><label class="form-check-label" for="INTEGUMENT_NORMAL">Normal</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="INTEGUMENT_BAKAR" value="1" id="INTEGUMENT_BAKAR" {{ isCheckedRm3f($row, 'INTEGUMENT_BAKAR') }}><label class="form-check-label" for="INTEGUMENT_BAKAR">Luka Bakar</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="INTEGUMENT_ROBEK" value="1" id="INTEGUMENT_ROBEK" {{ isCheckedRm3f($row, 'INTEGUMENT_ROBEK') }}><label class="form-check-label" for="INTEGUMENT_ROBEK">Luka Robek</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="INTEGUMENT_LECET" value="1" id="INTEGUMENT_LECET" {{ isCheckedRm3f($row, 'INTEGUMENT_LECET') }}><label class="form-check-label" for="INTEGUMENT_LECET">Lecet</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="INTEGUMENT_DEKUBITUS" value="1" id="INTEGUMENT_DEKUBITUS" {{ isCheckedRm3f($row, 'INTEGUMENT_DEKUBITUS') }}><label class="form-check-label" for="INTEGUMENT_DEKUBITUS">Luka Dekubitus</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="INTEGUMENT_GANGREN" value="1" id="INTEGUMENT_GANGREN" {{ isCheckedRm3f($row, 'INTEGUMENT_GANGREN') }}><label class="form-check-label" for="INTEGUMENT_GANGREN">Luka Gangren</label></div>
                        </fieldset>
                    </div>
                    <!-- Edema -->
                    <div class="col-md-6 mb-3">
                        <fieldset class="border p-3 rounded h-100">
                            <legend class="fs-6 fw-bold w-auto px-2">Edema:</legend>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="EDEMA_TIDAK_ADA" value="1" id="EDEMA_TIDAK_ADA" {{ isCheckedRm3f($row, 'EDEMA_TIDAK_ADA') }}><label class="form-check-label" for="EDEMA_TIDAK_ADA">Tidak Ada</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="EDEMA_EXTRIMITAS" value="1" id="EDEMA_EXTRIMITAS" {{ isCheckedRm3f($row, 'EDEMA_EXTRIMITAS') }}><label class="form-check-label" for="EDEMA_EXTRIMITAS">Ekstremitas</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="EDEMA_TUBUH" value="1" id="EDEMA_TUBUH" {{ isCheckedRm3f($row, 'EDEMA_TUBUH') }}><label class="form-check-label" for="EDEMA_TUBUH">Seluruh Tubuh</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="EDEMA_ASCITES" value="1" id="EDEMA_ASCITES" {{ isCheckedRm3f($row, 'EDEMA_ASCITES') }}><label class="form-check-label" for="EDEMA_ASCITES">Ascites</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="EDEMA_PALPEBRA" value="1" id="EDEMA_PALPEBRA" {{ isCheckedRm3f($row, 'EDEMA_PALPEBRA') }}><label class="form-check-label" for="EDEMA_PALPEBRA">Palpebra</label></div>
                        </fieldset>
                    </div>
                    <!-- Turgor Kulit & Mukosa Mulut -->
                    <div class="col-md-6 mb-3">
                        <fieldset class="border p-3 rounded h-100">
                            <legend class="fs-6 fw-bold w-auto px-2">Turgor Kulit & Mukosa Mulut:</legend>
                            <label class="form-label d-block">Turgor Kulit:</label>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="TURGOR_KULIT_BAIK" value="1" id="TURGOR_KULIT_BAIK" {{ isCheckedRm3f($row, 'TURGOR_KULIT_BAIK') }}><label class="form-check-label" for="TURGOR_KULIT_BAIK">Baik</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="TURGOR_KULIT_LEMBAB" value="1" id="TURGOR_KULIT_LEMBAB" {{ isCheckedRm3f($row, 'TURGOR_KULIT_LEMBAB') }}><label class="form-check-label" for="TURGOR_KULIT_LEMBAB">Menurun</label></div>
                            <hr class="my-2">
                            <label class="form-label d-block">Mukosa Mulut:</label>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="MUKOSA_KERING" value="1" id="MUKOSA_KERING" {{ isCheckedRm3f($row, 'MUKOSA_KERING') }}><label class="form-check-label" for="MUKOSA_KERING">Kering</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="MUKOSA_LEMBAB" value="1" id="MUKOSA_LEMBAB" {{ isCheckedRm3f($row, 'MUKOSA_LEMBAB') }}><label class="form-check-label" for="MUKOSA_LEMBAB">Lembab</label></div>
                        </fieldset>
                    </div>
                    <!-- Perdarahan -->
                    <div class="col-md-6 mb-3">
                        <fieldset class="border p-3 rounded h-100">
                            <legend class="fs-6 fw-bold w-auto px-2">Perdarahan:</legend>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="PERDARAHAN_TDK_ADA" value="1" id="PERDARAHAN_TDK_ADA" {{ isCheckedRm3f($row, 'PERDARAHAN_TDK_ADA') }}><label class="form-check-label" for="PERDARAHAN_TDK_ADA">Tidak Ada</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="PERDARAHAN" value="1" id="PERDARAHAN" {{ isCheckedRm3f($row, 'PERDARAHAN') }}><label class="form-check-label" for="PERDARAHAN">Ada</label></div>
                            <div id="perdarahan-detail" class="mt-2" style="display: {{ isCheckedRm3f($row, 'PERDARAHAN') ? 'block' : 'none' }};">
                                <div class="row g-2">
                                    <div class="col-md-6"><label for="PERDARAHAN_JUMLAH" class="form-label">Jumlah:</label><input type="text" id="PERDARAHAN_JUMLAH" name="PERDARAHAN_JUMLAH" class="form-control" value="{{ getValueRm3f($row, 'PERDARAHAN_JUMLAH') }}"></div>
                                    <div class="col-md-6"><label for="PERDARAHAN_WARNA" class="form-label">Warna:</label><input type="text" id="PERDARAHAN_WARNA" name="PERDARAHAN_WARNA" class="form-control" value="{{ getValueRm3f($row, 'PERDARAHAN_WARNA') }}"></div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <!-- Intoksikasi -->
                    <div class="col-md-12 mb-3">
                        <fieldset class="border p-3 rounded">
                            <legend class="fs-6 fw-bold w-auto px-2">Intoksikasi:</legend>
                            <div class="row">
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="INTOKSIKASI_TIDAK" value="1" id="INTOKSIKASI_TIDAK" {{ isCheckedRm3f($row, 'INTOKSIKASI_TIDAK') }}><label class="form-check-label" for="INTOKSIKASI_TIDAK">Tidak</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="INTOKSIKASI_MAKANAN" value="1" id="INTOKSIKASI_MAKANAN" {{ isCheckedRm3f($row, 'INTOKSIKASI_MAKANAN') }}><label class="form-check-label" for="INTOKSIKASI_MAKANAN">Makanan</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="INTOKSIKASI_GIGIT" value="1" id="INTOKSIKASI_GIGIT" {{ isCheckedRm3f($row, 'INTOKSIKASI_GIGIT') }}><label class="form-check-label" for="INTOKSIKASI_GIGIT">Gigitan Binatang</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="INTOKSIKASI_ZAT" value="1" id="INTOKSIKASI_ZAT" {{ isCheckedRm3f($row, 'INTOKSIKASI_ZAT') }}><label class="form-check-label" for="INTOKSIKASI_ZAT">Zat Kimia</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="INTOKSIKASI_GAS" value="1" id="INTOKSIKASI_GAS" {{ isCheckedRm3f($row, 'INTOKSIKASI_GAS') }}><label class="form-check-label" for="INTOKSIKASI_GAS">Gas</label></div></div>
                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="INTOKSIKASI_OBAT" value="1" id="INTOKSIKASI_OBAT" {{ isCheckedRm3f($row, 'INTOKSIKASI_OBAT') }}><label class="form-check-label" for="INTOKSIKASI_OBAT">Obat</label></div></div>
                            </div>
                        </fieldset>
                    </div>
                    <!-- Eliminasi -->
                    <div class="col-md-12 mb-3">
                        <fieldset class="border p-3 rounded">
                            <legend class="fs-6 fw-bold w-auto px-2">Eliminasi:</legend>
                            <div class="row g-3">
                                <div class="col-12"><h6 class="text-primary fw-bold border-bottom pb-1 mb-2">BAB</h6></div>
                                <div class="col-md-3">
                                    <label for="ELIMINASI_FREKUENSI" class="form-label">Frekuensi:</label>
                                    <div class="input-group"><input type="text" id="ELIMINASI_FREKUENSI" name="ELIMINASI_FREKUENSI" class="form-control" value="{{ getValueRm3f($row, 'ELIMINASI_FREKUENSI') }}"><span class="input-group-text">x/hari</span></div>
                                </div>
                                <div class="col-md-3"><label for="ELIMINASI_KONSISTENSI" class="form-label">Konsistensi:</label><input type="text" id="ELIMINASI_KONSISTENSI" name="ELIMINASI_KONSISTENSI" class="form-control" value="{{ getValueRm3f($row, 'ELIMINASI_KONSISTENSI') }}"></div>
                                <div class="col-md-3"><label for="ELIMINASI_WARNA" class="form-label">Warna:</label><input type="text" id="ELIMINASI_WARNA" name="ELIMINASI_WARNA" class="form-control" value="{{ getValueRm3f($row, 'ELIMINASI_WARNA') }}"></div>
                                <div class="col-md-3"><label for="URINE" class="form-label">Urine:</label><input type="text" id="URINE" name="URINE" class="form-control" value="{{ getValueRm3f($row, 'URINE') }}"></div>
                            </div>
                        </fieldset>
                    </div>
                    <!-- Riwayat Alergi -->
                    <div class="col-md-12">
                        <fieldset class="border p-3 rounded">
                            <legend class="fs-6 fw-bold w-auto px-2">Riwayat Alergi:</legend>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="RIWAYAT_ALERGI_TIDAK" value="1" id="RIWAYAT_ALERGI_TIDAK" {{ isCheckedRm3f($row, 'RIWAYAT_ALERGI_TIDAK') }}><label class="form-check-label" for="RIWAYAT_ALERGI_TIDAK">Tidak</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="RIWAYAT_ALERGI_ADA" value="1" id="RIWAYAT_ALERGI_ADA" {{ isCheckedRm3f($row, 'RIWAYAT_ALERGI_ADA') }}><label class="form-check-label" for="RIWAYAT_ALERGI_ADA">Ada</label></div>
                            <input type="text" class="form-control mt-2" name="RIWAYAT_ALERGI_ADA_KET" id="RIWAYAT_ALERGI_ADA_KET" placeholder="Keterangan alergi..." value="{{ getValueRm3f($row, 'RIWAYAT_ALERGI_ADA_KET') }}" style="display: {{ isCheckedRm3f($row, 'RIWAYAT_ALERGI_ADA') ? 'block' : 'none' }};">
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengkajian Primer -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white"><h5 class="mb-0">PENGKAJIAN PRIMER</h5></div>
            <div class="card-body">
                <!-- Airway -->
                <div class="mb-4 border-bottom pb-3">
                    <h5 class="mb-3 fw-bold text-success">Airway</h5>
                    <div class="row">
                        <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="AIRWAY_NORMAL" value="1" id="AIRWAY_NORMAL" {{ isCheckedRm3f($row, 'AIRWAY_NORMAL') }}><label class="form-check-label" for="AIRWAY_NORMAL">Normal</label></div></div>
                        <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="AIRWAY_SUMBAT" value="1" id="AIRWAY_SUMBAT" {{ isCheckedRm3f($row, 'AIRWAY_SUMBAT') }}><label class="form-check-label" for="AIRWAY_SUMBAT">Sumbatan</label></div></div>
                        <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="AIRWAY_BENDA" value="1" id="AIRWAY_BENDA" {{ isCheckedRm3f($row, 'AIRWAY_BENDA') }}><label class="form-check-label" for="AIRWAY_BENDA">Benda Asing</label></div></div>
                        <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="AIRWAY_DARAH" value="1" id="AIRWAY_DARAH" {{ isCheckedRm3f($row, 'AIRWAY_DARAH') }}><label class="form-check-label" for="AIRWAY_DARAH">Darah</label></div></div>
                        <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="AIRWAY_SPUTUM" value="1" id="AIRWAY_SPUTUM" {{ isCheckedRm3f($row, 'AIRWAY_SPUTUM') }}><label class="form-check-label" for="AIRWAY_SPUTUM">Sputum</label></div></div>
                        <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="AIRWAY_LENDIR" value="1" id="AIRWAY_LENDIR" {{ isCheckedRm3f($row, 'AIRWAY_LENDIR') }}><label class="form-check-label" for="AIRWAY_LENDIR">Lendir</label></div></div>
                        <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="AIRWAY_BRONCHOSPASME" value="1" id="AIRWAY_BRONCHOSPASME" {{ isCheckedRm3f($row, 'AIRWAY_BRONCHOSPASME') }}><label class="form-check-label" for="AIRWAY_BRONCHOSPASME">Bronchospasme</label></div></div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="form-check me-2"><input class="form-check-input" type="checkbox" name="AIRWAY_LAIN" value="1" id="AIRWAY_LAIN" {{ isCheckedRm3f($row, 'AIRWAY_LAIN') }}><label class="form-check-label" for="AIRWAY_LAIN">Lainnya:</label></div>
                                <input type="text" id="AIRWAY_KET_LAIN" name="AIRWAY_KET_LAIN" class="form-control form-control-sm flex-grow-1" placeholder="Keterangan Lainnya" value="{{ getValueRm3f($row, 'AIRWAY_KET_LAIN') }}" style="display: {{ isCheckedRm3f($row, 'AIRWAY_LAIN') ? 'block' : 'none' }};">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Breathing -->
                <div class="mb-4 border-bottom pb-3">
                    <h5 class="mb-3 fw-bold text-success">Breathing</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">Sesak Nafas:</legend>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="BREATH_SESAK_TIDAK" value="1" id="BREATH_SESAK_TIDAK" {{ isCheckedRm3f($row, 'BREATH_SESAK_TIDAK') }}><label class="form-check-label" for="BREATH_SESAK_TIDAK">Tidak Sesak</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="BREATH_SESAK_YA" value="1" id="BREATH_SESAK_YA" {{ isCheckedRm3f($row, 'BREATH_SESAK_YA') }}><label class="form-check-label" for="BREATH_SESAK_YA">Sesak</label></div>
                                <div id="sesakDetailOptions" class="mt-2 ps-3 border-start border-3 border-secondary" style="display:{{ isCheckedRm3f($row, 'BREATH_SESAK_YA') ? 'block' : 'none' }};">
                                    <div class="form-check"><input class="form-check-input sesak-detail" type="checkbox" name="BREATH_SESAK_TNP_AKTIF" value="1" id="BREATH_SESAK_TNP_AKTIF" {{ isCheckedRm3f($row, 'BREATH_SESAK_TNP_AKTIF') }}><label class="form-check-label" for="BREATH_SESAK_TNP_AKTIF">Tanpa Aktivitas</label></div>
                                    <div class="form-check"><input class="form-check-input sesak-detail" type="checkbox" name="BREATH_SESAK_AKTIF" value="1" id="BREATH_SESAK_AKTIF" {{ isCheckedRm3f($row, 'BREATH_SESAK_AKTIF') }}><label class="form-check-label" for="BREATH_SESAK_AKTIF">Dengan Aktivitas</label></div>
                                    <div class="form-check"><input class="form-check-input sesak-detail" type="checkbox" name="BREATH_SESAK_OTOT" value="1" id="BREATH_SESAK_OTOT" {{ isCheckedRm3f($row, 'BREATH_SESAK_OTOT') }}><label class="form-check-label" for="BREATH_SESAK_OTOT">Otot Bantu Napas</label></div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">Pernapasan:</legend>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="BREATH_FREKUENSI" class="form-label">Frekuensi:</label>
                                        <div class="input-group"><input type="text" id="BREATH_FREKUENSI" name="BREATH_FREKUENSI" class="form-control" value="{{ getValueRm3f($row, 'PACS2_NAFAS') }}"><span class="input-group-text">x/mnt</span></div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Irama:</label>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="BREATH_SESAK_TERATUR" value="1" id="BREATH_SESAK_TERATUR" {{ isCheckedRm3f($row, 'BREATH_SESAK_TERATUR') }}><label class="form-check-label" for="BREATH_SESAK_TERATUR">Teratur</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="BREATH_SESAK_TDK_TERATUR" value="1" id="BREATH_SESAK_TDK_TERATUR" {{ isCheckedRm3f($row, 'BREATH_SESAK_TDK_TERATUR') }}><label class="form-check-label" for="BREATH_SESAK_TDK_TERATUR">Tidak Teratur</label></div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <!-- Circulation -->
                <div class="mb-4 border-bottom pb-3">
                    <h5 class="mb-3 fw-bold text-success">Circulation</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="CIRCU_TD" class="form-label">Tekanan Darah:</label>
                            <div class="input-group"><input type="text" id="CIRCU_TD" name="CIRCU_TD" class="form-control" value="{{ getValueRm3f($row, 'PACS1_TD') }}"><span class="input-group-text">mmHg</span></div>
                        </div>
                        <div class="col-md-4">
                            <label for="CIRCU_NADI" class="form-label">Nadi:</label>
                            <div class="input-group mb-2"><input type="text" id="CIRCU_NADI" name="CIRCU_NADI" class="form-control" value="{{ getValueRm3f($row, 'PACS1_NADI') }}"><span class="input-group-text">x/mnt</span></div>
                            <div class="d-flex justify-content-around">
                                <div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="CIRCU_NADI_KUAT" value="1" id="CIRCU_NADI_KUAT" {{ isCheckedRm3f($row, 'CIRCU_NADI_KUAT') }}><label class="form-check-label" for="CIRCU_NADI_KUAT">Kuat</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="CIRCU_NADI_LEMAH" value="1" id="CIRCU_NADI_LEMAH" {{ isCheckedRm3f($row, 'CIRCU_NADI_LEMAH') }}><label class="form-check-label" for="CIRCU_NADI_LEMAH">Lemah</label></div>
                                </div>
                                <div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="CIRCU_NADI_TERATUR" value="1" id="CIRCU_NADI_TERATUR" {{ isCheckedRm3f($row, 'CIRCU_NADI_TERATUR') }}><label class="form-check-label" for="CIRCU_NADI_TERATUR">Teratur</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="CIRCU_NADI_TDK_TERATUR" value="1" id="CIRCU_NADI_TDK_TERATUR" {{ isCheckedRm3f($row, 'CIRCU_NADI_TDK_TERATUR') }}><label class="form-check-label" for="CIRCU_NADI_TDK_TERATUR">Tidak Teratur</label></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="CIRCU_SATURASI" class="form-label">Saturasi O₂:</label>
                            <div class="input-group"><input type="text" id="CIRCU_SATURASI" name="CIRCU_SATURASI" class="form-control" value="{{ getValueRm3f($row, 'PACS3_SATURASI') }}"><span class="input-group-text">%</span></div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">CRT:</legend>
                                <div class="form-check"><input class="form-check-input crt-checkbox" type="checkbox" name="CIRCU_LBH2DTK" value="1" id="CIRCU_LBH2DTK" {{ isCheckedRm3f($row, 'CIRCU_LBH2DTK') }}><label class="form-check-label" for="CIRCU_LBH2DTK">&gt; 2 detik</label></div>
                                <div class="form-check"><input class="form-check-input crt-checkbox" type="checkbox" name="CIRCU_KRNG2DTK" value="1" id="CIRCU_KRNG2DTK" {{ isCheckedRm3f($row, 'CIRCU_KRNG2DTK') }}><label class="form-check-label" for="CIRCU_KRNG2DTK">≤ 2 detik</label></div>
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">Ekstremitas:</legend>
                                <div class="form-check"><input class="form-check-input extremitas-checkbox" type="checkbox" name="CIRCU_HANGAT" value="1" id="CIRCU_HANGAT" {{ isCheckedRm3f($row, 'CIRCU_HANGAT') }}><label class="form-check-label" for="CIRCU_HANGAT">Hangat</label></div>
                                <div class="form-check"><input class="form-check-input extremitas-checkbox" type="checkbox" name="CIRCU_OEDEM" value="1" id="CIRCU_OEDEM" {{ isCheckedRm3f($row, 'CIRCU_OEDEM') }}><label class="form-check-label" for="CIRCU_OEDEM">Oedem</label></div>
                                <div class="form-check"><input class="form-check-input extremitas-checkbox" type="checkbox" name="CIRCU_DINGIN" value="1" id="CIRCU_DINGIN" {{ isCheckedRm3f($row, 'CIRCU_DINGIN') }}><label class="form-check-label" for="CIRCU_DINGIN">Dingin</label></div>
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">Turgor Kulit:</legend>
                                <div class="form-check"><input class="form-check-input turgor-checkbox" type="checkbox" name="CIRCU_BAIK" value="1" id="CIRCU_BAIK" {{ isCheckedRm3f($row, 'CIRCU_BAIK') }}><label class="form-check-label" for="CIRCU_BAIK">Baik</label></div>
                                <div class="form-check"><input class="form-check-input turgor-checkbox" type="checkbox" name="CIRCU_KURANG" value="1" id="CIRCU_KURANG" {{ isCheckedRm3f($row, 'CIRCU_KURANG') }}><label class="form-check-label" for="CIRCU_KURANG">Kurang</label></div>
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">Warna Kulit:</legend>
                                <div class="form-check"><input class="form-check-input warna-kulit" type="checkbox" name="CIRCU_NORMAL" value="1" id="CIRCU_NORMAL" {{ isCheckedRm3f($row, 'CIRCU_NORMAL') }}><label class="form-check-label" for="CIRCU_NORMAL">Normal</label></div>
                                <div class="form-check"><input class="form-check-input warna-kulit" type="checkbox" name="CIRCU_PUCAT" value="1" id="CIRCU_PUCAT" {{ isCheckedRm3f($row, 'CIRCU_PUCAT') }}><label class="form-check-label" for="CIRCU_PUCAT">Pucat</label></div>
                                <div class="form-check"><input class="form-check-input warna-kulit" type="checkbox" name="CIRCU_CYANOSIS" value="1" id="CIRCU_CYANOSIS" {{ isCheckedRm3f($row, 'CIRCU_CYANOSIS') }}><label class="form-check-label" for="CIRCU_CYANOSIS">Cyanosis</label></div>
                                <div class="form-check"><input class="form-check-input warna-kulit" type="checkbox" name="CIRCU_IKRETIK" value="1" id="CIRCU_IKRETIK" {{ isCheckedRm3f($row, 'CIRCU_IKRETIK') }}><label class="form-check-label" for="CIRCU_IKRETIK">Ikterik</label></div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <!-- Disability -->
                <div class="mb-4 border-bottom pb-3">
                    <h5 class="mb-3 fw-bold text-success">Disability</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="DISAB_KESADARAN" class="form-label">Kesadaran:</label>
                            <input type="text" id="DISAB_KESADARAN" name="DISAB_KESADARAN" class="form-control" value="{{ getValueRm3f($row, 'KESADARAN') }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GCS (E/V/M):</label>
                            <div class="input-group">
                                <span class="input-group-text">E</span>
                                <input type="number" class="form-control gcs-input-rm3f" id="DISAB_GCS_E" name="DISAB_GCS_E" min="1" max="4" value="{{ getValueRm3f($row, 'GCS_E') }}">
                                <span class="input-group-text">V</span>
                                <input type="number" class="form-control gcs-input-rm3f" id="DISAB_GCS_V" name="DISAB_GCS_V" min="1" max="5" value="{{ getValueRm3f($row, 'GCS_V') }}">
                                <span class="input-group-text">M</span>
                                <input type="number" class="form-control gcs-input-rm3f" id="DISAB_GCS_M" name="DISAB_GCS_M" min="1" max="6" value="{{ getValueRm3f($row, 'GCS_M') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="DISAB_PUPIL" class="form-label">Pupil:</label>
                            <input type="text" id="DISAB_PUPIL" name="DISAB_PUPIL" class="form-control mb-2" value="{{ getValueRm3f($row, 'DISAB_PUPIL') }}" placeholder="Ukuran/Reaksi...">
                            <div class="form-check form-check-inline"><input class="form-check-input pupil" type="checkbox" name="DISAB_PUPIL_ISOKOR" value="1" id="DISAB_PUPIL_ISOKOR" {{ isCheckedRm3f($row, 'DISAB_PUPIL_ISOKOR') }}><label class="form-check-label" for="DISAB_PUPIL_ISOKOR">Isokor</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input pupil" type="checkbox" name="DISAB_PUPIL_ANISOKOR" value="1" id="DISAB_PUPIL_ANISOKOR" {{ isCheckedRm3f($row, 'DISAB_PUPIL_ANISOKOR') }}><label class="form-check-label" for="DISAB_PUPIL_ANISOKOR">Anisokor</label></div>
                        </div>
                    </div>
                </div>

                <!-- Exposure -->
                <div>
                    <h5 class="mb-3 fw-bold text-success">Exposure</h5>
                    <fieldset class="border p-3 rounded">
                        <legend class="fs-6 fw-bold w-auto px-2">Luka:</legend>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="EXPOSURE_LUKA_TIDKA" name="EXPOSURE_LUKA_TIDKA" value="1" {{ isCheckedRm3f($row, 'EXPOSURE_LUKA_TIDKA') }}><label class="form-check-label" for="EXPOSURE_LUKA_TIDKA">Tidak Ada</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="EXPOSURE_LUKA_ADA" name="EXPOSURE_LUKA_ADA" value="1" {{ isCheckedRm3f($row, 'EXPOSURE_LUKA_ADA') }}><label class="form-check-label" for="EXPOSURE_LUKA_ADA">Ada</label></div>
                        <div id="exposureForm" class="mt-3" style="display: {{ isCheckedRm3f($row, 'EXPOSURE_LUKA_ADA') ? 'block' : 'none' }};">
                            <div class="row g-3">
                                <div class="col-md-6"><label for="EXPOSURE_LUKA" class="form-label">Keadaan Luka:</label><input type="text" id="EXPOSURE_LUKA" name="EXPOSURE_LUKA" class="form-control" value="{{ getValueRm3f($row, 'EXPOSURE_LUKA') }}"></div>
                                <div class="col-md-6"><label for="EXPOSURE_KEDALAMAN" class="form-label">Kedalaman:</label><input type="text" id="EXPOSURE_KEDALAMAN" name="EXPOSURE_KEDALAMAN" class="form-control" value="{{ getValueRm3f($row, 'EXPOSURE_KEDALAMAN') }}"></div>
                                <div class="col-md-6"><label for="EXPOSURE_PERDARAHAN" class="form-label">Perdarahan:</label><input type="text" id="EXPOSURE_PERDARAHAN" name="EXPOSURE_PERDARAHAN" class="form-control" value="{{ getValueRm3f($row, 'EXPOSURE_PERDARAHAN') }}"></div>
                                <div class="col-md-6"><label for="EXPOSURE_FRAKTUR" class="form-label">Fraktur/dislokasi:</label><input type="text" id="EXPOSURE_FRAKTUR" name="EXPOSURE_FRAKTUR" class="form-control" value="{{ getValueRm3f($row, 'EXPOSURE_FRAKTUR') }}"></div>
                                <div class="col-12"><label for="EXPOSURE_LOKASI" class="form-label">Lokasi:</label><input type="text" id="EXPOSURE_LOKASI" name="EXPOSURE_LOKASI" class="form-control" value="{{ getValueRm3f($row, 'EXPOSURE_LOKASI') }}"></div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <!-- Asesmen Nyeri -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white"><h5 class="mb-0">ASESMEN NYERI</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <fieldset class="border p-3 rounded">
                        <legend class="fs-6 fw-bold w-auto px-2">Apakah Terdapat Keluhan Nyeri?</legend>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="NYERI_TIDAK" value="1" id="NYERI_TIDAK" {{ isCheckedRm3f($row, 'NYERI_TIDAK') }}><label class="form-check-label" for="NYERI_TIDAK">Tidak</label></div>
                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="NYERI_YA" value="1" id="NYERI_YA" {{ isCheckedRm3f($row, 'NYERI_YA3B') }}><label class="form-check-label" for="NYERI_YA">Ya</label></div>
                    </fieldset>
                </div>
                <div id="nyeriForm" style="display: {{ isCheckedRm3f($row, 'NYERI_YA3B') ? 'block' : 'none' }};">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="SKALA_NYERI" class="form-label fw-bold">Skala Nyeri:</label>
                            <input type="text" id="SKALA_NYERI" name="SKALA_NYERI" class="form-control" value="{{ getValueRm3f($row, 'NYERI_YA_KET') }}">
                        </div>
                        <div class="col-md-8">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">Metode Penilaian:</legend>
                                <div class="row">
                                    <div class="col-sm-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="METODE_VAS" value="1" id="METODE_VAS" {{ isCheckedRm3f($row, 'VAS') }}><label class="form-check-label" for="METODE_VAS">VAS</label></div></div>
                                    <div class="col-sm-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="METODE_WONGBAKER" value="1" id="METODE_WONGBAKER" {{ isCheckedRm3f($row, 'WONG') }}><label class="form-check-label" for="METODE_WONGBAKER">Wong-Baker</label></div></div>
                                    <div class="col-sm-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="METODE_FLACC" value="1" id="METODE_FLACC" {{ isCheckedRm3f($row, 'FLACC') }}><label class="form-check-label" for="METODE_FLACC">FLACC</label></div></div>
                                    <div class="col-sm-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="METODE_NIPS" value="1" id="METODE_NIPS" {{ isCheckedRm3f($row, 'NIPS') }}><label class="form-check-label" for="METODE_NIPS">NIPS</label></div></div>
                                    <div class="col-sm-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="METODE_SKOR" value="1" id="METODE_SKOR" {{ isCheckedRm3f($row, 'METODE_SKOR') }}><label class="form-check-label" for="METODE_SKOR">Skor</label></div></div>
                                    <div class="col-sm-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="METODE_KATEGORI" value="1" id="METODE_KATEGORI" {{ isCheckedRm3f($row, 'METODE_KATEGORI') }}><label class="form-check-label" for="METODE_KATEGORI">Kategori</label></div></div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">Sifat Nyeri:</legend>
                                <label class="form-label d-block">Apakah berpindah?</label>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="NYERI_PINDAH_TIDAK" value="1" id="NYERI_PINDAH_TIDAK" {{ isCheckedRm3f($row, 'NYERI_PINDAH_TIDAK') }}><label class="form-check-label" for="NYERI_PINDAH_TIDAK">Tidak</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="NYERI_PINDAH_YA" value="1" id="NYERI_PINDAH_YA" {{ isCheckedRm3f($row, 'NYERI_PINDAH_YA') }}><label class="form-check-label" for="NYERI_PINDAH_YA">Ya</label></div>
                                <hr class="my-2">
                                <label class="form-label d-block">Berapa lama?</label>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="LAMA_NYERI_AKUT" value="1" id="LAMA_NYERI_AKUT" {{ isCheckedRm3f($row, 'LAMA_NYERI_AKUT') }}><label class="form-check-label" for="LAMA_NYERI_AKUT">&lt; 3 Bln (Akut)</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="LAMA_NYERI_KRONIK" value="1" id="LAMA_NYERI_KRONIK" {{ isCheckedRm3f($row, 'LAMA_NYERI_KRONIK') }}><label class="form-check-label" for="LAMA_NYERI_KRONIK">&gt; 3 Bln (Kronik)</label></div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="border p-3 rounded h-100">
                                <legend class="fs-6 fw-bold w-auto px-2">Frekuensi Nyeri:</legend>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="NYERI_1JAM" value="1" id="NYERI_1JAM" {{ isCheckedRm3f($row, 'NYERI_1JAM') }}><label class="form-check-label" for="NYERI_1JAM">1-2 Jam</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="NYERI_3JAM" value="1" id="NYERI_3JAM" {{ isCheckedRm3f($row, 'NYERI_3JAM') }}><label class="form-check-label" for="NYERI_3JAM">3-4 Jam</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="NYERI_KRNG30MNT" value="1" id="NYERI_KRNG30MNT" {{ isCheckedRm3f($row, 'NYERI_KRNG30MNT') }}><label class="form-check-label" for="NYERI_KRNG30MNT">&lt; 30 Menit</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="NYERI_LBH30MNT" value="1" id="NYERI_LBH30MNT" {{ isCheckedRm3f($row, 'NYERI_LBH30MNT') }}><label class="form-check-label" for="NYERI_LBH30MNT">&gt; 30 Menit</label></div>
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <fieldset class="border p-3 rounded">
                                <legend class="fs-6 fw-bold w-auto px-2">Kualitas Rasa Nyeri:</legend>
                                <div class="row">
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_TAJAM" value="1" id="RASA_TAJAM" {{ isCheckedRm3f($row, 'RASA_TAJAM') }}><label class="form-check-label" for="RASA_TAJAM">Tajam</label></div></div>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_TUMPUL" value="1" id="RASA_TUMPUL" {{ isCheckedRm3f($row, 'RASA_TUMPUL') }}><label class="form-check-label" for="RASA_TUMPUL">Nyeri Tumpul</label></div></div>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_DITARIK" value="1" id="RASA_DITARIK" {{ isCheckedRm3f($row, 'RASA_DITARIK') }}><label class="form-check-label" for="RASA_DITARIK">Nyeri Ditarik</label></div></div>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_DISUTUK" value="1" id="RASA_DISUTUK" {{ isCheckedRm3f($row, 'RASA_DISUTUK') }}><label class="form-check-label" for="RASA_DISUTUK">Seperti ditusuk</label></div></div>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_DIPUKUL" value="1" id="RASA_DIPUKUL" {{ isCheckedRm3f($row, 'RASA_DIPUKUL') }}><label class="form-check-label" for="RASA_DIPUKUL">Seperti dipukul</label></div></div>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_DIBAKAR" value="1" id="RASA_DIBAKAR" {{ isCheckedRm3f($row, 'RASA_DIBAKAR') }}><label class="form-check-label" for="RASA_DIBAKAR">Seperti Dibakar</label></div></div>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_BERDENYUT" value="1" id="RASA_BERDENYUT" {{ isCheckedRm3f($row, 'RASA_BERDENYUT') }}><label class="form-check-label" for="RASA_BERDENYUT">Seperti Berdenyut</label></div></div>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_DITIKAM" value="1" id="RASA_DITIKAM" {{ isCheckedRm3f($row, 'RASA_DITIKAM') }}><label class="form-check-label" for="RASA_DITIKAM">Seperti ditikam</label></div></div>
                                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="RASA_KRAM" value="1" id="RASA_KRAM" {{ isCheckedRm3f($row, 'RASA_KRAM') }}><label class="form-check-label" for="RASA_KRAM">Seperti Kram</label></div></div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-12">
                            <fieldset class="border p-3 rounded">
                                <legend class="fs-6 fw-bold w-auto px-2">Faktor yang Mengurangi Nyeri:</legend>
                                <div class="row">
                                    <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="NYERI_KOMPRES" value="1" id="NYERI_KOMPRES" {{ isCheckedRm3f($row, 'NYERI_KOMPRES') }}><label class="form-check-label" for="NYERI_KOMPRES">Kompres Hangat/Dingin</label></div></div>
                                    <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="NYERI_AKTIFITAS" value="1" id="NYERI_AKTIFITAS" {{ isCheckedRm3f($row, 'NYERI_AKTIFITAS') }}><label class="form-check-label" for="NYERI_AKTIFITAS">Aktivitas Dikurangi</label></div></div>
                                    <div class="col-md-12 mt-2">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check me-2"><input class="form-check-input" type="checkbox" name="NYERI_LAIN" value="1" id="NYERI_LAIN" {{ isCheckedRm3f($row, 'NYERI_LAIN') }}><label class="form-check-label" for="NYERI_LAIN">Lain-lain:</label></div>
                                            <div id="NYERI_LAIN_KET_CONTAINER" class="flex-grow-1" style="{{ isCheckedRm3f($row, 'NYERI_LAIN') ? '' : 'display: none;' }}"><input type="text" id="NYERI_LAIN_KET" name="NYERI_LAIN_KET" class="form-control" placeholder="Keterangan Lainnya" value="{{ getValueRm3f($row, 'NYERI_LAIN_KET') }}"></div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diagnosa dan Rencana -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white"><h5 class="mb-0">DIAGNOSA DAN RENCANA</h5></div>
            <div class="card-body">
                <div class="mb-3"><label for="DIAGNOSA_KPRWTN" class="form-label fw-bold">Diagnosa Keperawatan:</label><textarea id="DIAGNOSA_KPRWTN" name="DIAGNOSA_KPRWTN" class="form-control" rows="3">{{ getValueRm3f($row, 'DIAGNOSA_KPRWTN') }}</textarea></div>
                <div class="mb-3"><label for="RENCANA_KPRWTN" class="form-label fw-bold">Rencana Keperawatan:</label><textarea id="RENCANA_KPRWTN" name="RENCANA_KPRWTN" class="form-control" rows="3">{{ getValueRm3f($row, 'RENCANA_KPRWTN') }}</textarea></div>
                <div class="mb-3"><label for="TINDAKAN" class="form-label fw-bold">Tindakan:</label><textarea id="TINDAKAN" name="TINDAKAN" class="form-control" rows="3">{{ getValueRm3f($row, 'TINDAKAN') }}</textarea></div>
                <div><label for="EVALUASI" class="form-label fw-bold">Evaluasi:</label><textarea id="EVALUASI" name="EVALUASI" class="form-control" rows="3">{{ getValueRm3f($row, 'EVALUASI') }}</textarea></div>
            </div>
        </div>

        <!-- Tindakan Medis -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white"><h5 class="mb-0">TINDAKAN MEDIS</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="OKSIGENISASI" value="1" id="OKSIGENISASI" {{ isCheckedRm3f($row, 'OKSIGENISASI') }}><label class="form-check-label" for="OKSIGENISASI">Oksigenisasi</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="NEBULIZER" value="1" id="NEBULIZER" {{ isCheckedRm3f($row, 'NEBULIZER') }}><label class="form-check-label" for="NEBULIZER">Nebulizer</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="IVFD" value="1" id="IVFD" {{ isCheckedRm3f($row, 'IVFD') }}><label class="form-check-label" for="IVFD">IVFD</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="EKG" value="1" id="EKG" {{ isCheckedRm3f($row, 'EKG') }}><label class="form-check-label" for="EKG">EKG</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="TRANSFUSI" value="1" id="TRANSFUSI" {{ isCheckedRm3f($row, 'TRANSFUSI') }}><label class="form-check-label" for="TRANSFUSI">Transfusi</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="NGT" value="1" id="NGT" {{ isCheckedRm3f($row, 'NGT') }}><label class="form-check-label" for="NGT">NGT</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="DC_SHOCK" value="1" id="DC_SHOCK" {{ isCheckedRm3f($row, 'DC_SHOCK') }}><label class="form-check-label" for="DC_SHOCK">DC Shock</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="EKSPLORASI" value="1" id="EKSPLORASI" {{ isCheckedRm3f($row, 'EKSPLORASI') }}><label class="form-check-label" for="EKSPLORASI">Eksplorasi</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="BILAS_LAMBUNG" value="1" id="BILAS_LAMBUNG" {{ isCheckedRm3f($row, 'BILAS_LAMBUNG') }}><label class="form-check-label" for="BILAS_LAMBUNG">Bilas Lambung</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="MENYIAPKAN_LAB" value="1" id="MENYIAPKAN_LAB" {{ isCheckedRm3f($row, 'MENYIAPKAN_LAB') }}><label class="form-check-label" for="MENYIAPKAN_LAB">Menyiapkan Lab</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="IRIGASI_MATA" value="1" id="IRIGASI_MATA" {{ isCheckedRm3f($row, 'IRIGASI_MATA') }}><label class="form-check-label" for="IRIGASI_MATA">Irigasi Mata</label></div></div>
                    <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="KATETER" value="1" id="KATETER" {{ isCheckedRm3f($row, 'KATETER') }}><label class="form-check-label" for="KATETER">Kateter</label></div></div>
                    <div class="col-md-12 mt-2">
                        <div class="d-flex align-items-center">
                            <div class="form-check me-2"><input class="form-check-input" type="checkbox" name="LAINNYA" value="1" id="LAINNYA" {{ isCheckedRm3f($row, 'LAINNYA') }}><label class="form-check-label" for="LAINNYA">Lainnya:</label></div>
                            <div id="lainnyaKetContainer" class="flex-grow-1" style="display: {{ isCheckedRm3f($row, 'LAINNYA') ? 'block' : 'none' }};"><input type="text" id="LAINNYA_KET" name="LAINNYA_KET" class="form-control" value="{{ getValueRm3f($row, 'LAINNYA_KET') }}"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OBAT -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white"><h5 class="mb-0">OBAT</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="OBAT_ORAL" value="1" id="OBAT_ORAL" {{ isCheckedRm3f($row, 'OBAT_ORAL') }}><label class="form-check-label fw-bold" for="OBAT_ORAL">Oral</label></div>
                    <div id="OBAT_ORAL_CONTAINER" style="{{ isCheckedRm3f($row, 'OBAT_ORAL') ? '' : 'display: none;' }}">
                        <textarea id="OBAT_ORAL_KET" name="OBAT_ORAL_KET" class="form-control" rows="3" placeholder="Sebutkan obat oral...">{{ getValueRm3f($row, 'OBAT_ORAL_KET') }}</textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="OBAT_PARENTERAL" value="1" id="OBAT_PARENTERAL" {{ isCheckedRm3f($row, 'OBAT_PARENTERAL') }}><label class="form-check-label fw-bold" for="OBAT_PARENTERAL">Parenteral</label></div>
                    <div id="OBAT_PARENTERAL_CONTAINER" style="{{ isCheckedRm3f($row, 'OBAT_PARENTERAL') ? '' : 'display: none;' }}">
                        <textarea id="OBAT_PARENTERAL_KET" name="OBAT_PARENTERAL_KET" class="form-control" rows="3" placeholder="Sebutkan obat parenteral...">{{ getValueRm3f($row, 'OBAT_PARENTERAL_KET') }}</textarea>
                    </div>
                </div>
                <div>
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="OBAT_LAIN" value="1" id="OBAT_LAIN" {{ isCheckedRm3f($row, 'OBAT_LAIN') }}><label class="form-check-label fw-bold" for="OBAT_LAIN">Lain-lain</label></div>
                    <div id="OBAT_LAIN_CONTAINER" style="{{ isCheckedRm3f($row, 'OBAT_LAIN') ? '' : 'display: none;' }}">
                        <textarea id="OBAT_LAIN_KET" name="OBAT_LAIN_KET" class="form-control" rows="3" placeholder="Sebutkan terapi lainnya...">{{ getValueRm3f($row, 'OBAT_LAIN_KET') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-transparent border-0 px-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ $submitButtonText }}</button>
            <button type="button" class="btn btn-outline-secondary" id="reset-rm3f"><i class="fas fa-undo"></i> Batal / Reset</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // =================================================================================
    // FUNGSI HELPER DAN INISIALISASI
    // =================================================================================

    // Fungsi untuk checkbox eksklusif dalam satu grup
    function handleExclusiveCheck(groupName) {
        $(`input[data-group="${groupName}"]`).on('change', function() {
            if ($(this).is(':checked')) {
                $(`input[data-group="${groupName}"]`).not(this).prop('checked', false);
            }
        });
    }

    // Fungsi untuk toggle visibility berdasarkan checkbox
    function toggleVisibility(checkboxSelector, containerSelector) {
        const checkbox = $(checkboxSelector);
        const container = $(containerSelector);

        function toggle() {
            if (checkbox.is(':checked')) {
                container.slideDown(200);
            } else {
                container.slideUp(200);
                container.find('input[type="text"], textarea').val('');
                container.find('input[type="checkbox"]').prop('checked', false);
            }
        }
        checkbox.on('change', toggle);
        toggle(); // Initial check on page load
    }

    // Fungsi untuk checkbox eksklusif dengan toggle (misal: Ya/Tidak)
    function exclusiveCheckboxWithToggle(yesSelector, noSelector, containerSelector) {
        const checkboxYes = $(yesSelector);
        const checkboxNo = $(noSelector);
        const container = $(containerSelector);

        checkboxYes.on('change', function() {
            if ($(this).is(':checked')) {
                checkboxNo.prop('checked', false);
                container.slideDown(200);
            } else if (!checkboxNo.is(':checked')) {
                container.slideUp(200);
                container.find('input, textarea').val('');
                container.find('input[type="checkbox"]').prop('checked', false);
            }
        });

        checkboxNo.on('change', function() {
            if ($(this).is(':checked')) {
                checkboxYes.prop('checked', false);
                container.slideUp(200);
                container.find('input, textarea').val('');
                container.find('input[type="checkbox"]').prop('checked', false);
            }
        });

        // Initial state
        if (checkboxYes.is(':checked')) {
            container.show();
        } else {
            container.hide();
        }
    }

    // =================================================================================
    // LOGIKA FORM
    // =================================================================================

    // Sistem Tubuh
    exclusiveCheckboxWithToggle('#PERDARAHAN', '#PERDARAHAN_TDK_ADA', '#perdarahan-detail');
    exclusiveCheckboxWithToggle('#RIWAYAT_ALERGI_ADA', '#RIWAYAT_ALERGI_TIDAK', '#RIWAYAT_ALERGI_ADA_KET');

    // Pengkajian Primer
    toggleVisibility('#AIRWAY_LAIN', '#AIRWAY_KET_LAIN');
    exclusiveCheckboxWithToggle('#BREATH_SESAK_YA', '#BREATH_SESAK_TIDAK', '#sesakDetailOptions');
    handleExclusiveCheck('sesak-detail');
    handleExclusiveCheck('irama-pernapasan');
    handleExclusiveCheck('crt-checkbox');
    handleExclusiveCheck('extremitas-checkbox');
    handleExclusiveCheck('turgor-checkbox');
    handleExclusiveCheck('warna-kulit');
    handleExclusiveCheck('pupil-disability');
    exclusiveCheckboxWithToggle('#EXPOSURE_LUKA_ADA', '#EXPOSURE_LUKA_TIDKA', '#exposureForm');

    // Asesmen Nyeri
    exclusiveCheckboxWithToggle('#NYERI_YA', '#NYERI_TIDAK', '#nyeriForm');
    toggleVisibility('#NYERI_LAIN', '#NYERI_LAIN_KET_CONTAINER');

    // Tindakan Medis & Obat
    toggleVisibility('#LAINNYA', '#lainnyaKetContainer');
    toggleVisibility('#OBAT_ORAL', '#OBAT_ORAL_CONTAINER');
    toggleVisibility('#OBAT_PARENTERAL', '#OBAT_PARENTERAL_CONTAINER');
    toggleVisibility('#OBAT_LAIN', '#OBAT_LAIN_CONTAINER');

    // GCS Calculation
    function calculateGCS_rm3f() {
        const gcsE = parseInt($('#DISAB_GCS_E').val()) || 0;
        const gcsV = parseInt($('#DISAB_GCS_V').val()) || 0;
        const gcsM = parseInt($('#DISAB_GCS_M').val()) || 0;
        const totalGCS = gcsE + gcsV + gcsM;

        let kesadaran = '';
        if (totalGCS === 0) kesadaran = '';
        else if (totalGCS >= 14) kesadaran = 'Composmentis';
        else if (totalGCS >= 12) kesadaran = 'Apatis';
        else if (totalGCS >= 10) kesadaran = 'Delirium';
        else if (totalGCS >= 7) kesadaran = 'Somnolen';
        else if (totalGCS >= 4) kesadaran = 'Sopor';
        else if (totalGCS >= 3) kesadaran = 'Coma';

        $('#DISAB_KESADARAN').val(kesadaran);
    }

    $('.gcs-input-rm3f').on('input', function() {
        const min = parseInt($(this).attr('min'));
        const max = parseInt($(this).attr('max'));
        let value = parseInt($(this).val());
        if (value < min) $(this).val(min);
        if (value > max) $(this).val(max);
        calculateGCS_rm3f();
    });
    calculateGCS_rm3f(); // Initial call

    // =================================================================================
    // AJAX SUBMISSION
    // =================================================================================
    $('#form-rm3f-submit').on('submit', function(e) {
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500, // Notifikasi akan hilang setelah 1.5 detik
                        showConfirmButton: false
                    });
                    // Beri jeda sejenak agar pengguna bisa melihat notifikasi sebelum form dimuat ulang.
                    // Lalu picu event 'change' pada dropdown di halaman utama untuk memuat ulang form.
                    setTimeout(() => $('#form-selector').trigger('change'), 1500);
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

    // Tombol Reset
    $('#reset-rm3f').on('click', function() {
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
                $('#form-rm3f-submit')[0].reset();
                // Trigger change untuk semua elemen yang memiliki logika show/hide
                $('#form-rm3f-submit').find('input[type="checkbox"]').trigger('change');
                calculateGCS_rm3f(); // Recalculate GCS
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });
});
</script>
