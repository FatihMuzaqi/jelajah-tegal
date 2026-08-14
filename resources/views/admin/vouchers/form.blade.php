@extends('layouts.admin') @section('title', 'Buat voucher') @section('page-title', 'Buat Voucher Platform')
@section('page-description', 'Nilai persentase menggunakan basis points: 1000 = 10%.') @section('content')<form
    class="content-card" method="POST" action="{{ route('admin.vouchers.store') }}">@csrf
@include('shared.voucher-fields')<button class="btn btn-lokantara mt-3">Simpan</button></form>@endsection
