@if (empty($obatResults) && empty($bhpResults))
    <div class="alert alert-info text-center py-4">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5 class="alert-heading">Tidak Ada Data Obat</h5>
        <p>Tidak ditemukan data obat atau BHP untuk pasien ini.</p>
    </div>
@endif

{{-- Process Obat results if exists --}}
@if (!empty($obatResults))
    @php $firstResult = $obatResults[0]; @endphp
    <div class="card card-primary card-outline mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0"><i class="fas fa-pills mr-2"></i> LEMBAR PERINCIAN BON OBAT APOTIK</h4>
        </div>
        <div class="card-body">
            {{-- Patient Info --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">No. RM</dt>
                        <dd class="col-sm-8">: {{ $firstResult['NoRM'] ?? '-' }}</dd>
                        <dt class="col-sm-4">Nama Pasien</dt>
                        <dd class="col-sm-8">: {{ $firstResult['NamaPasien'] ?? '-' }}</dd>
                        <dt class="col-sm-4">Tgl. Masuk</dt>
                        <dd class="col-sm-8">: {{ !empty($firstResult['TglMasuk']) ? \Carbon\Carbon::parse($firstResult['TglMasuk'])->format('d/m/Y') : '-' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Usia</dt>
                        <dd class="col-sm-8">: {{ $firstResult['Umur'] ?? '-' }}</dd>
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8">: {{ $firstResult['Kelas'] ?? '-' }}</dd>
                        <dt class="col-sm-4">Asuransi</dt>
                        <dd class="col-sm-8">: {{ $firstResult['Asuransi'] ?? '-' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Obat Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light text-center">
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 20%;">Dokter</th>
                            <th style="width: 40%;">Obat</th>
                            <th style="width: 25%;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groupedObat as $group)
                            @php
                                $firstItem = true;
                                $rowCount = count($group['Items']);
                            @endphp
                            @foreach ($group['Items'] as $index => $item)
                                <tr>
                                    @if ($firstItem)
                                        <td rowspan="{{ $rowCount }}" class="text-center align-middle">{{ !empty($group['Tanggal']) ? \Carbon\Carbon::parse($group['Tanggal'])->format('d/m/Y') : '-' }}</td>
                                        <td rowspan="{{ $rowCount }}" class="align-middle">{{ $group['Dokter'] ?? '-' }}</td>
                                        @php $firstItem = false; @endphp
                                    @endif
                                    <td>
                                        {{ $item['NamaObat'] ?? '-' }}
                                        <div class="d-flex justify-content-between mt-1">
                                            @php
                                                $qty = $item['Qty'] ?? 0;
                                                $qtyFormatted = floor($qty) == $qty ? number_format($qty, 0, ',', '.') : number_format($qty, 2, ',', '.');
                                            @endphp
                                            <small class="text-muted">Jml: {{ $qtyFormatted }}</small>
                                            <small class="text-muted">Rp {{ number_format($item['RupiahJual'] ?? 0, 0, ',', '.') }}</small>
                                        </div>
                                    </td>
                                    @if ($index === 0)
                                        <td rowspan="{{ $rowCount }}" class="text-right align-middle">Rp {{ number_format($group['Subtotal'] ?? 0, 0, ',', '.') }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-active font-weight-bold">
                            <td colspan="3" class="text-right">TOTAL OBAT</td>
                            <td class="text-right">Rp {{ number_format($firstResult['RpTotal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endif

{{-- Process BHP results if exists --}}
@if (!empty($bhpResults))
    @php $firstBhpResult = $bhpResults[0]; @endphp
    <div class="card card-primary card-outline mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0"><i class="fas fa-medkit mr-2"></i> LEMBAR PERINCIAN BON (BHP) APOTIK</h4>
        </div>
        <div class="card-body">
            {{-- Patient Info --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">No. RM</dt>
                        <dd class="col-sm-8">: {{ $firstBhpResult['NoRM'] ?? '-' }}</dd>
                        <dt class="col-sm-4">Nama Pasien</dt>
                        <dd class="col-sm-8">: {{ $firstBhpResult['NamaPasien'] ?? '-' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Kelas</dt>
                        <dd class="col-sm-8">: {{ $firstBhpResult['Kelas'] ?? '-' }}</dd>
                        <dt class="col-sm-4">Asuransi</dt>
                        <dd class="col-sm-8">: {{ $firstBhpResult['Asuransi'] ?? '-' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- BHP Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light text-center">
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 20%;">Dokter</th>
                            <th style="width: 40%;">Alkes/BHP</th>
                            <th style="width: 25%;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groupedBhp as $group)
                            @php
                                $firstItem = true;
                                $rowCount = count($group['Items']);
                            @endphp
                            @foreach ($group['Items'] as $index => $item)
                                <tr>
                                    @if ($firstItem)
                                        <td rowspan="{{ $rowCount }}" class="text-center align-middle">{{ !empty($group['Tanggal']) ? \Carbon\Carbon::parse($group['Tanggal'])->format('d/m/Y') : '-' }}</td>
                                        <td rowspan="{{ $rowCount }}" class="align-middle">{{ $group['Dokter'] ?? '-' }}</td>
                                        @php $firstItem = false; @endphp
                                    @endif
                                    <td>
                                        {{ $item['NamaObat'] ?? '-' }}
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted">Jml: {{ (int)($item['Qty'] ?? 0) }}</small>
                                            <small class="text-muted">Rp {{ number_format($item['RupiahJual'] ?? 0, 0, ',', '.') }}</small>
                                        </div>
                                    </td>
                                    @if ($index === 0)
                                        <td rowspan="{{ $rowCount }}" class="text-right align-middle">Rp {{ number_format($group['Subtotal'] ?? 0, 0, ',', '.') }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-active font-weight-bold">
                            <td colspan="3" class="text-right">TOTAL BHP</td>
                            <td class="text-right">Rp {{ number_format($firstBhpResult['RpTotal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endif