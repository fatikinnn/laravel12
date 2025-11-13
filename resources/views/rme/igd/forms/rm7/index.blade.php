<div class="card-body">
    {{-- Hidden fields for URLs and data --}}
    <input type="hidden" id="rm7-no-pendaftaran" value="{{ $noPendaftaran }}">
    <input type="hidden" id="rm7-norm" value="{{ $norm }}">
    <input type="hidden" id="rm7-user" value="{{ trim($user['username']) }}">
    <input type="hidden" id="rm7-dpjp" value="{{ $patientDetails['DPJP'] ?? '' }}">
    <input type="hidden" id="rm7-diagnosa" value="{{ trim($patientDetails['DIAGNOSIS_UTAMA'] ?? '') }}">
    <input type="hidden" id="rm7-url-history" value="{{ route('rme.igd.form.rm7.history') }}">
    <input type="hidden" id="rm7-url-detail" value="{{ route('rme.igd.form.rm7.detail') }}">
    <input type="hidden" id="rm7-url-store" value="{{ route('rme.igd.form.rm7.store') }}">

    {{-- Riwayat Transfer --}}
    <div class="card card-outline card-info shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title mt-1">Riwayat Transfer Pasien</h3>
            <div class="card-tools d-flex align-items-center">
                <button type="button" class="btn btn-info btn-sm mr-2" data-toggle="modal" data-target="#rm7PanduanModal">
                    <i class="fas fa-question-circle mr-1"></i>Panduan
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-new-transfer-rm7">
                    <i class="fas fa-plus-circle mr-1"></i>Tambah Transfer Baru
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0" id="history-table-rm7">
                    <thead class="thead-light text-center">
                        <tr>
                            <th class="text-center" style="width: 10%;">No.</th>
                            <th class="text-left">Asal & Tujuan</th>
                            <th class="text-left d-none d-md-table-cell">Diserahkan Oleh</th>
                            <th class="text-left d-none d-md-table-cell">Diterima Oleh</th>
                            <th class="text-center" style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="history-body-rm7">
                        {{-- Data dimuat oleh AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Form Utama --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mt-1" id="form-title-rm7">Buat Transfer Internal Baru</h3>
        </div>
        <form id="form-rm7" class="card-body">
            @csrf
            <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
            <input type="hidden" name="NORM" value="{{ $norm }}">
            <input type="hidden" name="NOTRANSFER" id="form-notransfer-rm7">

            <!-- ASAL & TUJUAN -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Asal & Tujuan Transfer</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asal-ruangan-rm7">Asal Pasien</label>
                                <select class="form-control" id="asal-ruangan-rm7" name="ASAL_PASIEN_RUANGAN_TEXT" required>
                                    <option value="">-- Pilih Ruangan Asal --</option>
                                    <option value="PONEK">PONEK</option>
                                    <option value="Rawat Jalan">Rawat Jalan</option>
                                    @foreach ($ruangan as $ruang)
                                        <option value="{{ trim($ruang->NAMARUANG) }}">{{ trim($ruang->NAMAKELAS) }} - {{ trim($ruang->NAMARUANG) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pindah-ruangan-rm7">Pindah Ke</label>
                                <select class="form-control" id="pindah-ruangan-rm7" name="PINDAH_KE_RUANG_TEXT">
                                    <option value="">-- Pilih Ruangan Tujuan --</option>
                                    <option value="IBS">IBS</option>
                                    <option value="Rawat Jalan">Rawat Jalan (Pulang)</option>
                                    @foreach ($ruangan as $ruang)
                                        <option value="{{ trim($ruang->NAMARUANG) }}">{{ trim($ruang->NAMAKELAS) }} - {{ trim($ruang->NAMARUANG) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INFORMASI MEDIS -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Medis</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="dpjp-rm7">DPJP</label>
                            <select class="form-control" id="dpjp-rm7" name="DPJP" style="width: 100%;">
                                <option value="">-- Pilih DPJP --</option>
                                @foreach ($dokterList as $dokter)
                                    <option value="{{ trim($dokter->NAMAPEMERIKSA) }}">
                                        {{ trim($dokter->NAMAPEMERIKSA) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="diagnosa-rm7">Diagnosa Sementara</label>
                            <textarea class="form-control" id="diagnosa-rm7" name="DIAGNOSA_SEMENTARA" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VITAL SIGNS -->
            <div class="card-deck mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">Vital Sign (Saat Transfer)</h6>
                        <small class="d-block text-muted">Diisi oleh petugas yang mengirim</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Tekanan Darah</label><div class="input-group"><input type="text" class="form-control" name="TRANSFER_TD"><div class="input-group-append"><span class="input-group-text">mmHg</span></div></div></div>
                            <div class="col-md-6 form-group"><label>Nadi</label><div class="input-group"><input type="text" class="form-control" name="TRANSFER_NADI"><div class="input-group-append"><span class="input-group-text">x/mnt</span></div></div></div>
                            <div class="col-md-6 form-group"><label>Suhu</label><div class="input-group"><input type="text" class="form-control" name="TRANSFER_SUHU"><div class="input-group-append"><span class="input-group-text">°C</span></div></div></div>
                            <div class="col-md-6 form-group"><label>Pernafasan</label><div class="input-group"><input type="text" class="form-control" name="TRANSFER_PERNAFASAN"><div class="input-group-append"><span class="input-group-text">x/mnt</span></div></div></div>
                            <div class="col-md-6 form-group"><label>Kesadaran</label><input type="text" class="form-control" name="TRANSFER_KESADARAN"></div>
                            <div class="col-md-6 form-group"><label>GCS (E/M/V)</label><div class="input-group"><input type="text" class="form-control" name="TRANSFER_GCS_E" placeholder="E"><input type="text" class="form-control" name="TRANSFER_GCS_M" placeholder="M"><input type="text" class="form-control" name="TRANSFER_GCS_V" placeholder="V"></div></div>
                        </div>
                    </div>
                </div>
                <div class="card card-receiver">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">Vital Sign (Saat Diterima)</h6>
                        <small class="d-block text-muted">Diisi oleh petugas yang menerima</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Tekanan Darah</label><div class="input-group"><input type="text" class="form-control" name="DITERIMA_TD"><div class="input-group-append"><span class="input-group-text">mmHg</span></div></div></div>
                            <div class="col-md-6 form-group"><label>Nadi</label><div class="input-group"><input type="text" class="form-control" name="DITERIMA_NADI"><div class="input-group-append"><span class="input-group-text">x/mnt</span></div></div></div>
                            <div class="col-md-6 form-group"><label>Suhu</label><div class="input-group"><input type="text" class="form-control" name="DITERIMA_SUHU"><div class="input-group-append"><span class="input-group-text">°C</span></div></div></div>
                            <div class="col-md-6 form-group"><label>Pernafasan</label><div class="input-group"><input type="text" class="form-control" name="DITERIMA_PERNAFASAN"><div class="input-group-append"><span class="input-group-text">x/mnt</span></div></div></div>
                            <div class="col-md-6 form-group"><label>Kesadaran</label><input type="text" class="form-control" name="DITERIMA_KESADARAN"></div>
                            <div class="col-md-6 form-group"><label>GCS (E/M/V)</label><div class="input-group"><input type="text" class="form-control" name="DITERIMA_GCS_E" placeholder="E"><input type="text" class="form-control" name="DITERIMA_GCS_M" placeholder="M"><input type="text" class="form-control" name="DITERIMA_GCS_V" placeholder="V"></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NYERI & ALERGI -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nyeri</label>
                            <div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="NYERI_GROUP" id="nyeri-tidak-rm7" value="0"><label class="form-check-label" for="nyeri-tidak-rm7">Tidak</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="NYERI_GROUP" id="nyeri-ya-rm7" value="1"><label class="form-check-label" for="nyeri-ya-rm7">Ya</label></div>
                            </div>
                            <div id="skala-nyeri-container-rm7" class="mt-2" style="display: none;">
                                <label for="skala-nyeri-rm7">Skala Nyeri</label>
                                <input type="number" class="form-control" id="skala-nyeri-rm7" name="SKALA_NYERI" min="1" max="10" style="max-width: 150px;">
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="obat-alergi-rm7">Obat Yang Menimbulkan Alergi</label>
                            <input type="text" class="form-control" id="obat-alergi-rm7" name="OBAT_ALERGI">
                        </div>
                    </div>
                </div>
            </div>

            <!-- OBAT-OBATAN -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Obat-obatan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 font-weight-bold">Obat Yang Dibawa Dari Rumah</h6>
                        <button type="button" class="btn btn-sm btn-success" id="btn-add-obat-rumah-rm7"><i class="fas fa-plus mr-1"></i>Tambah</button>
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered" id="table-obat-rumah-rm7" style="min-width: 1200px;">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th style="width: 12%;">Tgl Dibawa</th>
                                    <th style="width: 20%;">Nama Obat</th>
                                    <th style="width: 10%;">Dosis</th>
                                    <th style="width: 10%;">Jumlah</th>
                                    <th style="width: 15%;">Alasan Minum</th>
                                    <th style="width: 10%;">Lanjut Ranap?</th>
                                    <th style="width: 15%;">Telaah Obat</th>
                                    <th style="width: 8%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="body-obat-rumah-rm7"></tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 font-weight-bold">Obat Yang Dibawa Ke Unit</h6>
                        <button type="button" class="btn btn-sm btn-success" id="btn-add-obat-unit-rm7"><i class="fas fa-plus mr-1"></i>Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table-obat-unit-rm7">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th style="width: 40%;">Nama Obat</th>
                                    <th style="width: 25%;">Jumlah</th>
                                    <th style="width: 25%;">Dosis</th>
                                    <th style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="body-obat-unit-rm7"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- BARANG/DOKUMEN -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Barang/Dokumen Yang Diserahkan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach (['RM_RANAP' => 'RM Ranap', 'RM_RAJAL' => 'RM Rajal', 'TREADMILL' => 'Treadmill', 'ENDOSKOPI' => 'Endoskopi', 'HASIL_RAD' => 'Hasil Radiologi', 'HASIL_LAB' => 'Hasil Lab', 'EKG' => 'EKG', 'ECHOCARDIOGRAPHY' => 'Echocardiography'] as $key => $label)
                        <div class="col-md-3 col-sm-6 form-group"><div class="form-check"><input class="form-check-input" type="checkbox" name="{{$key}}" id="{{$key}}_rm7" value="1"><label class="form-check-label" for="{{$key}}_rm7">{{$label}}</label></div></div>
                        @endforeach
                        <div class="col-md-3 col-sm-6 form-group">
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="LAINNYA" id="LAINNYA_rm7" value="1"><label class="form-check-label" for="LAINNYA_rm7">Lainnya</label></div>
                            <div id="lainnya-text-container-rm7" class="mt-2" style="display: none;"><input type="text" class="form-control form-control-sm" name="LAINNYA_TEXT" placeholder="Keterangan lainnya..."></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WAKTU & PETUGAS -->
            <div class="card-deck mb-4">
                <div class="card">
                    <div class="card-header bg-light"><h6 class="card-title mb-0">Waktu & Petugas Penyerahan</h6></div>
                    <div class="card-body">
                        <div class="form-group"><label>Tanggal & Jam Diantar</label><div class="input-group"><input type="date" class="form-control" name="TANGGAL_DIANTAR"><input type="time" class="form-control" name="JAM_DIANTAR"></div></div>
                        <div class="form-group"><label>Perawat/Bidan Yang Menyerahkan</label><input type="text" class="form-control bg-light" id="perawat-menyerahkan-rm7" name="PERAWAT_MENYERAHKAN" readonly></div>
                    </div>
                </div>
                <div class="card card-receiver">
                    <div class="card-header bg-light"><h6 class="card-title mb-0">Waktu & Petugas Penerima</h6></div>
                    <div class="card-body">
                        <div class="form-group"><label>Tanggal & Jam Diterima</label><div class="input-group"><input type="date" class="form-control" name="TANGGAL_DITERIMA"><input type="time" class="form-control" name="JAM_DITERIMA"></div></div>
                        <div class="form-group"><label>Perawat/Bidan yang Menerima</label><input type="text" class="form-control bg-light" id="perawat-menerima-rm7" name="PERAWAT_MENERIMA" readonly></div>
                    </div>
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary" id="btn-submit-rm7">Simpan</button>
                <button type="button" class="btn btn-outline-secondary" id="btn-reset-rm7">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal untuk Detail --}}
<div class="modal fade" id="rm7DetailModal" tabindex="-1" aria-labelledby="rm7DetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rm7DetailModalLabel">Detail Transfer Pasien</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="rm7-modal-content">
                    <p class="text-center">Memuat detail...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn-edit-from-modal-rm7" style="display: none;">
                    <i class="fas fa-edit mr-1"></i> Edit Data Ini
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Panduan --}}
<div class="modal fade" id="rm7PanduanModal" tabindex="-1" aria-labelledby="rm7PanduanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="rm7PanduanModalLabel"><i class="fas fa-info-circle mr-2"></i>Panduan Penggunaan Formulir Transfer Pasien</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Formulir ini digunakan untuk mencatat transfer pasien antar unit/ruangan di dalam rumah sakit. Ada dua peran utama: <strong>Petugas Pengirim</strong> dan <strong>Petugas Penerima</strong>.</p>

                <hr>

                <h6><i class="fas fa-paper-plane mr-2 text-primary"></i>Alur untuk Petugas Pengirim:</h6>
                <ol>
                    <li>Klik tombol <strong>"Tambah Transfer Baru"</strong>.</li>
                    <li>Lengkapi semua data pada formulir, terutama bagian <strong>Asal & Tujuan</strong>, <strong>Informasi Medis</strong>, dan <strong>Vital Sign (Saat Transfer)</strong>.</li>
                    <li>Nama Anda akan otomatis terisi di kolom "Perawat/Bidan Yang Menyerahkan".</li>
                    <li>Klik <strong>"Simpan"</strong>. Data transfer akan muncul di riwayat dengan status <span class="badge badge-warning">Menunggu</span>.</li>
                    <li>Jika perlu mengubah data sebelum diterima, klik baris riwayat tersebut, lakukan perubahan, lalu klik <strong>"Update"</strong>.</li>
                </ol>

                <hr>

                <h6><i class="fas fa-hand-holding-medical mr-2 text-success"></i>Alur untuk Petugas Penerima:</h6>
                <ol>
                    <li>Cari data transfer pasien yang relevan di tabel <strong>"Riwayat Transfer Pasien"</strong> (biasanya ditandai dengan status <span class="badge badge-warning">Menunggu</span>).</li>
                    <li>Klik pada baris riwayat tersebut untuk memuat datanya ke dalam formulir.</li>
                    <li>Lengkapi data pada bagian yang bisa diisi, terutama <strong>Vital Sign (Saat Diterima)</strong>.</li>
                    <li>Nama Anda akan otomatis terisi di kolom "Perawat/Bidan yang Menerima".</li>
                    <li>Klik tombol <strong>"Terima Pasien"</strong> untuk menyelesaikan proses transfer.</li>
                    <li>Status transfer akan berubah menjadi <span class="badge badge-success">Selesai</span>.</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.card-receiver { border: 2px solid #ffc107; }
.card-receiver .card-header { background-color: #fff3cd !important; }
.history-row:hover {
    background-color: #e9ecef !important;
    cursor: pointer;
}
</style>

<script>
$(document).ready(function() {
    // --- MODAL & GLOBAL VARS ---
    // Inisialisasi Select2
    $('#dpjp-rm7').select2({
        theme: 'bootstrap4'
    });
    $('#asal-ruangan-rm7').select2({
        theme: 'bootstrap4'
    });
    $('#pindah-ruangan-rm7').select2({
        theme: 'bootstrap4'
    });

    let currentModalData = null;


    // --- CONFIG & GLOBAL VARS ---
    const config = {
        noPendaftaran: $('#rm7-no-pendaftaran').val(),
        norm: $('#rm7-norm').val(),
        currentUser: $('#rm7-user').val(),
        dpjp: $('#rm7-dpjp').val(),
        diagnosa: $('#rm7-diagnosa').val(),
        urls: {
            history: $('#rm7-url-history').val(),
            detail: $('#rm7-url-detail').val(),
            store: $('#rm7-url-store').val(),
        }
    };

    // --- HELPER FUNCTIONS ---
    function formatDate(dateString) {
        return dateString ? moment(dateString).format('YYYY-MM-DD') : '';
    }
    function formatTime(timeString) {
        if (!timeString) return '';
        // Cek jika string adalah datetime lengkap (dari TGLJAM_TERIMA)
        if (moment(timeString, 'YYYY-MM-DD HH:mm:ss.SSS', true).isValid()) {
            return moment(timeString, 'YYYY-MM-DD HH:mm:ss.SSS').format('HH:mm');
        }
        // Jika hanya string jam (misal: "21:44"), langsung kembalikan
        return timeString.substring(0, 5);
    }

    // --- UI FUNCTIONS ---
    function addObatRumahRow(data = {}) {
        const index = $('#body-obat-rumah-rm7 tr').length;
        const row = `
            <tr class="align-middle">
                <td><input type="date" name="obat_rumah[${index}][TANGGAL_DIBAWA]" class="form-control form-control-sm" value="${formatDate(data.TANGGAL_DIBAWA) || moment().format('YYYY-MM-DD')}"></td>
                <td><input type="text" name="obat_rumah[${index}][NAMA_OBAT]" class="form-control form-control-sm" value="${data.NAMA_OBAT || ''}"></td>
                <td><input type="text" name="obat_rumah[${index}][DOSIS]" class="form-control form-control-sm" value="${data.DOSIS || ''}"></td>
                <td><input type="text" name="obat_rumah[${index}][JUMLAH]" class="form-control form-control-sm" value="${data.JUMLAH || ''}"></td>
                <td><input type="text" name="obat_rumah[${index}][ALASAN_MINUM]" class="form-control form-control-sm" value="${data.ALASAN_MINUM || ''}"></td>
                <td><select name="obat_rumah[${index}][RANAP_KET]" class="form-control form-control-sm"><option value="">Pilih</option><option value="Ya" ${data.RANAP_KET == 'Ya' ? 'selected' : ''}>Ya</option><option value="Tidak" ${data.RANAP_KET == 'Tidak' ? 'selected' : ''}>Tidak</option></select></td>
                <td><input type="text" name="obat_rumah[${index}][TELAAH_OBAT]" class="form-control form-control-sm" value="${data.TELAAH_OBAT || ''}"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-row-rm7" title="Hapus baris"><i class="fas fa-trash"></i></button></td>
            </tr>`;
        $('#body-obat-rumah-rm7').append(row);
    }

    function addObatUnitRow(data = {}) {
        const index = $('#body-obat-unit-rm7 tr').length;
        const row = `
            <tr class="align-middle">
                <td><input type="text" name="obat_unit[${index}][NAMA_OBAT]" class="form-control form-control-sm" value="${data.NAMA_OBAT || ''}"></td>
                <td><input type="text" name="obat_unit[${index}][JUMLAH]" class="form-control form-control-sm" value="${data.JUMLAH || ''}"></td>
                <td><input type="text" name="obat_unit[${index}][DOSIS]" class="form-control form-control-sm" value="${data.DOSIS || ''}"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-row-rm7" title="Hapus baris"><i class="fas fa-trash"></i></button></td>
            </tr>`;
        $('#body-obat-unit-rm7').append(row);
    }

    function resetForm() {
        $('#form-rm7')[0].reset();
        $('#form-notransfer-rm7').val('');
        $('#body-obat-rumah-rm7, #body-obat-unit-rm7').empty();
        $('#history-table-rm7 tbody tr').removeClass('table-info');
        $('#form-title-rm7').html('<i class="fas fa-file-medical mr-2"></i>Buat Transfer Internal Baru');
        $('#btn-submit-rm7').html('<i class="fas fa-save mr-1"></i> Simpan').show();

        // Set defaults for new form
        $('#dpjp-rm7').val(config.dpjp);
        $('#diagnosa-rm7').val(config.diagnosa);
        $('#perawat-menyerahkan-rm7').val(config.currentUser);
        $('input[name="TANGGAL_DIANTAR"]').val(moment().format('YYYY-MM-DD'));
        $('input[name="JAM_DIANTAR"]').val(moment().format('HH:mm'));
        
        // Enable all fields for new form
        $('#form-rm7 :input').prop('disabled', false);
        // Re-initialize select2 value
        $('#dpjp-rm7').val(config.dpjp).trigger('change');
        // Reset select2 untuk ruangan
        $('#asal-ruangan-rm7').val(null).trigger('change');
        $('#pindah-ruangan-rm7').val(null).trigger('change');


        $('#perawat-menyerahkan-rm7, #perawat-menerima-rm7').prop('readonly', true);
        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Form siap untuk input baru.', showConfirmButton: false, timer: 2000 });
    }
    
    // --- DATA HANDLING ---    
    function loadHistory() {
        const body = $('#history-body-rm7');        
        body.html('<tr><td colspan="5" class="text-center p-3"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat riwayat transfer...</td></tr>');
        
        $.get(config.urls.history, { noPendaftaran: config.noPendaftaran }, function(response) {
            body.empty();
            if (response.status === 'success' && response.data.length > 0) {
                let hasPending = false;
                response.data.forEach(item => {
                    const isPending = !item.PERAWAT_MENERIMA || item.PERAWAT_MENERIMA.trim() === '' || !item.TGLJAM_TERIMA;
                    if (isPending) hasPending = true;
                    const statusBadge = isPending ? '<span class="badge badge-warning">Menunggu</span>' : '<span class="badge badge-success">Selesai</span>';
                    const rowClass = isPending ? 'table-warning' : '';
                    const tglEntry = item.TGLJAM_ENTRY ? moment(item.TGLJAM_ENTRY).format('DD/MM/YY HH:mm') : '-';

                    const row = `
                        <tr class="history-row ${rowClass}" data-notransfer="${item.NOTRANSFER}" title="Klik untuk melihat detail dan mengedit">
                            <td class="text-center align-middle"><strong>#${item.NOTRANSFER}</strong></td>
                            <td class="align-middle">
                                <div><strong>${item.ASAL_PASIEN_RUANGAN_TEXT || '-'}</strong> &rarr; <strong>${item.PINDAH_KE_RUANG_TEXT || '-'}</strong></div>
                                <div class="d-block d-md-none mt-1">
                                    <div class="text-muted small">
                                        <div><i class="fas fa-user-nurse text-primary"></i> <strong>Oleh:</strong> ${item.PERAWAT_MENYERAHKAN || '-'} (${tglEntry})</div>
                                        <div><i class="fas fa-user-check text-success"></i> <strong>Terima:</strong> ${isPending ? '<em>Belum diterima</em>' : `${item.PERAWAT_MENERIMA} (${moment(item.TGLJAM_TERIMA).format('DD/MM/YY HH:mm')})`}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle d-none d-md-table-cell">${item.PERAWAT_MENYERAHKAN || '-'}<br><small class="text-muted">${tglEntry}</small></td>
                            <td class="align-middle d-none d-md-table-cell">${isPending ? '<em class="text-muted">---</em>' : `${item.PERAWAT_MENERIMA}<br><small class="text-muted">${moment(item.TGLJAM_TERIMA).format('DD/MM/YY HH:mm')}</small>`}</td>
                            <td class="text-center align-middle">${statusBadge}</td>
                        </tr>`;
                    body.append(row);
                });
                if (hasPending) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Ada transfer pasien yang perlu diterima.', showConfirmButton: false, timer: 4000 });
                }
            } else {
                body.html('<tr><td colspan="5" class="text-center text-muted p-3">Belum ada riwayat transfer untuk pasien ini.</td></tr>');
            }
        }).fail(() => {
            body.html('<tr><td colspan="5" class="text-center text-danger p-3">Gagal memuat riwayat. Silakan coba lagi.</td></tr>');
        });
    }

    function loadDetail(noTransfer) {
        Swal.fire({ title: 'Memuat...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });
        $.get(config.urls.detail, { noPendaftaran: config.noPendaftaran, noTransfer: noTransfer }, function(response) {
            if (response.status === 'success') {
                resetForm();
                const data = response.data.main;
                const perawatMenyerahkan = (data.PERAWAT_MENYERAHKAN || '').trim();
                const perawatMenerima = (data.PERAWAT_MENERIMA || '').trim();
                const isReceived = perawatMenerima !== '';

                // --- Access Control & UI Logic ---
                const isSender = config.currentUser === perawatMenyerahkan;
                const isReceiver = config.currentUser === perawatMenerima;
                const isNewReceiver = !isReceived && config.currentUser !== perawatMenyerahkan;

                // --- Populate Form ---
                $('#form-notransfer-rm7').val(data.NOTRANSFER);
                $('#form-title-rm7').html(`<i class="fas fa-edit mr-2"></i>Edit Transfer #${data.NOTRANSFER}`);
                $(`#history-table-rm7 tbody tr[data-notransfer="${data.NOTRANSFER}"]`).addClass('table-info');

                // Fill all fields
                // Pertama, aktifkan semua field agar bisa diisi
                $('#form-rm7 :input').prop('readonly', false).prop('disabled', false);
                $('#btn-submit-rm7').show();

                Object.keys(data).forEach(key => {
                    const value = data[key] || ''; // *** FIX: Treat null/undefined as empty string
                    const input = $(`#form-rm7 [name="${key}"]`);
                    if (!input.length) return;

                    if (input.is(':checkbox')) {
                        input.prop('checked', String(value).trim() === '1').trigger('change');
                    } else if (input.is(':radio')) {
                        $(`#form-rm7 [name="${key}"][value="${String(value).trim()}"]`).prop('checked', true).trigger('change');
                    } else if (input.is('input[type="date"]')) {
                        input.val(formatDate(String(value).trim()));
                    } else if (input.is('input[type="time"]')) {
                        input.val(value ? String(value).substring(0, 5) : '');
                    } else if (input.is('select')) {
                        input.val(String(value).trim()).trigger('change');
                    } else {
                        input.val(String(value).trim());
                    }
                });

                // Nyeri radio buttons
                if (String(data.NYERI_YA || '').trim() == '1') {
                    $('input#nyeri-ya-rm7').prop('checked', true).trigger('change');
                } else {
                    $('input#nyeri-tidak-rm7').prop('checked', true).trigger('change');
                }

                // Populate tables
                (response.data.obat_rumah || []).forEach(addObatRumahRow);
                (response.data.obat_unit || []).forEach(addObatUnitRow);

                if (isSender) {
                    // Jika user adalah pengirim, bagian penerima tidak bisa diedit.
                    $('.card-receiver').find('input, textarea').prop('readonly', true);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Mode Edit Pengirim Aktif.', showConfirmButton: false, timer: 3000 });
                }
                
                if (isReceiver) {
                    // Jika user adalah penerima, bagian pengirim tidak bisa diedit.
                    // Gunakan :not(.card-receiver :input) untuk menargetkan semua input di luar card penerima.
                    $('#form-rm7 :input:not(.card-receiver :input)').each(function() {
                        const el = $(this);
                        if (!el.is(':button')) { // Jangan readonly tombol
                            el.prop('readonly', true);
                        }
                    });
                    Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Mode Edit Penerima Aktif.', showConfirmButton: false, timer: 3000 });
                } else if (isNewReceiver) {
                    // KASUS 3: Penerima BARU akan menerima pasien untuk pertama kali.
                    // Bagian pengirim tidak bisa diedit.
                     $('#form-rm7 :input:not(.card-receiver :input)').each(function() {
                        const el = $(this);
                        if (!el.is(':button')) {
                            el.prop('readonly', true);
                        }
                    });

                    // Isi data default untuk penerima baru
                    $('#perawat-menerima-rm7').val(config.currentUser);
                    if (!$('input[name="TANGGAL_DITERIMA"]').val()) $('input[name="TANGGAL_DITERIMA"]').val(moment().format('YYYY-MM-DD'));
                    if (!$('input[name="JAM_DITERIMA"]').val()) $('input[name="JAM_DITERIMA"]').val(moment().format('HH:mm'));
                    $('#btn-submit-rm7').html('<i class="fas fa-check-circle mr-1"></i> Terima Pasien').show();
                    Swal.fire('Mode Terima Pasien Aktif', 'Anda akan menerima pasien ini. Silakan lengkapi data penerimaan dan simpan.', 'info');
                } else if (!isSender && !isReceiver && !isNewReceiver) {
                    // KASUS 4: User lain yang tidak berkepentingan (hanya bisa melihat).
                    $('#form-rm7').find('input, textarea').prop('readonly', true);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Mode Lihat Data.', text: 'Anda hanya dapat melihat data ini.', showConfirmButton: false, timer: 4000 });
                }
                
                // Selalu readonly untuk nama perawat
                $('#perawat-menyerahkan-rm7, #perawat-menerima-rm7').prop('readonly', true);
                
                if (Swal.isVisible()) { Swal.close(); }
                $('html, body').animate({ scrollTop: $("#form-rm7").offset().top - 70 }, 500);

            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }).fail(() => Swal.fire('Error', 'Gagal memuat detail transfer.', 'error'));
    }

    function showDetailModal(noTransfer) {
        currentModalData = { noTransfer: noTransfer }; // Simpan noTransfer untuk tombol edit
        const modalContent = $('#rm7-modal-content');
        const editButton = $('#btn-edit-from-modal-rm7');
        
        modalContent.html('<p class="text-center">Memuat detail...</p>');
        editButton.hide();
        $('#rm7DetailModal').modal('show');

        $.get(config.urls.detail, { noPendaftaran: config.noPendaftaran, noTransfer: noTransfer }, function(response) {
            if (response.status === 'success') {
                const mainData = response.data.main;
                const obatRumah = response.data.obat_rumah || [];
                const obatUnit = response.data.obat_unit || [];
                const data = response.data.main;
                const perawatMenyerahkan = (data.PERAWAT_MENYERAHKAN || '').trim();
                const perawatMenerima = (data.PERAWAT_MENERIMA || '').trim();
                const isReceived = perawatMenerima !== '';
                const isSender = config.currentUser === perawatMenyerahkan;
                const isReceiver = config.currentUser === perawatMenerima;
                const isNewReceiver = !isReceived && !isSender;

                // --- Buat Tabel Obat ---
                let obatRumahHtml = '<p class="text-muted">Tidak ada data.</p>';
                if (obatRumah.length > 0) {
                    obatRumahHtml = `
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light"><tr><th>Tgl Dibawa</th><th>Nama Obat</th><th>Dosis</th><th>Jumlah</th><th>Alasan</th><th>Lanjut Ranap?</th><th>Telaah</th></tr></thead>
                                <tbody>
                                    ${obatRumah.map(o => `
                                        <tr>
                                            <td>${formatDate(o.TANGGAL_DIBAWA) || '-'}</td>
                                            <td>${o.NAMA_OBAT || '-'}</td>
                                            <td>${o.DOSIS || '-'}</td>
                                            <td>${o.JUMLAH || '-'}</td>
                                            <td>${o.ALASAN_MINUM || '-'}</td>
                                            <td>${o.RANAP_KET || '-'}</td>
                                            <td>${o.TELAAH_OBAT || '-'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>`;
                }

                let obatUnitHtml = '<p class="text-muted">Tidak ada data.</p>';
                if (obatUnit.length > 0) {
                    obatUnitHtml = `
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light"><tr><th>Nama Obat</th><th>Jumlah</th><th>Dosis</th></tr></thead>
                                <tbody>
                                    ${obatUnit.map(o => `
                                        <tr>
                                            <td>${o.NAMA_OBAT || '-'}</td>
                                            <td>${o.JUMLAH || '-'}</td>
                                            <td>${o.DOSIS || '-'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>`;
                }

                // --- Buat Konten Modal Utama ---
                let html = `
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Transfer #${mainData.NOTRANSFER}</h5>
                        <p class="mb-0"><strong>Dari:</strong> ${mainData.ASAL_PASIEN_RUANGAN_TEXT || '-'} &rarr; <strong>Ke:</strong> ${mainData.PINDAH_KE_RUANG_TEXT || '-'}</p>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header"><strong>Informasi Medis</strong></div>
                        <div class="card-body">
                            <p><strong>DPJP:</strong> ${mainData.DPJP || '-'}</p>
                            <p class="mb-0"><strong>Diagnosa:</strong> ${mainData.DIAGNOSA_SEMENTARA || '-'}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header"><strong>Vital Sign (Saat Transfer)</strong></div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between"><span>Tekanan Darah:</span> <strong>${mainData.TRANSFER_TD || '-'} mmHg</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Nadi:</span> <strong>${mainData.TRANSFER_NADI || '-'} x/mnt</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Suhu:</span> <strong>${mainData.TRANSFER_SUHU || '-'} °C</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Pernafasan:</span> <strong>${mainData.TRANSFER_PERNAFASAN || '-'} x/mnt</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Kesadaran:</span> <strong>${mainData.TRANSFER_KESADARAN || '-'}</strong></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header"><strong>Vital Sign (Saat Diterima)</strong></div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between"><span>Tekanan Darah:</span> <strong>${mainData.DITERIMA_TD || '-'} mmHg</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Nadi:</span> <strong>${mainData.DITERIMA_NADI || '-'} x/mnt</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Suhu:</span> <strong>${mainData.DITERIMA_SUHU || '-'} °C</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Pernafasan:</span> <strong>${mainData.DITERIMA_PERNAFASAN || '-'} x/mnt</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Kesadaran:</span> <strong>${mainData.DITERIMA_KESADARAN || '-'}</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3"><div class="card-header"><strong>Obat Yang Dibawa Dari Rumah</strong></div><div class="card-body">${obatRumahHtml}</div></div>
                    <div class="card mb-3"><div class="card-header"><strong>Obat Yang Dibawa Ke Unit</strong></div><div class="card-body">${obatUnitHtml}</div></div>

                    <hr>
                    <h6>Petugas</h6>
                    <p><strong>Diserahkan oleh:</strong> ${mainData.PERAWAT_MENYERAHKAN || '-'} pada ${formatDate(mainData.TANGGAL_DIANTAR)} ${formatTime(mainData.JAM_DIANTAR)}<br>
                       <strong>Diterima oleh:</strong> ${mainData.PERAWAT_MENERIMA || '<em>Belum diterima</em>'} ${mainData.TGLJAM_TERIMA ? 'pada ' + moment(mainData.TGLJAM_TERIMA).format('DD/MM/YYYY HH:mm') : ''}</p>
                `;
                modalContent.html(html);

                // Tampilkan tombol edit jika user berhak
                if ((isSender && !isReceived) || isReceiver || isNewReceiver) {
                    editButton.show();
                }

            } else {
                modalContent.html('<p class="text-center text-danger">Gagal memuat detail.</p>');
            }
        }).fail(() => {
            modalContent.html('<p class="text-center text-danger">Gagal memuat detail.</p>');
        });
    }

    // --- EVENT LISTENERS ---
    $('#btn-new-transfer-rm7').click(resetForm);
    $('#btn-reset-rm7').click(resetForm);

    $('#history-body-rm7').on('click', 'tr', function(e) {
        // Mencegah trigger ganda jika tombol di dalam baris diklik
        if ($(e.target).is('button, i, a')) return;

        const noTransfer = $(this).closest('tr').data('notransfer');
        showDetailModal(noTransfer);
    });

    // Tombol Edit dari dalam Modal
    $('#btn-edit-from-modal-rm7').on('click', function() {
        if (currentModalData && currentModalData.noTransfer) {
            $('#rm7DetailModal').modal('hide');
            // Beri sedikit delay agar modal sempat tertutup sebelum scroll
            setTimeout(() => {
                loadDetail(currentModalData.noTransfer);
            }, 300);
        }
    });

    $('#btn-add-obat-rumah-rm7').click(() => addObatRumahRow());
    $('#btn-add-obat-unit-rm7').click(() => addObatUnitRow());
    $(document).on('click', '.btn-remove-row-rm7', function() { $(this).closest('tr').remove(); });
    
    $('input[name="NYERI_GROUP"]').change(function() {
        if ($(this).val() == '1') $('#skala-nyeri-container-rm7').slideDown();
        else $('#skala-nyeri-container-rm7').slideUp();
    });

    $('input[name="LAINNYA"]').change(function() {
        if ($(this).is(':checked')) $('#lainnya-text-container-rm7').slideDown();
        else $('#lainnya-text-container-rm7').slideUp();
    });

    $('#form-rm7').on('submit', function(e) {
        e.preventDefault();

        const submitButton = $('#btn-submit-rm7');
        const originalButtonHtml = submitButton.html();
        const form = $(this);
        let formData = form.serializeArray();

        // *** FIX: Convert empty string values from selects to null
        formData.forEach(function(field) {
            if (form.find(`[name="${field.name}"]`).is('select') && field.value === '') {
                field.value = null;
            }
        });

        $.ajax({
            url: config.urls.store,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
            },
            success: function(response) {
            Swal.fire('Berhasil!', response.message, 'success').then(() => {
                resetForm();
                loadHistory();
            });
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan pada server. Silakan coba lagi.';
                Swal.fire('Gagal!', errorMsg, 'error');
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });

    // --- INITIAL LOAD ---
    resetForm();
    loadHistory();
});
</script>