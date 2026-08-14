@extends('layouts.app') @section('content')
    <h1>Verifikasi email</h1>
    <form method=POST action={{ route('verification.send') }}>@csrf<button>Kirim ulang</button></form>
@endsection
