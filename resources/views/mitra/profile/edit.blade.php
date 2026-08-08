@extends('layouts.mitra')
@section('title','Profil Mitra') @section('page-title','Profil Mitra') @section('page-description','Identitas bisnis, lokasi, media, dan jam operasional tenant aktif.')
@section('content')
<div class='dashboard-grid'>
 <x-content-card title='Informasi bisnis'><form method='POST' action='{{ route('mitra.profile.update') }}'>@csrf @method('PUT')
  <x-form-input name='display_name' label='Nama bisnis' :value='$mitra->display_name' required />
  <x-textarea name='description' label='Deskripsi'>{{ old('description',$mitra->description) }}</x-textarea>
  <div class='row'><div class='col-md-6'><x-form-input name='contact_email' label='Email kontak' type='email' :value='$mitra->contact_email' /></div><div class='col-md-6'><x-form-input name='contact_phone' label='Telepon' :value='$mitra->contact_phone' /></div></div>
  <x-select name='region_id' label='Lokasi'><option value=''>Pilih lokasi</option>@foreach($regions as $region)<option value='{{ $region->id }}' @selected(old('region_id',$mitra->region_id)==$region->id)>{{ $region->name }}</option>@endforeach</x-select>
  <x-textarea name='address' label='Alamat'>{{ old('address',$mitra->address) }}</x-textarea>
  <button class='btn btn-lokantara'>Simpan profil</button>
 </form></x-content-card>
 <div>
  <x-content-card title='Status bisnis'><dl class='profile-summary'><div><dt>Status</dt><dd><x-status-badge :status='$mitra->status' /></dd></div><div><dt>Nama legal</dt><dd>{{ $mitra->legal_name }}</dd></div><div><dt>Komisi</dt><dd>{{ data_get($commission,'rate') !== null ? data_get($commission,'rate').'%' : 'Belum dikonfigurasi' }}</dd></div><div><dt>Fitur aktif</dt><dd>{{ $mitra->features->where('status','enabled')->count() }}</dd></div></dl><a href='{{ route('mitra.kyc.index') }}'>Kelola dokumen legal</a></x-content-card>
  <x-content-card title='Logo dan banner' class='mt-3'><form method='POST' action='{{ route('mitra.profile.media','logo') }}' enctype='multipart/form-data'>@csrf<x-file-uploader name='image' label='Unggah logo' accept='image/jpeg,image/png,image/webp' /><button class='btn btn-lokantara mt-2'>Simpan logo</button></form><hr><form method='POST' action='{{ route('mitra.profile.media','banner') }}' enctype='multipart/form-data'>@csrf<x-file-uploader name='image' label='Unggah banner' accept='image/jpeg,image/png,image/webp' /><button class='btn btn-lokantara mt-2'>Simpan banner</button></form></x-content-card>
 </div>
</div>
<x-content-card title='Jam operasional' class='mt-3'><form method='POST' action='{{ route('mitra.profile.hours') }}'>@csrf @method('PUT')
 @php($dayNames=['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'])
 <div class='hours-grid'>@foreach($dayNames as $day=>$name) @php($hour=$mitra->operatingHours->firstWhere('day_of_week',$day))<div class='hour-row'><input type='hidden' name='hours[{{ $day }}][day_of_week]' value='{{ $day }}'><strong>{{ $name }}</strong><label><input type='checkbox' name='hours[{{ $day }}][is_closed]' value='1' @checked(old('hours.'.$day.'.is_closed',$hour?->is_closed))> Tutup</label><input class='form-control' type='time' name='hours[{{ $day }}][opens_at]' value='{{ old('hours.'.$day.'.opens_at',$hour?->opens_at) }}'><input class='form-control' type='time' name='hours[{{ $day }}][closes_at]' value='{{ old('hours.'.$day.'.closes_at',$hour?->closes_at) }}'></div>@endforeach</div>
 <button class='btn btn-lokantara mt-3'>Simpan jam operasional</button>
</form></x-content-card>
@endsection
