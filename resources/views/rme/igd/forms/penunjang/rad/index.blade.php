@if (empty($allKwitansiData))
    <div class="alert alert-info text-center m-0">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5 class="alert-heading">Tidak Ada Hasil Radiologi</h5>
        <p class="mb-0">Tidak ditemukan hasil pemeriksaan radiologi untuk pasien ini.</p>
    </div>
@else
    @foreach ($allKwitansiData as $kwitansiData)
        @php
            $result = $kwitansiData['resultData'];
            $imagePath = $kwitansiData['imagePath'];
            $tanggalRD = !empty($result['TanggalRD']) ? \Carbon\Carbon::parse($result['TanggalRD'])->format('d/m/Y') : '-';
        @endphp

        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt mr-2"></i> Pemeriksaan Tanggal:
                    <span class="badge badge-info font-weight-normal">{{ $tanggalRD }}</span>
                </h5>
            </div>
            <div class="card-body">
                <h4 class="text-center text-primary mb-4">HASIL PEMERIKSAAN RADIOLOGI</h4>

                {{-- Informasi Pasien --}}
                <div class="row border-bottom pb-2 mb-3">
                    <div class="col-md-4"><strong>Nama Pasien:</strong> {{ $result['Namapasien'] ?? '-' }} ({{ $result['JnKelamin'] ?? '-' }})</div>
                    <div class="col-md-4"><strong>Usia:</strong> {{ $result['Usia'] ?? '-' }}</div>
                    <div class="col-md-4"><strong>No. RM:</strong> {{ $result['NoReg'] ?? '-' }}</div>
                </div>

                {{-- Hasil Pemeriksaan --}}
                <div class="row mb-3">
                    <div class="col-md-8"><strong>Hasil Pemeriksaan:</strong></div>
                    <div class="col-md-4"><strong>X-Foto No:</strong> {{ $result['Nomor'] ?? '-' }}</div>
                </div>
                <div class="p-3 bg-light rounded mb-4" style="white-space: pre-line;">
                    {{ $result['Hasil'] ?? 'Tidak ada hasil pemeriksaan' }}
                </div>

                {{-- Info Dokter dan Gambar --}}
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2"><strong>Yth. Sejawat:</strong> {{ $result['drPemeriksa'] ?? '-' }}</p>
                        <p class="mb-3"><strong>Tanggal:</strong> {{ $tanggalRD }}</p>

                        @if (!empty($result['Sejawat']))
                            <div class="p-3 bg-light rounded">
                                <strong>Catatan:</strong><br>
                                <div style="white-space: pre-line;">{{ $result['Sejawat'] }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4 text-center">
                        @if ($imagePath)
                            @php
                                // Ganti path lokal dengan path network jika perlu
                                $finalImagePath = str_ireplace('E:\\Fotorontgen\\', '\\\\192.168.30.6\\Fotorontgen\\', $imagePath);
                                
                                // Buat URL dengan path yang sudah disesuaikan
                                $imageUrl = route('rme.igd.penunjang.rad.image', ['path' => $finalImagePath]);
                            @endphp
                            <strong>Gambar Radiologi</strong>
                            <a href="{{ $imageUrl }}" data-lightbox="radiologi-group-{{ $loop->iteration }}" data-title="Radiologi - {{ $result['Namapasien'] ?? '' }} - {{ $tanggalRD }}">
                                <img src="{{ $imageUrl }}" class="img-thumbnail mt-2" style="max-width: 100%; height: auto; max-height: 250px;" alt="Gambar Radiologi">
                            </a>
                        @else
                            <div class="alert alert-secondary mt-4">Tidak ada foto hasil rontgen.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <script>
        // Re-initialize lightbox for newly loaded content
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': "Gambar %1 dari %2"
        });
    </script>
@endif
