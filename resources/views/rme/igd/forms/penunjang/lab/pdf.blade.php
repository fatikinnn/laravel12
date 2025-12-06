<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hasil Lab - {{ $patientInfo['Namapasien'] ?? 'Pasien' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header-table {
            width: 100%;
        }
        .header-table .logo {
            width: 80px;
            text-align: left;
        }
        .header-table .logo img {
            width: 70px;
            height: auto;
        }
        .header-table .title {
            text-align: center;
        }
        .header-table .title h4 {
            margin: 0;
            font-size: 18px;
        }
        .header-table .title p {
            margin: 2px 0;
            font-size: 12px;
        }
        .patient-info {
            margin-bottom: 20px;
            width: 100%;
        }
        .patient-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .patient-info td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .patient-info .label {
            font-weight: bold;
            width: 100px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .results-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .group-header {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .abnormal {
            font-weight: bold;
            color: #dc3545;
        }
        .results-table tr, .results-table td {
            page-break-inside: auto;
        }
        .hiv-table {
            margin-top: 20px;
            width: 50%; /* Lebar bisa disesuaikan */
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @foreach ($allKwitansiData as $kwitansiData)
        @php
            $patientInfo = $kwitansiData['patientInfo'];
            $groupedResults = $kwitansiData['groupedResults'];
            $hivResults = $kwitansiData['hivResults'];
            $waktuPeriksa = !empty($patientInfo['TglPemeriksaan']) ? \Carbon\Carbon::parse($patientInfo['TglPemeriksaan'])->format('d/m/Y') . ' ' . ($patientInfo['JamPeriksa'] ?? '') : '-';
        @endphp
        <div class="container">
            <div class="header">
                <table class="header-table">
                    <tr>
                        
                        <td class="title">
                            <h4>HASIL PEMERIKSAAN LABORATORIUM</h4>
                            <p>Tanggal Pemeriksaan: {{ $waktuPeriksa }}</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="patient-info">
                <table>
                    <tr>
                        <td class="label">No. RM</td>
                        <td>: {{ $patientInfo['NoReg'] ?? '-' }}</td>
                        <td class="label">Alamat</td>
                        <td>: {{ $patientInfo['Alamat'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Pasien</td>
                        <td>: {{ $patientInfo['Namapasien'] ?? '-' }} ({{ $patientInfo['JnKelamin'] ?? '-' }})</td>
                        <td class="label">Jam Hasil</td>
                        <td>: {{ $patientInfo['JamHasil'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tgl. Lahir</td>
                        <td>: {{ !empty($patientInfo['TanggalLahir']) ? \Carbon\Carbon::parse($patientInfo['TanggalLahir'])->format('d/m/Y') : '-' }} ({{ $patientInfo['Usia'] ?? '-' }})</td>
                        <td class="label">Pengirim</td>
                        <td>: {{ $patientInfo['PenanggungJawab'] ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <table class="results-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Jenis Pemeriksaan</th>
                        <th style="width: 20%;">Hasil</th>
                        <th style="width: 25%;">Nilai Rujukan</th>
                        <th style="width: 20%;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedResults as $groupName => $tests)
                        <tr class="group-header">
                            <td colspan="4">{{ $groupName }}</td>
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
                            <tr>
                                <td>{{ $test['Pemeriksaan'] ?? '-' }}</td>
                                <td class="{{ $isAbnormal ? 'abnormal' : '' }}">{{ $hasil }}</td>
                                <td>{{ $rujukan }}</td>
                                <td>{{ $test['Satuan'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            {{-- Hasil Pemeriksaan HIV --}}
            @if (!empty($hivResults))
                <h5 style="margin-top: 25px; margin-bottom: 10px; font-size: 14px; font-weight: bold; color: #dc3545;">Hasil Pemeriksaan HIV</h5>
                <table class="results-table hiv-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Tes</th>
                            <th style="text-align: center;">Hasil</th>
                        </tr>
                    </thead>Pada hasil lab pdf index.blade.php PenunjangController.php web.php user ingin mengirim langsung ke wa, bukan bentuk teks/link. apakah gambar bisa?

Jadi jika pdf tidak bisa bentuk gambar saja langsung dikirim ke wa

Alurnya:

Laravel generate PDF

Simpan file ke server

Klik tombol → buka WhatsApp Web

File siap dikirim ke pasien

Jadi dikirim itu ke wa dokter nanti, bukan wa pasien
                    <tbody>
                        @foreach ($hivResults as $hivTest)
                            <tr>
                                <td>{{ $hivTest['Pemeriksaan'] ?? '-' }}</td>
                                <td style="text-align: center; font-weight: bold;">
                                    @if (($hivTest['CR'] ?? '0') === '1')
                                        <span style="color: #dc3545;">Reaktif</span>
                                    @elseif (($hivTest['CN'] ?? '0') === '1')
                                        <span style="color: #28a745;">Non Reaktif</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="footer">
                Pemeriksa: <strong>{{ $patientInfo['Pemeriksa'] ?? '-' }}</strong>
            </div>
        </div>

        {{-- Tambahkan page break jika ini bukan iterasi terakhir --}}
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>