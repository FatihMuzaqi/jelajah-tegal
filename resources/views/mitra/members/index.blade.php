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
                                    <div class="d-flex justify-content-end align-items-center gap-1.5 flex-wrap">
                                        @if ($member->user_id !== $mitra->owner_user_id && $member->user_id !== auth()->id() && $member->status !== 'revoked')
                                            <!-- Reset Password Button -->
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;"
                                                    data-bs-toggle="modal" data-bs-target="#reset-member-pw-{{ $member->id }}">
                                                <i class="fa-solid fa-key me-1"></i> Reset Password
                                            </button>

                                            <!-- Cabut Akses Button -->
                                            <form method="POST" action="{{ route('mitra.members.destroy', $member) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 11px;"
                                                    onclick="return confirm('Apakah Anda yakin ingin mencabut akses anggota ini?')">
                                                    <i class="fa-solid fa-user-xmark me-1"></i> Cabut
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge text-bg-light border" style="font-size: 11px;">Owner / Akun Utama</span>
                                        @endif
                                    </div>

                                    <!-- Modal Reset Password Anggota -->
                                    <div class="modal fade text-start" id="reset-member-pw-{{ $member->id }}" tabindex="-1" aria-labelledby="resetMemberPwLabel{{ $member->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <form method="POST" action="{{ route('mitra.members.reset-password', $member) }}">
                                                    @csrf
                                                    <div class="modal-header border-bottom py-3 px-4" style="background: #f8fafc;">
                                                        <h6 class="modal-title fw-bold text-dark" id="resetMemberPwLabel{{ $member->id }}">
                                                            <i class="fa-solid fa-key text-primary me-1"></i> Reset Password Anggota
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="p-2.5 rounded-3 mb-3" style="background: #f1f5f9; font-size: 13px;">
                                                            <span class="text-muted d-block" style="font-size: 11px;">Anggota Tim:</span>
                                                            <strong class="text-dark">{{ $member->user->name }}</strong> ({{ $member->user->email }})
                                                        </div>

                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <label class="form-label fw-bold text-dark mb-0" style="font-size: 13px;">
                                                                    Kata Sandi Baru <span class="text-danger">*</span>
                                                                </label>
                                                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 11px;"
                                                                        onclick="generateMemberPassword('new_pwd_{{ $member->id }}', 'conf_pwd_{{ $member->id }}')">
                                                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Buat Acak
                                                                </button>
                                                            </div>
                                                            <input type="text" name="password" id="new_pwd_{{ $member->id }}" class="form-control font-monospace" placeholder="Minimal 8 karakter" required>
                                                        </div>

                                                        <div class="mb-2">
                                                            <label class="form-label fw-bold text-dark" style="font-size: 13px;">
                                                                Ulangi Kata Sandi Baru <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="password_confirmation" id="conf_pwd_{{ $member->id }}" class="form-control font-monospace" placeholder="Ulangi kata sandi baru" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top py-2.5 px-4" style="background: #f8fafc;">
                                                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-lokantara rounded-pill px-4 fw-bold">
                                                            <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Password Baru
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
                <x-slot:pagination>{{ $members->links() }}</x-slot:pagination>
            </x-table-wrapper>
        </div>
    </div>

    @push('scripts')
    <script>
        function generateMemberPassword(newId, confId) {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
            let pwd = '';
            for (let i = 0; i < 10; i++) {
                pwd += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById(newId).value = pwd;
            document.getElementById(confId).value = pwd;
        }
    </script>
    @endpush
@endsection
