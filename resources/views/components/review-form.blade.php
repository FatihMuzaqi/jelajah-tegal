@props([
    'action',
    'title' => 'Pengalaman Anda',
    'itemType' => 'destinasi',
    'compact' => false
])

<div class="review-form-card rounded-4 p-3.5 p-md-4 border shadow-sm" style="background: #ffffff; border-color: #e2e8f0 !important;">
    <h5 class="fw-bold text-dark mb-3" style="font-size: 16px;">Tulis Ulasan Anda</h5>
    @auth
        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="needs-validation review-interactive-form">
            @csrf
            
            <!-- 1. Interactive Star Rating Box -->
            <div class="mb-3.5 p-3 rounded-3 text-center" style="background: #f0fdf4; border: 1px solid #86efac;">
                <div class="star-rating-interactive d-flex align-items-center justify-content-center gap-2 mb-1" id="starRatingContainer-{{ md5($action) }}" data-target="ratingInput-{{ md5($action) }}" data-label="ratingLabelFeedback-{{ md5($action) }}">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="btn p-0 star-btn border-0 bg-transparent" data-rating="{{ $i }}" aria-label="Beri {{ $i }} bintang" style="font-size: 24px; color: #f59e0b; transition: transform 0.15s ease, color 0.15s ease; cursor: pointer;">
                            <i class="fa-solid fa-star"></i>
                        </button>
                    @endfor
                    <input type="hidden" name="rating" id="ratingInput-{{ md5($action) }}" value="5" required>
                </div>
                <div class="rating-feedback-badge fw-semibold" id="ratingLabelFeedback-{{ md5($action) }}" style="font-size: 12px; color: #166534;">
                    5.0 / 5.0 — Luar Biasa & Sangat Puas!
                </div>
            </div>

            <!-- 2. Review Body (Cerita & Ulasan Pengalaman) -->
            <div class="mb-3.5">
                <label class="form-label text-dark fw-medium mb-1.5" style="font-size: 13px;">
                    Cerita & Ulasan Pengalaman <span class="text-danger">*</span>
                </label>
                <textarea name="body" class="form-control rounded-3 review-textarea" rows="3" required minlength="5" maxlength="1000" placeholder="Ceritakan suasana tempat, kebersihan, pelayanan, dan pengalaman Anda..." style="border: 1px solid #cbd5e1; font-size: 13.5px; padding: 10px 14px; background: #f8fafc; resize: vertical;"></textarea>
            </div>

            <!-- 3. Photo Upload Attachment with Live Preview -->
            <div class="mb-3.5">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <label class="form-label text-dark fw-medium mb-0" style="font-size: 13px;">
                        <i class="fa-solid fa-camera text-success me-1" style="color: #0d9488 !important;"></i> Foto Pengalaman <span class="text-muted fw-normal" style="font-size: 11.5px;">(Opsional, maks. 5 foto)</span>
                    </label>
                    <span class="badge bg-light text-muted border fw-normal" id="photoCountBadge-{{ md5($action) }}" style="font-size: 11px;">0 / 5 foto</span>
                </div>

                <!-- Hidden File Input -->
                <input type="file" name="photos[]" id="reviewPhotosInput-{{ md5($action) }}" class="d-none review-file-input" multiple accept="image/png,image/jpeg,image/jpg,image/webp" data-target-grid="previewGrid-{{ md5($action) }}" data-target-badge="photoCountBadge-{{ md5($action) }}">

                <!-- Dropzone / Upload Trigger Button -->
                <div class="review-upload-dropzone p-3 rounded-3 text-center cursor-pointer" id="dropzone-{{ md5($action) }}" onclick="document.getElementById('reviewPhotosInput-{{ md5($action) }}').click()" style="background: #f8fafc; border: 1.5px dashed #cbd5e1; transition: all 0.2s ease; cursor: pointer;">
                    <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; background: #ccfbf1; color: #0d9488; font-size: 15px;">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        <span class="fw-semibold text-dark mt-1" style="font-size: 12.5px;">Pilih Foto atau Seret ke Sini</span>
                        <span class="text-muted" style="font-size: 11px;">Format JPG, PNG, atau WebP (Maks. 5MB per foto)</span>
                    </div>
                </div>

                <!-- Responsive Live Preview Grid -->
                <div class="review-preview-grid d-flex flex-wrap gap-2 mt-2" id="previewGrid-{{ md5($action) }}"></div>
            </div>

            <!-- 4. Submit Action -->
            <div>
                <button type="submit" class="btn w-100 fw-bold text-white py-2.5 rounded-3 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background: #0d9488; font-size: 14px; border: none; transition: background-color 0.2s ease;">
                    <i class="fa-solid fa-paper-plane" style="font-size: 13px;"></i>
                    <span>Kirim Ulasan Saya</span>
                </button>
            </div>
        </form>
    @else
        <!-- GUEST CALLOUT CARD -->
        <div class="text-center py-3 px-2">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2.5 shadow-sm" style="width: 48px; height: 48px; background: #ecfdf5; color: #047857; font-size: 20px;">
                <i class="fa-solid fa-pen-nib"></i>
            </div>
            <p class="text-muted small mb-3" style="max-width: 480px; margin-inline: auto; font-size: 12.5px; line-height: 1.5;">
                Punya cerita atau penilaian menarik tentang {{ $itemType }} ini? Masuk dengan akun Jelajah Tegal Anda untuk memberikan rating dan ulasan jujur beserta foto pengalaman.
            </p>
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                <a href="{{ route('login') }}" class="btn fw-bold px-4 py-2 text-white rounded-3 shadow-sm d-inline-flex align-items-center gap-2" style="background: #0d9488; font-size: 13px;">
                    <i class="fa-regular fa-user"></i>
                    <span>Masuk untuk Tulis Ulasan</span>
                </a>
            </div>
        </div>
    @endauth
</div>

@pushOnce('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = {
        5: '5.0 / 5.0 — Luar Biasa & Sangat Puas!',
        4: '4.0 / 5.0 — Bagus & Menyenangkan!',
        3: '3.0 / 5.0 — Cukup Standar & Nyaman',
        2: '2.0 / 5.0 — Kurang Memuaskan',
        1: '1.0 / 5.0 — Sangat Mengecewakan'
    };

    document.querySelectorAll('.star-rating-interactive').forEach(container => {
        const input = document.getElementById(container.dataset.target);
        const labelEl = document.getElementById(container.dataset.label);
        const buttons = container.querySelectorAll('.star-btn');

        function updateStars(val) {
            buttons.forEach(btn => {
                const rating = parseInt(btn.dataset.rating, 10);
                const icon = btn.querySelector('i');
                if (rating <= val) {
                    btn.style.color = '#f59e0b';
                    btn.style.transform = 'scale(1.08)';
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                } else {
                    btn.style.color = '#cbd5e1';
                    btn.style.transform = 'scale(1)';
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                }
            });
            if (labelEl && labels[val]) {
                labelEl.textContent = labels[val];
            }
        }

        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                updateStars(parseInt(btn.dataset.rating, 10));
            });

            btn.addEventListener('click', () => {
                const val = parseInt(btn.dataset.rating, 10);
                if (input) input.value = val;
                updateStars(val);
            });
        });

        container.addEventListener('mouseleave', () => {
            const currentVal = input ? parseInt(input.value, 10) : 5;
            updateStars(currentVal);
        });

        // Init with 5 stars
        updateStars(5);
    });

    // 2. Photo Upload Live Previews & Drag-Drop Handling
    document.querySelectorAll('.review-file-input').forEach(input => {
        const grid = document.getElementById(input.dataset.targetGrid);
        const badge = document.getElementById(input.dataset.targetBadge);
        const dropzone = input.closest('form')?.querySelector('.review-upload-dropzone');
        let dt = new DataTransfer();

        function renderPreviews() {
            if (!grid) return;
            grid.innerHTML = '';
            
            Array.from(dt.files).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                const previewCard = document.createElement('div');
                previewCard.className = 'position-relative rounded-3 border overflow-hidden shadow-2xs preview-card';
                previewCard.style.cssText = 'width: 72px; height: 72px; background: #000; flex-shrink: 0; animation: fadeIn 0.2s ease;';

                reader.onload = (e) => {
                    previewCard.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.92;">
                        <button type="button" class="btn btn-danger position-absolute top-0 end-0 m-1 p-0 d-flex align-items-center justify-content-center rounded-circle border-0 shadow-sm" style="width: 18px; height: 18px; font-size: 9px; line-height: 1;" data-index="${index}" title="Hapus foto ini">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    `;

                    // Remove item handler
                    previewCard.querySelector('button').addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        const newDt = new DataTransfer();
                        Array.from(dt.files).forEach((f, i) => {
                            if (i !== index) newDt.items.add(f);
                        });
                        dt = newDt;
                        input.files = dt.files;
                        renderPreviews();
                    });
                };

                reader.readAsDataURL(file);
                grid.appendChild(previewCard);
            });

            if (badge) {
                badge.textContent = `${dt.files.length} / 5 foto`;
                if (dt.files.length > 0) {
                    badge.className = 'badge bg-teal-50 text-teal-700 border border-teal-200 fw-semibold';
                    badge.style.color = '#0f766e';
                    badge.style.background = '#f0fdfa';
                } else {
                    badge.className = 'badge bg-light text-muted border fw-normal';
                    badge.style.color = '';
                    badge.style.background = '';
                }
            }
        }

        input.addEventListener('change', () => {
            const files = Array.from(input.files);
            if (dt.files.length + files.length > 5) {
                alert('Maksimal 5 foto yang dapat diunggah per ulasan.');
            }
            files.forEach(file => {
                if (dt.files.length < 5 && file.type.startsWith('image/')) {
                    dt.items.add(file);
                }
            });
            input.files = dt.files;
            renderPreviews();
        });

        // Drag & Drop effects on dropzone
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.style.background = '#f0fdfa';
                    dropzone.style.borderColor = '#0d9488';
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.style.background = '#f8fafc';
                    dropzone.style.borderColor = '#cbd5e1';
                });
            });

            dropzone.addEventListener('drop', (e) => {
                const dtDropped = e.dataTransfer;
                if (dtDropped && dtDropped.files) {
                    const files = Array.from(dtDropped.files);
                    if (dt.files.length + files.length > 5) {
                        alert('Maksimal 5 foto yang dapat diunggah per ulasan.');
                    }
                    files.forEach(file => {
                        if (dt.files.length < 5 && file.type.startsWith('image/')) {
                            dt.items.add(file);
                        }
                    });
                    input.files = dt.files;
                    renderPreviews();
                }
            });
        }
    });
});
</script>
@endPushOnce
