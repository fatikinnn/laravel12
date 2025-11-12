@php
    function getValueRm25a($data, $key, $default = '') {
        if (is_object($data) && property_exists($data, $key) && !is_null($data->$key)) {
            return trim($data->$key);
        }
        return $default;
    }
    function isCheckedRm25a($data, $key) {
        return getValueRm25a($data, $key) == 1 ? 'checked' : '';
    }
@endphp
<div class="card-body">
    {{-- Hidden Inputs for AJAX URLs --}}
    <input type="hidden" id="rm25a_history_url" value="{{ route('rme.igd.form.rm25a.history') }}">
    <input type="hidden" id="rm25a_detail_url" value="{{ route('rme.igd.form.rm25a.detail') }}">

    {{-- Riwayat Pemeriksaan --}}
    <div class="mb-4">
        <h6 class="font-weight-bold mb-3"><i class="fas fa-history mr-2"></i>Riwayat Penilaian</h6>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-hover" id="rm25aHistoryTable">
                <thead class="thead-light sticky-top" style="top: -1px;">
                    <tr>
                        <th>Tanggal & Jam</th>
                        <th>Total Skor</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody id="rm25aHistoryBody">
                    <tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Input/Edit --}}
    <form id="rm25aForm" action="{{ route('rme.igd.form.rm25a.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" id="rm25a_nopendaftaran" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="COUNTER" id="rm25a_counter">

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title" id="rm25a-form-title">Input Data Baru</h3>
            </div>

            <div class="card-body">
                <div class="form-row mb-3">
                    <div class="form-group col-md-4">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="TGL" id="rm25a_tgl">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Jam</label>
                        <input type="time" class="form-control" name="JAM" id="rm25a_jam">
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
                                            ['name' => 'UMUR_SKOR', 'label' => 'Umur', 'options' => [['text' => 'Di bawah 3 tahun', 'value' => 4], ['text' => '3-7 tahun', 'value' => 3], ['text' => '7-13 tahun', 'value' => 2], ['text' => '>13 tahun', 'value' => 1]]],
                                            ['name' => 'JENISKELAMIN_SKOR', 'label' => 'Jenis Kelamin', 'options' => [['text' => 'Laki-laki', 'value' => 2], ['text' => 'Perempuan', 'value' => 1]]],
                                            ['name' => 'DIAGNOSA_SKOR', 'label' => 'Diagnosa', 'options' => [['text' => 'Kelainan neurologi', 'value' => 4], ['text' => 'Perubahan dalam oksigenasi', 'value' => 3], ['text' => 'Kelainan psikis/perilaku', 'value' => 2], ['text' => 'Diagnosis lain', 'value' => 1]]],
                                            ['name' => 'GANGGUAN_KOGNI_SKOR', 'label' => 'Gangguan Kognitif', 'options' => [['text' => 'Tidak sadar terhadap keterbatasan', 'value' => 3], ['text' => 'Lupa keterbatasan', 'value' => 2], ['text' => 'Mengetahui kemampuan diri', 'value' => 1]]],
                                            ['name' => 'FAKTOR_LINGK_SKOR', 'label' => 'Faktor Lingkungan', 'options' => [['text' => 'Riwayat jatuh dari tempat tidur', 'value' => 4], ['text' => 'Pasien menggunakan alat bantu', 'value' => 3], ['text' => 'Pasien berada di tempat tidur', 'value' => 2], ['text' => 'Diluar ruang rawat', 'value' => 1]]],
                                            ['name' => 'TERHADAP_OPERASI_SKOR', 'label' => 'Terhadap Operasi/Obat Penenang', 'options' => [['text' => 'Dalam 48 jam riwayat jatuh', 'value' => 2], ['text' => '>48 jam', 'value' => 1]]],
                                            ['name' => 'PENGGUNAAN_OBAT_SKOR', 'label' => 'Penggunaan Obat', 'options' => [['text' => 'Bermacam-macam obat', 'value' => 3], ['text' => 'Salah satu dari pengobatan', 'value' => 2], ['text' => 'Pengobatan lain', 'value' => 1]]],
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
                                            <select class="form-control form-control-sm score-select" name="{{ $param['name'] }}">
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
                    {{-- RESIKO RENDAH (7-11) --}}
                    <div class="card card-outline card-warning intervention-card risk-low rr-card" style="display: none;">
                        <div class="card-header"><h3 class="card-title">Intervensi Resiko Rendah (Skor 7-11)</h3></div>
                        <div class="card-body">
                            <div class="form-group icheck-primary"><input type="checkbox" name="RR_ORIENTASI" value="1" id="rr_orientasi"><label class="form-check-label ml-2" for="rr_orientasi">Orientasi lingkungan</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RR_PASTIKAN_BEL" value="1" id="rr_pastikan_bel"><label class="form-check-label ml-2" for="rr_pastikan_bel">Pastikan bel mudah dijangkau</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RR_RODA_TT" value="1" id="rr_roda_tt"><label class="form-check-label ml-2" for="rr_roda_tt">Roda tempat tidur berada pada posisi terkunci</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RR_POSISIKAN_TT" value="1" id="rr_posisikan_tt"><label class="form-check-label ml-2" for="rr_posisikan_tt">Posisikan tempat tidur pada posisi rendah</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RR_NAIKKAN_PAGAR" value="1" id="rr_naikkan_pagar"><label class="form-check-label ml-2" for="rr_naikkan_pagar">Naikkan pagar pengaman tempat tidur</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RR_LAMPU_TIDUR" value="1" id="rr_lampu_tidur"><label class="form-check-label ml-2" for="rr_lampu_tidur">Pastikan lampu tidur hidup saat malam hari</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RR_EDUKASI" value="1" id="rr_edukasi"><label class="form-check-label ml-2" for="rr_edukasi">Berikan edukasi pasien atau keluarga</label></div>
                        </div>
                    </div>

                    {{-- RESIKO TINGGI (>=12) --}}
                    <div class="card card-outline card-danger intervention-card risk-high rt-card" style="display: none;">
                        <div class="card-header"><h3 class="card-title">Intervensi Resiko Tinggi (Skor ≥12)</h3></div>
                        <div class="card-body">
                            <div class="form-group icheck-primary"><input type="checkbox" name="RT_LAKUKAN_SEMUA" value="1" id="rt_lakukan_semua"><label class="form-check-label ml-2" for="rt_lakukan_semua">Lakukan semua pedoman pencegahan jatuh resiko rendah</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RT_TANDA_SEGITIGA" value="1" id="rt_tanda_segitiga"><label class="form-check-label ml-2" for="rt_tanda_segitiga">Berikan tanda segitiga warna kuning pada bed pasien, pintu atau RM pasien</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RT_TANDA_RESIKO" value="1" id="rt_tanda_resiko"><label class="form-check-label ml-2" for="rt_tanda_resiko">Beri tanda resiko jatuh pada gelang identitas yang menempel pasien</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RT_1JAM" value="1" id="rt_1jam"><label class="form-check-label ml-2" for="rt_1jam">Kunjungi dan monitor pasien tiap 1 jam</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RT_TEMPATKAN_PASIEN" value="1" id="rt_tempatkan_pasien"><label class="form-check-label ml-2" for="rt_tempatkan_pasien">Tempatkan pasien di kamar yang paling dekat dengan nurse station (bila mungkin)</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RT_ALAT_BANTU" value="1" id="rt_alat_bantu"><label class="form-check-label ml-2" for="rt_alat_bantu">Pastikan pasien menggunakan alat bantu jalan</label></div>
                            <div class="form-group icheck-primary"><input type="checkbox" name="RT_LIBATKAN_KELUARGA" value="1" id="rt_libatkan_keluarga"><label class="form-check-label ml-2" for="rt_libatkan_keluarga">Libatkan keluarga untuk mengawasi jalan pasien</label></div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-4 mt-3 pl-0 d-none">
                    <label>Nama Petugas</label>
                    <input type="text" class="form-control" name="NAMA_PETUGAS" id="rm25a_nama_petugas" value="{{ $user['username'] ?? '' }}" readonly>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" id="btn-save-rm25a"><i class="fas fa-save mr-1"></i> Simpan</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-reset-rm25a"><i class="fas fa-sync-alt mr-1"></i> Batal / Baru</button>
        </div>
    </form>
</div>

<script>
    function calculateTotalRm25a() {
        const form = $('#rm25aForm');
        let total = 0;
        form.find('.score-select').each(function() {
            total += parseInt($(this).val()) || 0;
        });

        form.find('.total-skor').val(total);

        const scoreBadge = form.find('.score-badge');
        let badgeClass = 'bg-secondary';
        let badgeText = 'Belum Dinilai';

        if (total >= 12) {
            badgeClass = 'bg-danger';
            badgeText = 'Resiko Tinggi';
        } else if (total >= 7) {
            badgeClass = 'bg-warning';
            badgeText = 'Resiko Rendah';
        }

        scoreBadge.removeClass('bg-secondary bg-warning bg-danger').addClass(badgeClass).text(badgeText);

        const rrCard = form.find('.rr-card');
        const rtCard = form.find('.rt-card');

        // Sembunyikan kedua panel intervensi terlebih dahulu.
        // PENTING: Jangan hapus centang checkbox di sini, karena akan menghapus data saat mode edit.
        // Biarkan status checked diatur oleh data dari server atau input manual pengguna.
        rrCard.hide();
        rtCard.hide();

        if (total >= 12) {
            rtCard.show();
        } else if (total >= 7) {
            rrCard.show();
        }
    }

    function resetRm25aForm(showToast = true) {
        $('#rm25aForm')[0].reset();
        $('#rm25a_counter').val('');
        $('#rm25a_tgl').val('{{ now()->format("Y-m-d") }}');
        $('#rm25a_jam').val('{{ now()->format("H:i") }}');
        $('#rm25a_nama_petugas').val('{{ $user["username"] ?? "" }}');

        calculateTotalRm25a(); // Hitung ulang skor agar badge dan intervensi kembali ke default

        $('#rm25a-form-title').text('Input Data Baru'); // Selalu set ke Input Baru
        $('#btn-save-rm25a').html('<i class="fas fa-save mr-1"></i> Simpan'); // Selalu set ke Simpan
        $('#rm25aHistoryTable tbody tr').removeClass('table-info'); // Hapus highlight dari riwayat

        if (showToast) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Form dibersihkan, mode input baru.', showConfirmButton: false, timer: 2000 });
        }
    }

    function loadRm25aHistory() {
        const url = $('#rm25a_history_url').val();
        const noPendaftaran = $('#rm25a_nopendaftaran').val();

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
            $('#rm25aHistoryBody').html(html);
            // Panggil reset HANYA SEKALI saat riwayat pertama kali dimuat.
            resetRm25aForm(false); // false agar tidak menampilkan notifikasi "Form dibersihkan"
        }).fail(function() {
            $('#rm25aHistoryBody').html('<tr><td colspan="3" class="text-center text-danger">Gagal memuat riwayat.</td></tr>');
            resetRm25aForm(false);
        });
    }

    function loadDetailForEditRm25a(counter) {
        const url = $('#rm25a_detail_url').val();
        const noPendaftaran = $('#rm25a_nopendaftaran').val();
        $.get(url, { noPendaftaran: noPendaftaran, counter: counter }, function(response) {
            if (response.status === 'success') {
                // 1. Reset form TANPA notifikasi. Ini akan membersihkan semua input dan skor.
                resetRm25aForm(false); 

                // 2. Isi kembali form dengan data dari server.
                const data = response.data;
                
                $('#rm25a_counter').val(data.COUNTER);
                $('#rm25a_tgl').val(data.TGL ? data.TGL.substring(0, 10) : '');
                $('#rm25a_jam').val(data.JAM ? data.JAM.substring(0, 5) : '');
                $('#rm25a_nama_petugas').val(data.NAMA_PETUGAS);

                // Populate selects
                $('select[name="UMUR_SKOR"]').val(data.UMUR_SKOR);
                $('select[name="JENISKELAMIN_SKOR"]').val(data.JENISKELAMIN_SKOR);
                $('select[name="DIAGNOSA_SKOR"]').val(data.DIAGNOSA_SKOR);
                $('select[name="GANGGUAN_KOGNI_SKOR"]').val(data.GANGGUAN_KOGNI_SKOR);
                $('select[name="FAKTOR_LINGK_SKOR"]').val(data.FAKTOR_LINGK_SKOR);
                $('select[name="TERHADAP_OPERASI_SKOR"]').val(data.TERHADAP_OPERASI_SKOR);
                $('select[name="PENGGUNAAN_OBAT_SKOR"]').val(data.PENGGUNAAN_OBAT_SKOR);

                // Populate checkboxes
                // Intervensi Resiko Rendah (RR)
                $('input[name="RR_ORIENTASI"]').prop('checked', String(data.RR_ORIENTASI).trim() == '1');
                $('input[name="RR_PASTIKAN_BEL"]').prop('checked', String(data.RR_PASTIKAN_BEL).trim() == '1');
                $('input[name="RR_RODA_TT"]').prop('checked', String(data.RR_RODA_TT).trim() == '1');
                $('input[name="RR_POSISIKAN_TT"]').prop('checked', String(data.RR_POSISIKAN_TT).trim() == '1');
                $('input[name="RR_NAIKKAN_PAGAR"]').prop('checked', String(data.RR_NAIKKAN_PAGAR).trim() == '1');
                $('input[name="RR_LAMPU_TIDUR"]').prop('checked', String(data.RR_LAMPU_TIDUR).trim() == '1');
                $('input[name="RR_EDUKASI"]').prop('checked', String(data.RR_EDUKASI).trim() == '1');

                // Intervensi Resiko Tinggi (RT)
                $('input[name="RT_LAKUKAN_SEMUA"]').prop('checked', String(data.RT_LAKUKAN_SEMUA).trim() == '1');
                $('input[name="RT_TANDA_SEGITIGA"]').prop('checked', String(data.RT_TANDA_SEGITIGA).trim() == '1');
                $('input[name="RT_TANDA_RESIKO"]').prop('checked', String(data.RT_TANDA_RESIKO).trim() == '1');
                $('input[name="RT_1JAM"]').prop('checked', String(data.RT_1JAM).trim() == '1');
                $('input[name="RT_TEMPATKAN_PASIEN"]').prop('checked', String(data.RT_TEMPATKAN_PASIEN).trim() == '1');
                $('input[name="RT_ALAT_BANTU"]').prop('checked', String(data.RT_ALAT_BANTU).trim() == '1');
                $('input[name="RT_LIBATKAN_KELUARGA"]').prop('checked', String(data.RT_LIBATKAN_KELUARGA).trim() == '1');

                // 3. Hitung ulang skor dan tampilkan intervensi yang benar berdasarkan data yang baru diisi.
                calculateTotalRm25a();

                $('#rm25a-form-title').text('Edit Data (No. ' + data.COUNTER + ')');
                $('#btn-save-rm25a').html('<i class="fas fa-pencil-alt mr-1"></i> Update');
                $('#rm25aHistoryTable tbody tr').removeClass('table-info');
                $(`#rm25aHistoryTable tbody tr[data-counter="${counter}"]`).addClass('table-info');

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
        loadRm25aHistory();

        $('#rm25aForm .score-select').on('change', calculateTotalRm25a);

        $('#rm25aHistoryBody').on('click', '.editable-row', function() {
            const counter = $(this).closest('tr').data('counter');
            loadDetailForEditRm25a(counter);
            $('html, body').animate({ scrollTop: $('#rm25aForm').offset().top - 100 }, 500);
        });

        $('#rm25aForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            
            var data = form.serializeArray();
            form.find('input[type=checkbox]:not(:checked)').each(function() {
                data.push({ name: this.name, value: '0' });
            });
            var button = $('#btn-save-rm25a');
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
                        Swal.fire('Berhasil!', response.message, 'success').then(() => {
                            loadRm25aHistory();
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
                complete: function() {
                    button.prop('disabled', false).html(originalButtonText);
                }
            });
        });

        $('#btn-reset-rm25a').on('click', function() {
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
                    resetRm25aForm(true); // Panggil dengan true untuk menampilkan notifikasi
                }
            });
        });
    });
</script>