@php
    function getValueRm26($data, $key, $default = '') {
        if (is_object($data) && property_exists($data, $key) && !is_null($data->$key)) {
            return trim($data->$key);
        }
        return $default;
    }
    function isCheckedRm26($data, $key) {
        return getValueRm26($data, $key) == 1 ? 'checked' : '';
    }
    $isUpdate = !empty($rm26->TGLJAM_ENTRY);
    $submitButtonText = $isUpdate ? 'Update' : 'Simpan';
@endphp

<div class="card-body">
    <form id="rm26Form" action="{{ route('rme.igd.form.rm26.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="USER_ENTRY" value="{{ $user['username'] ?? '' }}">
        <input type="hidden" name="TGLJAM_ENTRY" value="{{ now()->format('Y-m-d H:i:s') }}">

        {{-- Data Antropometri --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Data Antropometri</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="TGL_SKRINING">Tanggal & Jam Skrining</label>
                        <input type="datetime-local" class="form-control" name="TGL_SKRINING" id="TGL_SKRINING" required value="{{ getValueRm26($rm26, 'TGL_SKRINING') ? \Carbon\Carbon::parse(getValueRm26($rm26, 'TGL_SKRINING'))->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="TB">Tinggi Badan (cm)</label>
                        <input type="text" class="form-control" name="TB" id="TB" value="{{ getValueRm26($rm3a, 'PACS4_TB') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="BB">Berat Badan (kg)</label>
                        <input type="text" class="form-control" name="BB" id="BB" value="{{ getValueRm26($rm3a, 'PACS4_BB') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="IMT">IMT (kg/m²)</label>
                        <input type="text" class="form-control" name="IMT" id="IMT" value="{{ getValueRm26($rm3a, 'BMI') }}" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="LILA">LILA (cm)</label>
                        <input type="text" class="form-control" name="LILA" id="LILA" value="{{ getValueRm26($rm26, 'LILA') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="Z_SCCRE">Z-Score</label>
                        <select class="form-control" id="Z_SCCRE" name="Z_SCCRE">
                            <option value="">Pilih Z-Score</option>
                            <option value="NORMAL" {{ getValueRm26($rm26, 'Z_SCCRE') == 'NORMAL' ? 'selected' : '' }}>Normal</option>
                            <option value="UNDER" {{ getValueRm26($rm26, 'Z_SCCRE') == 'UNDER' ? 'selected' : '' }}>Under</option>
                            <option value="OVER" {{ getValueRm26($rm26, 'Z_SCCRE') == 'OVER' ? 'selected' : '' }}>Over</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="NAMA_DOKTER">Nama Dokter</label>
                        <input type="text" class="form-control" id="NAMA_DOKTER" name="NAMA_DOKTER" value="{{ getValueRm26($rm26, 'NAMA_DOKTER', $user['username'] ?? '') }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="DIAGNOSIS_MEDIS">Diagnosis Medis</label>
                    <textarea class="form-control" name="DIAGNOSIS_MEDIS" id="DIAGNOSIS_MEDIS" rows="2">{{ getValueRm26($rm3b, 'DIAGNOSIS_UTAMA') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Bentuk Makanan --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Bentuk Makanan yang Disarankan</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="CAIR" id="CAIR" value="1" {{ isCheckedRm26($rm26, 'CAIR') }}><label class="form-check-label" for="CAIR">Cair</label></div></div>
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="SONDE" id="SONDE" value="1" {{ isCheckedRm26($rm26, 'SONDE') }}><label class="form-check-label" for="SONDE">Sonde</label></div></div>
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="BUBUR_SARING" id="BUBUR_SARING" value="1" {{ isCheckedRm26($rm26, 'BUBUR_SARING') }}><label class="form-check-label" for="BUBUR_SARING">Bubur Saring</label></div></div>
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="BUBUR_SUMSUM" id="BUBUR_SUMSUM" value="1" {{ isCheckedRm26($rm26, 'BUBUR_SUMSUM') }}><label class="form-check-label" for="BUBUR_SUMSUM">Bubur Sumsum</label></div></div>
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="BUBUR_BIASA" id="BUBUR_BIASA" value="1" {{ isCheckedRm26($rm26, 'BUBUR_BIASA') }}><label class="form-check-label" for="BUBUR_BIASA">Bubur Biasa</label></div></div>
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="BUBUR_TIM" id="BUBUR_TIM" value="1" {{ isCheckedRm26($rm26, 'BUBUR_TIM') }}><label class="form-check-label" for="BUBUR_TIM">Bubur Tim</label></div></div>
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="BUBUR_TEMPE" id="BUBUR_TEMPE" value="1" {{ isCheckedRm26($rm26, 'BUBUR_TEMPE') }}><label class="form-check-label" for="BUBUR_TEMPE">Bubur Tempe</label></div></div>
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="NASI_TIM" id="NASI_TIM" value="1" {{ isCheckedRm26($rm26, 'NASI_TIM') }}><label class="form-check-label" for="NASI_TIM">Nasi Tim</label></div></div>
                    <div class="col-md-3"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="NASI" id="NASI" value="1" {{ isCheckedRm26($rm26, 'NASI') }}><label class="form-check-label" for="NASI">Nasi</label></div></div>
                </div>
                <div class="input-group mt-2 col-md-6">
                    <div class="input-group-prepend"><div class="input-group-text"><input type="checkbox" name="MAKANAN_LAINNYA" value="1" id="MAKANAN_LAINNYA" {{ isCheckedRm26($rm26, 'MAKANAN_LAINNYA') }}></div></div>
                    <input type="text" class="form-control" name="MAKANAN_LAINNYA_TEXT" placeholder="Lainnya..." value="{{ getValueRm26($rm26, 'MAKANAN_LAINNYA_TEXT') }}">
                </div>
            </div>
        </div>

        {{-- Skrining Gizi STRONG-KIDS --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Skrining Gizi STRONG-KIDS</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr>
                                <th style="width: 5%" class="text-center">No</th>
                                <th>Parameter</th>
                                <th style="width: 20%">Jawaban</th>
                                <th style="width: 10%" class="text-center">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>Apakah pasien tampak kurus?</td>
                                <td class="align-middle">
                                    <div class="form-check form-check-inline"><input class="form-check-input skor-cb" type="checkbox" data-group="pasien" data-score="0" name="PASIEN_TIDAK_0" id="PASIEN_TIDAK_0" value="1" {{ isCheckedRm26($rm26, 'PASIEN_TIDAK_0') }}><label class="form-check-label" for="PASIEN_TIDAK_0">Tidak</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input skor-cb" type="checkbox" data-group="pasien" data-score="2" name="PASIEN_YA_1" id="PASIEN_YA_1" value="1" {{ isCheckedRm26($rm26, 'PASIEN_YA_1') }}><label class="form-check-label" for="PASIEN_YA_1">Ya</label></div>
                                </td>
                                <td class="text-center align-middle font-weight-bold" id="skor1">0</td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>Apakah ada penurunan berat badan selama satu bulan terakhir? <span class="small text-muted">(Berdasarkan data objektif/subjektif orang tua, atau BB tidak naik pada bayi < 1 tahun selama 3 bulan)</span></td>
                                <td class="align-middle">
                                    <div class="form-check form-check-inline"><input class="form-check-input skor-cb" type="checkbox" data-group="penurunan" data-score="0" name="PENURUNAN_BB_TIDAK_0" id="PENURUNAN_BB_TIDAK_0" value="1" {{ isCheckedRm26($rm26, 'PENURUNAN_BB_TIDAK_0') }}><label class="form-check-label" for="PENURUNAN_BB_TIDAK_0">Tidak</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input skor-cb" type="checkbox" data-group="penurunan" data-score="1" name="PENURUNAN_BB_YA_1" id="PENURUNAN_BB_YA_1" value="1" {{ isCheckedRm26($rm26, 'PENURUNAN_BB_YA_1') }}><label class="form-check-label" for="PENURUNAN_BB_YA_1">Ya</label></div>
                                </td>
                                <td class="text-center align-middle font-weight-bold" id="skor2">0</td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td>
                                    Apakah terdapat salah satu dari kondisi berikut:
                                    <ul class="mb-0 small text-muted">
                                        <li>Diare > 5 kali/sehari</li>
                                        <li>Asupan makanan berkurang selama 1 minggu terakhir</li>
                                    </ul>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="KONDISI_DIARE" id="KONDISI_DIARE" value="1" {{ isCheckedRm26($rm26, 'KONDISI_DIARE') }}><label class="form-check-label" for="KONDISI_DIARE">Diare</label></div>
                                        <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="KONDISI_MAKANAN_BERKURANG" id="KONDISI_MAKANAN_BERKURANG" value="1" {{ isCheckedRm26($rm26, 'KONDISI_MAKANAN_BERKURANG') }}><label class="form-check-label" for="KONDISI_MAKANAN_BERKURANG">Makanan Berkurang</label></div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="form-check form-check-inline"><input class="form-check-input skor-cb" type="checkbox" data-group="kondisi" data-score="0" name="KONDISI_TIDAK_0" id="KONDISI_TIDAK_0" value="1" {{ isCheckedRm26($rm26, 'KONDISI_TIDAK_0') }}><label class="form-check-label" for="KONDISI_TIDAK_0">Tidak</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input skor-cb" type="checkbox" data-group="kondisi" data-score="1" name="KONDISI_YA_1" id="KONDISI_YA_1" value="1" {{ isCheckedRm26($rm26, 'KONDISI_YA_1') }}><label class="form-check-label" for="KONDISI_YA_1">Ya</label></div>
                                </td>
                                <td class="text-center align-middle font-weight-bold" id="skor3">0</td>
                            </tr>
                            <tr>
                                <td class="text-center">4</td>
                                <td>Apakah terdapat penyakit atau keadaan yang mengakibatkan pasien beresiko mengalami malnutrisi?</td>
                                <td class="align-middle">
                                    <div class="form-check form-check-inline"><input class="form-check-input skor-cb" type="checkbox" data-group="penyakit" data-score="0" name="TERDAPAT_PENYAKIT_TIDAK_0" id="TERDAPAT_PENYAKIT_TIDAK_0" value="1" {{ isCheckedRm26($rm26, 'TERDAPAT_PENYAKIT_TIDAK_0') }}><label class="form-check-label" for="TERDAPAT_PENYAKIT_TIDAK_0">Tidak</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input skor-cb" type="checkbox" data-group="penyakit" data-score="1" name="TERDAPAT_PENYAKIT_YA_1" id="TERDAPAT_PENYAKIT_YA_1" value="1" {{ isCheckedRm26($rm26, 'TERDAPAT_PENYAKIT_YA_1') }}><label class="form-check-label" for="TERDAPAT_PENYAKIT_YA_1">Ya</label></div>
                                </td>
                                <td class="text-center align-middle font-weight-bold" id="skor4">0</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="3" class="text-right font-weight-bold">Total Skor:</td>
                                <td class="text-center font-weight-bold">
                                    <input type="hidden" id="TOTAL_SKOR" name="TOTAL_SKOR" value="{{ getValueRm26($rm26, 'TOTAL_SKOR', '0') }}">
                                    <span id="totalSkorDisplay" style="font-size: 1.2rem;">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Resiko:</td>
                                <td class="text-center">
                                    <div id="riskIndicator" class="p-2 rounded font-weight-bold"></div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save mr-1"></i> {{ $submitButtonText }}</button>
            <button type="button" class="btn btn-outline-danger" id="reset-rm26"><i class="fas fa-times-circle mr-1"></i> Batal</button>
        </div>
    </form>
</div>

<style>
    #riskIndicator.risk-low { background-color: #d4edda; color: #155724; }
    #riskIndicator.risk-medium { background-color: #fff3cd; color: #856404; }
    #riskIndicator.risk-high { background-color: #f8d7da; color: #721c24; }
</style>

<script>
$(document).ready(function() {
    // --- Score Calculation ---
    function calculateTotalScore() {
        let total = 0;
        
        // Skor 1
        let skor1 = $('#PASIEN_YA_1').is(':checked') ? 2 : 0;
        $('#skor1').text(skor1);
        total += skor1;

        // Skor 2
        let skor2 = $('#PENURUNAN_BB_YA_1').is(':checked') ? 1 : 0;
        $('#skor2').text(skor2);
        total += skor2;

        // Skor 3
        let skor3 = $('#KONDISI_YA_1').is(':checked') ? 1 : 0;
        $('#skor3').text(skor3);
        total += skor3;

        // Skor 4
        let skor4 = $('#TERDAPAT_PENYAKIT_YA_1').is(':checked') ? 1 : 0;
        $('#skor4').text(skor4);
        total += skor4;

        // Update total score display and hidden input
        $('#totalSkorDisplay').text(total);
        $('#TOTAL_SKOR').val(total);

        // Update risk indicator
        const riskIndicator = $('#riskIndicator');
        riskIndicator.removeClass('risk-low risk-medium risk-high');
        if (total === 0) {
            riskIndicator.addClass('risk-low').text('Resiko Rendah');
        } else if (total >= 1 && total <= 3) {
            riskIndicator.addClass('risk-medium').text('Resiko Sedang');
        } else if (total >= 4) {
            riskIndicator.addClass('risk-high').text('Resiko Berat');
        }
    }

    // --- Exclusive Checkbox Logic ---
    function setupExclusiveCheckboxes() {
        $('.skor-cb').on('change', function() {
            const group = $(this).data('group');
            // Uncheck others in the same group
            $(`.skor-cb[data-group="${group}"]`).not(this).prop('checked', false);
            calculateTotalScore();
        });
    }

    // Set default checked if no data exists
    function setDefaultChecks() {
        if (!"{{ $isUpdate }}") {
            $('#PASIEN_TIDAK_0, #PENURUNAN_BB_TIDAK_0, #KONDISI_TIDAK_0, #TERDAPAT_PENYAKIT_TIDAK_0').prop('checked', true);
        }
    }

    // Initial setup
    setDefaultChecks();
    setupExclusiveCheckboxes();
    calculateTotalScore();

    // --- AJAX Form Submission ---
    $('#rm26Form').on('submit', function(e) {
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
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#form-selector').trigger('change');
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
    $('#reset-rm26').on('click', function() {
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
                $('#rm26Form')[0].reset();
                setDefaultChecks();
                calculateTotalScore();
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });
});
</script>