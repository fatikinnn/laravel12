@php
    // Helper untuk mendapatkan nilai dengan aman
    function getValueRm4a($data, $key, $default = '') {
        if (is_object($data) && property_exists($data, $key) && !is_null($data->$key)) {
            return trim($data->$key);
        }
        return $default;
    }
    function isCheckedRm4a($data, $key) {
        return getValueRm4a($data, $key) == 1 ? 'checked' : '';
    }
@endphp

<div class="card-body">
    {{-- Hidden Inputs for AJAX URLs --}}
    <input type="hidden" id="rm4a_history_url" value="{{ route('rme.igd.form.rm4a.history') }}">
    <input type="hidden" id="rm4a_detail_url" value="{{ route('rme.igd.form.rm4a.detail') }}">

    {{-- Riwayat Pemeriksaan --}}
    <div class="mb-4">
        <h6 class="font-weight-bold mb-3"><i class="fas fa-history mr-2"></i>Riwayat Penilaian</h6>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-hover" id="rm4aHistoryTable">
                <thead class="thead-light sticky-top" style="top: -1px;">
                    <tr>
                        <th>Tanggal & Jam</th>
                        <th>Total Skor</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody id="rm4aHistoryBody">
                    <tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Input/Edit --}}
    <form id="rm4aForm" action="{{ route('rme.igd.form.rm4a.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" id="rm4a_nopendaftaran" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="COUNTER" id="rm4a_counter">

        <div class="card card-outline card-primary">
                    <div class="card-header">
                <h3 class="card-title" id="rm4a-form-title">Input Data Baru</h3>
                    </div>

                    <div class="card-body">
                        <div class="form-row mb-3">
                            <div class="form-group col-md-4">
                                <label>Tanggal</label>
                                <input type="date" class="form-control" name="TGL" id="rm4a_tgl">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jam</label>
                                <input type="time" class="form-control" name="JAM" id="rm4a_jam">
                            </div>
                        </div>

                        {{-- Penilaian Skor --}}
                        <div class="card card-outline card-info">
                            <div class="card-header"><h3 class="card-title">Penilaian Skor</h3></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="25%">Parameter</th>
                                                <th width="55%">Kriteria</th>
                                                <th width="20%">Skor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $parameters = [
                                                    ['name' => 'RIWAYAT_JATUH_SKOR', 'label' => 'Riwayat Jatuh', 'options' => [['text' => 'Riwayat jatuh dalam 3 bulan terakhir', 'value' => 25], ['text' => 'Tidak ada riwayat jatuh', 'value' => 0]]],
                                                    ['name' => 'KONDISI_KESEHATAN_SKOR', 'label' => 'Kondisi Kesehatan', 'options' => [['text' => 'Lebih dari satu diagnosa penyakit', 'value' => 15], ['text' => 'Satu diagnosa penyakit', 'value' => 0]]],
                                                    ['name' => 'BANTUAN_AMBULANSI_SKOR', 'label' => 'Bantuan Ambulansi', 'options' => [['text' => 'Furniture (dinding, meja, kursi, lemari)', 'value' => 30], ['text' => 'Kruk, tongkat, walker', 'value' => 15], ['text' => 'Di tempat tidur/butuh bantuan perawat/kursi roda', 'value' => 0]]],
                                                    ['name' => 'TERAPI_IV_SKOR', 'label' => 'Terapi IV/Antikoagulan', 'options' => [['text' => 'Terapi intravena terus menerus', 'value' => 20], ['text' => 'Tidak ada terapi IV', 'value' => 0]]],
                                                    ['name' => 'GAYA_BERJALAN_SKOR', 'label' => 'Gaya Berjalan/Berpindah', 'options' => [['text' => 'Kerusakan (gangguan berat)', 'value' => 20], ['text' => 'Lemah', 'value' => 10], ['text' => 'Normal/di tempat tidur/immobilisasi', 'value' => 0]]],
                                                    ['name' => 'STATUS_MENTAL_SKOR', 'label' => 'Status Mental', 'options' => [['text' => 'Lupa keterbatasan', 'value' => 15], ['text' => 'Orientasi dengan kemampuan sendiri', 'value' => 0]]],
                                                ];
                                            @endphp

                                            @foreach ($parameters as $param)
                                            <tr>
                                                <td><strong>{{ $param['label'] }}</strong></td>
                                                <td>
                                                    @foreach ($param['options'] as $opt)
                                                        <div class="mb-1">{{ $opt['text'] }} <span class="float-right">({{ $opt['value'] }})</span></div>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm score-select" name="{{ $param['name'] }}" onchange="calculateTotal(this)">
                                                        <option value="" selected>Pilih</option>
                                                        @foreach ($param['options'] as $opt)
                                                            <option value="{{ $opt['value'] }}">{{ $opt['value'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Total Skor --}}
                                <div class="row mt-3">
                                    <div class="col-lg-4 col-md-6">
                                        <label class="font-weight-bold">Total Skor:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control total-skor" name="TOTAL_SKOR" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text score-badge font-weight-bold">Belum Dinilai</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Intervensi --}}
                        <div class="intervensi-container mt-3">
                            {{-- RESIKO RENDAH (0-24) --}}
                            <div class="card card-outline card-success intervention-card risk-low rr-card" style="display: none;">
                                <div class="card-header"><h3 class="card-title">Intervensi Resiko Rendah (Skor 0-24)</h3></div>
                                <div class="card-body">
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RR_ORIENTASI" value="1" id="rr_orientasi"><label class="font-weight-normal" for="rr_orientasi">Orientasi lingkungan</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RR_PASTIKAN_BEL" value="1" id="rr_pastikan_bel"><label class="font-weight-normal" for="rr_pastikan_bel">Pastikan bel mudah dijangkau</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RR_RODA_TT" value="1" id="rr_roda_tt"><label class="font-weight-normal" for="rr_roda_tt">Roda tempat tidur berada pada posisi terkunci</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RR_POSISIKAN_TT" value="1" id="rr_posisikan_tt"><label class="font-weight-normal" for="rr_posisikan_tt">Posisikan tempat tidur pada posisi rendah</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RR_NAIKKAN_PAGAR" value="1" id="rr_naikkan_pagar"><label class="font-weight-normal" for="rr_naikkan_pagar">Naikkan pagar pengaman tempat tidur</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RR_LAMPU_TIDUR" value="1" id="rr_lampu_tidur"><label class="font-weight-normal" for="rr_lampu_tidur">Pastikan lampu tidur hidup saat malam hari</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RR_EDUKASI" value="1" id="rr_edukasi"><label class="font-weight-normal" for="rr_edukasi">Berikan edukasi pasien atau keluarga</label></div></div>
                                </div>
                            </div>

                            {{-- RESIKO SEDANG (25-50) --}}
                            <div class="card card-outline card-warning intervention-card risk-medium rs-card" style="display: none;">
                                <div class="card-header"><h3 class="card-title">Intervensi Resiko Sedang (Skor 25-50)</h3></div>
                                <div class="card-body">
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RS_LAKUKAN_SEMUA" value="1" id="rs_lakukan_semua"><label class="font-weight-normal" for="rs_lakukan_semua">Lakukan semua pedoman Pencegahan jatuh risiko rendah</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RS_TANDA_SEGITIGA" value="1" id="rs_tanda_segitiga"><label class="font-weight-normal" for="rs_tanda_segitiga">Berikan tanda segitiga warna kuning pada bed pasien, pintu atau RM pasien</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RS_TANDA_RESIKO" value="1" id="rs_tanda_resiko"><label class="font-weight-normal" for="rs_tanda_resiko">Beri tanda resiko jatuh pada gelang identitas yang menempel pasien</label></div></div>
                                </div>
                            </div>

                            {{-- RESIKO TINGGI (>=51) --}}
                            <div class="card card-outline card-danger intervention-card risk-high rt-card" style="display: none;">
                                <div class="card-header"><h3 class="card-title">Intervensi Resiko Tinggi (Skor ≥51)</h3></div>
                                <div class="card-body">
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RT_LAKUKAN_SEMUA" value="1" id="rt_lakukan_semua"><label class="font-weight-normal" for="rt_lakukan_semua">Lakukan semua pedoman pencegahan jatuh resiko rendah dan sedang</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RT_1JAM" value="1" id="rt_1jam"><label class="font-weight-normal" for="rt_1jam">Kunjungi dan monitor pasien tiap 1 jam</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RT_TEMPATKAN_PASIEN" value="1" id="rt_tempatkan_pasien"><label class="font-weight-normal" for="rt_tempatkan_pasien">Tempatkan pasien di kamar yang paling dekat dengan nurse station (bila mungkin)</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RT_ALAT_BANTU" value="1" id="rt_alat_bantu"><label class="font-weight-normal" for="rt_alat_bantu">Pastikan pasien menggunakan alat bantu jalan</label></div></div>
                                    <div class="form-group"><div class="icheck-primary"><input type="checkbox" name="RT_LIBATKAN_KELUARGA" value="1" id="rt_libatkan_keluarga"><label class="font-weight-normal" for="rt_libatkan_keluarga">Libatkan keluarga untuk mengawasi jalan pasien</label></div></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4 mt-3 pl-0 d-none">
                            <label>Nama Petugas</label>
                            <input type="text" class="form-control" name="NAMA_PETUGAS" id="rm4a_nama_petugas" value="{{ $user['username'] ?? '' }}" readonly>
                        </div>
                    </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" id="btn-save-rm4a"><i class="fas fa-save mr-1"></i> Simpan</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-reset-rm4a"><i class="fas fa-sync-alt mr-1"></i> Batal / Baru</button>
        </div>
    </form>
</div>

<script>
    // Fungsi untuk menghitung total skor
    function calculateTotal(selectElement) {
        const formSection = $('#rm4aForm'); // jQuery object
        const selects = formSection.find('.score-select'); // Use jQuery's .find()
        let total = 0;

        selects.each(function() { // Use jQuery's .each()
            const value = parseInt($(this).val()) || 0;
            total += value;
        });

        const totalInput = formSection.find('.total-skor');
        totalInput.val(total || 0);

        // Update badge
        const scoreBadge = formSection.find('.score-badge');
        let badgeClass = 'bg-secondary text-white';
        let badgeText = 'Belum Dinilai'; 

        if (total >= 51) {
            badgeClass = 'bg-danger';
            badgeText = 'Resiko Tinggi';
        } else if (total >= 25) {
            badgeClass = 'bg-warning';
            badgeText = 'Resiko Sedang'; // text-dark is default on warning
        } else if (total >= 0 && total < 25) {
            badgeClass = 'bg-success';
            badgeText = 'Resiko Rendah';
        }
        
        scoreBadge.removeClass('bg-secondary bg-success bg-warning bg-danger text-white').addClass(badgeClass);
        scoreBadge.text(badgeText);

        // Tampilkan/sembunyikan card intervensi
        const rrCard = formSection.find('.rr-card');
        const rsCard = formSection.find('.rs-card');
        const rtCard = formSection.find('.rt-card');

        rrCard.hide();
        rsCard.hide();
        rtCard.hide();

        if (total >= 51) {
            // Tampilkan Resiko Tinggi, sembunyikan & bersihkan yang lain
            rtCard.show();
            rrCard.find('input[type=checkbox]').prop('checked', false);
            rsCard.find('input[type=checkbox]').prop('checked', false);
        } else if (total >= 25) {
            // Tampilkan Resiko Sedang, sembunyikan & bersihkan yang lain
            rsCard.show();
            rrCard.find('input[type=checkbox]').prop('checked', false);
            rtCard.find('input[type=checkbox]').prop('checked', false);
        } else if (total >= 0 && total < 25) {
            // Tampilkan Resiko Rendah, sembunyikan & bersihkan yang lain
            rrCard.show();
            rsCard.find('input[type=checkbox]').prop('checked', false);
            rtCard.find('input[type=checkbox]').prop('checked', false);
        } else {
            // Jika tidak ada skor, bersihkan semua
            rrCard.find('input[type=checkbox]').prop('checked', false);
            rsCard.find('input[type=checkbox]').prop('checked', false);
            rtCard.find('input[type=checkbox]').prop('checked', false);
        }
    }

    function resetRm4aForm() {
        $('#rm4aForm')[0].reset();
        $('#rm4a_counter').val('');
        $('#rm4a_tgl').val('{{ now()->format("Y-m-d") }}');
        $('#rm4a_jam').val('{{ now()->format("H:i") }}');
        $('#rm4a_nama_petugas').val('{{ $user["username"] ?? "" }}');
        
        calculateTotal($('#rm4aForm .score-select')[0]);

        $('#rm4a-form-title').text('Input Data Baru');
        $('#btn-save-rm4a').html('<i class="fas fa-save mr-1"></i> Simpan');
        $('#rm4aHistoryTable tbody tr').removeClass('table-info');

        Swal.fire({
            toast: true, position: 'top-end',
            icon: 'info', title: 'Form dibersihkan, mode input baru.',
            showConfirmButton: false, timer: 2000
        });
    }

    function loadRm4aHistory() {
        const url = $('#rm4a_history_url').val();
        const noPendaftaran = $('#rm4a_nopendaftaran').val();

        $.get(url, { noPendaftaran: noPendaftaran }, function(response) {
            let html = '';
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(item => {
                    const tgl = item.TGL ? new Date(item.TGL).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
                    const jam = item.JAM ? item.JAM.substring(0, 5) : '-';
                    html += `
                        <tr data-counter="${item.COUNTER}" class="editable-row" title="Klik untuk edit data ini">
                            <td>${tgl} ${jam}</td>
                            <td>${item.TOTAL_SKOR || 0}</td>
                            <td>${item.NAMA_PETUGAS || '-'}</td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="3" class="text-center">Tidak ada riwayat ditemukan.</td></tr>';
            }
            $('#rm4aHistoryBody').html(html);
            resetRm4aForm();
        }).fail(function() {
            $('#rm4aHistoryBody').html('<tr><td colspan="3" class="text-center text-danger">Gagal memuat riwayat.</td></tr>');
            resetRm4aForm();
        });
    }

    function loadDetailForEdit(counter) {
        const url = $('#rm4a_detail_url').val();
        const noPendaftaran = $('#rm4a_nopendaftaran').val();

        $.get(url, { noPendaftaran: noPendaftaran, counter: counter }, function(response) {
            if (response.status === 'success') {
                const data = response.data;
                
                resetRm4aForm(); // Reset form first

                $('#rm4a_counter').val(data.COUNTER);
                $('#rm4a_tgl').val(data.TGL ? data.TGL.substring(0, 10) : '');
                $('#rm4a_jam').val(data.JAM ? data.JAM.substring(0, 5) : '');
                $('#rm4a_nama_petugas').val(data.NAMA_PETUGAS);

                // Populate selects
                $('select[name="RIWAYAT_JATUH_SKOR"]').val(data.RIWAYAT_JATUH_SKOR);
                $('select[name="KONDISI_KESEHATAN_SKOR"]').val(data.KONDISI_KESEHATAN_SKOR);
                $('select[name="BANTUAN_AMBULANSI_SKOR"]').val(data.BANTUAN_AMBULANSI_SKOR);
                $('select[name="TERAPI_IV_SKOR"]').val(data.TERAPI_IV_SKOR);
                $('select[name="GAYA_BERJALAN_SKOR"]').val(data.GAYA_BERJALAN_SKOR);
                $('select[name="STATUS_MENTAL_SKOR"]').val(data.STATUS_MENTAL_SKOR);

                // Populate checkboxes
                $('#rr_orientasi').prop('checked', data.RR_ORIENTASI == 1);
                $('#rr_pastikan_bel').prop('checked', data.RR_PASTIKAN_BEL == 1);
                $('#rr_roda_tt').prop('checked', data.RR_RODA_TT == 1);
                $('#rr_posisikan_tt').prop('checked', data.RR_POSISIKAN_TT == 1);
                $('#rr_naikkan_pagar').prop('checked', data.RR_NAIKKAN_PAGAR == 1);
                $('#rr_lampu_tidur').prop('checked', data.RR_LAMPU_TIDUR == 1);
                $('#rr_edukasi').prop('checked', data.RR_EDUKASI == 1);
                $('#rs_lakukan_semua').prop('checked', data.RS_LAKUKAN_SEMUA == 1);
                $('#rs_tanda_segitiga').prop('checked', data.RS_TANDA_SEGITIGA == 1);
                $('#rs_tanda_resiko').prop('checked', data.RS_TANDA_RESIKO == 1);
                $('#rt_lakukan_semua').prop('checked', data.RT_LAKUKAN_SEMUA == 1);
                $('#rt_1jam').prop('checked', data.RT_1JAM == 1);
                $('#rt_tempatkan_pasien').prop('checked', data.RT_TEMPATKAN_PASIEN == 1);
                $('#rt_alat_bantu').prop('checked', data.RT_ALAT_BANTU == 1);
                $('#rt_libatkan_keluarga').prop('checked', data.RT_LIBATKAN_KELUARGA == 1);

                calculateTotal($('.score-select')[0]);

                $('#rm4a-form-title').text('Edit Data (No. ' + data.COUNTER + ')');
                $('#btn-save-rm4a').html('<i class="fas fa-pencil-alt mr-1"></i> Update');
                $('#rm4aHistoryTable tbody tr').removeClass('table-info');
                $(`#rm4aHistoryTable tbody tr[data-counter="${counter}"]`).addClass('table-info');

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
        loadRm4aHistory();

        // Event handler untuk klik baris riwayat untuk mengedit (seperti di RM6B)
        $('#rm4aHistoryBody').on('click', '.editable-row', function() {
            const counter = $(this).closest('tr').data('counter');
            loadDetailForEdit(counter);
            // Scroll ke form untuk UX yang lebih baik
            $('html, body').animate({ scrollTop: $('#rm4aForm').offset().top - 100 }, 500);
        });

    $('#rm4aForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        
        // Serialize form data, and explicitly add unchecked checkboxes with a value of '0'
        var data = form.serializeArray();
        form.find('input[type=checkbox]:not(:checked)').each(function() {
            // Only add if it's not already in the serialized data array
            if(data.filter(item => item.name === this.name).length === 0) {
                data.push({ name: this.name, value: '0' });
            }
        });
        var button = $('#btn-save-rm4a');
        var originalButtonText = button.html();

        $.ajax({
            type: 'POST',
            url: url,
            data: $.param(data),
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                button.prop('disabled', false).html(originalButtonText);
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => {
                        loadRm4aHistory();
                    });
                } else {
                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: function(xhr) {
                button.prop('disabled', false).html(originalButtonText);
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            },
        });
    });

    // --- Reset Button ---
    $('#btn-reset-rm4a').on('click', function() {
        Swal.fire({
            title: 'Yakin ingin batal?',
            text: "Formulir akan dikosongkan dan disiapkan untuk input data baru.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                resetRm4aForm();
            }
        });
    });
});
</script>