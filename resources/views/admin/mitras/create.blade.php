@extends('layouts.admin')
@section('title', 'Buat Mitra') @section('page-title', 'Buat Mitra') @section('page-description', 'Buat tenant dan kirim
undangan aktivasi satu kali kepada owner.')
@section('content')
    <x-content-card title='Data Mitra dan Owner'>
        <form method='POST' action='{{ route('admin.mitras.store') }}'>
            @csrf
            <div class='row'>
                <div class='col-md-6'><x-form-input name='owner_name' label='Nama owner' required /></div>
                <div class='col-md-6'><x-form-input name='owner_email' label='Email owner' type='email' required /></div>
                <div class='col-md-6'><x-form-input name='legal_name' label='Nama legal bisnis' required /></div>
                <div class='col-md-6'><x-form-input name='display_name' label='Nama tampil' required /></div>
                <div class='col-md-6'><x-form-input name='slug' label='Slug'
                        hint='Huruf kecil, angka, dan tanda hubung.' required /></div>
                <div class='col-md-6'><x-select name='region_id' label='Lokasi'>
                        <option value=''>Pilih lokasi</option>
                        @foreach ($regions as $region)
                            <option value='{{ $region->id }}' @selected(old('region_id') == $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </x-select></div>
            </div>
            <button class='btn btn-lokantara mt-3'>Buat dan kirim undangan</button>
        </form>
    </x-content-card>
@endsection
