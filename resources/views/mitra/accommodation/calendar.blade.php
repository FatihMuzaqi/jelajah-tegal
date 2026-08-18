@extends('layouts.mitra')

@section('title', 'Kalender: ' . $room->name)
@section('page-title', 'Kalender Ketersediaan & Tarif Musiman')
@section('page-description', $accommodation->name . ' · Tipe Kamar: ' . $room->name)

@section('content')
    <!-- Action Bar -->
    <div class='d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded-4'
        style='background: #ffffff; border: 1px solid var(--lokantara-border, #e2e8f0); box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);'>
        <div class='d-flex align-items-center gap-2'>
            <a href="{{ route('mitra.accommodation.show', $accommodation) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Properti
            </a>
            <span class='text-muted'>|</span>
            <span class='fw-bold text-dark fs-6'>{{ $room->name }}</span>
            <span class='badge bg-primary-subtle text-primary border'>
                <i class="fa-solid fa-bed me-1"></i> Total Stok: {{ $room->total_units }} Unit
            </span>
            <span class='badge bg-success-subtle text-success border'>
                Tarif Dasar: Rp {{ number_format($room->offer->price, 0, ',', '.') }} / Malam
            </span>
        </div>
    </div>

    <x-content-card title='Atur Kuota & Harga Musiman / Weekend'>
        <form method='POST' action='{{ route('mitra.accommodation.rooms.calendar.update', [$accommodation, $room]) }}'>
            @csrf
            @method('PUT')
            <div class='row g-3'>
                <div class='col-md-3'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Tanggal Mulai <span class='text-danger'>*</span></label>
                    <input type='date' name='start_date' class='form-control' value='{{ date('Y-m-d') }}' min='{{ date('Y-m-d') }}' required>
                </div>
                <div class='col-md-3'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Tanggal Selesai <span class='text-danger'>*</span></label>
                    <input type='date' name='end_date' class='form-control' value='{{ date('Y-m-d') }}' min='{{ date('Y-m-d') }}' required>
                </div>
                <div class='col-md-2'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Stok Unit Tersedia <span class='text-danger'>*</span></label>
                    <input type='number' min='1' max='{{ $room->total_units }}' name='available_units' class='form-control' value='{{ $room->total_units }}' required>
                </div>
                <div class='col-md-2'>
                    <label class='form-label fw-bold' style='font-size: 13px;'>Tarif Khusus (Rp)</label>
                    <input type='number' min='0' name='price_override' class='form-control' placeholder='Harga Override'>
                </div>
                <div class='col-md-2 d-flex align-items-end'>
                    <div class='form-check mb-2'>
                        <input class='form-check-input' type='checkbox' name='is_blocked' value='1' id='blockCheckbox'>
                        <label class='form-check-label fw-bold text-danger' for='blockCheckbox' style='font-size: 13px;'>
                            <i class="fa-solid fa-ban me-1"></i> Blokir Tanggal
                        </label>
                    </div>
                </div>
            </div>

            <button class='btn btn-lokantara fw-bold rounded-pill px-4 mt-3'>
                <i class="fa-solid fa-floppy-disk me-1"></i> Terapkan ke Kalender
            </button>
        </form>
    </x-content-card>

    <x-table-wrapper title='Status Ketersediaan & Kalender Mendatang' class='mt-4'>
        @if ($rows->isEmpty())
            <tbody>
                <tr>
                    <td colspan='5'>
                        <x-empty-state title='Kalender belum diatur khusus'
                            description='Harga dasar kamar Rp {{ number_format($room->offer->price, 0, ',', '.') }} dan kuota default tetap berlaku setiap hari sampai Anda membuat override tanggal di atas.' compact />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Tanggal Layanan</th>
                    <th>Status</th>
                    <th>Sisa Unit Riil</th>
                    <th>Terpesan / Hold</th>
                    <th>Tarif Berlaku</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>
                            <strong class='text-dark'>
                                <i class="fa-regular fa-calendar text-primary me-1"></i>
                                {{ $row->service_date->translatedFormat('d F Y (l)') }}
                            </strong>
                        </td>
                        <td><x-status-badge :status='$row->status' /></td>
                        <td>
                            <span class='badge bg-success-subtle text-success border border-success'>
                                {{ max(0, $row->capacity - $row->reserved_quantity) }} Unit Tersedia
                            </span>
                        </td>
                        <td>
                            <span class='badge text-bg-light border'>
                                {{ $row->reserved_quantity }} Unit
                            </span>
                        </td>
                        <td>
                            @if($row->price_override)
                                <strong class='text-primary'>Rp {{ number_format($row->price_override, 0, ',', '.') }}</strong>
                                <small class='badge bg-warning-subtle text-warning-emphasis border ms-1'>Khusus</small>
                            @else
                                <span class='text-muted'>Harga Dasar (Rp {{ number_format($room->offer->price, 0, ',', '.') }})</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $rows->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
