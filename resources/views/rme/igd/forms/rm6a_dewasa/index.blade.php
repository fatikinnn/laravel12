@php
    function getValueRm6aDewasa($data, $key, $default = '') {
        if (is_object($data) && property_exists($data, $key) && !is_null($data->$key)) {
            return trim($data->$key);
        }
        return $default;
    }
    function isCheckedRm6aDewasa($data, $key) {
        return getValueRm6aDewasa($data, $key) == 1 ? 'checked' : '';
    }
    $isUpdate = !empty($rm6a_dewasa->TGLJAM_ENTRY);
    $submitButtonText = $isUpdate ? 'Update' : 'Simpan';
@endphp

<div class="card-body">
    <form id="rm6aDewasaForm" action="{{ route('rme.igd.form.rm6a_dewasa.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="USER_ENTRY" value="{{ $user['username'] ?? '' }}">
        <input type="hidden" name="TGLJAM_ENTRY" value="{{ now()->format('Y-m-d H:i:s') }}">

        {{-- Informasi Umum --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Informasi Umum</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="TGL_SKRINING">Tanggal & Jam Skrining</label>
                        <input type="datetime-local" class="form-control" name="TGL_SKRINING" id="TGL_SKRINING" required value="{{ getValueRm6aDewasa($rm6a_dewasa, 'TGL_SKRINING') ? \Carbon\Carbon::parse(getValueRm6aDewasa($rm6a_dewasa, 'TGL_SKRINING'))->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="NAMA_DOKTER">Nama Dokter</label>
                        <input type="text" class="form-control" name="NAMA_DOKTER" maxlength="100" value="{{ getValueRm6aDewasa($rm6a_dewasa, 'NAMA_DOKTER', $user['username'] ?? '') }}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="LILA">LILA (cm)</label>
                        <input type="text" class="form-control" name="LILA" id="LILA" maxlength="10" value="{{ getValueRm6aDewasa($rm6a_dewasa, 'LILA') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="TB">Tinggi Badan (cm)</label>
                        <input type="text" class="form-control" name="TB" id="TB" maxlength="10" value="{{ getValueRm6aDewasa($rm3a, 'PACS4_TB') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="BB">Berat Badan (kg)</label>
                        <input type="text" class="form-control" name="BB" id="BB" maxlength="10" value="{{ getValueRm6aDewasa($rm3a, 'PACS4_BB') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="IMT">IMT (kg/m²)</label>
                        <input type="text" class="form-control" name="IMT" id="IMT" maxlength="15" value="{{ getValueRm6aDewasa($rm3a, 'BMI') }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="DIAGNOSIS_MEDIS">Diagnosis Medis</label>
                    <textarea class="form-control" name="DIAGNOSIS_MEDIS" id="DIAGNOSIS_MEDIS" rows="2">{{ getValueRm6aDewasa($rm3b, 'DIAGNOSIS_UTAMA') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Diet --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Jenis Diet & Diet Khusus</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Jenis Diet</h6>
                        <div class="row">
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="CAIR" id="CAIR" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'CAIR') }}><label class="form-check-label" for="CAIR">Cair</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="SONDE" id="SONDE" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'SONDE') }}><label class="form-check-label" for="SONDE">Sonde</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="BUBUR_SARING" id="BUBUR_SARING" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'BUBUR_SARING') }}><label class="form-check-label" for="BUBUR_SARING">Bubur Saring</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="BUBUR_SUMSUM" id="BUBUR_SUMSUM" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'BUBUR_SUMSUM') }}><label class="form-check-label" for="BUBUR_SUMSUM">Bubur Sumsum</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="BUBUR_BIASA" id="BUBUR_BIASA" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'BUBUR_BIASA') }}><label class="form-check-label" for="BUBUR_BIASA">Bubur Biasa</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="NASI_TIM" id="NASI_TIM" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'NASI_TIM') }}><label class="form-check-label" for="NASI_TIM">Nasi Tim</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="NASI" id="NASI" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'NASI') }}><label class="form-check-label" for="NASI">Nasi</label></div></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Diet Khusus</h6>
                        <div class="row">
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="DIABETES_MELITUS" id="DIABETES_MELITUS" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'DIABETES_MELITUS') }}><label class="form-check-label" for="DIABETES_MELITUS">Diabetes Melitus</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="RG_DM" id="RG_DM" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'RG_DM') }}><label class="form-check-label" for="RG_DM">Rendah Gula</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="TKTP" id="TKTP" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'TKTP') }}><label class="form-check-label" for="TKTP">Tinggi Kalori Tinggi Protein</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="RENDAH_GARAM" id="RENDAH_GARAM" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'RENDAH_GARAM') }}><label class="form-check-label" for="RENDAH_GARAM">Rendah Garam</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="RENDAH_PROTEIN" id="RENDAH_PROTEIN" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'RENDAH_PROTEIN') }}><label class="form-check-label" for="RENDAH_PROTEIN">Rendah Protein</label></div></div>
                            <div class="col-6"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="RENDAH_PURIN" id="RENDAH_PURIN" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'RENDAH_PURIN') }}><label class="form-check-label" for="RENDAH_PURIN">Rendah Purin</label></div></div>
                        </div>
                        <div class="input-group mt-2">
                            <div class="input-group-prepend"><div class="input-group-text"><input type="checkbox" name="KET_LAINNYA" value="1" id="KET_LAINNYA" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'KET_LAINNYA') }}></div></div>
                            <input type="text" class="form-control" name="KET_LAINNYA_TEXT" maxlength="50" placeholder="Lainnya..." value="{{ getValueRm6aDewasa($rm6a_dewasa, 'KET_LAINNYA_TEXT') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Malnutrition Screening Tool (MST) --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Malnutrition Screening Tool (MST)</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr>
                                <th style="width: 5%" class="text-center">No</th>
                                <th>Parameter</th>
                                <th style="width: 25%">Pilihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>
                                    <strong>Apakah pasien mengalami penurunan berat badan yang tidak direncanakan?</strong>
                                    <ul class="mb-0 small text-muted">
                                        <li>Tidak (Skor 0)</li>
                                        <li>Tidak yakin (baju/celana terasa longgar) (Skor 2)</li>
                                        <li>Ya, berapa penurunan berat badan tersebut:
                                            <ul id="weightLossDetails" style="display: {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_TIDAK') == 'ya' ? 'block' : 'none' }};">
                                                <li>1 - 5 kg (Skor 1)</li>
                                                <li>6 - 10 kg (Skor 2)</li>
                                                <li>11 - 15 kg (Skor 3)</li>
                                                <li>&gt; 15 kg (Skor 4)</li>
                                                <li>Tidak yakin (Skor 2)</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                <td class="align-middle">
                                    <select class="form-control" name="PENURUNAN_BB_TIDAK" id="PENURUNAN_BB_SELECT">
                                        <option value="0" {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_TIDAK') == '0' ? 'selected' : '' }}>Tidak</option>
                                        <option value="2" {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_TIDAK') == '2' ? 'selected' : '' }}>Tidak Yakin</option>
                                        <option value="ya" {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_TIDAK') == 'ya' ? 'selected' : '' }}>Ya</option>
                                    </select>
                                    <select class="form-control mt-2" name="PENURUNAN_BB_YA" id="PENURUNAN_BB_DETAIL" style="display: {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_TIDAK') == 'ya' ? 'block' : 'none' }};">
                                        <option value="">Pilih...</option>
                                        <option value="1" {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_YA') == '1' ? 'selected' : '' }}>1 - 5 kg</option>
                                        <option value="2" {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_YA') == '2' ? 'selected' : '' }}>6 - 10 kg / Tidak Yakin</option>
                                        <option value="3" {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_YA') == '3' ? 'selected' : '' }}>11 - 15 kg</option>
                                        <option value="4" {{ getValueRm6aDewasa($rm6a_dewasa, 'PENURUNAN_BB_YA') == '4' ? 'selected' : '' }}>&gt; 15 kg</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>
                                    <strong>Apakah asupan makanan pasien buruk akibat nafsu makan yang menurun?</strong>
                                    <p class="mb-0 small text-muted">(misalnya asupan makan hanya 3/4 dari biasanya)</p>
                                </td>
                                <td class="align-middle">
                                    <select class="form-control" name="ASUPAN_MAKAN" id="ASUPAN_MAKAN_SELECT">
                                        <option value="0" {{ getValueRm6aDewasa($rm6a_dewasa, 'ASUPAN_MAKAN') == '0' ? 'selected' : '' }}>Tidak (Skor 0)</option>
                                        <option value="1" {{ getValueRm6aDewasa($rm6a_dewasa, 'ASUPAN_MAKAN') == '1' ? 'selected' : '' }}>Ya (Skor 1)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td><strong>Apakah pasien dengan kondisi khusus?</strong></td>
                                <td class="align-middle">
                                    <select class="form-control" name="KONDISI_KHUSUS" id="KONDISI_KHUSUS_SELECT">
                                        <option value="0" {{ getValueRm6aDewasa($rm6a_dewasa, 'KONDISI_KHUSUS') == '0' ? 'selected' : '' }}>Tidak (Skor 0)</option>
                                        <option value="1" {{ getValueRm6aDewasa($rm6a_dewasa, 'KONDISI_KHUSUS') == '1' ? 'selected' : '' }}>Ya (Skor 1)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="2" class="text-right font-weight-bold">Total Skor:</td>
                                <td>
                                    <input type="text" class="form-control font-weight-bold text-center" id="TOTAL_SKOR" name="TOTAL_SKOR" value="{{ getValueRm6aDewasa($rm6a_dewasa, 'TOTAL_SKOR', '0') }}" readonly>
                                </td>
                            </tr>
                            <tr class="table-warning">
                                <td colspan="2" class="text-right font-weight-bold">Hasil Skrining:</td>
                                <td>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="KESIMPULAN_1" name="KESIMPULAN_1" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'KESIMPULAN_1') }}>
                                        <label class="form-check-label font-weight-bold text-danger" for="KESIMPULAN_1">Risiko malnutrisi (skor ≥ 2)</label>
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="KESIMPULAN_2" name="KESIMPULAN_2" value="1" {{ isCheckedRm6aDewasa($rm6a_dewasa, 'KESIMPULAN_2') }}>
                                        <label class="form-check-label font-weight-bold text-success" for="KESIMPULAN_2">Tidak berisiko (skor &lt; 2)</label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save mr-1"></i> {{ $submitButtonText }}</button>
            <button type="button" class="btn btn-outline-danger" id="reset-rm6a-dewasa"><i class="fas fa-times-circle mr-1"></i> Batal</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // --- IMT Calculation ---
    function hitungIMT() {
        const tb = parseFloat($('#TB').val()); // cm
        const bb = parseFloat($('#BB').val()); // kg

        if (!isNaN(tb) && !isNaN(bb) && tb > 0) {
            const tinggiMeter = tb / 100;
            const imt = bb / (tinggiMeter * tinggiMeter);
            $('#IMT').val(imt.toFixed(2));
        } else {
            $('#IMT').val('');
        }
    }
    $('#TB, #BB').on('input', hitungIMT);

    // --- MST Score Calculation ---
    function calculateTotalScore() {
        let weightLossPoints = 0;
        const weightLossSelect = $('#PENURUNAN_BB_SELECT');
        const weightLossDetailSelect = $('#PENURUNAN_BB_DETAIL');

        if (weightLossSelect.val() === 'ya') {
            weightLossPoints = parseInt(weightLossDetailSelect.val()) || 0;
        } else {
            weightLossPoints = parseInt(weightLossSelect.val()) || 0;
        }

        const foodIntakePoints = parseInt($('#ASUPAN_MAKAN_SELECT').val()) || 0;
        const specialConditionPoints = parseInt($('#KONDISI_KHUSUS_SELECT').val()) || 0;
        const totalScore = weightLossPoints + foodIntakePoints + specialConditionPoints;

        $('#TOTAL_SKOR').val(totalScore);

        // Update conclusions
        if (totalScore >= 2) {
            $('#KESIMPULAN_1').prop('checked', true);
            $('#KESIMPULAN_2').prop('checked', false);
        } else {
            $('#KESIMPULAN_1').prop('checked', false);
            $('#KESIMPULAN_2').prop('checked', true);
        }
    }

    function toggleWeightLossDetails() {
        const weightLossSelect = $('#PENURUNAN_BB_SELECT');
        const detailsDiv = $('#weightLossDetails');
        const detailSelect = $('#PENURUNAN_BB_DETAIL');

        if (weightLossSelect.val() === 'ya') {
            detailsDiv.slideDown();
            detailSelect.slideDown();
        } else {
            detailsDiv.slideUp();
            detailSelect.slideUp();
            detailSelect.val(''); // Kosongkan nilai jika tidak 'ya'
        }
    }

    // Event listeners for MST
    $('#PENURUNAN_BB_SELECT, #PENURUNAN_BB_DETAIL, #ASUPAN_MAKAN_SELECT, #KONDISI_KHUSUS_SELECT').on('change', function() {
        toggleWeightLossDetails();
        calculateTotalScore();
    });

    // Exclusive checkboxes for conclusion
    $('#KESIMPULAN_1').on('change', function() { if ($(this).is(':checked')) $('#KESIMPULAN_2').prop('checked', false); });
    $('#KESIMPULAN_2').on('change', function() { if ($(this).is(':checked')) $('#KESIMPULAN_1').prop('checked', false); });

    // Initial calculation on page load
    hitungIMT();
    toggleWeightLossDetails();
    calculateTotalScore();

    // --- AJAX Form Submission ---
    $('#rm6aDewasaForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        
        // Prepare data, handle unchecked checkboxes
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
                    $('#form-selector').trigger('change'); // Reload form
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
    $('#reset-rm6a-dewasa').on('click', function() {
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
                $('#rm6aDewasaForm')[0].reset();
                // Re-trigger calculations and UI changes
                hitungIMT();
                toggleWeightLossDetails();
                calculateTotalScore();
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });
});
</script>