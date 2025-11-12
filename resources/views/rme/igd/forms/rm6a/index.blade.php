@php
    // Helper function to get value from existing data or provide a default
    function getValueRm6a($data, $key, $default = '') {
        if (is_object($data) && property_exists($data, $key) && !is_null($data->$key)) {
            return trim($data->$key);
        }
        return $default;
    }

    // Determine if we are updating or creating
    $isUpdate = !empty($rm6a->TGLJAM_ENTRY);
    $submitButtonText = $isUpdate ? 'Update' : 'Simpan';
@endphp

<div class="card-body">
    <form id="rm6aForm" action="{{ route('rme.igd.form.rm6a.store') }}" method="post">
        @csrf
        {{-- Hidden fields for registration, user, and timestamp --}}
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="USER_ENTRY" value="{{ $user['username'] ?? '' }}">
        <input type="hidden" name="TGLJAM_ENTRY" value="{{ now()->format('Y-m-d H:i:s') }}">
        
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Informasi Umum</h3>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="TGL_SKRINING">Tanggal Skrining</label>
                        <input type="date" class="form-control" id="TGL_SKRINING" name="TGL_SKRINING" value="{{ getValueRm6a($rm6a, 'TGL_SKRINING') ? \Carbon\Carbon::parse(getValueRm6a($rm6a, 'TGL_SKRINING'))->format('Y-m-d') : now()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="JAM_SKRINING">Jam Skrining</label>
                        <input type="time" class="form-control" id="JAM_SKRINING" name="JAM_SKRINING" value="{{ getValueRm6a($rm6a, 'JAM_SKRINING') ? \Carbon\Carbon::parse(getValueRm6a($rm6a, 'JAM_SKRINING'))->format('H:i') : now()->format('H:i') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="TB">Tinggi Badan (cm)</label>
                        <input type="number" class="form-control" id="TB" name="TB" value="{{ getValueRm6a($rm3a, 'PACS4_TB') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="BB">Berat Badan (kg)</label>
                        <input type="number" class="form-control" id="BB" name="BB" value="{{ getValueRm6a($rm3a, 'PACS4_BB') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="IMT">Indeks Massa Tubuh</label>
                        <input type="text" class="form-control" id="IMT" name="IMT" value="{{ getValueRm6a($rm3a, 'BMI') }}" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="LINGKAR_BETIS">Lingkar Betis (cm)</label>
                        <input type="number" class="form-control" id="LINGKAR_BETIS" name="LINGKAR_BETIS" value="{{ getValueRm6a($rm6a, 'LINGKAR_BETIS') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="DIAGNOSIS_MEDIS">Diagnosis Medis</label>
                        <textarea class="form-control" id="DIAGNOSIS_MEDIS" name="DIAGNOSIS_MEDIS" rows="2">{{ getValueRm6a($rm3b, 'DIAGNOSIS_UTAMA') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scoring Section --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Penilaian Skor</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-info">
                            <tr>
                                <th style="width: 5%" class="text-center">No.</th>
                                <th style="width: 75%">Parameter</th>
                                <th style="width: 20%">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Skor A --}}
                            <tr>
                                <td class="text-center">A.</td>
                                <td>
                                    Apakah asupan makanan berkurang selama 3 bulan terakhir karena kehilangan nafsu makan, gangguan pencernaan, kesulitan mengunyah atau menelan?
                                    <div class="small text-muted mt-1">0 = asupan makanan sangat berkurang<br>1 = asupan makanan agak berkurang<br>2 = asupan makanan tidak berkurang</div>
                                </td>
                                <td>
                                    <div class="form-label-group mb-0">
                                        <select class="form-control skor" name="SKOR_A" id="SKOR_A">
                                            <option value="0" {{ getValueRm6a($rm6a, 'SKOR_A') == '0' ? 'selected' : '' }}>0</option>
                                            <option value="1" {{ getValueRm6a($rm6a, 'SKOR_A') == '1' ? 'selected' : '' }}>1</option>
                                            <option value="2" {{ getValueRm6a($rm6a, 'SKOR_A', '2') == '2' ? 'selected' : '' }}>2</option>
                                        </select>
                                        <label for="SKOR_A">Pilih Skor A</label>
                                    </div>
                                </td>
                            </tr>
                            {{-- Skor B --}}
                            <tr class="align-middle">
                                <td class="text-center">B.</td>
                                <td>
                                    Penurunan berat badan selama 3 tahun terakhir
                                    <div class="small text-muted mt-1">0 = penurunan berat badan &gt; 3 kg<br>1 = tidak tahu<br>2 = penurunan berat badan 1-3 kg<br>3 = tidak ada penurunan berat badan</div>
                                </td>
                                <td>
                                    <div class="form-label-group mb-0">
                                        <select class="form-control skor" name="SKOR_B" id="SKOR_B">
                                            <option value="0" {{ getValueRm6a($rm6a, 'SKOR_B') == '0' ? 'selected' : '' }}>0</option>
                                            <option value="1" {{ getValueRm6a($rm6a, 'SKOR_B') == '1' ? 'selected' : '' }}>1</option>
                                            <option value="2" {{ getValueRm6a($rm6a, 'SKOR_B') == '2' ? 'selected' : '' }}>2</option>
                                            <option value="3" {{ getValueRm6a($rm6a, 'SKOR_B', '3') == '3' ? 'selected' : '' }}>3</option>
                                        </select>
                                        <label for="SKOR_B">Pilih Skor B</label>
                                    </div>
                                </td>
                            </tr>
                            {{-- Skor C --}}
                            <tr>
                                <td class="text-center">C.</td>
                                <td>
                                    Mobilitas
                                    <div class="small text-muted mt-1">0 = terbatas ditempat tidur atau kursi<br>1 = mampu bangun dari tempat tidur/kursi tetapi tidak bepergian ke luar rumah<br>2 = dapat bepergian ke luar rumah</div>
                                </td>
                                <td>
                                    <div class="form-label-group mb-0">
                                        <select class="form-control skor" name="SKOR_C" id="SKOR_C">
                                            <option value="0" {{ getValueRm6a($rm6a, 'SKOR_C') == '0' ? 'selected' : '' }}>0</option>
                                            <option value="1" {{ getValueRm6a($rm6a, 'SKOR_C') == '1' ? 'selected' : '' }}>1</option>
                                            <option value="2" {{ getValueRm6a($rm6a, 'SKOR_C', '2') == '2' ? 'selected' : '' }}>2</option>
                                        </select>
                                        <label for="SKOR_C">Pilih Skor C</label>
                                    </div>
                                </td>
                            </tr>
                            {{-- Skor D --}}
                            <tr>
                                <td class="text-center">D.</td>
                                <td>
                                    Menderita tekanan psikologis atau penyakit yang berat dalam 3 bulan terakhir
                                    <div class="small text-muted mt-1">0 = ya<br>2 = tidak</div>
                                </td>
                                <td>
                                    <div class="form-label-group mb-0">
                                        <select class="form-control skor" name="SKOR_D" id="SKOR_D">
                                            <option value="0" {{ getValueRm6a($rm6a, 'SKOR_D') == '0' ? 'selected' : '' }}>0</option>
                                            <option value="2" {{ getValueRm6a($rm6a, 'SKOR_D', '2') == '2' ? 'selected' : '' }}>2</option>
                                        </select>
                                        <label for="SKOR_D">Pilih Skor D</label>
                                    </div>
                                </td>
                            </tr>
                            {{-- Skor E --}}
                            <tr>
                                <td class="text-center">E.</td>
                                <td>
                                    Gangguan neuropsikologis
                                    <div class="small text-muted mt-1">0 = depresi berat atau kepikunan berat<br>1 = kepikunan ringan<br>2 = tidak ada gangguan psikologis</div>
                                </td>
                                <td>
                                    <div class="form-label-group mb-0">
                                        <select class="form-control skor" name="SKOR_E" id="SKOR_E">
                                            <option value="0" {{ getValueRm6a($rm6a, 'SKOR_E') == '0' ? 'selected' : '' }}>0</option>
                                            <option value="1" {{ getValueRm6a($rm6a, 'SKOR_E') == '1' ? 'selected' : '' }}>1</option>
                                            <option value="2" {{ getValueRm6a($rm6a, 'SKOR_E', '2') == '2' ? 'selected' : '' }}>2</option>
                                        </select>
                                        <label for="SKOR_E">Pilih Skor E</label>
                                    </div>
                                </td>
                            </tr>
                            {{-- Skor F1 (IMT) --}}
                            @if(!empty(getValueRm6a($rm3a, 'BMI')))
                            <tr id="skorF1Row">
                                <td class="text-center">F1.</td>
                                <td>
                                    Indeks Massa Tubuh (IMT)
                                    <div class="small text-muted mt-1">0 = IMT &lt; 19<br>1 = IMT 19 hingga &lt; 21<br>2 = IMT 21 hingga &lt; 23<br>3 = IMT &gt;= 23</div>
                                </td>
                                <td>
                                    <div class="form-label-group mb-0">
                                        <select class="form-control skor" name="SKOR_F1" id="SKOR_F1">
                                            <option value="0" {{ getValueRm6a($rm6a, 'SKOR_F1') == '0' ? 'selected' : '' }}>0</option>
                                            <option value="1" {{ getValueRm6a($rm6a, 'SKOR_F1') == '1' ? 'selected' : '' }}>1</option>
                                            <option value="2" {{ getValueRm6a($rm6a, 'SKOR_F1') == '2' ? 'selected' : '' }}>2</option>
                                            <option value="3" {{ getValueRm6a($rm6a, 'SKOR_F1', '3') == '3' ? 'selected' : '' }}>3</option>
                                        </select>
                                        <label for="SKOR_F1">Pilih Skor F1</label>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            {{-- Skor F2 (Lingkar Betis) --}}
                            @if(empty(getValueRm6a($rm3a, 'BMI')))
                            <tr id="skorF2Row">
                                <td class="text-center">F2.</td>
                                <td>
                                    Lingkar Betis
                                    <div class="small text-muted mt-1">0 = Lingkar betis &lt; 31 cm<br>3 = Lingkar betis &gt;= 31 cm</div>
                                </td>
                                <td>
                                    <div class="form-label-group mb-0">
                                        <select class="form-control skor" name="SKOR_F2" id="SKOR_F2">
                                            <option value="0" {{ getValueRm6a($rm6a, 'SKOR_F2') == '0' ? 'selected' : '' }}>0</option>
                                            <option value="3" {{ getValueRm6a($rm6a, 'SKOR_F2', '3') == '3' ? 'selected' : '' }}>3</option>
                                        </select>
                                        <label for="SKOR_F2">Pilih Skor F2</label>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
    
                {{-- Total Score and Interpretation --}}
                <div class="row mt-4 align-items-center">
                    <div class="col-md-4">
                        <label for="TOTAL_SKOR" class="form-label font-weight-bold">Total Skor</label>
                        <div class="input-group">
                            <input type="number" class="form-control form-control-lg font-weight-bold text-center" id="TOTAL_SKOR" name="TOTAL_SKOR" value="{{ getValueRm6a($rm6a, 'TOTAL_SKOR') }}" readonly style="background-color: #e9ecef; font-size: 1.5rem;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card border-left-0 border-info shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <h5 class="card-title font-weight-bold text-info">
                                    <i class="fas fa-info-circle mr-2"></i>Interpretasi Skor
                                </h5>
                                <p id="skorKeterangan" class="card-text mt-2 mb-0" style="font-size: 1.2rem;">
                                    <span id="skorIcon" class="mr-2"></span>
                                    <span id="skorText" class="font-weight-bold">-</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fas fa-save mr-1"></i> {{ $submitButtonText }}
            </button>
            <button type="button" class="btn btn-outline-danger" id="reset-rm6a">
                <i class="fas fa-times-circle mr-1"></i> Batal
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Function to update score interpretation
    function updateSkorKeterangan() {
        const totalScoreInput = $('#TOTAL_SKOR');
        const skorText = $('#skorText');
        const skorIcon = $('#skorIcon');
        const skorKeterangan = $('#skorKeterangan');
        const score = parseInt(totalScoreInput.val()) || 0;

        let text = '-';
        let iconHtml = '';
        let className = 'mb-0 fs-5 fw-semibold text-primary d-flex align-items-center';

        if (score >= 12) {
            text = 'Normal Nutrisi';
            className = 'text-success';
            iconHtml = '<i class="fas fa-check-circle"></i>';
        } else if (score >= 8 && score <= 11) {
            text = 'Risiko Malnutrisi';
            className = 'text-warning';
            iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
        } else {
            text = 'Malnutrisi';
            className = 'text-danger';
            iconHtml = '<i class="fas fa-times-circle"></i>';
        }

        skorText.text(text).attr('class', `font-weight-bold ${className}`);
        skorIcon.html(iconHtml);
        skorKeterangan.attr('class', className);
    }

    // Function to calculate total score
    function calculateTotal() {
        let total = 0;
        $('.skor').each(function() {
            // Only add score if the row is visible
            if ($(this).closest('tr').is(':visible')) {
                total += parseInt($(this).val()) || 0;
            }
        });
        $('#TOTAL_SKOR').val(total);
        updateSkorKeterangan();
    }

    // Automatically select score based on IMT or Lingkar Betis
    function autoSelectScores() {
        // Hanya jalankan jika data RM6A belum ada (saat pengisian baru)
        if ("{{ $isUpdate }}") return;

        // F1 Score based on IMT
        const imt = parseFloat($('#IMT').val());
        if (!isNaN(imt)) {
            let skorF1 = 3; // Default
            if (imt < 19) {
                skorF1 = 0;
            } else if (imt >= 19 && imt < 21) {
                skorF1 = 1;
            } else if (imt >= 21 && imt < 23) {
                skorF1 = 2;
            }
            $('#SKOR_F1').val(skorF1);
        }

        // F2 Score based on Lingkar Betis
        const lingkarBetis = parseFloat($('#LINGKAR_BETIS').val());
        if (!isNaN(lingkarBetis)) {
            let skorF2 = 3; // Default
            if (lingkarBetis < 31) {
                skorF2 = 0;
            }
            $('#SKOR_F2').val(skorF2);
        }
    }

    // Initialize event listeners
    function initScoreCalculation() {
        // Panggil autoSelectScores hanya sekali saat inisialisasi
        autoSelectScores();
        
        // Hitung total awal dan perbarui keterangan skor
        calculateTotal();

        $('.skor').on('change', calculateTotal);
    }

    // Call the initialization function
    initScoreCalculation();

    // AJAX form submission
    $('#rm6aForm').on('submit', function(e) {
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
                    // Reload the form to show updated data
                    $('#form-selector').trigger('change');
                } else {
                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan saat menyimpan.', 'error');
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
                // Re-enable button only if it's not a success, because success reloads the form
                if (!xhr.responseJSON || xhr.responseJSON.status !== 'success') {
                    button.prop('disabled', false).html(originalButtonText);
                }
            }
        });
    });

    // Reset button logic
    $('#reset-rm6a').on('click', function() {
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
                $('#rm6aForm')[0].reset();
                // Re-trigger calculations and selections
                initScoreCalculation();
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });
});
</script>