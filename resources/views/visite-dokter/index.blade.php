@php
    // Define access roles for cleaner Blade syntax
    $access = strtoupper(session('user.access') ?? '');
    $isAdmin = $access === 'ADMIN'; // Assuming 'ADMIN' is the role name for admin
    $isDokterUmum = $access === 'DOKTER UMUM';    $isBidan = $access === 'BIDAN';
    $isPerawat = $access === 'PERAWAT';
    $isGizi = $access === 'GIZI';
    $isDokter = in_array($access, ['DOKTER UMUM', 'DOKTER']); // Assuming 'DOKTER' is for specialists
    $isPerawatBidan = in_array($access, ['PERAWAT', 'BIDAN']);
@endphp

@extends('layouts.visite-dokter')

@section('title', 'Visite Dokter')

@section('rme-sidebar')
    {{-- CARD PENCARIAN PASIEN --}}
    <div id="search-card" class="card card-primary card-outline sticky-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search mr-1"></i>
                Pencarian Pasien
            </h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="search_term">No. RM / Nama Pasien</label>
                <div class="input-group">
                    <input type="text" id="search_term" name="search_term" class="form-control js-search-term" placeholder="Ketik No.RM atau Nama">
                    <div class="input-group-append">
                        <span class="input-group-text js-search-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="doctor_filter">Filter per Dokter</label>
                <select id="doctor_filter" class="form-control select2-dokter" style="width: 100%;">
                    <option value="">-- Semua Dokter --</option>
                    @forelse ($doctors as $doctor)
                        <option value="{{ $doctor->NamaPemeriksa }}">{{ $doctor->NamaPemeriksa }}</option>
                    @empty
                        <option value="" disabled>Data dokter tidak ditemukan</option>
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    {{-- CARD DAFTAR PASIEN --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bed"></i> Daftar Pasien
            </h3>
        </div>
        <div class="card-body p-0">
            <div id="patient-list-container" class="table-responsive js-patient-list-container" style="max-height: 50vh; overflow-y: auto;">
                <p class="text-muted text-center p-3">Silakan lakukan pencarian untuk menampilkan daftar pasien.</p>
            </div>
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
                    <label for="search_term_mobile">No. RM / Nama Pasien</label>
                    <div class="input-group">
                        <input type="text" id="search_term_mobile" name="search_term_mobile" class="form-control js-search-term" placeholder="Ketik No.RM atau Nama">
                        <div class="input-group-append">
                            <span class="input-group-text js-search-spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="doctor_filter_mobile">Filter per Dokter</label>
                    <select id="doctor_filter_mobile" class="form-control select2-dokter" style="width: 100%;">
                        <option value="">-- Semua Dokter --</option>
                        @forelse ($doctors as $doctor)
                            <option value="{{ $doctor->NamaPemeriksa }}">{{ $doctor->NamaPemeriksa }}</option>
                        @empty
                            <option value="" disabled>Data dokter tidak ditemukan</option>
                        @endforelse
                    </select>
                </div>
                {{-- Daftar Pasien Mobile --}}
                <div id="patient-list-container-mobile" class="mt-3 js-patient-list-container" style="max-height: 40vh; overflow-y: auto;">
                    <p class="text-muted text-center p-3">Silakan lakukan pencarian untuk menampilkan daftar pasien.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    {{-- Area konten utama untuk form RME --}}
    <div class="content-header" id="main-content-header" style="display: none;">
        <div class="container-fluid">
            <h1 class="m-0">Visite Dokter</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            {{-- CARD DETAIL PASIEN (KONTEN UTAMA) --}}
            <div id="patient-details-card" class="card card-primary card-outline" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-injured mr-1"></i>
                        Detail Pasien
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body js-patient-details-container" id="patient-details-container">
                    {{-- Konten detail pasien akan dimuat di sini oleh AJAX --}}
                    <p class="text-muted text-center p-5">Klik salah satu pasien dari daftar di samping untuk melihat detailnya.</p>
                </div>
            </div>

            {{-- CARD AKSI PASIEN --}}
            <div id="patient-actions-card" class="card card-primary card-outline" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-1"></i>
                        Aksi & Pemeriksaan
                    </h3>
                </div>
                <div class="card-body" id="patient-actions-container">
                    {{-- Tombol aksi akan dimuat di sini oleh AJAX --}}
                    <p class="text-muted text-center p-3">Pilih pasien untuk melihat opsi.</p>
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

            {{-- Modal untuk Cek Obat --}}
            <div class="modal fade" id="CekObatModal" tabindex="-1" aria-labelledby="cekObatModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="cekObatModalLabel">
                                <i class="fas fa-pills mr-2"></i> Lembar Perincian Bon
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-white">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="obatResultsContainer">
                            {{-- Konten hasil obat akan dimuat di sini oleh AJAX --}}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }
        .patient-row-clickable {
            cursor: pointer;
        }
    </style>
@endpush

@push('scripts')
<script>

// Setup CSRF Token untuk semua request AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function() {
    // Inisialisasi Select2 untuk dropdown dokter
    $('.select2-dokter').select2({
        theme: 'bootstrap4',
        placeholder: '-- Pilih Dokter --',
        allowClear: true
    });

    let selectedPatientData = null; // Variabel untuk menyimpan data pasien terpilih

    // Membersihkan input text jika dropdown dokter dipilih, dan sebaliknya.
    $('#doctor_filter').on('change', function() {
        const doctorName = $(this).val();
        $('.js-search-term').val(''); // Kosongkan semua input text
        $('#doctor_filter_mobile').val(doctorName).trigger('change.select2'); // Sinkronkan dropdown mobile
    });
    $('#doctor_filter_mobile').on('change', function() {
        const doctorName = $(this).val();
        $('.js-search-term').val(''); // Kosongkan semua input text
        $('#doctor_filter').val(doctorName).trigger('change.select2'); // Sinkronkan dropdown desktop
    });

    // Fungsi untuk melakukan pencarian via AJAX
    function searchPatients(searchTerm, searchType = 'text') {
        // [MODIFIKASI] Sembunyikan detail pasien dan header saat pencarian baru dimulai
        // untuk membersihkan tampilan dari data sebelumnya.
        $('#patient-details-card').slideUp();
        $('#patient-actions-card').slideUp(); // Sembunyikan card aksi juga
        $('#main-content-header').slideUp();

        const loadingHtml = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari daftar pasien...</div>';
        const patientListContainers = $('.js-patient-list-container'); // Kontainer daftar pasien

        // [MODIFIKASI] Tampilkan spinner hanya di input teks jika pencarian berdasarkan teks
        if (searchType === 'text') {
            $('.js-search-spinner').show();
        }
        
        patientListContainers.html(loadingHtml);

        $.ajax({
            url: "{{ route('visite-dokter.searchPatients') }}",
            type: 'GET',
            data: { search: searchTerm.trim() },
            success: function(response) {
                let html = '';
                if (response.length > 0) {
                    html += '<table class="table table-bordered table-hover table-striped table-sm">';
                    html += '<thead class="table-secondary"><tr><th>Bangsal</th><th>Nama Pasien</th><th>No. RM</th><th>Tgl. Masuk</th><th>Lama Inap</th><th>kelas</th><th>Dokter</th></tr></thead>';
                    html += '<tbody>';
                    response.forEach(function(patient) {
                        html += `<tr class="patient-row-clickable" data-norm="${patient.NoRM}" data-nopendaftaran="${patient.NoPendaftaran}">
                            <td>${patient.NamaRuang || ''}</td>
                            <td>${patient.NamaPasien || ''}</td>
                            <td>${patient.NoRM || ''}</td>
                            <td>${
                            patient.TanggalMasuk 
                            ? new Date(patient.TanggalMasuk).toLocaleDateString('id-ID') 
                            : ''
                            }</td>
                            <td>${patient.JumlahHari || '0'} hari</td>
                            <td>${patient.Kelas || ''}</td>
                            <td>${patient.NamaPemeriksa || ''}</td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                } else {
                    html = '<p class="text-muted text-center">Tidak ada pasien yang ditemukan.</p>';
                }
                patientListContainers.html(html); // Tampilkan hasil di semua kontainer
            },
            error: function(xhr) {
                patientListContainers.html('<div class="alert alert-danger text-center">Terjadi kesalahan saat mengambil data.</div>');
            },
            complete: function() {
                if (searchType === 'text') {
                    $('.js-search-spinner').hide(); // Sembunyikan spinner di input teks
                }
            }
        });
    }

    // Event handler untuk klik pada baris pasien
    $(document).on('click', '.patient-row-clickable', function() {
        const norm = $(this).data('norm');
        const noPendaftaran = $(this).data('nopendaftaran');

        // Simpan data pasien yang dipilih
        selectedPatientData = { norm: norm, noPendaftaran: noPendaftaran };
        
        // Beri highlight pada baris yang diklik
        $('.patient-row-clickable').removeClass('table-primary');
        $(this).addClass('table-primary');
        
        // Tampilkan card detail dengan status loading
        const detailContainer = $('#patient-details-container');
        const actionsContainer = $('#patient-actions-container');

        if ($('#patient-details-card').is(':hidden')) {
            $('#main-content-header').slideDown(); // Tampilkan judul "Visite Dokter"
            $('#patient-details-card').slideDown();
            $('#patient-actions-card').slideDown(); // Tampilkan card aksi
        }
        detailContainer.html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3 text-muted">Memuat detail pasien...</p></div>');

        $.ajax({
            url: "{{ route('visite-dokter.getPatientDetails') }}",
            type: 'GET',
            data: { norm: norm },
            success: function(details) {
                if (details) {
                    // [MODIFIKASI] Menggunakan grid layout untuk detail pasien
                    const detailsHtml = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. RM</label>
                                    <input type="text" class="form-control" value="${details.Norm || '-'}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pasien</label>
                                    <input type="text" class="form-control" value="${details.NamaPasien || '-'}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Usia</label>
                                    <input type="text" class="form-control" value="${details.Usia || '-'}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <input type="text" class="form-control" value="${details.JenisKelamin || '-'}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" rows="2" readonly>${details.Alamat || '-'}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Asuransi / Mitra</label>
                            <input type="text" class="form-control" value="${details.NamaMitra || '-'}" readonly>
                        </div>
                    `;
                    detailContainer.html(detailsHtml);

                    // [MODIFIKASI] Isi card aksi dengan tombol-tombol
                    const actionsHtml = `
                        <div class="row">
                            <div class="col-lg-2 col-md-4 mb-2">
                                <button type="button" class="btn btn-danger btn-block" id="btn-cek-triage">
                                    <i class="fas fa-heartbeat mr-2"></i> Cek Triage
                                </button>
                            </div>
                            <div class="col-lg-2 col-md-4 mb-2">
                                <button type="button" class="btn btn-primary btn-block btn-load-penunjang" data-type="lab">
                                    <i class="fas fa-flask mr-2"></i> Cek Lab
                                </button>
                            </div>
                            <div class="col-lg-2 col-md-4 mb-2">
                                <button type="button" class="btn btn-warning btn-block btn-load-penunjang text-white" data-type="rad">
                                    <i class="fas fa-x-ray mr-2"></i> Cek Radiologi
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button type="button" class="btn btn-success btn-block" id="btn-cek-obat">
                                    <i class="fas fa-pills mr-2"></i> Cek Obat
                                </button>
                            </div>
                             <div class="col-lg-2 col-md-4 mb-2">
                                <button type="button" class="btn btn-info btn-block" disabled>
                                    <i class="fas fa-notes-medical mr-2"></i> Cek PA
                                </button>
                            </div>
                        </div>
                    `;
                    actionsContainer.html(actionsHtml);
                } else {
                    detailContainer.html('<div class="alert alert-warning">Detail pasien tidak ditemukan.</div>');
                    actionsContainer.html('<p class="text-muted text-center p-3">Pilih pasien untuk melihat opsi.</p>');
                }
            },
            error: function() {
                detailContainer.html('<div class="alert alert-danger">Gagal memuat detail pasien.</div>');
            }
        });
    });

    // --- [MODIFIKASI] Pencarian dinamis pada input text ---
    let searchDebounce = null;
    $('.js-search-term').on('keyup', function(e) {
        clearTimeout(searchDebounce);
        const searchTerm = $(this).val().trim();

        // Sinkronkan nilai antar input
        $('.js-search-term').val(searchTerm);

        // 1. Trigger pencarian saat menekan tombol Enter
        if (e.keyCode === 13) { // 13 adalah kode untuk tombol Enter
            if (searchTerm !== '') {
                // [MODIFIKASI] Reset dropdown dokter tanpa memicu event 'change' yang salah
                $('.select2-dokter').val(null).trigger('change.select2');
                searchPatients(searchTerm);
            }
            return; // Hentikan eksekusi lebih lanjut
        }

        // 2. Trigger pencarian otomatis setelah mengetik (dengan debounce)
        searchDebounce = setTimeout(() => {
            if (searchTerm.length >= 3) { // Minimal 3 karakter
                $('.select2-dokter').val(null).trigger('change.select2');
                searchPatients(searchTerm);
            }
        }, 500); // Jeda 500ms setelah user berhenti mengetik
    });

    // Event handler saat memilih dokter dari dropdown
    $('.select2-dokter').on('select2:select', function (e) {
        let doctorName = e.params.data.id;
        if (doctorName) {
            $('.js-search-term').val(''); // Kosongkan semua input text
            searchPatients(doctorName, 'doctor'); // Lakukan pencarian dengan tipe 'doctor'
        }
    });

    // Logika untuk auto-search jika user adalah dokter
    $(function() {
        const userAccess = "{{ strtoupper(session('user.access') ?? '') }}";
        const isDokter = ['DOKTER', 'DOKTER UMUM'].includes(userAccess);
        
        if (isDokter) {
            const namaPemeriksa = "{{ addslashes(session('user.namapemeriksa') ?? '') }}";
            if (namaPemeriksa) {
                // Set semua dropdown ke nama dokter yang login dan picu pencarian
                $('#doctor_filter').val(namaPemeriksa).trigger('change');
                searchPatients(namaPemeriksa, 'doctor');
            }
        }
    });

    // Aksi untuk tombol "Hasil Lab" dan "Hasil Rad"
    $(document).on('click', '.btn-load-penunjang', function(e) {
        e.preventDefault();
        const type = $(this).data('type');
        const modal = $('#penunjangModal');
        const modalBody = $('#penunjangModalBody');
        const modalTitle = $('#penunjangModalLabel');

        if (!selectedPatientData || !selectedPatientData.noPendaftaran) {
            Swal.fire('Peringatan', 'Silakan pilih pasien terlebih dahulu.', 'warning');
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

        $.get(url, { NoPendaftaran: selectedPatientData.noPendaftaran })
            .done(function(response) {
                modalBody.html(response);
            })
            .fail(function() {
                modalBody.html('<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>');
            });
    });

    // Aksi untuk tombol "Cek Obat"
    $(document).on('click', '#btn-cek-obat', function(e) {
        e.preventDefault();
        const modal = $('#CekObatModal');
        const modalBody = $('#obatResultsContainer');
 
        if (!selectedPatientData || !selectedPatientData.noPendaftaran) {
            Swal.fire('Peringatan', 'Silakan pilih pasien terlebih dahulu.', 'warning');
            return;
        }
 
        const url = "{{ route('rme.igd.penunjang.obat') }}";
 
        modalBody.html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">Memuat data obat...</p></div>');
        modal.modal('show');
 
        $.get(url, { NoPendaftaran: selectedPatientData.noPendaftaran })
            .done(function(response) {
                modalBody.html(response);
            })
            .fail(function() {
                modalBody.html('<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>');
            });
    });

    // Aksi untuk tombol "Cek Triage"
    $(document).on('click', '#btn-cek-triage', function(e) {
        e.preventDefault();
        const modal = $('#penunjangModal'); // Kita bisa pakai modal yang sama
        const modalBody = $('#penunjangModalBody');
        const modalTitle = $('#penunjangModalLabel');

        if (!selectedPatientData || !selectedPatientData.noPendaftaran) {
            Swal.fire('Peringatan', 'Silakan pilih pasien terlebih dahulu.', 'warning');
            return;
        }

        const url = "{{ route('rme.igd.form.rm3a.load') }}";
        const title = '<i class="fas fa-heartbeat mr-2"></i> Hasil Triage Gawat Darurat (RM3A)';

        modalTitle.html(title);
        modalBody.html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">Memuat data triage...</p></div>');
        modal.modal('show');

        $.get(url, { NoPendaftaran: selectedPatientData.noPendaftaran, NoRM: selectedPatientData.norm, readonly: true })
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
