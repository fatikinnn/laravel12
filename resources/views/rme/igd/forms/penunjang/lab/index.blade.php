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
                <a href="{{ route('rme.igd.penunjang.lab.pdf', ['NoPendaftaran' => $patientInfo['NoPendaftaran'], 'NoKwitansi' => $patientInfo['NoKwitansi']]) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF Ini
                </a>
                <small class="text-muted">Pemeriksa: <strong>{{ $patientInfo['Pemeriksa'] ?? '-' }}</strong></small>
            </div>
        </div>
    @endforeach

    {{-- Tombol Export Semua PDF diletakkan di luar loop --}}
    <div id="export-pdf-button-template" class="d-none"> {{-- Changed ID to indicate it's a template --}}
        @php
            $firstPatientInfo = $allKwitansiData[0]['patientInfo'] ?? null;
        @endphp
        @if (count($allKwitansiData) > 1)
            {{-- Added a unique class to the button for easier targeting and removal --}}
            <a href="{{ route('rme.igd.penunjang.lab.pdf.all', ['NoPendaftaran' => $firstPatientInfo['NoPendaftaran']]) }}"
               target="_blank"
               class="btn btn-danger export-lab-pdf-button" {{-- Added class here --}}
               id="export-lab-pdf-btn" {{-- Kept ID for specific targeting if needed --}}
            >
                <i class="fas fa-file-pdf mr-1"></i> Export Semua ke PDF
            </a>
        @endif
    </div>

    <script>
        $(document).ready(function() {
            const templateContainer = $('#export-pdf-button-template');
            const buttonHtml = templateContainer.html();

            // Only proceed if there's a button to add (i.e., if count($allKwitansiData) > 1)
            if (buttonHtml.trim() !== '') {
                // Find the closest modal footer relative to the current script's parent
                // This assumes the script is loaded within the modal's content
                const modalFooter = templateContainer.closest('.modal-content').find('.modal-footer');

                // IMPORTANT: Remove any existing instances of the button before adding a new one
                modalFooter.find('.export-lab-pdf-button').remove();

                // Prepend the button HTML to the modal footer
                modalFooter.prepend(buttonHtml);
            }
        });
    </script>
@endif
