@extends('layouts.mitra')

@section('title', $tourism->name)
@section('page-title', $tourism->name)
@section('page-description', 'Preview, kelola foto media, paket tiket, jam operasional, dan moderasi destinasi wisata.')

@section('content')
    <!-- Action Bar -->
    <div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded-4'
        style='background: #ffffff; border: 1px solid var(--lokantara-border, #e2e8f0); box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);'>
        <div class='d-flex align-items-center gap-2'>
            <x-status-badge :status='$tourism->status' />
            <span class='text-muted'>|</span>
            <span class='badge text-bg-light border'>
                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                {{ $tourism->region?->name ?? 'Tegal' }}
            </span>
            <span class='badge bg-secondary-subtle text-secondary border'>
                <i class="fa-solid fa-umbrella-beach me-1"></i>
                {{ $tourism->category?->name ?? 'Wisata' }}
            </span>
        </div>

        <div class='d-flex align-items-center gap-2'>
            <a class='btn btn-sm btn-outline-lokantara rounded-pill px-3 fw-bold' href='{{ route('mitra.tourism.edit', $tourism) }}'>
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Destinasi
            </a>

            @if (in_array($tourism->status, ['draft', 'rejected']))
                <form method='POST' action='{{ route('mitra.tourism.submit', $tourism) }}'>
                    @csrf
                    <button class='btn btn-sm btn-lokantara rounded-pill px-3 fw-bold'>
                        <i class="fa-solid fa-paper-plane me-1"></i> Ajukan Moderasi
                    </button>
                </form>
            @endif

            @if (in_array($tourism->status, ['draft', 'rejected', 'published']))
                <form method='POST' action='{{ route('mitra.tourism.archive', $tourism) }}'>
                    @csrf
                    <button class='btn btn-sm btn-outline-danger rounded-pill px-3'
                        onclick="return confirm('Apakah Anda yakin ingin mengarsipkan destinasi ini?')">
                        <i class="fa-solid fa-box-archive me-1"></i> Arsipkan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class='row g-4'>
        <!-- Left Column: Preview & Info (7 Cols) -->
        <div class='col-lg-7'>
            <x-content-card title='Informasi Destinasi'>
                <h2 class='fs-4 fw-bold mb-2 text-dark'>{{ $tourism->name }}</h2>
                <p class='text-muted mb-3' style='font-size: 14px;'>
                    {{ $tourism->description ?: 'Deskripsi destinasi belum diisi.' }}
                </p>

                <div class='p-3 rounded-3 mb-3'
                    style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;'>
                    <div class='mb-2'>
                        <strong class="text-dark"><i class="fa-solid fa-map-pin text-primary me-1"></i> Alamat:</strong> {{ $tourism->address ?: 'Belum diisi' }}
                    </div>
                    @if ($tourism->location)
                        <div class='mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2'>
                            <div>
                                <strong class="text-dark"><i class="fa-solid fa-crosshairs text-success me-1"></i> Koordinat GPS:</strong> {{ $tourism->location->latitude }}, {{ $tourism->location->longitude }}
                            </div>
                            <a href="https://www.google.com/maps?q={{ $tourism->location->latitude }},{{ $tourism->location->longitude }}" target="_blank" rel="noopener noreferrer" class="badge text-bg-light border text-decoration-none py-1.5 px-2">
                                <i class="fa-solid fa-arrow-up-right-from-square text-success me-1"></i> Buka di Google Maps
                            </a>
                        </div>
                    @endif
                </div>

                <strong class='d-block mb-2 text-dark' style='font-size: 13px;'>Fasilitas Tersedia:</strong>
                <div class='d-flex gap-2 flex-wrap'>
                    @forelse($tourism->facilities as $facility)
                        <span class='badge border'
                            style='background: #f8fafc; color: #334155; padding: 6px 12px; font-weight: 500;'>
                            <i class="fa-solid fa-check text-success me-1"></i> {{ $facility->name }}
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
                        <label class='form-label fw-semibold text-dark' style='font-size: 13px;'>Pilih File Foto</label>
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
                                <option value='cover'>Foto Cover Utama</option>
                                <option value='gallery'>Galeri Wisata</option>
                            </select>
                        </div>
                        <div class='col-6'>
                            <label class='form-label' style='font-size: 12px;'>Keterangan / Caption</label>
                            <input class='form-control form-control-sm' name='caption' placeholder='Opsional'>
                        </div>
                    </div>

                    <button class='btn btn-sm btn-lokantara w-100 fw-bold'>
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Unggah Foto
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
                        <i class="fa-solid fa-images fs-2 text-muted mb-1 d-block"></i>
                        <small class='text-muted'>Belum ada foto yang diunggah. Unggah minimal 1 Foto Cover agar dapat
                            diajukan ke publik.</small>
                    </div>
                @else
                    <div class='row g-2'>
                        @foreach ($tourism->media as $media)
                            <div class='col-4'>
                                <div class='card border h-100 overflow-hidden position-relative'
                                    style='border-radius: 8px;'>
                                    <div style='height: 75px; background: #000; overflow: hidden;'>
                                        <img src='{{ asset('storage/' . $media->object_key) }}'
                                            alt='{{ $media->original_name }}'
                                            style='width: 100%; height: 100%; object-fit: cover;'>
                                        <span
                                            class='badge {{ $media->pivot->role === 'cover' ? 'bg-primary' : 'bg-dark' }}'
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
                    <button class='btn btn-sm btn-lokantara fw-bold mt-2'>
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Jam Operasional
                    </button>
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
                    <button class='btn btn-sm btn-lokantara mt-2 fw-bold'>
                        <i class="fa-solid fa-plus me-1"></i> Tambah Paket Tiket
                    </button>
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
                        <div class='p-3 rounded-3 mb-3'
                            style='background: var(--lokantara-background); border: 1px solid var(--lokantara-border); font-size: 13px;'>
                            <div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2'>
                                <div>
                                    <strong class='fs-6 text-dark'>{{ $offer->name }}</strong>
                                    <span class='badge bg-success ms-1'>Rp {{ number_format($offer->price, 0, ',', '.') }}</span>
                                    <span class='badge text-bg-light border ms-1'>Kuota: {{ $offer->ticketPackage?->quota_per_day ?? 100 }}/hari</span>
                                </div>
                                <div class='d-flex align-items-center gap-1.5'>
                                    @if ($rem <= 0)
                                        <span class='badge bg-danger'><i class="fa-solid fa-circle-xmark me-1"></i> Habis (Sold Out)</span>
                                    @elseif ($rem <= 10)
                                        <span class='badge bg-warning text-dark'><i class="fa-solid fa-triangle-exclamation me-1"></i> Sisa Kritis</span>
                                    @else
                                        <span class='badge bg-success-subtle text-success border border-success'><i class="fa-solid fa-circle-check me-1"></i> Tersedia</span>
                                    @endif

                                    <!-- Button Trigger Edit Modal -->
                                    <button type='button' class='btn btn-xs btn-outline-primary fw-bold py-1 px-2 rounded-2' data-bs-toggle='modal' data-bs-target='#editPackageModal{{ $offer->id }}' style='font-size: 11px;'>
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </button>

                                    <!-- Delete Package Form -->
                                    <form method='POST' action='{{ route('mitra.tourism.packages.destroy', [$tourism, $offer->ticketPackage]) }}' class='d-inline'>
                                        @csrf
                                        @method('DELETE')
                                        <button type='submit' class='btn btn-xs btn-outline-danger py-1 px-2 rounded-2' style='font-size: 11px;' onclick="return confirm('Apakah Anda yakin ingin menghapus paket tiket {{ $offer->name }}?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Edit Paket Tiket -->
                            <div class='modal fade' id='editPackageModal{{ $offer->id }}' tabindex='-1' aria-labelledby='editPackageModalLabel{{ $offer->id }}' aria-hidden='true'>
                                <div class='modal-dialog modal-dialog-centered'>
                                    <div class='modal-content border-0 shadow-lg rounded-4 text-start'>
                                        <form method='POST' action='{{ route('mitra.tourism.packages.update', [$tourism, $offer->ticketPackage]) }}'>
                                            @csrf
                                            @method('PUT')
                                            <div class='modal-header border-bottom py-3 px-4' style='background: #f8fafc;'>
                                                <h6 class='modal-title fw-bold text-dark' id='editPackageModalLabel{{ $offer->id }}'>
                                                    <i class='fa-solid fa-ticket text-primary me-1'></i> Edit Paket Tiket Wisata
                                                </h6>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body p-4'>
                                                <div class='mb-3'>
                                                    <label class='form-label fw-bold' style='font-size: 13px;'>Nama Paket Tiket <span class='text-danger'>*</span></label>
                                                    <input type='text' name='name' class='form-control' value='{{ old('name', $offer->name) }}' placeholder='Contoh: Tiket Masuk Reguler' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label class='form-label fw-bold' style='font-size: 13px;'>Harga Tiket Masuk (Rp) <span class='text-danger'>*</span></label>
                                                    <div class='input-group'>
                                                        <span class='input-group-text bg-white fw-bold text-success'>Rp</span>
                                                        <input type='number' step='0.01' min='0' name='price' class='form-control' value='{{ old('price', $offer->price) }}' placeholder='Contoh: 25000' required>
                                                    </div>
                                                </div>
                                                <div class='mb-2'>
                                                    <label class='form-label fw-bold' style='font-size: 13px;'>Kuota Harian Dasar</label>
                                                    <div class='input-group'>
                                                        <input type='number' min='0' name='quota_per_day' class='form-control' value='{{ old('quota_per_day', $offer->ticketPackage?->quota_per_day ?? 100) }}' placeholder='Contoh: 100'>
                                                        <span class='input-group-text bg-white'>Tiket/Hari</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='modal-footer border-top py-2.5 px-4' style='background: #f8fafc;'>
                                                <button type='button' class='btn btn-sm btn-secondary rounded-pill px-3' data-bs-dismiss='modal'>Batal</button>
                                                <button type='submit' class='btn btn-sm btn-lokantara rounded-pill px-4 fw-bold'>Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
