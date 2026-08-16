@extends('layouts.public')

@section('title', 'Tour Assistant AI - Rencana Liburan Otomatis Jelajah Tegal')
@section('meta-description', 'Rencanakan liburan impian Anda di Tegal secara otomatis menggunakan kecerdasan buatan. Dapatkan paket wisata, penginapan, kuliner, dan rental sesuai budget Anda.')

@section('content')
<style>
/* Tour Assistant Hero & Form Theme */
.ta-hero-banner {
    position: relative;
    background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #065f46 100%);
    color: #ffffff;
    padding: clamp(40px, 6vw, 70px) 0 clamp(60px, 8vw, 90px);
    overflow: hidden;
    text-align: center;
}
.ta-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    border-radius: 99px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 16px;
}
.ta-hero-title {
    font-size: clamp(28px, 5vw, 46px);
    font-weight: 900;
    letter-spacing: -0.02em;
    color: #ffffff;
    margin-bottom: 12px;
}
.ta-hero-title span {
    color: #34d399;
}
.ta-hero-desc {
    font-size: clamp(14px, 2vw, 17px);
    color: rgba(255, 255, 255, 0.9);
    max-width: 680px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Floating Form Card */
.ta-form-card {
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 20px 50px rgba(6, 78, 59, 0.12);
    margin-top: -50px;
    position: relative;
    z-index: 10;
}

/* Category Selection Card */
.ta-category-card {
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.25s ease;
    background: #ffffff;
    height: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    user-select: none;
}
.ta-category-card:hover {
    border-color: #a7f3d0;
    background: #f0fdf4;
    transform: translateY(-2px);
}
.ta-category-card.active {
    border-color: #059669;
    background: #ecfdf5;
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.15);
}
.ta-cat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 20px;
    flex-shrink: 0;
    transition: all 0.25s ease;
}
.ta-category-card.active .ta-cat-icon {
    color: #ffffff !important;
}

/* Trust Card */
.ta-trust-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    padding: 24px;
    text-align: center;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    height: 100%;
}
.ta-trust-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: #ecfdf5;
    color: #059669;
    display: grid;
    place-items: center;
    font-size: 24px;
    margin: 0 auto 14px;
}
</style>

<!-- 1. Hero Section -->
<section class="ta-hero-banner">
    <div class="container public-container">
        <div class="ta-hero-badge">
            <i class="fa-solid fa-wand-magic-sparkles text-warning"></i> AI Smart Travel Planner
        </div>
        <h1 class="ta-hero-title">
            Rencana Liburan Impian,<br><span>Dibuat Otomatis oleh AI</span>
        </h1>
        <p class="ta-hero-desc">
            Cukup tentukan budget, tanggal, dan preferensi layanan Anda. Sistem cerdas kami akan menyusun 3 opsi paket wisata terintegrasi multi-mitra secara presisi dan instan.
        </p>
    </div>
</section>

<!-- 2. Main Form Container -->
<div class="container public-container pb-5">
    <div class="ta-form-card p-4 p-md-5 mx-auto" style="max-width: 860px;">
        <form action="{{ route('tour-assistant.generate') }}" method="POST" id="ta-form">
            @csrf

            <!-- Section 1: Tanggal & Jadwal -->
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-emerald-subtle text-emerald rounded-circle p-2 fs-7 fw-bold" style="width: 28px; height: 28px; display: grid; place-items: center; background: #ecfdf5; color: #047857;">1</span>
                    <h5 class="fw-bold text-dark mb-0">Kapan Anda Berencana Liburan?</h5>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label small fw-bold text-secondary">
                            <i class="fa-solid fa-calendar-day text-emerald me-1"></i> Tanggal Keberangkatan
                        </label>
                        <input type="date" name="start_date" id="start_date" 
                            value="{{ old('start_date', date('Y-m-d', strtotime('+1 day'))) }}" 
                            min="{{ date('Y-m-d') }}" required
                            class="form-control form-control-lg rounded-3 border">
                        @error('start_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label small fw-bold text-secondary">
                            <i class="fa-solid fa-calendar-check text-emerald me-1"></i> Tanggal Kepulangan
                        </label>
                        <input type="date" name="end_date" id="end_date" 
                            value="{{ old('end_date', date('Y-m-d', strtotime('+3 days'))) }}" 
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                            class="form-control form-control-lg rounded-3 border">
                        @error('end_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <!-- Section 2: Budget & Jumlah Wisatawan -->
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-emerald-subtle text-emerald rounded-circle p-2 fs-7 fw-bold" style="width: 28px; height: 28px; display: grid; place-items: center; background: #ecfdf5; color: #047857;">2</span>
                    <h5 class="fw-bold text-dark mb-0">Berapa Budget & Jumlah Wisatawan?</h5>
                </div>

                <div class="row g-3">
                    <div class="col-md-7">
                        <label for="budget" class="form-label small fw-bold text-secondary">
                            <i class="fa-solid fa-wallet text-emerald me-1"></i> Total Alokasi Budget Liburan
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-dark fw-bold border">Rp</span>
                            <input type="number" name="budget" id="budget" 
                                value="{{ old('budget', 3000000) }}" min="50000" step="10000" required
                                class="form-control border fw-bold text-dark" placeholder="Contoh: 3000000">
                        </div>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5 text-xs" onclick="setBudget(1000000)">Rp 1 Juta</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5 text-xs" onclick="setBudget(2500000)">Rp 2.5 Juta</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5 text-xs" onclick="setBudget(5000000)">Rp 5 Juta</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5 text-xs" onclick="setBudget(10000000)">Rp 10 Juta</button>
                        </div>
                        @error('budget') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-5">
                        <label for="pax" class="form-label small fw-bold text-secondary">
                            <i class="fa-solid fa-users text-emerald me-1"></i> Jumlah Orang / Wisatawan
                        </label>
                        <div class="input-group input-group-lg">
                            <button type="button" class="btn btn-light border" onclick="changePax(-1)"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" name="pax" id="pax" value="{{ old('pax', 2) }}" min="1" max="50" required
                                class="form-control text-center fw-bold border" readonly>
                            <button type="button" class="btn btn-light border" onclick="changePax(1)"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        <div class="form-text small text-muted text-center mt-1">Wisatawan dewasa & anak-anak</div>
                        @error('pax') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <!-- Section 3: Kategori Layanan Pilihan -->
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-emerald-subtle text-emerald rounded-circle p-2 fs-7 fw-bold" style="width: 28px; height: 28px; display: grid; place-items: center; background: #ecfdf5; color: #047857;">3</span>
                        <h5 class="fw-bold text-dark mb-0">Pilih Layanan yang Ingin Dimasukkan</h5>
                    </div>
                    <span class="small text-muted">Bisa pilih lebih dari satu</span>
                </div>

                <div class="row g-3">
                    <!-- Hotel -->
                    <div class="col-md-4 col-sm-6">
                        <div class="ta-category-card active" onclick="toggleCat('cat_accommodation', this)">
                            <input type="checkbox" name="categories[]" value="accommodation" id="cat_accommodation" checked class="d-none">
                            <div class="ta-cat-icon" style="background: #3b82f6; color: #ffffff;">
                                <i class="fa-solid fa-bed"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark text-sm">Penginapan</div>
                                <small class="text-muted text-xs d-block">Hotel, Villa & Homestay</small>
                            </div>
                            <i class="fa-solid fa-circle-check text-success fs-5 check-indicator"></i>
                        </div>
                    </div>

                    <!-- Wisata -->
                    <div class="col-md-4 col-sm-6">
                        <div class="ta-category-card active" onclick="toggleCat('cat_tourism', this)">
                            <input type="checkbox" name="categories[]" value="tourism" id="cat_tourism" checked class="d-none">
                            <div class="ta-cat-icon" style="background: #10b981; color: #ffffff;">
                                <i class="fa-solid fa-mountain-sun"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark text-sm">Wisata</div>
                                <small class="text-muted text-xs d-block">Pantai, Guci & Alam</small>
                            </div>
                            <i class="fa-solid fa-circle-check text-success fs-5 check-indicator"></i>
                        </div>
                    </div>

                    <!-- Kuliner -->
                    <div class="col-md-4 col-sm-6">
                        <div class="ta-category-card active" onclick="toggleCat('cat_culinary', this)">
                            <input type="checkbox" name="categories[]" value="culinary" id="cat_culinary" checked class="d-none">
                            <div class="ta-cat-icon" style="background: #f97316; color: #ffffff;">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark text-sm">Kuliner</div>
                                <small class="text-muted text-xs d-block">Voucher Makan & Resto</small>
                            </div>
                            <i class="fa-solid fa-circle-check text-success fs-5 check-indicator"></i>
                        </div>
                    </div>

                    <!-- Event -->
                    <div class="col-md-6 col-sm-6">
                        <div class="ta-category-card active" onclick="toggleCat('cat_event', this)">
                            <input type="checkbox" name="categories[]" value="event" id="cat_event" checked class="d-none">
                            <div class="ta-cat-icon" style="background: #a855f7; color: #ffffff;">
                                <i class="fa-solid fa-calendar-star"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark text-sm">Event & Festival</div>
                                <small class="text-muted text-xs d-block">Tiket Acara & Pameran Budaya</small>
                            </div>
                            <i class="fa-solid fa-circle-check text-success fs-5 check-indicator"></i>
                        </div>
                    </div>

                    <!-- Rental -->
                    <div class="col-md-6 col-sm-6">
                        <div class="ta-category-card active" onclick="toggleCat('cat_rental', this)">
                            <input type="checkbox" name="categories[]" value="rental" id="cat_rental" checked class="d-none">
                            <div class="ta-cat-icon" style="background: #0ea5e9; color: #ffffff;">
                                <i class="fa-solid fa-car"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark text-sm">Rental Kendaraan</div>
                                <small class="text-muted text-xs d-block">Sewa Mobil & Motor Nyaman</small>
                            </div>
                            <i class="fa-solid fa-circle-check text-success fs-5 check-indicator"></i>
                        </div>
                    </div>
                </div>
                @error('categories') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-3">
                <button type="submit" class="btn btn-lg w-100 py-3 rounded-pill fw-bold text-white shadow-lg d-flex align-items-center justify-content-center gap-2"
                        style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none; font-size: 17px; box-shadow: 0 10px 25px rgba(4, 120, 87, 0.4);">
                    <i class="fa-solid fa-wand-magic-sparkles text-warning fs-5"></i>
                    <span>Generate Rekomendasi Itinerary Pintar</span>
                    <i class="fa-solid fa-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Trust Highlights -->
    <div class="row g-4 mt-4 max-w-5xl mx-auto" style="max-width: 860px;">
        <div class="col-md-4">
            <div class="ta-trust-card">
                <div class="ta-trust-icon">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Presisi Sesuai Budget</h6>
                <p class="text-muted small mb-0">Dana terbagi seimbang tanpa khawatir overbudget, dengan sisa uang saku jelas.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ta-trust-card">
                <div class="ta-trust-icon">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Multi-Mitra 1-Click</h6>
                <p class="text-muted small mb-0">Hotel, tiket wisata, kuliner, dan rental terpadu dalam satu invoice instan.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ta-trust-card">
                <div class="ta-trust-icon">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">E-Tiket & QR Code Resmi</h6>
                <p class="text-muted small mb-0">Tiket langsung aktif dan dapat dipindai saat tiba di loket masing-masing mitra.</p>
            </div>
        </div>
    </div>
</div>

<script>
function setBudget(amount) {
    document.getElementById('budget').value = amount;
}

function changePax(delta) {
    const input = document.getElementById('pax');
    let val = parseInt(input.value) || 1;
    val = Math.max(1, Math.min(50, val + delta));
    input.value = val;
}

function toggleCat(checkboxId, cardEl) {
    const checkbox = document.getElementById(checkboxId);
    checkbox.checked = !checkbox.checked;
    
    const indicator = cardEl.querySelector('.check-indicator');
    if (checkbox.checked) {
        cardEl.classList.add('active');
        indicator.className = 'fa-solid fa-circle-check text-success fs-5 check-indicator';
    } else {
        cardEl.classList.remove('active');
        indicator.className = 'fa-regular fa-circle text-muted fs-5 check-indicator';
    }
}
</script>
@endsection
