<div class="card-body">
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title mt-1">Pengelompokan Data / Catatan Perkembangan</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" id="btnAddRowRm24d">
                    <i class="fas fa-plus-circle mr-1"></i>Tambah Baris
                </button>
                <button class="btn btn-success btn-sm" id="btnSaveAllRm24d">
                    <i class="fas fa-save mr-1"></i>Simpan Semua
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive w-100">
                <table class="table table-bordered table-hover" id="monitoringTableRm24d">
                    <thead class="thead-light text-center align-middle">
                        <tr>
                            <th style="width: 200px;">Tgl. Shift</th>
                            <th style="width: 240px;">Fokus</th>
                            <th style="width: 240px;">Diagnosa Keperawatan</th>
                            <th style="width: 240px;">Tujuan</th>
                            <th style="width: 240px;">Rencana</th>
                            <th style="width: 240px;">Tindakan</th>
                            <th style="width: 120px;">Jam</th>
                            <th style="width: 250px;">Evaluasi (SOAP)</th>
                            <th style="width: 250px;">Nama yang Menyerahkan</th>
                            <th style="width: 250px;">Nama yang Menerima</th>
                            <th style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="monitoringBodyRm24d">
                        {{-- Data akan dimuat via AJAX --}}
                        <tr>
                            <td colspan="11" class="text-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const noPendaftaran = "{{ $noPendaftaran }}";
    const currentUser = "{{ htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') }}";
    const perawatBidanList = @json($perawatBidan);

    function parseFlexibleDateTime(dateTimeString) {
        if (!dateTimeString || typeof dateTimeString !== 'string') return '';
        return moment(dateTimeString, ["DD/MM/YYYY HH:mm:ss", "YYYY-MM-DD HH:mm:ss", moment.ISO_8601], true).format('YYYY-MM-DDTHH:mm');
    }

    function addNewRow() {
        const index = $('#monitoringBodyRm24d tr').length;
        const now = moment();
        const tglShift = now.format('YYYY-MM-DDTHH:mm');
        const jam = now.format('HH:mm');

        const newRow = `
            <tr data-new="true" data-index="${index}">
                <input type="hidden" class="COUNTER" value="0">
                <td><input type="datetime-local" class="form-control form-control-sm TGLSHIT" value="${tglShift}"></td>
                <td><textarea class="form-control form-control-sm FOKUS" rows="3"></textarea></td>
                <td><textarea class="form-control form-control-sm DIAGNOSA_PRWT" rows="3"></textarea></td>
                <td><textarea class="form-control form-control-sm TUJUAN" rows="3"></textarea></td>
                <td><textarea class="form-control form-control-sm RENCANA" rows="3"></textarea></td>
                <td><textarea class="form-control form-control-sm TINDAKAN" rows="3"></textarea></td>
                <td><input type="time" class="form-control form-control-sm JAM" value="${jam}"></td>
                <td><textarea class="form-control form-control-sm EVALUASI" rows="3"></textarea></td>
                <td><input type="text" class="form-control form-control-sm NM_SERAH" value="${currentUser}" readonly></td>
                <td>
                    <select class="form-control form-control-sm NM_TERIMA select2-penerima">
                        <option value="" selected>Pilih Nama...</option>
                        ${perawatBidanList.map(p => `<option value="${p.Username}">${p.Username}</option>`).join('')}
                    </select>
                </td>
                <td class="text-center align-middle">
                    <button class="btn btn-sm btn-outline-danger btnRemoveRowRm24d"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        $('#monitoringBodyRm24d').append(newRow);
        $('#monitoringBodyRm24d').find('.select2-penerima').last().select2({
            theme: 'bootstrap4'
        });
    }

    function loadMonitoringData() {
        $.get("{{ route('rme.igd.rm24d.history') }}", { noPendaftaran: noPendaftaran }, function(response) {
            $('#monitoringBodyRm24d').empty();
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(function(item, index) {
                    const tglShiftInput = item.TGLSHIT ? moment(item.TGLSHIT).format('YYYY-MM-DDTHH:mm') : '';
                    const jamInput = item.JAM ? moment(item.JAM, 'HH:mm:ss').format('HH:mm') : '';

                    const rowHTML = `
                        <tr data-counter="${item.COUNTER}" data-index="${index}">
                            <input type="hidden" class="COUNTER" value="${item.COUNTER}">
                            <td><input type="datetime-local" class="form-control form-control-sm TGLSHIT" value="${tglShiftInput}"></td>
                            <td><textarea class="form-control form-control-sm FOKUS" rows="3">${item.FOKUS || ''}</textarea></td>
                            <td><textarea class="form-control form-control-sm DIAGNOSA_PRWT" rows="3">${item.DIAGNOSA_PRWT || ''}</textarea></td>
                            <td><textarea class="form-control form-control-sm TUJUAN" rows="3">${item.TUJUAN || ''}</textarea></td>
                            <td><textarea class="form-control form-control-sm RENCANA" rows="3">${item.RENCANA || ''}</textarea></td>
                            <td><textarea class="form-control form-control-sm TINDAKAN" rows="3">${item.TINDAKAN || ''}</textarea></td>
                            <td><input type="time" class="form-control form-control-sm JAM" value="${jamInput}"></td>
                            <td><textarea class="form-control form-control-sm EVALUASI" rows="3">${item.EVALUASI || ''}</textarea></td>
                            <td><input type="text" class="form-control form-control-sm NM_SERAH" value="${item.NM_SERAH || currentUser}" readonly></td>
                            <td>
                                <select class="form-control form-control-sm NM_TERIMA select2-penerima">
                                    <option value="">Pilih Nama...</option>
                                    ${perawatBidanList.map(p => `<option value="${p.Username}" ${item.NM_TERIMA === p.Username ? 'selected' : ''}>${p.Username}</option>`).join('')}
                                </select>
                            </td>
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-outline-danger btnRemoveRowRm24d"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                    $('#monitoringBodyRm24d').append(rowHTML);
                });
                // Inisialisasi semua select2 setelah loop selesai
                $('.select2-penerima').select2({
                    theme: 'bootstrap4'
                });
            } else {
                addNewRow();
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal memuat data.', 'error');
            $('#monitoringBodyRm24d').html('<tr><td colspan="11" class="text-center text-danger">Gagal memuat data.</td></tr>');
        });
    }

    function saveAllData() {
        const dataToSave = [];
        $('#monitoringBodyRm24d tr').each(function() {
            const row = $(this);
            dataToSave.push({
                COUNTER: row.find('.COUNTER').val() || 0,
                TGLSHIT: row.find('.TGLSHIT').val() ? row.find('.TGLSHIT').val().replace('T', ' ') : '',
                FOKUS: row.find('.FOKUS').val(),
                DIAGNOSA_PRWT: row.find('.DIAGNOSA_PRWT').val(),
                TUJUAN: row.find('.TUJUAN').val(),
                RENCANA: row.find('.RENCANA').val(),
                JAM: row.find('.JAM').val(),
                TINDAKAN: row.find('.TINDAKAN').val(),
                EVALUASI: row.find('.EVALUASI').val(),
                NM_SERAH: row.find('.NM_SERAH').val(),
                NM_TERIMA: row.find('.NM_TERIMA').val(),
                TTD_TERIMA: null // TTD tidak digunakan lagi
            });
        });

        if (dataToSave.length === 0) {
            Swal.fire('Peringatan', 'Tidak ada data untuk disimpan.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Menyimpan...',
            html: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: "{{ route('rme.igd.rm24d.store') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                nopendaftaran: noPendaftaran,
                monitoring: dataToSave
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil', response.message, 'success');
                    loadMonitoringData();
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan: ' + (xhr.responseJSON.message || xhr.statusText), 'error');
            }
        });
    }

    $('#btnAddRowRm24d').click(addNewRow);
    $('#btnSaveAllRm24d').click(saveAllData);

    $(document).on('click', '.btnRemoveRowRm24d', function() {
        const row = $(this).closest('tr');
        const counter = row.find('.COUNTER').val();

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                if (counter && counter != '0') {
                    $.post("{{ route('rme.igd.rm24d.destroy') }}", { // Menggunakan POST
                        _token: "{{ csrf_token() }}",
                        nopendaftaran: noPendaftaran,
                        counter: counter
                    }, function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                            row.remove();
                        } else {
                            Swal.fire('Gagal', 'Gagal menghapus: ' + response.message, 'error');
                        }
                    }).fail(function(xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan: ' + (xhr.responseJSON.message || xhr.statusText), 'error');
                    });
                } else {
                    row.remove();
                    Swal.fire('Dibatalkan', 'Baris baru telah dibatalkan.', 'info');
                }
            }
        });
    });


    // Initial load
    loadMonitoringData();
});
</script>