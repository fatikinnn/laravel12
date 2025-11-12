<div class="card-body">
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title mt-1">Monitoring Infus</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" id="btnAddRowRm16b">
                    <i class="fas fa-plus-circle mr-1"></i>Tambah Baris
                </button>
                <button class="btn btn-success btn-sm" id="btnSaveAllRm16b">
                    <i class="fas fa-save mr-1"></i>Simpan Semua
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="monitoringTableRm16b" style="min-width: 1500px;">
                    <thead class="thead-light text-center align-middle">
                        <tr>
                            <th style="width: 200px;">Tanggal dan Jam</th>
                            <th style="width: 250px;">Nama Cairan dan No Flabot</th>
                            <th style="width: 120px;">TTS / mnt</th>
                            <th style="width: 200px;">Kondisi Infus</th>
                            <th style="width: 200px;">Keterangan / Tindakan</th>
                            <th style="width: 200px;">Perawat</th>
                            <th style="width: 200px;">Pasien</th>
                            <th style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="monitoringBodyRm16b">
                        <tr>
                            <td colspan="8" class="text-center">
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
    // Menggunakan pola dari RM3B untuk mengambil detail pasien
    const patientDetails = @json($patientDetails ?? []);
    const currentPatient = patientDetails['Nama_Pasien'] || patientDetails['Nama Pasien'] || patientDetails['NAMAPASIEN'] || '';

    // --- DEBUGGING START ---
    console.log('RM16B: patientDetails object:', patientDetails);
    console.log('RM16B: currentPatient value:', currentPatient);
    // --- DEBUGGING END ---

    function addNewRow() {
        const index = $('#monitoringBodyRm16b tr').length;
        const now = moment().format('YYYY-MM-DDTHH:mm');

        const newRow = `
            <tr data-new="true" data-index="${index}">
    <input type="hidden" class="COUNTER" value="0">
    <td><input type="datetime-local" class="form-control form-control-sm WAKTU" value="${now}"></td>
    <td><input type="text" class="form-control form-control-sm CAIRAN" placeholder="Cairan & No Flabot..."></td>
    <td><input type="text" class="form-control form-control-sm TTS" placeholder="TTS/mnt..."></td>
    <td><input type="text" class="form-control form-control-sm KONDISI" placeholder="Kondisi..."></td>
    <td><input type="text" class="form-control form-control-sm TINDAKAN" placeholder="Tindakan..."></td>
    <td><input type="text" class="form-control form-control-sm NM_PERAWAT" value="${currentUser}" readonly></td>
    <td><input type="text" class="form-control form-control-sm NM_PASIEN" value="${currentPatient}" readonly></td>
    <td class="text-center align-middle">
        <button class="btn btn-sm btn-outline-danger btnRemoveRowRm16b">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
`;
        $('#monitoringBodyRm16b').append(newRow);
    }

    function loadMonitoringData() {
        $.get("{{ route('rme.igd.rm16b.history') }}", { noPendaftaran: noPendaftaran }, function(response) {
            $('#monitoringBodyRm16b').empty();
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(function(item, index) { 
                    // Perbaikan: Beri tahu moment.js format tanggal yang datang dari server
                    // Cek juga jika WAKTU null atau kosong
                    const waktuInput = item.WAKTU ? moment(item.WAKTU).format('YYYY-MM-DDTHH:mm') : '';

                    const rowHTML = `
                        <tr data-counter="${item.COUNTER}" data-index="${index}">
    <input type="hidden" class="COUNTER" value="${item.COUNTER}">
    <td><input type="datetime-local" class="form-control form-control-sm WAKTU" value="${waktuInput}"></td>
    <td><input type="text" class="form-control form-control-sm CAIRAN" value="${item.CAIRAN || ''}" placeholder="Cairan & No Flabot..."></td>
    <td><input type="text" class="form-control form-control-sm TTS" value="${item.TTS || ''}" placeholder="TTS/mnt..."></td>
    <td><input type="text" class="form-control form-control-sm KONDISI" value="${item.KONDISI || ''}" placeholder="Kondisi..."></td>
    <td><input type="text" class="form-control form-control-sm TINDAKAN" value="${item.TINDAKAN || ''}" placeholder="Tindakan..."></td>
    <td><input type="text" class="form-control form-control-sm NM_PERAWAT" value="${item.NM_PERAWAT || currentUser}" readonly></td>
    <td><input type="text" class="form-control form-control-sm NM_PASIEN" value="${item.NM_PASIEN || currentPatient}" readonly></td>
    <td class="text-center align-middle">
        <button class="btn btn-sm btn-outline-danger btnRemoveRowRm16b">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
`;
                    $('#monitoringBodyRm16b').append(rowHTML);
                });
            } else {
                addNewRow(); // Tambah baris baru jika tidak ada data
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal memuat data monitoring infus.', 'error');
            $('#monitoringBodyRm16b').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
        });
    }

    function saveAllData() {
        const dataToSave = [];
        $('#monitoringBodyRm16b tr').each(function() {
            const row = $(this);
            dataToSave.push({
                COUNTER: row.find('.COUNTER').val() || 0,
                WAKTU: row.find('.WAKTU').val() ? row.find('.WAKTU').val().replace('T', ' ') : '',
                CAIRAN: row.find('.CAIRAN').val(),
                TTS: row.find('.TTS').val(),
                KONDISI: row.find('.KONDISI').val(),
                TINDAKAN: row.find('.TINDAKAN').val(),
                NM_PERAWAT: row.find('.NM_PERAWAT').val(),
                NM_PASIEN: row.find('.NM_PASIEN').val(),
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
            url: "{{ route('rme.igd.rm16b.store') }}",
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

    $('#btnAddRowRm16b').click(addNewRow);
    $('#btnSaveAllRm16b').click(saveAllData);

    $(document).on('click', '.btnRemoveRowRm16b', function() {
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
                    $.post("{{ route('rme.igd.rm16b.destroy') }}", {
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