<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel"><i class="fas fa-user-edit mr-2"></i>Ubah Profil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="profileUpdateForm">
                @csrf
                <div class="modal-body">
                    <!-- Form Error Placeholder -->
                    <div id="profile-form-errors" class="alert alert-danger" style="display: none;"></div>

                    <div class="form-group">
                        <label for="profile_username">Username</label>
                        <input type="text" class="form-control" id="profile_username" name="username" readonly>
                        <small class="form-text text-muted">Username tidak dapat diubah.</small>
                    </div>

                    <div class="form-group">
                        <label for="profile_namapemeriksa">Nama Pemeriksa</label>
                        <input type="text" class="form-control" id="profile_namapemeriksa" name="namapemeriksa">
                    </div>

                    <div class="form-group">
                        <label for="profile_email">Email</label>
                        <input type="email" class="form-control" id="profile_email" name="email" >
                    </div>

                    <div class="form-group">
                        <label for="profile_nik">NIK</label>
                        <input type="text" class="form-control" id="profile_nik" name="nik">
                    </div>

                    <hr>
                    <p class="text-muted">Isi bagian di bawah ini hanya jika Anda ingin mengubah password.</p>

                    <div class="form-group">
                        <label for="profile_password">Password Baru</label>
                        <input type="password" class="form-control" id="profile_password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="profile_password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="profile_password_confirmation" name="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script untuk modal ini akan ditambahkan di app.blade.php
    // agar bisa diakses secara global
</script>
@endpush