<form id="formPermintaanLab" action="{{ route('rme.igd.form.permintaan-penunjang.store') }}" method="POST">
    @csrf
    <input type="hidden" name="NOPENDAFTARAN" value="{{ $noPendaftaran }}">

    <!-- Card Informasi Umum -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Informasi Umum</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="DRPEMOHON">Dokter Pemohon</label>
                        <select class="form-control" id="DRPEMOHON" name="DRPEMOHON">
                            <option value="">-- Pilih Dokter --</option>
                            @foreach ($doctors ?? [] as $doctor)
                                <option value="{{ $doctor->NAMAPEMERIKSA }}">
                                    {{ $doctor->NAMAPEMERIKSA }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="DIAGNOSTIK">Diagnosa</label>
                        <input type="text" class="form-control" id="DIAGNOSTIK" name="DIAGNOSTIK" value="{{ $diagnostikDefault }}">
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="LAB_LUAR">Laboratorium Luar (Jika ada)</label>
                        <textarea class="form-control" id="LAB_LUAR" name="LAB_LUAR" rows="2" placeholder="Isi jika ada hasil lab dari luar"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="RONTG_LUAR">Rontgen Luar (Jika ada)</label>
                        <textarea class="form-control" id="RONTG_LUAR" name="RONTG_LUAR" rows="2" placeholder="Isi jika ada hasil rontgen dari luar"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EKG">Permintaan EKG</label>
                        <input type="text" class="form-control" id="EKG" name="EKG" placeholder="Contoh: EKG 12 Lead">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="USG">Permintaan USG</label>
                        <input type="text" class="form-control" id="USG" name="USG" placeholder="Contoh: USG Abdomen">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Permintaan Penunjang (Lab & Rad) -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Permintaan Penunjang</h3>
            <div class="card-tools">
                <ul class="nav nav-pills ml-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#lab-tab" data-toggle="tab">Laboratorium</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#rad-tab" data-toggle="tab">Radiologi</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Laboratorium Tab -->
                <div class="tab-pane active" id="lab-tab">
                    <div class="row mb-2">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="labGroupSelect">Pilih Grup Pemeriksaan Lab</label>
                                <select class="form-control" id="labGroupSelect">
                                    @foreach ($labGroups ?? [] as $group)
                                        <option value="{{ $group->NoGroupLab }}">{{ $group->NamaGroupLab }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="labServiceSearch">Cari Pemeriksaan Lab</label>
                                <input type="text" id="labServiceSearch" class="form-control" placeholder="Ketik untuk mencari...">
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary select-all-visible">Pilih Semua</button>
                                <button type="button" class="btn btn-sm btn-outline-danger deselect-all">Hapus Semua</button>
                            </div>
                            <div class="form-group mb-0">
                                <span class="info-badge badge badge-secondary">Tersedia: 0 | Dipilih: 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="services-container mt-3" style="max-height: 300px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px; border-radius: .25rem;">
                        <div class="text-center text-muted">Pilih grup untuk menampilkan pemeriksaan.</div>
                    </div>
                </div>

                <!-- Radiologi Tab -->
                <div class="tab-pane" id="rad-tab">
                    <div class="row mb-2">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="radGroupSelect">Pilih Grup Pemeriksaan Rad</label>
                                <select class="form-control" id="radGroupSelect">
                                    @foreach ($radGroups ?? [] as $group)
                                        <option value="{{ $group->NoGroupRad }}">{{ $group->NamaGroupRad }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="radServiceSearch">Cari Pemeriksaan Rad</label>
                                <input type="text" id="radServiceSearch" class="form-control" placeholder="Ketik untuk mencari...">
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary select-all-visible">Pilih Semua</button>
                                <button type="button" class="btn btn-sm btn-outline-danger deselect-all">Hapus Semua</button>
                            </div>
                            <div class="form-group mb-0">
                                <span class="info-badge badge badge-secondary">Tersedia: 0 | Dipilih: 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="services-container mt-3" style="max-height: 300px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px; border-radius: .25rem;">
                        <div class="text-center text-muted">Pilih grup untuk menampilkan pemeriksaan.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3 mb-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i> Simpan Permintaan
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // --- GLOBAL INITIALIZATION ---
    $('#DRPEMOHON').select2({
        theme: 'bootstrap4',
        placeholder: '-- Pilih Dokter --',
        allowClear: true
    });

    // Use Sets to store selected services globally
    let globallySelectedServices = new Set();
    let globallySelectedRadServices = new Set();

    // --- REUSABLE FUNCTIONS FOR TABS ---
    function initializeTab(tabId, config) {
        const tab = $(`#${tabId}`);
        const groupSelect = tab.find(config.groupSelect);
        const searchInput = tab.find(config.searchInput);
        const container = tab.find(config.container);
        const selectAllBtn = tab.find('.select-all-visible');
        const deselectAllBtn = tab.find('.deselect-all');
        const infoBadge = tab.find('.info-badge');
        const selectedSet = config.selectedSet;

        // Init Select2
        groupSelect.select2({
            theme: 'bootstrap4',
            placeholder: config.placeholder,
            allowClear: true
        }).on('select2:unselect', function(e) {
            $(this).val(0).trigger('change');
            e.preventDefault();
        });

        function updateInfo() {
            const totalAvailable = container.find('.service-item').length;
            const totalSelected = selectedSet.size;
            infoBadge.text(`Tersedia: ${totalAvailable} | Dipilih: ${totalSelected}`);
            infoBadge.toggleClass('badge-success', totalSelected > 0).toggleClass('badge-secondary', totalSelected === 0);
        }

        function loadServices(groupValue) {
            const groupCode = config.prefix + groupValue;
            container.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>');
            $.ajax({
                url: '{{ route('rme.igd.form.permintaan-penunjang.getLabServices') }}',
                type: 'GET',
                data: { group_code: groupCode },
                success: function(services) {
                    let content = '<div class="row">';
                    if (services.length > 0) {
                        const filteredServices = services.filter(service => service.Nomor != 0);
                        filteredServices.forEach(function(service) {
                            const serviceValue = `${service.Nomor}::${service.Layanan}`;
                            const isSelected = selectedSet.has(serviceValue);
                            const selectedClass = isSelected ? 'bg-success text-white' : 'bg-light';
                            content += `
                                <div class="col-md-4 mb-2">
                                    <div class="service-item p-2 border rounded ${selectedClass}" data-value="${serviceValue}" style="cursor: pointer;">
                                        ${service.Layanan}
                                    </div>
                                </div>`;
                        });
                    } else {
                        content += '<div class="col-12 text-center text-muted">Tidak ada layanan untuk grup ini.</div>';
                    }
                    content += '</div>';
                    container.html(content);
                    updateInfo();
                },
                error: function() {
                    container.html('<div class="text-center text-danger">Gagal memuat layanan.</div>');
                }
            });
        }

        // Event Listeners
        groupSelect.on('change', function() { loadServices($(this).val()); });

        searchInput.on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            container.find('.service-item').each(function() {
                const label = $(this).text().toLowerCase();
                $(this).closest('.col-md-4').toggle(label.includes(searchTerm));
            });
        });

        container.on('click', '.service-item', function() {
            const item = $(this);
            const serviceValue = item.data('value');
            if (selectedSet.has(serviceValue)) {
                selectedSet.delete(serviceValue);
                item.removeClass('bg-success text-white').addClass('bg-light');
            } else {
                selectedSet.add(serviceValue);
                item.addClass('bg-success text-white').removeClass('bg-light');
            }
            updateInfo();
        });

        selectAllBtn.on('click', function() {
            container.find('.col-md-4:visible .service-item').each(function() {
                const item = $(this);
                const serviceValue = item.data('value');
                if (!selectedSet.has(serviceValue)) {
                    selectedSet.add(serviceValue);
                    item.addClass('bg-success text-white').removeClass('bg-light');
                }
            });
            updateInfo();
        });

        deselectAllBtn.on('click', function() {
            selectedSet.clear();
            container.find('.service-item').removeClass('bg-success text-white').addClass('bg-light');
            updateInfo();
        });

        // Initial load
        groupSelect.val(0).trigger('change');
    }

    // --- INITIALIZE TABS ---
    initializeTab('lab-tab', {
        groupSelect: '#labGroupSelect',
        searchInput: '#labServiceSearch',
        container: '.services-container',
        selectedSet: globallySelectedServices,
        prefix: 'L',
        placeholder: 'Pilih Grup Pemeriksaan Lab'
    });

    initializeTab('rad-tab', {
        groupSelect: '#radGroupSelect',
        searchInput: '#radServiceSearch',
        container: '.services-container',
        selectedSet: globallySelectedRadServices,
        prefix: 'R',
        placeholder: 'Pilih Grup Pemeriksaan Rad'
    });

    // --- FORM SUBMISSION ---
    $('#formPermintaanLab').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        
        // Clear old arrays and append selected services
        formData.delete('LABORATORIUM[]');
        formData.delete('RADIOLOGI[]');

        globallySelectedServices.forEach(service => {
            formData.append('LABORATORIUM[]', service);
        });
        globallySelectedRadServices.forEach(service => {
            formData.append('RADIOLOGI[]', service);
        });

        const submitButton = $(this).find('button[type="submit"]');
        const originalButtonHtml = submitButton.html();

        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                $('#formPermintaanLab')[0].reset();
                $('#DRPEMOHON').val(null).trigger('change');
                
                // Reset both tabs
                globallySelectedServices.clear();
                globallySelectedRadServices.clear();
                $('#labGroupSelect').val(0).trigger('change');
                $('#radGroupSelect').val(0).trigger('change');

            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire('Gagal!', errorMessage, 'error');
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonHtml);
            }
        });
    });
});

</script>