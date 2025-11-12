@extends('layouts.app')
  <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

@section('title', 'Manajemen Pengguna')

@push('styles')
    {{-- <style>
        .table-responsive {
            overflow-x: auto;
        }
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }
        .modal-body .row .col-sm-4 {
            font-weight: bold;
            color: #495057;
        }
    </style> --}}
@endpush

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manajemen Pengguna</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Pengguna</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-users-cog text-primary mr-2"></i>
            Daftar Pengguna Sistem
        </h5>
        <button class="btn btn-success btn-sm float-right mr-2" id="addBulkUserBtn">
            <i class="fas fa-users mr-2"></i>Tambah Massal
        </h5>
        <button class="btn btn-primary btn-sm float-right" id="addUserBtn">
            <i class="fas fa-user-plus mr-2"></i>Tambah Pengguna
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="group-filter">Filter Berdasarkan Group:</label>
                    <select id="group-filter" class="form-control form-control-sm">
                        <option value="">-- Tampilkan Semua --</option>
                        @foreach ($groups as $group)
                            <option value="{{ trim($group->KODEGROUP) }}">
                                {{ trim($group->KODEGROUP) }} - {{ trim($group->NAMAGROUP) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="users-table" style="width:100%">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Pemeriksa</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- User Detail Modal -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailModalLabel"><i class="fas fa-user-tag mr-2"></i>Detail Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-sm-4">Nama Pemeriksa</div>
                    <div class="col-sm-8" id="modal_nama_pemeriksa"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">Username</div>
                    <div class="col-sm-8" id="modal_username"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">Email</div>
                    <div class="col-sm-8" id="modal_email"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">NIK</div>
                    <div class="col-sm-8" id="modal_nik"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">Template ID</div>
                    <div class="col-sm-8" id="modal_template_id"></div>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-sm-4">No. User</div>
                    <div class="col-sm-8" id="modal_no_user"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">No. Pemeriksa</div>
                    <div class="col-sm-8" id="modal_no_pemeriksa"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">Kode Unit</div>
                    <div class="col-sm-8" id="modal_kode_unit"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">Kode Group</div>
                    <div class="col-sm-8" id="modal_kode_group"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- User Create Modal -->
<div class="modal fade" id="userCreateModal" tabindex="-1" aria-labelledby="userCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userCreateModalLabel"><i class="fas fa-user-plus mr-2"></i>Tambah Pengguna Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userCreateForm">
                @csrf
                <div class="modal-body">
                    <div id="create-form-errors" class="alert alert-danger" style="display: none;"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_username">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="create_username" name="Username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="create_password" name="Password" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_namapemeriksa">Nama Pemeriksa</label>
                                <input type="text" class="form-control" id="create_namapemeriksa" name="NAMAPEMERIKSA">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_email">Email</label>
                                <input type="email" class="form-control" id="create_email" name="Email">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="create_nik">NIK</label>
                                <input type="text" class="form-control" id="create_nik" name="nik">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="create_nopemeriksa">No. Pemeriksa</label>
                                <input type="text" class="form-control" id="create_nopemeriksa" name="NOPEMERIKSA">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="create_nouser">No. User</label>
                                <input type="text" class="form-control" id="create_nouser" name="NOUSER">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="create_access">Role</label>
                                <input type="text" class="form-control" id="create_access" name="ACCESS">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="create_template_id">Template ID</label>
                        <input type="text" class="form-control" id="create_template_id" name="TEMPLATEID">
                    </div>

                    <hr>
                    <p class="text-muted">Data Tambahan</p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="create_kodegroup">Kode Group</label>
<select class="form-control" id="create_kodegroup" name="KODEGROUP">
    <option value="">-- Pilih Group --</option>
    @foreach ($groups as $group)
        <option value="{{ trim($group->KODEGROUP) }}" data-kdunit="{{ trim($group->KDUNIT) }}">
            {{ trim($group->KODEGROUP) }} - {{ trim($group->NAMAGROUP) }}
        </option>
    @endforeach
</select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="create_kdunit">Kode Unit</label>
                                <input type="text" class="form-control" id="create_kdunit" name="KDUNIT" readonly>
                                <small class="form-text text-muted">Kode Unit akan terisi otomatis.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="create_isactive">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="create_isactive" name="IsActive" required>
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="createUserBtn"><i class="fas fa-plus-circle mr-2"></i>Tambah Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Edit Modal -->
<div class="modal fade" id="userEditModal" tabindex="-1" aria-labelledby="userEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userEditModalLabel"><i class="fas fa-user-edit mr-2"></i>Edit Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userEditForm">
                @csrf
                <div class="modal-body">
                    <div id="edit-form-errors" class="alert alert-danger" style="display: none;"></div>
                    <input type="hidden" id="edit_userid" name="UserID">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_username">Username</label>
                                <input type="text" class="form-control" id="edit_username" name="Username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_password">Password Baru</label>
                                <input type="password" class="form-control" id="edit_password" name="Password" placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_namapemeriksa">Nama Pemeriksa</label>                                <input type="text" class="form-control" id="edit_namapemeriksa" name="NAMAPEMERIKSA">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nik">NIK</label>
                                <input type="text" class="form-control" id="edit_nik" name="nik">
                            </div>
                        </div>
                    </div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label for="edit_nopemeriksa">No. Pemeriksa</label>
            <input type="text" class="form-control" id="edit_nopemeriksa" name="NOPEMERIKSA">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="edit_nouser">No. User</label>
            <input type="text" class="form-control" id="edit_nouser" name="NOUSER">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="edit_access">Role</label>
            <input type="text" class="form-control" id="edit_access" name="ACCESS">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="edit_isactive">Status</label>
            <select class="form-control" id="edit_isactive" name="IsActive">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>
    </div>
</div>
                    <div class="form-group">
                        <label for="edit_template_id">Template ID</label>
                        <input type="text" class="form-control" id="edit_template_id" name="TEMPLATEID" >
                    </div>


                    <hr>
                    <p class="text-muted">Data Tambahan</p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_kodegroup">Kode Group</label>
<select class="form-control" id="edit_kodegroup" name="KODEGROUP">
    <option value="">-- Pilih Group --</option>
    @foreach ($groups as $group)
        <option value="{{ trim($group->KODEGROUP) }}" data-kdunit="{{ trim($group->KDUNIT) }}">
            {{ trim($group->KODEGROUP) }} - {{ trim($group->NAMAGROUP) }}
        </option>
    @endforeach
</select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_kdunit">Kode Unit</label>
                                <input type="text" class="form-control" id="edit_kdunit" name="KDUNIT" readonly>
                                <small class="form-text text-muted">Kode Unit akan terisi otomatis.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveUserBtn"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk User Create Modal -->
<div class="modal fade" id="userBulkCreateModal" tabindex="-1" aria-labelledby="userBulkCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userBulkCreateModalLabel"><i class="fas fa-users mr-2"></i>Tambah Pengguna Massal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userBulkCreateForm">
                @csrf
                <div class="modal-body">
                    <div id="bulk-create-form-errors" class="alert alert-danger" style="display: none;"></div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="bulk_kodegroup">Kode Group <span class="text-danger">*</span></label>
                                <select class="form-control" id="bulk_kodegroup" name="KODEGROUP" required>
                                    <option value="">-- Pilih Group --</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ trim($group->KODEGROUP) }}" data-kdunit="{{ trim($group->KDUNIT) }}">
                                            {{ trim($group->KODEGROUP) }} - {{ trim($group->NAMAGROUP) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bulk_kdunit">Kode Unit</label>
                                <input type="text" class="form-control" id="bulk_kdunit" name="KDUNIT" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bulk_users_data">Data Pengguna</label>
                        <textarea class="form-control" id="bulk_users_data" name="users_data" rows="10" 
                                  placeholder="Masukkan data pengguna, satu per baris. Pisahkan dengan spasi.
Format: Username Password No.Pemeriksa No.User Role" required></textarea>
                        <small class="form-text text-muted">
                            Gunakan tanda hubung (<code>-</code>) untuk kolom yang ingin dikosongkan. <br>
                            <strong>Contoh:</strong><br>
                            <code>user1 pass123 12345 54321 DOKTER</code> (Semua terisi)<br>
                            <code>user2 pass456 - 54322 PERAWAT</code> (No. Pemeriksa kosong)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="createBulkUserBtn"><i class="fas fa-plus-circle mr-2"></i>Tambah Semua Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable dengan AJAX
        var usersTable = $('#users-table').DataTable({
            "processing": true,
            "serverSide": false, // Kita gunakan client-side processing karena data tidak terlalu besar
            "responsive": true,
            "ajax": {
                "url": "{{ route('users.index') }}",
                "type": "GET",
                "dataSrc": "data", // Memberitahu DataTables bahwa data ada di dalam properti 'data'
                "data": function(d) {
                    d.kodegroup = $('#group-filter').val(); // Tambahkan parameter kodegroup
                }
            },
            "columns": [
                { "data": null, "orderable": false, "searchable": false }, // Kolom nomor urut
                { "data": "NAMAPEMERIKSA", "render": $.trim },
                { "data": "Username", "render": $.trim },
                { "data": "Email", "render": $.trim },
                { "data": "ACCESS", "render": $.trim },
                {
                    "data": "IsActive",
                    "render": function(data, type, row) {
                        return data ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>';
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    "render": function(data, type, row) {
                        // Menggunakan backtick (`) untuk multiline string
                        return `
                            <button class="btn btn-sm btn-info detail-btn" data-user='${JSON.stringify(row)}' title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning edit-btn" data-user='${JSON.stringify(row)}' title="Edit Pengguna">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.UserID}" data-name="${$.trim(row.Username)}" title="Hapus Pengguna">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Semua"] ],
            "pageLength": 10,
            // Membuat nomor urut otomatis
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                $("td:first", nRow).html(iDisplayIndex + 1);
                return nRow;
            }
        });

        // Event handler untuk filter group
        $('#group-filter').on('change', function() {
            usersTable.ajax.reload(); // Muat ulang data tabel saat filter berubah
        });

        // Event handler untuk tombol tambah pengguna
        $('#addUserBtn').on('click', function() {
            // Reset form dan pesan error
            $('#userCreateForm')[0].reset();
            $('#create-form-errors').hide().html('');
            $('.is-invalid').removeClass('is-invalid');
            $('#create_kdunit').val(''); // Pastikan kdunit kosong
            $('#userCreateModal').modal('show');
        });

        // Event listener untuk perubahan dropdown Kode Group di form create
        $('#create_kodegroup').on('change', function() {
            const selectedKdUnit = $(this).find('option:selected').data('kdunit');
            $('#create_kdunit').val(selectedKdUnit || '');
        });

        // Event handler untuk tombol tambah massal
        $('#addBulkUserBtn').on('click', function() {
            $('#userBulkCreateForm')[0].reset();
            $('#bulk-create-form-errors').hide().html('');
            $('#userBulkCreateModal').modal('show');
        });

        // Event listener untuk perubahan dropdown Kode Group di form massal
        $('#bulk_kodegroup').on('change', function() {
            const selectedKdUnit = $(this).find('option:selected').data('kdunit');
            $('#bulk_kdunit').val(selectedKdUnit || '');
        });
        // Event handler untuk submit form create
        $('#userCreateForm').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('users.store') }}",
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('#createUserBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menambahkan...');
                    $('#create-form-errors').hide().html('');
                    $('.is-invalid').removeClass('is-invalid');
                },
                success: function(response) {
                    $('#userCreateModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        usersTable.ajax.reload(null, false); // false agar tidak kembali ke halaman pertama
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // Validation error
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                            // Menandai field yang error
                            $('#create_' + key.toLowerCase()).addClass('is-invalid');
                        });
                        errorHtml += '</ul>';
                        $('#create-form-errors').html(errorHtml).show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat menambah data.'
                        });
                    }
                },
                complete: function() {
                    $('#createUserBtn').prop('disabled', false).html('<i class="fas fa-plus-circle mr-2"></i>Tambah Pengguna');
                }
            });
        });

        // Event handler untuk submit form bulk create
        $('#userBulkCreateForm').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('users.bulkStore') }}",
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    $('#createBulkUserBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menambahkan...');
                    $('#bulk-create-form-errors').hide().html('');
                },
                success: function(response) {
                    $('#userBulkCreateModal').modal('hide');
                    let successMessage = `Berhasil menambahkan ${response.success_count} pengguna.`;
                    if (response.errors.length > 0) {
                        successMessage += `\n\nGagal menambahkan ${response.errors.length} pengguna karena duplikasi atau format salah.`;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Proses Selesai!',
                        text: successMessage,
                    }).then(() => {
                        usersTable.ajax.reload(null, false);
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // Validation error
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul>';
                        $('#bulk-create-form-errors').html(errorHtml).show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat menambah data.'
                        });
                    }
                },
                complete: function() {
                    $('#createBulkUserBtn').prop('disabled', false).html('<i class="fas fa-plus-circle mr-2"></i>Tambah Semua Pengguna');
                }
            });
        });

        // Event handler untuk tombol detail
        $('#users-table').on('click', '.detail-btn', function() {
            const userData = $(this).data('user');

            // Fungsi untuk membersihkan dan menampilkan data
            const displayData = (data) => data ? $.trim(data) : '-';

            // Mengisi data ke dalam modal
            $('#modal_nama_pemeriksa').text(displayData(userData.NAMAPEMERIKSA));
            $('#modal_username').text(displayData(userData.Username));
            $('#modal_email').text(displayData(userData.Email));
            $('#modal_nik').text(displayData(userData.nik));
            $('#modal_no_user').text(displayData(userData.NOUSER));
            $('#modal_no_pemeriksa').text(displayData(userData.NOPEMERIKSA));
            $('#modal_kode_unit').text(displayData(userData.KDUNIT));
            $('#modal_kode_group').text(displayData(userData.KODEGROUP));
            $('#modal_template_id').text(displayData(userData.TEMPLATEID));

            // Menampilkan modal
            $('#userDetailModal').modal('show');
        });

        // Event handler untuk tombol edit
        $('#users-table').on('click', '.edit-btn', function() {
            const userData = $(this).data('user');

            // Reset form dan pesan error
            $('#userEditForm')[0].reset();
            $('#edit-form-errors').hide().html('');
            $('.is-invalid').removeClass('is-invalid');

            // Mengisi data ke dalam form modal edit
            $('#edit_userid').val(userData.UserID);
            $('#edit_username').val($.trim(userData.Username));
            $('#edit_namapemeriksa').val($.trim(userData.NAMAPEMERIKSA));
            $('#edit_email').val($.trim(userData.Email));
            $('#edit_access').val($.trim(userData.ACCESS));
            $('#edit_nik').val($.trim(userData.nik));
            $('#edit_nopemeriksa').val($.trim(userData.NOPEMERIKSA));
            $('#edit_nouser').val($.trim(userData.NOUSER));
            
            // Set Kode Group dan trigger change untuk mengisi Kode Unit
            $('#edit_kodegroup').val($.trim(userData.KODEGROUP));
            $('#edit_kodegroup').trigger('change'); // Memanggil event change secara manual

            $('#edit_template_id').val($.trim(userData.TEMPLATEID));
            $('#edit_isactive').val(userData.IsActive ? 1 : 0);

            // Event listener untuk perubahan dropdown Kode Group
            $('#edit_kodegroup').on('change', function() {
                // Ambil data-kdunit dari option yang dipilih
                const selectedKdUnit = $(this).find('option:selected').data('kdunit');
                // Set nilai input Kode Unit
                $('#edit_kdunit').val(selectedKdUnit || '');
            });

            // Menampilkan modal
            $('#userEditModal').modal('show');
        });

        // Event handler untuk submit form edit
        $('#userEditForm').on('submit', function(e) {
            e.preventDefault();

            // Ambil UserID dari hidden input
            const userId = $('#edit_userid').val();
            // Buat URL dinamis dengan mengganti placeholder
            let url = "{{ route('users.update', ['UserID' => ':id']) }}";
            url = url.replace(':id', userId);

            let formData = $(this).serialize();

            $.ajax({
                url: url, // Gunakan URL yang sudah dinamis
                type: 'PUT', // Gunakan method PUT sesuai definisi rute
                data: formData, // Data form tetap dikirim
                beforeSend: function() {
                    $('#saveUserBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                    $('#edit-form-errors').hide().html('');
                    $('.is-invalid').removeClass('is-invalid');
                },
                success: function(response) {
                    $('#userEditModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Muat ulang data tabel tanpa refresh halaman
                        usersTable.ajax.reload(null, false);
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // Validation error
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                            // Menandai field yang error
                            $('#edit_' + key.toLowerCase()).addClass('is-invalid');
                        });
                        errorHtml += '</ul>';
                        $('#edit-form-errors').html(errorHtml).show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat memperbarui data.'
                        });
                    }
                },
                complete: function() {
                    $('#saveUserBtn').prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Simpan Perubahan');
                }
            });
        });

        // Event handler untuk tombol hapus
        $('#users-table').on('click', '.delete-btn', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            
            let url = "{{ route('users.destroy', ['UserID' => ':id']) }}";
            url = url.replace(':id', userId);

            Swal.fire({
                title: 'Anda yakin?',
                text: `Anda akan menghapus pengguna "${userName}". Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            }).then(() => {
                                usersTable.ajax.reload(null, false); // Muat ulang data tabel tanpa refresh halaman
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush