@php
    function getValue($data, $key, $default = '') {
        return data_get($data, $key, $default);
    }
    function isChecked($data, $key, $value = 1) {
        return data_get($data, $key) == $value ? 'checked' : '';
    }
@endphp

<div class="card-body">
    <form id="formSkriningSepsis" action="{{ route('rme.igd.form.skriningsepsis.store') }}" method="POST">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ htmlspecialchars($noPendaftaran) }}">
        <input type="hidden" name="NORM" value="{{ htmlspecialchars($norm) }}">

        <fieldset class="border p-3 rounded">
            <legend class="fs-6 fw-bold w-auto px-2">Penilaian Kriteria Sepsis</legend>
            <p class="text-muted mt-n2 mb-3">Klik pada salah satu atau lebih kriteria di bawah ini jika ditemukan pada pasien.</p>
            <div class="row">
                <div class="col-md-12">
                    <div class="sepsis-option mb-2 {{ isChecked($data, 'PENURUNANSADAR') ? 'selected' : '' }}" data-name="PENURUNANSADAR">
                        <span>Penurunan kesadaran</span>
                        <input type="checkbox" name="PENURUNANSADAR" value="1" {{ isChecked($data, 'PENURUNANSADAR') }} style="display: none;">
                    </div>
                    <div class="sepsis-option mb-2 {{ isChecked($data, 'TAKIPNEU') ? 'selected' : '' }}" data-name="TAKIPNEU">
                        <span>Takipneu (RR > 20x/menit)</span>
                        <input type="checkbox" name="TAKIPNEU" value="1" {{ isChecked($data, 'TAKIPNEU') }} style="display: none;">
                    </div>
                    <div class="sepsis-option mb-2 {{ isChecked($data, 'SISTOLIK') ? 'selected' : '' }}" data-name="SISTOLIK">
                        <span>Tekanan darah sistolik < 90 mmHg</span>
                        <input type="checkbox" name="SISTOLIK" value="1" {{ isChecked($data, 'SISTOLIK') }} style="display: none;">
                    </div>
                </div>
            </div>
        </fieldset>

        <hr>

        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Kesimpulan</label>
                <div id="kesimpulan-text" class="alert alert-secondary">Belum ada kesimpulan</div>
                <input type="hidden" name="KESIMPULAN" id="KESIMPULAN" value="{{ getValue($data, 'KESIMPULAN') }}">
            </div>
            <div class="col-md-6">
                <label for="RTL" class="form-label fw-bold">Rencana Tindak Lanjut</label>
                <textarea class="form-control" name="RTL" id="RTL" rows="3" readonly>{{ getValue($data, 'RTL') }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

<script>
// Tambahkan style untuk membuat area klik lebih besar dan memberikan feedback visual
if (!document.getElementById('sepsis-style')) {
    const style = document.createElement('style');
    style.id = 'sepsis-style';
    style.innerHTML = `
        #formSkriningSepsis .sepsis-option {
            cursor: pointer;
            border: 2px solid #dee2e6 !important; /* Border lebih tebal dan sedikit lebih gelap */
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        #formSkriningSepsis .sepsis-option:not(.selected):hover {
            background-color: #f8f9fa;
        }
        #formSkriningSepsis .sepsis-option.selected {
            background-color: #cff4fc !important; /* Bootstrap info-light */
            border-color: #9eeaf9 !important; /* Border lebih kontras saat dipilih */
        }
    `;
    document.head.appendChild(style);
}

$(document).ready(function() {
    function updateSepsisScreening() {
        const checkedCount = $('#formSkriningSepsis input[type="checkbox"]:checked').length;
        const kesimpulanText = $('#kesimpulan-text');
        const kesimpulanInput = $('#KESIMPULAN');
        const rtlTextarea = $('#RTL');
        let kesimpulan = '';
        let rtlValue = '';

        kesimpulanText.removeClass('alert-warning alert-danger').addClass('alert-secondary');

        if (checkedCount === 1) {
            kesimpulan = 'Low Risk';
            kesimpulanText.html('<strong>Low Risk</strong>').removeClass('alert-secondary').addClass('alert-warning');
            rtlValue = 'Observasi rutin';
        } else if (checkedCount >= 2) {
            kesimpulan = 'High Risk Sepsis';
            kesimpulanText.html('<strong>High Risk Sepsis</strong>').removeClass('alert-secondary').addClass('alert-danger');
            rtlValue = 'Lapor DPJP\nTerapi antibiotik golongan carbapenem segera';
        } else {
            kesimpulan = '';
            kesimpulanText.html('Belum ada kesimpulan');
            rtlValue = '';
        }

        kesimpulanInput.val(kesimpulan);
        rtlTextarea.val(rtlValue);
    }

    // Event listener untuk semua checkbox skrining
    $('#formSkriningSepsis').on('change', 'input[type="checkbox"]', function() {
        // Update class 'selected' pada parent div
        $(this).closest('.sepsis-option').toggleClass('selected', $(this).is(':checked'));
        updateSepsisScreening();
    });

    // Event listener untuk membuat seluruh div bisa diklik
    $('#formSkriningSepsis').on('click', '.sepsis-option', function(e) {
        // Hindari trigger ganda jika yang diklik adalah input itu sendiri
        if ($(e.target).is('input')) return;

        const checkbox = $(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
    });

    // Panggil fungsi saat halaman dimuat untuk menampilkan state awal
    updateSepsisScreening();

    // AJAX form submission
    $('#formSkriningSepsis').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var formData = new FormData(this);
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.html();

        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                     Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: function(xhr) {
                var errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMessage
                });
            },
            complete: function() {
                submitButton.html(originalButtonText).prop('disabled', false);
            }
        });
    });
});
</script>