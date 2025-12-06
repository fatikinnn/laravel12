<div class="card-body">
    <form id="rm80Form" action="{{ route('rme.igd.form.rm80.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">

        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Berita Acara Serah Terima Bayi</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 form-group"><label>Hari</label><input type="text" class="form-control" name="HARI" value="{{ optional($data)->HARI ?? '' }}"></div>
                    <div class="col-md-3 form-group"><label>Tanggal</label><input type="date" class="form-control" name="TANGGAL" value="{{ optional($data)->TANGGAL ? \Carbon\Carbon::parse($data->TANGGAL)->format('Y-m-d') : now()->format('Y-m-d') }}"></div>
                    <div class="col-md-3 form-group"><label>Bulan</label><input type="text" class="form-control" name="BULAN" value="{{ optional($data)->BULAN ?? '' }}"></div>
                    <div class="col-md-3 form-group"><label>Tahun</label><input type="text" class="form-control" name="TAHUN" value="{{ optional($data)->TAHUN ?? now()->format('Y') }}"></div>
                    <div class="col-md-3 form-group"><label>Jam</label><input type="time" class="form-control" name="JAM" value="{{ optional($data)->JAM ? \Carbon\Carbon::parse(optional($data)->JAM)->format('H:i') : now()->format('H:i') }}"></div>
                </div>
                <hr>
                <p>Yang bertanda tangan di bawah ini:</p>
                <div class="row">
                    <div class="col-md-6 form-group"><label>Nama</label><input type="text" class="form-control" name="NM_BERTANDA" placeholder="Nama yang menyerahkan..." value="{{ optional($data)->NM_BERTANDA ?? ($user['username'] ?? '') }}"></div>
                    <div class="col-md-6 form-group"><label>Jabatan</label><input type="text" class="form-control" name="JAB_BERTANDA" placeholder="Jabatan yang menyerahkan..." value="{{ optional($data)->JAB_BERTANDA ?? '' }}"></div>
                </div>
                <p>Telah menyerahkan kepada:</p>
                <div class="row">
                    <div class="col-md-6 form-group"><label>Nama</label><input type="text" class="form-control" name="NM_PIHAK" placeholder="Nama penerima..." value="{{ optional($data)->NM_PIHAK ?? '' }}"></div>
                    <div class="col-md-6 form-group"><label>Jabatan</label><input type="text" class="form-control" name="JAB_PIHAK" placeholder="Jabatan penerima..." value="{{ optional($data)->JAB_PIHAK ?? '' }}"></div>
                </div>
                <hr>
                <p>Seorang bayi dengan data sebagai berikut:</p>
                <div class="row">
                    <div class="col-md-6 form-group"><label>Nama Bayi</label><input type="text" class="form-control" name="NM_BAYI" placeholder="Nama bayi..." value="{{ optional($data)->NM_BAYI ?? '' }}"></div>
                    <div class="col-md-6 form-group"><label>Tanggal Lahir Bayi</label><input type="date" class="form-control" name="TGLLAHIR_BAYI" value="{{ optional($data)->TGLLAHIR_BAYI ? \Carbon\Carbon::parse(optional($data)->TGLLAHIR_BAYI)->format('Y-m-d') : '' }}"></div>
                    <div class="col-md-4 form-group"><label>Berat Badan (gram)</label><input type="text" class="form-control" name="BB_BATI" placeholder="Contoh: 3000" value="{{ optional($data)->BB_BATI ?? '' }}"></div>
                    <div class="col-md-4 form-group"><label>Panjang Badan (cm)</label><input type="text" class="form-control" name="PANJANG_LAHIR" placeholder="Contoh: 50" value="{{ optional($data)->PANJANG_LAHIR ?? '' }}"></div>
                    <div class="col-md-4 form-group"><label>Surat Ket. Lahir</label><input type="text" class="form-control" name="SURAT_KETLAHIR" placeholder="Nomor surat..." value="{{ optional($data)->SURAT_KETLAHIR ?? '' }}"></div>
                    <div class="col-12 form-group"><label>Keadaan</label><textarea name="KEADAAN" class="form-control" rows="3">{{ optional($data)->KEADAAN ?? '' }}</textarea></div>
                </div>
                <hr>
                <div class="row justify-content-center">
                    <div class="col-md-6 text-center">
                        <label class="d-block font-weight-bold mb-2">Yang Menerima</label>
                        <div class="border rounded p-2" style="width: 100%; max-width: 400px; margin: auto;">
                            <canvas id="canvasPenerima" style="width: 100%; height: 150px;"></canvas>
                        </div>
                        <input type="hidden" id="hiddenPenerima" name="TTD_PENERIMA">
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 clear-signature-btn" data-target="Penerima">Hapus Tanda Tangan</button>
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="d-block font-weight-bold mb-2">Yang Menyerahkan</label>
                        <div class="border rounded p-2" style="width: 100%; max-width: 400px; margin: auto;">
                            <canvas id="canvasMenyerahkan" style="width: 100%; height: 150px;"></canvas>
                        </div>
                        <input type="hidden" id="hiddenMenyerahkan" name="TTD_MENYERAHKAN">
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 clear-signature-btn" data-target="Menyerahkan">Hapus Tanda Tangan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" id="btn-save-rm80"><i class="fas fa-save mr-1"></i> Simpan</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    let signaturePads = {};

    function initSignaturePad(canvasId, hiddenInputId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
        });

        const hiddenInput = document.getElementById(hiddenInputId);

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        // Initial resize and setup listener
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        signaturePad.addEventListener("endStroke", () => {
            if (!signaturePad.isEmpty()) {
                // Adopsi logika dari RM8A untuk resize dan konversi ke JPEG
                const tempCanvas = document.createElement('canvas');
                const tempCtx = tempCanvas.getContext('2d');
                const targetWidth = 100;
                const targetHeight = 100;

                tempCanvas.width = targetWidth;
                tempCanvas.height = targetHeight;

                // Beri background putih agar tidak transparan
                tempCtx.fillStyle = "white";
                tempCtx.fillRect(0, 0, targetWidth, targetHeight);

                // Gambar konten dari canvas asli ke canvas sementara
                tempCtx.drawImage(signaturePad.canvas, 0, 0, targetWidth, targetHeight);

                hiddenInput.value = tempCanvas.toDataURL('image/jpeg', 0.75); // Simpan sebagai JPEG
            }
        });

        return signaturePad;
    }

    // Initialize all signature pads
    signaturePads.Penerima = initSignaturePad('canvasPenerima', 'hiddenPenerima');
    signaturePads.Menyerahkan = initSignaturePad('canvasMenyerahkan', 'hiddenMenyerahkan');

    // Clear signature button
    $('.clear-signature-btn').on('click', function() {
        const target = $(this).data('target');
        if (signaturePads[target]) {
            signaturePads[target].clear();
            $(`#hidden${target}`).val('');
        }
    });

    // Load existing signatures
    function loadSignatures() {
        const noPendaftaran = '{{ $noPendaftaran }}';
        const timestamp = new Date().getTime();

        @if(optional($data)->TTD_PENERIMA)
            if (signaturePads.Penerima) {
                const imageUrl = `{{ route('rme.igd.form.rm80.showImage', [$noPendaftaran, 'TTD_PENERIMA']) }}?v=${timestamp}`;
                signaturePads.Penerima.fromDataURL(imageUrl, {
                    // Gunakan devicePixelRatio untuk konsistensi, sama seperti saat resize
                    ratio: Math.max(window.devicePixelRatio || 1, 1),
                    width: signaturePads.Penerima.canvas.offsetWidth,
                    height: signaturePads.Penerima.canvas.offsetHeight
                }).catch((e) => {
                    console.error(`Gagal memuat TTD Penerima:`, e);
                });
            }
        @endif

        @if(optional($data)->TTD_MENYERAHKAN)
            if (signaturePads.Menyerahkan) {
                const imageUrl = `{{ route('rme.igd.form.rm80.showImage', [$noPendaftaran, 'TTD_MENYERAHKAN']) }}?v=${timestamp}`;
                signaturePads.Menyerahkan.fromDataURL(imageUrl, {
                    // Gunakan devicePixelRatio untuk konsistensi
                    ratio: Math.max(window.devicePixelRatio || 1, 1),
                    width: signaturePads.Menyerahkan.canvas.offsetWidth,
                    height: signaturePads.Menyerahkan.canvas.offsetHeight
                }).catch((e) => {
                    console.error(`Gagal memuat TTD Menyerahkan:`, e);
                });
            }
        @endif
    }

    // Form submission
    $('#rm80Form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        const button = $('#btn-save-rm80');
        const originalButtonHtml = button.html();

        const formData = new FormData(form[0]);

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            processData: false, // Important!
            contentType: false, // Important!
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                Swal.fire('Berhasil!', response.message, 'success');
                // Optionally, you can reload the form or parts of it here
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            },
            complete: function() {
                button.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });

    // Initial load
    loadSignatures();

    // Auto-fill date parts
    $('input[name="TANGGAL"]').on('change', function() {
        const date = new Date($(this).val());
        if (!isNaN(date)) {
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            $('input[name="HARI"]').val(days[date.getDay()]);
            $('input[name="BULAN"]').val(months[date.getMonth()]);
            $('input[name="TAHUN"]').val(date.getFullYear());
        }
    }).trigger('change');

});
</script>