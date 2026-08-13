@extends('layouts.mitra')
@section('title', 'Fitur Layanan Bisnis')
@section('page-title', 'Fitur Layanan Bisnis')
@section('page-description', 'Kelola status fitur layanan aktif dan ajukan izin pembukaan jenis layanan baru untuk usaha Anda.')

@section('content')
{{-- Status Fitur Aktif --}}
<div class='stats-grid'>
    @foreach($features as $feature)
        @php($isActive = $feature->status === 'enabled')
        @php($featureTone = $isActive ? 'success' : 'warning')
        @php($featureIcon = match($feature->serviceType->code ?? '') {
            'tourism' => 'fa-solid fa-umbrella-beach',
            'accommodation' => 'fa-solid fa-hotel',
            'culinary' => 'fa-solid fa-utensils',
            'event' => 'fa-solid fa-calendar-days',
            'rental' => 'fa-solid fa-car',
            default => 'fa-solid fa-layer-group'
        })
        <x-stat-card 
            :label='$feature->serviceType->name' 
            :value="$isActive ? 'Aktif' : 'Nonaktif'" 
            :tone='$featureTone'
            :caption="$isActive ? 'Dapat dikelola' : 'Perlu aktivasi'"
            :icon='$featureIcon'
        />
    @endforeach
</div>

{{-- Form Pengajuan Fitur Tambahan --}}
<x-content-card title='Ajukan Aktivasi Layanan Tambahan' subtitle='Pilih jenis layanan bisnis yang ingin dibuka untuk Mitra ini.' class='mt-4'>
    <form method='POST' action='{{ route('mitra.features.store') }}'>
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="service_type_id" class="form-label fw-bold">Pilih Jenis Layanan:</label>
                    <select name="service_type_id" id="service_type_id" class="form-select" required>
                        <option value="">-- Pilih Jenis Layanan --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @selected(old('service_type_id') == $service->id)>
                                @switch($service->code)
                                    @case('tourism')
                                        🏖️ {{ $service->name }} (Wisata Alam, Rekreasi & Wahana)
                                        @break
                                    @case('accommodation')
                                        🏨 {{ $service->name }} (Hotel, Villa, Homestay & Glamping)
                                        @break
                                    @case('culinary')
                                        🍲 {{ $service->name }} (Restoran, Kafe & Kuliner Khas)
                                        @break
                                    @case('event')
                                        🎪 {{ $service->name }} (Festival, Konser & Tiket Acara)
                                        @break
                                    @case('rental')
                                        🚗 {{ $service->name }} (Sewa Mobil, Motor & Transportasi)
                                        @break
                                    @default
                                        📦 {{ $service->name }}
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                    @error('service_type_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="reason" class="form-label fw-bold">Alasan / Deskripsi Kebutuhan:</label>
                    <textarea name="reason" id="reason" rows="3" class="form-control" placeholder="Contoh: Kami berencana membuka unit usaha penyewaan mobil pariwisata di Tegal..." required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <button class='btn btn-lokantara d-inline-flex align-items-center gap-2 mt-2'>
            <i class="fa-solid fa-paper-plane"></i>
            <span>Kirim Permohonan Fitur</span>
        </button>
    </form>
</x-content-card>

{{-- Tabel Riwayat Permintaan --}}
<x-table-wrapper title='Riwayat Pengajuan Fitur Layanan' class='mt-4'>
    @if($requests->isEmpty())
        <tbody>
            <tr>
                <td colspan="4">
                    <x-empty-state title='Belum ada pengajuan' description='Riwayat permohonan fitur tambahan akan tampil di sini.' compact />
                </td>
            </tr>
        </tbody>
    @else
        <thead>
            <tr>
                <th>Layanan Bisnis</th>
                <th>Alasan Pengajuan</th>
                <th>Status Pengajuan</th>
                <th>Catatan / Tanggapan Admin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $item)
                <tr>
                    <td data-label='Layanan Bisnis' class="fw-bold">
                        {{ $item->serviceType->name }}
                    </td>
                    <td data-label='Alasan Pengajuan'>
                        {{ str($item->reason)->limit(100) }}
                    </td>
                    <td data-label='Status Pengajuan'>
                        @if($item->status === 'approved')
                            <span class="badge bg-success-subtle text-success d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Disetujui
                            </span>
                        @elseif($item->status === 'rejected')
                            <span class="badge bg-danger-subtle text-danger d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-xmark"></i> Ditolak
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-clock"></i> Menunggu Review
                            </span>
                        @endif
                    </td>
                    <td data-label='Catatan / Tanggapan Admin' class="text-muted">
                        {{ $item->review_note ?? '—' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endif
    <x-slot:pagination>{{ $requests->links() }}</x-slot:pagination>
</x-table-wrapper>
@endsection
