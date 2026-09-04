<x-table-wrapper title="Pendaftaran Mitra Terbaru">
    @if ($rows->isEmpty())
        <tbody>
            <tr>
                <td colspan="4">
                    <x-empty-state title="Belum ada Mitra Terdaftar" description="Mitra baru yang mendaftar akan tampil otomatis di sini." compact />
                </td>
            </tr>
        </tbody>
    @else
        <thead>
            <tr>
                <th>Nama Mitra &amp; Kategori</th>
                <th>Pemilik (Owner)</th>
                <th>Status</th>
                <th class="text-end pe-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td data-label="Mitra">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-8 flex-shrink-0" style="width: 32px; height: 32px;">
                                {{ strtoupper(substr($row->display_name ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <a href="{{ route('admin.mitras.show', $row) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block fs-8">
                                    {{ $row->display_name }}
                                </a>
                                <small class="text-muted fs-9">
                                    {{ $row->isDinas() ? 'Dinas (Pemerintah)' : 'Swasta / Umum' }}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td data-label="Pemilik">
                        <span class="text-dark fs-8 fw-medium d-block">{{ $row->owner?->name ?? '—' }}</span>
                        <small class="text-muted fs-9">{{ $row->owner?->email ?? '' }}</small>
                    </td>
                    <td data-label="Status">
                        <x-status-badge :status="$row->status" />
                    </td>
                    <td data-label="Aksi" class="text-end pe-3">
                        <a href="{{ route('admin.mitras.show', $row) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 fs-9 fw-semibold text-secondary d-inline-flex align-items-center gap-1">
                            <span>Buka</span>
                            <i class="fa-solid fa-arrow-right fs-9"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    @endif
</x-table-wrapper>
