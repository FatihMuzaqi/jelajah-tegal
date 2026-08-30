@extends('layouts.public')

@section('title', 'Tanya Jawab (FAQ) & Kotak Saran — Jelajah Tegal')
@section('meta-description', 'Temukan jawaban atas pertanyaan umum seputar destinasi wisata, tiket online, penginapan, pendaftaran mitra, dan sampaikan saran atau kritik Anda untuk Jelajah Tegal.')
@section('canonical', route('public.faq'))

@section('content')
<style>
/* FAQ Hero */
.faq-hero {
    position: relative;
    background: linear-gradient(135deg, #0d261e 0%, #154737 55%, #1b634b 100%);
    color: #ffffff;
    padding: 65px 0 85px;
    overflow: hidden;
}
.faq-hero-bg {
    position: absolute;
    inset: 0;
    opacity: 0.12;
    background-image: url('{{ asset("images/guci_hero.webp") }}');
    background-size: cover;
    background-position: center;
    filter: blur(4px) scale(1.05);
}
.faq-hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(21, 128, 61, 0.4) 0%, rgba(13, 38, 30, 0.95) 75%);
}
.faq-hero-content {
    position: relative;
    z-index: 2;
}
.faq-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(8px);
    color: #a7f3d0;
    font-size: 13px;
    font-weight: 600;
    padding: 6px 16px;
    border-radius: 999px;
    margin-bottom: 18px;
}
.faq-hero h1 {
    font-size: clamp(30px, 4vw, 44px);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -0.02em;
    color: #ffffff;
    margin-bottom: 16px;
}
.faq-hero p.lead-text {
    font-size: 16.5px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.85);
    max-width: 640px;
    margin-bottom: 24px;
}

/* Search Box in Hero */
.faq-search-wrapper {
    max-width: 580px;
    position: relative;
}
.faq-search-input {
    width: 100%;
    height: 52px;
    padding: 12px 20px 12px 50px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    font-size: 15px;
    color: #0f172a;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    outline: none;
    transition: all 0.2s ease;
}
.faq-search-input:focus {
    background: #ffffff;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    border-color: #10b981;
}
.faq-search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 16px;
    pointer-events: none;
}

/* Section styling */
.faq-main-section {
    padding: 60px 0 80px;
}
.faq-category-card {
    background: var(--lokantara-surface, #ffffff);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 28px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
}
.faq-category-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 22px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--lokantara-border, #f1f5f9);
}
.faq-category-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #ecfdf5;
    color: #15803d;
    display: grid;
    place-items: center;
    font-size: 18px;
    flex-shrink: 0;
}
.faq-category-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--lokantara-text, #0f172a);
    margin: 0;
}

/* Accordion Item */
.faq-item {
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 14px;
    margin-bottom: 12px;
    background: var(--lokantara-background, #f8fafc);
    overflow: hidden;
    transition: all 0.2s ease;
}
.faq-item:hover {
    border-color: #86efac;
}
.faq-question-btn {
    width: 100%;
    text-align: left;
    padding: 16px 20px;
    background: transparent;
    border: none;
    font-size: 15px;
    font-weight: 700;
    color: var(--lokantara-text, #0f172a);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    cursor: pointer;
}
.faq-chevron {
    font-size: 13px;
    color: var(--lokantara-muted, #94a3b8);
    transition: transform 0.25s ease;
}
.faq-item.active .faq-chevron {
    transform: rotate(180deg);
    color: #15803d;
}
.faq-item.active {
    background: var(--lokantara-surface, #ffffff);
    border-color: #15803d;
    box-shadow: 0 4px 12px rgba(21, 128, 61, 0.05);
}
.faq-answer {
    padding: 0 20px 18px;
    font-size: 14.5px;
    line-height: 1.7;
    color: var(--lokantara-muted, #475569);
    display: none;
}
.faq-item.active .faq-answer {
    display: block;
}

/* Feedback Card */
.feedback-box-card {
    background: var(--lokantara-surface, #ffffff);
    border: 2px solid #86efac;
    border-radius: 24px;
    padding: 36px;
    box-shadow: 0 16px 36px rgba(21, 128, 61, 0.07);
}
.feedback-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #15803d;
    background: #dcfce7;
    padding: 4px 14px;
    border-radius: 999px;
    margin-bottom: 12px;
}

/* Type Pill selector */
.type-pill-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 12px;
    border: 1.5px solid var(--lokantara-border, #e2e8f0);
    background: var(--lokantara-background, #f8fafc);
    cursor: pointer;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--lokantara-text, #334155);
    transition: all 0.2s ease;
    user-select: none;
}
.type-pill-label:hover {
    border-color: #15803d;
    background: #f0fdf4;
}
.type-pill-input:checked + .type-pill-label {
    background: #dcfce7;
    border-color: #15803d;
    color: #14532d;
    box-shadow: 0 2px 8px rgba(21, 128, 61, 0.15);
}

/* Quick Support Card */
.support-side-card {
    background: linear-gradient(135deg, #0d261e 0%, #154737 100%);
    border-radius: 20px;
    padding: 30px;
    color: #ffffff;
    margin-bottom: 24px;
}
</style>

<!-- Hero Section -->
<header class="faq-hero">
    <div class="faq-hero-bg"></div>
    <div class="faq-hero-overlay"></div>
    <div class="container public-container faq-hero-content">
        <nav class="d-flex align-items-center gap-2 mb-3" style="font-size: 13px; color: rgba(255,255,255,0.7);" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a>
            <i class="fa-solid fa-chevron-right fs-8 opacity-50"></i>
            <span class="text-white fw-semibold">Tanya Jawab & Kotak Saran</span>
        </nav>

        <div class="faq-badge">
            <i class="fa-solid fa-circle-question"></i>
            <span>Pusat Bantuan & Layanan Masukan Pengguna</span>
        </div>

        <h1>Ada yang Bisa Kami Bantu?</h1>
        <p class="lead-text">
            Temukan jawaban cepat atas pertanyaan yang sering diajukan, atau sampaikan kritik dan saran terbaik Anda demi kemajuan pariwisata Kabupaten Tegal.
        </p>

        <div class="faq-search-wrapper">
            <i class="fa-solid fa-magnifying-glass faq-search-icon"></i>
            <input type="text" id="faqSearchInput" class="faq-search-input" placeholder="Cari pertanyaan... (contoh: tiket, pesan, mitra, guci)">
        </div>
    </div>
</header>

<!-- Main FAQ & Feedback Section -->
<section class="faq-main-section">
    <div class="container public-container">
        <div class="row g-5">
            <!-- Left Column: FAQ Accordion List (7 Cols) -->
            <div class="col-lg-7">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h2 class="fw-bold fs-4 mb-1 text-dark">Daftar Pertanyaan Populer</h2>
                        <p class="text-muted fs-7 mb-0">Klik pada pertanyaan untuk melihat penjelasan lengkap.</p>
                    </div>
                </div>

                <div id="faqContainer">
                    @foreach ($faqCategories as $catIndex => $category)
                        <div class="faq-category-card" data-category="{{ $category['name'] }}">
                            <div class="faq-category-header">
                                <div class="faq-category-icon">
                                    <i class="fa-solid {{ $category['icon'] }}"></i>
                                </div>
                                <h3 class="faq-category-title">{{ $category['name'] }}</h3>
                            </div>

                            <div class="faq-items-list">
                                @foreach ($category['items'] as $itemIndex => $item)
                                    <div class="faq-item {{ $catIndex === 0 && $itemIndex === 0 ? 'active' : '' }}">
                                        <button type="button" class="faq-question-btn" onclick="toggleFaq(this)">
                                            <span>{{ $item['q'] }}</span>
                                            <i class="fa-solid fa-chevron-down faq-chevron"></i>
                                        </button>
                                        <div class="faq-answer">
                                            {{ $item['a'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="noFaqFound" class="p-4 text-center rounded-4 border bg-light d-none">
                    <i class="fa-solid fa-search fs-2 text-muted mb-2"></i>
                    <h5 class="fw-bold fs-6 text-dark mb-1">Pertanyaan tidak ditemukan</h5>
                    <p class="text-muted fs-7 mb-0">Coba gunakan kata kunci lain atau kirimkan pertanyaan Anda melalui form di samping.</p>
                </div>
            </div>

            <!-- Right Column: Kotak Saran & Kritik (5 Cols) -->
            <div class="col-lg-5" id="kotak-saran">
                <!-- Success Alert -->
                @if (session('feedback_success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-4" role="alert">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check fs-5 text-success mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Pesan Berhasil Dikirim!</h6>
                                <p class="mb-0 fs-7">{{ session('feedback_success') }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Feedback Box Form -->
                <div class="feedback-box-card">
                    <span class="feedback-badge">
                        <i class="fa-solid fa-paper-plane"></i> Kotak Aspirasi
                    </span>
                    <h3 class="fw-bold fs-4 text-dark mb-2">Saran, Kritik & Masukan</h3>
                    <p class="text-muted fs-7 mb-4">
                        Suara Anda sangat berharga bagi kami untuk terus menyempurnakan platform Jelajah Tegal.
                    </p>

                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger rounded-3 p-3 mb-3 fs-7">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Mohon lengkapi seluruh kolom formulir dengan benar.
                        </div>
                    @endif

                    <form action="{{ route('public.feedback.store') }}" method="POST">
                        @csrf

                        <!-- Feedback Type Pills -->
                        <div class="mb-3">
                            <label class="form-label fw-bold fs-7 text-dark mb-2">Jenis Pesan <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-2">
                                <div>
                                    <input type="radio" name="type" id="type_saran" value="saran" class="d-none type-pill-input" {{ old('type', 'saran') === 'saran' ? 'checked' : '' }}>
                                    <label for="type_saran" class="type-pill-label">
                                        <i class="fa-solid fa-lightbulb text-warning"></i> Saran Perbaikan
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" name="type" id="type_kritik" value="kritik" class="d-none type-pill-input" {{ old('type') === 'kritik' ? 'checked' : '' }}>
                                    <label for="type_kritik" class="type-pill-label">
                                        <i class="fa-solid fa-comment-dots text-danger"></i> Kritik Membangun
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" name="type" id="type_pertanyaan" value="pertanyaan" class="d-none type-pill-input" {{ old('type') === 'pertanyaan' ? 'checked' : '' }}>
                                    <label for="type_pertanyaan" class="type-pill-label">
                                        <i class="fa-solid fa-question text-info"></i> Pertanyaan
                                    </label>
                                </div>
                                <div>
                                    <input type="radio" name="type" id="type_apresiasi" value="apresiasi" class="d-none type-pill-input" {{ old('type') === 'apresiasi' ? 'checked' : '' }}>
                                    <label for="type_apresiasi" class="type-pill-label">
                                        <i class="fa-solid fa-heart text-danger"></i> Apresiasi
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Nama & Email -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-3 py-2" placeholder="Nama Anda" value="{{ old('name', auth()->user()?->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-dark">Email Aktif <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm rounded-3 py-2" placeholder="email@contoh.com" value="{{ old('email', auth()->user()?->email) }}" required>
                            </div>
                        </div>

                        <!-- WhatsApp & Kategori -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-dark">No. WhatsApp <span class="text-muted fw-normal">(Opsional)</span></label>
                                <input type="tel" name="phone" class="form-control form-control-sm rounded-3 py-2" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-dark">Kategori Layanan <span class="text-danger">*</span></label>
                                <select name="category" class="form-select form-select-sm rounded-3 py-2" required>
                                    <option value="umum" {{ old('category') === 'umum' ? 'selected' : '' }}>Umum / Layanan Website</option>
                                    <option value="wisata" {{ old('category') === 'wisata' ? 'selected' : '' }}>Objek Wisata</option>
                                    <option value="penginapan" {{ old('category') === 'penginapan' ? 'selected' : '' }}>Hotel & Penginapan</option>
                                    <option value="kuliner" {{ old('category') === 'kuliner' ? 'selected' : '' }}>Kuliner & Rumah Makan</option>
                                    <option value="event" {{ old('category') === 'event' ? 'selected' : '' }}>Event & Tiket Acara</option>
                                    <option value="rental" {{ old('category') === 'rental' ? 'selected' : '' }}>Rental Kendaraan</option>
                                    <option value="mitra" {{ old('category') === 'mitra' ? 'selected' : '' }}>Pendaftaran Mitra</option>
                                </select>
                            </div>
                        </div>

                        <!-- Subjek -->
                        <div class="mb-3">
                            <label class="form-label fw-bold fs-7 text-dark">Judul Masukan / Subjek <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control form-control-sm rounded-3 py-2" placeholder="Ringkasan singkat masukan Anda" value="{{ old('subject') }}" required>
                        </div>

                        <!-- Pesan -->
                        <div class="mb-4">
                            <label class="form-label fw-bold fs-7 text-dark">Isi Saran / Kritik / Pertanyaan <span class="text-danger">*</span></label>
                            <textarea name="message" rows="4" class="form-control rounded-3" placeholder="Tuliskan secara rinci masukan atau keluhan yang Anda rasakan..." required>{{ old('message') }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-lokantara w-100 rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Masukan Anda</span>
                        </button>
                    </form>
                </div>

                <!-- Fast Help Card -->
                <div class="support-side-card mt-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-2 rounded-circle bg-white text-success fs-5">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold fs-6 mb-0 text-white">Butuh Bantuan Mendesak?</h5>
                            <small class="text-white-50">Layanan bantuan pelanggan siap membantu.</small>
                        </div>
                    </div>
                    <p class="fs-7 text-white-50 mb-3">
                        Untuk kendala transaksi tiket atau reservasi yang butuh konfirmasi segera, hubungi kontak resmi kami:
                    </p>
                    <a href="{{ route('public.contact') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5 fw-semibold">
                        <i class="fa-solid fa-arrow-right me-1"></i> Buka Halaman Kontak Resmi
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function toggleFaq(button) {
    const item = button.closest('.faq-item');
    const wasActive = item.classList.contains('active');
    
    // Optional: close other items in the same category
    const parentList = item.closest('.faq-items-list');
    parentList.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
    
    if (!wasActive) {
        item.classList.add('active');
    }
}

// Live Search FAQ
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faqSearchInput');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const categoryCards = document.querySelectorAll('.faq-category-card');
        const noFoundAlert = document.getElementById('noFaqFound');
        let totalVisible = 0;
        
        categoryCards.forEach(card => {
            const items = card.querySelectorAll('.faq-item');
            let visibleInCard = 0;
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = 'block';
                    visibleInCard++;
                    totalVisible++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            if (visibleInCard > 0) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        if (totalVisible === 0) {
            noFoundAlert.classList.remove('d-none');
        } else {
            noFoundAlert.classList.add('d-none');
        }
    });
});
</script>
@endpush
@endsection
