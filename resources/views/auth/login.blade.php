<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RSUI Mutiara Bunda | Log in</title>
  <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page" style="background-image: url('{{ asset('img/bg.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
<div class="login-box">
  <div class="login-logo">
    <a href="#">
        <img src="{{ asset('img/logo.png') }}" alt="Logo RS" style="width:100px; height:auto;">
        <br>
        <b>RSUI</b> Mutiara Bunda
    </a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="loginForm" action="{{ route('login') }}" method="post">
        @csrf
        <div class="input-group mb-3">
          <input type="text" name="Username" id="username" class="form-control @error('Username') is-invalid @enderror" placeholder="Username" value="{{ old('Username') }}" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
          @error('Username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>
        <div class="input-group mb-3">
          <input type="password" name="Password" id="password" class="form-control @error('Password') is-invalid @enderror" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
          @error('Password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" id="loginBtn" class="btn btn-primary btn-block">Masuk</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Hapus kelas error saat pengguna mulai mengetik lagi
    $('#username, #password').on('input', function() {
        $(this).removeClass('is-invalid').closest('.input-group').next('.invalid-feedback').remove();
    });
    $('#loginForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah form submit biasa

        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var data = form.serialize();

        // Ambil tombol login dan ubah ke state loading
        var loginButton = $('#loginBtn');
        loginButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        // Hapus semua pesan error sebelumnya
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
            url: url,
            type: method,
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil!',
                        text: response.message,
                        timer: 1000, // Alert akan hilang setelah 2 detik
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect ke dashboard setelah alert ditutup
                        window.location.href = response.redirect_url;
                    });
                }
            },
            error: function(xhr) {
                // Menampilkan error validasi dari Laravel
                var errors; // Definisikan di sini agar bisa diakses di luar blok if
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errors = xhr.responseJSON.errors;
                    if (errors.Username) {
                        var errorMessage = errors.Username[0];
                        $('#username').addClass('is-invalid').closest('.input-group').after('<span class="invalid-feedback d-block" role="alert"><strong>' + errorMessage + '</strong></span>');
                    } else if (errors.Password) {
                        var errorMessage = errors.Password[0];
                        $('#password').addClass('is-invalid').closest('.input-group').after('<span class="invalid-feedback d-block" role="alert"><strong>' + errorMessage + '</strong></span>');
                    }
                }

                // Kembalikan tombol ke state semula
                loginButton.prop('disabled', false).html('Sign In');
                // Fokus ke input yang salah
                if (errors && errors.Password) {
                    $('#password').focus();
                }
            }
        });
    });
});
</script>
</body>
</html>