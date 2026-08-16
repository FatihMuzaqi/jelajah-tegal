@extends('layouts.mitra')

@section('title', $tourism->name)
@section('page-title', $tourism->name)
@section('page-description', 'Preview, kelola foto media, paket tiket, jam operasional, dan moderasi destinasi.')

@section('content')
    <!-- Action Bar -->
    <div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded'
        style='background: var(--lokantara-surface); border: 1px solid var(--lokantara-border);'>
        <div class='d-flex align-items-center gap-2'>
            <x-status-badge :status='$tourism->status' />
            <span class='text-muted'>|</span>
            <span class='fw-semibold'>📍 {{ $tourism->region?->name ?? 'Tegal' }}</span>
            <span class='text-muted'>·</span>
            <span class='badge text-bg-light'>{{ $tourism->category?->name ?? 'Wisata' }}</span>
        </div>

        <div class='d-flex align-items-center gap-2'>
            <a class='btn btn-sm btn-outline-lokantara fw-bold' href='{{ route('mitra.tourism.edit', $tourism) }}'>
                ✏️ Edit Destinasi
            </a>

            @if (in_array($tourism->status, ['draft', 'rejected']))
                <form method='POST' action='{{ route('mitra.tourism.submit', $tourism) }}'>
                    @csrf
                    <button class='btn btn-sm btn-lokantara fw-bold'>
                        🚀 Ajukan Moderasi
                    </button>
                </form>
            @endif

            @if (in_array($tourism->status, ['draft', 'rejected', 'published']))
                <form method='POST' action='{{ route('mitra.tourism.archive', $tourism) }}'>
                    @csrf
                    <button class='btn btn-sm btn-outline-danger'
                        onclick="return confirm('Apakah Anda yakin ingin mengarsipkan destinasi ini?')">
                        Arsipkan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class='row g-4'>
        <!-- Left Column: Preview & Info (7 Cols) -->
        <div class='col-lg-7'>
            <x-content-card title='Informasi Destinasi'>
                <h2 class='fs-4 fw-bold mb-2'>{{ $tourism->name }}</h2>
                <p class='text-muted mb-3' style='font-size: 14px;'>
                    {{ $tourism->description ?: 'Deskripsi belum diisi.' }}
                </p>

                <div class='p-3 rounded mb-3'
                    style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;'>
                    <div class='mb-2'>
                        <strong>📍 Alamat:</strong> {{ $tourism->address ?: 'Belum diisi' }}
                    </div>
                    <div>
                        <strong>🌐 Koordinat GPS:</strong> {{ $tourism->location?->latitude ?? '-' }},
                        {{ $tourism->location?->longitude ?? '-' }}
                    </div>
                </div>

                <strong class='d-block mb-2' style='font-size: 13px;'>Fasilitas Tersedia:</strong>
                <div class='d-flex gap-2 flex-wrap'>
                    @forelse($tourism->facilities as $facility)
                        <span class='badge'
                            style='background: #e2e8f0; color: #1e293b; padding: 6px 12px; font-weight: 500;'>
                            ✔ {{ $facility->name }}
                        </span>
                    @empty
                        <span class='text-muted' style='font-size: 13px;'>Belum ada fasilitas dipilih.</span>
                    @endforelse
                </div>
            </x-content-card>
        </div>

        <!-- Right Column: Media Upload & Gallery Preview (5 Cols) -->
        <div class='col-lg-5'>
            <x-content-card title='Media & Foto Wisata'>
                <!-- Upload Form -->
                <form method='POST' enctype='multipart/form-data' action='{{ route('mitra.tourism.media', $tourism) }}'
                    class='mb-3'>
                    @csrf
                    <div class='mb-2'>
                        <label class='form-label fw-semibold' style='font-size: 13px;'>Pilih File Foto</label>
                        <input class='form-control form-control-sm' type='file' name='image'
                            accept='image/jpeg,image/png,image/webp' required onchange='previewTourismMedia(this)'>
                    </div>

                    <!-- Live Preview Box -->
                    <div id='tourism-live-preview-box' class='mb-2 p-2 rounded'
                        style='display: none; background: var(--lokantara-background); border: 1px dashed var(--lokantara-primary); text-align: center;'>
                        <small class='text-muted d-block mb-1'>Preview foto yang akan diunggah:</small>
                        <img id='tourism-live-preview-img' src=''
                            style='max-height: 140px; max-width: 100%; border-radius: 6px; object-fit: cover;'>
                    </div>

                    <div class='row g-2 mb-2'>
                        <div class='col-6'>
                            <label class='form-label' style='font-size: 12px;'>Peran Foto</label>
                            <select class='form-select form-select-sm' name='role' required>
                                <option value='cover'>⭐ Foto Cover Utama</option>
                                <option value='gallery'>🖼️ Galeri Wisata</option>
                            </select>
                        </div>
                        <div class='col-6'>
                            <label class='form-label' style='font-size: 12px;'>Keterangan / Caption</label>
                            <input class='form-control form-control-sm' name='caption' placeholder='Opsional'>
                        </div>
                    </div>

                    <button class='btn btn-sm btn-lokantara w-100 fw-bold'>
                        📤 Unggah Foto
                    </button>
                </form>

                <hr class='my-3'>

                <!-- Saved Media Gallery Preview -->
                <div class='d-flex align-items-center justify-content-between mb-2'>
                    <h6 class='fw-bold mb-0' style='font-size: 13px;'>Foto Tersimpan ({{ $tourism->media->count() }})</h6>
                </div>

                @if ($tourism->media->isEmpty())
                    <div class='p-3 text-center rounded'
                        style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border);'>
                        <span class='fs-3 d-block mb-1'>📷</span>
                        <small class='text-muted'>Belum ada foto yang diunggah. Unggah minimal 1 Foto Cover agar dapat
                            diajukan ke publik.</small>
                    </div>
                @else
                    <div class='row g-2'>
                        @foreach ($tourism->media as $media)
                            <div class='col-6'>
                                <div class='card h-100 border' style='border-radius: 8px; overflow: hidden;'>
                                    <div style='height: 100px; background: #e2e8f0; position: relative;'>
                                        <img src='{{ asset('storage/' . $media->object_key) }}'
                                            alt='{{ $media->pivot->caption ?? $tourism->name }}'
                                            style='width: 100%; height: 100%; object-fit: cover;'>
                                        <span
                                            class='badge {{ $media->pivot->role === 'cover' ? 'bg-warning text-dark' : 'bg-dark text-white' }}'
                                            style='position: absolute; top: 6px; left: 6px; font-size: 10px;'>
                                            {{ strtoupper($media->pivot->role) }}
                                        </span>
                                    </div>
                                    <div class='p-2' style='font-size: 11px;'>
                                        <span
                                            class='text-truncate d-block fw-semibold'>{{ $media->pivot->caption ?: $media->original_name }}</span>
                                        <small class='text-muted'>{{ number_format($media->size_bytes / 1024, 0) }}
                                            KB</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-content-card>
        </div>
    </div>

    <!-- Operating Hours & Ticket Packages Grid -->
    <div class='row g-4 mt-1'>
        <div class='col-lg-6'>
            <x-content-card title='Jam Operasional Destinasi'>
                <form method='POST' action='{{ route('mitra.tourism.hours', $tourism) }}'>
                    @csrf
                    @method('PUT')
                    @php
                        $dayLabels = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                    @endphp
                    @for ($day = 1; $day <= 7; $day++)
                        <div class='row align-items-center mb-2 pb-2 border-bottom' style='font-size: 13px;'>
                            <input type='hidden' name='hours[{{ $day }}][weekday]' value='{{ $day }}'>
                            <div class='col-3 fw-semibold'>{{ $dayLabels[$day] }}</div>
                            <div class='col-4'>
                                <input class='form-control form-control-sm' type='time'
                                    name='hours[{{ $day }}][opens_at]' value='08:00'>
                            </div>
                            <div class='col-1 text-center text-muted'>-</div>
                            <div class='col-4'>
                                <input class='form-control form-control-sm' type='time'
                                    name='hours[{{ $day }}][closes_at]' value='17:00'>
                            </div>
                        </div>
                    @endfor
                    <button class='btn btn-sm btn-lokantara fw-bold mt-2'>Simpan Jam Operasional</button>
                </form>
            </x-content-card>
        </div>

        <div class='col-lg-6'>
            <x-content-card title='Paket Tiket Wisata'>
                <form method='POST' action='{{ route('mitra.tourism.packages.store', $tourism) }}' class='mb-3'>
                    @csrf
                    <div class='row g-2'>
                        <div class='col-md-5'>
                            <input class='form-control form-control-sm' name='name'
                                placeholder='Nama Tiket (cth: Reguler)' required>
                        </div>
                        <div class='col-md-4'>
                            <input class='form-control form-control-sm' name='price' type='number' step='0.01'
                                placeholder='Harga (Rp)' required>
                        </div>
                        <div class='col-md-3'>
                            <input class='form-control form-control-sm' name='quota_per_day' type='number'
                                placeholder='Kuota/hari'>
                        </div>
                    </div>
                    <button class='btn btn-sm btn-lokantara mt-2 fw-bold'>+ Tambah Paket Tiket</button>
                </form>

                <h6 class='fw-bold mt-4 mb-2' style='font-size: 13px;'>Daftar Paket Tiket & Status Kuota:</h6>
                @if($tourism->offers->isNotEmpty())
                    @foreach($tourism->offers as $offer)
                        @php
                            $todayAvail = $offer->availabilities->where('service_date', now()->format('Y-m-d'))->first();
                            $cap = $todayAvail?->capacity ?? ($offer->ticketPackage?->quota_per_day ?? 100);
                            $res = $todayAvail?->reserved_quantity ?? 0;
                            $rem = max(0, $cap - $res);
                            $pct = $cap > 0 ? min(100, round(($res / $cap) * 100)) : 0;
                            $progressColor = $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success');
                        @endphp
                        <div class='p-3 rounded mb-3'
                            style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;'>
                            <div class='d-flex align-items-center justify-content-between mb-2'>
                                <div>
                                    <strong class='fs-6 text-dark'>{{ $offer->name }}</strong>
                                    <span class='badge bg-success ms-1'>Rp {{ number_format($offer->price, 0, ',', '.') }}</span>
                                </div>
                                @if ($rem <= 0)
                                    <span class='badge bg-danger'>❌ Habis (Sold Out)</span>
                                @elseif ($rem <= 10)
                                    <span class='badge bg-warning text-dark'>⚠️ Sisa Kritis</span>
                                @else
                                    <span class='badge bg-success-subtle text-success border border-success'>🟢 Tersedia</span>
                                @endif
                            </div>

                            <!-- Quota Metric Summary Box -->
                            <div class='p-2 rounded mb-2' style='background: #ffffff; border: 1px solid #e2e8f0;'>
                                <div class='d-flex justify-content-between align-items-center mb-1' style='font-size: 11px;'>
                                    <span class='text-muted'>Keterisian Kuota Hari Ini ({{ now()->translatedFormat('d M Y') }}):</span>
                                    <strong class='text-dark'>{{ $res }} / {{ $cap }} Tiket ({{ $pct }}%)</strong>
                                </div>
                                <div class='progress' style='height: 8px; border-radius: 99px; background: #e2e8f0;'>
                                    <div class='progress-bar {{ $progressColor }}' role='progressbar' style='width: {{ $pct }}%;' aria-valuenow='{{ $pct }}' aria-valuemin='0' aria-valuemax='100'></div>
                                </div>
                                <div class='d-flex justify-content-between mt-2 pt-1 border-top' style='font-size: 11px;'>
                                    <span>Kapasitas: <strong>{{ $cap }}</strong></span>
                                    <span>Terjual/Hold: <strong>{{ $res }}</strong></span>
                                    <span class='text-success fw-bold'>Sisa Riil: {{ $rem }} Tiket</span>
                                </div>
                            </div>

                            <!-- Update Quota Form -->
                            <form class='row g-2 align-items-center' method='POST'
                                action='{{ route('mitra.tourism.quota', [$tourism, $offer->ticketPackage]) }}'>
                                @csrf
                                @method('PUT')
                                <div class='col-5'>
                                    <label class='form-label mb-1 text-muted' style='font-size: 10px;'>Pilih Tanggal</label>
                                    <input class='form-control form-control-sm' type='date' name='service_date'
                                        value='{{ date('Y-m-d') }}' min='{{ date('Y-m-d') }}' required>
                                </div>
                                <div class='col-4'>
                                    <label class='form-label mb-1 text-muted' style='font-size: 10px;'>Set Kuota</label>
                                    <input class='form-control form-control-sm' type='number' name='capacity'
                                        value='{{ $cap }}' placeholder='Kuota' min='1' required>
                                </div>
                                <div class='col-3'>
                                    <label class='form-label mb-1 text-muted d-block' style='font-size: 10px;'>&nbsp;</label>
                                    <button class='btn btn-sm btn-outline-lokantara w-100 fw-bold' title='Simpan Kuota Tanggal Ini'>Update</button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                @else
                    <div class='p-3 text-center rounded' style='background: var(--lokantara-background);'>
                        <small class='text-muted'>Belum ada paket tiket dibuat. Tambahkan formulir di atas.</small>
                    </div>
                @endif
            </x-content-card>
        </div>
    </div>

    <!-- Moderation History Card -->
    <x-content-card title='Riwayat Moderasi' class='mt-4'>
        @forelse($tourism->moderationReports as $report)
            @foreach ($report->actions as $action)
                <div class='border-bottom py-2'>
                    <strong>{{ str($action->action_type)->headline() }}</strong> ·
                    {{ $action->created_at?->format('d M Y H:i') }}
                    <div>{{ $action->notes }}</div>
                </div>
            @endforeach
        @empty
            <x-empty-state title='Belum ada riwayat' description='Riwayat muncul setelah destinasi diajukan ke admin.'
                compact />
        @endforelse
    </x-content-card>

    <script>
        function previewTourismMedia(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewBox = document.getElementById('tourism-live-preview-box');
                    const previewImg = document.getElementById('tourism-live-preview-img');
                    if (previewBox && previewImg) {
                        previewImg.src = e.target.result;
                        previewBox.style.display = 'block';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
