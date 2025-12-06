@if (!$data)
    <div class="alert alert-warning text-center">
        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
        <h5 class="alert-heading">Data Tidak Ditemukan</h5>
        <p>Data Triage (RM3A) untuk pasien ini belum diisi.</p>
    </div>
@else
    <div class="triage-readonly-container">
        <div class="row">
            {{-- Kolom Kiri --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tanggal & Jam Triage</label>
                    <input type="text" class="form-control" value="{{ !empty($data->TANGGAL) ? \Carbon\Carbon::parse($data->TANGGAL)->format('d-m-Y H:i') : '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label>Cara Datang</label>
                    <input type="text" class="form-control" value="{{ $data->CARADATANG ?? '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label>Jenis Kasus</label>
                    <input type="text" class="form-control" value="{{ $data->JENISKASUS ?? '-' }}" readonly>
                </div>

                <div class="form-group">
                    <label>Keluhan Utama</label>
                    <textarea class="form-control" rows="3" readonly>{{ $data->KELUHANUTAMA ?? '-' }}</textarea>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tanda Vital</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend"><span class="input-group-text" style="width: 80px;">TD</span></div>
                        <input type="text" class="form-control" value="{{ $data->TD ?? '-' }} mmHg" readonly>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend"><span class="input-group-text" style="width: 80px;">HR</span></div>
                        <input type="text" class="form-control" value="{{ $data->HR ?? '-' }} x/m" readonly>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend"><span class="input-group-text" style="width: 80px;">RR</span></div>
                        <input type="text" class="form-control" value="{{ $data->RR ?? '-' }} x/m" readonly>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend"><span class="input-group-text" style="width: 80px;">Suhu</span></div>
                        <input type="text" class="form-control" value="{{ $data->SUHU ?? '-' }} °C" readonly>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text" style="width: 80px;">SpO2</span></div>
                        <input type="text" class="form-control" value="{{ $data->SPO2 ?? '-' }} %" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tingkat Kesadaran</label>
                    <input type="text" class="form-control" value="{{ $data->KESADARAN ?? '-' }}" readonly>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Triase Berdasarkan ATS (Australasian Triage Scale)</label>
                    <div class="p-3 border rounded bg-light">
                        @php
                            $atsLevel = $data->TRIAGEATS ?? null;
                            $atsMapping = [
                                'ATS 1' => 'Merah - Resusitasi',
                                'ATS 2' => 'Orange - Emergensi',
                                'ATS 3' => 'Kuning - Urgen',
                                'ATS 4' => 'Hijau - Semi Urgen',
                                'ATS 5' => 'Biru - Tidak Urgen',
                                'DOA' => 'Hitam - Death on Arrival',
                            ];
                            $atsColorMapping = [
                                'ATS 1' => 'danger',
                                'ATS 2' => 'warning',
                                'ATS 3' => 'yellow', // AdminLTE doesn't have a 'yellow' text color, so we might need custom style or use another color.
                                'ATS 4' => 'success',
                                'ATS 5' => 'primary',
                                'DOA' => 'dark',
                            ];
                            $colorClass = $atsColorMapping[$atsLevel] ?? 'secondary';
                            $text = $atsMapping[$atsLevel] ?? 'Belum ditentukan';
                        @endphp
                        <h4 class="text-center">
                            <span class="badge badge-{{ $colorClass }}" style="font-size: 1.2rem; padding: 0.8rem; @if($colorClass == 'yellow') background-color: #ffc107; color: #343a40; @endif">
                                {{ $text }}
                            </span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light mt-3">
            <div class="d-flex justify-content-between">
                <small>Petugas: <strong>{{ $data->PETUGAS ?? '-' }}</strong></small>
                <small>Terakhir Update: <strong>{{ !empty($data->TANGGALUPDATE) ? \Carbon\Carbon::parse($data->TANGGALUPDATE)->format('d-m-Y H:i:s') : '-' }}</strong></small>
            </div>
        </div>
    </div>
@endif