@php
    function getValue($data, $key, $default = '') {
        return data_get($data, $key, $default);
    }
    function isChecked($data, $key, $value) {
        return data_get($data, $key) == $value ? 'checked' : '';
    }
@endphp

<div class="card-body">
    <form id="formSkriningGiziHamil" action="{{ route('rme.igd.form.skrininggiziibuhamil.store') }}" method="POST">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ htmlspecialchars($noPendaftaran) }}">
        <input type="hidden" name="NORM" value="{{ htmlspecialchars($norm) }}">

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="TGL_SKRINING" class="form-label fw-bold">Tanggal Skrining</label>
                <input type="datetime-local" class="form-control" id="TGL_SKRINING" name="TGL_SKRINING" value="{{ getValue($data, 'TGL_SKRINING') ? \Carbon\Carbon::parse(getValue($data, 'TGL_SKRINING'))->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-info text-center">
                    <tr>
                        <th style="width: 50%;">Parameter</th>
                        <th>Jawaban</th>
                        <th style="width: 10%;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Pertanyaan 1 -->
                    <tr>
                        <td>1. Apakah ada penurunan nafsu makan?</td>
                        <td class="text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="NAFSUMAKAN" id="nafsumakan_tidak" value="0" {{ isChecked($data, 'NAFSUMAKAN_TIDAK', 1) }}>
                                <label class="form-check-label" for="nafsumakan_tidak">Tidak</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="NAFSUMAKAN" id="nafsumakan_ya" value="1" {{ isChecked($data, 'NAFSUMAKAN_YA', 1) }}>
                                <label class="form-check-label" for="nafsumakan_ya">Ya</label>
                            </div>
                        </td>
                        <td class="text-center" id="skor_nafsumakan">0</td>
                    </tr>

                    <!-- Pertanyaan 2 -->
                    <tr>
                        <td>2. Adanya gangguan metabolisme (DM, Gangguan fungsi Tyroid, Infeksi Kronis (HIV, TB)) atau gangguan lain?</td>
                        <td class="text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="METABOLISME" id="metabolisme_tidak" value="0" {{ isChecked($data, 'METABOLISME_TIDAK', 1) }}>
                                <label class="form-check-label" for="metabolisme_tidak">Tidak</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="METABOLISME" id="metabolisme_ya" value="1" {{ isChecked($data, 'METABOLISME_YA', 1) }}>
                                <label class="form-check-label" for="metabolisme_ya">Ya</label>
                            </div>
                            <input type="text" class="form-control mt-2" name="GANGGUANLAIN" id="GANGGUANLAIN" placeholder="Sebutkan gangguan lain..." value="{{ htmlspecialchars(getValue($data, 'GANGGUANLAIN')) }}">
                        </td>
                        <td class="text-center" id="skor_metabolisme">0</td>
                    </tr>

                    <!-- Pertanyaan 3 -->
                    <tr>
                        <td>3. Adanya pertambahan BB yang tidak adekuat (&lt;0,5 kg/minggu di T2 & T3) atau &gt; 2 kg/minggu?</td>
                        <td class="text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="BERTAMBAHBB" id="bertambahbb_tidak" value="0" {{ isChecked($data, 'BERTAMBAHBB_TIDAK', 1) }}>
                                <label class="form-check-label" for="bertambahbb_tidak">Tidak</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="BERTAMBAHBB" id="bertambahbb_ya" value="1" {{ isChecked($data, 'BERTAMBAHBB_YA', 1) }}>
                                <label class="form-check-label" for="bertambahbb_ya">Ya</label>
                            </div>
                        </td>
                        <td class="text-center" id="skor_bertambahbb">0</td>
                    </tr>

                    <!-- Pertanyaan 4 -->
                    <tr>
                        <td>4. Nilai HB &lt; 11 gr/dl atau HCT &lt; 30%?</td>
                        <td class="text-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="NILAIHB" id="nilaihb_tidak" value="0" {{ isChecked($data, 'NILAIHB_TIDAK', 1) }}>
                                <label class="form-check-label" for="nilaihb_tidak">Tidak</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="NILAIHB" id="nilaihb_ya" value="1" {{ isChecked($data, 'NILAIHB_YA', 1) }}>
                                <label class="form-check-label" for="nilaihb_ya">Ya</label>
                            </div>
                        </td>
                        <td class="text-center" id="skor_nilaihb">0</td>
                    </tr>

                    <!-- Total Skor -->
                    <tr class="table-primary font-weight-bold">
                        <td colspan="2" class="text-right">Total Skor</td>
                        <td class="text-center" id="total_skor">
                            {{ htmlspecialchars(getValue($data, 'TOTALSKOR', '0')) }}
                        </td>
                        <input type="hidden" name="TOTALSKOR" id="TOTALSKOR" value="{{ htmlspecialchars(getValue($data, 'TOTALSKOR', '0')) }}">
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <p class="font-weight-bold">Kesimpulan:</p>
            <p>Total Skor (Jika jawaban "Ya" ≥ 1, dilakukan pengkajian lanjut oleh Dietisien)</p>
            <div class="mb-3">
                <label for="DIETFISIEN_KET" class="form-label">Keterangan Pengkajian Lanjut Dietisien:</label>
                <textarea class="form-control" id="DIETFISIEN_KET" name="DIETFISIEN_KET" rows="3" placeholder="Isi keterangan jika diperlukan pengkajian lanjut...">{{ htmlspecialchars(getValue($data, 'DIETFISIEN_KET')) }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        function calculateTotalScore() {
            let total = 0;
            
            let skor1 = parseInt($('input[name="NAFSUMAKAN"]:checked').val()) || 0;
            $('#skor_nafsumakan').text(skor1);
            total += skor1;

            let skor2 = parseInt($('input[name="METABOLISME"]:checked').val()) || 0;
            $('#skor_metabolisme').text(skor2);
            total += skor2;

            let skor3 = parseInt($('input[name="BERTAMBAHBB"]:checked').val()) || 0;
            $('#skor_bertambahbb').text(skor3);
            total += skor3;

            let skor4 = parseInt($('input[name="NILAIHB"]:checked').val()) || 0;
            $('#skor_nilaihb').text(skor4);
            total += skor4;

            $('#total_skor').text(total);
            $('#TOTALSKOR').val(total);
        }

        // Initial calculation on page load
        calculateTotalScore();

        // Recalculate on change
        $('input[type=radio]').on('change', function() {
            calculateTotalScore();
        });

        // Enable/disable GANGGUANLAIN input
        $('input[name="METABOLISME"]').on('change', function() {
            if ($('#metabolisme_ya').is(':checked')) {
                $('#GANGGUANLAIN').prop('disabled', false);
            } else {
                $('#GANGGUANLAIN').prop('disabled', true).val('');
            }
        }).trigger('change');

        // AJAX form submission
        $('#formSkriningGiziHamil').on('submit', function(e) {
            e.preventDefault();

            // Validation
            if (!$('input[name="NAFSUMAKAN"]:checked').length ||
                !$('input[name="METABOLISME"]:checked').length ||
                !$('input[name="BERTAMBAHBB"]:checked').length ||
                !$('input[name="NILAIHB"]:checked').length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Harap lengkapi semua pertanyaan skrining.'
                });
                return;
            }

            var form = $(this);
            var url = form.attr('action');
            var formData = new FormData(this);
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
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // The global ajaxSuccess handler will refresh the form content
                    } else {
                         Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
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
                    submitButton.html(originalButtonText).prop('disabled', false);
                }
            });
        });
    });
</script>