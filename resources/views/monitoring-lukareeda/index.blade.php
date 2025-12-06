@extends('layouts.app')

@section('title', 'Monitoring Penilaian Luka REEDA')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Monitoring Penilaian Luka REEDA</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Monitoring Luka REEDA</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <!-- Main content -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Penilaian Luka REEDA</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="reeda-table" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Pendaftaran</th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Total Skor</th>
                                        <th>Interpretasi</th>
                                        <th>User Entry</th>
                                        <th>Tgl Entry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data akan diisi oleh DataTables --}}
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
@endsection

@push('scripts')
<script>
$(function () {
    $('#reeda-table').DataTable({
        processing: true, // Tetap tampilkan 'processing...'
        serverSide: false, // Matikan server-side processing
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        // DOM yang dioptimalkan untuk Bootstrap 4 dengan lengthChange: false
        dom: "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'>>" + // Baris atas: Search di kiri
             "<'row'<'col-sm-12'tr>>" + // Baris tengah: Tabel
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", // Baris bawah: Info di kiri, Paginasi di kanan
        // Mengatur tipe paginasi agar sesuai dengan gaya Bootstrap 4
        pagingType: "full_numbers",
        language: {
            "search": "Pencarian:",
            "processing": "Memproses...",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            paginate: {
                "first": "<<", "last": ">>", "next": ">", "previous": "<"
            }
        },
        ajax: "{{ route('monitoring.lukareeda.index') }}",
        columns: [
            { data: 'NOPENDAFTARAN', name: 'NOPENDAFTARAN' },
            { data: 'NORM', name: 'NORM' },
            { data: 'NAMAPASIEN', name: 'NAMAPASIEN' },
            { data: 'TOTALSKOR', name: 'TOTALSKOR' },
            {
                data: 'INTERPRETASI',
                name: 'INTERPRETASI',
                render: function(data, type, row) {
                    if (data === 'Normal') {
                        return '<span class="badge bg-success">Normal</span>';
                    } else if (data === 'Waspada') {
                        return '<span class="badge bg-warning text-dark">Waspada</span>';
                    } else if (data === 'Curiga Infeksi Luka') {
                        return '<span class="badge bg-danger">Curiga Infeksi Luka</span>';
                    }
                    return data;
                }
            },
            { data: 'USER_ENTRY', name: 'USER_ENTRY' },
            {
                data: 'TGLJAM_ENTRY',
                name: 'TGLJAM_ENTRY',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleString('id-ID') : '';
                }
            }
        ]
    });
});
</script>
@endpush