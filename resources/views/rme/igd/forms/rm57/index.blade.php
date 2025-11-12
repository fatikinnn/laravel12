@php
    // Helper function to get value from existing data or provide a default
    function getValueRm57($data, $key, $default = '') {
        if (is_object($data) && property_exists($data, $key) && !is_null($data->$key)) {
            return trim($data->$key);
        }
        return $default;
    }

    // Determine if we are updating or creating
    $isUpdate = !empty($data->TGLJAM_ENTRY);
    $submitButtonText = $isUpdate ? 'Update' : 'Simpan';
@endphp

<div class="card-body">
    <form id="rm57Form" action="{{ route('rme.igd.form.rm57.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">

        <div class="row mb-3">
            <div class="col-md-6 form-group">
                <label for="DOKTERBIDAN">Dokter</label>
                <select class="form-control" id="DOKTERBIDAN" name="DOKTERBIDAN" required>
                    <option value="">-- Pilih Dokter --</option>
                    @foreach ($dokterList as $dokter)
                        <option value="{{ trim($dokter->NAMAPEMERIKSA) }}"
                            {{ (getValueRm57($data, 'DOKTERBIDAN') == trim($dokter->NAMAPEMERIKSA) || $patientDetails['DPJP'] == trim($dokter->NAMAPEMERIKSA)) ? 'selected' : '' }}>
                            {{ trim($dokter->NAMAPEMERIKSA) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <h5 class="mb-3 border-bottom pb-2">Data Orang Tua & Kelahiran</h5>
        <div class="row mb-3">
            <div class="col-md-6 form-group">
                <label for="NM_ISTRI">Nama Istri</label>
                <input type="text" class="form-control" id="NM_ISTRI" name="NM_ISTRI" value="{{ getValueRm57($data, 'NM_ISTRI', $patientDetails['Nama Pasien'] ?? '') }}">
            </div>
             <div class="col-md-6 form-group">
                <label for="NM_SUAMI">Nama Suami</label>
                <input type="text" class="form-control" id="NM_SUAMI" name="NM_SUAMI" value="{{ getValueRm57($data, 'NM_SUAMI', $patientDetails['Nama Suami'] ?? '') }}">
            </div>
            <div class="col-md-12 form-group">
                <label for="ALAMAT">Alamat</label>
                <input type="text" class="form-control" id="ALAMAT" name="ALAMAT" value="{{ getValueRm57($data, 'ALAMAT', $patientDetails['Alamat'] ?? '') }}">
            </div>
            <div class="col-md-3 form-group">
                <label for="HARI">Hari</label>
                <input type="text" class="form-control bg-light" id="HARI" name="HARI" value="{{ getValueRm57($data, 'HARI') }}" readonly>
            </div>
            <div class="col-md-3 form-group">
                <label for="TANGGAL">Tanggal</label>
                <input type="date" class="form-control" id="TANGGAL" name="TANGGAL" value="{{ getValueRm57($data, 'TANGGAL') ? \Carbon\Carbon::parse(getValueRm57($data, 'TANGGAL'))->format('Y-m-d') : '' }}">
            </div>
            <div class="col-md-3 form-group">
                <label for="JAM">Jam</label>
                <input type="time" class="form-control" id="JAM" name="JAM" value="{{ getValueRm57($data, 'JAM') ? \Carbon\Carbon::parse(getValueRm57($data, 'JAM'))->format('H:i') : '' }}">
            </div>
        </div>

        <h5 class="mb-3 border-bottom pb-2">Data Bayi</h5>
        <div class="row mb-4">
            <div class="col-md-6 form-group">
                <label for="NM_BAYI">Nama Bayi</label>
                <input type="text" class="form-control" id="NM_BAYI" name="NM_BAYI" value="{{ getValueRm57($data, 'NM_BAYI') }}">
            </div>
            <div class="col-md-2 form-group">
                <label for="JENIS_KELAMIN">Jenis Kelamin</label>
                <select class="form-control" id="JENIS_KELAMIN" name="JENIS_KELAMIN">
                    <option value="L" {{ getValueRm57($data, 'JENIS_KELAMIN') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ getValueRm57($data, 'JENIS_KELAMIN') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="BB">Berat Badan (gram)</label>
                <input type="number" class="form-control" id="BB" name="BB" value="{{ getValueRm57($data, 'BB') }}">
            </div>
            <div class="col-12 form-group">
                <label for="KETERANGAN">Keterangan Tambahan</label>
                <textarea class="form-control" id="KETERANGAN" name="KETERANGAN" rows="3">{{ getValueRm57($data, 'KETERANGAN') }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary mr-2 px-4" id="btn-save-rm57">
                <i class="fas fa-save mr-2"></i>{{ $submitButtonText }}
            </button>
            <button type="button" class="btn btn-outline-danger" id="btn-reset-rm57">
                <i class="fas fa-times-circle mr-1"></i> Batal / Reset
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Function to auto-fill day based on date
    function setDayFromDate() {
        const dateInput = $('#TANGGAL').val();
        if (dateInput) {
            const date = new Date(dateInput);
            const dayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            // Adjust for timezone offset to prevent day shifting
            const timezoneOffset = date.getTimezoneOffset() * 60000;
            const adjustedDate = new Date(date.getTime() + timezoneOffset); // Use adjusted date for getDay()
            $('#HARI').val(dayNames[adjustedDate.getDay()]);
        }
    }

    // Set day on date change
    $('#TANGGAL').on('change', setDayFromDate);

    // Set initial day if date is pre-filled
    if ($('#TANGGAL').val()) {
        setDayFromDate();
    }

    // Reset button logic
    $('#btn-reset-rm57').on('click', function() {
        Swal.fire({
            title: 'Yakin ingin batal?',
            text: "Semua isian pada formulir ini akan dikosongkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#rm57Form')[0].reset();
                // Re-populate default/initial values if necessary
                $('#NM_ISTRI').val("{{ $patientDetails['Nama Pasien'] ?? '' }}");
                $('#NM_SUAMI').val("{{ $patientDetails['Nama Suami'] ?? '' }}");
                $('#ALAMAT').val("{{ $patientDetails['Alamat'] ?? '' }}");
                $('#DOKTERBIDAN').val("{{ $patientDetails['DPJP'] ?? '' }}");
                Swal.fire('Dibatalkan!', 'Isian formulir telah dikosongkan.', 'success');
            }
        });
    });

    // AJAX form submission
    $('#rm57Form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        var button = $('#btn-save-rm57');
        var originalButtonHtml = button.html();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success');
                    // Change button text to 'Update' after first save
                    button.html('<i class="fas fa-save mr-2"></i>Update');
                } else {
                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            },
            complete: function() {
                button.prop('disabled', false).html(button.html().includes('Update') ? '<i class="fas fa-save mr-2"></i>Update' : originalButtonHtml);
            }
        });
    });
});
</script>