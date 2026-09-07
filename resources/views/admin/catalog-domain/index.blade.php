@extends('layouts.admin')

@section('title', $title)
@section('page-title', $title)
@section('page-description', 'Antrean pengajuan publikasi layanan ' . strtolower($title) . ' dan ulasan pengunjung.')

@section('content')
    <x-table-wrapper :title="'Antrean Moderasi ' . $title">
        @if ($items->isEmpty())
            <tbody>
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Antrean Kosong" :description="'Tidak ada ' . strtolower($title) . ' yang sedang menunggu moderasi saat ini.'" compact />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Item / Layanan</th>
                    <th>Mitra Pengelola</th>
                    <th>Kategori & Wilayah</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $cover = $item->media->where('pivot.role', 'cover')->first() ?? $item->media->first();
                                    $coverUrl = $cover ? asset('storage/' . $cover->object_key) : null;
                                @endphp
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="{{ $item->name }}" class="rounded-3 border" style="width: 38px; height: 38px; object-fit: cover;">
                                @else
                                    <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 38px; height: 38px; font-size: 14px;">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 13.5px;">{{ $item->name }}</strong>
                                    <small class="text-muted font-mono" style="font-size: 11px;">{{ $item->slug }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong class="text-dark fs-7">{{ $item->mitra->display_name }}</strong>
                            <small class="text-muted d-block" style="font-size: 11px;">Slug: {{ $item->mitra->slug }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 fs-8 fw-semibold">
                                {{ $item->category?->name ?? 'Umum' }}
                            </span>
                            <small class="text-muted d-block mt-0.5" style="font-size: 11px;">
                                <i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $item->region?->name ?? 'Tegal' }}
                            </small>
                        </td>
                        <td>
                            <x-status-badge :status="$item->status" />
                        </td>
                        <td>
                            <span class="text-muted fs-7" title="{{ $item->updated_at->translatedFormat('d F Y, H:i') }}">
                                {{ $item->updated_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-lokantara fw-bold px-3 py-1.5 rounded-pill shadow-xs" href="{{ route($routePrefix . '.show', $item) }}">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Tinjau
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>
            {{ $items->links() }}
        </x-slot:pagination>
    </x-table-wrapper>

    <x-table-wrapper title="Ulasan Menunggu Moderasi" class="mt-4">
        @if ($reviews->isEmpty())
            <tbody>
                <tr>
                    <td colspan="4">
                        <x-empty-state title="Tidak Ada Ulasan Tertunda" description="Ulasan baru dari wisatawan akan tampil di sini untuk dimoderasi." compact />
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Item Layanan</th>
                    <th>Penulis & Rating</th>
                    <th>Isi Ulasan</th>
                    <th style="min-width: 240px;">Keputusan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reviews as $review)
                    <tr>
                        <td>
                            <strong class="text-dark fs-7">{{ $review->catalogEntity->name }}</strong>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark fs-7">{{ $review->user?->name ?? 'Pengunjung' }}</div>
                            <div class="text-warning fs-8 mt-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                @endfor
                                <span class="text-muted ms-1 fw-normal">({{ $review->rating }}/5)</span>
                            </div>
                        </td>
                        <td>
                            <p class="mb-0 text-dark fs-7" style="line-height: 1.4;">{{ str($review->body)->limit(120) }}</p>
                        </td>
                        <td>
                            <form method="POST" action="{{ route($routePrefix . '.reviews.update', $review) }}">
                                @csrf
                                @method('PATCH')
                                <div class="d-flex align-items-center gap-1.5 mb-1.5">
                                    <button type="submit" class="btn btn-sm btn-success text-white fw-bold px-2.5 py-1 rounded-pill fs-8" name="decision" value="publish">
                                        <i class="fa-solid fa-check me-1"></i> Setujui
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-2.5 py-1 rounded-pill fs-8" name="decision" value="reject">
                                        <i class="fa-solid fa-xmark me-1"></i> Tolak
                                    </button>
                                </div>
                                <input class="form-control form-control-sm rounded-2 fs-8" name="reason" placeholder="Alasan jika ditolak (opsional)">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>
            {{ $reviews->links() }}
        </x-slot:pagination>
    </x-table-wrapper>
@endsection
