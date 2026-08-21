@extends('layouts.public')

@section('title', 'AI Travel Planner - Rancang Perjalanan Impian Anda Secara Instan')
@section('meta-description', 'Susun rencana perjalanan personal lengkap dengan rekomendasi tempat wisata terpopuler, kuliner favorit, dan rute hemat waktu yang dikonstruksi secara cerdas oleh AI Jelajah Tegal.')

@section('content')
<!-- 1. Hero Section Sesuai Referensi Gambar -->
<section class="ai-planner-hero">
    <div class="container public-container">
        <div class="row align-items-center g-5">
            <!-- Left Column: Copywriting & CTA -->
            <div class="col-12 col-lg-6 text-start">
                <div class="ai-planner-badge">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> AI TRAVEL PLANNER
                </div>
                <h1 class="ai-hero-title">
                    Rancang Perjalanan<br>
                    Impian Anda Secara<br>
                    <span class="ai-title-mint">Instan</span>
                </h1>
                <p class="ai-hero-desc">
                    Susun rencana perjalanan personal lengkap dengan rekomendasi tempat wisata terpopuler, kuliner favorit, dan rute hemat waktu yang dikonstruksi secara cerdas oleh AI kami.
                </p>
                <div>
                    <a href="#form-planner" class="ai-btn-cta">
                        <span>Rencanakan Sekarang &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Right Column: Interactive AI Card Mockup (Persis Sesuai Screenshot) -->
            <div class="col-12 col-lg-6">
                <div class="ai-preview-card-dark">
                    <!-- Header Card -->
                    <div class="ai-card-dark-header">
                        <h4 class="ai-card-dark-title">
                            <strong>Tegal:</strong> <span>Budaya & Kuliner</span>
                        </h4>
                        <span class="ai-card-dark-badge">3 Hari</span>
                    </div>

                    <!-- Connected Vertical Timeline Sesuai Gambar -->
                    <div class="ai-timeline-ring-wrap">
                        <!-- Timeline Item 1 -->
                        <div class="ai-timeline-ring-item">
                            <div class="ai-timeline-ring-dot-dark"></div>
                            <div class="ai-timeline-box-dark">
                                <div class="ai-timeline-box-time-mint">HARI 1 — 10:00 WIB</div>
                                <h5 class="ai-timeline-box-title-white">Tur Pemandian Air Panas Guci</h5>
                                <p class="ai-timeline-box-desc-muted">Eksplorasi mata air belerang alami lereng Gunung Slamet dengan pemandu lokal.</p>
                            </div>
                        </div>

                        <!-- Timeline Item 2 -->
                        <div class="ai-timeline-ring-item">
                            <div class="ai-timeline-ring-dot-dark"></div>
                            <div class="ai-timeline-box-dark">
                                <div class="ai-timeline-box-time-mint">HARI 1 — 13:00 WIB</div>
                                <h5 class="ai-timeline-box-title-white">Makan Siang Sate Kambing Wendy's</h5>
                                <p class="ai-timeline-box-desc-muted">Menikmati sate kambing muda empuk legendaris khas Tegal.</p>
                            </div>
                        </div>

                        <!-- Timeline Item 3 -->
                        <div class="ai-timeline-ring-item">
                            <div class="ai-timeline-ring-dot-dark"></div>
                            <div class="ai-timeline-box-dark">
                                <div class="ai-timeline-box-time-mint">HARI 1 — 16:30 WIB</div>
                                <h5 class="ai-timeline-box-title-white">Menikmati Sunset Pantai Alam Indah</h5>
                                <p class="ai-timeline-box-desc-muted">Menyaksikan pemandangan sunset memukau pesisir utara dari anjungan pantai.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Form Rencana Liburan Section -->
<section class="ai-planner-form-section" id="form-planner">
    <div class="container public-container">
        <div class="ai-form-card">
            <div class="text-center mb-4">
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-bold fs-8 mb-2">
                    <i class="fa-solid fa-sliders me-1"></i> Form Preferensi Liburan
                </span>
                <h2 class="fw-extrabold text-dark fs-2 mb-1" style="letter-spacing: -0.5px;">
                    Tentukan Preferensi Liburan Anda
                </h2>
                <p class="text-muted fs-7 mb-0">
                    AI kami akan menyusun alur perjalanan, waktu kegiatan, budget, dan destinasi yang paling ideal.
                </p>
            </div>

            @if(session('info'))
                <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-info fs-5"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            <form action="{{ route('tour-assistant.generate') }}" method="POST" id="aiPlannerForm">
                @csrf
                <div class="row g-4">
                    <!-- Tanggal Berangkat -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1.5">
                            <i class="fa-regular fa-calendar text-success me-1"></i> Tanggal Berangkat <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="start_date" id="startDate" 
                               value="{{ old('start_date', now()->addDays(1)->format('Y-m-d')) }}" 
                               min="{{ now()->format('Y-m-d') }}" 
                               class="form-control form-control-lg rounded-3 fs-7" required>
                    </div>

                    <!-- Tanggal Pulang -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1.5">
                            <i class="fa-regular fa-calendar-check text-success me-1"></i> Tanggal Pulang <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="end_date" id="endDate" 
                               value="{{ old('end_date', now()->addDays(3)->format('Y-m-d')) }}" 
                               min="{{ now()->format('Y-m-d') }}" 
                               class="form-control form-control-lg rounded-3 fs-7" required>
                    </div>

                    <!-- Total Budget -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1.5">
                            <i class="fa-solid fa-wallet text-success me-1"></i> Target Budget (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-muted fs-7 fw-bold">Rp</span>
                            <input type="number" name="budget" id="budgetInput" 
                                   value="{{ old('budget', 1500000) }}" 
                                   min="50000" step="50000" 
                                   class="form-control fs-7 fw-bold" placeholder="1.500.000" required>
                        </div>
                    </div>

                    <!-- Jumlah Wisatawan (Pax) -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label fw-bold text-dark fs-7 mb-1.5">
                            <i class="fa-solid fa-users text-success me-1"></i> Jumlah Orang (Pax) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="number" name="pax" id="paxInput" 
                                   value="{{ old('pax', 2) }}" 
                                   min="1" max="50" 
                                   class="form-control fs-7 fw-bold" required>
                            <span class="input-group-text bg-light text-muted fs-7">Orang</span>
                        </div>
                    </div>

                    <!-- Layanan yang Ingin Dimasukkan ke Rencana Liburan -->
                    <div class="col-12 mt-4">
                        <label class="form-label fw-bold text-dark fs-7 mb-2 d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-layer-group text-success me-1"></i> Pilih Layanan yang Diinginkan:</span>
                            <small class="text-muted fw-normal">Klik untuk memilih minimal 1 layanan</small>
                        </label>

                        <div class="row g-3">
                            <!-- 1. Wisata -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg">
                                <label class="ai-cat-card active" for="cat_tourism">
                                    <input type="checkbox" name="categories[]" value="tourism" id="cat_tourism" class="ai-cat-checkbox" checked>
                                    <div class="ai-cat-icon">
                                        <i class="fa-solid fa-umbrella-beach"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-0.5 fs-7">Wisata Alam</h6>
                                        <small class="text-muted fs-9 d-block">Pantai & Guci</small>
                                    </div>
                                    <div class="ai-cat-check-badge">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </label>
                            </div>

                            <!-- 2. Penginapan -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg">
                                <label class="ai-cat-card active" for="cat_accommodation">
                                    <input type="checkbox" name="categories[]" value="accommodation" id="cat_accommodation" class="ai-cat-checkbox" checked>
                                    <div class="ai-cat-icon">
                                        <i class="fa-solid fa-hotel"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-0.5 fs-7">Penginapan</h6>
                                        <small class="text-muted fs-9 d-block">Hotel & Villa</small>
                                    </div>
                                    <div class="ai-cat-check-badge">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </label>
                            </div>

                            <!-- 3. Kuliner -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg">
                                <label class="ai-cat-card active" for="cat_culinary">
                                    <input type="checkbox" name="categories[]" value="culinary" id="cat_culinary" class="ai-cat-checkbox" checked>
                                    <div class="ai-cat-icon">
                                        <i class="fa-solid fa-utensils"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-0.5 fs-7">Kuliner Khas</h6>
                                        <small class="text-muted fs-9 d-block">Sate & Kupat</small>
                                    </div>
                                    <div class="ai-cat-check-badge">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </label>
                            </div>

                            <!-- 4. Rental Kendaraan -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg">
                                <label class="ai-cat-card active" for="cat_rental">
                                    <input type="checkbox" name="categories[]" value="rental" id="cat_rental" class="ai-cat-checkbox" checked>
                                    <div class="ai-cat-icon">
                                        <i class="fa-solid fa-car"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-0.5 fs-7">Rental Mobil</h6>
                                        <small class="text-muted fs-9 d-block">Armada Siap</small>
                                    </div>
                                    <div class="ai-cat-check-badge">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </label>
                            </div>

                            <!-- 5. Event & Budaya -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg">
                                <label class="ai-cat-card active" for="cat_event">
                                    <input type="checkbox" name="categories[]" value="event" id="cat_event" class="ai-cat-checkbox" checked>
                                    <div class="ai-cat-icon">
                                        <i class="fa-solid fa-masks-theater"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-dark mb-0.5 fs-7">Event Seni</h6>
                                        <small class="text-muted fs-9 d-block">Festival & Budaya</small>
                                    </div>
                                    <div class="ai-cat-check-badge">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit Generate -->
                    <div class="col-12 text-center mt-4 pt-2">
                        <button type="submit" class="btn btn-lokantara btn-lg rounded-pill px-4 px-md-5 py-2.5 py-md-3 fw-bold fs-6 shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-wand-magic-sparkles text-warning fs-5"></i>
                            <span>Buat Rencana Liburan</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <p class="text-muted fs-8 mt-2 mb-0">
                            <i class="fa-solid fa-shield-check text-success me-1"></i> Bebas biaya konsultasi, dapat langsung dipesan dalam 1 invoice.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- 3. Value Proposition Cards -->
<section class="public-section py-5 bg-light">
    <div class="container public-container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold fs-8 mb-2">
                Keunggulan Fitur
            </span>
            <h2 class="fs-2 fw-extrabold text-dark">Mengapa Menggunakan AI Travel Planner?</h2>
            <p class="text-muted fs-7 mx-auto" style="max-width: 600px;">
                Mengubah cara Anda merencanakan liburan di Tegal menjadi lebih praktis, efisien, dan menyenangkan.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 p-4 shadow-2xs h-100 bg-white">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-size: 22px;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Penjadwalan Waktu Realistis</h5>
                    <p class="text-muted fs-7 mb-0">
                        AI menghitung estimasi durasi kunjungan, jam buka destinasi, dan waktu makan siang/malam secara pas tanpa terburu-buru.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 p-4 shadow-2xs h-100 bg-white">
                    <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-size: 22px;">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Optimasi Budget Cermat</h5>
                    <p class="text-muted fs-7 mb-0">
                        Pengeluaran tiket wisata, kamar penginapan, dan transportasi dihitung akurat per orang sehingga tidak melebihi anggaran Anda.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 rounded-4 p-4 shadow-2xs h-100 bg-white">
                    <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-size: 22px;">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">1-Click Direct Booking</h5>
                    <p class="text-muted fs-7 mb-0">
                        Tidak perlu pesan terpisah di banyak tempat. Seluruh item rencana liburan siap dibooking dalam 1 kali transaksi Midtrans.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Category Checkbox Card Styles */
.ai-cat-card {
    position: relative;
    border: 2px solid #e2e8f0;
    border-radius: 18px;
    padding: 14px 12px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    background: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
    user-select: none;
    height: 100%;
}
.ai-cat-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.ai-cat-card:hover {
    border-color: #a7f3d0;
    background: #f0fdf4;
    transform: translateY(-2px);
}
.ai-cat-card:has(input:checked),
.ai-cat-card.active {
    border-color: #059669 !important;
    background: #ecfdf5 !important;
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.12);
}
.ai-cat-card .ai-cat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 18px;
    flex-shrink: 0;
    background: #f1f5f9;
    color: #64748b;
    transition: all 0.2s ease;
}
.ai-cat-card:has(input:checked) .ai-cat-icon,
.ai-cat-card.active .ai-cat-icon {
    background: #059669 !important;
    color: #ffffff !important;
}
.ai-cat-check-badge {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: transparent;
    transition: all 0.2s ease;
    flex-shrink: 0;
}
.ai-cat-card:has(input:checked) .ai-cat-check-badge,
.ai-cat-card.active .ai-cat-check-badge {
    background: #059669;
    border-color: #059669;
    color: #ffffff;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="categories[]"]');
    
    // Initial sync state & Change listener
    checkboxes.forEach(cb => {
        const card = cb.closest('.ai-cat-card');
        
        const syncCard = () => {
            if (cb.checked) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        };

        syncCard();

        cb.addEventListener('change', function() {
            const checkedCount = Array.from(checkboxes).filter(c => c.checked).length;
            if (!this.checked && checkedCount === 0) {
                this.checked = true;
                alert('Silakan pilih minimal 1 jenis layanan untuk rencana liburan Anda.');
                syncCard();
                return;
            }
            syncCard();
        });
    });

    // Form submit validation
    const form = document.getElementById('aiPlannerForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedCount = Array.from(checkboxes).filter(c => c.checked).length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 jenis layanan terlebih dahulu.');
            }
        });
    }

    // Validasi Tanggal Pulang >= Tanggal Berangkat
    const startInput = document.getElementById('startDate');
    const endInput = document.getElementById('endDate');
    if (startInput && endInput) {
        startInput.addEventListener('change', function() {
            if (endInput.value < this.value) {
                endInput.value = this.value;
            }
            endInput.min = this.value;
        });
    }
});
</script>
@endpush
@endsection
