@extends('layouts.mitra')
@php($editing = isset($tourism))
@section('title', $editing ? 'Edit Wisata' : 'Tambah Wisata') @section('page-title', $editing ? 'Edit Wisata' : 'Tambah
Wisata') @section('page-description', 'Simpan sebagai draft sebelum diajukan ke Admin.')
@section('content')
    <x-content-card title='Informasi destinasi'>
        <form method='POST' action='{{ $editing ? route('mitra.tourism.update', $tourism) : route('mitra.tourism.store') }}'>
            @csrf @if ($editing)
                @method('PUT')
            @endif
            <div class='row'>
                <div class='col-md-8'><x-form-input name='name' label='Nama' :value='old('name', $tourism->name ?? '')' required /></div>
                <div class='col-md-4'><x-form-input name='slug' label='Slug' :value='old('slug', $tourism->slug ?? '')' required /></div>
            </div>
            <div class='row'>
                <div class='col-md-6'><x-select name='category_id' label='Kategori' required>
                        <option value=''>Pilih</option>
                        @foreach ($categories as $item)
                            <option value='{{ $item->id }}' @selected(old('category_id', $tourism->category_id ?? null) == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class='col-md-6'><x-select name='region_id' label='Wilayah' required>
                        <option value=''>Pilih</option>
                        @foreach ($regions as $item)
                            <option value='{{ $item->id }}' @selected(old('region_id', $tourism->region_id ?? null) == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>
            <x-textarea name='description'
                label='Deskripsi'>{{ old('description', $tourism->description ?? '') }}</x-textarea><x-textarea name='address'
                label='Alamat'>{{ old('address', $tourism->address ?? '') }}</x-textarea>
            <div class='row'>
                <div class='col-md-4'><x-select name='destination_type' label='Tipe' required>
                        @foreach (['nature' => 'Alam', 'culture' => 'Budaya', 'recreation' => 'Rekreasi', 'education' => 'Edukasi', 'religious' => 'Religi', 'other' => 'Lainnya'] as $key => $label)
                            <option value='{{ $key }}' @selected(old('destination_type', $tourism->tourism->destination_type ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class='col-md-4'><x-form-input name='visit_duration_minutes' type='number'
                        label='Durasi kunjungan (menit)' :value='old('visit_duration_minutes', $tourism->tourism->visit_duration_minutes ?? '')' /></div>
                <div class='col-md-4'><x-form-input name='badge' label='Badge' :value='old('badge', $tourism->tourism->badge ?? '')' /></div>
            </div>
            <div class='row'>
                <div class='col-md-6'><x-form-input name='latitude' label='Latitude' :value='old('latitude', $tourism->location->latitude ?? '')' required /></div>
                <div class='col-md-6'><x-form-input name='longitude' label='Longitude' :value='old('longitude', $tourism->location->longitude ?? '')' required /></div>
            </div>
            <div class='mb-3'><strong>Fasilitas</strong>
                <div class='d-flex flex-wrap gap-3 mt-2'>
                    @foreach ($facilities as $item)
                        <label><input type='checkbox' name='facilities[]' value='{{ $item->id }}'
                                @checked(in_array($item->id, old('facilities', $editing ? $tourism->facilities->pluck('id')->all() : [])))> {{ $item->name }}</label>
                    @endforeach
                </div>
            </div>
            <label class='me-3'><input type='checkbox' name='is_hidden_gem' value='1' @checked(old('is_hidden_gem', $tourism->tourism->is_hidden_gem ?? false))>
                Hidden gem</label><label><input type='checkbox' name='is_featured' value='1'
                    @checked(old('is_featured', $tourism->is_featured ?? false))> Featured</label>
            <div class='mt-3'><button class='btn btn-lokantara'>Simpan draft</button></div>
        </form>
    </x-content-card>
@endsection
