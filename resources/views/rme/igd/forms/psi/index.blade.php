@php
    // Helper functions
    function getValue($data, $key, $default = '') {
        return isset($data[$key]) && !empty(trim($data[$key])) ? trim($data[$key]) : $default;
    }

    function isChecked($data, $key) {
        return isset($data[$key]) && $data[$key] == 1 ? 'checked' : '';
    }

    // Determine if additional options should be displayed initially
    $showOpsiTambahan = false;
    $jumlah = isset($data['JUMLAH']) ? intval($data['JUMLAH']) : 0;
    $frekuenNafas20 = isset($data['FREKUEN_NAFAS_20']) ? $data['FREKUEN_NAFAS_20'] : 0;
    $tdSistolik20 = isset($data['TD_SISTOLIK_20']) ? $data['TD_SISTOLIK_20'] : 0;

    if ($jumlah <= 70 && !$frekuenNafas20 && !$tdSistolik20) {
        $showOpsiTambahan = true;
    }
@endphp

<div class="card-body">
    <form id="formPSI" action="{{ route('rme.igd.form.psi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ htmlspecialchars($noPendaftaran) }}">

        <!-- Tabel PSI -->
        <div class="mb-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th width="50%">Score PSI</th>
                            <th width="15%" class="text-center">Nilai</th>
                            <th width="15%" class="text-center">Pada Pasien</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Faktor Demografik -->
                        <tr class="bg-light">
                            <td colspan="3"><strong>A. Faktor Demografik</strong></td>
                        </tr>
                        <tr>
                            <td>Umur</td>
                            <td class="text-center">-</td>
                            <td class="text-center">
                                <input type="text" class="form-control form-control-sm text-center" id="USIA" name="USIA"
                                    value="{{ getValue($data, 'USIA', $patient['Usia']) }}">
                            </td>
                        </tr>
                        <tr>
                            <td>Laki - laki</td>
                            <td class="text-center">-</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="radio" id="LAKILAKI" name="GENDER"
                                        value="L" {{ ($patient['Gender'] ?? '') == 'L' ? 'checked' : '' }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Perempuan</td>
                            <td class="text-center">-10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="radio" id="PEREMPUAN" name="GENDER"
                                        value="P" {{ ($patient['Gender'] ?? '') == 'P' ? 'checked' : '' }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Penghuni panti werda</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="PANTI_WERDA_10"
                                        name="PANTI_WERDA_10" {{ isChecked($data, 'PANTI_WERDA_10') }}>
                                </div>
                            </td>
                        </tr>

                        <!-- Penyakit Komorbid -->
                        <tr class="bg-light">
                            <td colspan="3"><strong>B. Penyakit Komorbid</strong></td>
                        </tr>
                        <tr>
                            <td>Keganasan</td>
                            <td class="text-center">+30</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="KEGANASAN_30"
                                        name="KEGANASAN_30" {{ isChecked($data, 'KEGANASAN_30') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Penyakit Hati</td>
                            <td class="text-center">+20</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="PENYAKIT_HATI_20"
                                        name="PENYAKIT_HATI_20"
                                        {{ isChecked($data, 'PENYAKIT_HATI_20') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Penyakit Jantung</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="PENYAKIT_JANTUNG_10"
                                        name="PENYAKIT_JANTUNG_10"
                                        {{ isChecked($data, 'PENYAKIT_JANTUNG_10') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Penyakit Serebro</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="PENYAKIT_SEREBRO_10"
                                        name="PENYAKIT_SEREBRO_10"
                                        {{ isChecked($data, 'PENYAKIT_SEREBRO_10') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Penyakit Ginjal</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="PENYAKIT_GINJAL_10"
                                        name="PENYAKIT_GINJAL_10"
                                        {{ isChecked($data, 'PENYAKIT_GINJAL_10') }}>
                                </div>
                            </td>
                        </tr>

                        <!-- Pemeriksaan Fisik -->
                        <tr class="bg-light">
                            <td colspan="3"><strong>C. Pemeriksaan Fisik</strong></td>
                        </tr>
                        <tr>
                            <td>Gangguan Kesadaran</td>
                            <td class="text-center">+20</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="GANGGUAN_KESADARAN_20"
                                        name="GANGGUAN_KESADARAN_20"
                                        {{ isChecked($data, 'GANGGUAN_KESADARAN_20') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Frekuensi Nafas ≥ 30x/menit</td>
                            <td class="text-center">+20</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="FREKUEN_NAFAS_20"
                                        name="FREKUEN_NAFAS_20"
                                        {{ isChecked($data, 'FREKUEN_NAFAS_20') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>TD Sistolik < 90 mmHg</td>
                            <td class="text-center">+20</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="TD_SISTOLIK_20"
                                        name="TD_SISTOLIK_20" {{ isChecked($data, 'TD_SISTOLIK_20') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Suhu Tubuh < 35°C atau ≥ 40°C</td>
                            <td class="text-center">+15</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="SUHU_TUBUH_15"
                                        name="SUHU_TUBUH_15" {{ isChecked($data, 'SUHU_TUBUH_15') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Nadi ≥ 125x/menit</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="NADI_10" name="NADI_10"
                                        {{ isChecked($data, 'NADI_10') }}>
                                </div>
                            </td>
                        </tr>

                        <!-- Hasil Laboratorium -->
                        <tr class="bg-light">
                            <td colspan="3"><strong>D. Hasil Laboratorium</strong></td>
                        </tr>
                        <tr>
                            <td>pH Arteri < 7.35</td>
                            <td class="text-center">+30</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="PH_30" name="PH_30"
                                        {{ isChecked($data, 'PH_30') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Ureum ≥ 30 mg/dL</td>
                            <td class="text-center">+20</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="UREUM_20"
                                        name="UREUM_20" {{ isChecked($data, 'UREUM_20') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Natrium < 130 mEq/L</td>
                            <td class="text-center">+20</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="NATRIUM_20"
                                        name="NATRIUM_20" {{ isChecked($data, 'NATRIUM_20') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Glukosa ≥ 250 mg/dL</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="GLUKOSA_10"
                                        name="GLUKOSA_10" {{ isChecked($data, 'GLUKOSA_10') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Hematokrit < 30%</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="HEMATOKRIT_10"
                                        name="HEMATOKRIT_10" {{ isChecked($data, 'HEMATOKRIT_10') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>PaO2 < 60 mmHg</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="DARAHARTERI_10"
                                        name="DARAHARTERI_10" {{ isChecked($data, 'DARAHARTERI_10') }}>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Efusi Pleura</td>
                            <td class="text-center">+10</td>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" id="EFUSI_PLEURA_10"
                                        name="EFUSI_PLEURA_10"
                                        {{ isChecked($data, 'EFUSI_PLEURA_10') }}>
                                </div>
                            </td>
                        </tr>

                        <!-- Total Skor -->
                        <tr class="table-warning">
                            <td><strong>Total Skor PSI</strong></td>
                            <td colspan="2">
                                <input type="text" class="form-control form-control-sm text-center font-weight-bold" id="JUMLAH"
                                    name="JUMLAH" value="{{ getValue($data, 'JUMLAH', '0') }}" readonly>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kesimpulan dan Opsi Tambahan -->
        <div class="mb-4 p-3 border rounded">
            <h5 class="mb-3 text-primary"><i class="fas fa-clipboard-check mr-2"></i>Kesimpulan</h5>

            <!-- Input Kesimpulan -->
            <div class="form-group">
                <label for="KESIMPULAN">Hasil Kesimpulan</label>
                <input type="text" class="form-control font-weight-bold" id="KESIMPULAN" name="KESIMPULAN"
                    value="{{ getValue($data, 'KESIMPULAN') }}" readonly>
            </div>

            <!-- Opsi Tambahan -->
            <div id="opsiTambahan" style="display: {{ $showOpsiTambahan ? 'block' : 'none' }};">
                <h6 class="mb-3 mt-4 text-primary">Opsi Tambahan Rawat Inap</h6>
                <div class="alert alert-warning">
                    <small>Jika ada salah satu kondisi berikut, pasien perlu dirawat inap:</small>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="OPSI_PAO2" name="OPSI_PAO2"
                        {{ isChecked($data, 'OPSI_PAO2') }}>
                    <label class="form-check-label" for="OPSI_PAO2">PaO2/FiO2 kurang dari 250 mmHg</label>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="OPSI_RADIOLOGI"
                        name="OPSI_RADIOLOGI" {{ isChecked($data, 'OPSI_RADIOLOGI') }}>
                    <label class="form-check-label" for="OPSI_RADIOLOGI">Radiologi menunjukkan infiltrat/opasitas/konsolidasi multi lobus</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="OPSI_TEKANANDIASTOLIK"
                        name="OPSI_TEKANANDIASTOLIK"
                        {{ isChecked($data, 'OPSI_TEKANANDIASTOLIK') }}>
                    <label class="form-check-label" for="OPSI_TEKANANDIASTOLIK">Tekanan diastolik < 60 mmHg</label>
                </div>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="mb-4 p-3 border rounded">
            <h5 class="mb-3 text-primary"><i class="fas fa-info-circle mr-2"></i>Keterangan</h5>
            <p>PSI digunakan untuk menetapkan indikasi rawat inap pneumonia komunitas.</p>
            <ol class="mb-0 pl-4">
                <li>Skor PSI lebih dari 70</li>
                <li>Bila skor PSI kurang dari 70, pasien tetap perlu dirawat inap bila dijumpai salah satu dari kriteria dibawah ini:
                    <ul class="pl-4">
                        <li>Frekuensi nafas > 30 x/menit</li>
                        <li>PaO2/FiO2 kurang dari 250 mmHg</li>
                        <li>Radiologi menunjukkan infiltrat/opasitas/konsolidasi multi lobus</li>
                        <li>Tekanan sistolik < 90 mmHg</li>
                        <li>Tekanan diastolik < 60 mmHg</li>
                    </ul>
                </li>
            </ol>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Fungsi untuk menghitung total poin dan menentukan kesimpulan
    function hitungTotalPoin() {
        // Inisialisasi total dari 0. Perhitungan umur diabaikan sesuai permintaan.
        let total = 0;

        const isPerempuan = $('input[name="GENDER"]:checked').val() === 'P';
        if (isPerempuan) {
            total -= 10;
        }

        // Faktor Demografik
        if ($('#PANTI_WERDA_10').is(':checked')) total += 10;

        // Penyakit Komorbid
        if ($('#KEGANASAN_30').is(':checked')) total += 30;
        if ($('#PENYAKIT_HATI_20').is(':checked')) total += 20;
        if ($('#PENYAKIT_JANTUNG_10').is(':checked')) total += 10;
        if ($('#PENYAKIT_SEREBRO_10').is(':checked')) total += 10;
        if ($('#PENYAKIT_GINJAL_10').is(':checked')) total += 10;

        // Pemeriksaan Fisik
        if ($('#GANGGUAN_KESADARAN_20').is(':checked')) total += 20;
        if ($('#FREKUEN_NAFAS_20').is(':checked')) total += 20;
        if ($('#TD_SISTOLIK_20').is(':checked')) total += 20;
        if ($('#SUHU_TUBUH_15').is(':checked')) total += 15;
        if ($('#NADI_10').is(':checked')) total += 10;

        // Hasil Lab
        if ($('#PH_30').is(':checked')) total += 30;
        if ($('#UREUM_20').is(':checked')) total += 20;
        if ($('#NATRIUM_20').is(':checked')) total += 20;
        if ($('#GLUKOSA_10').is(':checked')) total += 10;
        if ($('#HEMATOKRIT_10').is(':checked')) total += 10;
        if ($('#DARAHARTERI_10').is(':checked')) total += 10;
        if ($('#EFUSI_PLEURA_10').is(':checked')) total += 10;

        // Pastikan total tidak negatif
        total = Math.max(total, 0);

        $('#JUMLAH').val(total);

        // Tentukan kesimpulan berdasarkan total skor
        const frekuenNafasChecked = $('#FREKUEN_NAFAS_20').is(':checked');
        const tdSistolikChecked = $('#TD_SISTOLIK_20').is(':checked');
        const opsiTambahan = $('#opsiTambahan');
        const opsiPaO2 = $('#OPSI_PAO2');
        const opsiRadiologi = $('#OPSI_RADIOLOGI');
        const opsiDiastolik = $('#OPSI_TEKANANDIASTOLIK');

        let kesimpulan = '';

        if (total > 70) {
            kesimpulan = 'Rawat Inap';
            opsiTambahan.slideUp();
            opsiPaO2.prop('checked', false);
            opsiRadiologi.prop('checked', false);
            opsiDiastolik.prop('checked', false);
        } else {
            if (frekuenNafasChecked || tdSistolikChecked) {
                kesimpulan = 'Rawat Inap';
                opsiTambahan.slideUp();
                opsiPaO2.prop('checked', false);
                opsiRadiologi.prop('checked', false);
                opsiDiastolik.prop('checked', false);
            } else {
                kesimpulan = 'Rawat Jalan';
                opsiTambahan.slideDown();

                if (opsiPaO2.is(':checked') || opsiRadiologi.is(':checked') || opsiDiastolik.is(':checked')) {
                    kesimpulan = 'Rawat Inap';
                }
            }
        }

        $('#KESIMPULAN').val(kesimpulan);
    }

    // Panggil fungsi hitung saat halaman dimuat
    hitungTotalPoin();

    // Tambahkan event listener untuk semua input
    $('#formPSI').on('change', 'input[type="checkbox"], input[type="radio"], input[type="text"]', function() {
        hitungTotalPoin();
    });

    // Handle form submission with AJAX
    $('#formPSI').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var formData = new FormData(this);

        // Add spinner to submit button
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.html();
        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                // The global ajaxSuccess handler will automatically refresh the form
            },
            error: function(xhr) {
                var errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMessage
                });
            },
            complete: function() {
                // Restore button
                submitButton.html(originalButtonText).prop('disabled', false);
            }
        });
    });
});
</script>