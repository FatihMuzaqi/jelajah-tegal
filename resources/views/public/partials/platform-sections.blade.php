<section class='public-section stats-section' aria-labelledby='stats-heading'>
 <div class='container public-container'><div class='section-heading'><div><p class='public-eyebrow'>Data platform</p><h2 id='stats-heading'>Angka aktual hari ini</h2></div></div><div class='platform-stats'>@foreach($stats as $stat)<div><strong>{{ number_format($stat['value'],0,',','.') }}</strong><span>{{ $stat['label'] }}</span></div>@endforeach</div></div>
</section>

<section class='public-section benefits-section'>
 <div class='container public-container'><div class='section-heading centered'><div><p class='public-eyebrow'>Dirancang untuk kejelasan</p><h2>Temukan layanan dengan informasi yang bertanggung jawab</h2></div></div><div class='benefit-grid'><article><span>01</span><h3>Konten terkontrol</h3><p>Hanya data dengan lifecycle publikasi yang sesuai yang dapat masuk katalog publik.</p></article><article><span>02</span><h3>Tenant terisolasi</h3><p>Data operasional setiap Mitra dipisahkan melalui active Mitra context dan policy.</p></article><article><span>03</span><h3>Akses dinamis</h3><p>Hak akses dikelola melalui permission database, bukan mengandalkan nama role semata.</p></article></div></div>
</section>

<section class='public-section'>
 <div class='container public-container'><div class='partner-cta'><div><p class='public-eyebrow'>Untuk pelaku lokal</p><h2>Kelola layanan Anda sebagai Mitra Lokantara.</h2><p>Pendaftaran akun tidak otomatis membuat Mitra. Aktivasi bisnis tetap melalui proses persetujuan platform.</p></div><a class='btn btn-lokantara' href='{{ route('register') }}'>Buat akun</a></div></div>
</section>

<section class='public-section faq-section' aria-labelledby='faq-heading'>
 <div class='container public-container'><div class='section-heading'><div><p class='public-eyebrow'>FAQ</p><h2 id='faq-heading'>Pertanyaan yang sering diajukan</h2></div><a href='{{ route('public.faq') }}'>Lihat halaman FAQ</a></div>
  @if(! $faq || empty($faq['items']))
   <x-empty-state title='FAQ belum diterbitkan' description='Jawaban resmi akan tampil setelah konten disetujui.' />
  @else
   <div class='accordion public-accordion' id='landing-faq'>@foreach($faq['items'] as $item)<div class='accordion-item'><h3 class='accordion-header'><button class='accordion-button @if(! $loop->first) collapsed @endif' type='button' data-bs-toggle='collapse' data-bs-target='#faq-{{ $loop->index }}'>{{ $item['question'] ?? '' }}</button></h3><div id='faq-{{ $loop->index }}' class='accordion-collapse collapse @if($loop->first) show @endif' data-bs-parent='#landing-faq'><div class='accordion-body'>{{ $item['answer'] ?? '' }}</div></div></div>@endforeach</div>
  @endif
 </div>
</section>

@if($newsletterEnabled)
<section class='public-section'><div class='container public-container'><div class='newsletter-card'><div><h2>Dapatkan pembaruan Lokantara</h2><p>Pendaftaran newsletter tersedia sesuai consent dan konfigurasi platform.</p></div><a class='btn btn-lokantara' href='{{ route('public.contact') }}'>Kelola langganan</a></div></div></section>
@endif
