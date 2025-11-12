@php
    $filename = "Data_Resep_Kronis_" . date('Ymd') . ".xls";
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=$filename");

    $date_info = "";
    if (!empty($startDate) && !empty($endDate)) {
        $date_info = "Data dari tanggal " . \Carbon\Carbon::parse($startDate)->format('d/m/Y') . " sampai " . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
    } elseif (!empty($startDate)) {
        $date_info = "Data dari tanggal " . \Carbon\Carbon::parse($startDate)->format('d/m/Y');
    } elseif (!empty($endDate)) {
        $date_info = "Data sampai tanggal " . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
    } else {
        $date_info = "Data seluruh periode";
    }
@endphp
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Data Resep Kronis</title>
    <style>
        td { mso-number-format:\@; }
    </style>
</head>
<body>
    <h3>Data Resep Kronis</h3>
    <p>{{ $date_info }}</p>
    <table border="1">
        <tr>
            <th>No</th>
            <th>No Resep</th>
            <th>No SEP</th>
            <th>No RM</th>
            <th>Nama Pasien</th>
            <th>Asuransi</th>
            <th>DPJP</th>
            <th>Tgl Daftar</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Zigna</th>
        </tr>
        @forelse($reseps as $resep)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $resep->noresep }}</td>
                <td>{{ $resep->no_sep }}</td>
                {{-- Format khusus untuk No RM agar dianggap sebagai teks di Excel --}}
                <td style="mso-number-format:\@;">{{ $resep->norm }}</td>
                <td>{{ $resep->nama_pasien }}</td>
                <td>{{ $resep->asuransi }}</td>
                <td>{{ $resep->dpjp }}</td>
                <td>{{ $resep->tanggal_daftar->format('d/m/Y') }}</td>
                <td>{{ $resep->nama_barang }}</td>
                <td>{{ $resep->qty }}</td>
                <td>{{ $resep->zigna }}</td>
            </tr>
        @empty
            <tr><td colspan="11" align="center">Tidak ada data</td></tr>
        @endforelse
    </table>
</body>
</html>