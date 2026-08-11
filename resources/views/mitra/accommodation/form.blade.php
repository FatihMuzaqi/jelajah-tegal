@extends('layouts.mitra')

@php($editing = isset($accommodation))

@section('title', $editing ? 'Edit Penginapan' : 'Tambah Penginapan')
@section('page-title', $editing ? 'Edit Penginapan' : 'Tambah Penginapan')
@section('page-description', 'Data disimpan sebagai draft sampai lolos moderasi.')

@section('content')
    <x-content-card title="Informasi properti">
        <form method="POST"
            action="{{ $editing ? route('mitra.accommodation.update', $accommodation) : route('mitra.accommodation.store') }}">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-8">
                    <x-form-input name="name" label="Nama properti" :value="old('name', $accommodation->name ?? '')" required />
                </div>
                <div class="col-md-4">
                    <x-form-input name="slug" label="Slug" :value="old('slug', $accommodation->slug ?? '')" required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <x-select name="property_type" label="Tipe properti" required>
                        @foreach (['hotel' => 'Hotel', 'homestay' => 'Homestay', 'villa' => 'Villa', 'camping_ground' => 'Camping Ground', 'resort' => 'Resort'] as $key => $label)
                            <option value="{{ $key }}" @selected(old('property_type', $accommodation->accommodation->property_type ?? '') === $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </x-select>
                </div>
                <div class="col-md-4">
                    <x-select name="category_id" label="Kategori" required>
                        <option value="">Pilih</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}" @selected(old('category_id', $accommodation->category_id ?? null) == $item->id)>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </x-select>
                </div>
                <div class="col-md-4">
                    <x-select name="region_id" label="Wilayah" required>
                        <option value="">Pilih</option>
                        @foreach ($regions as $item)
                            <option value="{{ $item->id }}" @selected(old('region_id', $accommodation->region_id ?? null) == $item->id)>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <x-textarea name="description"
                label="Deskripsi">{{ old('description', $accommodation->description ?? '') }}</x-textarea>
            <x-textarea name="address" label="Alamat">{{ old('address', $accommodation->address ?? '') }}</x-textarea>

            <div class="row">
                <div class="col-md-3">
                    <x-form-input name="check_in_time" type="time" label="Check-in" :value="old('check_in_time', $accommodation->accommodation->check_in_time ?? '')" />
                </div>
                <div class="col-md-3">
                    <x-form-input name="check_out_time" type="time" label="Check-out" :value="old('check_out_time', $accommodation->accommodation->check_out_time ?? '')" />
                </div>
                <div class="col-md-3">
                    <x-form-input name="star_rating" type="number" label="Bintang" :value="old('star_rating', $accommodation->accommodation->star_rating ?? '')" />
                </div>
                <div class="col-md-3">
                    <label class="mt-4">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $accommodation->is_featured ?? false))> Featured
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-form-input name="latitude" label="Latitude" :value="old('latitude', $accommodation->location->latitude ?? '')" required />
                </div>
                <div class="col-md-6">
                    <x-form-input name="longitude" label="Longitude" :value="old('longitude', $accommodation->location->longitude ?? '')" required />
                </div>
            </div>

            <div class="mb-3">
                <strong>Fasilitas properti</strong>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    @foreach ($facilities as $item)
                        <label>
                            <input type="checkbox" name="facilities[]" value="{{ $item->id }}"
                                @checked(in_array($item->id, old('facilities', $editing ? $accommodation->facilities->pluck('id')->all() : [])))>
                            {{ $item->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="btn btn-lokantara">Simpan draft</button>
        </form>
    </x-content-card>
@endsection
