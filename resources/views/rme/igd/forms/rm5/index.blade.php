{{-- resources/views/rme/igd/forms/rm5/index.blade.php --}}

<div class="card-body">
    {{-- Menampilkan notifikasi error dari controller --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form id="rm5Form" method="post" action="{{ route('rme.igd.form.rm5.store') }}" class="row g-3">
        @csrf

        {{-- Hidden Fields --}}
        <input type="hidden" id="rm5_nopendaftaran" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" id="rm5_norm" name="NORM" value="{{ $norm }}">

        {{-- Tanggal dan Jam Masuk --}}
        <div class="col-md-3">
            <label for="tglmasuk" class="form-label">Tanggal Masuk:</label>
            <input type="date" id="tglmasuk" name="tglmasuk" class="form-control" value="{{ old('tglmasuk', isset($row['TGLMASUK']) ? \Carbon\Carbon::parse($row['TGLMASUK'])->format('Y-m-d') : date('Y-m-d')) }}">
        </div>
        <div class="col-md-3">
            <label for="jammasuk" class="form-label">Jam Masuk:</label>
            <input type="time" id="jammasuk" name="jammasuk" class="form-control" value="{{ old('jammasuk', $row['JAMMASUK'] ?? date('H:i')) }}">
        </div>

        {{-- Nama Pasien --}}
        <div class="col-md-6">
            <label for="namapasien" class="form-label">Nama Pasien</label>
            <input type="text" class="form-control" id="namapasien" name="namapasien"
                   value="{{ htmlspecialchars($namaPasien) }}" readonly>
        </div>

        {{-- Nama Dokter --}}
        <div class="col-md-6">
            <label for="namadokter" class="form-label">Nama Dokter:</label>
            <select id="namadokter" name="namadokter" class="form-select form-control">
                <option value="">-- Pilih Dokter --</option>
                @foreach ($dokterList as $dokter)
                    <option value="{{ trim($dokter->NAMAPEMERIKSA) }}"
                            data-jabatan="{{ trim($dokter->KDJABATAN) }}"
                            @if(old('namadokter', trim($row['NAMADOKTER'] ?? '')) == trim($dokter->NAMAPEMERIKSA)) selected @endif>
                        {{ trim($dokter->NAMAPEMERIKSA) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Jabatan --}}
        <div class="col-md-6">
            <label class="form-label">Jabatan:</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="jab_umum" name="jab_umum" value="1"
                       @if(old('jab_umum', $row['JAB_UMUM'] ?? 0) == 1) checked @endif onchange="handleJabatanChangeRm5('jab_umum')">
                <label class="form-check-label" for="jab_umum">dr. Umum</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="jab_spes" name="jab_spes" value="1"
                       @if(old('jab_spes', $row['JAB_SPES'] ?? 0) == 1) checked @endif onchange="handleJabatanChangeRm5('jab_spes')">
                <label class="form-check-label" for="jab_spes">dr. Spesialis</label>
            </div>
        </div>

        {{-- Keterangan Spesialis --}}
        <div class="col-md-6">
            <label for="ket_spes" class="form-label">Keterangan Spesialis:</label>
            <input type="text" id="ket_spes" name="ket_spes" class="form-control"
                   value="{{ old('ket_spes', trim($row['KET_SPES'] ?? '')) }}" readonly>
        </div>

        {{-- Tanggal Lahir & Jenis Kelamin --}}
        <div class="col-md-6">
            <label for="tgllahir" class="form-label">Tanggal Lahir Pasien:</label>
            <input type="date" id="tgllahir" name="tgllahir" class="form-control"
                   value="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $tanggalLahir)->format('Y-m-d') }}" readonly>
        </div>
        <div class="col-md-6">
            <label for="jk" class="form-label">Jenis Kelamin:</label>
            <select id="jk" name="jk" class="form-select form-control" readonly>
                <option value="L" @if($gender == 'L') selected @endif>Laki-laki</option>
                <option value="P" @if($gender == 'P') selected @endif>Perempuan</option>
            </select>
        </div>

        {{-- Diagnosis & Alasan --}}
        <div class="col-md-6">
            <label for="diag_masuk" class="form-label">Diagnosis Masuk:</label>
            <input type="text" id="diag_masuk" name="diag_masuk" class="form-control"
                   value="{{ old('diag_masuk', $row['DIAG_MASUK'] ?? '') }}">
        </div>
        <div class="col-md-6">
            <label for="alasan" class="form-label">Alasan rawat jalan:</label>
            <input type="text" id="alasan" name="alasan" class="form-control"
                   value="{{ old('alasan', trim($row['ALASAN'] ?? '')) }}">
        </div>

        {{-- DPJP --}}
        <div class="col-md-6">
            <label for="spesialisasi" class="form-label">DPJP:</label>
            <select id="spesialisasi" name="spesialisasi" class="form-select form-control">
                <option value="">-- Pilih Dokter --</option>
                @foreach ($dokterList as $dokter)
                    <option value="{{ trim($dokter->NAMAPEMERIKSA) }}"
                            @if(old('spesialisasi', trim($row['SPESIALISASI'] ?? '')) == trim($dokter->NAMAPEMERIKSA)) selected @endif>
                        {{ trim($dokter->NAMAPEMERIKSA) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tombol Aksi --}}
        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary shadow-sm" id="btn-save-rm5">
                <i class="fa fa-save"></i> {{ isset($row['NOPENDAFTARAN']) && !empty($row['NOPENDAFTARAN']) ? 'Update' : 'Simpan' }}
            </button>
            <button type="reset" class="btn btn-outline-secondary shadow-sm">
                <i class="fa fa-undo"></i> Reset
            </button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Panggil fungsi ini saat halaman dimuat untuk mengisi data jika dokter sudah terpilih
        isiDataDokterRm5();

        $('#rm5Form #namadokter').on('change', isiDataDokterRm5);

        // Submit form RM5 via AJAX
        $('#rm5Form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let actionUrl = $(this).attr('action');
            let button = $('#btn-save-rm5');
            let originalButtonText = button.html();

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Berhasil!', response.message, 'success');
                        // Pemicu event 'change' pada dropdown utama untuk memuat ulang form
                        $('#form-selector').trigger('change');
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMsg, 'error');
                },
                complete: function(xhr, status) {
                    // Jika gagal, kembalikan tombol ke state semula.
                    // Jika berhasil, form akan dimuat ulang, jadi tidak perlu melakukan apa-apa.
                    if (status !== 'success') {
                        button.prop('disabled', false).html(originalButtonText);
                    }
                }
            });
        });
    });

    function handleJabatanChangeRm5(selected) {
        const form = $('#rm5Form');
        const jabUmum = form.find('#jab_umum')[0];
        const jabSpes = form.find('#jab_spes')[0];
        if (selected === 'jab_umum' && jabUmum.checked) {
            jabSpes.checked = false;
        } else if (selected === 'jab_spes' && jabSpes.checked) {
            jabUmum.checked = false;
        }
    }

    function isiDataDokterRm5() {
        const form = $('#rm5Form');
        const namadokterSelect = form.find('#namadokter')[0];
        const selectedOption = namadokterSelect.options[namadokterSelect.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            form.find('#ket_spes').val('');
            form.find('#jab_umum').prop('checked', false);
            form.find('#jab_spes').prop('checked', false);
            return;
        }
        
        const kdJabatan = selectedOption.getAttribute('data-jabatan');
        const jabatanList = @json($jabatanList);
        const namaJabatan = jabatanList[kdJabatan] || '';

        form.find('#ket_spes').val(namaJabatan);

        if (namaJabatan && namaJabatan.toLowerCase().includes('spesialis')) {
            form.find('#jab_spes').prop('checked', true);
            form.find('#jab_umum').prop('checked', false);
        } else {
            form.find('#jab_umum').prop('checked', true);
            form.find('#jab_spes').prop('checked', false);
        }
    }
</script>
