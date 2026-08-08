@extends('layouts.app') @section('content')<h1>Verifikasi MFA</h1><form method=POST>@csrf<input name=code placeholder=Kode><button>Verifikasi</button></form>@endsection
