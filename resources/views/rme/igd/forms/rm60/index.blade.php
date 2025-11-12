<div class="card-body">
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title mt-1">Monitoring Pasien Gawat Darurat</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" id="btnAddRowRm60">
                    <i class="fas fa-plus-circle mr-1"></i>Tambah Baris
                </button>
                <button class="btn btn-success btn-sm" id="btnSaveAllRm60">
                    <i class="fas fa-save mr-1"></i>Simpan Semua
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="monitoringTableRm60" style="min-width: 1800px;">
                    <thead class="thead-light text-center align-middle">
                        <tr>
                            <th style="width: 200px;">Waktu</th>
                            <th style="width: 180px;">Kesadaran / GCS</th>
                            <th style="width: 120px;">TD (mmHg)</th>
                            <th style="width: 120px;">Nadi (x/menit)</th>
                            <th style="width: 120px;">RR (x/menit)</th>
                            <th style="width: 120px;">Suhu (°C)</th>
                            <th style="width: 120px;">SpO2 (%)</th>
                            <th style="width: 150px;">Produksi Urine</th>
                            <th style="width: 250px;">Terapi / Tindakan</th>
                            <th style="width: 250px;">Konsultasi</th>
                            <th style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="monitoringBodyRm60">
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

    function addNewRow() {
        const index = $('#monitoringBodyRm60 tr').length;
        const now = moment().format('YYYY-MM-DDTHH:mm');

        const newRow = `
            <tr data-new="true" data-index="${index}">
                <input type="hidden" class="COUNTER" value="0">
                <td><input type="datetime-local" class="form-control form-control-sm WAKTU" value="${now}"></td>
                <td><input type="text" class="form-control form-control-sm KESADARAN" placeholder="Kesadaran..."></td>
                <td><input type="text" class="form-control form-control-sm TD" placeholder="TD..."></td>
                <td><input type="text" class="form-control form-control-sm NADI" placeholder="Nadi..."></td>
                <td><input type="text" class="form-control form-control-sm RR" placeholder="RR..."></td>
                <td><input type="text" class="form-control form-control-sm SUHU" placeholder="Suhu..."></td>
                <td><input type="text" class="form-control form-control-sm SPO" placeholder="SpO2..."></td>
                <td><input type="text" class="form-control form-control-sm URINE" placeholder="Urine..."></td>
                <td><input type="text" class="form-control form-control-sm TERAPI" placeholder="Terapi/Tindakan..."></td>
                <td><input type="text" class="form-control form-control-sm KONSUL" placeholder="Konsultasi..."></td>
                <td class="text-center align-middle">
                    <button class="btn btn-sm btn-outline-danger btnRemoveRowRm60">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#monitoringBodyRm60').append(newRow);
    }

    function loadMonitoringData() {
        $('#monitoringBodyRm60').html('<tr><td colspan="11" class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>');
        $.get("{{ route('rme.igd.form.rm60.history') }}", { noPendaftaran: noPendaftaran }, function(response) {
            $('#monitoringBodyRm60').empty();
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(function(item, index) {
                    const waktuInput = item.WAKTU ? moment(item.WAKTU, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DDTHH:mm') : '';

                    const rowHTML = `
                        <tr data-counter="${item.COUNTER}" data-index="${index}">
                            <input type="hidden" class="COUNTER" value="${item.COUNTER}">
                            <td><input type="datetime-local" class="form-control form-control-sm WAKTU" value="${waktuInput}"></td>
                            <td><input type="text" class="form-control form-control-sm KESADARAN" value="${item.KESADARAN || ''}" placeholder="Kesadaran..."></td>
                            <td><input type="text" class="form-control form-control-sm TD" value="${item.TD || ''}" placeholder="TD..."></td>
                            <td><input type="text" class="form-control form-control-sm NADI" value="${item.NADI || ''}" placeholder="Nadi..."></td>
                            <td><input type="text" class="form-control form-control-sm RR" value="${item.RR || ''}" placeholder="RR..."></td>
                            <td><input type="text" class="form-control form-control-sm SUHU" value="${item.SUHU || ''}" placeholder="Suhu..."></td>
                            <td><input type="text" class="form-control form-control-sm SPO" value="${item.SPO || ''}" placeholder="SpO2..."></td>
                            <td><input type="text" class="form-control form-control-sm URINE" value="${item.URINE || ''}" placeholder="Urine..."></td>
                            <td><input type="text" class="form-control form-control-sm TERAPI" value="${item.TERAPI || ''}" placeholder="Terapi/Tindakan..."></td>
                            <td><input type="text" class="form-control form-control-sm KONSUL" value="${item.KONSUL || ''}" placeholder="Konsultasi..."></td>
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-outline-danger btnRemoveRowRm60">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#monitoringBodyRm60').append(rowHTML);
                });
            } else {
                addNewRow(); // Add a new row if no data exists
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal memuat data monitoring.', 'error');
            $('#monitoringBodyRm60').html('<tr><td colspan="11" class="text-center text-danger">Gagal memuat data.</td></tr>');
        });
    }

    function saveAllData() {
        const dataToSave = [];
        $('#monitoringBodyRm60 tr').each(function() {
            const row = $(this);
            dataToSave.push({
                COUNTER: row.find('.COUNTER').val() || 0,
                WAKTU: row.find('.WAKTU').val() ? row.find('.WAKTU').val().replace('T', ' ') : '',
                KESADARAN: row.find('.KESADARAN').val(),
                TD: row.find('.TD').val(),
                NADI: row.find('.NADI').val(),
                RR: row.find('.RR').val(),
                SUHU: row.find('.SUHU').val(),
                SPO: row.find('.SPO').val(),
                URINE: row.find('.URINE').val(),
                TERAPI: row.find('.TERAPI').val(),
                KONSUL: row.find('.KONSUL').val(),
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
            url: "{{ route('rme.igd.form.rm60.store') }}",
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
                Swal.fire('Error', 'Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    }

    $('#btnAddRowRm60').click(addNewRow);
    $('#btnSaveAllRm60').click(saveAllData);

    $(document).on('click', '.btnRemoveRowRm60', function() {
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
                    $.post("{{ route('rme.igd.form.rm60.destroy') }}", {
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
                        Swal.fire('Error', 'Terjadi kesalahan: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                    });
                } else {
                    row.remove();
                    Swal.fire({toast: true, position: 'top-end', icon: 'info', title: 'Baris baru telah dibatalkan.', showConfirmButton: false, timer: 2000});
                }
            }
        });
    });

    // Initial load
    loadMonitoringData();
});
</script>