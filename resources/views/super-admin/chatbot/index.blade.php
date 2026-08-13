@extends('layouts.super-admin')
@section('title', 'Pengaturan AI Chatbot')
@section('page-title', 'Pengaturan AI Chatbot')
@section('page-description', 'Kelola kredensial Google Gemini API, pilihan model, instruksi persona, dan uji coba koneksi kecerdasan buatan.')

@section('content')
<div class="row g-4">
    {{-- Status Card Summary --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(31, 122, 92, 0.08) 0%, rgba(45, 140, 168, 0.08) 100%), var(--lokantara-surface); border-radius: 16px; border: 1px solid var(--lokantara-border);">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 52px; height: 52px; background: linear-gradient(135deg, var(--lokantara-primary), #175e47); font-size: 24px; flex-shrink: 0;">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h2 class="h5 fw-bold mb-0 text-dark">Asisten Wisata Cerdas Jelajah Tegal</h2>
                                @if($setting->is_enabled)
                                    <span class="badge bg-success-subtle text-success d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> Aktif di Publik
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid fa-circle-pause"></i> Nonaktif / Maintenance
                                    </span>
                                @endif
                            </div>
                            <p class="text-muted small mb-0">
                                Model Aktif: <strong class="text-dark">{{ $setting->model }}</strong> &middot;
                                API Key: <code class="text-muted">{{ $setting->api_key ? substr($setting->api_key, 0, 8) . '...' . substr($setting->api_key, -4) : 'Belum dikonfigurasi' }}</code>
                            </p>
                        </div>
                    </div>

                    {{-- Live Test Button --}}
                    <div>
                        <button type="button" id="btn-test-connection" class="btn btn-outline-primary rounded-pill d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm">
                            <i class="fa-solid fa-bolt"></i>
                            <span>Tes Koneksi API Sekarang</span>
                        </button>
                    </div>
                </div>

                {{-- Live Test Alert Result Container --}}
                <div id="test-result-container" class="mt-3 d-none">
                    <div id="test-alert-box" class="alert d-flex align-items-start gap-2 mb-0" style="border-radius: 12px; font-size: 13.5px;">
                        <i id="test-icon" class="fa-solid fa-spinner fa-spin mt-1"></i>
                        <div class="flex-grow-1">
                            <strong id="test-title">Memeriksa koneksi...</strong>
                            <div id="test-message" class="mt-1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Configuration Form --}}
    <div class="col-lg-8">
        <x-content-card title="Konfigurasi Engine & Kredensial AI">
            <form method="POST" action="{{ route('super-admin.chatbot.update') }}" id="chatbot-config-form">
                @csrf

                {{-- Status Toggle --}}
                <div class="p-3 mb-4 rounded" style="background: var(--lokantara-background); border: 1px solid var(--lokantara-border);">
                    <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0 mb-0">
                        <div>
                            <label class="form-check-label fw-bold text-dark d-block mb-1" for="is_enabled">
                                Status Widget Chatbot Publik
                            </label>
                            <small class="text-muted d-block">
                                Jika diaktifkan, tombol mengambang "Tanya Asisten AI" akan tampil di seluruh halaman pengunjung.
                            </small>
                        </div>
                        <input type="hidden" name="is_enabled" value="0">
                        <input class="form-check-input ms-3" type="checkbox" role="switch" id="is_enabled" name="is_enabled" value="1" style="width: 48px; height: 24px; cursor: pointer;" @checked(old('is_enabled', $setting->is_enabled))>
                    </div>
                </div>

                {{-- API Key Input with Toggle --}}
                <div class="mb-3">
                    <label for="api_key" class="form-label fw-bold">Google Gemini API Key:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input 
                            type="password" 
                            name="api_key" 
                            id="api_key" 
                            class="form-control font-monospace" 
                            value="{{ old('api_key', $setting->api_key) }}" 
                            placeholder="Contoh: AQ.Ab8RN6LL1NB6Xe4U4EqEbveZR5..."
                            autocomplete="off"
                        >
                        <button class="btn btn-outline-secondary" type="button" id="btn-toggle-key" title="Lihat/Sembunyikan Key">
                            <i class="fa-regular fa-eye" id="key-eye-icon"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">
                        Dapatkan API Key dari Google AI Studio (<a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener">ai.google.dev</a>).
                    </small>
                    @error('api_key')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Model Selector --}}
                <div class="mb-3">
                    <label for="model" class="form-label fw-bold">Pilihan Model Gemini:</label>
                    <select name="model" id="model" class="form-select" required>
                        @foreach($availableModels as $modelCode => $modelLabel)
                            <option value="{{ $modelCode }}" @selected(old('model', $setting->model) === $modelCode)>
                                {{ $modelLabel }} ({{ $modelCode }})
                            </option>
                        @endforeach
                    </select>
                    @error('model')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Base URL (Advanced) --}}
                <div class="mb-3">
                    <label for="base_url" class="form-label fw-bold">Endpoint Base URL:</label>
                    <input 
                        type="url" 
                        name="base_url" 
                        id="base_url" 
                        class="form-control" 
                        value="{{ old('base_url', $setting->base_url) }}" 
                        required
                    >
                    @error('base_url')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- System Prompt Custom Addition --}}
                <div class="mb-3">
                    <label for="system_prompt_addition" class="form-label fw-bold">Instruksi Khusus & Pengumuman Promo Tambahan:</label>
                    <textarea 
                        name="system_prompt_addition" 
                        id="system_prompt_addition" 
                        rows="4" 
                        class="form-control" 
                        placeholder="Contoh: Selalu ingatkan wisatawan bahwa pada akhir pekan ini ada Diskon 20% tiket Guci Hot Spring dengan kode voucher 'TEGALSERU'..."
                    >{{ old('system_prompt_addition', $setting->system_prompt_addition) }}</textarea>
                    <small class="text-muted d-block mt-1">
                        Instruksi ini akan disuntikkan langsung ke dalam panduan persona AI setiap kali menjawab pertanyaan pengguna.
                    </small>
                    @error('system_prompt_addition')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- AI Hyperparameters --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="temperature" class="form-label fw-bold">Kreativitas Jawaban (Temperature):</label>
                        <input 
                            type="number" 
                            name="temperature" 
                            id="temperature" 
                            step="0.05" 
                            min="0" 
                            max="1" 
                            class="form-control" 
                            value="{{ old('temperature', $setting->temperature) }}" 
                            required
                        >
                        <small class="text-muted">Rentang 0.0 (sangat presisi/kaku) hingga 1.0 (sangat kreatif/ekspresif). Default: 0.70.</small>
                        @error('temperature')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="max_tokens" class="form-label fw-bold">Batas Panjang Jawaban (Max Tokens):</label>
                        <input 
                            type="number" 
                            name="max_tokens" 
                            id="max_tokens" 
                            step="50" 
                            min="100" 
                            max="4000" 
                            class="form-control" 
                            value="{{ old('max_tokens', $setting->max_tokens) }}" 
                            required
                        >
                        <small class="text-muted">Maksimal token balasan AI per respons. Rekomendasi: 800 token.</small>
                        @error('max_tokens')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-lokantara d-inline-flex align-items-center gap-2 px-4 py-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Konfigurasi</span>
                    </button>
                </div>
            </form>
        </x-content-card>
    </div>

    {{-- Information & Architecture Sidebar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid var(--lokantara-border); background: var(--lokantara-surface);">
            <div class="card-body p-4">
                <h3 class="h6 fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                    <i class="fa-solid fa-circle-info text-primary"></i>
                    <span>Cara Kerja AI Chatbot</span>
                </h3>
                <ul class="text-muted small ps-3 mb-0 d-flex flex-column gap-2" style="line-height: 1.6;">
                    <li><strong>Live Knowledge Retrieval</strong>: Setiap kali pengguna bertanya, sistem otomatis mengambil ringkasan produk published (Wisata, Penginapan, Kuliner, Event, Rental) dari database lokal.</li>
                    <li><strong>Markdown Auto-link</strong>: AI dilatih untuk menyertakan link langsung ke halaman pemesanan tiket / sewa.</li>
                    <li><strong>Fallback Protection</strong>: Jika kuota API habis, sistem beralih otomatis ke rekomendasi database lokal tanpa error.</li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px; border: 1px solid var(--lokantara-border); background: var(--lokantara-surface);">
            <div class="card-body p-4">
                <h3 class="h6 fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Keamanan & Audit</span>
                </h3>
                <p class="text-muted small mb-2">
                    Setiap perubahan API Key, Model, atau Prompt oleh Super Admin akan dicatat ke dalam log audit keamanan platform.
                </p>
                <small class="text-muted d-block">
                    Terakhir diperbarui: <strong>{{ $setting->updated_at?->diffForHumans() ?? 'Belum pernah' }}</strong>
                    @if($setting->updatedBy)
                        oleh <span class="badge bg-light text-dark">{{ $setting->updatedBy->name }}</span>
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Inline Javascript for API Key Toggle & Live Test Connection --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Show / Hide API Key
    const keyInput = document.getElementById('api_key');
    const toggleKeyBtn = document.getElementById('btn-toggle-key');
    const eyeIcon = document.getElementById('key-eye-icon');

    if (toggleKeyBtn && keyInput) {
        toggleKeyBtn.addEventListener('click', function() {
            if (keyInput.type === 'password') {
                keyInput.type = 'text';
                eyeIcon.className = 'fa-regular fa-eye-slash';
            } else {
                keyInput.type = 'password';
                eyeIcon.className = 'fa-regular fa-eye';
            }
        });
    }

    // 2. Live Test Connection Button
    const btnTest = document.getElementById('btn-test-connection');
    const resultContainer = document.getElementById('test-result-container');
    const alertBox = document.getElementById('test-alert-box');
    const icon = document.getElementById('test-icon');
    const title = document.getElementById('test-title');
    const msg = document.getElementById('test-message');

    if (btnTest) {
        btnTest.addEventListener('click', async function() {
            const apiKey = document.getElementById('api_key').value;
            const model = document.getElementById('model').value;
            const baseUrl = document.getElementById('base_url').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            btnTest.disabled = true;
            btnTest.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Menguji Koneksi...</span>';

            resultContainer.classList.remove('d-none');
            alertBox.className = 'alert alert-info d-flex align-items-start gap-2 mb-0';
            icon.className = 'fa-solid fa-spinner fa-spin mt-1';
            title.textContent = 'Menghubungi Google Gemini API...';
            msg.textContent = 'Mengirim ping test ke model ' + model + '...';

            try {
                const res = await fetch('{{ route('super-admin.chatbot.test-connection') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        api_key: apiKey,
                        model: model,
                        base_url: baseUrl,
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    alertBox.className = 'alert alert-success d-flex align-items-start gap-2 mb-0';
                    icon.className = 'fa-solid fa-circle-check text-success fs-5';
                    title.textContent = 'Koneksi Berhasil! (' + data.latency_ms + ' ms)';
                    msg.innerHTML = `<strong>Status:</strong> ${data.message}<br><small class="text-muted d-block mt-1"><strong>Sampel Respon AI:</strong> "${data.sample_reply}"</small>`;
                } else {
                    alertBox.className = 'alert alert-danger d-flex align-items-start gap-2 mb-0';
                    icon.className = 'fa-solid fa-circle-xmark text-danger fs-5';
                    title.textContent = 'Koneksi Gagal';
                    msg.textContent = data.message || 'Gagal terhubung ke API. Pastikan API Key dan nama model valid.';
                }
            } catch (err) {
                alertBox.className = 'alert alert-danger d-flex align-items-start gap-2 mb-0';
                icon.className = 'fa-solid fa-triangle-exclamation text-danger fs-5';
                title.textContent = 'Kesalahan Jaringan';
                msg.textContent = err.message || 'Terjadi kesalahan saat memproses permintaan.';
            } finally {
                btnTest.disabled = false;
                btnTest.innerHTML = '<i class="fa-solid fa-bolt"></i> <span>Tes Koneksi API Sekarang</span>';
            }
        });
    }
});
</script>
@endsection
