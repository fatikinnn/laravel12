@php
    // Helper untuk mendapatkan nilai dengan aman
    function getValueRm6b($data, $key, $default = '') {
        if (is_object($data) && property_exists($data, $key) && !is_null($data->$key)) {
            return trim($data->$key);
        }
        return $default;
    }
    function isCheckedRm6b($data, $key) {
        return getValueRm6b($data, $key) == 1 ? 'checked' : '';
    }
@endphp

<div class="card-body">
    {{-- Hidden Inputs for AJAX URLs --}}
    <input type="hidden" id="rm6b_history_url" value="{{ route('rme.igd.form.rm6b.history') }}">
    <input type="hidden" id="rm6b_detail_url" value="{{ route('rme.igd.form.rm6b.detail') }}">

    {{-- Riwayat Pemeriksaan --}}
    <div class="mb-4">
        <h6 class="font-weight-bold mb-3"><i class="fas fa-history mr-2"></i>Riwayat Penilaian</h6>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-hover" id="rm6bHistoryTable">
                <thead class="thead-light sticky-top" style="top: -1px;">
                    <tr>
                        <th>Tanggal & Jam</th>
                        <th>Total Skor</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody id="rm6bHistoryBody">
                    <tr>
                        <td colspan="4" class="text-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Input --}}
    <form id="rm6bForm" action="{{ route('rme.igd.form.rm6b.store') }}" method="post">
        @csrf
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="NOPENDAFTARAN" id="rm6b_nopendaftaran" value="{{ $noPendaftaran }}">
        <input type="hidden" name="COUNTER" id="COUNTER">

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title" id="form-title">Input Data Baru</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="form-group col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="TGL" id="TGL">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="form-label">Jam</label> <!-- Adjusted column width -->
                        <input type="time" class="form-control" name="JAM" id="JAM">
                    </div>
                </div>

                <h4 class="mb-3">Penilaian</h4>
                <div class="alert alert-info py-2 px-3 mb-3" role="alert" style="font-size: 14px;">
                    <i class="fas fa-info-circle"></i>
                    <strong>Info:</strong> Centang pilihan pada kolom <strong>skrining</strong> untuk melakukan penilaian skor secara otomatis.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered risk-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Parameter</th>
                                <th width="35%">Skrining</th>
                                <th width="10%">Jawaban</th>
                                <th width="15%">Keterangan Nilai</th>
                                <th width="15%">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Riwayat Jatuh --}}
                            <tr>
                                <td>1</td>
                                <td>Riwayat Jatuh</td>
                                <td class="jatuh-group">
                                    <div class="form-check"><input class="form-check-input jatuh-option" type="checkbox" id="jatuh1" name="RIWAYAT_JATUH_SKOR" value="6"><label class="form-check-label" for="jatuh1">Apakah datang karena jatuh?</label></div>
                                    <div class="form-check"><input class="form-check-input jatuh-option" type="checkbox" id="jatuh2" name="RIWAYAT_JATUH_SKOR_2" value="6"><label class="form-check-label" for="jatuh2">Jika tidak, apakah pernah jatuh dalam 6 bulan terakhir?</label></div>
                                </td>
                                <td><span id="RIWAYAT_JATUH_JAWABAN">Tidak</span></td>
                                <td>Ya = 6<br>Tidak = 0</td>
                                <td>
                                    <input type="text" class="form-control skor-input" id="RIWAYAT_JATUH_SKOR_DISPLAY" value="0" readonly>
                                </td>
                            </tr>

                            {{-- Status Mental --}}
                            <tr>
                                <td>2</td>
                                <td>Status Mental</td>
                                <td>
                                    <div class="form-check"><input class="form-check-input mental-option" type="checkbox" id="mental1" name="STATUS_MENTAL_SKOR" value="14"><label class="form-check-label" for="mental1">Apakah pasien delirium?</label></div>
                                    <div class="form-check"><input class="form-check-input mental-option" type="checkbox" id="mental2" name="STATUS_MENTAL_SKOR_2" value="14"><label class="form-check-label" for="mental2">Apakah pasien disorientasi?</label></div>
                                    <div class="form-check"><input class="form-check-input mental-option" type="checkbox" id="mental3" name="STATUS_MENTAL_SKOR_3" value="14"><label class="form-check-label" for="mental3">Apakah pasien mengalami agitasi?</label></div>
                                </td>
                                <td><span id="STATUS_MENTAL_JAWABAN">Tidak</span></td>
                                <td>Salah satu Ya = 14<br>Tidak = 0</td>
                                <td>
                                    <input type="text" class="form-control skor-input" id="STATUS_MENTAL_SKOR_DISPLAY" value="0" readonly>
                                </td>
                            </tr>

                            {{-- Penglihatan --}}
                            <tr>
                                <td>3</td>
                                <td>Penglihatan</td>
                                <td>
                                    <div class="form-check"><input class="form-check-input penglihatan-option" type="checkbox" id="penglihatan1" name="PENGELIHATAN_SKOR" value="1"><label class="form-check-label" for="penglihatan1">Apakah memakai kacamata?</label></div>
                                    <div class="form-check"><input class="form-check-input penglihatan-option" type="checkbox" id="penglihatan2" name="PENGELIHATAN_SKOR_2" value="1"><label class="form-check-label" for="penglihatan2">Apakah penglihatan buram?</label></div>
                                    <div class="form-check"><input class="form-check-input penglihatan-option" type="checkbox" id="penglihatan3" name="PENGELIHATAN_SKOR_3" value="1"><label class="form-check-label" for="penglihatan3">Apakah Glaukoma/Katarak/degenerasi makula?</label></div>
                                </td>
                                <td><span id="PENGELIHATAN_JAWABAN">Tidak</span></td>
                                <td>Salah satu Ya = 1<br>Tidak = 0</td>
                                <td>
                                    <input type="text" class="form-control skor-input" id="PENGELIHATAN_SKOR_DISPLAY" value="0" readonly>
                                </td>
                            </tr>

                            {{-- Kebiasaan Berkemih --}}
                            <tr>
                                <td>4</td>
                                <td>Kebiasaan Berkemih</td>
                                <td>
                                    <div class="form-check"><input class="form-check-input kemih-option" type="checkbox" id="kemih1" value="2" name="KEBIASAAN_BERKEMIH_SKOR"><label class="form-check-label" for="kemih1">Ya, BAK Normal (Skor 2)</label></div>
                                    <div class="form-check"><input class="form-check-input kemih-option" type="checkbox" id="kemih2" value="0" name="KEBIASAAN_BERKEMIH_SKOR"><label class="form-check-label" for="kemih2">Tidak (Skor 0)</label></div>
                                </td>
                                <td><span id="KEBIASAAN_BERKEMIH_JAWABAN">Tidak</span></td>
                                <td>Ya = 2<br>Tidak = 0</td>
                                <td><input type="text" class="form-control skor-input" id="KEBIASAAN_BERKEMIH_SKOR_DISPLAY" value="0" readonly></td>
                            </tr>

                            {{-- Transfer TT --}}
                            <tr class="table-warning">
                                <td>5</td>
                                <td>Transfer (dari tempat tidur ke kursi dan kembali lagi ke tempat tidur)</td>
                                <td>
                                    <div class="form-check"><input class="form-check-input transfer-option" type="checkbox" id="transfer1" value="0" name="TRANSFER_TT_SKOR"><label class="form-check-label" for="transfer1">Mandiri (0)</label></div>
                                    <div class="form-check"><input class="form-check-input transfer-option" type="checkbox" id="transfer2" value="1" name="TRANSFER_TT_SKOR_2"><label class="form-check-label" for="transfer2">Bantuan (1 orang/dalam pengawasan) (1)</label></div>
                                    <div class="form-check"><input class="form-check-input transfer-option" type="checkbox" id="transfer3" value="2" name="TRANSFER_TT_SKOR_3"><label class="form-check-label" for="transfer3">Bantuan 2 orang (2)</label></div>
                                    <div class="form-check"><input class="form-check-input transfer-option" type="checkbox" id="transfer4" value="3" name="TRANSFER_TT_SKOR_4"><label class="form-check-label" for="transfer4">Perlu bantuan total (3)</label></div>
                                </td>
                                <td><span id="TRANSFER_TT_JAWABAN">0</span></td>
                                <td>Skor sesuai pilihan</td>
                                <td>
                                    <input type="text" class="form-control skor-input" id="TRANSFER_TT_SKOR_DISPLAY" value="0" readonly>
                                </td>
                            </tr>

                            {{-- Mobilitas --}}
                            <tr class="table-warning">
                                <td>6</td>
                                <td>Mobilitas</td>
                                <td>
                                    <div class="form-check"><input class="form-check-input mobilitas-option" type="checkbox" id="mobilitas1" value="0" name="MOBILITAS_SKOR"><label class="form-check-label" for="mobilitas1">Mandiri (0)</label></div>
                                    <div class="form-check"><input class="form-check-input mobilitas-option" type="checkbox" id="mobilitas2" value="1" name="MOBILITAS_SKOR2"><label class="form-check-label" for="mobilitas2">Bantuan 1 orang (1)</label></div>
                                    <div class="form-check"><input class="form-check-input mobilitas-option" type="checkbox" id="mobilitas3" value="2" name="MOBILITAS_SKOR3"><label class="form-check-label" for="mobilitas3">Menggunakan kursi roda (2)</label></div>
                                    <div class="form-check"><input class="form-check-input mobilitas-option" type="checkbox" id="mobilitas4" value="3" name="MOBILITAS_SKOR4"><label class="form-check-label" for="mobilitas4">Imobilisasi (3)</label></div>
                                </td>
                                <td><span id="MOBILITAS_JAWABAN">0</span></td>
                                <td>Skor sesuai pilihan</td>
                                <td><input type="text" class="form-control skor-input" id="MOBILITAS_SKOR_DISPLAY" value="0" readonly></td>
                            </tr>

                            {{-- Total Transfer & Mobilitas --}}
                            <tr class="table-warning">
                                <td colspan="5" class="text-right"><strong>Total Transfer & Mobilitas</strong></td>
                                <td><input type="text" class="form-control" name="TOTAL_TRANFER_MOBILITAS_SKOR" id="TOTAL_TRANFER_MOBILITAS_SKOR" value="0" readonly></td>
                            </tr>

                            {{-- Total Skor --}}
                            <tr class="bg-secondary">
                                <td colspan="5" class="text-right"><strong>Total Skor</strong></td>
                                <td><input type="text" class="form-control font-weight-bold" name="TOTAL_SKOR" id="TOTAL_SKOR" value="0" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="conclusion-section mt-3">
                    <h4 class="mb-3">Kesimpulan</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-check"><input type="checkbox" class="form-check-input kesimpulan-cb" name="KESIMPULAN_RR" id="KESIMPULAN_RR" style="pointer-events: none;"><label class="form-check-label" for="KESIMPULAN_RR">Resiko Rendah (RR) - Skor 0-5</label></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-check"><input type="checkbox" class="form-check-input kesimpulan-cb" name="KESIMPULAN_RS" id="KESIMPULAN_RS" style="pointer-events: none;"><label class="form-check-label" for="KESIMPULAN_RS">Resiko Sedang (RS) - Skor 6-16</label></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-check"><input type="checkbox" class="form-check-input kesimpulan-cb" name="KESIMPULAN_RT" id="KESIMPULAN_RT" style="pointer-events: none;"><label class="form-check-label" for="KESIMPULAN_RT">Resiko Tinggi (RT) - Skor 17-30</label></div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="form-group col-md-4 d-none">
                        <label class="form-label">Nama Petugas</label>
                        <input type="text" class="form-control" name="NAMA_PETUGAS" id="NAMA_PETUGAS" value="{{ $user['namapemeriksa'] ?? ($user['username'] ?? '') }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="btn-save-rm6b"><i class="fas fa-save mr-1"></i> Simpan</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-reset-rm6b"><i class="fas fa-sync-alt mr-1"></i> Batal / Baru</button>
        </div>
    </form>
</div>

<script>
    function calculateTotalScore() {
        // 1. Riwayat Jatuh
        let jatuhSkor = ($('#jatuh1').is(':checked') ? parseInt($('#jatuh1').val()) : 0) + ($('#jatuh2').is(':checked') ? parseInt($('#jatuh2').val()) : 0);
        $('#RIWAYAT_JATUH_SKOR_DISPLAY').val(jatuhSkor);
        $('#RIWAYAT_JATUH_JAWABAN').text(jatuhSkor > 0 ? 'Ya' : 'Tidak');

        // 2. Status Mental
        const mentalChecked = $('.mental-option:checked').length > 0;
        const mentalSkor = mentalChecked ? 14 : 0;
        $('#STATUS_MENTAL_SKOR_DISPLAY').val(mentalSkor);
        $('#STATUS_MENTAL_JAWABAN').text(mentalChecked ? 'Ya' : 'Tidak');

        // 3. Penglihatan
        const penglihatanChecked = $('.penglihatan-option:checked').length > 0;
        const penglihatanSkor = penglihatanChecked ? 1 : 0;
        $('#PENGELIHATAN_SKOR_DISPLAY').val(penglihatanSkor);
        $('#PENGELIHATAN_JAWABAN').text(penglihatanChecked ? 'Ya' : 'Tidak');

        // 4. Kebiasaan Berkemih
        // Karena hanya ada satu kolom, kita ambil nilai tertinggi jika ada yang dicentang.
        let kemihSkor = 0; 
        if ($('#kemih1').is(':checked')) kemihSkor = Math.max(kemihSkor, parseInt($('#kemih1').val()));
        if ($('#kemih2').is(':checked')) kemihSkor = Math.max(kemihSkor, parseInt($('#kemih2').val()));
        $('#KEBIASAAN_BERKEMIH_SKOR_DISPLAY').val(kemihSkor);
        $('#KEBIASAAN_BERKEMIH_JAWABAN').text(kemihSkor > 0 ? 'Ya' : 'Tidak');

        // 5. Transfer
        let transferScore = 0;
        if ($('#transfer1').is(':checked')) { transferScore = 0; }
        if ($('#transfer2').is(':checked')) { transferScore = 1; }
        if ($('#transfer3').is(':checked')) { transferScore = 2; }
        if ($('#transfer4').is(':checked')) { transferScore = 3; }
        $('#TRANSFER_TT_SKOR_DISPLAY').val(transferScore);
        $('#TRANSFER_TT_JAWABAN').text(transferScore);

        // 6. Mobilitas
        let mobilitasScore = 0;
        if ($('#mobilitas1').is(':checked')) { mobilitasScore = 0; }
        if ($('#mobilitas2').is(':checked')) { mobilitasScore = 1; }
        if ($('#mobilitas3').is(':checked')) { mobilitasScore = 2; }
        if ($('#mobilitas4').is(':checked')) { mobilitasScore = 3; }
        $('#MOBILITAS_SKOR_DISPLAY').val(mobilitasScore);
        $('#MOBILITAS_JAWABAN').text(mobilitasScore);

        // Total Transfer & Mobilitas
        const totalTransferMobilitas = transferScore + mobilitasScore;
        $('#TOTAL_TRANFER_MOBILITAS_SKOR').val(totalTransferMobilitas);

        // Grand Total
        const totalScore = jatuhSkor + mentalSkor + penglihatanSkor + kemihSkor + totalTransferMobilitas;
        $('#TOTAL_SKOR').val(totalScore);
 
        // Update kesimpulan
        const rrCheckbox = $('#KESIMPULAN_RR');
        const rsCheckbox = $('#KESIMPULAN_RS');
        const rtCheckbox = $('#KESIMPULAN_RT');

        rrCheckbox.prop('checked', false);
        rsCheckbox.prop('checked', false);
        rtCheckbox.prop('checked', false);
 
        if (totalScore <= 5) {
            rrCheckbox.prop('checked', true);
        } else if (totalScore >= 6 && totalScore <= 16) {
            rsCheckbox.prop('checked', true); 
        } else if (totalScore >= 17) {
            rtCheckbox.prop('checked', true);
        }
    }

    function resetRm6bForm(nextCounter) {
        $('#rm6bForm')[0].reset();
        $('#COUNTER').val(''); // Kosongkan counter untuk mode insert
        $('#TGL').val('{{ now()->format("Y-m-d") }}');
        $('#JAM').val('{{ now()->format("H:i") }}'); 
        $('#NAMA_PETUGAS').val('{{ $user["namapemeriksa"] ?? ($user["username"] ?? "") }}');
        
        calculateTotalScore();

        $('#form-title').text('Input Data Baru');
        $('#btn-save-rm6b').html('<i class="fas fa-save mr-1"></i> Simpan');
        $('#rm6bHistoryTable tbody tr').removeClass('table-info');

        Swal.fire({
            toast: true, position: 'top-end',
            icon: 'info', title: 'Form dibersihkan, mode input baru.',
            showConfirmButton: false, timer: 2000
        });
    }

    function loadRm6bHistory() {
        const url = $('#rm6b_history_url').val();
        const noPendaftaran = $('#rm6b_nopendaftaran').val();

        $.get(url, { noPendaftaran: noPendaftaran }, function(response) {
            let html = '';
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(item => {
                    const tgl = new Date(item.TGL).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    const jam = item.JAM ? item.JAM.substring(0, 5) : '';
                    html += `
                        <tr data-counter="${item.COUNTER}" class="editable-row" title="Klik untuk edit data ini">
                            <td>${tgl} ${jam}</td>
                            <td>${item.TOTAL_SKOR || 0}</td>
                            <td>${item.NAMA_PETUGAS}</td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">Tidak ada riwayat ditemukan.</td></tr>';
            }
            $('#rm6bHistoryBody').html(html);
            resetRm6bForm(); // Set form to new entry
        }).fail(function() {
            $('#rm6bHistoryBody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat riwayat.</td></tr>');
            resetRm6bForm(); // Still reset form on failure
        });
    }

    function loadDetailForEdit(counter) {
        const url = $('#rm6b_detail_url').val();
        const noPendaftaran = $('#rm6b_nopendaftaran').val();

        $.get(url, { noPendaftaran: noPendaftaran, counter: counter }, function(response) {
            // Reset form sebelum mengisi data baru
            $('#rm6bForm')[0].reset();
            if (response.status === 'success') {
                const data = response.data;
                
                // Populate form
                $('#COUNTER').val(data.COUNTER);
                $('#TGL').val(data.TGL ? data.TGL.substring(0, 10) : '');
                $('#JAM').val(data.JAM ? data.JAM.substring(0, 5) : '');
                $('#NAMA_PETUGAS').val(data.NAMA_PETUGAS);

                // Checkboxes
                $('#jatuh1').prop('checked', data.RIWAYAT_JATUH_SKOR == '6');
                $('#jatuh2').prop('checked', data.RIWAYAT_JATUH_SKOR_2 == '6');
                $('#mental1').prop('checked', data.STATUS_MENTAL_SKOR == '14');
                $('#mental2').prop('checked', data.STATUS_MENTAL_SKOR_2 == '14');
                $('#mental3').prop('checked', data.STATUS_MENTAL_SKOR_3 == '14');
                $('#penglihatan1').prop('checked', data.PENGELIHATAN_SKOR == '1');
                $('#penglihatan2').prop('checked', data.PENGELIHATAN_SKOR_2 == '1');
                $('#penglihatan3').prop('checked', data.PENGELIHATAN_SKOR_3 == '1');
                // Logika untuk Kebiasaan Berkemih saat edit
                // Ceklis checkbox berdasarkan nilai yang tersimpan
                $('#kemih1').prop('checked', data.KEBIASAAN_BERKEMIH_SKOR == '2');
                $('#kemih2').prop('checked', data.KEBIASAAN_BERKEMIH_SKOR == '0');
                // Jika keduanya tidak cocok (misal null), keduanya tidak akan tercentang, yang mana sudah benar.

                $('#transfer1').prop('checked', data.TRANSFER_TT_SKOR == '0');
                $('#transfer2').prop('checked', data.TRANSFER_TT_SKOR_2 == '1');
                $('#transfer3').prop('checked', data.TRANSFER_TT_SKOR_3 == '2');
                $('#transfer4').prop('checked', data.TRANSFER_TT_SKOR_4 == '3');
                $('#mobilitas1').prop('checked', data.MOBILITAS_SKOR == '0');
                $('#mobilitas2').prop('checked', data.MOBILITAS_SKOR2 == '1');
                $('#mobilitas3').prop('checked', data.MOBILITAS_SKOR3 == '2');
                $('#mobilitas4').prop('checked', data.MOBILITAS_SKOR4 == '3');

                calculateTotalScore();

                // Update UI for edit mode
                $('#form-title').text('Edit Data (No. ' + data.COUNTER + ')');
                $('#btn-save-rm6b').html('<i class="fas fa-pencil-alt mr-1"></i> Update');
                $('#rm6bHistoryTable tbody tr').removeClass('table-info');
                $(`#rm6bHistoryTable tbody tr[data-counter="${counter}"]`).addClass('table-info');

                Swal.fire({
                    toast: true, position: 'top-end',
                    icon: 'info', title: 'Mode Edit Aktif',
                    showConfirmButton: false, timer: 2000
                });

            } else {
                Swal.fire('Error', 'Gagal memuat detail data.', 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal memuat detail data.', 'error');
        });
    }

    // --- Document Ready ---
    $(document).ready(function() {
        loadRm6bHistory();

        // Event listeners for score calculation
        $('.jatuh-option').on('change', calculateTotalScore);
        $('.mental-option').on('change', calculateTotalScore);
        $('.penglihatan-option').on('change', calculateTotalScore);
        $('.kemih-option').on('change', calculateTotalScore);
        $('.transfer-option').on('change', calculateTotalScore);
        $('.mobilitas-option').on('change', calculateTotalScore);

        calculateTotalScore(); // Initial calculation

        // Edit button click
        // Event handler untuk klik baris riwayat untuk mengedit
        $('#rm6bHistoryBody').on('click', '.editable-row', function() {
            const counter = $(this).closest('tr').data('counter');
            loadDetailForEdit(counter);
            $('html, body').animate({ scrollTop: $('#rm6bForm').offset().top - 100 }, 500);
        });

        // --- AJAX Form Submission ---
        $('#rm6bForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            
            var data = form.serializeArray();
            form.find('input[type=checkbox]:not(:checked)').each(function() {
                data.push({ name: this.name, value: '' });
            });

            var button = $('#btn-save-rm6b');
            var originalButtonText = button.html();

            $.ajax({
                type: 'POST',
                url: url,
                data: $.param(data),
                beforeSend: function() {
                    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    button.prop('disabled', false).html(originalButtonText); // Re-enable button
                    if (response.status === 'success') {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => {
                            loadRm6bHistory(); // Reload history and reset form
                        });
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function(xhr) {
                    button.prop('disabled', false).html(originalButtonText); // Re-enable button
                    let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMsg, 'error');
                },
            });
        });

        // --- Reset Button ---
        $('#btn-reset-rm6b').on('click', function() {
            Swal.fire({
                title: 'Yakin ingin batal?',
                text: "Formulir akan dikosongkan dan disiapkan untuk input data baru.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) resetRm6bForm();
            });
        });
    });
</script>



{{-- OLD SCRIPT --}}
<script>
    // This script is now combined above. This is kept for reference and will be removed.
    // $(document).ready(function() {
        // ... (all the old script content)
        /*
        $('#rm6bForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            
            var data = form.serializeArray();
            form.find('input[type=checkbox]:not(:checked)').each(function() {
                data.push({ name: this.name, value: '' });
            });

            var button = $('#btn-save-rm6b');
            var originalButtonText = button.html();

            $.ajax({
                type: 'POST',
                url: url,
                data: $.param(data),
                beforeSend: function() {
                    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    button.prop('disabled', false).html(originalButtonText); // Re-enable button
                    if (response.status === 'success') {
                        Swal.fire('Berhasil!', response.message, 'success').then(() => {
                            loadRm6bHistory(); // Reload history and reset form
                        });
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function(xhr) {
                    button.prop('disabled', false).html(originalButtonText); // Re-enable button
                    let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMsg, 'error');
                },
            });
        });

        // --- Reset Button ---
        $('#btn-reset-rm6b').on('click', function() {
            Swal.fire({
                title: 'Yakin ingin batal?',
                text: "Formulir akan dikosongkan dan disiapkan untuk input data baru.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) resetRm6bForm();
            });
        });
    });*/
</script>