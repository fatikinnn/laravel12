@php
    function getValue($data, $key, $default = '') {
        return data_get($data, $key, $default);
    }
    function isChecked($data, $key, $value = 1) {
        return data_get($data, $key) == $value ? 'checked' : '';
    }
@endphp

<div class="card-body">
    <form id="formLukaReeda" action="{{ route('rme.igd.form.lukareeda.store') }}" method="POST">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ htmlspecialchars($noPendaftaran) }}">
        <input type="hidden" name="NORM" value="{{ htmlspecialchars($norm) }}">

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="JENISLUKA" class="form-label fw-bold">Jenis Luka</label>
                <input type="text" class="form-control" id="JENISLUKA" name="JENISLUKA" value="{{ getValue($data, 'JENISLUKA') }}">
            </div>
            <div class="col-md-6">
                <label for="UNIT" class="form-label fw-bold">Unit</label>
                <input type="text" class="form-control" id="UNIT" name="UNIT" value="{{ getValue($data, 'UNIT') }}">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered reeda-table">
                <thead class="table-info text-center">
                    <tr>
                        <th style="width: 15%;">Parameter</th>
                        <th style="width: 10%;">Skala</th>
                        <th>Kriteria</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- R - Redness -->
                    <tr>
                        <td rowspan="4" class="text-center align-middle fw-bold">R</td>
                        <td rowspan="4" class="align-middle">Kemerahan (Redness)</td>
                        <td class="reeda-option {{ isChecked($data, 'R_TDKMERAH0') ? 'selected' : '' }}"><span>Tidak ada kemerahan</span><input type="checkbox" name="R_TDKMERAH0" value="0" {{ isChecked($data, 'R_TDKMERAH0') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'R_MERAH1') ? 'selected' : '' }}"><span>Kemerahan ≤ 0.25 cm dari garis luka</span><input type="checkbox" name="R_MERAH1" value="1" {{ isChecked($data, 'R_MERAH1') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'R_MERAH2') ? 'selected' : '' }}"><span>Kemerahan 0.25 - 0.5 cm dari garis luka</span><input type="checkbox" name="R_MERAH2" value="2" {{ isChecked($data, 'R_MERAH2') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'R_MERAH3') ? 'selected' : '' }}"><span>Kemerahan > 0.5 cm dari garis luka</span><input type="checkbox" name="R_MERAH3" value="3" {{ isChecked($data, 'R_MERAH3') }}></td>
                    </tr>

                    <!-- E - Edema -->
                    <tr>
                        <td rowspan="4" class="text-center align-middle fw-bold">E</td>
                        <td rowspan="4" class="align-middle">Edema (Edema)</td>
                        <td class="reeda-option {{ isChecked($data, 'E_TDKEDEMA0') ? 'selected' : '' }}"><span>Tidak ada edema</span><input type="checkbox" name="E_TDKEDEMA0" value="0" {{ isChecked($data, 'E_TDKEDEMA0') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'E_RINGAN1') ? 'selected' : '' }}"><span>Edema ringan < 1 cm</span><input type="checkbox" name="E_RINGAN1" value="1" {{ isChecked($data, 'E_RINGAN1') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'E_SEDANG2') ? 'selected' : '' }}"><span>Edema sedang 1-2 cm</span><input type="checkbox" name="E_SEDANG2" value="2" {{ isChecked($data, 'E_SEDANG2') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'E_BERAT3') ? 'selected' : '' }}"><span>Edema berat > 2 cm</span><input type="checkbox" name="E_BERAT3" value="3" {{ isChecked($data, 'E_BERAT3') }}></td>
                    </tr>

                    <!-- E - Ecchymosis -->
                    <tr>
                        <td rowspan="4" class="text-center align-middle fw-bold">E</td>
                        <td rowspan="4" class="align-middle">Memar (Ecchymosis)</td>
                        <td class="reeda-option {{ isChecked($data, 'E_TDKMEMAR0') ? 'selected' : '' }}"><span>Tidak ada memar</span><input type="checkbox" name="E_TDKMEMAR0" value="0" {{ isChecked($data, 'E_TDKMEMAR0') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'E_MEMAR1') ? 'selected' : '' }}"><span>Memar ≤ 0.25 cm</span><input type="checkbox" name="E_MEMAR1" value="1" {{ isChecked($data, 'E_MEMAR1') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'E_MEMAR2') ? 'selected' : '' }}"><span>Memar 0.25 - 1 cm</span><input type="checkbox" name="E_MEMAR2" value="2" {{ isChecked($data, 'E_MEMAR2') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'E_MEMAR3') ? 'selected' : '' }}"><span>Memar > 1 cm</span><input type="checkbox" name="E_MEMAR3" value="3" {{ isChecked($data, 'E_MEMAR3') }}></td>
                    </tr>

                    <!-- D - Discharge -->
                    <tr>
                        <td rowspan="4" class="text-center align-middle fw-bold">D</td>
                        <td rowspan="4" class="align-middle">Cairan (Discharge)</td>
                        <td class="reeda-option {{ isChecked($data, 'D_TDKCAIRAN0') ? 'selected' : '' }}"><span>Tidak ada cairan</span><input type="checkbox" name="D_TDKCAIRAN0" value="0" {{ isChecked($data, 'D_TDKCAIRAN0') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'D_SEROSASDKT1') ? 'selected' : '' }}"><span>Cairan serosa sedikit</span><input type="checkbox" name="D_SEROSASDKT1" value="1" {{ isChecked($data, 'D_SEROSASDKT1') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'D_SERSDG2') ? 'selected' : '' }}"><span>Cairan serosa sedang</span><input type="checkbox" name="D_SERSDG2" value="2" {{ isChecked($data, 'D_SERSDG2') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'D_PURULEN3') ? 'selected' : '' }}"><span>Cairan purulen</span><input type="checkbox" name="D_PURULEN3" value="3" {{ isChecked($data, 'D_PURULEN3') }}></td>
                    </tr>

                    <!-- A - Approximation -->
                    <tr>
                        <td rowspan="4" class="text-center align-middle fw-bold">A</td>
                        <td rowspan="4" class="align-middle">Penyatuan Luka (Approximation)</td>
                        <td class="reeda-option {{ isChecked($data, 'A_LUKAMENYATU0') ? 'selected' : '' }}"><span>Luka menyatu</span><input type="checkbox" name="A_LUKAMENYATU0" value="0" {{ isChecked($data, 'A_LUKAMENYATU0') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'A_TERBUKA1') ? 'selected' : '' }}"><span>Luka terbuka ≤ 0.25 cm</span><input type="checkbox" name="A_TERBUKA1" value="1" {{ isChecked($data, 'A_TERBUKA1') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'A_TERBUKA2') ? 'selected' : '' }}"><span>Luka terbuka 0.25 - 0.5 cm</span><input type="checkbox" name="A_TERBUKA2" value="2" {{ isChecked($data, 'A_TERBUKA2') }}></td>
                    </tr>
                    <tr>
                        <td class="reeda-option {{ isChecked($data, 'A_TERBUKA3') ? 'selected' : '' }}"><span>Luka terbuka > 0.5 cm</span><input type="checkbox" name="A_TERBUKA3" value="3" {{ isChecked($data, 'A_TERBUKA3') }}></td>
                    </tr>

                    <!-- Total Skor -->
                    <tr class="table-primary fw-bold">
                        <td colspan="2" class="text-end">Total Skor</td>
                        <td class="text-center" id="total_skor">
                            {{ htmlspecialchars(getValue($data, 'TOTALSKOR', '0')) }}
                        </td>
                        <input type="hidden" name="TOTALSKOR" id="TOTALSKOR" value="{{ htmlspecialchars(getValue($data, 'TOTALSKOR', '0')) }}">
                    </tr>
                    <!-- Interpretasi Skor -->
                    <tr class="table-light fw-bold">
                        <td colspan="2" class="text-end">Interpretasi</td>
                        <td class="text-center" id="interpretasi_skor">
                            @if ($interpretationText === 'Normal')
                                <span class="badge bg-success">Normal</span>
                            @elseif ($interpretationText === 'Waspada')
                                <span class="badge bg-warning text-dark">Waspada</span>
                            @elseif ($interpretationText === 'Curiga Infeksi Luka')
                                <span class="badge bg-danger">Curiga Infeksi Luka</span>
                            @else
                                {{-- Kosong jika tidak ada skor --}}
                            @endif
                        </td>
                        {{-- Hidden input untuk menyimpan nilai interpretasi --}}
                        <input type="hidden" name="INTERPRETASI" id="INTERPRETASI" value="{{ htmlspecialchars($interpretationText) }}">
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label for="CATATANKLINIS" class="form-label fw-bold">Catatan Klinis</label>
                <textarea class="form-control" id="CATATANKLINIS" name="CATATANKLINIS" rows="4">{{ getValue($data, 'CATATANKLINIS') }}</textarea>
            </div>
            <div class="col-md-6">
                <label for="RTLLAIN_KET" class="form-label fw-bold">Rencana Tindak Lanjut</label>
                {{-- Textarea ini akan diisi otomatis oleh JavaScript berdasarkan skor --}}
                <textarea class="form-control" id="RTLLAIN_KET" name="RTLLAIN_KET" rows="4" readonly>{{ htmlspecialchars($rtlText) }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

<script>
    // Tambahkan beberapa style untuk UI yang lebih baik
    if (!document.getElementById('reeda-style')) {
        const style = document.createElement('style');
        style.id = 'reeda-style';
        style.innerHTML = `
            .reeda-option {
                cursor: pointer;
                transition: background-color 0.2s ease-in-out;
            }
            .reeda-option.selected {
                background-color: #d1ecf1 !important; /* Warna biru muda info */
            }
            .reeda-option input[type="checkbox"] { display: none; }
        `;
        document.head.appendChild(style);
    }

    $(document).ready(function() {
        function calculateTotalScore() {
            let total = 0;
            $('.reeda-table tbody input[type="checkbox"]:checked').each(function() {
                total += parseInt($(this).val()) || 0;
            });

            // Update Total Skor
            $('#total_skor').text(total);
            $('#TOTALSKOR').val(total);

            // Update Interpretasi
            let interpretationHtml = '';
            let interpretationText = '';
            let rtlText = '';

            if (total >= 0 && total <= 3) {
                interpretationHtml = '<span class="badge bg-success">Normal</span>';
                interpretationText = 'Normal';
                rtlText = "- Perawatan Luka rutin\n- Antibiotik diberikan melihat hasil leukosit";
            } else if (total >= 4 && total <= 6) {
                interpretationHtml = '<span class="badge bg-warning text-dark">Waspada</span>';
                interpretationText = 'Waspada';
                rtlText = "- Perawatan luka khusus\n- Antibiotik Broad Spectrum";
            } else if (total >= 7) {
                interpretationHtml = '<span class="badge bg-danger">Curiga Infeksi Luka</span>';
                interpretationText = 'Curiga Infeksi Luka';
                rtlText = "- Kultur luka\n- Antiobiotik Broad Spectrum\n- Antibiotik spesifik setelah hasil kultir keluar";
            }
            $('#interpretasi_skor').html(interpretationHtml);
            $('#INTERPRETASI').val(interpretationText);

            // Update Rencana Tindak Lanjut
            $('#RTLLAIN_KET').val(rtlText);
        }

        // Event handler untuk membuat baris bisa diklik
        $('.reeda-table').on('click', '.reeda-option', function() {
            const clickedCell = $(this);
            const checkbox = clickedCell.find('input[type="checkbox"]');
            const currentRow = clickedCell.closest('tr');
            const firstRowOfGroup = currentRow.closest('tbody').find('td[rowspan="4"]').filter(function() {
                return $(this).parent().nextAll().addBack().index(currentRow) >= 0;
            }).last().parent();
            const groupRows = firstRowOfGroup.add(firstRowOfGroup.nextUntil('tr:has(td[rowspan])'));

            // Hapus status 'checked' dan class 'selected' dari semua item di grup yang sama
            groupRows.find('.reeda-option').removeClass('selected').find('input[type="checkbox"]').prop('checked', false);

            // Terapkan status 'checked' dan class 'selected' ke item yang diklik
            checkbox.prop('checked', true);
            clickedCell.addClass('selected');

            calculateTotalScore();
        });

        // Initial calculation on page load
        calculateTotalScore();

        // AJAX form submission
        $('#formLukaReeda').on('submit', function(e) {
            e.preventDefault();

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