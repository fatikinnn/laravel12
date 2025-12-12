@php
    // Define access roles for cleaner Blade syntax
    $access = strtoupper(session('user.access') ?? '');
    $isAdmin = $access === 'ADMIN'; // Assuming 'ADMIN' is the role name for admin
    $isDokterUmum = $access === 'DOKTER UMUM';
    $isBidan = $access === 'BIDAN';
    $isPerawat = $access === 'PERAWAT';
    $isGizi = $access === 'GIZI';
    $isMcu = $access === 'MCU';
    $isDokter = in_array($access, ['DOKTER UMUM', 'DOKTER']); // Assuming 'DOKTER' is for specialists
    $isPerawatBidan = in_array($access, ['PERAWAT', 'BIDAN']);
@endphp

@extends('layouts.rme')

@section('title', 'Assesmen RME')

@php
    // Konten untuk sidebar dan panel mobile didefinisikan di sini agar tidak duplikasi kode
    $controlContent = '
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search mr-1"></i>
                Pencarian Pasien
            </h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="norm">No. Rekam Medis</label>
                <div class="input-group">
                    <input type="text" id="norm" name="norm" class="form-control" placeholder="Ketik 6 digit No.RM" maxlength="6" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="search-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>
                </div>
            </div>
        </div>
    ';
@endphp

@section('rme-sidebar')
    {{-- CARD PENCARIAN PASIEN --}}
    <div id="search-card" class="card card-primary card-outline sticky-card">
        {!! $controlContent !!}
    </div>

    {{-- CARD RIWAYAT KUNJUNGAN --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-1"></i>
                Riwayat Kunjungan
            </h3>
        </div>
        <div class="card-body" id="visit-history-container">
            <p class="text-muted text-center">Silakan cari pasien terlebih dahulu.</p>
        </div>
        <div class="card-footer p-2" id="visit-history-pagination" style="display: none;">
            <!-- Pagination controls will be inserted here -->
        </div>
    </div>

    {{-- CARD DETAIL PASIEN (SCROLLABLE) --}}
    <div id="patient-details-card" class="card card-primary card-outline" style="display: none;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-injured mr-1"></i>
                Detail Pasien
            </h3>
        </div>
        <div class="card-body" id="patient-details-container">
            {{-- Konten detail pasien akan dimuat di sini oleh AJAX --}}
        </div>
    </div>
@endsection

@section('rme-control-panel')
<div class="content-header">
    <div class="container-fluid">
        <div class="card card-primary card-outline" id="mobile-control-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search mr-1"></i>
                    Pencarian Pasien
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                {{-- Area Pencarian --}}
                <div class="form-group">
                    <label for="norm-mobile">No. Rekam Medis</label>
                    <div class="input-group">
                        <input type="text" id="norm-mobile" name="norm" class="form-control" placeholder="Ketik 6 digit No.RM" maxlength="6" required>
                        <div class="input-group-append">
                            <span class="input-group-text" id="search-spinner-mobile" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                        </div>
                    </div>
                </div>

                {{-- Riwayat Kunjungan Mobile --}}
                <div id="visit-history-container-mobile" class="mt-3">
                    <p class="text-muted text-center">Silakan cari pasien terlebih dahulu.</p>
                </div>
                <div class="card-footer p-2" id="visit-history-pagination-mobile" style="display: none;"></div>

                {{-- Detail Pasien Mobile --}}
                <div id="patient-details-card-mobile" class="mt-3" style="display: none;">
                    <div id="patient-details-container-mobile"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    {{-- Area konten utama untuk form RME --}}
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Formulir Asesmen RME</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            {{-- KONTROL PEMILIHAN FORMULIR --}}
            <div id="form-selection-card" class="card card-primary card-outline sticky-card sticky-form-selection" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">Pilih Formulir RME</h3>
                </div>
                <div class="card-body">
                    {{-- Custom Searchable Dropdown --}}
                    <div class="form-group">
                        <label for="form-search-input">Pilih formulir yang akan diisi:</label>
                        <div class="custom-form-search">
                            <input type="text" id="form-search-input" class="form-control" placeholder="Silakan Pilih Form..." autocomplete="off">
                            <div id="form-results-container" class="form-results-list"></div>
                        </div>
                        <select id="form-selector" class="form-control" style="display: none;">
                            <option value="">-- Pilih Formulir --</option>

                            {{-- DOKTER UMUM --}}
                            @if ($isAdmin || $isDokterUmum)
                                <option value="rm3a" data-url="{{ route('rme.igd.form.rm3a.load') }}">RM3A - Triage</option>
                                <option value="rm3b" data-url="{{ route('rme.igd.form.rm3b.load') }}">RM3B - Asesmen Medis Gawat Darurat</option>
                            @endif

                            {{-- SPRI / SPRJ --}}
                            @if ($isAdmin || $isDokterUmum || $isBidan)
                                <option value="rm1c" data-url="{{ route('rme.igd.form.rm1c.load') }}">RM1C - Surat Perintah Rawat Inap (SPRI)</option>
                                <option value="rm5" data-url="{{ route('rme.igd.form.rm5.load') }}">RM5 - Surat Perintah Rawat Jalan (SPRJ)</option>
                            @endif

                            {{-- ASESMEN KEPERAWATAN GADAR --}}
                            @if ($isAdmin || $isPerawatBidan)
                                <optgroup label="Asesmen Keperawatan Gawat Darurat">
                                    @if ($isAdmin || $isBidan)
                                        <option value="rm3d" data-url="{{ route('rme.igd.form.rm3d.load') }}">RM3D - Asesmen Kebidanan</option>
                                    @endif
                                    @if ($isAdmin || $isPerawat)
                                        <option value="rm3f" data-url="{{ route('rme.igd.form.rm3f.load') }}">RM3F - Asesmen Keperawatan</option>
                                    @endif
                                    @if ($isAdmin || $isPerawatBidan)
                                        <option value="rm28" data-url="{{ route('rme.igd.form.rm28.load') }}" data-kelompok-usia="neonatus">RM28 - Asesmen Neonatus</option>
                                    @endif
                                </optgroup>
                            @endif

                            {{-- SKRINING GIZI --}}
                            @if ($isAdmin || $isDokterUmum || $isGizi || $isBidan)
                                <optgroup label="Skrining Gizi">
                                    <option value="rm6a_dewasa" data-url="{{ route('rme.igd.form.rm6a_dewasa.load') }}">Skrining Gizi Dewasa</option>
                                    <option value="rm6a" data-url="{{ route('rme.igd.form.rm6a.load') }}" data-kelompok-usia="geriatri">Skrining Gizi Geriatri</option>
                                    <option value="rm26" data-url="{{ route('rme.igd.form.rm26.load') }}" data-kelompok-usia="anak">Skrining Gizi Anak</option>
                                    <option value="skrininggiziibuhamil" data-url="{{ route('rme.igd.form.skrininggiziibuhamil.load') }}" data-gender="P">Skrining Gizi Ibu Hamil</option>
                                    <option value="rm29" data-url="{{ route('rme.igd.form.rm29.load') }}" data-kelompok-usia="neonatus">Skrining Gizi Neonatus</option>
                                </optgroup>
                            @endif

                            {{-- RISIKO JATUH --}}
                            @if ($isAdmin || $isPerawatBidan)
                                <optgroup label="Risiko Jatuh">
                                    <option value="rm4a" data-url="{{ route('rme.igd.form.rm4a.load') }}" data-kelompok-usia="dewasa">Risiko Jatuh Dewasa (Morse)</option>
                                    <option value="rm6b" data-url="{{ route('rme.igd.form.rm6b.load') }}" data-kelompok-usia="geriatri">Risiko Jatuh Geriatri (Ontario)</option>
                                    <option value="rm25a" data-url="{{ route('rme.igd.form.rm25a.load') }}" data-kelompok-usia="anak">Risiko Jatuh Anak (Humpty Dumpty)</option>
                                </optgroup>
                            @endif

                            {{-- EDUKASI & PERSETUJUAN --}}
                            @if ($isAdmin || $isDokter || $isPerawatBidan)
                                <optgroup label="Edukasi & Persetujuan">
                                <option value="rm8a" data-url="{{ route('rme.igd.form.rm8a.load') }}">RM8A - Edukasi Pasien</option>
                                <option value="rm48a" data-url="{{ route('rme.igd.form.rm48a.load') }}">RM48A - Persetujuan Tindakan</option>
                                </optgroup>
                            @endif

                            {{-- MONITORING & OBSERVASI --}}
                            @if ($isAdmin || $isPerawatBidan)
                                <optgroup label="Monitoring & Observasi">
                                <option value="rm60" data-url="{{ route('rme.igd.form.rm60.load') }}">RM60 - Monitoring Gawat Darurat</option>
                                <option value="rm17" data-url="{{ route('rme.igd.rm17.load') }}">RM17 - Tanda Vital (TTV)</option>
                                <option value="rm16b" data-url="{{ route('rme.igd.rm16b.load') }}">RM16B - Monitoring Infus</option>
                                    <option value="rm9a3" data-url="{{ route('rme.igd.form.rm9a3.load') }}" data-gender="P">RM9A3 - Partograf</option>
                                <option value="skriningsepsis" data-url="{{ route('rme.igd.form.skriningsepsis.load') }}">Skrining Sepsis</option>
                                <option value="lukareeda" data-url="{{ route('rme.igd.form.lukareeda.load') }}">Penilaian Luka REEDA</option>
                                </optgroup>
                            @endif

                            {{-- EWS --}}
                            @if ($isAdmin || $isPerawatBidan)
                                <optgroup label="Early Warning System (EWS)">
                                    <option value="news" data-url="{{ route('rme.igd.form.news.load') }}" data-kelompok-usia="dewasa">NEWS - Dewasa</option>
                                    <option value="moews" data-url="{{ route('rme.igd.form.moews.load') }}" data-gender="P">MOEWS - Obstetri</option>
                                    <option value="pews" data-url="{{ route('rme.igd.form.pews.load') }}" data-kelompok-usia="anak">PEWS - Anak</option>
                                    <option value="ews" data-url="{{ route('rme.igd.form.ews.load') }}" data-kelompok-usia="neonatus">NEWS - Neonatal</option>
                                </optgroup>
                            @endif

                            {{-- CATATAN & LAPORAN --}}
                            <optgroup label="Catatan & Laporan">
                                @if ($isAdmin || $isDokter || $isPerawatBidan || $isGizi)
                                <option value="cppt" data-url="{{ route('rme.igd.form.cppt.load') }}">CPPT - Catatan Perkembangan</option>
                                @endif
                                @if ($isAdmin || $isPerawatBidan)
                                <option value="rm18" data-url="{{ route('rme.igd.rm18.load') }}">RM18 - Catatan Pemberian Obat</option>
                                <option value="rm24d" data-url="{{ route('rme.igd.rm24d.load') }}">RM24D - Pengelompokan Data</option>
                                @endif
                                @if ($isAdmin || $isPerawatBidan)
                                <option value="rm55" data-url="{{ route('rme.igd.form.rm55.load') }}">RM55 - Penyerahan dan Persetujuan Rawat Gabung</option>
                                <option value="rm7" data-url="{{ route('rme.igd.form.rm7.load') }}">RM7 - Transfer Pasien Internal</option>
                                @endif
                                @if ($isAdmin || $isBidan)
                                <option value="rm80" data-url="{{ route('rme.igd.form.rm80.load') }}">RM80 - Berita Acara Serah Terima Bayi</option>
                                @endif
                                @if ($isAdmin || $isBidan)
                                    <option value="rm57" data-url="{{ route('rme.igd.form.rm57.load') }}" data-gender="P">RM57 - Surat Keterangan Bersalin</option>
                                @endif
                                @if ($isAdmin)
                                    <option value="psi" data-url="{{ route('rme.igd.form.psi.load') }}">PSI - Pneumonia Severity Index</option>
                                @endif
                                @if ($isAdmin || $isMcu)
                                    <option value="pkn" data-url="{{ route('rme.igd.form.pkn.load') }}" data-kelompok-usia="neonatus">PKN - Perawatan Kesehatan Neonatal</option>
                                @endif
                                <option value="mcu" data-url="{{ route('rme.igd.form.mcu.load') }}">MCU - Medical Check Up</option>
                            </optgroup>
                            
                            <optgroup label="Penunjang">
                                <option value="fotopenunjang" data-url="{{ route('rme.igd.form.fotopenunjang.load') }}">FOTO - Upload Foto Penunjang</option>
                                {{-- <option value="permintaan-penunjang" data-url="{{ route('rme.igd.form.permintaan-penunjang.load') }}">PERMINTAAN - Permintaan Penunjang (Lab, Rad, dll)</option> --}}
                            </optgroup>
                        </select>
                    </div>
                    {{-- Tombol untuk membuka form di aplikasi native --}}
                    <div class="mt-2 text-start"> {{-- Tambahkan text-right untuk meratakan ke kanan jika diinginkan --}}
                        <div class="btn-toolbar" role="toolbar">
                            <button id="lihatFormBtn" type="button" class="btn btn-primary mr-2">
                                <i class="fas fa-desktop mr-2"></i>Lihat Form
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-file-medical-alt mr-2"></i>Hasil Penunjang
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item btn-load-penunjang" href="#" data-type="lab">
                                        <i class="fas fa-flask fa-fw mr-2 text-success"></i>Hasil Lab
                                    </a>
                                    <a class="dropdown-item btn-load-penunjang" href="#" data-type="rad">
                                        <i class="fas fa-x-ray fa-fw mr-2 text-warning"></i>Hasil Rad
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KONTAINER UNTUK FORMULIR YANG DIMUAT --}}
            <div id="form-container">
                <div id="initial-message" class="text-muted text-center" style="padding: 60px 0;">
                    <p style="font-size: 16px; line-height: 1.7;">
                        👋 <strong>Selamat datang di Formulir Rekam Medis Elektronik (RME)!</strong><br>
                        Untuk memulai, silakan:
                    </p>

                    <ol style="display: inline-block; text-align: left; font-size: 15px; line-height: 1.8; margin: 10px 0 20px;">
                        <li>Masukkan <strong>NORM (Nomor Rekam Medis)</strong> pasien pada kolom pencarian.</li>
                        <li>Pilih <strong>kunjungan pasien</strong> yang ingin Anda buka.</li>
                        <li>Kemudian pilih <strong>formulir RME</strong> yang ingin diisi atau diperbarui sesuai kebutuhan.</li>
                    </ol>

                    <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
                        💡 <em>Tips:</em> 
                    </p>
                    <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px;">
                        <li>🖥️ Gunakan perangkat <strong>desktop</strong> atau <strong>tablet</strong> agar tampilan form lebih lengkap dan mudah diisi.</li>
                        <li>📶 Pastikan koneksi internet stabil untuk menghindari kehilangan data saat menyimpan.</li>
                        <li>🖨️ Fitur <strong>lihat/print out</strong> hanya tersedia melalui komputer.</li>
                    </ul>

                    <p style="margin-top: 20px; font-size: 13px; color: #adb5bd;">
                        ⚙️ Sistem ini terintegrasi dengan database RME rumah sakit. Setiap perubahan akan tersimpan otomatis sesuai hak akses pengguna.
                    </p>
                </div>
            </div>  


        </div>
    </div>

    {{-- Modal untuk menampilkan hasil penunjang (Lab/Rad) --}}
    <div class="modal fade" id="penunjangModal" tabindex="-1" aria-labelledby="penunjangModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="penunjangModalLabel">Hasil Pemeriksaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="penunjangModalBody">
                    {{-- Konten hasil akan dimuat di sini oleh AJAX --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Lightbox2 Customizations */
    .lb-data .lb-caption { font-size: 1rem; }
    .custom-form-search {
        position: relative;
    }
    .form-results-list {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 .25rem .25rem;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1050; /* Higher than other elements */
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .form-result-item, .form-result-group {
        padding: 8px 12px;
    }
    .form-result-group {
        font-weight: bold;
        color: #495057;
        background-color: #e9ecef;
        border-top: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        margin-top: -1px; /* Prevent double borders */
    }
    .form-result-item {
        cursor: pointer;
        color: #212529;
    }
    .form-result-item:hover {
        background-color: #007bff;
        color: #fff;
    }
    .form-result-item.highlighted {
        background-color: #007bff;
        color: #fff;
    }
    .form-result-item.no-results {
        cursor: default;
        color: #6c757d;
    }
    .form-result-item.no-results:hover {
        background-color: #fff;
        color: #6c757d;
    }
</style>
@endpush

{{-- Seluruh script AJAX dipindahkan ke sini --}}
@push('scripts')
    <script>
        $(document).ready(function() {

            // Setup CSRF Token untuk semua request AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let searchRequest = null; // Untuk handle debouncing
            let selectedPatientData = null; // Menyimpan data pasien yang dipilih (termasuk NoRM)
            let selectedNoPendaftaran = null; // Menyimpan No Pendaftaran yang dipilih
            let currentFormText = ''; // Menyimpan teks dari form yang sedang aktif
            let formFillStatuses = {}; // Menyimpan status pengisian form
            let fullPatientDetails = null; // Variabel untuk menyimpan seluruh detail pasien yang dipilih

            // Variabel untuk pagination riwayat kunjungan
            let allVisits = []; // Ini akan menjadi satu-satunya sumber data riwayat
            let currentVisitPage = 1;
            const itemsPerVisitPage = 5;

            const IS_ADMIN = {{ $isAdmin ? 'true' : 'false' }};
            // Helper function to determine age group
            function getKelompokUsia(tanggalLahir) {
                if (!tanggalLahir) return 'dewasa'; // Default

                const birthDate = moment(tanggalLahir, 'DD-MM-YYYY');
                const today = moment();
                const ageInDays = today.diff(birthDate, 'days');
                const ageInYears = today.diff(birthDate, 'years');

                if (ageInDays <= 28) return 'neonatus';
                if (ageInYears < 18) return 'anak';
                if (ageInYears >= 60) return 'geriatri';
                return 'dewasa';
            }


            // 1. Pencarian otomatis saat 6 digit NoRM dimasukkan
            // Menggabungkan event handler untuk input desktop dan mobile
            $(document).on('input', '#norm, #norm-mobile', function() {
                const norm = $(this).val();
                const isMobile = $(this).attr('id') === 'norm-mobile';

                // Reset state setiap kali ada input baru untuk mencegah data lama terbawa
                selectedNoPendaftaran = null;
                selectedPatientData = null;
                $('#patient-details-card, #patient-details-card-mobile').slideUp();
                formFillStatuses = {}; // Reset status form
                fullPatientDetails = null;
                currentFormText = '';
                $('#form-selection-card').slideUp();
                $('#form-search-input').val('');
                $('#form-container').html(`
                    <div id="initial-message" class="text-muted text-center" style="padding: 60px 0;">
                        <p style="font-size: 16px; line-height: 1.7;">
                            👋 <strong>Selamat datang di Formulir Rekam Medis Elektronik (RME)!</strong><br>
                            Untuk memulai, silakan:
                        </p>

                        <ol style="display: inline-block; text-align: left; font-size: 15px; line-height: 1.8; margin: 10px 0 20px;">
                            <li>Masukkan <strong>NORM (Nomor Rekam Medis)</strong> pasien pada kolom pencarian.</li>
                            <li>Pilih <strong>kunjungan pasien</strong> yang ingin Anda buka.</li>
                            <li>Kemudian pilih <strong>formulir RME</strong> yang ingin diisi atau diperbarui sesuai kebutuhan.</li>
                        </ol>

                        <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
                            💡 <em>Tips:</em> 
                        </p>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px;">
                            <li>🖥️ Gunakan perangkat <strong>desktop</strong> atau <strong>tablet</strong> agar tampilan form lebih lengkap dan mudah diisi.</li>
                            <li>📶 Pastikan koneksi internet stabil untuk menghindari kehilangan data saat menyimpan.</li>
                            <li>🖨️ Fitur <strong>lihat/print out</strong> hanya tersedia melalui komputer.</li>
                        </ul>

                        <p style="margin-top: 20px; font-size: 13px; color: #adb5bd;">
                            ⚙️ Sistem ini terintegrasi dengan database RME rumah sakit. Setiap perubahan akan tersimpan otomatis sesuai hak akses pengguna.
                        </p>
                    </div>
                `); 
                // Hapus juga dari sessionStorage agar tidak ada kebingungan saat refresh
                sessionStorage.removeItem('rmeIgdNorm');
                sessionStorage.removeItem('rmeIgdNoPendaftaran');

                // Sinkronkan nilai antara input desktop dan mobile
                if (isMobile) $('#norm').val(norm);
                else $('#norm-mobile').val(norm);

                // Reset pagination
                $('#visit-history-pagination, #visit-history-pagination-mobile').hide();

                if (searchRequest) {
                    searchRequest.abort();
                }

                if (norm.length === 6) {
                    searchRequest = $.ajax({
                        url: "{{ route('rme.igd.searchVisits') }}",
                        type: 'GET',
                        data: { norm: norm },
                        beforeSend: function() {
                            $('#search-spinner, #search-spinner-mobile').show();
                            const loadingHtml = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari riwayat kunjungan...</div>';
                            $('#visit-history-container, #visit-history-container-mobile').html(loadingHtml);
                        },
                        success: function(visits) {
                            allVisits = visits;
                            renderVisitHistoryPage(1); // Render tabel riwayat

                            // --- AUTO SELECT FIRST VISIT ---
                            // Jika ada riwayat kunjungan, otomatis klik baris pertama.
                            if (visits.length > 0) {
                                $('#visit-history-container tr.selectable-row:first').click();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.statusText !== 'abort') {
                                const errorHtml = '<p class="text-danger text-center">Gagal memuat data.</p>';
                                $('#visit-history-container, #visit-history-container-mobile').html(errorHtml);
                            }
                        },
                        complete: function() {
                            $('#search-spinner, #search-spinner-mobile').hide();
                            searchRequest = null;
                        }
                    });
                } else {
                    const initialHtml = '<p class="text-muted text-center">Silakan cari pasien terlebih dahulu.</p>';
                    $('#visit-history-container, #visit-history-container-mobile').html(initialHtml);
                }
            });

            // Fungsi untuk render halaman riwayat kunjungan
            function renderVisitHistoryPage(page) {
                currentVisitPage = page;
                // Target kedua kontainer (desktop dan mobile)
                const container = $('#visit-history-container, #visit-history-container-mobile');
                const paginationControls = $('#visit-history-pagination, #visit-history-pagination-mobile');

                container.empty();
                paginationControls.empty();

                if (allVisits.length === 0) {
                    container.html(`<div class="alert alert-warning text-center">Tidak ada riwayat kunjungan ditemukan.</div>`);
                    paginationControls.hide();
                    return;
                }

                const totalPages = Math.ceil(allVisits.length / itemsPerVisitPage);
                const startIndex = (page - 1) * itemsPerVisitPage;
                const pageItems = allVisits.slice(startIndex, startIndex + itemsPerVisitPage);

                let tableRows = pageItems.map(visit => {
                    let tglDaftar = new Date(visit.TanggalDaftar)
                        .toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
                        .replace(/\//g, '-');
                    let rowClass = 'selectable-row'; // Semua baris sekarang bisa dipilih

                    return `
                        <tr data-nopendaftaran="${$.trim(visit.NoPendaftaran)}" class="${rowClass}">
                            <td>${visit.NoKunjungan}</td>
                            <td>${tglDaftar}</td>
                            <td>${visit.NamaPasien}</td>
                            <td>
                                ${visit.Rawat.trim() === 'Rawat Inap' 
                                    ? '<span class="badge badge-warning">Rawat Inap</span>' 
                                    : '<span class="badge badge-info">Rawat Jalan</span>'}
                            </td>
                            <td>${visit.NoPendaftaran}</td>
                        </tr>
                    `;
                }).join('');

                const tableHtml = `
                    <div class="table-responsive">
                        <table class="table table-hover table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No. Kunj</th>
                                    <th>Tgl. Daftar</th>
                                    <th>Nama Pasien</th>
                                    <th>Rawat</th>
                                    <th>No. Daftar</th>
                                </tr>
                            </thead>
                            <tbody>${tableRows}</tbody>
                        </table>
                    </div>
                `;
                container.html(tableHtml);

                const prevDisabled = page === 1 ? 'disabled' : '';
                const nextDisabled = page === totalPages ? 'disabled' : '';
                const paginationHtml = `
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <button class="btn btn-outline-primary btn-sm" id="prevVisitPage" ${prevDisabled}><i class="fas fa-chevron-left"></i></button>
                        <span class="text-muted small">Hal ${page} dari ${totalPages}</span>
                        <button class="btn btn-outline-primary btn-sm" id="nextVisitPage" ${nextDisabled}><i class="fas fa-chevron-right"></i></button>
                    </div>
                `;
                paginationControls.html(paginationHtml).show();
            }

            // Event handler untuk pagination (berlaku untuk kedua versi)
            $(document).on('click', '#prevVisitPage, #nextVisitPage', function() {
                const isPrev = $(this).attr('id').includes('prev');
                if (isPrev) {
                    if (currentVisitPage > 1) renderVisitHistoryPage(currentVisitPage - 1);
                } else {
                    const totalPages = Math.ceil(allVisits.length / itemsPerVisitPage);
                    if (currentVisitPage < totalPages) renderVisitHistoryPage(currentVisitPage + 1);
                }
            });

            // 2. Aksi saat tombol "Pilih" diklik
            $(document).on('click', '.selectable-row', function() {
                const noPendaftaran = $(this).data('nopendaftaran');
                selectedNoPendaftaran = noPendaftaran; // Simpan No Pendaftaran
                selectedPatientData = { NoPendaftaran: noPendaftaran, NoRM: $('#norm').val() || $('#norm-mobile').val() }; // Simpan NoRM juga

                // Visual feedback
                // Hapus highlight dari semua baris di kedua kontainer
                $('#visit-history-container .selectable-row, #visit-history-container-mobile .selectable-row').removeClass('table-primary');
                // Tambahkan highlight ke baris yang sesuai di kedua kontainer menggunakan data-nopendaftaran
                $(`.selectable-row[data-nopendaftaran="${noPendaftaran}"]`).addClass('table-primary');

                $.ajax({
                    url: "{{ route('rme.igd.getPatientDetails') }}",
                    type: 'GET',
                    data: { NoPendaftaran: noPendaftaran },
                    beforeSend: function() {
                        const loadingHtml = '<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat detail...</p>';
                        $('#patient-details-container, #patient-details-container-mobile').html(loadingHtml);
                        $('#patient-details-card, #patient-details-card-mobile').slideDown();
                    },
                    success: function(patient) {
                        // Simpan seluruh detail pasien ke variabel global
                        fullPatientDetails = patient;
                        // Simpan status pengisian form
                        // Simpan status pengisian form yang diterima dari backend
                        formFillStatuses = patient.form_statuses || {};
                        // Perbarui daftar form berdasarkan demografi pasien
                        updateFormOptionsBasedOnPatient(patient);


                        const patientDetailsHtml = `
                            <table>
                                <tr><td><strong>No Daftar</strong></td><td>: ${patient['No Pendaftaran']}</td></tr>
                                <tr><td><strong>Nama</strong></td><td>: ${patient['Nama Pasien']}</td></tr>
                                <tr><td><strong>Alamat</strong></td><td>: ${patient['Alamat']}</td></tr>
                                <tr><td><strong>Usia</strong></td><td>: ${patient['Usia']}</td></tr>
                                <tr><td><strong>Gender</strong></td><td>: ${patient['Gender'] === 'L' ? 'Laki-laki' : patient['Gender'] === 'P' ? 'Perempuan' : '-'}</td></tr>
                                <tr><td><strong>Cara Masuk</strong></td><td>: ${patient['Cara Masuk']} ${patient['Perujuk'] ? ' - ' + patient['Perujuk'] : ''}</td></tr>
                                <tr><td><strong>Asuransi</strong></td><td>: ${patient['Asuransi']}</td></tr>
                                <tr><td><strong>Tgl. Masuk</strong></td><td>: ${patient['Tanggal Masuk']}</td></tr>
                                <tr><td><strong>Tgl. Keluar</strong></td><td>: ${patient['Tanggal Keluar']}</td></tr>
                                <tr><td><strong>Status Inap</strong></td><td>: ${patient['Status Inap']}</td></tr>
                                <tr><td><strong>DPJP</strong></td><td>: ${patient['DPJP']}</td></tr>
                                <tr><td><strong>No Kunj</strong></td><td>: ${patient['No Kunjungan']}</td></tr>
                                <tr><td><strong>Bangsal</strong></td><td>: ${patient['Bangsal'] || '-'}</td></tr>
                                <tr><td><strong>Tgl. Lahir</strong></td><td>: ${patient['Tanggal Lahir']}</td></tr>
                            </table>
                            <button id="reset-search" class="btn btn-sm btn-outline-secondary btn-block mt-2">
                                <i class="fas fa-search mr-1"></i> Cari Pasien Lain
                            </button>
                        `;
                        // Tampilkan di kedua tempat (desktop dan mobile)
                        $('#patient-details-container, #patient-details-container-mobile').html(patientDetailsHtml);

                        // Tampilkan card pemilihan form dan reset state
                        $('#initial-message').hide();
                        $('#form-selection-card').slideDown();

                        // Simpan state ke sessionStorage
                        sessionStorage.setItem('rmeIgdNorm', selectedPatientData.NoRM);
                        sessionStorage.setItem('rmeIgdNoPendaftaran', noPendaftaran);

                        // Cek apakah ada form yang sedang aktif di dropdown.
                        // Jika ada, picu event 'change' untuk memuat ulang form tersebut dengan data pasien baru.
                        // Jika tidak, atau jika form yang aktif tidak lagi tersedia, kosongkan kontainer.
                        if (currentFormName && formOptions.some(opt => opt.value === currentFormName)) {
                            loadRmeForm(currentFormName, formOptions.find(opt => opt.value === currentFormName).url, currentFormText);
                        } else {
                            currentFormName = ''; // Reset jika form tidak lagi valid
                            $('#form-container').html('<div class="text-muted text-center" style="padding: 50px 0;"><p>Silakan pilih formulir untuk ditampilkan.</p></div>');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal mengambil detail pasien.', 'error');
                        $('#patient-details-card, #patient-details-card-mobile').slideUp();
                    }
                });
            });

            // 3. Aksi saat tombol "Cari Pasien Lain" diklik
            $(document).on('click', '#reset-search', function() {
                $('#norm, #norm-mobile').val('').focus();
                const initialHtml = '<p class="text-muted text-center">Silakan cari pasien terlebih dahulu.</p>';
                $('#visit-history-container, #visit-history-container-mobile').html(initialHtml);
                $('#patient-details-card, #patient-details-card-mobile').slideUp();
                $('#visit-history-pagination, #visit-history-pagination-mobile').hide();
                $('#form-selection-card').slideUp();
                $('#form-container').html(`
                    <div id="initial-message" class="text-muted text-center" style="padding: 60px 0;">
                        <p style="font-size: 16px; line-height: 1.7;">
                            👋 <strong>Selamat datang di Formulir Rekam Medis Elektronik (RME)!</strong><br>
                            Untuk memulai, silakan:
                        </p>

                        <ol style="display: inline-block; text-align: left; font-size: 15px; line-height: 1.8; margin: 10px 0 20px;">
                            <li>Masukkan <strong>NORM (Nomor Rekam Medis)</strong> pasien pada kolom pencarian.</li>
                            <li>Pilih <strong>kunjungan pasien</strong> yang ingin Anda buka.</li>
                            <li>Kemudian pilih <strong>formulir RME</strong> yang ingin diisi atau diperbarui sesuai kebutuhan.</li>
                        </ol>

                        <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
                            💡 <em>Tips:</em> 
                        </p>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px;">
                            <li>🖥️ Gunakan perangkat <strong>desktop</strong> atau <strong>tablet</strong> agar tampilan form lebih lengkap dan mudah diisi.</li>
                            <li>📶 Pastikan koneksi internet stabil untuk menghindari kehilangan data saat menyimpan.</li>
                            <li>🖨️ Fitur <strong>lihat/print out</strong> hanya tersedia melalui komputer.</li>
                        </ul>

                        <p style="margin-top: 20px; font-size: 13px; color: #adb5bd;">
                            ⚙️ Sistem ini terintegrasi dengan database RME rumah sakit. Setiap perubahan akan tersimpan otomatis sesuai hak akses pengguna.
                        </p>
                    </div>`);
                selectedNoPendaftaran = null;
                fullPatientDetails = null;
                formFillStatuses = {};
                currentFormText = '';

                // Hapus state dari sessionStorage
                sessionStorage.removeItem('rmeIgdNorm');
                sessionStorage.removeItem('rmeIgdNoPendaftaran');
            });

            // --- REFACTORED FORM LOADING LOGIC ---
            let currentFormName = '';
            function loadRmeForm(formName, url, formTitle) {
                if (formName && !selectedNoPendaftaran) {
                    Swal.fire('Peringatan', 'Silakan pilih kunjungan pasien terlebih dahulu.', 'warning');
                    $('#form-search-input').val(''); // Reset search input
                    return;
                }

                // Jika formName atau URL tidak ada (misal memilih "-- Pilih Formulir --"), hentikan eksekusi.
                if (!formName || !url) {
                currentFormName = '';
                currentFormText = '';
                $('#form-container').html(`
                    <div id="initial-message" class="text-muted text-center" style="padding: 60px 0;">
                        <p style="font-size: 16px; line-height: 1.7;">
                            👋 <strong>Selamat datang di Formulir Rekam Medis Elektronik (RME)!</strong><br>
                            Untuk memulai, silakan:
                        </p>

                        <ol style="display: inline-block; text-align: left; font-size: 15px; line-height: 1.8; margin: 10px 0 20px;">
                            <li>Masukkan <strong>NORM (Nomor Rekam Medis)</strong> pasien pada kolom pencarian.</li>
                            <li>Pilih <strong>kunjungan pasien</strong> yang ingin Anda buka.</li>
                            <li>Kemudian pilih <strong>formulir RME</strong> yang ingin diisi atau diperbarui sesuai kebutuhan.</li>
                        </ol>

                        <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
                            💡 <em>Tips:</em> 
                        </p>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px;">
                            <li>🖥️ Gunakan perangkat <strong>desktop</strong> atau <strong>tablet</strong> agar tampilan form lebih lengkap dan mudah diisi.</li>
                            <li>📶 Pastikan koneksi internet stabil untuk menghindari kehilangan data saat menyimpan.</li>
                            <li>🖨️ Fitur <strong>lihat/print out</strong> hanya tersedia melalui komputer.</li>
                        </ul>

                        <p style="margin-top: 20px; font-size: 13px; color: #adb5bd;">
                            ⚙️ Sistem ini terintegrasi dengan database RME rumah sakit. Setiap perubahan akan tersimpan otomatis sesuai hak akses pengguna.
                        </p>
                    </div>
                `);
                    return;
                }

                currentFormName = formName;
                currentFormText = formTitle;

                // Gabungkan data dasar (NoPendaftaran, NoRM) dengan detail pasien lengkap
                // Ini adalah cara yang paling langsung: kirim data yang sudah ada di JavaScript
                // ke controller form tujuan.
                const data = { 
                    ...{ NoPendaftaran: selectedPatientData.NoPendaftaran, NoRM: selectedPatientData.NoRM },
                    ...fullPatientDetails 
                };

                // Definisikan HTML untuk spinner
                const loaderHtml = `
                    <div id="form-loader" class="text-center" style="padding: 50px 0;">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-3 text-muted">Memuat formulir...</p>
                    </div>`;

                // Kosongkan kontainer dan tampilkan spinner
                $('#form-container').html(loaderHtml);

                // Gunakan AJAX untuk memuat konten form
                $.get(url, data, function(response) {
                    // Buat elemen form dengan jQuery, sembunyikan dulu
                    const $formWrapper = $(`
                        <div class="card card-outline card-primary mt-3" style="display: none;">
                            <div class="card-header">
                                <h3 class="card-title">${formTitle}</h3>
                            </div>
                            ${response}
                        </div>
                    `);

                    // Ganti spinner dengan form yang baru, lalu tampilkan dengan animasi
                    $('#form-container').html($formWrapper);
                    $formWrapper.slideDown(400); // Animasi slide down selama 400ms

                }).fail(function() {
                    Swal.fire('Error', 'Gagal memuat formulir.', 'error');
                    $('#form-container').html('<div class="text-danger text-center" style="padding: 50px 0;"><p>Gagal memuat formulir. Silakan coba lagi.</p></div>');
                    $('#form-search-input').val(''); // Reset search input
                });
            }

            // --- CUSTOM SEARCHABLE DROPDOWN LOGIC ---
            const searchInput = $('#form-search-input');
            const resultsContainer = $('#form-results-container');
            let allPossibleForms = [];
            let highlightedIndex = -1; // Untuk navigasi keyboard
            let formOptions = []; // This will be the dynamically filtered list
            // 1. Parse ALL possible form options from the Blade template into a JS array ONCE.
            function initializeAllForms() {
                $('#form-selector option').each(function() {
                    if ($(this).val()) { // Ignore the first "-- Pilih --" option
                        allPossibleForms.push({
                            value: $(this).val(),
                            text: $(this).text(),
                            url: $(this).data('url'),
                            group: $(this).parent().is('optgroup') ? $(this).parent().attr('label') : null,
                            kelompokUsia: $(this).data('kelompok-usia') || null, // e.g., 'anak', 'dewasa'
                            gender: $(this).data('gender') || null // e.g., 'P'
                        });
                    }
                });
                formOptions = [...allPossibleForms]; // Initially, show all forms allowed by role
            }

            // 2. Function to filter the form list based on patient demographics
            function updateFormOptionsBasedOnPatient(patient) {
                // If the user is an admin, bypass all demographic filters and show all forms.
                if (IS_ADMIN) {
                    formOptions = [...allPossibleForms];
                    return;
                }

                if (!patient) {
                    formOptions = [...allPossibleForms];
                    return;
                }
                const kelompokUsiaPasien = getKelompokUsia(patient['Tanggal Lahir']);
                const genderPasien = patient['Gender'] ? patient['Gender'].trim().toUpperCase() : null;

                formOptions = allPossibleForms.filter(form => {
                    const usiaMatch = !form.kelompokUsia || form.kelompokUsia === kelompokUsiaPasien;
                    const genderMatch = !form.gender || form.gender === genderPasien;
                    return usiaMatch && genderMatch;
                });
            }


            // 2. Render results function
            function renderResults(items) {
                resultsContainer.empty();
                highlightedIndex = -1; // Reset highlight setiap kali render ulang
                if (items.length === 0) {
                    resultsContainer.append('<div class="form-result-item no-results">Formulir tidak ditemukan.</div>');
                    return;
                }

                let currentGroup = null;
                items.forEach(item => {
                    // Render group header jika itu adalah grup baru
                    if (item.group && item.group !== currentGroup) {
                        resultsContainer.append(`<div class="form-result-group">${item.group}</div>`);
                        currentGroup = item.group;
                    }

                    // Tambahkan tanda centang jika formulir sudah diisi
                    const isFilled = formFillStatuses[item.value];
                    const checkmark = isFilled ? ' <i class="fas fa-check-circle text-success ml-2" title="Sudah Terisi"></i>' : '';

                    // Render item formulir
                    const itemHtml = `
                        <div class="form-result-item" data-value="${item.value}" data-url="${item.url}">${item.text}${checkmark}</div>
                    `;
                    resultsContainer.append(itemHtml);
                });
            }

            // 3. Event listeners for the custom search
            searchInput.on('focus', function() {
                // Saat input difokuskan, kosongkan nilainya, ubah placeholder, dan tampilkan semua opsi.
                $(this).val(''); // Mengosongkan input
                $(this).attr('placeholder', 'Ketik untuk mencari formulir...');
                renderResults(formOptions);
                resultsContainer.show();
            });

            searchInput.on('blur', function() {
                // Jika input kosong saat blur, kembalikan teks form yang aktif atau placeholder default.
                if ($(this).val() === '' && currentFormText) {
                    $(this).val(currentFormText);
                    $(this).attr('placeholder', 'Ketik untuk mencari formulir...');
                } else if ($(this).val() === '') {
                    $(this).attr('placeholder', 'Silakan Pilih Form...');
                }
            });

            searchInput.on('input', function() {
                const query = $(this).val().toLowerCase();
                const filteredOptions = formOptions.filter(opt => opt.text.toLowerCase().includes(query));
                renderResults(filteredOptions);
                resultsContainer.show();
            });

            // 3.5. Event listener untuk navigasi keyboard
            searchInput.on('keydown', function(e) {
                const items = resultsContainer.find('.form-result-item:not(.no-results)');
                if (!items.length || resultsContainer.is(':hidden')) {
                    return;
                }

                switch (e.keyCode) {
                    case 40: // Panah Bawah
                        e.preventDefault();
                        highlightedIndex++;
                        if (highlightedIndex >= items.length) {
                            highlightedIndex = 0; // Wrap ke atas
                        }
                        break;
                    case 38: // Panah Atas
                        e.preventDefault();
                        highlightedIndex--;
                        if (highlightedIndex < 0) {
                            highlightedIndex = items.length - 1; // Wrap ke bawah
                        }
                        break;
                    case 13: // Enter
                        e.preventDefault();
                        if (highlightedIndex > -1) {
                            items.eq(highlightedIndex).trigger('click');
                        }
                        return; // Hentikan eksekusi lebih lanjut
                    case 27: // Escape
                        resultsContainer.hide();
                        return;
                    default:
                        return; // Jangan lakukan apa-apa untuk tombol lain
                }

                // Update UI highlight
                items.removeClass('highlighted');
                const highlightedItem = items.eq(highlightedIndex);
                if (highlightedItem.length) {
                    highlightedItem.addClass('highlighted');

                    // Auto-scroll container jika item di luar viewport
                    const container = resultsContainer[0];
                    const itemTop = highlightedItem.position().top;
                    const itemBottom = itemTop + highlightedItem.outerHeight();
                    const containerHeight = container.clientHeight;
                    const scrollTop = container.scrollTop;

                    if (itemBottom > containerHeight) {
                        container.scrollTop = scrollTop + itemBottom - containerHeight;
                    } else if (itemTop < 0) {
                        container.scrollTop = scrollTop + itemTop;
                    }
                }
            });

            // Hide results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.custom-form-search').length) {
                    // Jika input kosong saat klik di luar, kembalikan teks form yang aktif atau placeholder default.
                    if (searchInput.val() === '' && currentFormText) {
                        searchInput.val(currentFormText);
                        searchInput.attr('placeholder', 'Ketik untuk mencari formulir...');
                    } else if (searchInput.val() === '') {
                        searchInput.attr('placeholder', 'Silakan Pilih Form...');
                    }
                    resultsContainer.hide();
                }
            });

            // Handle click on a result item
            resultsContainer.on('click', '.form-result-item', function() {
                if ($(this).hasClass('no-results')) return;

                const value = $(this).data('value');
                const url = $(this).data('url');
                const text = $(this).text();

                currentFormText = text; // Simpan teks form yang dipilih
                searchInput.val(text); // Set input text to the selected form name
                resultsContainer.hide();

                // Trigger the form loading logic
                loadRmeForm(value, url, text);
            });
            // --- END OF CUSTOM SEARCHABLE DROPDOWN LOGIC ---
            
            // --- GLOBAL AJAX EVENT LISTENER UNTUK REFRESH FORM SECARA OTOMATIS ---
            $(document).ajaxSuccess(function(event, xhr, settings) {
                // Cek apakah request ini adalah metode POST (umumnya untuk menyimpan data)
                // dan apakah respons dari server memiliki status 'success'.
                // Ini adalah cara cerdas untuk mendeteksi aksi simpan/update yang berhasil.
                if (settings.type.toUpperCase() === 'POST' && xhr.responseJSON && xhr.responseJSON.status === 'success') {
                    
                    // Cek apakah ada form yang sedang aktif.
                    if (currentFormName && formOptions.some(opt => opt.value === currentFormName)) {
                        console.log(`Form save detected for "${currentFormName}". Refreshing...`);
                        
                        // Temukan detail form yang aktif.
                        const activeForm = formOptions.find(opt => opt.value === currentFormName);
                        
                        // Muat ulang form tersebut untuk mendapatkan data terbaru.
                        // Kita beri sedikit jeda agar notifikasi SweetAlert sempat terlihat.
                        setTimeout(() => {
                            loadRmeForm(activeForm.value, activeForm.url, activeForm.text);
                        }, 500); // Jeda 0.5 detik
                    }
                }
            });

            // Initialize the form lists
            initializeAllForms();

            // 5. Fungsi untuk memulihkan state saat halaman dimuat
            function restoreState() {
                const storedNorm = sessionStorage.getItem('rmeIgdNorm');
                const storedNoPendaftaran = sessionStorage.getItem('rmeIgdNoPendaftaran');

                if (storedNorm && storedNoPendaftaran) {
                    console.log('Restoring state for NoRM:', storedNorm, 'and NoPendaftaran:', storedNoPendaftaran);

                    // Isi input (keduanya) dan trigger pencarian di input desktop
                    $('#norm, #norm-mobile').val(storedNorm);
                    $('#norm').trigger('input');

                    // Karena AJAX bersifat async, kita perlu menunggu sampai tabel riwayat dimuat
                    // lalu kita bisa "klik" tombol pilih yang sesuai.
                    $(document).ajaxComplete(function(event, xhr, settings) {
                        // Cek apakah ini adalah response dari searchVisits
                        if (settings.url.includes("{{ route('rme.igd.searchVisits') }}") && xhr.status === 200) {
                            const targetRow = $(`tr.selectable-row[data-nopendaftaran="${storedNoPendaftaran}"]`);
                            if (targetRow.length) {
                                console.log('Found visit row, clicking...');
                                targetRow.click();
                                
                                // Setelah klik, proses restore selesai.
                                // Hentikan listener ajaxComplete agar tidak berjalan lagi untuk request lain.
                                $(document).off('ajaxComplete');
                            }
                        }
                    });
                }
            }

            // Panggil fungsi restore saat dokumen siap
            restoreState();

            // 6. Hapus sessionStorage saat menavigasi keluar dari halaman RME
            // Ini menargetkan link di sidebar atau breadcrumb.
            // Selector `:not` digunakan untuk mengecualikan link/aksi di dalam konten RME itu sendiri.
            $('a.nav-link:not(.active), .breadcrumb-item a').on('click', function() {
                console.log('Navigating away from RME page, clearing session storage.');
                sessionStorage.removeItem('rmeIgdNorm');
                sessionStorage.removeItem('rmeIgdNoPendaftaran');
            });

            // 9. Aksi untuk tombol "Lihat Form di Aplikasi Native"
            // This script is for the main RME IGD page, not specific forms.
            $('#lihatFormBtn').click(function() {
                // Gunakan variabel JavaScript yang sudah menyimpan No Pendaftaran
                if (!selectedNoPendaftaran) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih kunjungan pasien terlebih dahulu!'
                    });
                    return;
                }
                // Panggil protocol handler "omb://" dengan No Pendaftaran
                window.location.href = "omb://" + encodeURIComponent(selectedNoPendaftaran);
            });

            // 7. Aksi untuk tombol "Hasil Lab" dan "Hasil Rad"
            $(document).on('click', '.btn-load-penunjang', function() {
                const type = $(this).data('type');
                const modal = $('#penunjangModal');
                const modalBody = $('#penunjangModalBody');
                const modalTitle = $('#penunjangModalLabel');

                if (!selectedNoPendaftaran) {
                    Swal.fire('Peringatan', 'Silakan pilih kunjungan pasien terlebih dahulu.', 'warning');
                    return;
                }

                let url = '';
                let title = '';
                if (type === 'lab') {
                    url = "{{ route('rme.igd.penunjang.lab') }}";
                    title = '<i class="fas fa-flask mr-2"></i> Hasil Pemeriksaan Laboratorium';
                } else if (type === 'rad') {
                    url = "{{ route('rme.igd.penunjang.rad') }}";
                    title = '<i class="fas fa-x-ray mr-2"></i> Hasil Pemeriksaan Radiologi';
                }

                modalTitle.html(title);
                modalBody.html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">Memuat data...</p></div>');
                modal.modal('show');

                $.get(url, { NoPendaftaran: selectedNoPendaftaran })
                    .done(function(response) {
                        modalBody.html(response);
                    })
                    .fail(function() {
                        modalBody.html('<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>');
                    });
            });


        });
    </script>
@endpush
