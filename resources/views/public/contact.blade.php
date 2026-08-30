@extends('layouts.public')

@section('title', 'Kontak & Pusat Bantuan — Jelajah Tegal')
@section('meta-description', 'Hubungi tim resmi Jelajah Tegal untuk informasi wisata, bantuan transaksi tiket, pendaftaran kemitraan, dan layanan pengaduan.')
@section('canonical', route('public.contact'))

@section('content')
<style>
/* Contact Hero */
.contact-hero {
    position: relative;
    background: linear-gradient(135deg, #0d261e 0%, #154737 55%, #1b634b 100%);
    color: #ffffff;
    padding: 65px 0 85px;
    overflow: hidden;
}
.contact-hero-bg {
    position: absolute;
    inset: 0;
    opacity: 0.12;
    background-image: url('{{ asset("images/guci_hero.webp") }}');
    background-size: cover;
    background-position: center;
    filter: blur(4px) scale(1.05);
}
.contact-hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(21, 128, 61, 0.4) 0%, rgba(13, 38, 30, 0.95) 75%);
}
.contact-hero-content {
    position: relative;
    z-index: 2;
}
.contact-badge {
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
.contact-hero h1 {
    font-size: clamp(30px, 4vw, 44px);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -0.02em;
    color: #ffffff;
    margin-bottom: 16px;
}
.contact-hero p.lead-text {
    font-size: 16.5px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.85);
    max-width: 640px;
    margin-bottom: 0;
}

/* Contact Cards Grid */
.contact-cards-section {
    margin-top: -45px;
    position: relative;
    z-index: 10;
    margin-bottom: 50px;
}
.contact-info-card {
    background: var(--lokantara-surface, #ffffff);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 20px;
    padding: 28px 24px;
    height: 100%;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
}
.contact-info-card:hover {
    transform: translateY(-4px);
    border-color: #15803d;
    box-shadow: 0 16px 36px rgba(21, 128, 61, 0.1);
}
.contact-card-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 22px;
    margin-bottom: 18px;
}
.contact-card-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--lokantara-text, #0f172a);
    margin-bottom: 8px;
}
.contact-card-desc {
    font-size: 14px;
    line-height: 1.6;
    color: var(--lokantara-muted, #64748b);
    margin-bottom: 16px;
    flex-grow: 1;
}
.contact-card-link {
    font-size: 14px;
    font-weight: 700;
    color: #15803d;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: gap 0.2s ease;
}
.contact-card-link:hover {
    color: #166534;
    gap: 10px;
}

/* Contact Main Section */
.contact-main-section {
    padding-bottom: 80px;
}
.contact-form-card {
    background: var(--lokantara-surface, #ffffff);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 24px;
    padding: 36px;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.04);
}
.contact-side-box {
    background: var(--lokantara-background, #f8fafc);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 20px;
    padding: 28px;
    height: 100%;
}
.operational-hour-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px dashed var(--lokantara-border, #e2e8f0);
    font-size: 14px;
}
.operational-hour-item:last-child {
    border-bottom: none;
}
.map-container {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--lokantara-border, #e2e8f0);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
</style>

<!-- Hero Section -->
<header class="contact-hero">
    <div class="contact-hero-bg"></div>
    <div class="contact-hero-overlay"></div>
    <div class="container public-container contact-hero-content">
        <nav class="d-flex align-items-center gap-2 mb-3" style="font-size: 13px; color: rgba(255,255,255,0.7);" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a>
            <i class="fa-solid fa-chevron-right fs-8 opacity-50"></i>
            <span class="text-white fw-semibold">Kontak & Pusat Bantuan</span>
        </nav>

        <div class="contact-badge">
            <i class="fa-solid fa-headset"></i>
            <span>Layanan Bantuan & Informasi Resmi</span>
        </div>

        <h1>Hubungi Tim Jelajah Tegal</h1>
        <p class="lead-text">
            Ada pertanyaan tentang destinasi wisata, bantuan pesanan tiket, pendaftaran mitra usaha, atau saran perbaikan? Kami siap mendengarkan dan membantu Anda.
        </p>
    </div>
</header>

<!-- Contact Cards Grid -->
<div class="container public-container contact-cards-section">
    <div class="row g-4">
        <!-- Card 1: WhatsApp Helpdesk -->
        <div class="col-md-4">
            <div class="contact-info-card">
                <div class="contact-card-icon" style="background: #ecfdf5; color: #15803d;">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <h3 class="contact-card-title">Layanan WhatsApp Cepat</h3>
                <p class="contact-card-desc">
                    Hubungi tim helpdesk kami untuk pertanyaan cepat seputar konfirmasi pemesanan, tiket QR, dan petunjuk arah objek wisata.
                </p>
                <div>
                    <a href="https://wa.me/6287872801727?text=Halo%20Admin%20Jelajah%20Tegal,%20saya%20ingin%20bertanya" target="_blank" rel="noopener noreferrer" class="contact-card-link">
                        <span>Chat via WhatsApp</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card 2: Email Dukungan -->
        <div class="col-md-4">
            <div class="contact-info-card">
                <div class="contact-card-icon" style="background: #eff6ff; color: #2563eb;">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
                <h3 class="contact-card-title">Email Resmi & Kerja Sama</h3>
                <p class="contact-card-desc">
                    Kirimkan pertanyaan resmi, verifikasi kemitraan dinas/swasta, proposal acara pariwisata, atau penawaran media.
                </p>
                <div>
                    <a href="mailto:support@jelajahtegal.com" class="contact-card-link">
                        <span>support@jelajahtegal.com</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Card 3: Kantor Layanan -->
        <div class="col-md-4">
            <div class="contact-info-card">
                <div class="contact-card-icon" style="background: #fff7ed; color: #ea580c;">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h3 class="contact-card-title">Kantor Pelayanan Slawi</h3>
                <p class="contact-card-desc">
                    Pusat Informasi Pariwisata Kabupaten Tegal, Jl. Ahmad Yani No. 1, Procot, Slawi, Kabupaten Tegal, Jawa Tengah 52411.
                </p>
                <div>
                    <a href="https://maps.google.com/?q=Slawi+Kabupaten+Tegal" target="_blank" rel="noopener noreferrer" class="contact-card-link">
                        <span>Petunjuk Arah Maps</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Contact Form & Location Section -->
<section class="contact-main-section">
    <div class="container public-container">
        <div class="row g-5">
            <!-- Left: Contact Form (7 Cols) -->
            <div class="col-lg-7">
                <!-- Alert Success -->
                @if (session('feedback_success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-4" role="alert">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check fs-5 text-success mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Pesan Anda Berhasil Terkirim!</h6>
                                <p class="mb-0 fs-7">{{ session('feedback_success') }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="contact-form-card">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success-subtle text-success fw-bold px-3 py-1.5 rounded-pill" style="font-size: 12px;">
                            <i class="fa-solid fa-paper-plane me-1"></i> Kirim Pesan Langsung
                        </span>
                    </div>
                    <h2 class="fw-bold fs-4 text-dark mb-2">Tinggalkan Pesan untuk Tim Kami</h2>
                    <p class="text-muted fs-7 mb-4">
                        Silakan lengkapi formulir di bawah ini. Tim pengelola Jelajah Tegal akan merespons melalui email atau WhatsApp sesegera mungkin.
                    </p>

                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger rounded-3 p-3 mb-3 fs-7">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Mohon periksa kembali kolom formulir yang belum sesuai.
                        </div>
                    @endif

                    <form action="{{ route('public.feedback.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="pertanyaan">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-3 py-2" placeholder="Nama Anda" value="{{ old('name', auth()->user()?->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-dark">Email Aktif <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control rounded-3 py-2" placeholder="email@domain.com" value="{{ old('email', auth()->user()?->email) }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-dark">No. WhatsApp / HP</label>
                                <input type="tel" name="phone" class="form-control rounded-3 py-2" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold fs-7 text-dark">Kategori Keperluan <span class="text-danger">*</span></label>
                                <select name="category" class="form-select rounded-3 py-2" required>
                                    <option value="umum" {{ old('category') === 'umum' ? 'selected' : '' }}>Pertanyaan Umum / Info Wisata</option>
                                    <option value="tiket" {{ old('category') === 'tiket' ? 'selected' : '' }}>Bantuan Pembelian Tiket & QR</option>
                                    <option value="penginapan" {{ old('category') === 'penginapan' ? 'selected' : '' }}>Reservasi Penginapan & Villa</option>
                                    <option value="mitra" {{ old('category') === 'mitra' ? 'selected' : '' }}>Kemitraan & Pendaftaran Usaha</option>
                                    <option value="kerjasama" {{ old('category') === 'kerjasama' ? 'selected' : '' }}>Kerja Sama Dinas & Promosi</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold fs-7 text-dark">Subjek Pesan <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control rounded-3 py-2" placeholder="Contoh: Pertanyaan seputar pemesanan tiket rombongan Guci" value="{{ old('subject') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold fs-7 text-dark">Pesan Lengkap <span class="text-danger">*</span></label>
                            <textarea name="message" rows="5" class="form-control rounded-3" placeholder="Tuliskan secara rinci detail pertanyaan atau keperluan Anda..." required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-lokantara w-100 rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Pesan Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Operational Hours & Location Map (5 Cols) -->
            <div class="col-lg-5">
                <div class="contact-side-box">
                    <!-- Jam Operasional -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="p-2 rounded-3 bg-success text-white fs-6">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold fs-6 mb-0 text-dark">Jam Operasional Layanan</h4>
                            <small class="text-muted">Waktu Indonesia Barat (WIB)</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="operational-hour-item">
                            <span class="text-muted">Senin – Kamis</span>
                            <span class="fw-bold text-dark">08:00 – 16:00 WIB</span>
                        </div>
                        <div class="operational-hour-item">
                            <span class="text-muted">Jumat</span>
                            <span class="fw-bold text-dark">08:00 – 15:30 WIB</span>
                        </div>
                        <div class="operational-hour-item">
                            <span class="text-muted">Sabtu – Minggu</span>
                            <span class="fw-bold text-success">08:00 – 14:00 WIB (Helpdesk Wisatawan)</span>
                        </div>
                    </div>

                    <!-- Asisten AI 24 Jam -->
                    <div class="p-3 rounded-3 mb-4" style="background: #ecfdf5; border: 1px solid #a7f3d0;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-wand-magic-sparkles text-success"></i>
                            <h5 class="fw-bold fs-7 mb-0 text-success">Layanan Asisten AI 24/7</h5>
                        </div>
                        <p class="fs-7 text-muted mb-0">
                            Di luar jam kerja di atas, Anda tetap dapat memperoleh rekomendasi wisata dan estimasi biaya melalui fitur <strong>Asisten AI</strong> di pojok kanan bawah.
                        </p>
                    </div>

                    <!-- Map Slawi Tegal -->
                    <div class="mb-2">
                        <h4 class="fw-bold fs-7 text-dark mb-2">
                            <i class="fa-solid fa-map-location-dot me-1 text-primary"></i> Lokasi Wilayah Kabupaten Tegal
                        </h4>
                        <div class="map-container">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.83060136262!2d109.07172828695029!3d-7.001650392942407!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fbf3413993d01%3A0x3027a76e352bd60!2sKabupaten%20Tegal%2C%20Jawa%20Tengah!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                                width="100%" 
                                height="220" 
                                style="border:0; display: block;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
