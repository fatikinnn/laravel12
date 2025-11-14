@php
    // Helper functions
    function getValue($data, $key, $default = '') {
        return isset($data[$key]) && !empty(trim($data[$key])) ? trim($data[$key]) : $default;
    }

    function isChecked($data, $key) {
        return isset($data[$key]) && $data[$key] == 1 ? 'checked' : '';
    }
@endphp

<div class="card-body">
    <form id="formPKN" action="{{ route('rme.igd.form.pkn.store') }}" method="POST">
        @csrf
        <input type="hidden" name="NOPENDAFTARAN" value="{{ htmlspecialchars($noPendaftaran) }}">
        <input type="hidden" name="NORM" value="{{ htmlspecialchars($norm) }}">
        <input type="hidden" name="selected_parts" id="selectedParts">

        <!-- Navigation tabs -->
        <ul class="nav nav-pills mb-4" id="pknTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="pkn-0-6jam-tab" data-toggle="pill" href="#pkn-0-6jam" role="tab" aria-controls="pkn-0-6jam" aria-selected="true">0-6 Jam</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pkn-6-48jam-tab" data-toggle="pill" href="#pkn-6-48jam" role="tab" aria-controls="pkn-6-48jam" aria-selected="false">6-48 Jam</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pkn-3-7hari-tab" data-toggle="pill" href="#pkn-3-7hari" role="tab" aria-controls="pkn-3-7hari" aria-selected="false">3-7 Hari</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pkn-8-28hari-tab" data-toggle="pill" href="#pkn-8-28hari" role="tab" aria-controls="pkn-8-28hari" aria-selected="false">8-28 Hari</a>
            </li>
        </ul>

        <div class="tab-content" id="pknTabsContent">
            <!-- 0-6 Jam Section -->
            <div class="tab-pane fade show active" id="pkn-0-6jam" role="tabpanel" aria-labelledby="pkn-0-6jam-tab">
                <h4 class="mb-4 text-primary"><i class="fas fa-clock mr-2"></i>Perawatan 0-6 Jam</h4>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="BB_0JAM" class="form-label">Berat Badan (gram)</label>
                        <input type="number" class="form-control" id="BB_0JAM" name="BB_0JAM" value="{{ htmlspecialchars(getValue($data, 'BB_0JAM')) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="PB_0JAM" class="form-label">Panjang Badan (cm)</label>
                        <input type="number" step="0.1" class="form-control" id="PB_0JAM" name="PB_0JAM" value="{{ htmlspecialchars(getValue($data, 'PB_0JAM')) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="LK_0JAM" class="form-label">Lingkar Kepala (cm)</label>
                        <input type="number" step="0.1" class="form-control" id="LK_0JAM" name="LK_0JAM" value="{{ htmlspecialchars(getValue($data, 'LK_0JAM')) }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Inisiasi Menyusu Dini</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="DINI_0JAM" name="DINI_0JAM" {{ isChecked($data, 'DINI_0JAM') }}>
                                    <label class="form-check-label" for="DINI_0JAM">Dini (IMD)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Intervensi</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="VITK1_0JAM" name="VITK1_0JAM" {{ isChecked($data, 'VITK1_0JAM') }}>
                                    <label class="form-check-label" for="VITK1_0JAM">Vitamin K1</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="SALEP_0JAM" name="SALEP_0JAM" {{ isChecked($data, 'SALEP_0JAM') }}>
                                    <label class="form-check-label" for="SALEP_0JAM">Salep/Tetes Mata</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="IMUNISASI_0JAM" name="IMUNISASI_0JAM" {{ isChecked($data, 'IMUNISASI_0JAM') }}>
                                    <label class="form-check-label" for="IMUNISASI_0JAM">Imunisasi HB-0</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="TGL_0JAM" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="TGL_0JAM" name="TGL_0JAM" value="{{ !empty($data['TGL_0JAM']) ? date('Y-m-d', strtotime($data['TGL_0JAM'])) : '' }}">
                    </div>
                    <div class="col-md-4">
                        <label for="JAM_0JAM" class="form-label">Jam</label>
                        <input type="time" class="form-control" id="JAM_0JAM" name="JAM_0JAM" value="{{ !empty($data['JAM_0JAM']) ? date('H:i', strtotime($data['JAM_0JAM'])) : '' }}">
                    </div>
                    <div class="col-md-4">
                        <label for="NOMORBATCH_0JAM" class="form-label">Nomor Batch</label>
                        <input type="text" class="form-control" id="NOMORBATCH_0JAM" name="NOMORBATCH_0JAM" value="{{ htmlspecialchars(getValue($data, 'NOMORBATCH_0JAM')) }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="PPIA_0JAM" class="form-label">PPIA</label>
                        <input type="text" class="form-control" id="PPIA_0JAM" name="PPIA_0JAM" value="{{ htmlspecialchars(getValue($data, 'PPIA_0JAM')) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="DIRUJUK_0JAM" class="form-label">Dirujuk ke</label>
                        <input type="text" class="form-control" id="DIRUJUK_0JAM" name="DIRUJUK_0JAM" value="{{ htmlspecialchars(getValue($data, 'DIRUJUK_0JAM')) }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="MASALAH_0JAM" class="form-label">Masalah yang Ditemukan</label>
                    <textarea class="form-control" id="MASALAH_0JAM" name="MASALAH_0JAM" rows="3">{{ htmlspecialchars(getValue($data, 'MASALAH_0JAM')) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="NAMATK_0JAM" class="form-label">Nama Tenaga Kesehatan</label>
                    <input type="text" class="form-control" id="NAMATK_0JAM" name="NAMATK_0JAM" value="{{ htmlspecialchars(getValue($data, 'NAMATK_0JAM', session('user.nama', ''))) }}">
                </div>
            </div>

            <!-- 6-48 Jam Section -->
            <div class="tab-pane fade" id="pkn-6-48jam" role="tabpanel" aria-labelledby="pkn-6-48jam-tab">
                <h4 class="mb-4 text-primary"><i class="fas fa-clock mr-2"></i>Perawatan 6-48 Jam</h4>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light"><h5 class="mb-0">Pemeriksaan</h5></div>
                            <div class="card-body">
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="MENYUSU_6JAM" name="MENYUSU_6JAM" {{ isChecked($data, 'MENYUSU_6JAM') }}><label class="form-check-label" for="MENYUSU_6JAM">Menyusu</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="TALIPUSAT_6JAM" name="TALIPUSAT_6JAM" {{ isChecked($data, 'TALIPUSAT_6JAM') }}><label class="form-check-label" for="TALIPUSAT_6JAM">Tali Pusat</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="VITK1_6JAM" name="VITK1_6JAM" {{ isChecked($data, 'VITK1_6JAM') }}><label class="form-check-label" for="VITK1_6JAM">Vitamin K1</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="SALEPMATA_6JAM" name="SALEPMATA_6JAM" {{ isChecked($data, 'SALEPMATA_6JAM') }}><label class="form-check-label" for="SALEPMATA_6JAM">Salep/Tetes Mata</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" id="IMUNISASI_6JAM" name="IMUNISASI_6JAM" {{ isChecked($data, 'IMUNISASI_6JAM') }}><label class="form-check-label" for="IMUNISASI_6JAM">Imunisasi HB</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light"><h5 class="mb-0">Kondisi Bayi</h5></div>
                            <div class="card-body">
                                <div class="form-group mb-2"><label for="BB_6JAM" class="form-label">Berat Badan (gram)</label><input type="number" class="form-control" id="BB_6JAM" name="BB_6JAM" value="{{ htmlspecialchars(getValue($data, 'BB_6JAM')) }}"></div>
                                <div class="form-group mb-2"><label for="PB_6JAM" class="form-label">Panjang Badan (cm)</label><input type="number" step="0.1" class="form-control" id="PB_6JAM" name="PB_6JAM" value="{{ htmlspecialchars(getValue($data, 'PB_6JAM')) }}"></div>
                                <div class="form-group mb-2"><label for="LK_6JAM" class="form-label">Lingkar Kepala (cm)</label><input type="number" step="0.1" class="form-control" id="LK_6JAM" name="LK_6JAM" value="{{ htmlspecialchars(getValue($data, 'LK_6JAM')) }}"></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" id="KONGENITAL_6JAM" name="KONGENITAL_6JAM" {{ isChecked($data, 'KONGENITAL_6JAM') }}><label class="form-check-label" for="KONGENITAL_6JAM">Kelainan Kongenital</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light"><h5 class="mb-0">Waktu Pemeriksaan</h5></div>
                            <div class="card-body">
                                <div class="form-group mb-2"><label for="TGL_6JAM" class="form-label">Tanggal</label><input type="date" class="form-control" id="TGL_6JAM" name="TGL_6JAM" value="{{ !empty($data['TGL_6JAM']) ? date('Y-m-d', strtotime($data['TGL_6JAM'])) : '' }}"></div>
                                <div class="form-group mb-2"><label for="JAM_6JAM" class="form-label">Jam</label><input type="time" class="form-control" id="JAM_6JAM" name="JAM_6JAM" value="{{ !empty($data['JAM_6JAM']) ? date('H:i', strtotime($data['JAM_6JAM'])) : '' }}"></div>
                                <div class="form-group"><label for="NOMORBATCH_6JAM" class="form-label">Nomor Batch</label><input type="text" class="form-control" id="NOMORBATCH_6JAM" name="NOMORBATCH_6JAM" value="{{ htmlspecialchars(getValue($data, 'NOMORBATCH_6JAM')) }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6"><label for="PPIA_6JAM" class="form-label">PPIA</label><input type="text" class="form-control" id="PPIA_6JAM" name="PPIA_6JAM" value="{{ htmlspecialchars(getValue($data, 'PPIA_6JAM')) }}"></div>
                    <div class="col-md-6"><label for="DIRUJUK_6JAM" class="form-label">Dirujuk ke</label><input type="text" class="form-control" id="DIRUJUK_6JAM" name="DIRUJUK_6JAM" value="{{ htmlspecialchars(getValue($data, 'DIRUJUK_6JAM')) }}"></div>
                </div>

                <div class="mb-4"><label for="MASALAH_6JAM" class="form-label">Masalah yang Ditemukan</label><textarea class="form-control" id="MASALAH_6JAM" name="MASALAH_6JAM" rows="3">{{ htmlspecialchars(getValue($data, 'MASALAH_6JAM')) }}</textarea></div>
                <div class="mb-4"><label for="NAMATK_6JAM" class="form-label">Nama Tenaga Kesehatan</label><input type="text" class="form-control" id="NAMATK_6JAM" name="NAMATK_6JAM" value="{{ htmlspecialchars(getValue($data, 'NAMATK_6JAM', session('user.nama', ''))) }}"></div>
            </div>

            <!-- 3-7 Hari Section -->
            <div class="tab-pane fade" id="pkn-3-7hari" role="tabpanel" aria-labelledby="pkn-3-7hari-tab">
                <h4 class="mb-4 text-primary"><i class="fas fa-calendar-day mr-2"></i>Perawatan 3-7 Hari</h4>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light"><h5 class="mb-0">Pemeriksaan</h5></div>
                            <div class="card-body">
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="MENYUSU_3HR" name="MENYUSU_3HR" {{ isChecked($data, 'MENYUSU_3HR') }}><label class="form-check-label" for="MENYUSU_3HR">Menyusu</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="TALIPUSAT_3HR" name="TALIPUSAT_3HR" {{ isChecked($data, 'TALIPUSAT_3HR') }}><label class="form-check-label" for="TALIPUSAT_3HR">Tali Pusat</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="TANDABAHAYA_3HR" name="TANDABAHAYA_3HR" {{ isChecked($data, 'TANDABAHAYA_3HR') }}><label class="form-check-label" for="TANDABAHAYA_3HR">Tanda Bahaya</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="KUNING_3HR" name="KUNING_3HR" {{ isChecked($data, 'KUNING_3HR') }}><label class="form-check-label" for="KUNING_3HR">Kuning</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" id="IMUNISASI_3HR" name="IMUNISASI_3HR" {{ isChecked($data, 'IMUNISASI_3HR') }}><label class="form-check-label" for="IMUNISASI_3HR">Imunisasi HB</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light"><h5 class="mb-0">Kondisi Bayi</h5></div>
                            <div class="card-body">
                                <div class="form-group mb-2"><label for="BB_3HR" class="form-label">Berat Badan (gram)</label><input type="number" class="form-control" id="BB_3HR" name="BB_3HR" value="{{ htmlspecialchars(getValue($data, 'BB_3HR')) }}"></div>
                                <div class="form-group mb-2"><label for="PB_3HR" class="form-label">Panjang Badan (cm)</label><input type="number" step="0.1" class="form-control" id="PB_3HR" name="PB_3HR" value="{{ htmlspecialchars(getValue($data, 'PB_3HR')) }}"></div>
                                <div class="form-group mb-2"><label for="LK_3HR" class="form-label">Lingkar Kepala (cm)</label><input type="number" step="0.1" class="form-control" id="LK_3HR" name="LK_3HR" value="{{ htmlspecialchars(getValue($data, 'LK_3HR')) }}"></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" id="KONGENITAL_3HR" name="KONGENITAL_3HR" {{ isChecked($data, 'KONGENITAL_3HR') }}><label class="form-check-label" for="KONGENITAL_3HR">Kelainan Kongenital</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-light"><h5 class="mb-0">Waktu Pemeriksaan</h5></div>
                            <div class="card-body">
                                <div class="form-group mb-2"><label for="TGL_3HR" class="form-label">Tanggal</label><input type="date" class="form-control" id="TGL_3HR" name="TGL_3HR" value="{{ !empty($data['TGL_3HR']) ? date('Y-m-d', strtotime($data['TGL_3HR'])) : '' }}"></div>
                                <div class="form-group mb-2"><label for="JAM_3HR" class="form-label">Jam</label><input type="time" class="form-control" id="JAM_3HR" name="JAM_3HR" value="{{ !empty($data['JAM_3HR']) ? date('H:i', strtotime($data['JAM_3HR'])) : '' }}"></div>
                                <div class="form-group"><label for="NOMORBATCH_3HR" class="form-label">Nomor Batch</label><input type="text" class="form-control" id="NOMORBATCH_3HR" name="NOMORBATCH_3HR" value="{{ htmlspecialchars(getValue($data, 'NOMORBATCH_3HR')) }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6"><label for="PPIA_3HR" class="form-label">PPIA</label><input type="text" class="form-control" id="PPIA_3HR" name="PPIA_3HR" value="{{ htmlspecialchars(getValue($data, 'PPIA_3HR')) }}"></div>
                    <div class="col-md-6"><label for="DIRUJUK_3HR" class="form-label">Dirujuk ke</label><input type="text" class="form-control" id="DIRUJUK_3HR" name="DIRUJUK_3HR" value="{{ htmlspecialchars(getValue($data, 'DIRUJUK_3HR')) }}"></div>
                </div>

                <div class="mb-4"><label for="MASALAH_3HR" class="form-label">Masalah yang Ditemukan</label><textarea class="form-control" id="MASALAH_3HR" name="MASALAH_3HR" rows="3">{{ htmlspecialchars(getValue($data, 'MASALAH_3HR')) }}</textarea></div>
                <div class="mb-4"><label for="NAMATK_3HR" class="form-label">Nama Tenaga Kesehatan</label><input type="text" class="form-control" id="NAMATK_3HR" name="NAMATK_3HR" value="{{ htmlspecialchars(getValue($data, 'NAMATK_3HR', session('user.nama', ''))) }}"></div>
            </div>

            <!-- 8-28 Hari Section -->
            <div class="tab-pane fade" id="pkn-8-28hari" role="tabpanel" aria-labelledby="pkn-8-28hari-tab">
                <h4 class="mb-4 text-primary"><i class="fas fa-calendar-week mr-2"></i>Perawatan 8-28 Hari</h4>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light"><h5 class="mb-0">Pemeriksaan</h5></div>
                            <div class="card-body">
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="MENYUSU_8HR" name="MENYUSU_8HR" {{ isChecked($data, 'MENYUSU_8HR') }}><label class="form-check-label" for="MENYUSU_8HR">Menyusu</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="TALIPUSAT_8HR" name="TALIPUSAT_8HR" {{ isChecked($data, 'TALIPUSAT_8HR') }}><label class="form-check-label" for="TALIPUSAT_8HR">Tali Pusat</label></div>
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="TANDABAHAYA_8HR" name="TANDABAHAYA_8HR" {{ isChecked($data, 'TANDABAHAYA_8HR') }}><label class="form-check-label" for="TANDABAHAYA_8HR">Tanda Bahaya</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" id="KUNING_8HR" name="KUNING_8HR" {{ isChecked($data, 'KUNING_8HR') }}><label class="form-check-label" for="KUNING_8HR">Kuning</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light"><h5 class="mb-0">Identifikasi Kuning</h5></div>
                            <div class="card-body">
                                <div class="alert alert-info mb-3"><small>Klik bagian tubuh bayi untuk menandai area yang tampak kuning.</small></div>
                                <div class="bayi-diagram mx-auto" style="max-width: 250px;">
                                    <svg viewBox="0 0 300 500" id="baby-diagram-svg">
                                        {{-- NOTE: You need to place the image '11-removebg-preview.png' in the 'public/images' directory --}}
                                        <image href="{{ asset('img/11-removebg-preview.png') }}" x="0" y="0" width="300" height="500" />

                                        <!-- Interactive body parts -->
                                        <circle class="body-part {{ getValue($data, 'KEPALA_8HR') ? 'active' : '' }}" data-name="kepala" cx="143" cy="71" r="15" />
                                        <ellipse class="body-part {{ getValue($data, 'DADA_8HR') ? 'active' : '' }}" data-name="dada" cx="142" cy="182" rx="15" />
                                        <ellipse class="body-part {{ getValue($data, 'PERUT_8HR') ? 'active' : '' }}" data-name="perut" cx="149" cy="264" rx="15" />
                                        <ellipse class="body-part {{ getValue($data, 'LENGANKANAN_8HR') ? 'active' : '' }}" data-name="lengan_kanan" cx="72" cy="181" rx="15" />
                                        <ellipse class="body-part {{ getValue($data, 'LENGAKKIRI_8HR') ? 'active' : '' }}" data-name="lengan_kiri" cx="231" cy="174" rx="15" />
                                        <circle class="body-part {{ getValue($data, 'TANGANKANAN_8HR') ? 'active' : '' }}" data-name="tangan_kanan" cx="69" cy="123" r="15" />
                                        <circle class="body-part {{ getValue($data, 'TANGANKIRI_8HR') ? 'active' : '' }}" data-name="tangan_kiri" cx="220" cy="122" r="15" />
                                        <ellipse class="body-part {{ getValue($data, 'BETISKANAN_8HR') ? 'active' : '' }}" data-name="paha_kanan" cx="77" cy="375" rx="15" />
                                        <ellipse class="body-part {{ getValue($data, 'BETISKIRI_8HR') ? 'active' : '' }}" data-name="paha_kiri" cx="230" cy="363" rx="15" />
                                        <circle class="body-part {{ getValue($data, 'TELAPKAKIKANAN_8HR') ? 'active' : '' }}" data-name="kaki_kanan" cx="86" cy="428" r="15" />
                                        <circle class="body-part {{ getValue($data, 'TELAPAKKAKIKIRI_8HR') ? 'active' : '' }}" data-name="kaki_kiri" cx="196" cy="414" r="15" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6"><label for="PPIA_8HR" class="form-label">PPIA</label><input type="text" class="form-control" id="PPIA_8HR" name="PPIA_8HR" value="{{ htmlspecialchars(getValue($data, 'PPIA_8HR')) }}"></div>
                    <div class="col-md-6"><label for="DIRUJUK_8HR" class="form-label">Dirujuk ke</label><input type="text" class="form-control" id="DIRUJUK_8HR" name="DIRUJUK_8HR" value="{{ htmlspecialchars(getValue($data, 'DIRUJUK_8HR')) }}"></div>
                </div>

                <div class="mb-4"><label for="MASALAH_8HR" class="form-label">Masalah yang Ditemukan</label><textarea class="form-control" id="MASALAH_8HR" name="MASALAH_8HR" rows="3">{{ htmlspecialchars(getValue($data, 'MASALAH_8HR')) }}</textarea></div>
                <div class="mb-4"><label for="NAMATK_8HR" class="form-label">Nama Tenaga Kesehatan</label><input type="text" class="form-control" id="NAMATK_8HR" name="NAMATK_8HR" value="{{ htmlspecialchars(getValue($data, 'NAMATK_8HR', session('user.nama', ''))) }}"></div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

<style>
    .body-part {
        fill: rgba(255, 193, 7, 0.2); /* Semi-transparent yellow */
        stroke: #6c757d;
        stroke-width: 2;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .body-part:hover {
        stroke: #0d6efd;
        fill: rgba(13, 110, 253, 0.3);
    }
    .body-part.active {
        fill: #ffc107; /* Solid yellow */
        stroke: #fd7e14;
    }
</style>

<script>
$(document).ready(function() {
    // Handle body part selection for jaundice identification
    const bodyParts = document.querySelectorAll('#baby-diagram-svg .body-part');
    const selectedPartsInput = document.getElementById('selectedParts');
    let selectedParts = {};

    // Initialize from existing data by checking the 'active' class set by Blade
    bodyParts.forEach(part => {
        const partName = part.dataset.name;
        if (part.classList.contains('active')) {
            selectedParts[partName] = true;
        }
    });
    selectedPartsInput.value = JSON.stringify(selectedParts);

    bodyParts.forEach(part => {
        part.addEventListener('click', () => {
            const partName = part.dataset.name;
            part.classList.toggle('active');
            selectedParts[partName] = part.classList.contains('active');
            selectedPartsInput.value = JSON.stringify(selectedParts);
        });
    });

    // Handle form submission with AJAX
    $('#formPKN').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var formData = new FormData(this);

        // Add spinner to submit button
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
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                // The global ajaxSuccess handler will automatically refresh the form
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
                // Restore button
                submitButton.html(originalButtonText).prop('disabled', false);
            }
        });
    });
});
</script>
