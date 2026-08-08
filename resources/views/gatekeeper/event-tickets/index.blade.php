@extends('layouts.gatekeeper')
@section('title', 'Validasi tiket')
@section('page-title', 'Validasi QR Ticket')
@section('page-description', 'Token hanya dapat digunakan satu kali pada Mitra aktif.')
@section('content')
<form class="content-card" method="POST" action="{{ route('gatekeeper.tickets.validate') }}">
    @csrf
    <label class="form-label">Token QR</label>
    <input class="form-control" name="token" required autofocus>
    <input class="form-control mt-2" name="device_reference" placeholder="Referensi perangkat (opsional)">
    <button class="btn btn-lokantara mt-3">Validasi</button>
</form>
@endsection
