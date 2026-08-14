<x-table-wrapper title='Mitra Terbaru'>
    @if ($rows->isEmpty())
        <tbody>
            <tr>
                <td><x-empty-state title='Belum ada Mitra' description='Mitra baru akan tampil setelah didaftarkan.'
                        compact /></td>
            </tr>
    </tbody>@else<thead>
            <tr>
                <th>Mitra</th>
                <th>Pemilik</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td data-label='Mitra'>{{ $row->display_name }}</td>
                    <td data-label='Pemilik'>{{ $row->owner?->name ?? '—' }}</td>
                    <td data-label='Status'><x-status-badge :status='$row->status' /></td>
                </tr>
            @endforeach
        </tbody>
    @endif
</x-table-wrapper>
