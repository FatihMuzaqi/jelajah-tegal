@extends('layouts.public')

@section('title', 'Tentang Jelajah Tegal — Platform Digital Pariwisata & Layanan Terpadu')
@section('meta-description', 'Mengenal Jelajah Tegal (Lokantara), ekosistem digital terpadu untuk eksplorasi wisata, penginapan, kuliner khas, dan event di Kabupaten Tegal.')
@section('canonical', route('public.about'))

@section('content')
<style>
/* Hero Section */
.about-hero {
    position: relative;
    background: linear-gradient(135deg, #0d261e 0%, #154737 55%, #1b634b 100%);
    color: #ffffff;
    padding: 70px 0 90px;
    overflow: hidden;
}
.about-hero-bg {
    position: absolute;
    inset: 0;
    opacity: 0.15;
    background-image: url('{{ asset("images/guci_hero.webp") }}');
    background-size: cover;
    background-position: center;
    filter: blur(4px) scale(1.05);
}
.about-hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(21, 128, 61, 0.4) 0%, rgba(13, 38, 30, 0.95) 75%);
}
.about-hero-content {
    position: relative;
    z-index: 2;
}
.about-badge {
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
    margin-bottom: 20px;
}
.about-hero h1 {
    font-size: clamp(32px, 4.5vw, 48px);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -0.02em;
    color: #ffffff;
    margin-bottom: 20px;
}
.about-hero p.lead-text {
    font-size: 17px;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.85);
    max-width: 680px;
    margin-bottom: 0;
}

/* Stats Floating Bar */
.about-stats-card {
    background: var(--lokantara-surface, #ffffff);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 20px;
    padding: 30px;
    margin-top: -50px;
    position: relative;
    z-index: 10;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
}
.about-stat-item {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 10px 15px;
}
.about-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 22px;
    flex-shrink: 0;
}
.about-stat-number {
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    color: var(--lokantara-text, #0f172a);
    margin-bottom: 4px;
}
.about-stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--lokantara-muted, #64748b);
    margin: 0;
}

/* Section Common */
.about-section {
    padding: 70px 0;
}
.section-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #15803d;
    background: #dcfce7;
    padding: 4px 12px;
    border-radius: 999px;
    margin-bottom: 12px;
}
.section-title {
    font-size: clamp(26px, 3.2vw, 36px);
    font-weight: 800;
    color: var(--lokantara-text, #0f172a);
    letter-spacing: -0.02em;
    margin-bottom: 16px;
}
.section-desc {
    font-size: 16px;
    line-height: 1.7;
    color: var(--lokantara-muted, #64748b);
    max-width: 640px;
}

/* Feature & Vision Cards */
.vision-card {
    background: var(--lokantara-surface, #ffffff);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 20px;
    padding: 32px;
    height: 100%;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
}
.vision-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    border-color: #86efac;
}
.vision-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    background: #ecfdf5;
    color: #15803d;
    display: grid;
    place-items: center;
    font-size: 24px;
    margin-bottom: 22px;
}
.vision-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--lokantara-text, #0f172a);
    margin-bottom: 12px;
}
.vision-text {
    font-size: 14.5px;
    line-height: 1.7;
    color: var(--lokantara-muted, #64748b);
    margin: 0;
}

/* Pillar Service Card */
.pillar-card {
    background: var(--lokantara-surface, #ffffff);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 16px;
    padding: 24px;
    transition: all 0.25s ease;
    height: 100%;
}
.pillar-card:hover {
    border-color: #15803d;
    background: #fdfefe;
    box-shadow: 0 8px 24px rgba(21, 128, 61, 0.06);
}
.pillar-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 20px;
    margin-bottom: 16px;
}

/* CTA Card */
.about-cta-box {
    background: linear-gradient(135deg, #0d261e 0%, #154737 60%, #166534 100%);
    border-radius: 24px;
    padding: 50px 40px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 45px rgba(13, 38, 30, 0.2);
}
.about-cta-pattern {
    position: absolute;
    top: -50%;
    right: -20%;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74, 222, 128, 0.15) 0%, rgba(21, 128, 61, 0) 70%);
    pointer-events: none;
}
</style>

<!-- Hero Banner -->
<header class="about-hero">
    <div class="about-hero-bg"></div>
    <div class="about-hero-overlay"></div>
    <div class="container public-container about-hero-content">
        <nav class="d-flex align-items-center gap-2 mb-3" style="font-size: 13px; color: rgba(255,255,255,0.7);" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a>
            <i class="fa-solid fa-chevron-right fs-8 opacity-50"></i>
            <span class="text-white fw-semibold">Tentang Kami</span>
        </nav>

        <div class="about-badge">
            <i class="fa-solid fa-compass"></i>
            <span>Platform Resmi Pariwisata & Layanan Terpadu Tegal</span>
        </div>

        <h1>Menghubungkan Pesona Tegal<br>dengan Sentuhan Digital Modern</h1>
        <p class="lead-text">
            <strong>Jelajah Tegal</strong> hadir sebagai gerbang digital satu pintu yang mengintegrasikan destinasi wisata unggulan, penginapan nyaman, kuliner legendaris, pagelaran acara, serta transportasi di Kabupaten Tegal.
        </p>
    </div>
</header>

<!-- Floating Stats Bar -->
<div class="container public-container">
    <div class="about-stats-card">
        <div class="row g-4 align-items-center">
            <div class="col-6 col-lg-3">
                <div class="about-stat-item">
                    <div class="about-stat-icon" style="background: #ecfdf5; color: #10b981;">
                        <i class="fa-solid fa-mountain-sun"></i>
                    </div>
                    <div>
                        <div class="about-stat-number">{{ max($stats['tourism_count'] ?? 0, 15) }}+</div>
                        <p class="about-stat-label">Destinasi Wisata</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="about-stat-item">
                    <div class="about-stat-icon" style="background: #eff6ff; color: #3b82f6;">
                        <i class="fa-solid fa-hotel"></i>
                    </div>
                    <div>
                        <div class="about-stat-number">{{ max($stats['accommodation_count'] ?? 0, 10) }}+</div>
                        <p class="about-stat-label">Hotel & Penginapan</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="about-stat-item">
                    <div class="about-stat-icon" style="background: #fff7ed; color: #f97316;">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <div>
                        <div class="about-stat-number">{{ max($stats['culinary_count'] ?? 0, 20) }}+</div>
                        <p class="about-stat-label">Sentra Kuliner Khas</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="about-stat-item">
                    <div class="about-stat-icon" style="background: #fdf2f8; color: #db2777;">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <div>
                        <div class="about-stat-number">{{ max($stats['mitra_count'] ?? 0, 25) }}+</div>
                        <p class="about-stat-label">Mitra Terverifikasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visi & Misi Section -->
<section class="about-section">
    <div class="container public-container">
        <div class="text-center mx-auto mb-5" style="max-width: 680px;">
            <span class="section-tag">
                <i class="fa-solid fa-bullseye"></i> Visi & Komitmen
            </span>
            <h2 class="section-title">Mendorong Kemajuan Pariwisata Berbasis Kolaborasi</h2>
            <p class="section-desc mx-auto">
                Kami membangun sinergi berkelanjutan antara pemerintah daerah, pelaku usaha lokal (Mitra), dan para wisatawan demi menghadirkan pengalaman berwisata yang aman, transparan, dan berkesan.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="vision-card">
                    <div class="vision-icon-wrapper">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <h3 class="vision-title">Visi Utama</h3>
                    <p class="vision-text">
                        Menjadikan Kabupaten Tegal sebagai destinasi pariwisata terkemuka di Jawa Tengah dengan ekosistem digital terintegrasi yang memudahkan akses informasi, reservasi langsung, serta memberdayakan ekonomi kreatif lokal secara inklusif.
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="vision-card">
                    <div class="vision-icon-wrapper" style="background: #eff6ff; color: #2563eb;">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <h3 class="vision-title">Misi Kami</h3>
                    <p class="vision-text">
                        1. Menyediakan direktori wisata, penginapan, dan kuliner terakurat dan terverifikasi.<br>
                        2. Membuka pintu digitalisasi bagi UMKM dan pelaku wisata lokal di Tegal.<br>
                        3. Menghadirkan sistem pemesanan dan tiket digital QR yang cepat, mudah, dan terpercaya.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Layanan Ekosistem Digital -->
<section class="about-section" style="background: var(--lokantara-background, #f8fafc); border-top: 1px solid var(--lokantara-border, #e2e8f0); border-bottom: 1px solid var(--lokantara-border, #e2e8f0);">
    <div class="container public-container">
        <div class="row align-items-end justify-content-between mb-5">
            <div class="col-lg-7">
                <span class="section-tag">
                    <i class="fa-solid fa-cubes"></i> Ekosistem Layanan
                </span>
                <h2 class="section-title mb-2">Solusi Lengkap untuk Perjalanan Anda</h2>
                <p class="section-desc mb-0">
                    Jelajah Tegal menggabungkan berbagai pilar layanan untuk memastikan semua kebutuhan liburan Anda terpenuhi dalam satu genggaman.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="pillar-card">
                    <div class="pillar-icon" style="background: #ecfdf5; color: #15803d;">
                        <i class="fa-solid fa-mountain-sun"></i>
                    </div>
                    <h4 class="fw-bold fs-6 mb-2">Wisata & Rekreasi</h4>
                    <p class="text-muted fs-7 mb-0">
                        Eksplorasi keindahan alam Guci, pesisir pantai utara, situs purbakala Semedo, hingga agrowisata perkebunan teh yang menyejukkan.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pillar-card">
                    <div class="pillar-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fa-solid fa-hotel"></i>
                    </div>
                    <h4 class="fw-bold fs-6 mb-2">Penginapan & Villa</h4>
                    <p class="text-muted fs-7 mb-0">
                        Pilihan hotel berbintang, resort air panas, homestay ramah keluarga, hingga area glamping dengan tarif transparan dan fasilitas lengkap.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pillar-card">
                    <div class="pillar-icon" style="background: #fff7ed; color: #ea580c;">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h4 class="fw-bold fs-6 mb-2">Kuliner Otentik Tegal</h4>
                    <p class="text-muted fs-7 mb-0">
                        Cita rasa legendaris Sate Kambing Batibul, Tahu Aci khas Slawi, Teh Poci harum melati, dan aneka jajanan pasar tradisional.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pillar-card">
                    <div class="pillar-icon" style="background: #fdf2f8; color: #db2777;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h4 class="fw-bold fs-6 mb-2">Event & Festival Budaya</h4>
                    <p class="text-muted fs-7 mb-0">
                        Informasi kalender festival daerah, konser musik, karnaval budaya, serta pameran ekonomi kreatif tahunan di Kabupaten Tegal.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pillar-card">
                    <div class="pillar-icon" style="background: #fefce8; color: #ca8a04;">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <h4 class="fw-bold fs-6 mb-2">Rental & Transportasi</h4>
                    <p class="text-muted fs-7 mb-0">
                        Sewa mobil lepas kunci, paket dengan supir berpengalaman, hingga rental motor untuk kemudahan mobilitas selama berlibur.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="pillar-card">
                    <div class="pillar-icon" style="background: #f5f3ff; color: #7c3aed;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h4 class="fw-bold fs-6 mb-2">Keamanan & Keaslian Data</h4>
                    <p class="text-muted fs-7 mb-0">
                        Semua Mitra melalui proses verifikasi resmi dari dinas dan tim kurasi demi menjamin kenyamanan transaksi Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mengapa Memilih Jelajah Tegal -->
<section class="about-section">
    <div class="container public-container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="section-tag">
                    <i class="fa-solid fa-award"></i> Keunggulan Platform
                </span>
                <h2 class="section-title">Dibuat Khusus untuk Kemudahan Wisatawan & Mitra Lokal</h2>
                <p class="section-desc mb-4">
                    Kami memadukan teknologi modern dengan keramahan lokal Tegal agar setiap pengguna merasakan kepraktisan maksimal saat merencanakan perjalanan.
                </p>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-circle bg-success-subtle text-success fs-6 mt-1">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold fs-6 mb-1">Tiket QR & Pemesanan Instan</h5>
                            <p class="text-muted fs-7 mb-0">Masuk ke objek wisata tanpa antre panjang dengan tiket digital yang tersinkronisasi langsung ke pos tiket resmi.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-circle bg-success-subtle text-success fs-6 mt-1">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold fs-6 mb-1">Bantuan Asisten AI Pintar</h5>
                            <p class="text-muted fs-7 mb-0">Dapatkan rekomendasi rute, estimasi biaya, dan rekomendasi kuliner 24/7 melalui asisten cerdas terintegrasi.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="p-2 rounded-circle bg-success-subtle text-success fs-6 mt-1">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold fs-6 mb-1">Ulasan Otentik Berbasis Pengalaman</h5>
                            <p class="text-muted fs-7 mb-0">Baca review jujur dan foto nyata langsung dari wisatawan lain yang telah berkunjung sebelumnya.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="about-cta-box">
                    <div class="about-cta-pattern"></div>
                    <span class="badge bg-white text-success fw-bold px-3 py-2 rounded-pill mb-3" style="font-size: 12px;">
                        <i class="fa-solid fa-users-gear me-1"></i> Mitra & Komunitas
                    </span>
                    <h3 class="fw-bold fs-4 mb-3">Punya Usaha Wisata, Penginapan, atau Kuliner di Tegal?</h3>
                    <p class="text-white-50 fs-7 mb-4">
                        Bergabunglah bersama puluhan mitra lainnya untuk memperluas jangkauan promosi usaha Anda ke ribuan wisatawan nusantara. Pendaftaran mudah, gratis, dan diverifikasi langsung.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('mitra.register') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2.5 shadow-sm">
                            <i class="fa-solid fa-user-plus me-1"></i> Daftar Sebagai Mitra
                        </a>
                        <a href="{{ route('public.contact') }}" class="btn btn-outline-light rounded-pill px-4 py-2.5 fw-semibold">
                            <i class="fa-solid fa-headset me-1"></i> Hubungi Dukungan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
