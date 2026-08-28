@props([
    'action',
    'title' => 'Pengalaman Anda',
    'itemType' => 'destinasi',
    'compact' => false
])

<div class="review-form-card rounded-4 p-3.5 p-md-4 border shadow-sm" style="background: #ffffff;">
    @auth
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-2.5">
                @if(auth()->user()->profile?->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->profile->avatar->object_key) }}" alt="{{ auth()->user()->name }}" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #047857 0%, #10b981 100%); font-size: 16px;">
                        {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                    </div>
                @endif
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <strong class="text-dark fs-7">{{ auth()->user()->name }}</strong>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill" style="font-size: 10px;">
                            <i class="fa-solid fa-circle-check me-1"></i> Pengulas Terverifikasi
                        </span>
                    </div>
                    <small class="text-muted d-block" style="font-size: 11.5px;">Beri rating & tulis pengalaman jujur Anda</small>
                </div>
            </div>
            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                <i class="fa-solid fa-bolt text-warning me-1"></i> Langsung Terbit
            </span>
        </div>

        <form method="POST" action="{{ $action }}" class="needs-validation review-interactive-form">
            @csrf
            
            <!-- 1. Interactive Star Rating -->
            <div class="mb-3.5 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <label class="form-label fw-bold text-dark fs-7 mb-1 d-flex align-items-center justify-content-between">
                    <span>Pilih Penilaian Anda <span class="text-danger">*</span></span>
                    <span class="rating-feedback-badge fw-semibold text-emerald fs-8" id="ratingLabelFeedback-{{ md5($action) }}">5 / 5 — Luar Biasa!</span>
                </label>
                <div class="star-rating-interactive d-flex align-items-center gap-1.5 my-1" id="starRatingContainer-{{ md5($action) }}" data-target="ratingInput-{{ md5($action) }}" data-label="ratingLabelFeedback-{{ md5($action) }}">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="btn p-0 star-btn border-0 bg-transparent" data-rating="{{ $i }}" aria-label="Beri {{ $i }} bintang" style="font-size: 26px; color: #f59e0b; transition: transform 0.15s ease, color 0.15s ease; cursor: pointer;">
                            <i class="fa-solid fa-star"></i>
                        </button>
                    @endfor
                    <input type="hidden" name="rating" id="ratingInput-{{ md5($action) }}" value="5" required>
                </div>
                <small class="text-muted d-block" style="font-size: 11px;">Klik salah satu bintang untuk menentukan skor penilaian Anda.</small>
            </div>

            <!-- 2. Review Title (Optional) -->
            <div class="mb-3">
                <label class="form-label fw-bold text-dark fs-7 mb-1">
                    Judul Ulasan <span class="text-muted fw-normal fs-8">(Opsional)</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-heading fs-7"></i></span>
                    <input type="text" name="title" class="form-control border-start-0 ps-1" placeholder="Contoh: Pengalaman seru, suasana asri dan ramah keluarga..." maxlength="120" style="font-size: 13.5px;">
                </div>
            </div>

            <!-- 3. Review Body -->
            <div class="mb-3">
                <label class="form-label fw-bold text-dark fs-7 mb-1">
                    Cerita & Ulasan Pengalaman <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <textarea name="body" class="form-control review-textarea p-3" rows="3" required minlength="5" maxlength="1000" placeholder="Ceritakan suasana tempat, kebersihan, pelayanan, spot favorit, tips perjalanan, atau saran berguna untuk pengunjung lainnya..." style="font-size: 13.5px; border-radius: 12px; resize: vertical;"></textarea>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-1">
                    <small class="text-muted d-flex align-items-center gap-1" style="font-size: 11px;">
                        <i class="fa-solid fa-shield-halved text-success"></i> Ulasan otomatis ditayangkan dan disaring secara aman.
                    </small>
                </div>
            </div>

            <!-- 4. Submit Action -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-1">
                <button type="submit" class="btn btn-lokantara fw-bold py-2.5 px-4 rounded-pill shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13.5px;">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Ulasan Saya</span>
                </button>
            </div>
        </form>
    @else
        <!-- GUEST CALLOUT CARD -->
        <div class="text-center py-3 px-2">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2.5 shadow-sm" style="width: 52px; height: 52px; background: #ecfdf5; color: #047857; font-size: 22px;">
                <i class="fa-solid fa-pen-nib"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1 fs-6">Bagikan Pengalaman Berharga Anda</h6>
            <p class="text-muted small mb-3" style="max-width: 480px; margin-inline: auto; font-size: 12.5px; line-height: 1.5;">
                Punya cerita atau penilaian menarik tentang {{ $itemType }} ini? Masuk dengan akun Jelajah Tegal Anda untuk memberikan rating dan ulasan jujur.
            </p>
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                <a href="{{ route('login') }}" class="btn btn-lokantara fw-bold px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13px;">
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
        5: '5 / 5 — Luar Biasa & Sangat Puas! ⭐⭐⭐⭐⭐',
        4: '4 / 5 — Bagus & Menyenangkan! ⭐⭐⭐⭐',
        3: '3 / 5 — Cukup Standar & Nyaman ⭐⭐⭐',
        2: '2 / 5 — Kurang Memuaskan ⭐⭐',
        1: '1 / 5 — Sangat Mengecewakan ⭐'
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
});
</script>
@endPushOnce
