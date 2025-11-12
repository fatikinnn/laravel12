
<div class="card-body">
    {{-- Hidden Inputs --}}
    <input type="hidden" id="cppt_nopendaftaran" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
    <input type="hidden" id="cppt_norm" name="NORM" value="{{ $norm }}">
    <input type="hidden" id="cppt_form_action" value="{{ route('rme.igd.form.cppt.store') }}">
    <input type="hidden" id="cppt_history_action" value="{{ route('rme.igd.form.cppt.history') }}">
    <input type="hidden" id="cppt_detail_action" value="{{ route('rme.igd.form.cppt.detail') }}">
    <input type="hidden" id="cppt_check_action" value="{{ route('rme.igd.form.cppt.check') }}">

    {{-- Riwayat Pemeriksaan --}}
    <div class="mb-4">
        <h6 class="font-weight-bold mb-3"><i class="fas fa-history mr-2"></i>Riwayat Pemeriksaan</h6>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-hover" id="cpptHistoryTable">
                <thead class="thead-light sticky-top" style="top: -1px;">
                    <tr>
                        <th>Tanggal</th>
                        <th>PPA</th>
                        <th>Pemeriksaan (SOAP)</th>
                        <th>Instruksi (Plan)</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody id="cpptHistoryBody">
                    <tr>
                        <td colspan="5" class="text-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Input CPPT --}}
    <form id="cpptForm">
        @csrf
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        {{-- Hidden inputs for other fields from native code --}}
        <input type="hidden" id="cppt_kesadaran" name="KESADARAN">
        <input type="hidden" id="cppt_skor" name="SKOR">
        <input type="hidden" id="cppt_bb" name="BB">
        <input type="hidden" id="cppt_tb" name="TB">
        <input type="hidden" id="cppt_peroral" name="PERORAL">
        <input type="hidden" id="cppt_parenteral" name="PARENTERAL">
        <input type="hidden" id="cppt_mlain" name="MLAIN">
        <input type="hidden" id="cppt_urin" name="URIN">
        <input type="hidden" id="cppt_muntah" name="MUNTAH">
        <input type="hidden" id="cppt_klain" name="KLAIN">
        {{-- Flag untuk menandai update --}}
        <input type="hidden" id="is_update_flag" name="is_update" value="false">

        {{-- Tanggal dan Jam --}}
        <div class="row mb-3">
            <div class="col-lg-4 col-md-6 mb-2">
                <label for="cppt_tanggal" class="form-label">Tanggal</label>
                <input type="date" id="cppt_tanggal" name="TANGGAL" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-lg-4 col-md-6 mb-2">
                <label for="cppt_jam" class="form-label">Jam</label>
                <input type="time" id="cppt_jam" name="JAM" class="form-control" value="{{ date('H:i:s') }}" step="1">
            </div>
            <div class="col-lg-4 col-md-12">
                <label for="cppt_catatan" class="form-label">Catatan</label>
                <input type="text" class="form-control" id="cppt_catatan" name="CATATAN">
            </div>
        </div>

        {{-- Pemeriksaan & Instruksi --}}
        <div class="row mb-3">
            <div class="col-12">
                <label for="cppt_pemeriksaan" class="form-label">Pemeriksaan (SOAP)</label>
                <textarea class="form-control" id="cppt_pemeriksaan" name="PEMERIKSAAN" rows="8">S :
O :
    TD :
    N :
    RR :
    S :
    SP02 :
    Urine Ouput :
    BB :
    TB :
A :</textarea>
            </div>
            <div class="col-12 mt-3">
                <label for="cppt_instruksi" class="form-label">Instruksi (Plan)</label>
                <textarea class="form-control" id="cppt_instruksi" name="INSTRUKSI" rows="5">P :</textarea>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="col-12 mt-4">
            <div class="">
                <button type="submit" class="btn btn-primary" id="btn-save-cppt" style="width: 100%; max-width: 180px;">
                    <i class="fas fa-save mr-2"></i> Simpan
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btn-reset-cppt" style="width: 100%; max-width: 180px;">
                    <i class="fas fa-sync-alt mr-2"></i> Batal / Baru
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Modal untuk Detail --}}
<div class="modal fade" id="cpptDetailModal" tabindex="-1" aria-labelledby="cpptDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cpptDetailModalLabel">Detail Pemeriksaan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 id="cppt-modal-ppa"></h6>
                    <small id="cppt-modal-tanggal" class="text-muted"></small>
                </div>
                <div class="mb-3">
                    <h6>Pemeriksaan (SOAP):</h6>
                    <div class="border p-2 bg-light" id="cppt-modal-pemeriksaan" style="white-space: pre-wrap;"></div>
                </div>
                <div>
                    <h6>Instruksi (Plan):</h6>
                    <div class="border p-2 bg-light" id="cppt-modal-instruksi" style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn-edit-from-modal" style="display: none;">Edit Data Ini</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    // Set CSRF token untuk semua AJAX request
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let currentEditData = null;
    
    // Fungsi untuk escape HTML
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Fungsi untuk mereset form
    function resetCpptForm() {
        $('#cpptForm')[0].reset();
        $('#cppt_tanggal').val('{{ date('Y-m-d') }}');
        $('#cppt_jam').val('{{ date('H:i:s') }}');
        $('#cppt_pemeriksaan').val(`S :\nO :\n    TD :\n    N :\n    RR :\n    S :\n    SP02 :\n    Urine Ouput :\n    BB :\n    TB :\nA :`);
        $('#cppt_instruksi').val(`P :`);
        // Reset hidden fields
        $('#cppt_kesadaran, #cppt_skor, #cppt_bb, #cppt_tb, #cppt_peroral, #cppt_parenteral, #cppt_mlain, #cppt_urin, #cppt_muntah, #cppt_klain').val('');
        $('#is_update_flag').val('false');

        // Reset state edit
        currentEditData = null;
        $('#btn-save-cppt').html('<i class="fas fa-save mr-2"></i> Simpan');
        $('#cpptHistoryTable tbody tr').removeClass('table-info');
        Swal.fire({
            toast: true, position: 'top-end',
            icon: 'info', title: 'Form dibersihkan, mode input baru.',
            showConfirmButton: false, timer: 2000
        });
    }

    // Fungsi untuk memformat string waktu ke H:i:s
    function formatTime(timeString) {
        if (!timeString || typeof timeString !== 'string') return '';
        const trimmedTime = timeString.trim();
        if (!trimmedTime) return '';

        // Coba parse dengan Date object untuk validasi dasar
        const testDate = new Date(`1970-01-01T${trimmedTime}Z`);
        if (isNaN(testDate.getTime())) {
            return ''; // Return empty string for invalid time formats
        }

        let [h, m, s] = trimmedTime.split(':');
        h = h.padStart(2, '0');
        m = (m || '00').padStart(2, '0');
        s = (s || '00').padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    // Fungsi memuat riwayat
    function loadCpptHistory() {
        const url = $('#cppt_history_action').val();
        const noPendaftaran = $('#cppt_nopendaftaran').val();

        $.get(url, { noPendaftaran: noPendaftaran }, function(response) {
            let html = '';
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(item => {
                    // Pisahkan tanggal dan jam, lalu format jamnya
                    const [tgl, rawJam] = (item.TanggalVisit || '  ').split('  ');
                    // Logika dari kode native: editability ditentukan oleh nama profesi.
                    const isEditable = ['perawat', 'bidan', 'dr. umum'].includes((item.Profesi || '').toLowerCase());
                    const jam = (rawJam || '').trim(); // Gunakan nilai jam mentah
                    const rowClass = isEditable ? 'row-perawat' : 'row-ppa'; // 'row-perawat' class is used for styling editable rows
                    const title = isEditable ? 'Klik untuk edit data ini' : 'Klik untuk lihat detail';
                    const detailButton = `<button class="btn btn-xs btn-outline-primary view-detail-btn" title="Lihat Detail"><i class="fas fa-eye"></i></button>`;
                    html += `
                        <tr class="${rowClass}" title="${title}" 
                            data-tanggal="${tgl}"
                            data-jam="${jam}"
                            data-profesi="${escapeHtml(item.Profesi)}"
                            data-pemeriksaan="${escapeHtml(item.Perjalanan)}"
                            data-instruksi="${escapeHtml(item.Pengobatan)}">
                            <td>${item.TanggalVisit}</td>
                            <td>${item.Profesi}</td>
                            <td>${(item.Perjalanan || '').substring(0, 50)}...</td>
                            <td>${(item.Pengobatan || '').substring(0, 50)}...</td>
                            <td>${item.UserEntry} ${detailButton}</td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">Tidak ada riwayat ditemukan.</td></tr>';
            }
            $('#cpptHistoryBody').html(html);
        }).fail(function(xhr) {
            console.error('Gagal memuat riwayat:', xhr);
            $('#cpptHistoryBody').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat riwayat.</td></tr>');
        });
    }

    // Event handler untuk klik baris riwayat
    $('#cpptHistoryBody').on('click', 'tr', function(e) {
        if ($(e.target).closest('.view-detail-btn').length) {
            return;
        }

        const row = $(this);
        const profesi = row.data('profesi') || '';
        const tanggal = row.data('tanggal');
        const jam = row.data('jam');

        // Logika dari kode native: hanya profesi tertentu yang bisa diedit.
        if (['perawat', 'bidan', 'dr. umum'].includes(profesi.toLowerCase())) {
            // Langkah 1: Verifikasi data ke backend sebelum mengizinkan edit
            verifyAndLoadForEdit(tanggal, jam, row);
        }
    });

    // Event handler untuk tombol "Lihat Detail"
    $('#cpptHistoryBody').on('click', '.view-detail-btn', function(e) {
        e.stopPropagation();
        const row = $(this).closest('tr');
        showDetailModal(row);
    });

    // Fungsi BARU untuk verifikasi dan memuat data untuk diedit
    function verifyAndLoadForEdit(tanggal, jam, rowElement) {
        // Gunakan URL dari 'cppt_check_action' yang sudah ada untuk verifikasi
        const url = $('#cppt_check_action').val();
        const noPendaftaran = $('#cppt_nopendaftaran').val();
        const [d, m, y] = tanggal.split('/');
        const formattedDate = `20${y}-${m}-${d}`;

        Swal.fire({
            title: 'Memuat data...',
            didOpen: () => { Swal.showLoading() },
            allowOutsideClick: false
        });

        $.get(url, {
            noPendaftaran: noPendaftaran,
            tanggal: formattedDate,
            jam: jam.trim() // Tambahkan .trim() untuk memastikan tidak ada spasi
        }, function(response) {
            if (response.status === 'exists') {
                // Langkah 2: Jika data ada, lanjutkan untuk memuat detail ke form
                loadDetailForEdit(tanggal, jam.trim(), rowElement); // Kirim jam yang sudah di-trim
            } else { // Jika data tidak ditemukan di CPPTPERAWAT atau ada error
                resetCpptForm(); // Kosongkan form
                Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Data tidak dapat diedit, form direset.', showConfirmButton: false, timer: 3000 });
            }
        }).fail(function() {
            resetCpptForm(); // Kosongkan form juga jika terjadi error pada AJAX
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Gagal memuat data, form direset.', showConfirmButton: false, timer: 3000 });
        });
    }

    // Event listener untuk perubahan tanggal dan jam untuk cek data
    $('#cppt_tanggal, #cppt_jam').on('change', function() {
        if (!currentEditData) {
            checkExistingData();
        }
        else if (currentEditData) {
            resetCpptForm();
            Swal.fire('Mode Edit Dibatalkan', 'Anda mengubah tanggal/jam, silakan isi sebagai data baru.', 'info');
        }
    });

    // Fungsi untuk memuat data ke form untuk diedit
    function loadDetailForEdit(tanggal, jam, rowElement) {
        const url = $('#cppt_detail_action').val();
        const noPendaftaran = $('#cppt_nopendaftaran').val();
        const [d, m, y] = tanggal.split('/');
        const formattedDate = `20${y}-${m}-${d}`;

        $.get(url, { 
            noPendaftaran: noPendaftaran, 
            tanggal: formattedDate, 
            jam: jam // jam sudah di-trim dari pemanggilan fungsi
        }, function(response) {
            if (response.status === 'success') {
                const data = response.data;
                $('#cppt_tanggal').val(formattedDate);
                // Gunakan fungsi formatTime untuk memastikan format jam selalu benar (HH:mm:ss)
                $('#cppt_jam').val(formatTime(data.JAM)); 

                $('#cppt_catatan').val(data.CATATAN);
                $('#cppt_pemeriksaan').val(data.PEMERIKSAAN);
                $('#cppt_instruksi').val(data.INSTRUKSI);
                
                // Populate hidden fields
                $('#cppt_kesadaran').val(data.KESADARAN);
                $('#cppt_skor').val(data.SKOR);
                $('#cppt_bb').val(data.BB);
                $('#cppt_tb').val(data.TB);
                $('#cppt_peroral').val(data.PERORAL);
                $('#cppt_parenteral').val(data.PARENTERAL);
                $('#cppt_mlain').val(data.MLAIN);
                $('#cppt_urin').val(data.URIN);
                $('#cppt_muntah').val(data.MUNTAH);
                $('#cppt_klain').val(data.KLAIN);

                // Simpan nilai jam asli dari database untuk digunakan saat update
                // Ini penting karena input jam di form dikosongkan
                currentEditData = { tanggal: formattedDate, jam: data.JAM.trim() };

                $('#is_update_flag').val('true');
                $('#btn-save-cppt').html('<i class="fas fa-pencil-alt mr-2"></i> Update');

                // Highlight baris
                $('#cpptHistoryTable tbody tr').removeClass('table-info');
                if(rowElement) rowElement.addClass('table-info');

                Swal.fire({
                    toast: true, position: 'top-end',
                    icon: 'info', title: 'Mode Edit Aktif',
                    showConfirmButton: false, timer: 2000
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }).fail(function(xhr) {
            console.error('Gagal memuat detail:', xhr);
            Swal.fire('Error', 'Gagal memuat detail data untuk diedit.', 'error');
        });
    }

    // Fungsi untuk mengecek apakah data sudah ada
    function checkExistingData() {
        const url = $('#cppt_check_action').val();
        const noPendaftaran = $('#cppt_nopendaftaran').val();
        const tanggal = $('#cppt_tanggal').val();
        const jam = $('#cppt_jam').val();

        if (!tanggal || !jam) return;

        $.get(url, { 
            noPendaftaran: noPendaftaran, 
            tanggal: tanggal, 
            jam: jam
        }, function(response) {
            if (response.status === 'exists') {
                Swal.fire({
                    title: 'Data Sudah Ada',
                    text: "Data untuk tanggal dan jam ini sudah ada. Apakah Anda ingin mengedit data tersebut?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Edit',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const rowToClick = $(`#cpptHistoryBody tr[data-tanggal="${tanggal.split('-').reverse().join('/')}"][data-jam="${jam}"]`);
                        loadDetailForEdit(tanggal.split('-').reverse().join('/'), jam, rowToClick.length ? rowToClick : null);
                    }
                });
            }
        }).fail(function(xhr) {
            console.error('Gagal memeriksa data:', xhr);
        });
    }

    // Fungsi untuk menampilkan modal detail
    function showDetailModal(row) {
        const profesi = row.data('profesi');
        const tanggal = row.data('tanggal');
        const jam = row.data('jam');
        const pemeriksaan = row.data('pemeriksaan');
        const instruksi = row.data('instruksi');

        $('#cpptDetailModalLabel').text('Detail Pemeriksaan - ' + profesi);
        $('#cppt-modal-ppa').text(profesi);
        $('#cppt-modal-tanggal').text(tanggal + ' ' + jam);
        $('#cppt-modal-pemeriksaan').text(pemeriksaan || '-');
        $('#cppt-modal-instruksi').text(instruksi || '-');
        $('#cpptDetailModal').modal('show');
    }

    // Submit form
    $('#cpptForm').on('submit', function(e) {
        e.preventDefault();
        const url = $('#cppt_form_action').val();
        
        // Gunakan FormData untuk mengirim data
        const formData = new FormData(this);
        formData.set('NOPENDAFTARAN', $('#cppt_nopendaftaran').val());
        formData.set('NORM', $('#cppt_norm').val());

        // Jika ini adalah update, pastikan kita menggunakan jam asli yang disimpan
        // karena input jam di form mungkin kosong.
        if ($('#is_update_flag').val() === 'true' && currentEditData) {
            formData.set('JAM', currentEditData.jam);
        }

        $('#btn-save-cppt').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    const originalButtonHtml = $('#is_update_flag').val() === 'true' ? '<i class="fas fa-pencil-alt mr-2"></i> Update' : '<i class="fas fa-save mr-2"></i> Simpan';
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                        didClose: () => $('#btn-save-cppt').prop('disabled', false).html(originalButtonHtml)
                    }).then(() => {
                        resetCpptForm();
                        loadCpptHistory();
                    });
                } else {
                    Swal.fire('Gagal', response.message || 'Terjadi kesalahan yang tidak diketahui.', 'error');
                    // Kembalikan tombol ke state semula jika gagal
                    if ($('#is_update_flag').val() === 'true') {
                        $('#btn-save-cppt').prop('disabled', false).html('<i class="fas fa-pencil-alt mr-2"></i> Update');
                    } else {
                        $('#btn-save-cppt').prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Simpan');
                    }
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan. ';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg += Object.values(xhr.responseJSON.errors).flat().join(' ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
                // Kembalikan tombol ke state semula jika error
                if ($('#is_update_flag').val() === 'true') {
                    $('#btn-save-cppt').prop('disabled', false).html('<i class="fas fa-pencil-alt mr-2"></i> Update');
                } else {
                    $('#btn-save-cppt').prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Simpan');
                }
            },
            complete: function() {
                // Untuk memastikan tombol selalu aktif kembali, bahkan jika ada masalah tak terduga
                if ($('#btn-save-cppt').prop('disabled')) {
                    $('#btn-save-cppt').prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Simpan');
                }
            }
        });
    });

    // Tombol Batal/Baru
    $('#btn-reset-cppt').on('click', function() {
        resetCpptForm();
    });

    // Panggil fungsi load riwayat saat form pertama kali dimuat
    loadCpptHistory();
});
</script>