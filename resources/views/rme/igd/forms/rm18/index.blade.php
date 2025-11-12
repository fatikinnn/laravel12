<div class="card-body">
    <div class="row">
        <!-- Kolom Kiri: Daftar Obat Pasien -->
        <div class="col-md-5 mb-4 d-flex">
            <div class="card card-outline card-primary shadow-sm h-100 w-100 d-flex flex-column">
                <div class="card-header">
                    <h3 class="card-title mt-1"><i class="fas fa-pills mr-2"></i>Daftar Obat Pasien</h3>
                    <div class="card-tools d-flex align-items-center">
                        <!-- Pagination controls will be inserted here -->
                        <div id="paginationDaftarObatRm18" class="mr-2" style="display: none;"></div>
                        <input type="text" id="searchObatPenjualanRm18" class="form-control form-control-sm" placeholder="Cari obat..." style="width: 150px;">
                    </div>
                </div>
                <div class="card-body flex-grow-1 overflow-auto">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm" id="tabelDaftarObatRm18">
                            <thead class="thead-light text-center sticky-top">
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Dosis</th> 
                                    <th>Tgl & Jam Jual</th>
                                </tr>
                            </thead>
                            <tbody id="bodyDaftarObatRm18">
                                <tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Pemberian Obat -->
        <div class="col-md-7 mb-4 d-flex">
            <div class="card card-outline card-success shadow-sm h-100 w-100 d-flex flex-column">
                <div class="card-header">
                    <h3 class="card-title mt-1"><i class="fas fa-edit mr-2"></i>Formulir Pemberian Obat</h3>
                </div>
                <div class="card-body flex-grow-1 overflow-auto">
                    <form id="formPemberianObatRm18">
                        @csrf
                        <input type="hidden" name="nopendaftaran" value="{{ $noPendaftaran }}">
                        <input type="hidden" name="KODEBARANG" id="form_kodebarang_rm18">
                        <input type="hidden" name="TGL_OBAT" id="form_tgl_obat_rm18">
                        <input type="hidden" name="JAM_OBAT" id="form_jam_obat_rm18">

                        <div class="form-group">
                            <label for="form_nama_obat_rm18">Nama Obat</label>
                            <input type="text" id="form_nama_obat_rm18" name="NAMA_OBAT" class="form-control bg-light" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label for="form_dosis_rm18">Dosis</label><input type="text" id="form_dosis_rm18" name="DOSIS" class="form-control"></div>
                            <div class="col-md-6 form-group"><label for="form_frekuensi_rm18">Frekuensi</label><input type="text" id="form_frekuensi_rm18" name="FREKUENSI" class="form-control" placeholder="Contoh: 3x1"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label for="form_cara_pemberian_rm18">Cara Pemberian</label><input type="text" id="form_cara_pemberian_rm18" name="CARA_PEMBERIAN" class="form-control" placeholder="Contoh: Oral"></div>
                            <div class="col-md-6 form-group"><label for="form_d_check_rm18">D-Check</label><input type="text" id="form_d_check_rm18" name="D_CHECK" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label for="form_tgl_pemberian_rm18">Tgl Pemberian</label><input type="date" id="form_tgl_pemberian_rm18" name="TGL_PEMBERIAN" class="form-control"></div>
                            <div class="col-md-6 form-group"><label for="form_jam_pemberian_rm18">Jam Pemberian</label><input type="time" id="form_jam_pemberian_rm18" name="JAM_PEMBERIAN" class="form-control" step="1"></div>
                        </div>
                        <div class="form-group"><label for="form_keterangan_rm18">Keterangan</label><textarea id="form_keterangan_rm18" name="KETERANGAN" class="form-control" rows="2"></textarea></div>
                        
                        <div class="form-group text-center">
                            <label class="d-block font-weight-bold">Tanda Tangan Pasien/Wali</label>
                            <div class="border rounded p-2 mx-auto bg-white" style="max-width: 400px;">
                                <canvas id="canvasTTDRm18" style="width: 100%; height: 150px;"></canvas>
                            </div>
                            <input type="hidden" id="hiddenTTDRm18" name="TTD_PENERIMA">
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clear-signature-btn-rm18">Hapus Tanda Tangan</button>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Catatan</button>
                            <button type="button" class="btn btn-outline-secondary" id="btnClearFormRm18"><i class="fas fa-sync-alt mr-1"></i> Bersihkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagian Bawah: Riwayat Pemberian Obat -->
    <div class="card card-outline card-info shadow-sm mt-4">
        <div class="card-header">
            <h3 class="card-title mt-1"><i class="fas fa-history mr-2"></i>Riwayat Pemberian Obat</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="tabelRiwayatObatRm18" style="min-width: 1000px;">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>Tgl & Jam Pemberian</th>
                            <th>Nama Obat</th>
                            <th>Tgl & Jam Obat</th>
                            <th>Dosis</th>
                            <th>Frekuensi</th>
                            <th>Cara Pemberian</th>
                            <th>D-Check</th>
                            <th>Keterangan</th>
                            <th>Perawat</th>
                            <th style="width: 100px;">TTD</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyRiwayatObatRm18" class="text-center">
                        <tr><td colspan="10" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat riwayat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const noPendaftaran = "{{ $noPendaftaran }}";
    // Variabel untuk pagination daftar obat
    let allDaftarObat = [];
    let currentPageDaftarObat = 1;
    const itemsPerPageDaftarObat = 10;

    let signaturePadRm18;

    // --- Inisialisasi Tanda Tangan ---
    function initSignaturePadRm18() {
        const canvas = document.getElementById('canvasTTDRm18');
        if (!canvas) return;
        signaturePadRm18 = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)', penColor: 'rgb(0, 0, 0)' });

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePadRm18.clear();
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        signaturePadRm18.addEventListener("endStroke", () => {
            if (!signaturePadRm18.isEmpty()) {
                // Logika kompresi gambar seperti di RM8A
                const tempCanvas = document.createElement('canvas');
                const tempCtx = tempCanvas.getContext('2d');
                const targetWidth = 100;
                const targetHeight = 100;

                tempCanvas.width = targetWidth;
                tempCanvas.height = targetHeight;

                // Beri background putih
                tempCtx.fillStyle = "white";
                tempCtx.fillRect(0, 0, targetWidth, targetHeight);

                // Gambar ulang dengan ukuran baru
                tempCtx.drawImage(signaturePadRm18.canvas, 0, 0, targetWidth, targetHeight);

                // Simpan sebagai JPEG dengan kualitas 75%
                const resizedImage = tempCanvas.toDataURL('image/jpeg', 0.75);
                $('#hiddenTTDRm18').val(resizedImage);
            }
        });
    }

    $('#clear-signature-btn-rm18').on('click', function() {
        if (signaturePadRm18) {
            signaturePadRm18.clear();
            $('#hiddenTTDRm18').val('');
        }
    });

    // --- Fungsi Bantuan ---
    function formatDosis(dosis) {
        if (!dosis) return '';
        const num = parseFloat(dosis);
        return num % 1 === 0 ? num.toFixed(0) : num.toFixed(2);
    }

    function clearForm() {
        $('#formPemberianObatRm18')[0].reset();
        $('#form_kodebarang_rm18, #form_tgl_obat_rm18, #form_jam_obat_rm18, #hiddenTTDRm18').val('');
        $('#tabelDaftarObatRm18 .clickable-row').removeClass('table-info'); // Hapus highlight dari tabel obat
        if (signaturePadRm18) signaturePadRm18.clear();
        const now = new Date();
        $('#form_tgl_pemberian_rm18').val(now.toISOString().split('T')[0]);
        $('#form_jam_pemberian_rm18').val(now.toTimeString().slice(0, 8));
    }

    // --- Fungsi Pagination ---
    function renderDaftarObatPage(page) {
        currentPageDaftarObat = page;
        const tableBody = $('#bodyDaftarObatRm18');
        const paginationControls = $('#paginationDaftarObatRm18');
        tableBody.empty();
        paginationControls.empty();

        const totalItems = allDaftarObat.length;
        const totalPages = Math.ceil(totalItems / itemsPerPageDaftarObat);

        if (totalItems === 0) {
            tableBody.append('<tr><td colspan="3" class="text-center">Tidak ada data obat ditemukan.</td></tr>');
            paginationControls.hide();
            return;
        }

        const startIndex = (page - 1) * itemsPerPageDaftarObat;
        const endIndex = startIndex + itemsPerPageDaftarObat;
        const pageItems = allDaftarObat.slice(startIndex, endIndex);

        pageItems.forEach(obat => {
            const tglJual = obat.TanggalJual ? moment(obat.TanggalJual).format('DD/MM/YY') : '-';
            const jamObat = obat.JamObat ? obat.JamObat.substring(0, 5) : '';
            const tglJamDisplay = `${tglJual} ${jamObat}`.trim();
            const row = `<tr class="clickable-row" style="cursor: pointer;" 
                             data-kode="${obat.KodeBarang || ''}" 
                             data-nama="${obat.NamaBarang || ''}"
                             data-dosis="${obat.DosisObat || ''}"
                             data-tgljual="${obat.TanggalJual || ''}"
                             data-jamobat="${obat.JamObat || ''}">
                            <td>${obat.NamaBarang || ''}</td>
                            <td>${formatDosis(obat.DosisObat)}</td>
                            <td>${tglJamDisplay}</td>
                        </tr>`;
            tableBody.append(row);
        });

        const prevDisabled = page === 1 ? 'disabled' : '';
        const nextDisabled = page === totalPages ? 'disabled' : '';
        const paginationHtml = `
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-primary btn-sm" id="prevPageObat" ${prevDisabled}><i class="fas fa-chevron-left"></i></button>
                <span class="text-muted small mx-2">Hal ${page} / ${totalPages}</span>
                <button class="btn btn-outline-primary btn-sm" id="nextPageObat" ${nextDisabled}><i class="fas fa-chevron-right"></i></button>
            </div>
        `;
        paginationControls.html(paginationHtml).show();
    }

    // --- Memuat Data ---
    function loadDaftarObat() {
        const tableBody = $('#bodyDaftarObatRm18');
        tableBody.html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>');
        $.get("{{ route('rme.igd.rm18.obatList') }}", { noPendaftaran: noPendaftaran }, function(response) {
            if (response.status === 'success' && response.data.length > 0) {
                allDaftarObat = response.data;
                renderDaftarObatPage(1);
            } else {
                allDaftarObat = [];
                renderDaftarObatPage(1);
            }
        }).fail(() => {
            tableBody.html('<tr><td colspan="3" class="text-center text-danger">Gagal memuat data obat.</td></tr>');
        });
    }

    function loadRiwayatObat() {
        const tableBody = $('#bodyRiwayatObatRm18');
        tableBody.html('<tr><td colspan="11" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat riwayat...</td></tr>');
        $.get("{{ route('rme.igd.rm18.history') }}", { noPendaftaran: noPendaftaran }, function(response) {
            tableBody.empty();
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(item => {
                    const ttdImage = item.TTD_PENERIMA_BASE64
                        ? `<img src="data:image/jpeg;base64,${item.TTD_PENERIMA_BASE64}" alt="TTD" class="img-fluid" style="max-height: 40px;"/>`
                        : '<span class="text-muted">-</span>';
                    const tglBeri = item.TGL_PEMBERIAN ? moment(item.TGL_PEMBERIAN).format('DD/MM/YY') : '-';
                    const jamBeri = item.JAM_PEMBERIAN ? item.JAM_PEMBERIAN.substring(0, 5) : '-';
                    const tglObat = item.TGL_OBAT ? moment(item.TGL_OBAT).format('DD/MM/YY') : '-';
                    const jamObat = item.JAM_OBAT ? item.JAM_OBAT.substring(0, 5) : '-';

                    const row = `<tr data-counter="${item.COUNTER}">
                                    <td>${tglBeri} ${jamBeri}</td>
                                    <td>${item.NAMA_OBAT || '-'}</td>
                                    <td>${tglObat} ${jamObat}</td>
                                    <td>${item.DOSIS || ''}</td>
                                    <td>${item.FREKUENSI || '-'}</td>
                                    <td>${item.CARA_PEMBERIAN || '-'}</td>
                                    <td>${item.D_CHECK || '-'}</td>
                                    <td>${item.KETERANGAN || '-'}</td>
                                    <td>${(item.NAMA_PERAWAT || '').trim()}</td>
                                    <td class="text-center align-middle">${ttdImage}</td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-danger btn-delete-riwayat-rm18" title="Hapus" data-counter="${item.COUNTER}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                    tableBody.append(row);
                });
            } else {
                tableBody.append('<tr><td colspan="11" class="text-center">Belum ada riwayat pemberian obat.</td></tr>');
            }
        }).fail(() => {
            tableBody.html('<tr><td colspan="11" class="text-center text-danger">Gagal memuat riwayat.</td></tr>');
        });
    }

    // --- Event Handlers ---
    $('#searchObatPenjualanRm18').on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#bodyDaftarObatRm18 tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $(document).on('click', '#prevPageObat', function() {
        if (currentPageDaftarObat > 1) renderDaftarObatPage(currentPageDaftarObat - 1);
    });

    $(document).on('click', '#nextPageObat', function() {
        renderDaftarObatPage(currentPageDaftarObat + 1);
    });

    $(document).on('click', '#tabelDaftarObatRm18 .clickable-row', function() {
        $('#tabelDaftarObatRm18 .clickable-row').removeClass('table-info');
        $(this).addClass('table-info');
        const data = $(this).data();
        $('#form_tgl_obat_rm18').val(data.tgljual);
        $('#form_jam_obat_rm18').val(data.jamobat);
        $('#form_nama_obat_rm18').val(data.nama);
        $('#form_dosis_rm18').val(formatDosis(data.dosis));
        $('#form_kodebarang_rm18').val(data.kode);

        $('#form_frekuensi_rm18').focus();
        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: `Obat dipilih.`, showConfirmButton: false, timer: 1500 });
    });

    $('#btnClearFormRm18').click(clearForm);

    $('#formPemberianObatRm18').on('submit', function(e) {
        e.preventDefault();
        if (!$('#form_nama_obat_rm18').val()) {
            Swal.fire('Peringatan', 'Pilih obat terlebih dahulu dari daftar.', 'warning');
            return;
        }

        const formData = $(this).serialize();
        const button = $(this).find('button[type="submit"]');
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: "{{ route('rme.igd.rm18.store') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil', response.message, 'success');
                    clearForm();
                    loadRiwayatObat();
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan server.', 'error');
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Catatan');
            }
        });
    });

    $(document).on('click', '.btn-delete-riwayat-rm18', function(e) {
        e.stopPropagation(); // Mencegah trigger klik pada baris
        const counter = $(this).data('counter');
        Swal.fire({
            title: 'Hapus Data Ini?',
            text: "Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('rme.igd.rm18.destroy') }}", {
                    _token: "{{ csrf_token() }}",
                    nopendaftaran: noPendaftaran,
                    counter: counter
                }, function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Terhapus!', response.message, 'success');
                        loadRiwayatObat();
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                }).fail((xhr) => {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menghubungi server.', 'error');
                });
            }
        });
    });

    // --- Initial Load ---
    initSignaturePadRm18();
    clearForm();
    loadDaftarObat();
    loadRiwayatObat();
});

</script>