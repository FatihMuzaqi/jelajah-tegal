@extends('layouts.mitra')

@section('title', 'Rekening Bank Mitra')
@section('page-title', 'Manajemen Rekening Bank')
@section('page-description', 'Kelola rekening bank terdaftar untuk pencairan saldo dan settlement otomatis melalui transfer bank / Midtrans Iris.')

@section('content')
    <div class="row g-4">
        <!-- Form Tambah Rekening -->
        <div class="col-lg-5">
            <x-content-card title="Tambah Rekening Bank">
                <div class="alert alert-info py-2 px-3 mb-3 fs-8 border-0" style="background: rgba(16, 185, 129, 0.1); color: #065f46; border-radius: 12px;">
                    <i class="fa-solid fa-shield-halved me-1 text-emerald"></i>
                    Nomor rekening disimpan dengan <strong>enkripsi standar AES-256</strong> untuk menjamin keamanan finansial Mitra.
                </div>

                <form method="POST" action="{{ route('mitra.bank-accounts.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7" for="bank_code">Nama / Kode Bank <span class="text-danger">*</span></label>
                        <select name="bank_code" id="bank_code" class="form-select fs-7" required>
                            <option value="">-- Pilih Bank Tujuan --</option>
                            <option value="BCA" @selected(old('bank_code') === 'BCA')>Bank Central Asia (BCA)</option>
                            <option value="MANDIRI" @selected(old('bank_code') === 'MANDIRI')>Bank Mandiri</option>
                            <option value="BNI" @selected(old('bank_code') === 'BNI')>Bank Negara Indonesia (BNI)</option>
                            <option value="BRI" @selected(old('bank_code') === 'BRI')>Bank Rakyat Indonesia (BRI)</option>
                            <option value="PERMATA" @selected(old('bank_code') === 'PERMATA')>Bank Permata</option>
                            <option value="CIMB" @selected(old('bank_code') === 'CIMB')>Bank CIMB Niaga</option>
                            <option value="BSI" @selected(old('bank_code') === 'BSI')>Bank Syariah Indonesia (BSI)</option>
                            <option value="DANAMON" @selected(old('bank_code') === 'DANAMON')>Bank Danamon</option>
                            <option value="JATENG" @selected(old('bank_code') === 'JATENG')>Bank Jateng</option>
                            <option value="JAGO" @selected(old('bank_code') === 'JAGO')>Bank Jago</option>
                            <option value="SEABANK" @selected(old('bank_code') === 'SEABANK')>SeaBank Indonesia</option>
                            <option value="BTPN" @selected(old('bank_code') === 'BTPN')>Bank BTPN / Jenius</option>
                        </select>
                        @error('bank_code')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7" for="account_name">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" id="account_name" class="form-control fs-7"
                               placeholder="Nama sesuai buku tabungan..." value="{{ old('account_name') }}" required>
                        @error('account_name')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7" for="account_number">Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" id="account_number" class="form-control fs-7"
                               placeholder="Contoh: 1234567890" inputmode="numeric" value="{{ old('account_number') }}" required>
                        @error('account_number')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_primary" id="is_primary" value="1" @checked(old('is_primary'))>
                        <label class="form-check-label fs-7 fw-medium text-dark" for="is_primary">
                            Jadikan rekening utama penarikan dana
                        </label>
                    </div>

                    <button type="submit" class="btn btn-lokantara w-100 fw-bold py-2">
                        <i class="fa-solid fa-plus-circle me-1"></i> Daftarkan Rekening
                    </button>
                </form>
            </x-content-card>
        </div>

        <!-- Daftar Rekening Tersimpan -->
        <div class="col-lg-7">
            <x-content-card title="Daftar Rekening Bank Terdaftar">
                @if (empty($accounts) || count($accounts) === 0)
                    <div class="p-4 text-center">
                        <x-empty-state title="Belum Ada Rekening Bank"
                            description="Tambahkan rekening bank untuk memproses penarikan saldo pendapatan usaha Anda." compact />
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach ($accounts as $item)
                            @php($account = $item['model'])
                            <div class="p-3 rounded-4 border bg-white shadow-xs d-flex flex-wrap align-items-center justify-content-between gap-2"
                                 style="border-color: {{ $account->is_primary ? '#10b981' : '#e2e8f0' }} !important; background: {{ $account->is_primary ? '#f0fdf4' : '#ffffff' }};">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-grid place-items-center text-white fw-bold fs-5"
                                         style="width: 46px; height: 46px; background: linear-gradient(135deg, #065f46 0%, #10b981 100%);">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="text-dark fs-6">{{ $account->bank_code }}</strong>
                                            @if($account->is_primary)
                                                <span class="badge bg-success text-white fs-8 rounded-pill px-2.5">Utama</span>
                                            @endif
                                            <x-status-badge :status="$account->status" />
                                        </div>
                                        <div class="text-muted fs-7">
                                            <span>{{ $item['name'] }}</span> &middot; <strong class="text-dark font-monospace">{{ $item['masked'] }}</strong>
                                        </div>
                                        @if($account->status === 'pending')
                                            <small class="text-warning-emphasis d-block mt-0.5" style="font-size: 11px;">
                                                <i class="fa-solid fa-clock-rotate-left"></i> Menunggu review verifikasi tim Admin
                                            </small>
                                        @elseif($account->status === 'verified')
                                            <small class="text-success d-block mt-0.5" style="font-size: 11px;">
                                                <i class="fa-solid fa-circle-check"></i> Terverifikasi pada {{ $account->verified_at?->translatedFormat('d M Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <form method="POST" action="{{ route('mitra.bank-accounts.destroy', $account) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"
                                                onclick="return confirm('Hapus rekening bank {{ $account->bank_code }} ini?')">
                                            <i class="fa-solid fa-trash-can me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-content-card>
        </div>
    </div>
@endsection
