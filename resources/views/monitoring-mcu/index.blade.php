@extends('layouts.app')

@section('title', 'Monitoring MCU')

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
                    <li class="breadcrumb-item">Monitoring MCU</li>
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
                <div class="table-responsive">
                    <table id="mcu-table" class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
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
</div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#mcu-table').DataTable({
                processing: true,
                serverSide: true,
                // Opsi scrollX dan scrollCollapse dinonaktifkan.
                // Scrolling horizontal sekarang sepenuhnya ditangani oleh div.table-responsive dari Bootstrap.
                lengthChange: false,
                autoWidth: false,
                ajax: '{{ route('monitoring.mcu.data') }}',
                order: [[ 0, "desc" ]], // Tambahkan baris ini untuk default ordering
                columns: [ 
                    { 
                        data: null, 
                        name: null, 
                        orderable: false, 
                        searchable: false, 
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
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
                // Menggunakan struktur DOM yang lebih rapi dan konsisten dengan Bootstrap
                // B: Buttons, f: filter, t: table, i: info, p: pagination
                dom:  "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" + // 'l' untuk length, 'f' untuk filter
                      "<'row'<'col-sm-12'tr>>" +
                      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                pagingType: "full_numbers", // Menggunakan paginasi yang lebih lengkap
                language: {
                    "search": "Pencarian:",
                    "processing": "Memproses...",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "paginate": { "first": "<<", "last": ">>", "next": ">", "previous": "<" }
                },
                // Menambahkan tombol-tombol di atas tabel
                initComplete: function () {
                    var buttons = new $.fn.dataTable.Buttons( this, {
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
                    } ).container().appendTo( '#mcu-table_wrapper .col-md-6:eq(0)' );

                    // Memberi jarak antar tombol
                    buttons.find('.btn').addClass('mr-2');
                },
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]] // Opsi untuk "Show entries"
            });
        });
    </script>
@endpush