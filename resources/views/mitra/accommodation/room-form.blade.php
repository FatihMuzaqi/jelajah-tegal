@extends('layouts.mitra')
@section('title', 'Edit ' . $room->name) @section('page-title', 'Edit Kamar') @section('page-description',
$accommodation->name)
@section('content')
    <x-content-card title='Detail kamar'>
        <form method='POST' action='{{ route('mitra.accommodation.rooms.update', [$accommodation, $room]) }}'>@csrf
            @method('PUT')<div class='row'>
                <div class='col-md-4'><x-form-input name='name' label='Nama' :value='old('name', $room->name)' required /></div>
                <div class='col-md-4'><x-form-input name='room_type' label='Tipe kamar' :value='old('room_type', $room->room_type)' required /></div>
                <div class='col-md-4'><x-select name='kind' label='Jenis' required>
                        <option value='room' @selected($room->kind === 'room')>Kamar</option>
                        <option value='tent_plot' @selected($room->kind === 'tent_plot')>Lahan tenda</option>
                    </x-select></div>
            </div><x-textarea name='description' label='Deskripsi'>{{ old('description', $room->description) }}</x-textarea>
            <div class='row'>
                <div class='col-md-3'><x-form-input name='capacity_adults' type='number' label='Kapasitas dewasa'
                        :value='$room->capacity_adults' required /></div>
                <div class='col-md-3'><x-form-input name='capacity_children' type='number' label='Kapasitas anak'
                        :value='$room->capacity_children' required /></div>
                <div class='col-md-3'><x-form-input name='total_units' type='number' label='Total unit' :value='$room->total_units'
                        required /></div>
                <div class='col-md-3'><x-form-input name='nightly_price' type='number' label='Harga/malam'
                        :value='$room->offer->price' required /></div>
            </div>
            <div class='row'>
                <div class='col-md-3'><x-form-input name='min_stay_nights' type='number' label='Minimum malam'
                        :value='$room->min_stay_nights' /></div>
                <div class='col-md-3'><x-form-input name='max_stay_nights' type='number' label='Maksimum malam'
                        :value='$room->max_stay_nights' /></div>
                <div class='col-md-3'><x-form-input name='advance_booking_days' type='number'
                        label='Advance booking (hari)' :value='$room->advance_booking_days' /></div>
                <div class='col-md-3'><x-select name='status' label='Status'>
                        <option value='draft' @selected($room->status === 'draft')>Draft</option>
                        <option value='active' @selected($room->status === 'active')>Aktif</option>
                    </x-select></div>
            </div><x-textarea name='availability_notes'
                label='Catatan ketersediaan'>{{ old('availability_notes', $room->availability_notes) }}</x-textarea>
            <div class='mb-3'><strong>Fasilitas kamar</strong>
                <div class='d-flex flex-wrap gap-3 mt-2'>
                    @foreach ($facilities as $item)
                        <label><input type='checkbox' name='facilities[]' value='{{ $item->id }}'
                                @checked($room->facilities->contains('id', $item->id))> {{ $item->name }}</label>
                    @endforeach
                </div>
            </div><button class='btn btn-lokantara'>Simpan kamar</button>
        </form>
    </x-content-card>
    <div class='row g-3 mt-1'>
        <div class='col-lg-6'><x-content-card title='Media kamar'>
                <form method='POST' enctype='multipart/form-data'
                    action='{{ route('mitra.accommodation.rooms.media', [$accommodation, $room]) }}'>@csrf<input
                        class='form-control mb-2' type='file' name='image' accept='image/jpeg,image/png,image/webp'
                        required><select class='form-select mb-2' name='role'>
                        <option value='cover'>Cover</option>
                        <option value='gallery'>Galeri</option>
                    </select><button class='btn btn-sm btn-lokantara'>Unggah</button></form>
                <p>{{ $room->media->count() }} media kamar.</p>
            </x-content-card></div>
        <div class='col-lg-6'><x-content-card title='Tindakan'><a class='btn btn-outline-lokantara'
                    href='{{ route('mitra.accommodation.rooms.calendar', [$accommodation, $room]) }}'>Buka kalender</a>
                <form class='mt-3' method='POST'
                    action='{{ route('mitra.accommodation.rooms.archive', [$accommodation, $room]) }}'>@csrf<button
                        class='btn btn-outline-danger'>Arsipkan kamar</button></form>
            </x-content-card></div>
    </div>
@endsection
