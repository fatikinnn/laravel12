@php
    function getValueRm29($data, $key, $default = '') {
        if (is_object($data) && property_exists($data, $key) && !is_null($data->$key)) {
            return trim($data->$key);
        }
        return $default;
    }
    function isCheckedRm29($data, $key) {
        return getValueRm29($data, $key) == 1 ? 'checked' : '';
    }
    $isUpdate = !empty($rm29->TGLJAM_ENTRY);
    $submitButtonText = $isUpdate ? 'Update' : 'Simpan';
@endphp

<div class="card-body">
    <form id="rm29Form" action="{{ route('rme.igd.form.rm29.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="USER_ENTRY" value="{{ $user['username'] ?? '' }}">
        <input type="hidden" name="TGLJAM_ENTRY" value="{{ now()->format('Y-m-d H:i:s') }}">

        {{-- Data Bayi --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Data Bayi</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="TGL_SKRINING">Tanggal Skrining</label>
                        <input type="date" class="form-control" name="TGL_SKRINING" id="TGL_SKRINING" required value="{{ getValueRm29($rm29, 'TGL_SKRINING') ? \Carbon\Carbon::parse(getValueRm29($rm29, 'TGL_SKRINING'))->format('Y-m-d') : now()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="BBL">Berat Badan Lahir (kg)</label>
                        <input type="number" step="0.01" class="form-control" name="BBL" id="BBL" value="{{ getValueRm29($rm29, 'BBL') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="BB">Berat Badan Sekarang (kg)</label>
                        <input type="number" step="0.01" class="form-control" name="BB" id="BB" value="{{ getValueRm29($rm3a, 'PACS4_BB') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="PB">Panjang Badan (cm)</label>
                        <input type="number" step="0.1" class="form-control" name="PB" id="PB" value="{{ getValueRm29($rm3a, 'PACS4_TB') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="LINGKAR_KEPALA">Lingkar Kepala (cm)</label>
                        <input type="number" step="0.1" class="form-control" name="LINGKAR_KEPALA" id="LINGKAR_KEPALA" value="{{ getValueRm29($rm29, 'LINGKAR_KEPALA') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="Z_SCORE">Z-Score</label>
                        <input type="text" class="form-control" name="Z_SCORE" id="Z_SCORE" value="{{ getValueRm29($rm29, 'Z_SCORE') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="DIAG_MEDIS">Diagnosa Medis</label>
                        <input type="text" class="form-control" name="DIAG_MEDIS" id="DIAG_MEDIS" value="{{ getValueRm29($rm3b, 'DIAGNOSIS_UTAMA') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Parameter Skrining --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Parameter Skrining</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr class="text-center">
                                <th style="width: 5%">No</th>
                                <th>Parameter</th>
                                <th style="width: 25%">Pilihan</th>
                                <th style="width: 15%">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Parameter 1: Minum --}}
                            <tr>
                                <td class="text-center align-middle">1</td>
                                <td class="align-middle"><strong>Minum</strong></td>
                                <td>
                                    <div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="asi" name="ASI" id="ASI" value="1" {{ isCheckedRm29($rm29, 'ASI') }}><label for="ASI">ASI</label></div>
                                    <div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="asi" name="PASI_FREK" id="PASI_FREK" value="1" {{ isCheckedRm29($rm29, 'PASI_FREK') }}><label for="PASI_FREK">PASI Frekuensi ....x/24 jam</label></div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="icheck-primary d-inline mr-2"><input type="checkbox" class="score-cb" data-group="asi" name="ASI_YA" id="ASI_YA" value="1" {{ isCheckedRm29($rm29, 'ASI_YA') }}><label for="ASI_YA">Ya (1)</label></div>
                                    <div class="icheck-primary d-inline"><input type="checkbox" class="score-cb" data-group="asi" name="ASI_TIDAK" id="ASI_TIDAK" value="0" {{ isCheckedRm29($rm29, 'ASI_TIDAK') }}><label for="ASI_TIDAK">Tidak (0)</label></div>
                                </td>
                            </tr>
                            {{-- Parameter 2: Penurunan BB --}}
                            <tr>
                                <td class="text-center align-middle">2</td>
                                <td class="align-middle"><strong>Penurunan Berat Badan</strong></td>
                                <td>
                                    <div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="bb" name="BB_TURUN" id="BB_TURUN" value="1" {{ isCheckedRm29($rm29, 'BB_TURUN') }}><label for="BB_TURUN">BB &lt; 10% dari BBL</label></div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="icheck-primary d-inline mr-2"><input type="checkbox" class="score-cb" data-group="bb" name="BB_YA" id="BB_YA" value="1" {{ isCheckedRm29($rm29, 'BB_YA') }}><label for="BB_YA">Ya (1)</label></div>
                                    <div class="icheck-primary d-inline"><input type="checkbox" class="score-cb" data-group="bb" name="BB_TIDAK" id="BB_TIDAK" value="0" {{ isCheckedRm29($rm29, 'BB_TIDAK') }}><label for="BB_TIDAK">Tidak (0)</label></div>
                                </td>
                            </tr>
                            {{-- Parameter 3: Penyakit --}}
                            <tr>
                                <td class="text-center align-middle">3</td>
                                <td class="align-middle"><strong>Penyakit yang menyertai</strong></td>
                                <td>
                                    <div class="row">
                                        <div class="col-6"><div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="penyakit" name="SEPSIS" id="SEPSIS" value="1" {{ isCheckedRm29($rm29, 'SEPSIS') }}><label for="SEPSIS">Sepsis</label></div></div>
                                        <div class="col-6"><div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="penyakit" name="JANTUNG" id="JANTUNG" value="1" {{ isCheckedRm29($rm29, 'JANTUNG') }}><label for="JANTUNG">Penyakit Jantung</label></div></div>
                                        <div class="col-6"><div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="penyakit" name="BBLR" id="BBLR" value="1" {{ isCheckedRm29($rm29, 'BBLR') }}><label for="BBLR">BBLR</label></div></div>
                                        <div class="col-6"><div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="penyakit" name="HIPOGLIKEMI" id="HIPOGLIKEMI" value="1" {{ isCheckedRm29($rm29, 'HIPOGLIKEMI') }}><label for="HIPOGLIKEMI">Hipoglikemi</label></div></div>
                                        <div class="col-6"><div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="penyakit" name="DIARE" id="DIARE" value="1" {{ isCheckedRm29($rm29, 'DIARE') }}><label for="DIARE">Diare</label></div></div>
                                        <div class="col-6"><div class="icheck-primary"><input type="checkbox" class="param-cb" data-group="penyakit" name="HIPERBILIRUBIN" id="HIPERBILIRUBIN" value="1" {{ isCheckedRm29($rm29, 'HIPERBILIRUBIN') }}><label for="HIPERBILIRUBIN">Hiperbilirubin</label></div></div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="icheck-primary d-inline mr-2"><input type="checkbox" class="score-cb" data-group="penyakit" name="PENYAKIT_YA" id="PENYAKIT_YA" value="2" {{ isCheckedRm29($rm29, 'PENYAKIT_YA') }}><label for="PENYAKIT_YA">Ya (2)</label></div>
                                    <div class="icheck-primary d-inline"><input type="checkbox" class="score-cb" data-group="penyakit" name="PENYAKIT_TIDAK" id="PENYAKIT_TIDAK" value="0" {{ isCheckedRm29($rm29, 'PENYAKIT_TIDAK') }}><label for="PENYAKIT_TIDAK">Tidak (0)</label></div>
                                </td>
                            </tr>
                            {{-- Total Skor --}}
                            <tr class="table-active">
                                <td colspan="3" class="text-right font-weight-bold">Total Skor:</td>
                                <td class="text-center">
                                    <input type="text" id="TOTAL_SCORE" name="TOTAL_SCORE" class="form-control font-weight-bold text-center" readonly value="{{ getValueRm29($rm29, 'TOTAL_SCORE', '0') }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Interpretasi Skor --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Interpretasi Skor</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><i class="fas fa-check-circle text-success mr-2"></i><strong>Skor &lt; 2:</strong> <span class="badge badge-success">Diit: ASI</span></p>
                        <p><i class="fas fa-exclamation-triangle text-warning mr-2"></i><strong>Skor ≥ 2:</strong> <span class="badge badge-warning">Lapor DPJP</span> <small class="text-muted">(Assesmen lanjut oleh ahli Gizi)</small></p>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <h6 class="font-weight-bold">Hasil Saat Ini:</h6>
                            <div id="interpretationText" class="p-2 rounded font-weight-bold h5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save mr-1"></i> {{ $submitButtonText }}</button>
            <button type="button" class="btn btn-outline-danger" id="reset-rm29"><i class="fas fa-times-circle mr-1"></i> Batal</button>
        </div>
    </form>
</div>

<style>
    #interpretationText.bg-success { color: white; }
    #interpretationText.bg-warning { color: #333; }
</style>

<script>
$(document).ready(function() {
    // --- Score Calculation Logic ---
    function updateTotalScore() {
        let total = 0;
        $('.score-cb:checked').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        $('#TOTAL_SCORE').val(total);
        updateInterpretation(total);
    }

    function updateInterpretation(score) {
        const interpretationDiv = $('#interpretationText');
        interpretationDiv.removeClass('bg-success bg-warning');
        if (score < 2) {
            interpretationDiv.addClass('bg-success').text('Diit: ASI');
        } else {
            interpretationDiv.addClass('bg-warning').text('Lapor DPJP');
        }
    }

    function handleGroupLogic(group) {
        const paramCheckboxes = $(`.param-cb[data-group="${group}"]`);
        const yaCheckbox = $(`.score-cb[data-group="${group}"][value="1"], .score-cb[data-group="${group}"][value="2"]`);
        const tidakCheckbox = $(`.score-cb[data-group="${group}"][value="0"]`);

        if (paramCheckboxes.is(':checked')) {
            yaCheckbox.prop('checked', true);
            tidakCheckbox.prop('checked', false);
        } else {
            yaCheckbox.prop('checked', false);
            tidakCheckbox.prop('checked', true);
        }
        updateTotalScore();
    }

    // Event listener untuk checkbox parameter
    $('.param-cb').on('change', function() {
        const group = $(this).data('group');
        handleGroupLogic(group);
    });

    // Prevent manual checking of score checkboxes
    $('.score-cb').on('click', function(e) {
        e.preventDefault();
    });

    // Initial setup on page load
    function initializeForm() {
        handleGroupLogic('asi');
        handleGroupLogic('bb');
        handleGroupLogic('penyakit');
        if (!"{{ $isUpdate }}") {
            $('#ASI_TIDAK, #BB_TIDAK, #PENYAKIT_TIDAK').prop('checked', true);
        }
        updateTotalScore();
    }
    initializeForm();

    // --- AJAX Form Submission ---
    $('#rm29Form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        
        var data = form.serializeArray();
        form.find('input[type=checkbox]:not(:checked)').each(function() {
            data.push({ name: this.name, value: '0' });
        });

        var button = form.find('button[type="submit"]');
        var originalButtonText = button.html();

        $.ajax({
            type: 'POST',
            url: url,
            data: $.param(data),
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => {
                            // Reload form HANYA SETELAH alert ditutup
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
            complete: function(xhr) {
                if (!xhr.responseJSON || xhr.responseJSON.status !== 'success') {
                    button.prop('disabled', false).html(originalButtonText);
                }
            }
        });
    });

    // --- Reset Button ---
    $('#reset-rm29').on('click', function() {
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
                $('#rm29Form')[0].reset();
                initializeForm();
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });
});
</script>