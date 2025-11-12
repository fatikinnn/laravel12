<div class="card-body">
    {{-- Riwayat Perawatan --}}
    <div class="card card-outline card-info shadow-sm mb-4">
        <div class="card-header" data-card-widget="collapse" style="cursor: pointer;">
            <h3 class="card-title mt-1"><i class="fas fa-history mr-2"></i>Riwayat Data Perawatan (CPPTPERAWAT)</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 300px;">
                <table class="table table-bordered table-hover table-sm" id="perawatanTableRm17">
                    <thead class="thead-light text-center sticky-top">
                        <tr>
                            <th>User</th>
                            <th>Tanggal</th>
                            <th>GCS</th>
                            <th>Nyeri</th>
                            <th>BB</th>
                            <th>TB</th>
                            <th>Ms. Peroral</th>
                            <th>Ms. Parenteral</th>
                            <th>Ms. Lain</th>
                            <th>Kl. Urine</th>
                            <th>Kl. Muntah</th>
                            <th>Kl. Lain</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="perawatanBodyRm17">
                        <tr><td colspan="13" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Kolom Kiri: Form Input --}}
        <div class="col-lg-12">
            <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title mt-1"><i class="fas fa-edit mr-2"></i>Input Tanda Vital</h3>
                </div>
                <div class="card-body">
                    <form id="rm17Form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group"><label for="tanggalRm17">Tanggal</label><input type="date" class="form-control" id="tanggalRm17" required></div>
                            <div class="col-md-6 form-group"><label for="jamRm17">Jam</label><input type="time" class="form-control" id="jamRm17" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label for="tdRm17">Tekanan Darah</label><input type="text" class="form-control" id="tdRm17" placeholder="e.g. 120/80"></div>
                            <div class="col-md-6 form-group"><label for="nafasRm17">Pernafasan</label><input type="number" class="form-control" id="nafasRm17" placeholder="x/menit"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="nadiRm17">Nadi</label>
                                <select class="form-control" id="nadiRm17">
                                    <option value="">- Pilih Nadi -</option>
                                    @for ($i = 40; $i <= 180; $i += 4)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @if ($i == 160) <option value=">160">&gt;160</option> @endif
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="suhuRm17">Suhu (°C)</label>
                                <select class="form-control" id="suhuRm17">
                                    <option value="">- Pilih Suhu -</option>
                                    @for ($i = 350; $i <= 420; $i++)
                                        @php $val = $i / 10; @endphp
                                        <option value="{{ $val }}">{{ number_format($val, 1) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan TTV</button>
                            <button type="button" class="btn btn-outline-secondary" id="resetRm17Form"><i class="fas fa-sync-alt mr-1"></i> Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Grafik --}}
        <div class="col-lg-12">
            <div class="card card-outline card-secondary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mt-1"><i class="fas fa-chart-line mr-2"></i>Grafik Monitoring Tanda Vital</h3>
                </div>
                <div class="card-body">
                    <div style="position: relative; height:600px; width:100%;">
                        <canvas id="vitalChartRm17"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const noPendaftaran = "{{ $noPendaftaran }}";
    let vitalChart = null;

    // Set default tanggal dan jam
    function setDefaultDateTime() {
        const now = new Date();
        $('#tanggalRm17').val(now.toISOString().split('T')[0]);
        $('#jamRm17').val(now.toTimeString().slice(0, 5));
    }

    // Load Riwayat Perawatan (CPPTPERAWAT)
    function loadPerawatanData() {
        $('#perawatanBodyRm17').html('<tr><td colspan="12" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');
        $.get("{{ route('rme.igd.rm17.perawatanHistory') }}", { noPendaftaran: noPendaftaran }, function(response) {
            $('#perawatanBodyRm17').empty();
            if (response.status === 'success' && response.data.length > 0) {
                response.data.forEach(function(item) {
                    const tgl = item.TANGGAL ? moment(item.TANGGAL).format('DD/MM/YYYY') : '-';
                    const row = `
                        <tr>
                            <td>${item.USERENTRI || '-'}</td>
                            <td>${tgl}</td>
                            <td>${item.KESADARAN || '-'}</td>
                            <td>${item.SKOR || '-'}</td>
                            <td>${item.BB || '-'}</td>
                            <td>${item.TB || '-'}</td>
                            <td>${item.PERORAL || '-'}</td>
                            <td>${item.PARENTERAL || '-'}</td>
                            <td>${item.MLAIN || '-'}</td>
                            <td>${item.URIN || '-'}</td>
                            <td>${item.MUNTAH || '-'}</td>
                            <td>${item.KLAIN || '-'}</td>
                            <td>${item.CATATAN || '-'}</td>
                        </tr>`;
                    $('#perawatanBodyRm17').append(row);
                });
            } else {
                $('#perawatanBodyRm17').html('<tr><td colspan="13" class="text-center">Tidak ada riwayat perawatan ditemukan.</td></tr>');
            }
        }).fail(function() {
            $('#perawatanBodyRm17').html('<tr><td colspan="13" class="text-center text-danger">Gagal memuat data.</td></tr>');
        });
    }

    // Load Data untuk Grafik
    function loadChartData() {
        $.get("{{ route('rme.igd.rm17.vitalSignHistory') }}", { noPendaftaran: noPendaftaran }, function(response) {
            if (response.status === 'success') {
                renderChart(response.data);
            } else {
                Swal.fire('Error', 'Gagal memuat data grafik.', 'error');
            }
        }).fail(function() {
            Swal.fire('Error', 'Gagal menghubungi server untuk data grafik.', 'error');
        });
    }

    // Render Grafik
    function renderChart(data) {
        const ctx = document.getElementById('vitalChartRm17').getContext('2d');
        const labels = data.map(item => moment(item.TANGGAL).format('DD/MM') + ' ' + item.JAM);
        const nadiData = data.map(item => item.NADI ? parseFloat(item.NADI) : null);
        const suhuData = data.map(item => item.SUHU ? parseFloat(item.SUHU) : null);

        if (vitalChart) {
            vitalChart.destroy();
        }

        vitalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Nadi (x/menit)',
                        data: nadiData,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderWidth: 2,
                        yAxisID: 'yNadi',
                        tension: 0.1,
                        fill: false,
                        pointRadius: 6, // Ukuran titik
                        pointHoverRadius: 8, // Ukuran titik saat hover
                    },
                    {
                        label: 'Suhu (°C)',
                        data: suhuData,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        yAxisID: 'ySuhu',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: false,
                        pointRadius: 6, // Ukuran titik
                        pointHoverRadius: 8, // Ukuran titik saat hover
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                devicePixelRatio: 2, // Meningkatkan ketajaman di layar HiDPI
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    tooltip: { 
                        mode: 'index', 
                        intersect: false,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 }
                    },
                    legend: {
                        labels: {
                            font: { size: 14 }
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Grafik Tanda Vital Pasien',
                    font: { size: 16 }
                },
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Waktu Pengukuran',
                            fontSize: 14
                        },
                        ticks: {
                            fontSize: 12
                        }
                    }],
                    yAxes: [{
                        id: 'yNadi',
                        type: 'linear',
                        position: 'left',
                        scaleLabel: { display: true, labelString: 'Nadi', fontSize: 14 },
                        ticks: { min: 40, max: 180, fontSize: 12 }
                    }, {
                        id: 'ySuhu',
                        type: 'linear',
                        position: 'right',
                        scaleLabel: { display: true, labelString: 'Suhu', fontSize: 14 },
                        ticks: { 
                            min: 35, max: 42,
                            fontSize: 12
                        },
                        gridLines: {
                            drawOnChartArea: false,
                        },
                    }]
                }
            }
        });
    }

    // Simpan Data RM17
    $('#rm17Form').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            _token: "{{ csrf_token() }}",
            nopendaftaran: noPendaftaran,
            tanggal: $('#tanggalRm17').val(),
            jam: $('#jamRm17').val(),
            td: $('#tdRm17').val(),
            nafas: $('#nafasRm17').val(),
            nadi: $('#nadiRm17').val(),
            nonadi: $('#nadiRm17').prop('selectedIndex'),
            suhu: $('#suhuRm17').val(),
            nosuhu: $('#suhuRm17').prop('selectedIndex'),
        };

        if (!formData.tanggal || !formData.jam) {
            Swal.fire('Peringatan', 'Tanggal dan Jam wajib diisi.', 'warning');
            return;
        }

        const button = $(this).find('button[type="submit"]');
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: "{{ route('rme.igd.rm17.store') }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil', response.message, 'success');
                    $('#rm17Form').find('input[type="text"], input[type="number"], select').val('');
                    setDefaultDateTime();
                    loadChartData(); // Refresh grafik
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan server.';
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan TTV');
            }
        });
    });

    // Tombol Batal
    $('#resetRm17Form').on('click', function() {
        $('#rm17Form').find('input[type="text"], input[type="number"], select').val('');
        setDefaultDateTime();
        Swal.fire({
            toast: true, position: 'top-end',
            icon: 'info', title: 'Form input dibersihkan.',
            showConfirmButton: false, timer: 2000
        });
    });

    // Initial Load
    setDefaultDateTime();
    loadPerawatanData();
    loadChartData();
});
</script>