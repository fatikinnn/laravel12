<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'RSUI Mutiara Bunda')</title>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <!-- Lightbox2 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
  
  @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-navbar-fixed layout-fixed layout-footer-fixed" >
<div class="wrapper">
    <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('img/logo.png') }}" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  @include('layouts.partials._navbar')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  @include('layouts.partials._sidebar')

  {{-- Form Logout tersembunyi, akan dipicu oleh JavaScript --}}
  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
      @csrf
  </form>

  {{-- Include Profile Modal --}}
  @include('layouts.partials._profile_modal')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @yield('content-header')
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        @yield('content')
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
   @include('layouts.partials._footer')
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- DataTables Buttons & other plugins -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Script untuk menampilkan notifikasi SweetAlert dari session --}}
<script>
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
</script>

{{-- Script untuk konfirmasi logout --}}
<script>
    $(document).on('click', '#logout-button', function(e) {
        e.preventDefault(); // Mencegah link default beraksi

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
                // Jika dikonfirmasi, submit form logout
                document.getElementById('logout-form').submit();
            }
        });
    });
</script>

{{-- Script untuk Profile Modal --}}
<script>
    $(document).ready(function() {
        // 1. Buka Modal dan Ambil Data
        $('#profile-button').on('click', function(e) {
            e.preventDefault();

            // Reset form dan error messages
            $('#profileUpdateForm')[0].reset();
            $('#profile-form-errors').hide().html('');
            $('.is-invalid').removeClass('is-invalid');

            $.ajax({
                url: "{{ route('profile.show') }}",
                type: 'GET',
                beforeSend: function() {
                    // Tampilkan loading atau disable tombol jika perlu
                },
                success: function(data) {
                    $('#profile_username').val(data.username);
                    $('#profile_namapemeriksa').val(data.namapemeriksa);
                    $('#profile_email').val(data.email);
                    $('#profile_nik').val(data.nik);
                    $('#profileModal').modal('show');
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memuat data profil.', 'error');
                }
            });
        });

        // 2. Submit Form Update
        $('#profileUpdateForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('profile.update') }}",
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('#saveProfileBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                    $('#profile-form-errors').hide().html('');
                     $('.is-invalid').removeClass('is-invalid');
                },
                success: function(response) {
                    $('#profileModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // Validation error
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                            $('#profile_' + key).addClass('is-invalid');
                        });
                        errorHtml += '</ul>';
                        $('#profile-form-errors').html(errorHtml).show();
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan saat memperbarui profil.', 'error');
                    }
                },
                complete: function() {
                    $('#saveProfileBtn').prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Simpan Perubahan');
                }
            });
        });
    });
</script>

@stack('scripts')
</body>
</html>