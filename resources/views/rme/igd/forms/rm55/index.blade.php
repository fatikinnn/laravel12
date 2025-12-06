<div class="card-body">
    <form id="rm55Form" action="{{ route('rme.igd.form.rm55.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" id="rm55_nopendaftaran" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">

        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Surat Keterangan Lahir</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="rm55_nm_ibu">Nama Ibu</label>
                        <input type="text" class="form-control" id="rm55_nm_ibu" name="NM_IBU" value="{{ $data->NM_IBU ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="rm55_nm_ayah">Nama Ayah</label>
                        <input type="text" class="form-control" id="rm55_nm_ayah" name="NM_AYAH" value="{{ $data->NM_AYAH ?? '' }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="rm55_tgljamlahir">Tanggal & Jam Lahir</label>
                        <input type="datetime-local" class="form-control" id="rm55_tgljamlahir" name="TGLJAMLAHIR" value="{{ isset($data->TGLJAMLAHIR) ? \Carbon\Carbon::parse($data->TGLJAMLAHIR)->format('Y-m-d\TH:i') : '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="rm55_jenkel">Jenis Kelamin</label>
                        <select class="form-control" id="rm55_jenkel" name="JENKEL">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ ($data->JENKEL ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ ($data->JENKEL ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="rm55_bb">Berat Badan (gram)</label>
                        <input type="text" class="form-control" id="rm55_bb" name="BB" placeholder="Contoh: 3000" value="{{ $data->BB ?? '' }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="rm55_pb">Panjang Badan (cm)</label>
                        <input type="text" class="form-control" id="rm55_pb" name="PB" placeholder="Contoh: 50" value="{{ $data->PB ?? '' }}">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="rm55_nopemeriksa">DPJP</label>
                        <select class="form-control" id="rm55_nopemeriksa" name="NOPEMERIKSA" style="width: 100%;">
                            <option value="">-- Pilih DPJP --</option>
                            @foreach($dokterList as $dokter)
                                <option value="{{ trim($dokter->NOPEMERIKSA) }}" data-nama="{{ trim($dokter->NAMAPEMERIKSA) }}"
                                    {{ (trim($selectedNopemeriksa) == trim($dokter->NOPEMERIKSA)) ? 'selected' : '' }}>{{ trim($dokter->NAMAPEMERIKSA) }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="dpjp_text" id="rm55_dpjp_text">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="rm55_petugas_rnifas">Petugas Ruang Nifas</label>
                        <input type="text" class="form-control" id="rm55_petugas_rnifas" name="PETUGAS_RNIFAS" value="{{ $data->PETUGAS_RNIFAS ?? ($user['username'] ?? '') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="rm55_nm_penerima">Ibu/Keluarga</label>
                        <input type="text" class="form-control" id="rm55_nm_penerima" name="NM_PENERIMA" placeholder="Masukkan nama penerima" value="{{ $data->NM_PENERIMA ?? '' }}">
                    </div>
                </div>
                <hr>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 text-center">
                        <label class="d-block font-weight-bold mb-2">Tanda Tangan Penerima</label>
                        <div class="border rounded p-2" style="width: 100%; max-width: 400px; margin: auto;">
                            <canvas id="canvasTTDRm55" style="width: 100%; height: 150px;"></canvas>
                        </div>
                        <input type="hidden" id="hiddenTTDRm55" name="TTD_PENERIMA">
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clear-signature-btn-rm55">Hapus Tanda Tangan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" id="btn-save-rm55"><i class="fas fa-save mr-1"></i> Simpan</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-reset-rm55"><i class="fas fa-sync-alt mr-1"></i> Reset Form</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        let signaturePadRm55;
        const noPendaftaran = $('#rm55_nopendaftaran').val();

        // Inisialisasi Select2 untuk DPJP
        $('#rm55_nopemeriksa').select2({
            placeholder: 'Pilih DPJP',
        }).on('select2:select', function (e) {
            // Simpan nama dokter ke input tersembunyi saat dipilih
            var selectedOption = $(this).find('option:selected');
            $('#rm55_dpjp_text').val(selectedOption.data('nama'));
        });

        // Trigger event select untuk mengisi hidden input saat form pertama kali dimuat
        if ($('#rm55_nopemeriksa').val()) {
            $('#rm55_nopemeriksa').trigger('select2:select');
        }

        function initSignaturePadRm55() {
            const canvas = document.getElementById('canvasTTDRm55');
            if (!canvas) return;

            signaturePadRm55 = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
            });

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePadRm55.clear();
            }
            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            signaturePadRm55.addEventListener("endStroke", () => {
                if (!signaturePadRm55.isEmpty()) {
                    const tempCanvas = document.createElement('canvas');
                    const tempCtx = tempCanvas.getContext('2d');
                    tempCanvas.width = 100;
                    tempCanvas.height = 100;
                    tempCtx.fillStyle = "white";
                    tempCtx.fillRect(0, 0, 100, 100);
                    tempCtx.drawImage(signaturePadRm55.canvas, 0, 0, 100, 100);
                    $('#hiddenTTDRm55').val(tempCanvas.toDataURL('image/jpeg', 0.75));
                }
            });
        }

        $('#clear-signature-btn-rm55').on('click', function() {
            if (signaturePadRm55) {
                signaturePadRm55.clear();
                $('#hiddenTTDRm55').val('');
            }
        });

        $('#btn-reset-rm55').on('click', function() {
            $('#rm55Form')[0].reset();
            $('#rm55_nopemeriksa').val(null).trigger('change'); // Reset select2
            $('#rm55_dpjp_text').val('');
            $('#rm55_petugas_rnifas').val("{{ $user['username'] ?? '' }}");
            if (signaturePadRm55) signaturePadRm55.clear();
            $('#hiddenTTDRm55').val('');
            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Form dibersihkan.', showConfirmButton: false, timer: 1500 });
        });

        $('#rm55Form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            const button = $('#btn-save-rm55');

            // Gunakan FormData untuk mengirim data, ini penting untuk TTD base64
            const formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                processData: false, // Wajib false untuk FormData
                contentType: false, // Wajib false untuk FormData
                beforeSend: function() {
                    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success');
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMsg, 'error');
                },
                complete: function() {
                    button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan');
                }
            });
        });

        // Inisialisasi
        initSignaturePadRm55();

        // Load TTD yang sudah ada jika ada
        @if($data && $data->TTD_PENERIMA)
            // Menunggu sebentar agar canvas siap
            setTimeout(function() {
                const imageUrl = `{{ route('rme.igd.form.rm55.showImage', [$noPendaftaran]) }}?v=${new Date().getTime()}`;
                signaturePadRm55.fromDataURL(imageUrl, {
                    ratio: 1,
                    width: signaturePadRm55.canvas.offsetWidth,
                    height: signaturePadRm55.canvas.offsetHeight
                }).catch(() => {
                    console.error("Gagal memuat gambar tanda tangan yang ada.");
                });
            }, 500);
        @endif
    });
</script>