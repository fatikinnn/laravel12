<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RME - RSUI Mutiara Bunda')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.3.2/dist/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <style>
        /* Palet Warna Kustom RS */
        :root {
            --rs-primary: #005A9C; /* Biru Tua */
            --rs-danger: #D8292F;  /* Merah */
            --rs-light: #f8f9fa;   /* Putih Keabuan */
            --rs-white: #FFFFFF;   /* Putih Bersih */
        }

        /* Mengganti warna default AdminLTE */
        .bg-primary, .btn-primary, .card-primary.card-outline {
            border-color: var(--rs-primary) !important;
        }
        .btn-primary, .bg-primary {
            background-color: var(--rs-primary) !important;
        }
        body {
            overflow-x: hidden;
        }

        /* --- Transisi Animasi Halus --- */
        /* Terapkan transisi ke elemen yang akan dianimasikan */
        .rme-layout .main-sidebar,
        .rme-layout .content-wrapper,
        .rme-layout .main-header,
        .rme-layout .main-footer {
            transition: margin-left .3s ease-in-out, width .3s ease-in-out !important;
        }
        /* --- Responsive RME Sidebar --- */
        /* Default untuk layar besar (di atas 1200px) */
        @media (min-width: 1200px) {
            .rme-layout:not(.sidebar-collapse) .main-sidebar { width: 400px !important; }
            .rme-layout:not(.sidebar-collapse) .content-wrapper,
            .rme-layout:not(.sidebar-collapse) .main-header,
            .rme-layout:not(.sidebar-collapse) .main-footer { margin-left: 400px !important; }
        }

        /* Untuk tablet dan laptop kecil (992px - 1199px) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .rme-layout:not(.sidebar-collapse) .main-sidebar { width: 350px !important; }
            .rme-layout:not(.sidebar-collapse) .content-wrapper,
            .rme-layout:not(.sidebar-collapse) .main-header,
            .rme-layout:not(.sidebar-collapse) .main-footer { margin-left: 350px !important; }
        }
        /* AdminLTE default breakpoint untuk sidebar overlay adalah 991.98px.
           Di bawah ukuran ini, sidebar akan menjadi overlay dan tidak memerlukan
           lebar atau margin khusus, sehingga kita tidak perlu mendefinisikan
           aturan untuk layar yang lebih kecil dari 992px.
        */
        /* --- End of Responsive RME Sidebar --- */

        /* --- Fully Collapsed Sidebar on Desktop --- */
        /* Aturan ini hanya berlaku untuk layar desktop (lebar > 991.98px) */
        @media (min-width: 992px) {
            .rme-layout.sidebar-collapse .content-wrapper,
            .rme-layout.sidebar-collapse .main-header,
            .rme-layout.sidebar-collapse .main-footer {
                /* Hapus margin kiri agar konten mengisi layar */
                margin-left: 0 !important;
            }
            .rme-layout.sidebar-collapse .main-sidebar {
                /* Animasikan lebar menjadi 0, jangan gunakan display:none */
                width: 0 !important;
                overflow: hidden; /* Sembunyikan konten saat sidebar menyusut */
            }
        }

        /* --- Mobile Control Panel --- */
        #mobile-control-panel {
            display: none; /* Sembunyikan di desktop secara default */
        }
        @media (max-width: 991.98px) {
            .main-sidebar { display: none !important; } /* Sembunyikan sidebar asli di mobile */
            #mobile-control-panel { display: block; } /* Tampilkan panel kontrol mobile */
        }

        /* --- Center Navbar Brand --- */
        .navbar-brand-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        @media (max-width: 575.98px) {
            .navbar-brand-center { display: none; } /* Sembunyikan di layar xs jika tumpang tindih */
        }
        /* --- End of Center Navbar Brand --- */
     </style>
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed rme-layout layout-footer-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            {{-- Tombol ini sekarang hanya muncul di layar besar (desktop) untuk mengontrol sidebar RME --}}
            <li class="nav-item d-none d-lg-block">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            {{-- Link Dashboard ini juga hanya muncul di layar besar (desktop) --}}
            <li class="nav-item d-none d-lg-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link" title="Kembali ke Dashboard">
                    Dashboard
                </a>
            </li>
        </ul>

        <!-- Brand Center -->
        <a href="#" class="navbar-brand-center d-flex align-items-center">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" width="30" height="30" class="brand-image-xl">
            <span class="brand-text font-weight-bold d-none d-sm-inline ml-2">
                RSUI MUTIARA BUNDA
            </span>
        </a>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-circle"></i>
                    <span class="d-none d-md-inline ml-1">{{ session('user.namapemeriksa') ?? session('user.username') }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="{{ route('dashboard') }}" class="dropdown-item">
                        Dashboard
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" id="logout-button-rme" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-light-primary elevation-4">
        <!-- Sidebar -->
        <div class="sidebar">
            @yield('rme-sidebar')
        </div>
        <!-- /.sidebar -->
    </aside>

    {{-- Form Logout tersembunyi --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        {{-- Kontainer ini hanya akan muncul di layar kecil (<992px) --}}
        <div id="mobile-control-panel">
            @yield('rme-control-panel')
        </div>

        @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
        <span id="digital-date"></span> | 
        <span id="digital-clock"></span>
    </div>
        <strong>Copyright &copy; {{ date('Y') }} <a href="#">IT RSUI Mutiara Bunda</a>.</strong>
        All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Script untuk konfirmasi logout --}}
<script>
    $(document).on('click', '#logout-button-rme', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan keluar dari sesi ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    });

    // SweetAlert Notif
    $(function() {
        @if (session('swal-success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('swal-success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        @if (session('swal-error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('swal-error') }}'
            });
        @endif
    });
    function updateDateTime() {
        const now = new Date();

        const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                       "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const tanggal = `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;

        const jam = now.toLocaleTimeString('id-ID', { hour12: false });

        document.getElementById('digital-date').textContent = tanggal;
        document.getElementById('digital-clock').textContent = jam;
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>

{{-- Script untuk membuat footer responsif --}}
<script>
    function adjustLayoutForScreenSize() {
        const isMobile = $(window).width() < 992;

        // Toggle footer fixed
        if (isMobile) {
            if ($('body').hasClass('layout-footer-fixed')) {
                $('body').removeClass('layout-footer-fixed');
            }
        } else {
            if (!$('body').hasClass('layout-footer-fixed')) {
                $('body').addClass('layout-footer-fixed');
            }
        }

        // Toggle sidebar vs mobile panel
        // Tidak perlu lagi karena sudah ditangani oleh CSS media query
    }

    $(document).ready(function() {
        adjustLayoutForScreenSize();
        $(window).on('resize', adjustLayoutForScreenSize);
    });
</script>

{{-- Script untuk auto-logout karena tidak ada aktivitas --}}
<script>
    $(document).ready(function() {
        let inactivityTimer;

        // Durasi timeout dalam milidetik. 5 jam untuk testing.
        const inactivityTimeout = 5 * 60 * 60 * 1000;

        // Fungsi yang akan dijalankan saat sesi dianggap berakhir karena inaktivitas
        function logoutUser() {
            // Hentikan timer untuk mencegah notifikasi ganda
            clearTimeout(inactivityTimer);

            // Cek apakah notifikasi sudah ditampilkan untuk menghindari duplikasi
            if ($('.swal2-container').is(':visible')) {
                return;
            }

            Swal.fire({
                title: 'Sesi Anda Telah Berakhir',
                text: 'Anda tidak melakukan aktivitas selama 5 jam. Demi keamanan, Anda akan diarahkan ke halaman login.',
                icon: 'warning',
                confirmButtonText: 'OK',
                allowOutsideClick: false, // Mencegah user menutup notifikasi
                timer: 10000, // Notifikasi akan tertutup otomatis setelah 10 detik
                timerProgressBar: true
            }).then((result) => {
                // Arahkan ke URL logout. Menggunakan location.assign() dan kemudian
                // location.href memastikan navigasi penuh ke halaman login yang baru.
                window.location.href = "{{ route('logout') }}";
            });
        }

        // Fungsi untuk mereset timer setiap ada aktivitas
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(logoutUser, inactivityTimeout);
        }

        // Mulai timer saat halaman dimuat
        resetInactivityTimer();

        // Reset timer pada setiap aktivitas pengguna (gerakan mouse, keyboard, klik, scroll)
        $(document).on('mousemove keydown click scroll', resetInactivityTimer);
    });
</script>

{{-- Script Proaktif untuk Pengecekan & Penanganan Sesi Habis --}}
<script>
    $(document).ready(function() {
        let sessionCheckInterval;
        const pingUrl = "{{ route('session.ping') }}";
        let isLoggingOut = false; // Flag untuk mencegah notifikasi ganda

        // Fungsi untuk menampilkan notifikasi dan logout
        function handleExpiredSession() {
            // Jika proses logout sudah berjalan, jangan lakukan apa-apa
            if (isLoggingOut) {
                return;
            }
            isLoggingOut = true; // Set flag

            // Hentikan pengecekan sesi lebih lanjut
            clearInterval(sessionCheckInterval);

            Swal.fire({
                title: 'Sesi Habis',
                text: 'Sesi login Anda telah berakhir. Untuk keamanan, Anda akan diarahkan kembali ke halaman login.',
                icon: 'warning',
                confirmButtonText: 'OK',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                // Paksa muat ulang halaman. Karena sesi sudah tidak valid di server,
                // middleware 'auth.custom' Laravel akan secara otomatis mengarahkan
                // ke halaman login dengan CSRF token yang baru.
                window.location.reload(true);
            });
        }

        // 1. Handler Reaktif: Menangkap error AJAX dari aksi pengguna
        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            // Jangan picu jika error berasal dari ping kita sendiri (untuk menghindari loop)
            if (settings.url === pingUrl) {
                return;
            }

            // Status 401 (Unauthorized) atau 419 (CSRF Token Mismatch / Session Expired)
            if (jqxhr.status === 401 || jqxhr.status === 419) {
                handleExpiredSession();
            }
        });

        // 2. Handler Proaktif: Pengecekan sesi secara berkala (setiap 2 menit)
        sessionCheckInterval = setInterval(function() {
            $.get(pingUrl).fail(function(jqxhr) {
                if (jqxhr.status === 401 || jqxhr.status === 419) {
                    handleExpiredSession();
                }
            });
        }, 120000); // 120000 ms = 2 menit

    });
</script>

@stack('scripts')
</body>
</html>