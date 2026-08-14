@props(['title' => 'Akses ditolak', 'description' => 'Anda tidak memiliki permission untuk membuka halaman ini.'])
<div class='state-page forbidden-state'>
    <div class='state-code'>403</div>
    <h1>{{ $title }}</h1>
    <p>{{ $description }}</p><a class='btn btn-lokantara' href='{{ route('post-login') }}'>Kembali ke dashboard</a>
</div>
