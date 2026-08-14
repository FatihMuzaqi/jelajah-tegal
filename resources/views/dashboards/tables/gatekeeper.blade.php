<x-table-wrapper title='Assignment Saya'>
    @if ($rows->isEmpty())
        <tbody>
            <tr>
                <td><x-empty-state title='Belum ada assignment'
                        description='Assignment validasi akan tampil setelah diberikan.' compact /></td>
            </tr>
    </tbody>@else<thead>
            <tr>
                <th>Scope</th>
                <th>Berlaku mulai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                @php($status = $row->revoked_at ? 'revoked' : 'active')
                <tr>
                    <td data-label='Scope'>{{ str($row->scope_type)->headline() }}</td>
                    <td data-label='Berlaku'>{{ $row->valid_from?->format('d M Y H:i') ?? 'Sekarang' }}</td>
                    <td data-label='Status'><x-status-badge :status='$status' /></td>
                </tr>
            @endforeach
        </tbody>
    @endif
</x-table-wrapper>
