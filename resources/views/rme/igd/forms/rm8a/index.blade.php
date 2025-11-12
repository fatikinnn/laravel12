<div class="card-body">
    {{-- Hidden Inputs for AJAX URLs --}}
    <input type="hidden" id="rm8a_history_url" value="{{ route('rme.igd.form.rm8a.history') }}">
    <input type="hidden" id="rm8a_detail_url" value="{{ route('rme.igd.form.rm8a.detail') }}">

    {{-- Riwayat Pemeriksaan --}}
    <div class="mb-4">
        <h6 class="font-weight-bold mb-3"><i class="fas fa-history mr-2"></i>Riwayat Edukasi</h6>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-hover" id="rm8aHistoryTable">
                <thead class="thead-light sticky-top" style="top: -1px;">
                    <tr>
                        <th>Tanggal & Jam</th>
                        <th>Materi Edukasi</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody id="rm8aHistoryBody">
                    <tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Input/Edit --}}
    <form id="rm8aForm" action="{{ route('rme.igd.form.rm8a.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" id="rm8a_nopendaftaran" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="COUNTER" id="rm8a_counter">

        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title" id="rm8a-form-title">Input Data Baru</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group"><label>Hari</label><select name="HARI" id="rm8a_hari" class="form-control"></select></div>
                    <div class="col-md-4 form-group"><label>Tanggal</label><input type="date" class="form-control" name="TGL" id="rm8a_tgl"></div>
                    <div class="col-md-4 form-group"><label>Jam</label><input type="time" class="form-control" name="JAM" id="rm8a_jam"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 form-group"><label>Penerima Edukasi</label><select name="PENERIMA_EDUKASI" id="rm8a_penerima_edukasi" class="form-control"></select></div>
                    <div class="col-md-6 form-group"><label>Metode</label><select name="METODE" id="rm8a_metode" class="form-control"></select></div>
                    <div class="col-12 form-group"><label>Materi Edukasi</label><select name="MATERI_EDUKASI" id="rm8a_materi_edukasi" class="form-control"></select></div>
                    <div class="col-12 form-group"><label>Pendidikan Kesehatan</label><textarea name="ISI_PEND_KESEHATAN" id="rm8a_isi_pend_kesehatan" class="form-control" rows="4"></textarea></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 form-group"><label>Pemberi Edukasi (Jabatan)</label><select name="PEMBERI_EDUKASI" id="rm8a_pemberi_edukasi" class="form-control"></select></div>
                    <div class="col-md-6 form-group"><label>Evaluasi Respon</label><select name="EVALUASI_RESPON" id="rm8a_evaluasi_respon" class="form-control"></select></div>
                    <div class="col-md-6 form-group"><label>Nama Pemberi Edukasi</label><input type="text" class="form-control bg-light" name="NAMA_PEMBERI_EDUKASI" id="rm8a_nama_pemberi_edukasi" readonly></div>
                    <div class="col-md-6 form-group"><label>Nama Penerima Edukasi</label><input type="text" class="form-control" name="NAMA_PENERIMA_EDUKASI" id="rm8a_nama_penerima_edukasi" placeholder="Masukkan nama penerima"></div>
                </div>
                <hr>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 text-center">
                        <label class="d-block font-weight-bold mb-2">Tanda Tangan Penerima</label>
                        <div class="border rounded p-2" style="width: 100%; max-width: 400px; margin: auto;">
                            <canvas id="canvasTTD" style="width: 100%; height: 150px;"></canvas>
                        </div>
                        <input type="hidden" id="hiddenTTD" name="TTD_PENERIMA">
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clear-signature-btn">Hapus Tanda Tangan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" id="btn-save-rm8a"><i class="fas fa-save mr-1"></i> Simpan</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-reset-rm8a"><i class="fas fa-sync-alt mr-1"></i> Batal / Baru</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
    let signaturePad;

    // Opsi untuk dropdowns
    const hariOptions = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const penerimaOptions = { 'Pasien': 'Pasien', 'Keluarga': 'Keluarga', 'Lain-lain': 'Lain-lain' };
    const metodeOptions = { 'Ceramah': 'Ceramah', 'Demonstrasi': 'Demonstrasi', 'Audio visual': 'Audio visual', 'Media cetak': 'Media cetak', 'Lain-lain': 'Lain-lain' };
    const materiOptions = { 'Persetujuan Tindakan': 'Persetujuan Tindakan', 'Edukasi Kondisi Pasien': 'Edukasi Kondisi Pasien' };
    const pemberiOptions = { 'Dokter': 'Dokter', 'Perawat': 'Perawat', 'Bidan': 'Bidan', 'Farmasi': 'Farmasi', 'Gizi': 'Gizi', 'Laborat': 'Laborat', 'Lain-lain': 'Lain-lain' };
    const evaluasiOptions = { 'Tidak mengerti': 'Tidak mengerti', 'Mengerti': 'Mengerti', 'Mengerti Mengulang': 'Mengerti Mengulang', 'Mengerti Mengulang mendemonstrasikan': 'Mengerti Mengulang mendemonstrasikan' };

    function populateSelect(selector, options, selectedValue = '') {
        let html = '';
        if (Array.isArray(options)) {
            options.forEach(opt => {
                html += `<option value="${opt}">${opt}</option>`;
            });
        } else {
            for (const [key, value] of Object.entries(options)) {
                html += `<option value="${key}">${value}</option>`;
            }
        }
        $(selector).html(html).val(selectedValue);
    }

    function initSignaturePad(canvasId, hiddenInputId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
        });

        const hiddenInput = document.getElementById(hiddenInputId);

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Hapus canvas saat ukuran diubah
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        signaturePad.addEventListener("endStroke", () => {
            if (!signaturePad.isEmpty()) {
                // Buat canvas sementara untuk mengubah ukuran gambar
                const tempCanvas = document.createElement('canvas');
                const tempCtx = tempCanvas.getContext('2d');
                const targetWidth = 100;
                const targetHeight = 100;

                tempCanvas.width = targetWidth;
                tempCanvas.height = targetHeight;

                // Beri background putih agar tidak transparan saat disimpan sebagai JPEG
                tempCtx.fillStyle = "white";
                tempCtx.fillRect(0, 0, targetWidth, targetHeight);

                // Gambar konten dari canvas asli ke canvas sementara (proses resize)
                tempCtx.drawImage(signaturePad.canvas, 0, 0, targetWidth, targetHeight);

                // Ambil data URL dari canvas yang sudah di-resize sebagai JPEG
                hiddenInput.value = tempCanvas.toDataURL('image/jpeg', 0.75); // Kualitas 75% untuk file lebih kecil
            }
        });
    }

    $('#clear-signature-btn').on('click', function() {
        if (signaturePad) {
            signaturePad.clear();
            $('#hiddenTTD').val('');
        }
    });

    // Fungsi untuk auto-select jabatan berdasarkan kodegroup user
    function autoSelectJabatan() {
        const userAccess = ("{{ $user['access'] ?? '' }}").toUpperCase();
        const pemberiSelect = $('#rm8a_pemberi_edukasi');

        if (userAccess === 'DOKTER' || userAccess === 'DOKTER UMUM') {
            pemberiSelect.val('Dokter');
        } else if (userAccess === 'PERAWAT') {
            pemberiSelect.val('Perawat');
        } else if (userAccess === 'BIDAN') {
            pemberiSelect.val('Bidan');
        } else if (userAccess === 'FARMASI') {
            pemberiSelect.val('Farmasi');
        } else if (userAccess === 'GIZI') {
            pemberiSelect.val('Gizi');
        } else {
            pemberiSelect.val('Perawat'); // Default
        }
    }

    function resetRm8aForm() {
        $('#rm8aForm')[0].reset();
        $('#rm8a_counter').val('');
        $('#rm8a_tgl').val('{{ now()->format("Y-m-d") }}');
        $('#rm8a_jam').val('{{ now()->format("H:i") }}');
        $('#rm8a_nama_pemberi_edukasi').val('{{ $user["username"] ?? "" }}');
        
        // Set default values for selects
        $('#rm8a_hari').val(hariOptions[new Date().getDay()]);
        $('#rm8a_penerima_edukasi').val('Keluarga');
        $('#rm8a_metode').val('Ceramah');
        $('#rm8a_materi_edukasi').val('Edukasi Kondisi Pasien');
        autoSelectJabatan(); // Panggil fungsi untuk set jabatan otomatis
        $('#rm8a_evaluasi_respon').val('Mengerti');
        $('#rm8a_isi_pend_kesehatan').val("Edukasi pemasangan infus kepada pasien\nEdukasi tentang pelayanan rawat inap di rumah sakit\nEdukasi mengenai antrian pasien di IGD berdasarkan tingkat kegawatdaruratanya\nEdukasi mengenai cara pemberian terapi untuk pasien rawat inap\nEdukasi pasien bahwa membutuhkan perawatan lebih lanjut di rumah sakit");

        if (signaturePad) signaturePad.clear();
        $('#hiddenTTD').val('');

        $('#rm8a-form-title').text('Input Data Baru');
        $('#btn-save-rm8a').html('<i class="fas fa-save mr-1"></i> Simpan');
        $('#rm8aHistoryTable tbody tr').removeClass('table-info');

        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Form dibersihkan, mode input baru.', showConfirmButton: false, timer: 2000 });
    }

    function loadHistory() {
        const url = $('#rm8a_history_url').val();
        const noPendaftaran = $('#rm8a_nopendaftaran').val();

        $.get(url, { noPendaftaran: noPendaftaran }, function(response) {
            let html = '';
            if (response.status === 'success') {
                if (response.data.length > 0) {
                    response.data.forEach(item => {
                        const tgl = item.TGL ? new Date(item.TGL).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
                        const jam = item.JAM ? item.JAM.substring(0, 5) : '-';
                        html += `<tr data-counter="${item.COUNTER}" class="editable-row" title="Klik untuk edit">
                                    <td>${tgl} ${jam}</td>
                                    <td>${item.MATERI_EDUKASI || '-'}</td>
                                    <td>${item.NAMA_PEMBERI_EDUKASI || '-'}</td>
                                 </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="3" class="text-center">Tidak ada riwayat ditemukan.</td></tr>';
                }
            } else {
                html = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat riwayat.</td></tr>';
            }
            $('#rm8aHistoryBody').html(html);
            resetRm8aForm();
        }).fail(function() {
            $('#rm8aHistoryBody').html('<tr><td colspan="3" class="text-center text-danger">Gagal memuat riwayat.</td></tr>');
            resetRm8aForm();
        });
    }

    function loadDetailForEdit(counter) {
        const url = $('#rm8a_detail_url').val();
        const noPendaftaran = $('#rm8a_nopendaftaran').val();

        $.get(url, { noPendaftaran: noPendaftaran, counter: counter }, function(response) {
            // Pindahkan reset ke paling atas untuk memastikan state bersih
            resetRm8aForm();

            if (response.status === 'success') {
                const data = response.data;

                $('#rm8a_counter').val(data.COUNTER);
                $('#rm8a_tgl').val(data.TGL ? data.TGL.substring(0, 10) : '');
                $('#rm8a_jam').val(data.JAM ? data.JAM.substring(0, 5) : '');
                $('#rm8a_hari').val(data.HARI);
                $('#rm8a_penerima_edukasi').val(data.PENERIMA_EDUKASI);
                $('#rm8a_metode').val(data.METODE);
                $('#rm8a_materi_edukasi').val(data.MATERI_EDUKASI);
                $('#rm8a_isi_pend_kesehatan').val(data.ISI_PEND_KESEHATAN);
                $('#rm8a_pemberi_edukasi').val(data.PEMBERI_EDUKASI);
                $('#rm8a_evaluasi_respon').val(data.EVALUASI_RESPON);
                $('#rm8a_nama_pemberi_edukasi').val(data.NAMA_PEMBERI_EDUKASI);
                $('#rm8a_nama_penerima_edukasi').val(data.NAMA_PENERIMA_EDUKASI);

                // Logika untuk memuat TTD
                // Pastikan signaturePad ada sebelum digunakan
                if (!signaturePad) {
                    initSignaturePad('canvasTTD', 'hiddenTTD');
                }

                if (data.TTD_PENERIMA) {
                    const imageUrl = `{{ route('rme.igd.form.rm8a.showImage', [':noPendaftaran', ':counter']) }}`
                                        .replace(':noPendaftaran', noPendaftaran)
                                        .replace(':counter', counter) + `?v=${new Date().getTime()}`;

                    // Muat gambar ke signature pad
                    signaturePad.fromDataURL(imageUrl, {
                        ratio: 1,
                        width: signaturePad.canvas.offsetWidth,
                        height: signaturePad.canvas.offsetHeight
                    }).catch(() => {
                        console.error("Gagal memuat gambar tanda tangan dari URL:", imageUrl);
                    });
                }

                $('#rm8a-form-title').text('Edit Data (No. ' + data.COUNTER + ')');
                $('#btn-save-rm8a').html('<i class="fas fa-pencil-alt mr-1"></i> Update');
                $('#rm8aHistoryTable tbody tr').removeClass('table-info');
                $(`#rm8aHistoryTable tbody tr[data-counter="${counter}"]`).addClass('table-info');

                Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Mode Edit Aktif', showConfirmButton: false, timer: 2000 });
            } else {
                Swal.fire('Error', 'Gagal memuat detail data.', 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal memuat detail data.', 'error');
        });
    }

    // Tombol Batal
    $('#btn-reset-rm8a').on('click', function() {
        Swal.fire({
            title: 'Yakin ingin batal?',
            text: "Formulir akan dikosongkan dan disiapkan untuk input data baru.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                resetRm8aForm();
            }
        });
    });

    // Submit Form
    $('#rm8aForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        const button = $('#btn-save-rm8a');

        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                Swal.fire('Berhasil!', response.message, 'success').then(() => loadHistory());
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            },
            complete: function() {
                // Tombol akan diaktifkan kembali di sini.
                // Teks tombol akan diatur oleh fungsi resetRm8aForm yang dipanggil oleh loadHistory().
                button.prop('disabled', false);
            }
        });
    });

    // Klik riwayat untuk edit
    $('#rm8aHistoryBody').on('click', '.editable-row', function() {
        const counter = $(this).data('counter');
        loadDetailForEdit(counter);
        $('html, body').animate({ scrollTop: $('#rm8aForm').offset().top - 100 }, 500);
    });

    // Inisialisasi
    populateSelect('#rm8a_hari', hariOptions);
    populateSelect('#rm8a_penerima_edukasi', penerimaOptions);
    populateSelect('#rm8a_metode', metodeOptions);
    populateSelect('#rm8a_materi_edukasi', materiOptions);
    populateSelect('#rm8a_pemberi_edukasi', pemberiOptions);
    populateSelect('#rm8a_evaluasi_respon', evaluasiOptions);
    initSignaturePad('canvasTTD', 'hiddenTTD');
    loadHistory();
});
</script>