@extends('layouts.admin')

@section('title', 'Saran & Kontak Masuk')
@section('page-title', 'Saran & Kontak Masuk')
@section('page-description', 'Kelola aspirasi, saran perbaikan, kritik, dan pertanyaan masyarakat yang masuk melalui portal publik Jelajah Tegal.')

@section('content')
    {{-- Summary Stats Grid --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 fw-semibold text-uppercase">Total Masukan</span>
                        <h3 class="fw-bold fs-4 mb-0 text-dark">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="p-2.5 rounded-3 bg-light text-primary fs-5">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 fw-semibold text-uppercase">Menunggu Review</span>
                        <h3 class="fw-bold fs-4 mb-0 text-warning">{{ $stats['pending'] }}</h3>
                    </div>
                    <div class="p-2.5 rounded-3 bg-warning-subtle text-warning fs-5">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 fw-semibold text-uppercase">Saran Perbaikan</span>
                        <h3 class="fw-bold fs-4 mb-0 text-success">{{ $stats['saran'] }}</h3>
                    </div>
                    <div class="p-2.5 rounded-3 bg-success-subtle text-success fs-5">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: #ffffff; border: 1px solid #e2e8f0;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 fw-semibold text-uppercase">Kritik Membangun</span>
                        <h3 class="fw-bold fs-4 mb-0 text-danger">{{ $stats['kritik'] }}</h3>
                    </div>
                    <div class="p-2.5 rounded-3 bg-danger-subtle text-danger fs-5">
                        <i class="fa-solid fa-comment-dots"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4" style="background: #ffffff; border: 1px solid #e2e8f0;">
        <div class="row g-2 align-items-center">
            {{-- Type Pills --}}
            <div class="col-lg-8 d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('admin.feedbacks.index', request()->except('type', 'page')) }}"
                   class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 {{ !request('type') ? 'btn-dark' : 'btn-light border text-muted' }}">
                    Semua Jenis
                </a>
                <a href="{{ route('admin.feedbacks.index', array_merge(request()->except('type', 'page'), ['type' => 'saran'])) }}"
                   class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 {{ request('type') === 'saran' ? 'btn-success' : 'btn-light border text-muted' }}">
                    <i class="fa-solid fa-lightbulb me-1"></i> Saran
                </a>
                <a href="{{ route('admin.feedbacks.index', array_merge(request()->except('type', 'page'), ['type' => 'kritik'])) }}"
                   class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 {{ request('type') === 'kritik' ? 'btn-danger' : 'btn-light border text-muted' }}">
                    <i class="fa-solid fa-comment-dots me-1"></i> Kritik
                </a>
                <a href="{{ route('admin.feedbacks.index', array_merge(request()->except('type', 'page'), ['type' => 'pertanyaan'])) }}"
                   class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 {{ request('type') === 'pertanyaan' ? 'btn-info text-white' : 'btn-light border text-muted' }}">
                    <i class="fa-solid fa-question me-1"></i> Pertanyaan
                </a>
                <a href="{{ route('admin.feedbacks.index', array_merge(request()->except('type', 'page'), ['type' => 'apresiasi'])) }}"
                   class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold fs-8 {{ request('type') === 'apresiasi' ? 'btn-warning text-dark' : 'btn-light border text-muted' }}">
                    <i class="fa-solid fa-heart me-1"></i> Apresiasi
                </a>

                <span class="text-muted mx-1">|</span>

                {{-- Status Filter --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle rounded-pill px-3 py-1.5 fs-8 fw-semibold" type="button" data-bs-toggle="dropdown">
                        Status: {{ request('status') ? ucfirst(request('status')) : 'Semua' }}
                    </button>
                    <ul class="dropdown-menu border-0 shadow-sm rounded-3 fs-8">
                        <li><a class="dropdown-item" href="{{ route('admin.feedbacks.index', request()->except('status', 'page')) }}">Semua Status</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.feedbacks.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}">Pending (Menunggu)</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.feedbacks.index', array_merge(request()->except('status', 'page'), ['status' => 'reviewed'])) }}">Reviewed (Ditinjau)</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.feedbacks.index', array_merge(request()->except('status', 'page'), ['status' => 'replied'])) }}">Replied (Dijawab)</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.feedbacks.index', array_merge(request()->except('status', 'page'), ['status' => 'archived'])) }}">Archived (Diarsipkan)</a></li>
                    </ul>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="col-lg-4">
                <form method="GET" action="{{ route('admin.feedbacks.index') }}">
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0 fs-8" placeholder="Cari nama, email, subjek, pesan...">
                        @if(request('q'))
                            <a href="{{ route('admin.feedbacks.index', request()->except('q', 'page')) }}" class="btn btn-light border border-start-0 text-muted">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <x-table-wrapper title="Kotak Masuk Masukan & Aspirasi ({{ $feedbacks->total() }} Pesan)">
        @if ($feedbacks->isEmpty())
            <tbody>
                <tr>
                    <td colspan="7">
                        <div class="text-center py-5">
                            <i class="fa-solid fa-inbox text-muted fs-1 mb-2"></i>
                            <h5 class="fw-bold fs-6 text-dark mb-1">Belum Ada Masukan</h5>
                            <p class="text-muted fs-8 mb-0">Tidak ada data saran, kritik, atau kontak masuk yang sesuai dengan filter saat ini.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        @else
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th>Waktu Masuk</th>
                    <th>Pengirim</th>
                    <th>Jenis & Kategori</th>
                    <th>Subjek & Pesan</th>
                    <th class="text-center">Status</th>
                    <th style="width: 140px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($feedbacks as $index => $item)
                    <tr>
                        <td class="text-center text-muted fw-semibold fs-8">
                            {{ $feedbacks->firstItem() + $index }}
                        </td>
                        <td class="fs-8 text-nowrap">
                            <span class="fw-bold text-dark d-block">{{ $item->created_at->format('d M Y') }}</span>
                            <small class="text-muted">{{ $item->created_at->format('H:i') }} WIB ({{ $item->created_at->diffForHumans() }})</small>
                        </td>
                        <td>
                            <span class="fw-bold text-dark fs-8 d-block">{{ $item->name }}</span>
                            <small class="text-muted fs-8">{{ $item->email }}</small>
                            @if($item->phone)
                                <div class="mt-1">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->phone) }}" target="_blank" class="badge bg-success-subtle text-success text-decoration-none fs-9">
                                        <i class="fa-brands fa-whatsapp me-1"></i> {{ $item->phone }}
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeBadgeClass = match($item->type) {
                                    'saran' => 'bg-success text-white',
                                    'kritik' => 'bg-danger text-white',
                                    'pertanyaan' => 'bg-info text-white',
                                    'apresiasi' => 'bg-warning text-dark',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <span class="badge {{ $typeBadgeClass }} text-uppercase fs-9 mb-1">
                                {{ $item->type }}
                            </span>
                            <small class="d-block text-muted text-capitalize fs-8">
                                <i class="fa-solid fa-tag me-1"></i> {{ $item->category }}
                            </small>
                        </td>
                        <td>
                            <strong class="d-block text-dark fs-8 mb-1">{{ $item->subject }}</strong>
                            <p class="text-muted fs-8 mb-0 text-truncate" style="max-width: 320px;">
                                {{ $item->message }}
                            </p>
                        </td>
                        <td class="text-center">
                            @php
                                $statusBadgeClass = match($item->status) {
                                    'pending' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    'reviewed' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                    'replied' => 'bg-success-subtle text-success border border-success-subtle',
                                    'archived' => 'bg-light text-muted border',
                                    default => 'bg-light text-muted'
                                };
                                $statusLabel = match($item->status) {
                                    'pending' => 'Menunggu Review',
                                    'reviewed' => 'Ditinjau',
                                    'replied' => 'Dijawab',
                                    'archived' => 'Diarsipkan',
                                    default => ucfirst($item->status)
                                };
                            @endphp
                            <span class="badge {{ $statusBadgeClass }} fs-9 py-1 px-2">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fs-8 fw-semibold"
                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                    <i class="fa-solid fa-eye me-1"></i> Detail
                                </button>

                                <form action="{{ route('admin.feedbacks.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus masukan ini?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 28px; height: 28px;" title="Hapus">
                                        <i class="fa-solid fa-trash-can fs-9"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Detail & Action Modal --}}
                            <div class="modal fade text-start" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <div class="modal-header border-bottom px-4 py-3 bg-light">
                                            <h5 class="modal-title fs-6 fw-bold text-dark d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-envelope-open-text text-primary"></i>
                                                <span>Detail Pesan & Tindak Lanjut</span>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.feedbacks.update', $item) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body p-4">
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted fs-8 text-uppercase fw-bold mb-1">Nama Pengirim</label>
                                                        <p class="fw-bold text-dark fs-7 mb-0">{{ $item->name }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted fs-8 text-uppercase fw-bold mb-1">Kontak Pengirim</label>
                                                        <p class="text-dark fs-7 mb-0">
                                                            <i class="fa-solid fa-envelope me-1 text-muted"></i> {{ $item->email }}
                                                            @if($item->phone)
                                                                <br><i class="fa-brands fa-whatsapp me-1 text-success"></i> {{ $item->phone }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted fs-8 text-uppercase fw-bold mb-1">Jenis & Kategori</label>
                                                        <p class="text-dark fs-7 mb-0">
                                                            <span class="badge {{ $typeBadgeClass }} text-uppercase fs-9 me-1">{{ $item->type }}</span>
                                                            <span class="badge bg-light text-dark border fs-9 text-capitalize">{{ $item->category }}</span>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-muted fs-8 text-uppercase fw-bold mb-1">Waktu Masuk</label>
                                                        <p class="text-dark fs-7 mb-0">{{ $item->created_at->format('d F Y, H:i') }} WIB</p>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-muted fs-8 text-uppercase fw-bold mb-1">Subjek Masukan</label>
                                                    <div class="p-2.5 rounded-3 bg-light border fw-bold text-dark fs-7">
                                                        {{ $item->subject }}
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="form-label text-muted fs-8 text-uppercase fw-bold mb-1">Isi Pesan / Masukan</label>
                                                    <div class="p-3 rounded-3 bg-light border text-dark fs-7" style="line-height: 1.7; white-space: pre-line;">
                                                        {{ $item->message }}
                                                    </div>
                                                </div>

                                                <hr class="my-4 text-muted">

                                                <h6 class="fw-bold fs-7 text-dark mb-3">
                                                    <i class="fa-solid fa-pen-to-square me-1 text-success"></i> Tindak Lanjut Admin
                                                </h6>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold fs-8 text-dark">Ubah Status <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select form-select-sm" required>
                                                            <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending (Menunggu Review)</option>
                                                            <option value="reviewed" {{ $item->status === 'reviewed' ? 'selected' : '' }}>Reviewed (Telah Ditinjau)</option>
                                                            <option value="replied" {{ $item->status === 'replied' ? 'selected' : '' }}>Replied (Telah Dibalas / Ditindaklanjuti)</option>
                                                            <option value="archived" {{ $item->status === 'archived' ? 'selected' : '' }}>Archived (Diarsipkan)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold fs-8 text-dark">Catatan Internal Admin (Opsional)</label>
                                                        <input type="text" name="admin_notes" value="{{ $item->admin_notes }}" class="form-control form-control-sm" placeholder="Contoh: Sudah dikonfirmasi ke pengelola Guci">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top px-4 py-3 bg-light d-flex justify-content-between">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                                                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Status
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @endif
    </x-table-wrapper>

    {{-- Pagination --}}
    @if ($feedbacks->hasPages())
        <div class="mt-4">
            {{ $feedbacks->links() }}
        </div>
    @endif
@endsection
