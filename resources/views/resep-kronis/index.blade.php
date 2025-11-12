@extends('layouts.app')

@section('title', 'Data Resep Kronis')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Resep Kronis</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Resep Kronis</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .editable {
        cursor: pointer;
        border-bottom: 1px dashed #007bff;
    }

    .editable:hover {
        background-color: #f8f9fa;
    }

    .row-checkbox, #selectAll {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }

    #deleteSelectedBtn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Resep Kronis</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger btn-sm" id="deleteSelectedBtn" disabled>
                            <i class="fas fa-trash"></i> Hapus yang Dipilih
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="clearTableBtn">
                            <i class="fas fa-trash-alt"></i> Bersihkan Tabel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter Form --}}
                    <div class="mb-4 p-3 border rounded bg-light">
                        <form method="get" action="{{ route('resep-kronis.index') }}" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('resep-kronis.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                                <a href="{{ route('resep-kronis.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- Hidden container untuk semua ID berdasarkan filter --}}
                    <div id="allFilteredIds" data-ids='@json($allIds)' style="display: none;"></div>

                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-primary btn-sm me-2" id="selectAllFiltered">
                            <i class="fas fa-check-square"></i> Pilih Semua Data (Filter)
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="deselectAll">
                            <i class="fas fa-times-circle"></i> Batal Pilih Semua
                        </button>
                        <span class="text-muted" id="selectionInfo">0 data dipilih</span>
                    </div>

                    <div class="table-responsive">
                        <table id="resep-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 30px;"><input type="checkbox" id="selectAll"></th>
                                <th style="width: 50px;">No</th>
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
                        </thead>
                        <tbody>
                            @forelse($reseps as $resep)
                                <tr data-id="{{ $resep->id }}">
                                    <td><input type="checkbox" class="row-checkbox" value="{{ $resep->id }}"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="editable badge bg-primary" 
                                              data-id="{{ $resep->id }}" 
                                              data-no_sep="{{ $resep->no_sep }}" 
                                              data-norm="{{ $resep->norm }}" 
                                              data-tanggal_daftar="{{ $resep->tanggal_daftar }}">{{ $resep->noresep }}</span>
                                    </td>
                                    <td>{{ $resep->no_sep }}</td>
                                    <td>{{ $resep->norm }}</td>
                                    <td>{{ $resep->nama_pasien }}</td>
                                    <td>{{ $resep->asuransi }}</td>
                                    <td>{{ $resep->dpjp }}</td>
                                    <td>{{ \Carbon\Carbon::parse($resep->tanggal_daftar)->format('d/m/Y') }}</td>
                                    <td>{{ $resep->nama_barang }}</td>
                                    <td>{{ $resep->qty }}</td>
                                    <td>{{ $resep->zigna }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                            <span class="text-muted">Tidak ada data yang tersedia</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
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
    $(document).ready(function() {
        const allFilteredIds = JSON.parse(document.getElementById('allFilteredIds').getAttribute('data-ids') || '[]');
        let selectedIds = new Set();
        const table = $('#resep-table').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            columnDefs: [
                {
                    orderable: false,
                    targets: [0] // kolom 0 = checkbox
                }
            ]
        });

        function updateSelectionInfo() {
            $('#selectionInfo').text(`${selectedIds.size} dari ${allFilteredIds.length} data dipilih`);
            $('#deleteSelectedBtn').prop('disabled', selectedIds.size === 0);
        }

        function syncCheckboxes() {
            $('.row-checkbox').each(function() {
                $(this).prop('checked', selectedIds.has($(this).val().toString()));
            });
            const allVisibleOnPage = $('.row-checkbox:visible');
            const allVisibleChecked = allVisibleOnPage.length > 0 && allVisibleOnPage.filter(':checked').length === allVisibleOnPage.length;
            $('#selectAll').prop('checked', allVisibleChecked);
        }

        $('#selectAll').on('change', function() {
            const isChecked = this.checked;
            $('.row-checkbox:visible').each(function() {
                const id = $(this).val().toString();
                $(this).prop('checked', isChecked);
                if (isChecked) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }
            });
            updateSelectionInfo();
        });

        $('#resep-table tbody').on('change', '.row-checkbox', function() {
            const id = $(this).val().toString();
            if (this.checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
            updateSelectionInfo();
            syncCheckboxes();
        });

        $('#selectAllFiltered').on('click', function() {
            allFilteredIds.forEach(id => selectedIds.add(id.toString()));
            syncCheckboxes();
            updateSelectionInfo();
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            Toast.fire({ icon: 'success', title: `Semua ${allFilteredIds.length} data berdasarkan filter telah dipilih` });
        });

        $('#deselectAll').on('click', function() {
            selectedIds.clear();
            syncCheckboxes();
            updateSelectionInfo();
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            Toast.fire({ icon: 'info', title: 'Semua pilihan dibatalkan.' });
        });

        table.on('draw', function() {
            syncCheckboxes();
        });

        // Editable 'noresep' - PERBAIKAN: kirim semua data yang diperlukan
        $('#resep-table tbody').on('click', '.editable', function() {
            const $span = $(this);
            const oldValue = $span.text().trim();
            const id = $span.data('id');
            const no_sep = $span.data('no_sep');
            const norm = $span.data('norm');
            const tanggal_daftar = $span.data('tanggal_daftar');

            $span.html(`<input type="text" class="form-control form-control-sm" value="${oldValue}">`);
            const $input = $span.find('input');
            $input.focus();

            $input.on('blur keypress', function(e) {
                if (e.type === 'keypress' && e.which !== 13) return;

                const newValue = $(this).val().trim();
                $span.html(oldValue).html('<i class="fas fa-spinner fa-spin"></i>');

                if (newValue !== '' && newValue !== oldValue) {
                    $.ajax({
                        url: `/resep-kronis/${id}`,
                        type: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}',
                            noresep: newValue,
                            no_sep: no_sep,
                            norm: norm,
                            tanggal_daftar: tanggal_daftar
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                // Update semua baris terkait di UI tanpa reload
                                $(`span.editable[data-no_sep="${no_sep}"][data-norm="${norm}"][data-tanggal_daftar="${tanggal_daftar}"]`).each(function() {
                                    const relatedId = $(this).data('id');
                                    const cell = table.cell($(this).closest('td'));
                                    const newSpan = `<span class="editable badge bg-primary" data-id="${relatedId}" data-no_sep="${no_sep}" data-norm="${norm}" data-tanggal_daftar="${tanggal_daftar}">${newValue}</span>`;
                                    cell.data(newSpan);
                                });
                                table.draw(false);

                                const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                                Toast.fire({ icon: 'success', title: response.message });
                            } else {
                                $span.html(oldValue);
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        }, error: function(xhr) {
                            $span.html(oldValue);
                            const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                            Swal.fire('Error!', errorMsg, 'error');
                        }
                    });
                } else {
                    $span.html(oldValue);
                }
                $input.blur();
            });
        });

        // Delete selected - PERBAIKAN: tampilkan jumlah data yang dihapus
        $('#deleteSelectedBtn').on('click', function() {
            const ids = Array.from(selectedIds);
            if (ids.length === 0) return;

            Swal.fire({
                title: 'Hapus Data Terpilih?',
                html: `Anda akan menghapus <b>${ids.length}</b> data. Nomor resep akan disesuaikan secara otomatis.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("resep-kronis.destroy-selected") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memproses...',
                                html: 'Sedang menghapus data dan menyesuaikan nomor resep.',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading() }
                            });
                        },
                        success: function(response) {
                            Swal.fire('Berhasil!', response.message + ' (' + response.deleted_count + ' data)', 'success').then(() => window.location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON.message || 'Terjadi kesalahan.', 'error');
                        }
                    });
                }
            });
        });

        // Clear table
        $('#clearTableBtn').on('click', function() {
            Swal.fire({
                title: 'Bersihkan Tabel?',
                text: "Semua data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("resep-kronis.clear") }}',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memproses...',
                                html: 'Sedang membersihkan tabel.',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading() }
                            });
                        },
                        success: function(response) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => window.location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON.message || 'Terjadi kesalahan.', 'error');
                        }
                    });
                }
            });
        });

    });
</script>
@endpush