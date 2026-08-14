@extends('layouts.admin') @section('title', $title) @section('page-title', $title) @section('page-description', 'Antrean katalog
dan ulasan.') @section('content')<x-table-wrapper title="Antrean">
        @if ($items->isEmpty())
            <tbody>
                <tr>
                    <td><x-empty-state title="Antrean kosong" compact /></td>
                </tr>
        </tbody>@else<tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td><x-status-badge :status="$item->status" /></td>
                        <td><a href="{{ route($routePrefix . '.show', $item) }}">Tinjau</a></td>
                    </tr>
                @endforeach
            </tbody>
        @endif
</x-table-wrapper>@endsection
