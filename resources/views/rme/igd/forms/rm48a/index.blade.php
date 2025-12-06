<div class="card-body">
    {{-- Hidden Inputs for AJAX --}}
    <input type="hidden" id="rm48a_nopendaftaran" value="{{ $noPendaftaran }}">
    <input type="hidden" id="rm48a_norm" value="{{ $norm }}">
    <input type="hidden" id="rm48a_history_url" value="{{ route('rme.igd.form.rm48a.history') }}">
    <input type="hidden" id="rm48a_detail_url" value="{{ route('rme.igd.form.rm48a.detail') }}">
    <input type="hidden" id="rm48a_image_url_base" value="{{ route('rme.igd.form.rm48a.showImage', [':noPendaftaran', ':counter', ':field']) }}">

    {{-- Riwayat Section --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="font-weight-bold mb-0"><i class="fas fa-history mr-2"></i>Riwayat Persetujuan/Penolakan</h5>
            <button type="button" id="btn-new-rm48a" class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i>Buat Baru
            </button>
        </div>
        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
            <table class="table table-sm table-bordered table-hover" id="rm48aHistoryTable">
                <thead class="thead-light sticky-top">
                    <tr>
                        <th>Tanggal & Jam</th>
                        <th>Status</th>
                        <th>Jenis Tindakan</th>
                        <th>Yang Menyatakan</th>
                        <th>Dokter</th>
                    </tr>
                </thead>
                <tbody id="rm48aHistoryBody">
                    <tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Section --}}
    <form id="rm48aForm" action="{{ route('rme.igd.form.rm48a.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="COUNTER" id="rm48a_counter">

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title" id="rm48a-form-title">Input Data Baru</h3>
            </div>
            <div class="card-body">
                {{-- Top Controls --}}
                <div class="row">
                    <div class="col-md-3 form-group"><label>Tanggal</label><input type="date" class="form-control" name="TANGGAL" id="rm48a_tanggal"></div>
                    <div class="col-md-3 form-group"><label>Jam</label><input type="time" class="form-control" name="JAM" id="rm48a_jam"></div>
                    <div class="col-md-3 form-group"><label>Status Persetujuan</label>
                        <select class="form-control" name="PERSETUJUAN_TINDAKAN" id="rm48a_persetujuan_tindakan">
                            <option value="">-- Pilih Status --</option>
                            <option value="Disetujui">Persetujuan</option>
                            <option value="Ditolak">Penolakan</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group"><label>Penanda Tangan</label>
                        <select class="form-control" name="TIPE_PENANDATANGAN" id="rm48a_tipe_penandatangan">
                            <option value="">-- Pilih --</option>
                            <option value="wali">Wali</option>
                            <option value="pasien">Pasien</option>
                        </select>
                    </div>
                </div>

                {{-- Main Form Content (Initially Hidden) --}}
                <div id="rm48a-main-content" style="display: none;">
                    <hr>
                    <div class="alert alert-info" id="rm48a-alert-disetujui" style="display: none;"><i class="fas fa-info-circle mr-2"></i>Silakan lengkapi form persetujuan tindakan medis.</div>
                    <div class="alert alert-warning" id="rm48a-alert-ditolak" style="display: none;"><i class="fas fa-exclamation-triangle mr-2"></i>Silakan lengkapi form penolakan tindakan medis.</div>

                    <div class="row">
                        {{-- Data Yang Bertanda Tangan --}}
                        <div class="col-lg-6">
                            <div class="card card-body shadow-sm mb-3">
                                <h6 class="font-weight-bold border-bottom pb-2 mb-3">Data Yang Bertanda Tangan</h6>
                                <div class="row">
                                    <div class="col-md-8 form-group"><label>Nama Lengkap</label><input type="text" class="form-control" name="YANG_BERTANDA_NAMA" id="rm48a_bertanda_nama"></div>
                                    <div class="col-md-4 form-group"><label>Usia</label><input type="text" class="form-control" name="YANG_BERTANDA_USIA" id="rm48a_bertanda_usia"></div>
                                    <div class="col-md-6 form-group"><label>Jenis Kelamin</label>
                                        <select class="form-control" name="YANG_BERTANDA_JK" id="rm48a_bertanda_jk">
                                            <option value="">-- Pilih --</option><option value="L">Laki-laki</option><option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group"><label>Alamat</label><input type="text" class="form-control" name="YANG_BERTANDA_ALAMAT" id="rm48a_bertanda_alamat"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Data Pasien --}}
                        <div class="col-lg-6">
                            <div class="card card-body bg-light shadow-sm mb-3">
                                <h6 class="font-weight-bold border-bottom pb-2 mb-3">Data Pasien</h6>
                                <div class="row">
                                    <div class="col-md-8 form-group"><label>Nama Pasien</label><input type="text" class="form-control" name="NAMA_PASIEN" id="rm48a_nama_pasien" value="{{ $patientDetails['Nama Pasien'] ?? '' }}" readonly></div>
                                    <div class="col-md-4 form-group"><label>Usia</label><input type="text" class="form-control" name="USIA_PASIEN" id="rm48a_usia_pasien" value="{{ $patientDetails['Usia'] ?? '' }}" readonly></div>
                                    <div class="col-md-6 form-group"><label>Jenis Kelamin</label>
                                        <select class="form-control" name="JK_PASIEN" id="rm48a_jk_pasien" readonly>
                                            <option value="L" {{ ($patientDetails['Gender'] ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option><option value="P" {{ ($patientDetails['Gender'] ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group"><label>Alamat</label><input type="text" class="form-control" name="ALAMAT_PASIEN" id="rm48a_alamat_pasien" value="{{ $patientDetails['Alamat'] ?? '' }}" readonly></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Data Tindakan & Pelaksanaan --}}
                    <div class="card card-body shadow-sm mb-3">
                        <h6 class="font-weight-bold border-bottom pb-2 mb-3">Data Tindakan & Pelaksanaan</h6>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>Jenis Tindakan</label><input type="text" class="form-control" name="TINDAKAN" id="rm48a_tindakan"></div>
                            <div class="col-md-4 form-group"><label>Terhadap</label>
                                <select class="form-control" name="TERHADAP" id="rm48a_terhadap">
                                    <option value="">-- Pilih --</option>
                                    <option value="Istri saya">Istri saya</option><option value="Suami saya">Suami saya</option><option value="Kakak/Adik saya">Kakak/Adik saya</option>
                                    <option value="Ayah/Ibu saya">Ayah/Ibu saya</option><option value="Saudara saya">Saudara saya</option><option value="Anak saya">Anak saya</option><option value="Saya sendiri">Saya sendiri</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group"><label>Dokter</label>
                                <select class="form-control" name="DOKTER" id="rm48a_dokter">
                                    <option value="">-- Pilih Dokter --</option>
                                    @foreach($dokterList as $dokter)
                                        <option value="{{ $dokter->NAMAPEMERIKSA }}">{{ $dokter->NAMAPEMERIKSA }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Signature Section --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-body shadow-sm text-center">
                                <label class="font-weight-bold">Saksi</label>
                                <input type="text" class="form-control mb-2" name="NAMA_SAKSI_1" id="rm48a_nama_saksi_1" placeholder="Nama Saksi">
                                <div class="signature-pad-container border rounded p-1 bg-white" style="max-width: 400px; margin: 0 auto 8px auto;"><canvas id="canvasSaksi1"></canvas></div>
                                <input type="hidden" id="hiddenSaksi1" name="SAKSI_1">
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger clear-signature-btn" data-target="Saksi1" title="Hapus Tanda Tangan">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-body shadow-sm text-center">
                                <label class="font-weight-bold">Yang Menyatakan</label>
                                <input type="text" class="form-control mb-2" name="YANGMENYATAKAN" id="rm48a_yangmenyatakan" placeholder="Nama Yang Menyatakan">
                                <div class="signature-pad-container border rounded p-1 bg-white" style="max-width: 400px; margin: auto;"><canvas id="canvasYangMenyatakan"></canvas></div>
                                <input type="hidden" id="hiddenYangMenyatakan" name="TTD_YANGMENYATAKAN">
                                <div class="d-flex justify-content-center mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger clear-signature-btn" data-target="YangMenyatakan" title="Hapus Tanda Tangan">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" id="btn-save-rm48a"><i class="fas fa-save mr-1"></i> Simpan</button>
            <button type="button" class="btn btn-outline-secondary" id="btn-reset-rm48a"><i class="fas fa-sync-alt mr-1"></i> Batal / Baru</button>
        </div>
    </form>
</div>

{{-- Style khusus untuk canvas agar responsive --}}
<style>
    .signature-pad-container {
        position: relative;
        width: 100%;
        height: 150px; /* Tinggi default */
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .signature-pad-container canvas {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
    }
</style>

<script>
$(document).ready(function() {
    let signaturePads = {};
    let currentEditingCounter = null;
    let resizeTimeout;

    // Data dari PHP - dengan escape yang proper
    const patientData = {
        nama: `{{ $patientDetails['Nama Pasien'] ?? '' }}`,
        usia: `{{ $patientDetails['Usia'] ?? '' }}`,
        gender: `{{ $patientDetails['Gender'] ?? '' }}`,
        alamat: `{{ $patientDetails['Alamat'] ?? '' }}`
    };
    
    const userData = {
        username: `{{ $user['username'] ?? '' }}`
    };

    // Inisialisasi signature pads
    function initializeSignaturePads() {
        try {
            // Clear existing pads
            signaturePads = {};

            // Saksi 1
            const canvasSaksi1 = document.getElementById('canvasSaksi1');
            if (canvasSaksi1) {
                signaturePads.Saksi1 = new SignaturePad(canvasSaksi1, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 1,
                    maxWidth: 3,
                    throttle: 16
                });
                
                signaturePads.Saksi1.addEventListener('endStroke', () => {
                     if (!signaturePads.Saksi1.isEmpty()) {
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
                        tempCtx.drawImage(signaturePads.Saksi1.canvas, 0, 0, targetWidth, targetHeight);

                        // Ambil data URL dari canvas yang sudah di-resize sebagai JPEG
                        $('#hiddenSaksi1').val(tempCanvas.toDataURL('image/jpeg', 0.75));
                     }
                });
            }

            // Yang Menyatakan
            const canvasYangMenyatakan = document.getElementById('canvasYangMenyatakan');
            if (canvasYangMenyatakan) {
                signaturePads.YangMenyatakan = new SignaturePad(canvasYangMenyatakan, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 1,
                    maxWidth: 3,
                    throttle: 16
                });
                
                signaturePads.YangMenyatakan.addEventListener('endStroke', () => {
                     if (!signaturePads.YangMenyatakan.isEmpty()) {
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
                        tempCtx.drawImage(signaturePads.YangMenyatakan.canvas, 0, 0, targetWidth, targetHeight);

                        // Ambil data URL dari canvas yang sudah di-resize sebagai JPEG
                        $('#hiddenYangMenyatakan').val(tempCanvas.toDataURL('image/jpeg', 0.75));
                     }
                });
            }

            // Initial resize
            resizeSignaturePads();
        } catch (error) {
            console.error('Error initializing signature pads:', error);
        }
    }

    // Resize signature pads dengan preservasi data
    function resizeSignaturePads() {
        Object.values(signaturePads).forEach(pad => {
            if (pad && pad.canvas) {
                const canvas = pad.canvas;
                
                // Simpan data tanda tangan yang ada
                const signatureData = pad.toData();
                const isEmpty = pad.isEmpty();
                
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const canvasWidth = canvas.offsetWidth;
                const canvasHeight = canvas.offsetHeight;
                
                // Set ukuran canvas
                canvas.width = canvasWidth * ratio;
                canvas.height = canvasHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                
                // Clear canvas
                pad.clear();
                
                // Gambar kembali tanda tangan jika ada
                if (!isEmpty && signatureData && signatureData.length > 0) {
                    try {
                        pad.fromData(signatureData);
                    } catch (error) {
                        console.error('Error redrawing signature:', error);
                    }
                }
            }
        });
    }

    // Optimized resize dengan debouncing
    function debouncedResize() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            resizeSignaturePads();
        }, 250);
    }

    // Clear specific signature pad
    function clearSignaturePad(target) {
        if (signaturePads[target]) {
            signaturePads[target].clear();
            $(`#hidden${target}`).val('');
        }
    }

    // Reset form ke state awal
    function resetRm48aForm() {
        $('#rm48aForm')[0].reset();
        $('#rm48a_counter').val('');
        currentEditingCounter = null;
        
        // Set default values
        $('#rm48a_tanggal').val('{{ now()->format('Y-m-d') }}');
        $('#rm48a_jam').val('{{ now()->format("H:i") }}');
        $('#rm48a_nama_saksi_1').val(userData.username);

        // Clear signature pads
        Object.keys(signaturePads).forEach(key => {
            clearSignaturePad(key);
        });

        // Reset UI state
        $('#rm48a-main-content').hide();
        $('#rm48a-alert-disetujui, #rm48a-alert-ditolak').hide();
        $('#rm48a-form-title').text('Input Data Baru');
        $('#btn-save-rm48a').html('<i class="fas fa-save mr-1"></i> Simpan');
        $('#rm48aHistoryTable tbody tr').removeClass('table-info');

        // Re-initialize signature pads untuk memastikan ukuran sesuai
        setTimeout(() => {
            resizeSignaturePads();
        }, 300);
    }

    // Load history data
    function loadHistory() {
        const url = $('#rm48a_history_url').val();
        const noPendaftaran = $('#rm48a_nopendaftaran').val();

        $('#rm48aHistoryBody').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat...</td></tr>');

        $.get(url, { noPendaftaran: noPendaftaran })
            .done(function(response) {
                let html = '';
                if (response.status === 'success' && response.data.length > 0) {
                    response.data.forEach(item => {
                        const tgl = item.TANGGAL ? new Date(item.TANGGAL).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
                        const jam = item.JAM ? item.JAM.substring(0, 5) : '-';
                        const statusClass = item.PERSETUJUAN_TINDAKAN === 'Disetujui' ? 'badge-success' : 'badge-danger';
                        const isActive = currentEditingCounter === item.COUNTER ? 'table-info' : '';
                        
                        html += `<tr data-counter="${item.COUNTER}" class="editable-row ${isActive}" style="cursor: pointer;" title="Klik untuk edit">
                                    <td>${tgl} ${jam}</td>
                                    <td><span class="badge ${statusClass}">${item.PERSETUJUAN_TINDAKAN || '-'}</span></td>
                                    <td>${item.TINDAKAN || '-'}</td>
                                    <td>${item.YANGMENYATAKAN || '-'}</td>
                                    <td>${item.DOKTER || '-'}</td>
                                 </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center">Tidak ada riwayat ditemukan.</td></tr>';
                }
                $('#rm48aHistoryBody').html(html);
            })
            .fail(function() {
                $('#rm48aHistoryBody').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat riwayat.</td></tr>');
            });
    }

    // Load detail untuk edit
    function loadDetailForEdit(counter) {
        const url = $('#rm48a_detail_url').val();
        const noPendaftaran = $('#rm48a_nopendaftaran').val();

        Swal.fire({
            title: 'Memuat data...',
            html: 'Mohon tunggu sejenak.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.get(url, { noPendaftaran: noPendaftaran, counter: counter })
            .done(function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    currentEditingCounter = counter;
                    
                    // Isi form dengan data
                    $('#rm48a_counter').val(data.COUNTER);
                    $('#rm48a_tanggal').val(data.TANGGAL ? data.TANGGAL.substring(0, 10) : '');
                    $('#rm48a_jam').val(data.JAM ? data.JAM.substring(0, 5) : '');
                    $('#rm48a_persetujuan_tindakan').val(data.PERSETUJUAN_TINDAKAN || '');
                    $('#rm48a_tipe_penandatangan').val(data.TIPE_PENANDATANGAN || '');
                    
                    $('#rm48a_bertanda_nama').val(data.YANG_BERTANDA_NAMA || '');
                    $('#rm48a_bertanda_usia').val(data.YANG_BERTANDA_USIA || '');
                    $('#rm48a_bertanda_jk').val(data.YANG_BERTANDA_JK || '');
                    $('#rm48a_bertanda_alamat').val(data.YANG_BERTANDA_ALAMAT || '');
                    $('#rm48a_tindakan').val(data.TINDAKAN || '');
                    $('#rm48a_terhadap').val(data.TERHADAP || '');
                    $('#rm48a_dokter').val(data.DOKTER || '');
                    $('#rm48a_nama_saksi_1').val(data.NAMA_SAKSI_1 || userData.username);
                    $('#rm48a_yangmenyatakan').val(data.YANGMENYATAKAN || '');

                    // Langsung muat TTD dari data base64 yang diterima
                    loadSignaturesFromBase64(data);

                    // Update UI
                    $('#rm48a-form-title').text('Edit Data (No. ' + data.COUNTER + ')');
                    $('#btn-save-rm48a').html('<i class="fas fa-pencil-alt mr-1"></i> Update');

                    // Highlight the selected row
                    $('#rm48aHistoryTable tbody tr').removeClass('table-info');
                    $(`#rm48aHistoryTable tbody tr[data-counter="${counter}"]`).addClass('table-info');
                    
                    // Tampilkan/hide form content berdasarkan status
                    toggleFormContent(data.PERSETUJUAN_TINDAKAN);
                    
                    Swal.fire({ 
                        toast: true, 
                        position: 'top-end', 
                        icon: 'info', 
                        title: 'Mode Edit Aktif', 
                        showConfirmButton: false, 
                        timer: 2000 
                    });
                } else {
                    Swal.fire('Error', 'Gagal memuat detail data.', 'error');
                }
            })
            .fail(function() {
                Swal.fire('Error', 'Gagal memuat detail data.', 'error');
            })
            .always(function() {
                Swal.close(); // Selalu tutup loading dialog setelah selesai
            });
    }

    // Toggle form content berdasarkan status
    function toggleFormContent(status) {
        $('#rm48a-alert-disetujui, #rm48a-alert-ditolak').hide();
        
        if (status === 'Disetujui' || status === 'Ditolak') {
            if (status === 'Disetujui') {
                $('#rm48a-alert-disetujui').show();
            } else {
                $('#rm48a-alert-ditolak').show();
            }
            $('#rm48a-main-content').slideDown(400, () => {
                // Resize signature pads setelah animasi selesai
                resizeSignaturePads(); // Resize tetap di sini untuk memastikan canvas visible
            });
        } else {
            $('#rm48a-main-content').slideUp();
        }
    }

    // Fungsi baru untuk memuat TTD dari data base64
    function loadSignaturesFromBase64(data) {
        const loadPad = (padKey, base64Data) => {
            if (signaturePads[padKey]) {
                clearSignaturePad(padKey); // Bersihkan dulu
                if (base64Data) {
                    signaturePads[padKey].fromDataURL(base64Data, {
                        ratio: 1,
                        width: signaturePads[padKey].canvas.offsetWidth,
                        height: signaturePads[padKey].canvas.offsetHeight
                    }).then(() => {
                        // Update hidden input jika diperlukan
                        const resizedDataUrl = signaturePads[padKey].toDataURL('image/jpeg', 0.75);
                        $(`#hidden${padKey}`).val(resizedDataUrl);
                    }).catch(e => console.error(`Gagal memuat TTD untuk ${padKey}:`, e));
                }
            }
        }

        loadPad('Saksi1', data.SAKSI_1_BASE64);
        loadPad('YangMenyatakan', data.TTD_YANGMENYATAKAN_BASE64);
    }

    // Event Listeners
    $(document).on('click', '#btn-new-rm48a, #btn-reset-rm48a', function() {
        resetRm48aForm();
    });

    $(document).on('change', '#rm48a_persetujuan_tindakan', function() {
        const value = $(this).val();
        toggleFormContent(value);
    });

    $(document).on('change', '#rm48a_tipe_penandatangan', function() {
        if ($(this).val() === 'pasien') {
            $('#rm48a_bertanda_nama').val(patientData.nama);
            $('#rm48a_bertanda_usia').val(patientData.usia);
            $('#rm48a_bertanda_jk').val(patientData.gender);
            $('#rm48a_bertanda_alamat').val(patientData.alamat);
            $('#rm48a_yangmenyatakan').val(patientData.nama);
            $('#rm48a_terhadap').val('Saya sendiri');
        } else {
            $('#rm48a_bertanda_nama, #rm48a_bertanda_usia, #rm48a_bertanda_jk, #rm48a_bertanda_alamat, #rm48a_yangmenyatakan').val('');
        }
    });

    $(document).off('click', '.editable-row').on('click', '.editable-row', function() {
        const counter = $(this).data('counter');
        loadDetailForEdit(counter);
        $('html, body').animate({ scrollTop: $('#rm48aForm').offset().top - 100 }, 500);
    });

    $(document).on('click', '.clear-signature-btn', function() {
        const target = $(this).data('target');
        clearSignaturePad(target);
    });

    $('#rm48aForm').on('submit', function(e) {
        e.preventDefault();
        
        const status = $('#rm48a_persetujuan_tindakan').val();
        if (!status) {
            Swal.fire('Error!', 'Pilih status persetujuan terlebih dahulu.', 'error');
            return;
        }

        const form = $(this);
        const url = form.attr('action');
        const button = $('#btn-save-rm48a');
        const originalButtonHtml = button.html();

        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                Swal.fire('Berhasil!', response.message, 'success').then(() => {
                    loadHistory();
                    resetRm48aForm();
                });
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    errorMsg = 'Data yang dimasukkan tidak valid.';
                }
                Swal.fire('Error!', errorMsg, 'error');
            },
            complete: function() {
                button.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });

    // Handle window resize dengan debouncing
    $(window).on('resize', function() {
        debouncedResize();
    });

    // Juga handle saat container form di-show/hide
    $(document).on('shown.bs.collapse hidden.bs.collapse', function() {
        debouncedResize();
    });

    // Initial Load
    initializeSignaturePads();
    loadHistory();
    resetRm48aForm();
});
</script>