@extends('layouts.mitra')

@section('title', 'Anggota & Tim')
@section('page-title', 'Manajemen Anggota & Tim')
@section('page-description', 'Kelola staf operasional dan petugas scanner loket (gatekeeper) pada tenant usaha Anda.')

@section('content')
    <div class="row g-4">
        <!-- Form Undang Anggota -->
        <div class="col-lg-5">
            <x-content-card title="Undang Anggota Tim Baru">
                <form method="POST" action="{{ route('mitra.members.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 13px;">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-regular fa-user text-muted"></i></span>
                            <input type="text" name="name" class="form-control" placeholder="Nama staf / petugas" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 13px;">Email Login <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-regular fa-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 13px;">Peran Akses <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Pilih Peran Akses --</option>
                            <option value="mitra-staff" @selected(old('role') === 'mitra-staff')>Mitra Staff (Kelola Katalog & Layanan)</option>
                            <option value="gatekeeper" @selected(old('role') === 'gatekeeper')>Gatekeeper (Petugas Validasi QR Tiket)</option>
                        </select>
                    </div>
                    <button class="btn btn-lokantara w-100 fw-bold rounded-pill py-2">
                        <i class="fa-solid fa-paper-plane me-1"></i> Kirim Undangan
                    </button>
                </form>
            </x-content-card>
        </div>

        <!-- Daftar Anggota Aktif -->
        <div class="col-lg-7">
            <x-table-wrapper title="Daftar Anggota Aktif">
                @if ($members->isEmpty())
                    <tbody>
                        <tr>
                            <td><x-empty-state title="Belum ada anggota tim" description="Undang staf atau petugas loket gatekeeper untuk membantu operasional usaha." compact /></td>
                        </tr>
                    </tbody>
                @else
                    <thead>
                        <tr>
                            <th>Nama & Email</th>
                            <th>Status</th>
                            <th>Bergabung</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td>
                                    <strong class="d-block text-dark">{{ $member->user->name }}</strong>
                                    <small class="text-muted">{{ $member->user->email }}</small>
                                </td>
                                <td><x-status-badge :status="$member->status" /></td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fa-regular fa-calendar text-secondary me-1"></i>
                                        {{ $member->joined_at?->format('d M Y') ?? 'Menunggu aktivasi' }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    @if ($member->user_id !== $mitra->owner_user_id && $member->user_id !== auth()->id() && $member->status !== 'revoked')
                                        <form method="POST" action="{{ route('mitra.members.destroy', $member) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" style="font-size: 11px;"
                                                onclick="return confirm('Apakah Anda yakin ingin mencabut akses anggota ini?')">
                                                <i class="fa-solid fa-user-xmark me-1"></i> Cabut Akses
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge text-bg-light border">Owner / Akun Utama</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @endif
                <x-slot:pagination>{{ $members->links() }}</x-slot:pagination>
            </x-table-wrapper>
        </div>
    </div>
@endsection
