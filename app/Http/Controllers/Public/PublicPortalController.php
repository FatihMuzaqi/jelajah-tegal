<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSearchRequest;
use App\Models\ApplicationSetting;
use App\Models\CatalogEntity;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\Mitra;
use App\Models\Region;
use App\Models\ServiceType;
use App\Support\FeatureFlags;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicPortalController extends Controller
{
    public function __construct(private readonly FeatureFlags $flags) {}

    public function home(PublicSearchRequest $request): View
    {
        $filters = $request->validated();
        $directoryEnabled = $this->flags->enabled('public-mitra-directory');

        $mitras = Mitra::query()
            ->publiclyVisible()
            ->select(['id', 'display_name', 'slug', 'description', 'region_id'])
            ->with([
                'region:id,name',
                'features' => fn ($query) => $query->where('status', 'enabled')
                    ->select(['id', 'mitra_id', 'service_type_id', 'status'])
                    ->with('serviceType:id,code,name'),
            ])
            ->when(! $directoryEnabled, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filters['q'] ?? null, function ($query, string $term) {
                $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($term)).'%';
                $query->where(function ($nested) use ($term) {
                    $nested->where('display_name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('region', fn ($region) => $region->where('name', 'like', $term));
                });
            })
            ->when($filters['region'] ?? null, fn ($query, $region) => $query->where('region_id', $region))
            ->when($filters['service'] ?? null, fn ($query, $service) => $query->whereHas(
                'features',
                fn ($feature) => $feature->where('status', 'enabled')->whereHas('serviceType', fn ($type) => $type->where('code', $service))
            ))
            ->orderBy('display_name')
            ->paginate(9)
            ->withQueryString();

        $services = Cache::remember('home_services', 300, fn() => ServiceType::query()->orderBy('sort_order')->get(['id', 'code', 'name']));
        $categories = Cache::remember('home_categories', 300, fn() => Category::query()->where('is_active', true)->with('serviceType:id,code,name')->orderBy('name')->get(['id', 'service_type_id', 'name', 'slug']));
        $allRegions = Cache::remember('home_all_regions', 300, fn() => Region::orderBy('name')->get(['id', 'name', 'code']));
        
        $stats = Cache::remember('home_stats', 300, function() {
            $visibleMitraQuery = Mitra::query()->publiclyVisible();
            return [
                ['label' => 'Mitra aktif', 'value' => (clone $visibleMitraQuery)->count()],
                ['label' => 'Lokasi tersedia', 'value' => (clone $visibleMitraQuery)->whereNotNull('region_id')->distinct()->count('region_id')],
                ['label' => 'Layanan aktif', 'value' => ServiceType::query()->whereHas('categories', fn ($query) => $query->where('is_active', true))->count()],
            ];
        });

        $featuredTourisms = CatalogEntity::publicTourism()
            ->with(['region', 'category', 'media', 'tourism', 'mitra', 'offers'])
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(6)
            ->get();

        $featuredAccommodations = CatalogEntity::publicAccommodation()
            ->with(['region', 'category', 'accommodation.rooms.offer', 'media', 'mitra'])
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(4)
            ->get();

        $popularMitras = Mitra::query()
            ->publiclyVisible()
            ->with([
                'region:id,name',
                'features' => fn ($query) => $query->where('status', 'enabled')->with('serviceType:id,code,name'),
                'logoMedia',
                'bannerMedia',
            ])
            ->withCount('catalogEntities')
            ->orderByDesc('catalog_entities_count')
            ->orderBy('display_name')
            ->limit(6)
            ->get();

        return view('public.home', [
            'mitras' => $mitras,
            'popularMitras' => $popularMitras,
            'services' => $services,
            'categories' => $categories,
            'regions' => $allRegions,
            'stats' => $stats,
            'filters' => $filters,
            'domains' => config('public-portal.domains'),
            'aiPlannerEnabled' => $this->flags->enabled('public-ai-planner'),
            'newsletterEnabled' => $this->flags->enabled('public-newsletter'),
            'faq' => $this->publishedSetting('public.faq'),
            'featuredTourisms' => $featuredTourisms,
            'featuredAccommodations' => $featuredAccommodations,
        ]);
    }

    public function about(): View
    {
        $stats = [
            'tourism_count' => CatalogEntity::whereHas('serviceType', fn ($q) => $q->where('code', 'tourism'))->where('status', 'published')->count(),
            'accommodation_count' => CatalogEntity::whereHas('serviceType', fn ($q) => $q->where('code', 'accommodation'))->where('status', 'published')->count(),
            'culinary_count' => CatalogEntity::whereHas('serviceType', fn ($q) => $q->where('code', 'culinary'))->where('status', 'published')->count(),
            'mitra_count' => Mitra::where('status', 'active')->where('is_verified', true)->count(),
        ];

        return view('public.about', compact('stats'));
    }

    public function faq(): View
    {
        $faqCategories = [
            [
                'name' => 'Umum & Informasi Wisata',
                'icon' => 'fa-compass',
                'items' => [
                    [
                        'q' => 'Apa itu platform Jelajah Tegal?',
                        'a' => 'Jelajah Tegal adalah portal ekosistem digital pariwisata terpadu Kabupaten Tegal yang menyediakan informasi lengkap destinasi wisata, reservasi penginapan, panduan kuliner khas, jadwal event, dan rental transportasi lokal dalam satu pintu.',
                    ],
                    [
                        'q' => 'Apakah seluruh destinasi dan akomodasi di sini terpercaya?',
                        'a' => 'Ya, seluruh Mitra terdaftar telah melalui proses verifikasi resmi dari dinas terkait dan tim kurasi kami demi memastikan data akurat, legalitas terjamin, dan keamanan wisatawan.',
                    ],
                    [
                        'q' => 'Apakah saya harus memiliki akun untuk menjelajahi Jelajah Tegal?',
                        'a' => 'Tidak. Seluruh informasi katalog wisata, kuliner, dan penginapan dapat diakses secara publik dan gratis. Akun pengguna hanya diperlukan saat Anda ingin melakukan reservasi, pembelian tiket, atau memberikan ulasan.',
                    ],
                ],
            ],
            [
                'name' => 'Tiket & Pembayaran Online',
                'icon' => 'fa-ticket',
                'items' => [
                    [
                        'q' => 'Bagaimana cara membeli tiket wisata secara online?',
                        'a' => 'Pilih destinasi wisata atau event yang Anda tuju, tentukan tanggal kunjungan dan jumlah tiket, lalu klik tombol Pesan Sekarang. Anda dapat menyelesaikan pembayaran secara instan menggunakan QRIS, Virtual Account bank, maupun e-wallet.',
                    ],
                    [
                        'q' => 'Bagaimana bentuk tiket yang akan saya terima?',
                        'a' => 'Setelah pembayaran berhasil, Anda akan menerima tiket digital berformat QR Code yang dapat langsung ditunjukkan ke petugas loket pintu masuk untuk dipindai (scan). Tiket juga tersimpan rapi di dashboard profil Anda.',
                    ],
                    [
                        'q' => 'Apakah tiket yang sudah dibeli dapat dibatalkan (refund)?',
                        'a' => 'Kebijakan pengembalian dana mengikuti ketentuan masing-masing pengelola objek wisata atau penyelenggara event yang tertera pada rincian saat pemesanan.',
                    ],
                ],
            ],
            [
                'name' => 'Pendaftaran Mitra & Pelaku Usaha',
                'icon' => 'fa-handshake',
                'items' => [
                    [
                        'q' => 'Bagaimana cara mendaftarkan usaha saya sebagai Mitra resmi?',
                        'a' => 'Klik menu "Daftar Jadi Mitra" di bagian navigasi atas, lengkapi profil usaha dan dokumen identitas pendukung. Tim admin akan memverifikasi data Anda dalam waktu 1x24 jam kerja.',
                    ],
                    [
                        'q' => 'Layanan apa saja yang bisa didaftarkan oleh Mitra?',
                        'a' => 'Mitra dapat mendaftarkan usaha di bidang Objek Wisata, Penginapan (Hotel/Villa/Homestay/Glamping), Rumah Makan & Kuliner Tradisional, Event Organizer, maupun Rental Kendaraan.',
                    ],
                ],
            ],
            [
                'name' => 'Fitur AI & Kotak Saran',
                'icon' => 'fa-wand-magic-sparkles',
                'items' => [
                    [
                        'q' => 'Bagaimana cara menggunakan Asisten AI Pintar Jelajah Tegal?',
                        'a' => 'Klik tombol "Tanya Asisten AI" di pojok kanan bawah layar kapan saja untuk bertanya mengenai rekomendasi objek wisata, estimasi bujet liburan, maupun jadwal kuliner terbaik di Tegal.',
                    ],
                    [
                        'q' => 'Bagaimana cara menyampaikan saran, kritik, atau masukan untuk platform ini?',
                        'a' => 'Anda dapat langsung mengisi formulir "Kotak Saran & Kritik" pada bagian bawah halaman FAQ ini. Tim kami senantiasa meninjau setiap masukan demi peningkatan kualitas layanan.',
                    ],
                ],
            ],
        ];

        return view('public.faq', compact('faqCategories'));
    }

    public function storeFeedback(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:saran,kritik,pertanyaan,apresiasi'],
            'category' => ['required', 'string', 'max:64'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:32'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        Feedback::create([
            'user_id' => $request->user()?->id,
            'type' => $validated['type'],
            'category' => $validated['category'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return redirect()->route('public.faq', ['#kotak-saran'])->with('feedback_success', 'Terima kasih! Saran atau kritik Anda telah berhasil kami terima dan akan segera ditinjau oleh tim Jelajah Tegal.');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function privacy(): View
    {
        return $this->contentPage('Kebijakan Privasi', 'public.privacy', 'Kebijakan privasi belum diterbitkan. Jangan memproses data publik sebelum dokumen ini disetujui.');
    }

    public function terms(): View
    {
        return $this->contentPage('Syarat dan Ketentuan', 'public.terms', 'Syarat dan ketentuan belum diterbitkan.');
    }

    private function contentPage(string $title, string $key, string $emptyMessage): View
    {
        return view('public.content-page', [
            'title' => $title,
            'content' => $this->publishedSetting($key),
            'emptyMessage' => $emptyMessage,
        ]);
    }

    private function publishedSetting(string $key): ?array
    {
        $setting = ApplicationSetting::query()
            ->whereNull('mitra_id')
            ->where('key_name', $key)
            ->where('is_secret', false)
            ->first(['value_json']);
        $content = $setting?->value_json;

        return is_array($content) && ($content['published'] ?? false) === true ? $content : null;
    }
}
