@extends('layouts.public')

@section('title', $title . ' — Jelajah Tegal')
@section('meta-description', $content['summary'] ?? ($title . ' resmi portal ekosistem Jelajah Tegal.'))
@section('canonical', url()->current())

@section('content')
<style>
/* Document Page Hero */
.doc-page-hero {
    position: relative;
    background: linear-gradient(135deg, #092018 0%, #114232 55%, #185c46 100%);
    color: #ffffff;
    padding: clamp(45px, 7vw, 75px) 0 clamp(50px, 8vw, 80px);
    overflow: hidden;
}
.doc-hero-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 20%, rgba(21, 128, 61, 0.4) 0%, rgba(9, 32, 24, 0.95) 75%);
}
.doc-hero-content {
    position: relative;
    z-index: 2;
}
.doc-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    color: #a7f3d0;
    font-size: 12px;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 999px;
    margin-bottom: 16px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.doc-page-hero h1 {
    font-size: clamp(26px, 4.2vw, 42px);
    font-weight: 800;
    line-height: 1.25;
    letter-spacing: -0.02em;
    color: #ffffff;
    margin-bottom: 14px;
}
.doc-hero-summary {
    font-size: clamp(14px, 1.8vw, 15.5px);
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.88);
    max-width: 720px;
    margin-bottom: 0;
}

/* Document Container & Articles */
.doc-main-container {
    max-width: 880px;
    margin: 0 auto;
    padding: clamp(35px, 5vw, 60px) 16px clamp(50px, 7vw, 80px);
}
.doc-meta-card {
    background: var(--lokantara-surface, #ffffff);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 28px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
}
.doc-article-card {
    background: var(--lokantara-surface, #ffffff);
    border: 1px solid var(--lokantara-border, #e2e8f0);
    border-radius: 18px;
    padding: clamp(22px, 3.5vw, 32px);
    margin-bottom: 20px;
    transition: all 0.25s ease;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
}
.doc-article-card:hover {
    border-color: rgba(4, 120, 87, 0.35);
    box-shadow: 0 8px 24px rgba(4, 120, 87, 0.06);
}
.doc-article-card h2, .doc-article-card h3 {
    font-size: clamp(17px, 2.2vw, 20px);
    font-weight: 800;
    color: var(--lokantara-text, #0f172a);
    margin: 0 0 12px;
    letter-spacing: -0.01em;
    display: flex;
    align-items: center;
    gap: 10px;
}
.doc-article-body {
    font-size: 14.5px;
    line-height: 1.8;
    color: var(--lokantara-muted, #475569);
    margin: 0;
    white-space: pre-line;
}

/* Support Help Card */
.doc-help-card {
    background: linear-gradient(135deg, rgba(4, 120, 87, 0.06) 0%, rgba(6, 95, 70, 0.08) 100%);
    border: 1.5px dashed rgba(4, 120, 87, 0.3);
    border-radius: 18px;
    padding: clamp(22px, 3.5vw, 30px);
    margin-top: 36px;
    text-align: center;
}

@media (max-width: 576px) {
    .doc-meta-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        padding: 14px 16px;
    }
}
</style>

<!-- Hero Section -->
<section class="doc-page-hero">
    <div class="doc-hero-overlay"></div>
    <div class="container public-container doc-hero-content">
        <!-- Breadcrumb -->
        <nav class="d-flex align-items-center gap-2 mb-3" style="font-size: 13px; color: rgba(255,255,255,0.7);" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Beranda</a>
            <i class="fa-solid fa-chevron-right fs-8 opacity-50"></i>
            <span class="text-white fw-semibold">{{ $title }}</span>
        </nav>

        <div class="doc-badge">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Jelajah Tegal &middot; Dokumen Resmi</span>
        </div>

        <h1>{{ $title }}</h1>

        @if($content && !empty($content['summary']))
            <p class="doc-hero-summary">{{ $content['summary'] }}</p>
        @endif
    </div>
</section>

<!-- Main Document Content -->
<section class="public-section py-0">
    <div class="doc-main-container">

        <!-- Meta Card -->
        <div class="doc-meta-card">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 fs-8 fw-bold">
                    <i class="fa-solid fa-circle-check me-1"></i> Dokumen Resmi Berlaku
                </span>
                <span class="text-muted fs-8 fw-semibold">
                    &bull; Wilayah: Kabupaten Tegal
                </span>
            </div>
            <div class="text-muted fs-8">
                <i class="fa-regular fa-clock me-1"></i> Berlaku Efektif: <strong>Tahun {{ date('Y') }}</strong>
            </div>
        </div>

        @if(!$content)
            <x-empty-state :title="$emptyMessage" description="Dokumen resmi sedang dalam proses sinkronisasi regulasi." />
        @elseif(!empty($content['items']))
            <div class="doc-articles-wrapper">
                @foreach($content['items'] as $item)
                    <article class="doc-article-card">
                        <h2>
                            <i class="fa-solid fa-file-lines text-success fs-7"></i>
                            <span>{{ $item['title'] ?? $item['question'] ?? '' }}</span>
                        </h2>
                        <div class="doc-article-body">{{ $item['body'] ?? $item['answer'] ?? '' }}</div>
                    </article>
                @endforeach
            </div>
        @elseif(!empty($content['paragraphs']))
            <div class="doc-articles-wrapper">
                <article class="doc-article-card">
                    @foreach($content['paragraphs'] as $paragraph)
                        <p class="doc-article-body mb-3">{{ $paragraph }}</p>
                    @endforeach
                </article>
            </div>
        @else
            <x-empty-state title="Dokumen Belum Tersedia" description="Konten dokumen ini belum memiliki naskah yang dapat ditampilkan." />
        @endif

        <!-- Help & Contact Box -->
        <div class="doc-help-card">
            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-3" style="width: 46px; height: 46px; color: #047857;">
                <i class="fa-solid fa-headset fs-5"></i>
            </div>
            <h4 class="fw-bold fs-6 mb-2 text-dark">Punya Pertanyaan Mengenai {{ $title }}?</h4>
            <p class="text-muted fs-7 mb-3 mx-auto" style="max-width: 540px;">
                Tim layanan dan bantuan Jelajah Tegal siap membantu memberikan klarifikasi atas pertanyaan atau masukan Anda.
            </p>
            <a href="{{ route('public.contact') }}" class="btn btn-sm btn-success rounded-pill px-4 py-2 fw-bold" style="background: #047857; border-color: #047857;">
                <i class="fa-solid fa-envelope me-1.5"></i> Hubungi Tim Jelajah Tegal
            </a>
        </div>

    </div>
</section>
@endsection
