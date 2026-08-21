@props(['uploadUrl', 'deleteUrl', 'previewUrl' => '#', 'hasVirtualTour'])

<div class="card border mb-4 shadow-sm rounded-4" style="max-width: 500px;" x-data="virtualTourUploader('{{ $uploadUrl }}', '{{ $deleteUrl }}', {{ $hasVirtualTour ? 'true' : 'false' }})">
    <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 px-4 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Virtual Tour 360</h6>
            <p class="text-muted mb-0" style="font-size: 0.8rem;">Unggah file ZIP (Max 500MB)</p>
        </div>
        <div>
            <template x-if="hasVirtualTour">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1">
                    <i class="fa-solid fa-check-circle me-1"></i>Aktif
                </span>
            </template>
            <template x-if="!hasVirtualTour">
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-1">
                    Tidak Aktif
                </span>
            </template>
        </div>
    </div>

    <div class="card-body p-4">
        <template x-if="hasVirtualTour">
            <div class="d-flex align-items-center justify-content-between bg-light border rounded-3 p-3">
                <div class="text-muted me-3" style="font-size: 0.85rem; line-height: 1.4;">
                    Virtual Tour aktif dan publik.
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ $previewUrl }}" class="btn btn-outline-primary btn-sm text-nowrap d-flex align-items-center gap-2 rounded-pill px-3">
                        <i class="fa-solid fa-eye"></i> Lihat
                    </a>
                    <button type="button" @click="deleteTour" :disabled="isDeleting" class="btn btn-outline-danger btn-sm text-nowrap d-flex align-items-center gap-2 rounded-pill px-3">
                        <template x-if="isDeleting">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </template>
                        <template x-if="!isDeleting">
                            <i class="fa-solid fa-trash"></i>
                        </template>
                        Hapus
                    </button>
                </div>
            </div>
        </template>

        <template x-if="!hasVirtualTour">
            <div>
                <div x-show="!isUploading">
                    <label for="dropzone-file" class="w-100 d-flex flex-column align-items-center justify-content-center border rounded-4 cursor-pointer text-center position-relative transition-all" 
                           style="border-style: dashed !important; border-width: 2px !important; border-color: #dee2e6 !important; height: 140px; cursor: pointer;"
                           x-bind:style="isDragging ? 'background-color: #f8f9fa; border-color: #0d6efd !important;' : 'background-color: transparent;'"
                           @dragover.prevent="isDragging = true"
                           @dragleave.prevent="isDragging = false"
                           @drop.prevent="isDragging = false; handleDrop($event)"
                           @mouseenter="$el.style.backgroundColor='#f8f9fa'"
                           @mouseleave="if(!isDragging) $el.style.backgroundColor='transparent'">
                        <div class="pt-3 pb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-cloud-arrow-up fs-5"></i>
                            </div>
                            <p class="mb-1 text-dark" style="font-size: 0.85rem;"><span class="fw-bold">Klik untuk upload</span> atau drag and drop</p>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">.ZIP file only</p>
                        </div>
                        <input id="dropzone-file" type="file" class="d-none" accept=".zip,application/zip" @change="handleFileSelect" />
                    </label>
                </div>

                <div x-show="isUploading" class="w-100 bg-light border rounded-4 p-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div class="text-truncate me-3">
                            <h6 class="mb-0 fw-bold text-dark text-truncate" x-text="fileName" style="max-width: 200px; font-size: 0.85rem;"></h6>
                            <p class="mb-0 text-muted mt-1" style="font-size: 0.75rem;">Mengunggah dan memproses...</p>
                        </div>
                        <span class="fw-bold text-primary" style="font-size: 0.85rem;" x-text="progress + '%'"></span>
                    </div>
                    <div class="progress rounded-pill mt-2" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" :style="'width: ' + progress + '%'"></div>
                    </div>
                    <p class="text-danger mt-2 mb-0" style="font-size: 0.75rem;" x-show="errorMessage" x-text="errorMessage"></p>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    if (typeof window.virtualTourUploader !== 'function') {
        window.virtualTourUploader = function(uploadUrl, deleteUrl, initialStatus) {
            return {
                uploadUrl,
                deleteUrl,
                hasVirtualTour: initialStatus,
                isUploading: false,
                isDeleting: false,
                isDragging: false,
                progress: 0,
                fileName: '',
                errorMessage: '',

                handleDrop(event) {
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        this.processFile(files[0]);
                    }
                },

                handleFileSelect(event) {
                    const files = event.target.files;
                    if (files.length > 0) {
                        this.processFile(files[0]);
                    }
                },

                async processFile(file) {
                    if (!file) return;
                    
                    if (file.type !== 'application/zip' && file.type !== 'application/x-zip-compressed' && !file.name.endsWith('.zip')) {
                        alert('Harap unggah file ZIP.');
                        return;
                    }

                    this.fileName = file.name;
                    this.isUploading = true;
                    this.progress = 0;
                    this.errorMessage = '';

                    const chunkSize = 2 * 1024 * 1024; // 2MB chunks
                    const totalChunks = Math.ceil(file.size / chunkSize);
                    const identifier = file.size + '-' + file.name.replace(/[^0-9a-zA-Z_-]/g, '');

                    for (let chunkNumber = 1; chunkNumber <= totalChunks; chunkNumber++) {
                        const start = (chunkNumber - 1) * chunkSize;
                        const end = Math.min(start + chunkSize, file.size);
                        const chunk = file.slice(start, end);

                        const formData = new FormData();
                        formData.append('file', chunk);
                        formData.append('resumableIdentifier', identifier);
                        formData.append('resumableFilename', file.name);
                        formData.append('resumableChunkNumber', chunkNumber);
                        formData.append('resumableTotalChunks', totalChunks);

                        try {
                            const response = await fetch(this.uploadUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            if (!response.ok) {
                                const data = await response.json();
                                throw new Error(data.error || 'Upload failed');
                            }

                            if (chunkNumber === totalChunks) {
                                this.progress = 100;
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                this.progress = Math.round((chunkNumber / totalChunks) * 95);
                            }
                        } catch (e) {
                            this.errorMessage = e.message;
                            this.isUploading = false;
                            document.getElementById('dropzone-file').value = '';
                            return;
                        }
                    }
                },

                async deleteTour() {
                    if (!confirm('Apakah Anda yakin ingin menghapus Virtual Tour ini?')) return;
                    
                    this.isDeleting = true;
                    try {
                        const response = await fetch(this.deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            this.hasVirtualTour = false;
                            window.location.reload();
                        } else {
                            alert('Gagal menghapus virtual tour.');
                        }
                    } catch (e) {
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.isDeleting = false;
                    }
                }
            };
        };
    }
</script>
