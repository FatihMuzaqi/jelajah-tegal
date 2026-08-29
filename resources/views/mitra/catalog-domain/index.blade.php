@extends('layouts.mitra')

@php
    $domainIcon = match(strtolower($domain ?? $title)) {
        'kuliner', 'culinary' => 'fa-solid fa-utensils',
        'event' => 'fa-solid fa-calendar-days',
        'rental' => 'fa-solid fa-car',
        default => 'fa-solid fa-layer-group',
    };
@endphp

@section('title', 'Katalog ' . $title)
@section('page-title', 'Katalog ' . $title)
@section('page-description', 'Kelola data layanan, harga, ketersediaan, dan status kurasi publik ' . strtolower($title) . '.')

@section('page-actions')
    <a class="btn btn-lokantara rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" href="{{ route($routePrefix . '.create') }}">
        <i class="fa-solid fa-plus"></i>
        <span>Tambah {{ $title }}</span>
    </a>
@endsection

@section('content')
    <x-table-wrapper :title="'Daftar ' . $title">
        @if ($items->isEmpty())
            <tbody>
                <tr>
                    <td>
                        <x-empty-state :title="'Belum ada ' . strtolower($title)" description="Tambahkan layanan {{ strtolower($title) }} pertama Anda untuk diajukan ke kurasi publik." compact>
                            <a class="btn btn-lokantara rounded-pill px-4 py-2 mt-2 fw-bold d-inline-flex align-items-center gap-2" href="{{ route($routePrefix . '.create') }}">
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah {{ $title }} Sekarang</span>
                            </a>
                        </x-empty-state>
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Nama Layanan</th>
                    <th>Wilayah</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Diperbarui</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            <strong class="d-block text-dark">{{ $item->name }}</strong>
                            <small class="text-muted d-inline-flex align-items-center gap-1">
                                <i class="{{ $domainIcon }} text-secondary" style="font-size: 11px;"></i>
                                {{ $item->category?->name ?? $title }}
                            </small>
                        </td>
                        <td>
                            <span class="badge text-bg-light border">
                                <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                {{ $item->region?->name ?? 'Tegal' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border">
                                <i class="{{ $domainIcon }} me-1"></i>
                                {{ $item->category?->name ?? $title }}
                            </span>
                        </td>
                        <td><x-status-badge :status="$item->status" /></td>
                        <td>
                            <small class="text-muted d-inline-flex align-items-center gap-1">
                                <i class="fa-regular fa-clock" style="font-size: 11px;"></i>
                                {{ $item->updated_at->diffForHumans() }}
                            </small>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-lokantara fw-semibold rounded-pill px-3"
                               href="{{ route($routePrefix . '.show', $item) }}">
                                <i class="fa-solid fa-gear me-1"></i> Kelola
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
        <x-slot:pagination>{{ $items->links() }}</x-slot:pagination>
    </x-table-wrapper>
@endsection
