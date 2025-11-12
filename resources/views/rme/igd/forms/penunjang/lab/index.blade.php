@if (empty($allKwitansiData))
    <div class="alert alert-info text-center m-0">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5 class="alert-heading">Tidak Ada Hasil Laboratorium</h5>
        <p class="mb-0">Tidak ditemukan hasil pemeriksaan laboratorium untuk pasien ini.</p>
    </div>
@else
    @foreach ($allKwitansiData as $kwitansiData)
        @php
            $patientInfo = $kwitansiData['patientInfo'];
            $groupedResults = $kwitansiData['groupedResults'];
            $hivResults = $kwitansiData['hivResults'];
            $waktuPeriksa = !empty($patientInfo['TglPemeriksaan']) ? \Carbon\Carbon::parse($patientInfo['TglPemeriksaan'])->format('d/m/Y') . ' ' . ($patientInfo['JamPeriksa'] ?? '') : '-';
        @endphp

        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt mr-2"></i> Pemeriksaan Tanggal:
                    <span class="badge badge-info font-weight-normal">{{ $waktuPeriksa }}</span>
                </h5>
            </div>
            <div class="card-body">
                {{-- Informasi Pasien --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">No. RM</dt>
                            <dd class="col-sm-8">: {{ $patientInfo['NoReg'] ?? '-' }}</dd>
                            <dt class="col-sm-4">Nama Pasien</dt>
                            <dd class="col-sm-8">: {{ $patientInfo['Namapasien'] ?? '-' }} ({{ $patientInfo['JnKelamin'] ?? '-' }})</dd>
                            <dt class="col-sm-4">Tgl. Lahir</dt>
                            <dd class="col-sm-8">: {{ !empty($patientInfo['TanggalLahir']) ? \Carbon\Carbon::parse($patientInfo['TanggalLahir'])->format('d/m/Y') : '-' }} ({{ $patientInfo['Usia'] ?? '-' }})</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Alamat</dt>
                            <dd class="col-sm-8">: {{ $patientInfo['Alamat'] ?? '-' }}</dd>
                            <dt class="col-sm-4">Jam Hasil</dt>
                            <dd class="col-sm-8">: {{ $patientInfo['JamHasil'] ?? '-' }}</dd>
                            <dt class="col-sm-4">Pengirim</dt>
                            <dd class="col-sm-8">: {{ $patientInfo['PenanggungJawab'] ?? '-' }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Hasil Pemeriksaan Reguler --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 30%;">Jenis Pemeriksaan</th>
                                <th style="width: 20%;">Hasil</th>
                                <th style="width: 30%;">Nilai Rujukan</th>
                                <th style="width: 20%;">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedResults as $groupName => $tests)
                                <tr class="bg-light">
                                    <td colspan="4" class="font-weight-bold">
                                        <i class="fas fa-folder-open mr-2 text-primary"></i>{{ $groupName }}
                                    </td>
                                </tr>
                                @foreach ($tests as $test)
                                    @php
                                        $hasil = $test['Hasil'] ?? '-';
                                        $rujukan = $test['NilaiRujukan'] ?? '-';
                                        $isAbnormal = false;
                                        if (is_numeric($hasil) && strpos($rujukan, '-') !== false) {
                                            list($min, $max) = array_map('trim', explode('-', $rujukan));
                                            if (is_numeric($min) && is_numeric($max)) {
                                                $isAbnormal = ($hasil < $min || $hasil > $max);
                                            }
                                        }
                                    @endphp
                                    <tr class="{{ $isAbnormal ? 'table-warning' : '' }}">
                                        <td>{{ $test['Pemeriksaan'] ?? '-' }}</td>
                                        <td>
                                            <strong class="{{ $isAbnormal ? 'text-danger' : '' }}">{{ $hasil }}</strong>
                                            @if($isAbnormal) <i class="fas fa-exclamation-circle text-danger ml-1"></i> @endif
                                        </td>
                                        <td>{{ $rujukan }}</td>
                                        <td>{{ $test['Satuan'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Hasil Pemeriksaan HIV --}}
                @if (!empty($hivResults))
                    <h6 class="mt-4 font-weight-bold text-danger"><i class="fas fa-virus mr-2"></i>Hasil Pemeriksaan HIV</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Tes</th>
                                    <th class="text-center">Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hivResults as $hivTest)
                                    <tr>
                                        <td>{{ $hivTest['Pemeriksaan'] ?? '-' }}</td>
                                        <td class="text-center">
                                            @if (($hivTest['CR'] ?? '0') === '1')
                                                <span class="badge badge-danger">Reaktif</span>
                                            @elseif (($hivTest['CN'] ?? '0') === '1')
                                                <span class="badge badge-success">Non Reaktif</span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="card-footer text-right">
                <small class="text-muted">Pemeriksa: <strong>{{ $patientInfo['Pemeriksa'] ?? '-' }}</strong></small>
            </div>
        </div>
    @endforeach
@endif

