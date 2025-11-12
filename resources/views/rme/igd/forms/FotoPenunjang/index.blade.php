<div class="card-body">
    {{-- Hidden fields for URLs and data --}}
    <input type="hidden" id="fp-nopendaftaran" value="{{ $noPendaftaran }}">
    <input type="hidden" id="fp-norm" value="{{ $norm }}">
    <input type="hidden" id="fp-user" value="{{ trim($user['username']) }}">
    <input type="hidden" id="fp-url-list" value="{{ route('rme.igd.form.fotopenunjang.list') }}">
    <input type="hidden" id="fp-url-upload" value="{{ route('rme.igd.form.fotopenunjang.upload') }}">
    <input type="hidden" id="fp-url-destroy" value="{{ route('rme.igd.form.fotopenunjang.destroy') }}">
    <input type="hidden" id="fp-url-show" value="{{ route('rme.igd.form.fotopenunjang.show') }}">

    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="photoTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="usg-tab" data-toggle="pill" href="#usg-content" role="tab" data-type="usg"><i class="fas fa-wave-square mr-1"></i>USG</a></li>
                <li class="nav-item"><a class="nav-link" id="ekg-tab" data-toggle="pill" href="#ekg-content" role="tab" data-type="ekg"><i class="fas fa-heartbeat mr-1"></i>EKG</a></li>
                <li class="nav-item"><a class="nav-link" id="spirometri-tab" data-toggle="pill" href="#spirometri-content" role="tab" data-type="spirometri"><i class="fas fa-lungs mr-1"></i>Spirometri</a></li>
                <li class="nav-item"><a class="nav-link" id="ctg-tab" data-toggle="pill" href="#ctg-content" role="tab" data-type="ctg"><i class="fas fa-baby-carriage mr-1"></i>CTG</a></li>
                <li class="nav-item"><a class="nav-link" id="echo-tab" data-toggle="pill" href="#echo-content" role="tab" data-type="echo"><i class="fas fa-satellite-dish mr-1"></i>ECHO</a></li>
                <li class="nav-item"><a class="nav-link" id="penunjangluar-tab" data-toggle="pill" href="#penunjangluar-content" role="tab" data-type="penunjangluar"><i class="fas fa-paperclip mr-1"></i>Penunjang Luar</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="photoTabsContent">
                {{-- Tab content will be generated here --}}
            </div>
        </div>
    </div>
</div>

<!-- Modal for full image view -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tampilan Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Foto Penunjang">
            </div>
            <div class="modal-footer">
                <a id="downloadLink" href="#" class="btn btn-primary" download><i class="fas fa-download mr-1"></i>Download</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .dropzone { border: 2px dashed #007bff; border-radius: 5px; padding: 25px; text-align: center; cursor: pointer; transition: all 0.3s; }
    .dropzone.dragover { border-color: #0056b3; background-color: #f8f9fa; }
    .dropzone-icon { font-size: 48px; color: #6c757d; margin-bottom: 10px; }
    .preview-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
    .preview-item { position: relative; width: 120px; height: 120px; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; }
    .preview-image { width: 100%; height: 100%; object-fit: cover; }
    .preview-remove { position: absolute; top: 2px; right: 2px; background: rgba(255,0,0,0.7); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; }
    .photo-card { position: relative; transition: all 0.3s; }
    .photo-card.selectable .photo-thumbnail { cursor: pointer; }
    .photo-card.selected { border: 3px solid #007bff; box-shadow: 0 0 10px rgba(0,123,255,.5); }
    .photo-thumbnail { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; cursor: zoom-in; }
    .delete-btn { position: absolute; top: 5px; right: 5px; z-index: 2; }
    .action-buttons { display: none; }
    .action-buttons.show { display: flex; gap: 10px; }
</style>

<script>
$(document).ready(function() {
    const config = {
        noPendaftaran: $('#fp-nopendaftaran').val(),
        norm: $('#fp-norm').val(),
        user: $('#fp-user').val(),
        urls: {
            list: $('#fp-url-list').val(),
            upload: $('#fp-url-upload').val(),
            destroy: $('#fp-url-destroy').val(),
            show: $('#fp-url-show').val(),
        }
    };

    const photoTypes = ['usg', 'ekg', 'spirometri', 'ctg', 'echo', 'penunjangluar'];
    let selectedFiles = {};

    // Generate Tab Content and Initialize
    photoTypes.forEach((type, index) => {
        selectedFiles[type] = [];
        const typeCapitalized = type.charAt(0).toUpperCase() + type.slice(1);
        const activeClass = index === 0 ? 'show active' : '';

        const tabHtml = `
            <div class="tab-pane fade ${activeClass}" id="${type}-content" role="tabpanel">
                <!-- Upload Form -->
                <div class="card card-info card-outline">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-upload mr-2"></i>Upload Foto ${typeCapitalized} Baru</h3></div>
                    <div class="card-body">
                        <form class="upload-form" data-type="${type}">
                            <div class="dropzone" data-type="${type}">
                                <div class="dropzone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p>Tarik & lepas file di sini, atau klik untuk memilih.</p>
                                <small class="text-muted">Hanya file JPG/JPEG (maks. 5MB per file)</small>
                                <input class="d-none" type="file" name="photos[]" accept="image/jpeg" capture="environment" multiple>
                            </div>
                            <div class="preview-container mt-3" data-type="${type}"></div>
                            <button type="submit" class="btn btn-primary mt-3 upload-button"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload</button>
                        </form>
                    </div>
                </div>

                <!-- Photo Gallery -->
                <div class="card card-primary card-outline mt-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-images mr-2"></i>Galeri Foto ${typeCapitalized}</h3>
                        <div class="card-tools">
                            <button class="btn btn-outline-primary btn-sm select-photos-btn" data-type="${type}"><i class="fas fa-check-square mr-1"></i>Pilih Foto</button>
                            <div class="action-buttons" data-type="${type}">
                                <button class="btn btn-danger btn-sm delete-selected-btn" data-type="${type}"><i class="fas fa-trash mr-1"></i>Hapus Terpilih</button>
                                <button class="btn btn-secondary btn-sm select-all-btn" data-type="${type}"><i class="fas fa-check-double mr-1"></i>Pilih Semua</button>
                                <button class="btn btn-secondary btn-sm cancel-selection-btn" data-type="${type}"><i class="fas fa-times mr-1"></i>Batal</button>
                                <span class="selection-info align-self-center ml-2 text-muted" data-type="${type}">0 foto terpilih</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row photo-container" data-type="${type}">
                            <div class="col-12 text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat...</p></div>
                        </div>
                    </div>
                </div>
            </div>`;
        $('#photoTabsContent').append(tabHtml);
    });

    // Load initial data for the active tab
    loadPhotos(photoTypes[0]);

    // Event Handlers
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        const type = $(e.target).data('type');
        loadPhotos(type);
    });

    // Drag & Drop and File Input
    $('.dropzone').on('click', function(e) {
        // Prevent infinite loop by checking if the click target is the input itself
        if ($(e.target).is('input[type="file"]')) {
            return;
        }
        $(this).find('input[type="file"]').click();
    });
    $('.dropzone input[type="file"]').on('change', function(e) { handleFiles(e.target.files, $(this).closest('.dropzone').data('type')); });
    $('.dropzone').on('dragover dragenter', function(e) { e.preventDefault(); $(this).addClass('dragover'); });
    $('.dropzone').on('dragleave drop', function(e) { e.preventDefault(); $(this).removeClass('dragover'); });
    $('.dropzone').on('drop', function(e) { handleFiles(e.originalEvent.dataTransfer.files, $(this).data('type')); });

    // Form Submission
    $('.upload-form').on('submit', function(e) {
        e.preventDefault();
        const type = $(this).data('type');
        if (selectedFiles[type].length === 0) {
            Swal.fire('Peringatan', 'Silakan pilih file foto terlebih dahulu.', 'warning');
            return;
        }
        uploadFiles(type);
    });

    // Selection Mode
    $('.select-photos-btn').on('click', function() { toggleSelectionMode(true, $(this).data('type')); });
    $('.cancel-selection-btn').on('click', function() { toggleSelectionMode(false, $(this).data('type')); });
    $('.select-all-btn').on('click', function() { selectAllPhotos($(this).data('type')); });
    $('.delete-selected-btn').on('click', function() { deleteSelectedPhotos($(this).data('type')); });

    // Dynamic event listeners for generated content
    $(document).on('click', '.preview-remove', function() {
        const type = $(this).closest('.preview-container').data('type');
        const index = $(this).parent().index();
        selectedFiles[type].splice(index, 1);
        $(this).parent().remove();
    });

    $(document).on('click', '.photo-card.selectable', function() {
        $(this).toggleClass('selected');
        updateSelectionInfo($(this).closest('.photo-container').data('type'));
    });

    $(document).on('click', '.photo-thumbnail', function() {
        if ($(this).closest('.photo-card').hasClass('selectable')) return;
        $('#modalImage, #downloadLink').attr('src', $(this).attr('src'));
        $('#imageModal').modal('show');
    });

    $(document).on('click', '.delete-btn', function(e) {
        e.stopPropagation();
        const type = $(this).data('type');
        const counter = $(this).data('counter');
        deleteSinglePhoto(type, counter);
    });

    // --- FUNCTIONS ---

    function loadPhotos(type) {
        const container = $(`.photo-container[data-type="${type}"]`);
        container.html('<div class="col-12 text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat...</p></div>');

        $.get(config.urls.list, { nopendaftaran: config.noPendaftaran, type: type })
            .done(function(response) {
                container.empty();
                if (response.status === 'success' && response.data.length > 0) {
                    response.data.forEach(photo => {
                        const imageUrl = `${config.urls.show}?type=${type}&filename=${encodeURIComponent(photo.DIR_FOTO.split('/').pop())}`;
                        const photoHtml = `
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="photo-card" data-counter="${photo.COUNTER}">
                                    <button class="btn btn-danger btn-xs delete-btn" data-type="${type}" data-counter="${photo.COUNTER}"><i class="fas fa-trash-alt"></i></button>
                                    <img src="${imageUrl}" class="photo-thumbnail" alt="Foto ${type}">
                                    <div class="photo-info small text-muted mt-1">
                                        <div><strong>Oleh:</strong> ${photo.USER_ENTRY}</div>
                                        <div><strong>Tgl:</strong> ${moment(photo.TGLJAM_ENTRY).format('DD/MM/YYYY HH:mm')}</div>
                                    </div>
                                </div>
                            </div>`;
                        container.append(photoHtml);
                    });
                } else {
                    container.html('<div class="col-12 text-center p-5"><i class="fas fa-image fa-2x text-muted"></i><p class="mt-2 text-muted">Belum ada foto.</p></div>');
                }
            })
            .fail(function() {
                container.html('<div class="col-12 text-center p-5 text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p class="mt-2">Gagal memuat data.</p></div>');
            });
    }

    function handleFiles(files, type) {
        const previewContainer = $(`.preview-container[data-type="${type}"]`);
        Array.from(files).forEach(file => {
            if (!file.type.match('image/jpeg')) {
                Swal.fire('Error', `File ${file.name} bukan JPG/JPEG.`, 'error');
                return;
            }
            if (file.size > 5 * 1024 * 1024) { // 5MB
                Swal.fire('Error', `File ${file.name} terlalu besar (maks. 5MB).`, 'error');
                return;
            }
            selectedFiles[type].push(file);
            const reader = new FileReader();
            reader.onload = (e) => {
                const previewHtml = `
                    <div class="preview-item">
                        <img src="${e.target.result}" class="preview-image">
                        <button type="button" class="preview-remove">&times;</button>
                    </div>`;
                previewContainer.append(previewHtml);
            };
            reader.readAsDataURL(file);
        });
    }

    function uploadFiles(type) {
        const formData = new FormData();
        formData.append('nopendaftaran', config.noPendaftaran);
        formData.append('norm', config.norm);
        formData.append('type', type);
        selectedFiles[type].forEach(file => formData.append('photos[]', file));

        const button = $(`.upload-form[data-type="${type}"] .upload-button`);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Mengupload...');

        $.ajax({
            url: config.urls.upload,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        // Optional: show progress bar
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil', response.message, 'success');
                    selectedFiles[type] = [];
                    $(`.preview-container[data-type="${type}"]`).empty();
                    loadPhotos(type);
                } else {
                    Swal.fire('Gagal', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Gagal mengupload file.';
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt mr-1"></i>Upload');
            }
        });
    }

    function toggleSelectionMode(isSelecting, type) {
        const container = $(`.photo-container[data-type="${type}"]`);
        if (isSelecting) {
            $(`.select-photos-btn[data-type="${type}"]`).hide();
            $(`.action-buttons[data-type="${type}"]`).addClass('show');
            container.find('.photo-card').addClass('selectable');
        } else {
            $(`.select-photos-btn[data-type="${type}"]`).show();
            $(`.action-buttons[data-type="${type}"]`).removeClass('show');
            container.find('.photo-card').removeClass('selectable selected');
            updateSelectionInfo(type);
        }
    }

    function updateSelectionInfo(type) {
        const count = $(`.photo-container[data-type="${type}"] .photo-card.selected`).length;
        $(`.selection-info[data-type="${type}"]`).text(`${count} foto terpilih`);
    }

    function selectAllPhotos(type) {
        const container = $(`.photo-container[data-type="${type}"]`);
        const allCards = container.find('.photo-card');
        const selectedCards = container.find('.photo-card.selected');

        if (allCards.length === selectedCards.length) {
            allCards.removeClass('selected'); // Deselect all
        } else {
            allCards.addClass('selected'); // Select all
        }
        updateSelectionInfo(type);
    }

    function deletePhotos(type, photosToDelete) {
        Swal.fire({
            title: `Hapus ${photosToDelete.length} foto?`,
            text: "Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                const promises = photosToDelete.map(counter => {
                    return $.ajax({
                        url: config.urls.destroy,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            nopendaftaran: config.noPendaftaran,
                            type: type,
                            counter: counter
                        }
                    });
                });

                Promise.all(promises).then(() => {
                    Swal.fire('Berhasil', 'Foto terpilih telah dihapus.', 'success');
                    loadPhotos(type);
                    toggleSelectionMode(false, type);
                }).catch(() => {
                    Swal.fire('Error', 'Gagal menghapus beberapa foto.', 'error');
                });
            }
        });
    }

    function deleteSelectedPhotos(type) {
        const selectedCounters = $(`.photo-container[data-type="${type}"] .photo-card.selected`)
            .map(function() { return $(this).data('counter'); }).get();
        
        if (selectedCounters.length === 0) {
            Swal.fire('Peringatan', 'Tidak ada foto yang dipilih.', 'warning');
            return;
        }
        deletePhotos(type, selectedCounters);
    }

    function deleteSinglePhoto(type, counter) {
        deletePhotos(type, [counter]);
    }
});
</script>