@php
    // Helper function to get value from existing data or provide a default
    function getValue($data, $key, $default = '') {
        return isset($data[$key]) ? htmlspecialchars(trim($data[$key])) : $default;
    }

    // Helper function to check if a dynamic row is empty
    function isDynamicRowEmpty($item) {
        $dynamicFields = [
            'PT_JAM', 'PT_VITAL', 'PT_BUKA', 'PT_KK', 'PT_DJJ', 'PT_PORTIO',
            'PT_TERABA', 'PT_TURUN', 'PT_X', 'PT_DTK', 'PT_KUAT', 'PT_KET', 'PT_PETUGAS'
        ];
        foreach ($dynamicFields as $field) {
            if (!empty(trim($item[$field] ?? ''))) return false;
        }
        return true;
    }
@endphp

<div class="card-body">
    <form id="rm9a3Form" action="{{ route('rme.igd.form.rm9a3.store') }}" method="post">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        <input type="hidden" name="NORM" value="{{ $norm }}">
        <input type="hidden" name="USER_ENTRY" value="{{ $user['username'] ?? '' }}">

        {{-- Data Statis Partograf --}}
        <div class="card card-outline card-primary mb-4">
            <div class="card-header"><h3 class="card-title">Informasi Umum Partograf</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 form-group"><label>Umur</label><input type="text" name="PT_UMUR" class="form-control" value="{{ getValue($static_data, 'PT_UMUR') }}"></div>
                    <div class="col-md-1 form-group"><label>G</label><input type="text" name="PT_G" class="form-control" value="{{ getValue($static_data, 'PT_G') }}"></div>
                    <div class="col-md-1 form-group"><label>P</label><input type="text" name="PT_P" class="form-control" value="{{ getValue($static_data, 'PT_P') }}"></div>
                    <div class="col-md-1 form-group"><label>A</label><input type="text" name="PT_A" class="form-control" value="{{ getValue($static_data, 'PT_A') }}"></div>
                    <div class="col-md-2 form-group"><label>Hidup</label><input type="text" name="PT_HIDUP" class="form-control" value="{{ getValue($static_data, 'PT_HIDUP') }}"></div>
                    <div class="col-md-2 form-group"><label>Kehamilan</label><input type="text" name="PT_HAMIL" class="form-control" value="{{ getValue($static_data, 'PT_HAMIL') }}"></div>
                    <div class="col-md-3 form-group"><label>Tanggal</label><input type="date" name="PT_TANGGAL" class="form-control" value="{{ !empty($static_data['PT_TANGGAL']) ? date('Y-m-d', strtotime($static_data['PT_TANGGAL'])) : date('Y-m-d') }}"></div>
                    <div class="col-md-4 form-group"><label>Diagnosa</label><input type="text" name="PT_DIAGNOSA" class="form-control" value="{{ getValue($static_data, 'PT_DIAGNOSA') }}"></div>
                    <div class="col-md-5 form-group">
                        <label>DPJP</label>
                        <select name="PT_NODPJP" class="form-control select2-searchable" data-placeholder="-- Pilih DPJP --">
                            <option></option> {{-- Opsi kosong diperlukan untuk placeholder Select2 --}}
                            @foreach ($pemeriksaList as $pemeriksa)
                                <option value="{{ trim($pemeriksa->NOPEMERIKSA) }}" {{ (getValue($static_data, 'PT_NODPJP') == trim($pemeriksa->NOPEMERIKSA)) ? 'selected' : '' }}>
                                    {{ trim($pemeriksa->NAMAPEMERIKSA) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Data Dinamis Partograf --}}
        <div class="card card-outline card-success mb-4">
            <div class="card-header">
                <h3 class="card-title">Data Monitoring Partograf</h3>
                <div class="card-tools">
                    <button type="button" id="add-partograf-row" class="btn btn-sm btn-success"><i class="fas fa-plus-circle"></i> Tambah Baris</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="partograf-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Jam</th>
                                <th>Vital Sign</th>
                                <th>Pembukaan</th>
                                <th>Ketuban</th>
                                <th>DJJ</th>
                                <th>Portio</th>
                                <th>Teraba</th>
                                <th>Turun</th>
                                <th colspan="3" class="text-center">HIS</th>
                                <th>Keterangan</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
                            </tr>
                            <tr class="thead-light">
                                <th colspan="8"></th>
                                <th>Kontraksi/10'</th>
                                <th>Detik</th>
                                <th>Kuat/Tidak</th>
                                <th colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($rows))
                                {{-- Biarkan kosong jika tidak ada data dari database --}}
                            @else
                                @foreach ($rows as $index => $item)
                                    @if (!isDynamicRowEmpty($item))
                                    <tr class="partograf-row">
                                        <input type="hidden" name="partograf[{{ $index }}][COUNTER]" value="{{ getValue($item, 'COUNTER') }}">
                                        <td><input type="time" name="partograf[{{ $index }}][PT_JAM]" class="form-control form-control-sm" value="{{ !empty($item['PT_JAM']) ? date('H:i', strtotime($item['PT_JAM'])) : '' }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_VITAL]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_VITAL') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_BUKA]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_BUKA') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_KK]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_KK') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_DJJ]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_DJJ') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_PORTIO]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_PORTIO') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_TERABA]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_TERABA') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_TURUN]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_TURUN') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_X]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_X') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_DTK]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_DTK') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_KUAT]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_KUAT') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_KET]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_KET') }}"></td>
                                        <td><input type="text" name="partograf[{{ $index }}][PT_PETUGAS]" class="form-control form-control-sm" value="{{ getValue($item, 'PT_PETUGAS') }}"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-partograf-row"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Data Statis Persalinan --}}
        <div class="card card-outline card-info mb-4">
            <div class="card-header"><h3 class="card-title">Informasi Persalinan</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group"><label>Tanggal</label><input type="date" name="PS_TANGGAL" class="form-control" value="{{ !empty($static_data['PS_TANGGAL']) ? date('Y-m-d', strtotime($static_data['PS_TANGGAL'])) : '' }}"></div>
                    <div class="col-md-6 form-group"><label>Jam</label><input type="time" name="PS_JAM" class="form-control" value="{{ !empty($static_data['PS_JAM']) ? date('H:i', strtotime($static_data['PS_JAM'])) : '' }}"></div>
                </div>
                <div class="row">
                    {{-- Baris 1 --}}
                    <div class="col-md-4 form-group"><label>Tindakan</label><input type="text" name="PS_TINDAKAN" class="form-control" value="{{ getValue($static_data, 'PS_TINDAKAN') }}"></div>
                    <div class="col-md-4 form-group"><label>PLAC</label><input type="text" name="PS_PLAC" class="form-control" value="{{ getValue($static_data, 'PS_PLAC') }}"></div>
                    <div class="col-md-4 form-group"><label>Bayi</label><input type="text" name="PS_BAYI" class="form-control" value="{{ getValue($static_data, 'PS_BAYI') }}"></div>
                    {{-- Baris 2 --}}
                    <div class="col-md-4 form-group"><label>Indikasi</label><input type="text" name="PS_INDIKASI" class="form-control" value="{{ getValue($static_data, 'PS_INDIKASI') }}"></div>
                    <div class="col-md-4 form-group"><label>Cotiledon</label><input type="text" name="PS_COTIL" class="form-control" value="{{ getValue($static_data, 'PS_COTIL') }}"></div>
                    <div class="col-md-4 form-group"><label>BB</label><input type="text" name="PS_BB" class="form-control" value="{{ getValue($static_data, 'PS_BB') }}"></div>
                    {{-- Baris 3 --}}
                    <div class="col-md-4 form-group"><label>Lama</label><input type="text" name="PS_LAMA" class="form-control" value="{{ getValue($static_data, 'PS_LAMA') }}"></div>
                    <div class="col-md-4 form-group"><label>Pendarahan</label><input type="text" name="PS_PDRHN" class="form-control" value="{{ getValue($static_data, 'PS_PDRHN') }}"></div>
                    <div class="col-md-4 form-group"><label>PB</label><input type="text" name="PS_PB" class="form-control" value="{{ getValue($static_data, 'PS_PB') }}"></div>
                    {{-- Baris 4 --}}
                    <div class="col-md-4 form-group"><label>PNRM</label><input type="text" name="PS_PNRM" class="form-control" value="{{ getValue($static_data, 'PS_PNRM') }}"></div>
                    <div class="col-md-4 form-group"><label>AS</label><input type="text" name="PS_AS" class="form-control" value="{{ getValue($static_data, 'PS_AS') }}"></div>
                    <div class="col-md-4 form-group"><label>LK</label><input type="text" name="PS_LK" class="form-control" value="{{ getValue($static_data, 'PS_LK') }}"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Dokter</label>
                        <select name="PS_NODOKTER" class="form-control select2-searchable" data-placeholder="-- Pilih Dokter --">
                            <option></option>
                            @foreach ($pemeriksaList as $pemeriksa)
                                <option value="{{ trim($pemeriksa->NOPEMERIKSA) }}" {{ (getValue($static_data, 'PS_NODOKTER') == trim($pemeriksa->NOPEMERIKSA)) ? 'selected' : '' }}>
                                    {{ trim($pemeriksa->NAMAPEMERIKSA) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Bidan</label>
                        <select name="PS_NOBIDAN" class="form-control select2-searchable" data-placeholder="-- Pilih Bidan --">
                            <option></option>
                             @foreach ($bidanList as $bidan)
                                <option value="{{ trim($bidan->NOUSER) }}" {{ (getValue($static_data, 'PS_NOBIDAN') == trim($bidan->NOUSER)) ? 'selected' : '' }}>
                                    {{ trim($bidan->NAMAUSER) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Penolong Lain</label>
                        <select name="PS_NOPENOLONG" class="form-control select2-searchable" data-placeholder="-- Pilih Penolong --">
                            <option></option>
                            @foreach ($pemeriksaList as $pemeriksa)
                                <option value="{{ trim($pemeriksa->NOPEMERIKSA) }}" {{ (getValue($static_data, 'PS_NOPENOLONG') == trim($pemeriksa->NOPEMERIKSA)) ? 'selected' : '' }}>
                                    {{ trim($pemeriksa->NAMAPEMERIKSA) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-4">
            <button type="submit" name="submit_rm9a3" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Data</button>
            <button type="button" class="btn btn-outline-danger" id="btn-reset-rm9a3"><i class="fas fa-times-circle mr-1"></i> Batal</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Inisialisasi Select2 pada elemen yang memiliki kelas 'select2-searchable'
    $('.select2-searchable').each(function() {
        $(this).select2({
            theme: 'bootstrap4', // Pastikan tema sudah benar
            placeholder: $(this).data('placeholder'),
            width: '100%' // Pastikan select2 mengisi lebar kontainer
        });
    });

    // Fungsi untuk menambah baris baru ke tabel
    $('#add-partograf-row').on('click', function() {
        let newIndex = $('#partograf-table tbody tr').length;
        let maxCounter = 0;
        $('input[name^="partograf["][name$="][COUNTER]"]').each(function() {
            let currentVal = parseInt($(this).val());
            if (currentVal > maxCounter) {
                maxCounter = currentVal;
            }
        });
        let newCounter = maxCounter + 1;
        const now = new Date();
        const currentTime = now.toTimeString().slice(0,5);
        const currentUser = '{{ $user["username"] ?? "" }}';

        const newRow = `
            <tr class="partograf-row">
                <input type="hidden" name="partograf[${newIndex}][COUNTER]" value="${newCounter}">
                <td><input type="time" name="partograf[${newIndex}][PT_JAM]" class="form-control form-control-sm" value="${currentTime}"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_VITAL]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_BUKA]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_KK]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_DJJ]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_PORTIO]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_TERABA]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_TURUN]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_X]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_DTK]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_KUAT]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_KET]" class="form-control form-control-sm"></td>
                <td><input type="text" name="partograf[${newIndex}][PT_PETUGAS]" class="form-control form-control-sm" value="${currentUser}"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-partograf-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        $('#partograf-table tbody').append(newRow);
    });

    // Fungsi untuk menghapus baris
    $('#partograf-table').on('click', '.remove-partograf-row', function() {
        $(this).closest('tr').remove();
    });

    $('#btn-reset-rm9a3').on('click', function() {
        Swal.fire({
            title: 'Yakin ingin batal?',
            text: "Semua perubahan akan dibatalkan dan form akan di-reset.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form-selector').trigger('change'); // Reload the form
            }
        });
    });

    $('#rm9a3Form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        var button = form.find('button[type="submit"]');
        var originalButtonText = button.html();

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            beforeSend: function() {
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => {
                        // Reload form untuk menampilkan data terbaru
                        $('#form-selector').trigger('change');
                    });
                } else {
                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
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
                button.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});
</script>