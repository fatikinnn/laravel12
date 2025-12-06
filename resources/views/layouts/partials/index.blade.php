@extends('layouts.app')

@section('title', 'Monitoring MCU')

@push('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Monitoring MCU</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Monitoring</li>
                    <li class="breadcrumb-item active">MCU</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Medical Check Up (MCU)</h3>
            </div>
            <div class="card-body">
                <table id="mcu-table" class="table table-bordered table-striped table-hover nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Tgl Pemeriksaan</th>
                            <th>Nama Pasien</th>
                            <th>NIK</th>
                            <th>Tgl Lahir</th>
                            <th>Alamat</th>
                            <th>Jenis Kelamin</th>
                            <th>No. RM</th>
                            <th>Paket Pemeriksaan</th>
                            <th>TB</th>
                            <th>BB</th>
                            <th>IMT</th>
                            <th>Nadi</th>
                            <th>Pemeriksaan Fisik</th>
                            <th>DL</th>
                            <th>RO</th>
                            <th>Audiometri</th>
                            <th>Kesimpulan</th>
                            <th>Ket</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data akan diisi oleh DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('AdminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/pdfmale/pdfmake.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script>
        $(function () {
            $('#mcu-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                ajax: '{{ route('monitoring.mcu.data') }}',
                columns: [
                    { data: 'TGLJAM_ENTRY', name: 'm.TGLJAM_ENTRY', render: function(data) {
                        return data ? new Date(data).toLocaleString('id-ID') : '';
                    }},
                    { data: 'NAMAPASIEN', name: 'd.NAMAPASIEN' },
                    { data: 'NIK', name: 'd.NIK' },
                    { data: 'TANGGALLAHIR', name: 'd.TANGGALLAHIR', render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '';
                    }},
                    { data: 'ALAMAT', name: 'ALAMAT' },
                    { data: 'JNKEL', name: 'JNKEL' },
                    { data: 'NORM', name: 'd.NORM' },
                    { data: null, defaultContent: '', orderable: false, searchable: false }, // Paket Pemeriksaan
                    { data: 'TB', name: 'm.TB' },
                    { data: 'BB', name: 'm.BB' },
                    { data: 'IMT', name: 'm.IMT' },
                    { data: 'NADI', name: 'm.NADI' },
                    { data: 'PEMERIKSAANFISIK', name: 'm.PEMERIKSAANFISIK' },
                    { data: null, defaultContent: '', orderable: false, searchable: false }, // DL
                    { data: null, defaultContent: '', orderable: false, searchable: false }, // RO
                    { data: null, defaultContent: '', orderable: false, searchable: false }, // Audiometri
                    { data: 'KESIMPULAN', name: 'm.KESIMPULAN' },
                    { data: null, defaultContent: '', orderable: false, searchable: false }  // Ket
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Laporan Data MCU',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'colvis',
                        text: 'Tampilkan Kolom',
                        className: 'btn btn-info'
                    }
                ]
            });
        });
    </script>
@endpush