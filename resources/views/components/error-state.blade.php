@props(['title' => 'Terjadi kesalahan', 'description' => 'Data belum dapat dimuat. Silakan coba kembali.'])
<div class='state-page error-state' role='alert'>
    <div class='state-code'>!</div>
    <h2>{{ $title }}</h2>
    <p>{{ $description }}</p><button class='btn btn-lokantara' onclick='location.reload()'>Coba lagi</button>
</div>
