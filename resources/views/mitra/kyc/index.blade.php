@extends('layouts.mitra')

@section('title', 'Dokumen Legalitas & KYC')
@section('page-title', 'Dokumen Legalitas & Verifikasi Usaha (KYC)')
@section('page-description', 'Unggah dokumen legalitas izin usaha, identitas pemilik, atau bukti rekening untuk proses kurasi platform.')

@section('content')
    <div class="row g-4">
        <!-- Form Unggah Dokumen KYC -->
        <div class="col-lg-5">
            <x-content-card title="Unggah Dokumen Baru">
                <form method="POST" action="{{ route('mitra.kyc.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 13px;">Jenis Dokumen <span class="text-danger">*</span></label>
                        <select name="document_type" class="form-select" required>
                            <option value="">-- Pilih Jenis Dokumen --</option>
                            <option value="business_license">Izin Usaha / NIB / SIUP</option>
                            <option value="tax_document">Dokumen Perpajakan (NPWP)</option>
                            <option value="owner_identity">Kartu Identitas Pemilik (KTP)</option>
                            <option value="bank_proof">Buku Tabungan / Rekening Koran</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 13px;">Nomor Dokumen</label>
                        <input type="text" name="document_number" class="form-control" placeholder="Contoh: NIB-12345678 / NPWP">
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Disimpan dengan standar enkripsi aman.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 13px;">Tanggal Kedaluwarsa (Jika Ada)</label>
                        <input type="date" name="expires_on" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 13px;">File Dokumen (PDF, JPG, PNG maks 5MB) <span class="text-danger">*</span></label>
                        <input type="file" name="document" class="form-control" accept="application/pdf,image/jpeg,image/png" required>
                    </div>
                    <button class="btn btn-lokantara w-100 fw-bold rounded-pill py-2">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Kirim untuk Ditinjau
                    </button>
                </form>
            </x-content-card>
        </div>

        <!-- Tabel Riwayat Dokumen -->
        <div class="col-lg-7">
            <x-table-wrapper title="Riwayat Dokumen Legalitas">
                @if ($documents->isEmpty())
                    <tbody>
                        <tr>
                            <td><x-empty-state title="Belum ada dokumen yang diunggah" description="Unggah dokumen legalitas di samping untuk melengkapi verifikasi identitas mitra." compact /></td>
                        </tr>
                    </tbody>
                @else
                    <thead>
                        <tr>
                            <th>Jenis Dokumen</th>
                            <th>Versi</th>
                            <th>Status</th>
                            <th>Ditinjau Oleh</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr>
                                <td>
                                    <strong class="text-dark">
                                        <i class="fa-regular fa-file-pdf text-danger me-1"></i>
                                        {{ str($document->document_type)->replace('_', ' ')->headline() }}
                                    </strong>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary border">v{{ $document->version }}</span></td>
                                <td><x-status-badge :status="$document->status" /></td>
                                <td>{{ $document->reviewer?->name ?? '—' }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-lokantara rounded-pill px-3 py-1" style="font-size: 11px;"
                                       href="{{ route('mitra.kyc.download', $document) }}">
                                        <i class="fa-solid fa-download me-1"></i> Unduh File
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @endif
                <x-slot:pagination>{{ $documents->links() }}</x-slot:pagination>
            </x-table-wrapper>
        </div>
    </div>
@endsection
