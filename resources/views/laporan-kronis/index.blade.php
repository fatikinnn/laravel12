@extends('layouts.app')

@section('title', 'Filter Data Obat Kronis')

@push('styles')
    
    <style>
        .detail-row {
            display: none;
        }
        .detail-row.show {
            display: table-row;
        }
        .selected-row {
            background-color: #e3f2fd !important;
        }
        .card-header {
            cursor: pointer;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .qty-input {
            width: 80px;
            display: inline-block;
            margin-right: 5px;
        }
        .badge-selected {
            background-color: #198754;
        }
        .resep-badge {
            background-color: #6c757d;
            color: white;
            font-weight: bold;
            margin-left: 5px;
        }
        .table-success .resep-badge {
            background-color: #198754;
        }
        .table-warning .resep-badge {
            background-color: #ffc107;
            color: #212529;
        }
        .start-no-input {
            width: 100px;
            display: inline-block;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .select-all-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .select-all-checkbox {
            margin-right: 10px;
            transform: scale(1.2);
        }
        .group-actions {
            display: flex;
            gap: 5px;
        }
        .group-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .group-header {
            cursor: pointer;
        }
        .group-header:hover {
            background-color: #f8f9fa !important;
        }
        .poli-badge {
            background-color: #6f42c1;
            margin-left: 5px;
        }
        .dpjp-badge {
            background-color: #20c997;
            margin-left: 5px;
        }
    </style>
@endpush

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Filter Data Obat Kronis</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Filter Data</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
        <div class="card shadow mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter text-primary me-2"></i>Filter Data
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('laporan-kronis.index') }}" id="filterForm" class="row g-3">
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-prescription text-primary me-2"></i>
                    Laporan Resep Kronis
                    <span class="badge bg-primary ms-2">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</span>
                </h5>
                <div class="card-tools">
                    <label for="startNo" class="me-2 mb-0 align-middle">Mulai No Resep:</label>
                    <input type="number" id="startNo" class="form-control form-control-sm start-no-input d-inline-block me-2" value="{{ $nextNo }}" min="1">
                    <button id="saveSelectedBtn" class="btn btn-success btn-sm">
                        <i class="fas fa-save me-2"></i>Simpan Data Terpilih
                        <span id="selectedCount" class="badge bg-white text-success ms-2">0</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="action-buttons">
                    <button id="selectAllBtn" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>Pilih Semua
                    </button>
                    <button id="deselectAllBtn" class="btn btn-secondary">
                        <i class="fas fa-times-circle me-2"></i>Batal Pilih Semua
                    </button>
                    <button id="expandAllBtn" class="btn btn-info">
                        <i class="fas fa-expand me-2"></i>Buka Semua Detail
                    </button>
                    <button id="collapseAllBtn" class="btn btn-warning">
                        <i class="fas fa-compress me-2"></i>Tutup Semua Detail
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th width="50px">No </th>
                                <th>No SEP</th>
                                <th>No RM</th>
                                <th>Nama Pasien</th>
                                <th>Asuransi</th>
                                <th>Poli</th>
                                <th>DPJP</th>
                                <th>Tanggal Daftar</th>
                                <th width="180px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $display_counter = 1; @endphp
                            @forelse ($groups as $group)
                                @php
                                    $header = $group['header'];
                                    $items = $group['items'];
                                @endphp
                                <tr class="group-header" data-group="group-{{ $display_counter }}">
                                    <td>{{ $display_counter }}</td>
                                    <td>{{ $header->NOSEP }}</td>
                                    <td>{{ $header->NORM }}</td>
                                    <td>{{ $header->NAMAPASIEN }}</td>
                                    <td>{{ $header->ASURANSI }}</td>
                                    <td>{{ $header->NAMAPOLI }}</td>
                                    <td>{{ $header->DPJP }}</td><td>{{ \Carbon\Carbon::parse($header->TANGGALDAFTAR)->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="group-actions">
                                            <button class="btn btn-sm btn-outline-primary detail-toggle" onclick="toggleDetails('group-{{ $display_counter }}')">
                                                <i class="fas fa-list"></i> Detail
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger deselect-group" data-group="group-{{ $display_counter }}">
                                                <i class="fas fa-times"></i> Batal Pilih Grup
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                @foreach ($items as $item)
                                @php
                                    $existing_key = $item->NOSEP . '|' . $item->NORM . '|' . Carbon\Carbon::parse($item->TANGGALDAFTAR)->format('Y-m-d') . '|' . $item->NAMABARANG;
                                @endphp
                                {{-- Hanya tampilkan item yang belum ada di database --}}
                                @if (!isset($existingData[$existing_key]))
                                    @php
                                        $qty = rtrim(rtrim($item->QTY, '0'), '.');
                                        $rp_jual = number_format($item->RPJUAL, 0, ',', '.');
                                        $isMultipleOf30 = ($qty % 30 == 0);
                                        $rowClass = $isMultipleOf30 ? 'table-success' : 'table-warning';
                                    @endphp
                                    <tr class="detail-row {{ $rowClass }}" data-group="group-{{ $display_counter }}" data-item-key="{{ $existing_key }}">
                                        <td colspan="8">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>{{ $item->NAMABARANG }}</strong> - {{ $item->JENISOBAT }}
                                                    <div class="mt-1"><small class="text-muted">Zigna: {{ $item->ZIGNA }}</small></div>
                                                    <div class="mt-1"><small class="text-muted">ID BPJS: {{ $item->ID_BPJS }}</small></div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-light text-dark">Rp. {{ $rp_jual }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input type="number" class="form-control form-control-sm qty-input" value="{{ $qty }}" min="1">
                                                <button class="btn btn-sm btn-outline-success select-btn ms-2"
                                                        data-no="{{ $display_counter }}"
                                                        data-id-bpjs="{{ $item->ID_BPJS }}"
                                                        data-item-key="{{ $existing_key }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @endforeach
                                @php $display_counter++; @endphp
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data yang tersedia untuk tanggal yang dipilih.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection

@push('scripts')

    <script>
    let selectedRows = [];

    function toggleDetails(groupId) {
        $(`.detail-row[data-group="${groupId}"]`).toggleClass('show');
        const btn = $(`.detail-toggle[onclick="toggleDetails('${groupId}')"]`);
        if (btn.find('i').hasClass('fa-list')) {
            btn.find('i').removeClass('fa-list').addClass('fa-minus');
        } else {
            btn.find('i').removeClass('fa-minus').addClass('fa-list');
        }
    }

    function expandAllDetails() {
        $('.detail-row').addClass('show');
        $('.detail-toggle i').removeClass('fa-list').addClass('fa-minus');
    }

    function collapseAllDetails() {
        $('.detail-row').removeClass('show');
        $('.detail-toggle i').removeClass('fa-minus').addClass('fa-list');
    }

    function updateSelectedCount() {
        $('#selectedCount').text(selectedRows.length);
        if (selectedRows.length > 0) {
            $('#selectedCount').addClass('badge-selected');
        } else {
            $('#selectedCount').removeClass('badge-selected');
        }
    }

    function selectAllItems() {
        $('.select-btn:not(.btn-danger)').each(function() {
            $(this).click();
        });
    }

    function deselectAllItems() {
        $('.select-btn.btn-danger').each(function() {
            $(this).click();
        });
    }

    function deselectGroupItems(groupId) {
        const groupItems = $(`.detail-row[data-group="${groupId}"] .select-btn.btn-danger`);
        if (groupItems.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Tidak Ada Data Terpilih', text: 'Tidak ada data yang dipilih pada grup ini', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
            return;
        }
        groupItems.each(function() { $(this).click(); });
        Swal.fire({ icon: 'info', title: 'Pilihan Grup Dibatalkan', text: 'Semua item dalam grup ini telah dihapus dari seleksi', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        setTimeout(expandAllDetails, 300);

        $(document).on('click', '.select-btn', function() {
            const button = $(this);
            const rowElement = button.closest('tr');
            const groupElement = rowElement.prevAll('.group-header[data-group="' + rowElement.data('group') + '"]').first();
            const qtyInput = rowElement.find('.qty-input');
            const rpJualText = rowElement.find('.badge').text().replace('Rp. ', '').replace(/\./g, '');

            const groupData = {
                no_resep: button.data('no'),
                no_sep: groupElement.find('td:nth-child(2)').text(),
                norm: groupElement.find('td:nth-child(3)').text(),
                nama_pasien: groupElement.find('td:nth-child(4)').text(),
                id_bpjs: button.data('id-bpjs'),
                asuransi: groupElement.find('td:nth-child(5)').text(),
                nama_poli: groupElement.find('td:nth-child(6)').text().trim(),
                dpjp: groupElement.find('td:nth-child(7)').text().trim(),
                tanggal_daftar: groupElement.find('td:nth-child(8)').text(),
                nama_barang: rowElement.find('td strong').text().split(' - ')[0],
                jenis_obat: rowElement.find('td').text().split(' - ')[1].split('\n')[0].trim(),
                qty: qtyInput.val(),
                rp_jual: rpJualText,
                zigna: rowElement.find('small:contains("Zigna:")').text().replace('Zigna: ', '').trim()
            };

            const tglParts = groupData.tanggal_daftar.split('/');
            const tglDaftarDb = `${tglParts[2]}-${tglParts[1]}-${tglParts[0]}`;

            const rowData = [
                groupData.no_resep, groupData.no_sep, groupData.norm, groupData.nama_pasien,
                groupData.id_bpjs, groupData.asuransi, groupData.nama_poli, groupData.dpjp,
                tglDaftarDb, groupData.nama_barang, groupData.jenis_obat, groupData.qty,
                groupData.rp_jual, groupData.zigna
            ].join('|');

            if (button.hasClass('btn-outline-success')) {
                selectedRows.push(rowData);
                button.removeClass('btn-outline-success').addClass('btn-danger').html('<i class="fas fa-times"></i>');
                rowElement.addClass('selected-row');
                Swal.fire({ icon: 'success', title: 'Ditambahkan', text: 'Item telah ditambahkan ke seleksi', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            } else {
                const index = selectedRows.indexOf(rowData);
                if (index > -1) {
                    selectedRows.splice(index, 1);
                }
                button.removeClass('btn-danger').addClass('btn-outline-success').html('<i class="fas fa-check"></i>');
                rowElement.removeClass('selected-row');
                Swal.fire({ icon: 'info', title: 'Dihapus', text: 'Item telah dihapus dari seleksi', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            }
            updateSelectedCount();
        });

        $(document).on('click', '.deselect-group', function(e) {
            e.stopPropagation();
            const groupId = $(this).data('group');
            deselectGroupItems(groupId);
        });

        $('#saveSelectedBtn').click(function() {
            if (selectedRows.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Tidak ada data', text: 'Tidak ada data yang dipilih untuk disimpan' });
                return;
            }
            const startNo = $('#startNo').val();

            Swal.fire({
                title: 'Konfirmasi Penyimpanan',
                text: `Anda yakin ingin menyimpan ${selectedRows.length} data terpilih dengan nomor resep mulai dari ${startNo}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('laporan-kronis.store') }}",
                        type: 'POST',
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'selected_rows': selectedRows,
                            'start_no': startNo
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({ title: 'Menyimpan Data', html: 'Sedang memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: response.status === 'success' ? 'success' : 'error',
                                title: response.status === 'success' ? 'Berhasil!' : 'Gagal!',
                                text: response.message,
                            }).then(() => {
                                if (response.status === 'success') {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            let errorMsg = 'Terjadi kesalahan saat menyimpan data';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            }
                            Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
                        }
                    });
                }
            });
        });

        $('#selectAllBtn').click(function() {
            selectAllItems();
            Swal.fire({ icon: 'success', title: 'Semua Item Dipilih', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
        });

        $('#deselectAllBtn').click(function() {
            if (selectedRows.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Tidak Ada Data Terpilih', text: 'Tidak ada data yang dipilih untuk dibatalkan' });
                return;
            }
            deselectAllItems();
            Swal.fire({ icon: 'info', title: 'Semua Item Dibatalkan', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
        });

        $('#expandAllBtn').click(function() { expandAllDetails(); });
        $('#collapseAllBtn').click(function() { collapseAllDetails(); });

        updateSelectedCount();
    });
    </script>
@endpush