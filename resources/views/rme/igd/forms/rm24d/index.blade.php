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
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="monitoringTableRm24d" style="min-width: 2000px;">
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
                            <th style="width: 200px;">Nama yang Menyerahkan</th>
                            <th style="width: 280px;">Nama & TTD yang Menerima</th>
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
    let signaturePads = {};

    function initSignature(canvasId, inputId, existingSignature) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const hiddenInput = document.getElementById(inputId);
        
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
        signaturePads[canvasId] = signaturePad;

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
            if (signaturePad.fromDataURL && existingSignature) {
                signaturePad.fromDataURL(existingSignature);
            }
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        signaturePad.addEventListener("endStroke", () => {
            if (!signaturePad.isEmpty()) {
                hiddenInput.value = signaturePad.toDataURL('image/jpeg', 0.75);
            }
        });
    }

    window.clearSignatureRm24d = function(index) {
        const canvasId = 'canvasTTDRm24d' + index;
        if (signaturePads[canvasId]) {
            signaturePads[canvasId].clear();
            $('#hiddenTTDRm24d' + index).val('');
        }
        const preview = $(`#canvasTTDRm24d${index}`).closest('.signature-container').find('.signature-preview');
        if (preview.length) {
            preview.hide();
            $(`#${canvasId}`).show();
        }
    }

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
                    <input type="text" class="form-control form-control-sm NM_TERIMA mb-2" placeholder="Nama Penerima...">
                    <div class="signature-container position-relative">
                        <img src="" class="border signature-preview" style="width: 250px; height: 100px; display: none;">
                        <canvas id="canvasTTDRm24d${index}" class="border" width="250" height="100" style="display: block;"></canvas>
                        <div class="mt-1">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-ubah-ttd" style="display: none;"><i class="fas fa-pencil-alt"></i> Ubah</button>
                            <button type="button" class="btn btn-sm btn-outline-warning btn-clear-ttd" onclick="clearSignatureRm24d(${index})">
                                <i class="fas fa-eraser"></i> Hapus
                            </button>
                        </div>
                        <input type="hidden" class="TTD_TERIMA" id="hiddenTTDRm24d${index}">
                    </div>
                </td>
                <td class="text-center align-middle">
                    <button class="btn btn-sm btn-outline-danger btnRemoveRowRm24d"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        $('#monitoringBodyRm24d').append(newRow);
        initSignature(`canvasTTDRm24d${index}`, `hiddenTTDRm24d${index}`);
    }

    function loadMonitoringData() {
        $.get("{{ route('rme.igd.rm24d.history') }}", { noPendaftaran: noPendaftaran }, function(response) {
            $('#monitoringBodyRm24d').empty();
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(function(item, index) {
                    const tglShiftInput = item.TGLSHIT ? moment(item.TGLSHIT).format('YYYY-MM-DDTHH:mm') : '';
                    const jamInput = item.JAM ? moment(item.JAM, 'HH:mm:ss').format('HH:mm') : '';
                    
                    // Logika yang lebih andal: asumsikan TTD ada jika COUNTER ada dan bukan 0.
                    // Controller akan menangani jika TTD-nya memang kosong (null).
                    const hasSignature = item.COUNTER && item.COUNTER != '0';

                    const ttdUrl = hasSignature 
                        ? `{{ route('rme.igd.rm24d.showSignature', [':noPendaftaran', ':counter']) }}`.replace(':noPendaftaran', encodeURIComponent(noPendaftaran)).replace(':counter', item.COUNTER) + `?_=${new Date().getTime()}` // Use different param to avoid confusion
                        : '';

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
                                <input type="text" class="form-control form-control-sm NM_TERIMA mb-2" placeholder="Nama Penerima..." value="${item.NM_TERIMA || ''}">
                                <div class="signature-container position-relative">
                                    <img src="${ttdUrl}" class="border signature-preview" style="width: 250px; height: 100px; display: none;">
                                    <canvas id="canvasTTDRm24d${index}" class="border" width="250" height="100" style="display: block;"></canvas>
                                    <div class="mt-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-ubah-ttd" style="display: none;" title="Ubah Tanda Tangan">
                                            <i class="fas fa-pencil-alt"></i> Ubah
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning btn-clear-ttd" onclick="clearSignatureRm24d(${index})" title="Hapus Tanda Tangan">
                                            <i class="fas fa-eraser"></i> Hapus
                                        </button>
                                    </div>
                                    <input type="hidden" class="TTD_TERIMA" id="hiddenTTDRm24d${index}">
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <button class="btn btn-sm btn-outline-danger btnRemoveRowRm24d"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                    $('#monitoringBodyRm24d').append(rowHTML);
                    initSignature(`canvasTTDRm24d${index}`, `hiddenTTDRm24d${index}`);
                    
                    // Logika untuk menampilkan TTD yang sudah ada
                    if (hasSignature && ttdUrl) {
                        const container = $(`#canvasTTDRm24d${index}`).closest('.signature-container');
                        const previewImg = container.find('.signature-preview');
                        const canvas = container.find('canvas');
                        const btnUbah = container.find('.btn-ubah-ttd');
                        const btnClear = container.find('.btn-clear-ttd');

                        // Cek apakah gambar bisa dimuat
                        previewImg.on('load', function() {
                            $(this).show(); // Tampilkan gambar
                            canvas.hide();   // Sembunyikan canvas
                            btnUbah.show();  // Tampilkan tombol ubah
                            btnClear.hide(); // Sembunyikan tombol hapus
                        }).on('error', function(e) {
                            // Jika gambar gagal dimuat, pastikan canvas terlihat
                            console.error("Gagal memuat gambar tanda tangan dari URL:", ttdUrl);
                            canvas.show();
                        });
                    }
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
                TTD_TERIMA: row.find('.TTD_TERIMA').val()
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

    // Handler untuk tombol "Ubah TTD"
    $(document).on('click', '.btn-ubah-ttd', function() {
        const container = $(this).closest('.signature-container');
        container.find('.signature-preview').hide();
        container.find('canvas').show(); // Tampilkan canvas untuk menggambar ulang
        container.find('.btn-clear-ttd').show(); // Tampilkan tombol clear
        $(this).hide();
    });


    // Initial load
    loadMonitoringData();
});
</script>