<div class="card-body">
    <form id="form-mcu" action="{{ route('rme.igd.form.mcu.store') }}" method="POST">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">
        
        {{-- Pemeriksaan Klinis --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="card-title m-0">Anamnesa</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="KELUHAN">Keluhan</label>
                            <textarea class="form-control" id="KELUHAN" name="KELUHAN" rows="4">{{ $mcu->KELUHAN ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pemeriksaan Fisik --}}
        <div class="card card-primary card-outline mt-3">
            <div class="card-header">
                <h5 class="card-title m-0">Pemeriksaan Fisik</h5>
            </div>
            <div class="card-body">
                <h6><i class="fas fa-heartbeat mr-2"></i>Tanda - Tanda Vital</h6>
                <div class="row">
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="TD">Tekanan Darah</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="TD" name="TD" value="{{ $mcu->TD ?? '' }}" placeholder=".../...">
                                <div class="input-group-append"><span class="input-group-text">mmHg</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="NADI">Nadi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="NADI" name="NADI" value="{{ $mcu->NADI ?? '' }}" placeholder="...">
                                <div class="input-group-append"><span class="input-group-text">x/menit</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="SUHU">Suhu</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="SUHU" name="SUHU" value="{{ $mcu->SUHU ?? '' }}" placeholder="...">
                                <div class="input-group-append"><span class="input-group-text">°C</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="RR">Pernapasan</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="RR" name="RR" value="{{ $mcu->RR ?? '' }}" placeholder="...">
                                <div class="input-group-append"><span class="input-group-text">x/menit</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            <label for="SP02">SpO2</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="SP02" name="SP02" value="{{ $mcu->SP02 ?? '' }}" placeholder="...">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="TB">Tinggi Badan</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="TB" name="TB" value="{{ $mcu->TB ?? '' }}" placeholder="...">
                                <div class="input-group-append"><span class="input-group-text">cm</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="BB">Berat Badan</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="BB" name="BB" value="{{ $mcu->BB ?? '' }}" placeholder="...">
                                <div class="input-group-append"><span class="input-group-text">kg</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="IMT">Index Massa Tubuh</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="IMT" name="IMT" value="{{ $mcu->IMT ?? '' }}" placeholder="...">
                                <div class="input-group-append"><span class="input-group-text">kg/m²</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pemeriksaan_fisik_kepala" class="text-sm">Kepala</label>
                            <input type="text" class="form-control form-control-sm" id="pemeriksaan_fisik_kepala" name="pemeriksaan_fisik_kepala" value="{{ $pemeriksaanFisik['kepala'] }}">
                        </div>
                        <div class="form-group">
                            <label for="pemeriksaan_fisik_leher" class="text-sm">Leher</label>
                            <input type="text" class="form-control form-control-sm" id="pemeriksaan_fisik_leher" name="pemeriksaan_fisik_leher" value="{{ $pemeriksaanFisik['leher'] }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pemeriksaan_fisik_jantung" class="text-sm">Jantung</label>
                            <input type="text" class="form-control form-control-sm" id="pemeriksaan_fisik_jantung" name="pemeriksaan_fisik_jantung" value="{{ $pemeriksaanFisik['jantung'] }}">
                        </div>
                        <div class="form-group">
                            <label for="pemeriksaan_fisik_paru" class="text-sm">Paru</label>
                            <input type="text" class="form-control form-control-sm" id="pemeriksaan_fisik_paru" name="pemeriksaan_fisik_paru" value="{{ $pemeriksaanFisik['paru'] }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pemeriksaan_fisik_abdomen" class="text-sm">Abdomen</label>
                            <input type="text" class="form-control form-control-sm" id="pemeriksaan_fisik_abdomen" name="pemeriksaan_fisik_abdomen" value="{{ $pemeriksaanFisik['abdomen'] }}">
                        </div>
                        <div class="form-group">
                            <label for="pemeriksaan_fisik_anogenital" class="text-sm">Anogenital</label>
                            <input type="text" class="form-control form-control-sm" id="pemeriksaan_fisik_anogenital" name="pemeriksaan_fisik_anogenital" value="{{ $pemeriksaanFisik['anogenital'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pemeriksaan_fisik_ekstremitas_atas" class="text-sm">Ekstremitas Atas</label>
                            <input type="text" class="form-control form-control-sm" id="pemeriksaan_fisik_ekstremitas_atas" name="pemeriksaan_fisik_ekstremitas_atas" value="{{ $pemeriksaanFisik['ekstremitas_atas'] }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pemeriksaan_fisik_ekstremitas_bawah" class="text-sm">Ekstremitas Bawah</label>
                            <input type="text" class="form-control form-control-sm" id="pemeriksaan_fisik_ekstremitas_bawah" name="pemeriksaan_fisik_ekstremitas_bawah" value="{{ $pemeriksaanFisik['ekstremitas_bawah'] }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Penunjang, Terapi, Kesimpulan --}}
        <div class="card card-primary card-outline mt-3">
            <div class="card-header">
                <h5 class="card-title m-0">Penunjang, Diagnosis, Terapi & Kesimpulan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="PENUNJANG">Pemeriksaan Penunjang</label>
                            <input type="text" class="form-control" id="PENUNJANG" name="PENUNJANG" value="{{ $mcu->PENUNJANG ?? 'Terlampir' }}" placeholder="cth: Lab, Rontgen, dll">
                        </div>
                        <hr>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Diagnosis</label>
                            <div id="diagnosis-container">
                                @if(!empty($diagnoses) && count($diagnoses) > 0 && !empty($diagnoses[0]))
                                    @foreach($diagnoses as $index => $diagnosis)
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="DIAGNOSIS[]" value="{{ $diagnosis }}">
                                        @if($index > 0)
                                        <div class="input-group-append">
                                            <button class="btn btn-danger btn-sm remove-diagnosis" type="button"><i class="fas fa-times"></i></button>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2"><input type="text" class="form-control" name="DIAGNOSIS[]" value=""></div>
                                @endif
                            </div>
                            <button type="button" id="add-diagnosis" class="btn btn-success btn-sm mt-2"><i class="fas fa-plus"></i> Tambah Diagnosis</button>
                        </div>
                        <hr>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="RENCANATERAPI">Rencana Terapi</label>
                            <textarea class="form-control" id="RENCANATERAPI" name="RENCANATERAPI" rows="6">{{ $mcu->RENCANATERAPI ?? '' }}</textarea>
                        </div>
                        <hr>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="KESIMPULAN">Kesimpulan</label>
                            <input type="text" class="form-control" id="KESIMPULAN" name="KESIMPULAN" value="{{ $mcu->KESIMPULAN ?? '' }}" placeholder="cth: Sehat, Sakit, dll">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="DRPEMERIKSA">Dokter Pemeriksa</label>
                            <select class="form-control select2" id="DRPEMERIKSA" name="DRPEMERIKSA" style="width: 100%;">
                                <option value="">-- Pilih Dokter --</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->namapemeriksa }}" data-nopemeriksa="{{ $doctor->nopemeriksa }}" {{ isset($mcu) && $mcu->DRPEMERIKSA == $doctor->namapemeriksa ? 'selected' : '' }}>
                                        {{ $doctor->namapemeriksa }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="NOPEMERIKSA" name="NOPEMERIKSA" value="{{ $mcu->NOPEMERIKSA ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Simpan</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2 pada dropdown dokter
        $('#DRPEMERIKSA').select2({
            placeholder: "-- Pilih Dokter --",
            allowClear: true,
            theme: 'bootstrap4'
        });

        // Set NOPEMERIKSA saat pilihan dokter berubah
        $('#DRPEMERIKSA').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var noPemeriksa = selectedOption.data('nopemeriksa');
            $('#NOPEMERIKSA').val(noPemeriksa);
        });

        // Logika untuk tambah/hapus diagnosis
        $('#add-diagnosis').on('click', function() {
            var diagnosisCount = $('#diagnosis-container .input-group').length;
            if (diagnosisCount < 3) {
                var newDiagnosisInput = `
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="DIAGNOSIS[]" value="">
                        <div class="input-group-append">
                            <button class="btn btn-danger btn-sm remove-diagnosis" type="button"><i class="fas fa-times"></i></button>
                        </div>
                    </div>`;
                $('#diagnosis-container').append(newDiagnosisInput);
            }
            if ($('#diagnosis-container .input-group').length >= 3) {
                $(this).hide();
            }
        });

        $('#diagnosis-container').on('click', '.remove-diagnosis', function() {
            $(this).closest('.input-group').remove();
            if ($('#diagnosis-container .input-group').length < 3) {
                $('#add-diagnosis').show();
            }
        });

        // --- Perhitungan IMT Otomatis ---
        function calculateIMT() {
            const tb = parseFloat($('#TB').val()); // Tinggi Badan dalam cm
            const bb = parseFloat($('#BB').val()); // Berat Badan dalam kg
            const imtInput = $('#IMT');

            if (tb > 0 && bb > 0) {
                const tbInMeters = tb / 100;
                const imt = bb / (tbInMeters * tbInMeters);
                imtInput.val(imt.toFixed(2)); // Tampilkan dengan 2 angka desimal
            } else {
                imtInput.val(''); // Kosongkan jika input tidak valid
            }
        }

        // Panggil fungsi calculateIMT saat input TB atau BB berubah
        $('#TB, #BB').on('input', calculateIMT);
        // --- Akhir Perhitungan IMT Otomatis ---

        // AJAX form submission
        $('#form-mcu').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#form-mcu button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                },
                complete: function() {
                    $('#form-mcu button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Simpan');
                }
            });
        });
    });

</script>