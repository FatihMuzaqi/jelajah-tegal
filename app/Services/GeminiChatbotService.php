<?php

namespace App\Services;

use App\Models\CatalogEntity;
use App\Models\ChatbotSetting;
use App\Models\Mitra;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiChatbotService
{
    /**
     * Kirim pesan pengguna ke Gemini AI dan dapatkan balasan cerdas.
     */
    public function ask(string $message, array $history = []): string
    {
        $setting = ChatbotSetting::current();

        if (! $setting->is_enabled) {
            return "Mohon maaf, layanan Asisten Wisata Jelajah Tegal saat ini sedang dinonaktifkan untuk pemeliharaan berkala.";
        }

        $apiKey = $setting->api_key ?: config('services.gemini.api_key', '');
        $model = $setting->model ?: config('services.gemini.model', 'gemini-3.5-flash');
        $baseUrl = $setting->base_url ?: config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');

        $knowledge = $this->getKnowledgeBase();
        $systemPrompt = $this->buildSystemPrompt($knowledge, $setting->system_prompt_addition);

        if (empty($apiKey)) {
            return $this->getFallbackResponse($message, $knowledge);
        }

        try {
            $formattedContents = [];

            // Sisipkan System Prompt sebagai instruksi utama
            $formattedContents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => "Instruksi Sistem & Pengetahuan Platform:\n" . $systemPrompt . "\n\nMohon pahami instruksi di atas."]
                ]
            ];
            $formattedContents[] = [
                'role' => 'model',
                'parts' => [
                    ['text' => "Halo! Saya adalah Asisten Wisata Cerdas Jelajah Tegal. Saya siap membantu Anda dengan informasi lengkap mengenai lokasi/alamat, jadwal buka/tutup, harga tiket & stok kuota, fasilitas, check-in/out penginapan, menu kuliner, sewa rental kendaraan, serta rekomendasi wisata terbaik di Tegal! Ada yang bisa saya bantu?"]
                ]
            ];

            // Tambahkan riwayat percakapan sebelumnya (maksimal 6 percakapan terakhir)
            $recentHistory = array_slice($history, -6);
            foreach ($recentHistory as $chat) {
                if (!empty($chat['user'])) {
                    $formattedContents[] = [
                        'role' => 'user',
                        'parts' => [['text' => $chat['user']]]
                    ];
                }
                if (!empty($chat['bot'])) {
                    $formattedContents[] = [
                        'role' => 'model',
                        'parts' => [['text' => $chat['bot']]]
                    ];
                }
            }

            // Tambahkan pesan user saat ini
            $formattedContents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];

            $endpoint = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

            $generationConfig = [
                'temperature' => (float) ($setting->temperature ?? 0.70),
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => max((int) ($setting->max_tokens ?? 2000), 1500),
            ];

            // Matikan thinking token overhead untuk model versi 3.x/2.5 agar output tidak terpotong
            if (str_contains($model, '3.') || str_contains($model, '2.5')) {
                $generationConfig['thinkingConfig'] = [
                    'thinkingBudget' => 0,
                ];
            }

            $response = Http::withoutVerifying()->timeout(15)->post($endpoint, [
                'contents' => $formattedContents,
                'generationConfig' => $generationConfig,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $parts = $data['candidates'][0]['content']['parts'] ?? [];
                $reply = '';
                foreach ($parts as $part) {
                    if (!empty($part['text'])) {
                        $reply .= $part['text'];
                    }
                }
                if (!empty(trim($reply))) {
                    return trim($reply);
                }
            }

            Log::warning('Gemini API Error Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->getFallbackResponse($message, $knowledge);
        } catch (\Throwable $e) {
            Log::error('Gemini Chatbot Exception', ['error' => $e->getMessage()]);
            return $this->getFallbackResponse($message, $knowledge);
        }
    }

    /**
     * Uji coba koneksi API langsung dari dashboard Super Admin.
     */
    public function testConnection(?string $apiKey = null, ?string $model = null, ?string $baseUrl = null): array
    {
        $setting = ChatbotSetting::current();
        $apiKey = $apiKey ?: ($setting->api_key ?: config('services.gemini.api_key', ''));
        $model = $model ?: ($setting->model ?: config('services.gemini.model', 'gemini-3.5-flash'));
        $baseUrl = $baseUrl ?: ($setting->base_url ?: config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'));

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API Key belum diisi. Silakan masukkan Google Gemini API Key.',
                'status_code' => 400,
            ];
        }

        $startTime = microtime(true);

        try {
            $endpoint = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";
            $response = Http::withoutVerifying()->timeout(12)->post($endpoint, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => 'Halo! Jawab dalam 1 kalimat singkat: status sistem normal.']]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 100,
                ],
            ]);

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $reply = $response->json('candidates.0.content.parts.0.text', 'Koneksi berhasil.');
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Gemini AI (' . $model . ') berhasil!',
                    'sample_reply' => trim($reply),
                    'latency_ms' => $latency,
                    'status_code' => $response->status(),
                ];
            }

            $errorData = $response->json('error', []);
            $errMsg = $errorData['message'] ?? $response->body();

            return [
                'success' => false,
                'message' => "Koneksi gagal ({$response->status()}): {$errMsg}",
                'status_code' => $response->status(),
                'latency_ms' => $latency,
            ];
        } catch (\Throwable $e) {
            $latency = round((microtime(true) - $startTime) * 1000);
            return [
                'success' => false,
                'message' => "Kesalahan jaringan: {$e->getMessage()}",
                'status_code' => 500,
                'latency_ms' => $latency,
            ];
        }
    }

    /**
     * Membangun system prompt persona asisten wisata Jelajah Tegal dengan detail komprehensif.
     */
    protected function buildSystemPrompt(array $knowledge, ?string $customAddition = null): string
    {
        $wisataText = empty($knowledge['wisata_texts']) ? 'Belum ada data destinasi wisata tambahan di database.' : implode("\n\n", $knowledge['wisata_texts']);
        $penginapanText = empty($knowledge['penginapan_texts']) ? 'Belum ada data penginapan tambahan di database.' : implode("\n\n", $knowledge['penginapan_texts']);
        $kulinerText = empty($knowledge['kuliner_texts']) ? 'Belum ada data tempat kuliner tambahan di database.' : implode("\n\n", $knowledge['kuliner_texts']);
        $eventText = empty($knowledge['event_texts']) ? 'Belum ada data event tambahan di database.' : implode("\n\n", $knowledge['event_texts']);
        $rentalText = empty($knowledge['rental_texts']) ? 'Belum ada data rental kendaraan tambahan di database.' : implode("\n\n", $knowledge['rental_texts']);
        $mitraText = empty($knowledge['mitra_texts']) ? 'Belum ada data mitra terdaftar.' : implode("\n", $knowledge['mitra_texts']);
        $tegalMasterText = $this->getTegalMasterKnowledgeText();

        $customSection = $customAddition ? "\n\nINSTRUKSI & PENGUMUMAN KHUSUS DARI PENGELOLA:\n" . $customAddition : '';

        return <<<PROMPT
Anda adalah "Asisten Wisata Cerdas Jelajah Tegal", pemandu virtual resmi, cerdas, ramah, dan solutif untuk platform pariwisata terpadu Jelajah Tegal (Kabupaten Tegal, Jawa Tengah).

TUGAS DAN ATURAN UTAMA ANDA:
1. Jawab pertanyaan pengguna dengan ramah, hangat, antusias, solutif, dan menggunakan Bahasa Indonesia yang santun.
2. ANDA MEMILIKI AKSES LENGKAP KE DATA DETAIL SEMUA DESTINASI (Wisata, Kuliner, Penginapan, Event, Rental Kendaraan, dan Mitra):
   - **LOKASI / ALAMAT**: Jika pengguna bertanya "dimana lokasi [tempat]" atau "alamat [tempat]", berikan alamat jalan lengkap, desa/kelurahan, kecamatan, patokan/landmark wilayah di Tegal secara jelas dan akurat! (Contoh: "Kawasan Wisata Guci berlokasi di Jl. Objek Wisata Guci, Kalisiwi, Desa Guci, Kecamatan Bumijawa, Kabupaten Tegal...").
   - **JADWAL / JAM BUKA & TUTUP**: Berikan informasi hari dan jam buka secara jelas (misal Senin-Minggu 08:00 - 17:00 WIB atau Buka 24 Jam).
   - **HARGA TIKET & STOK KUOTA**: Berikan rincian harga tiket masuk (Dewasa, Anak, Weekday, Weekend), paket wahana, serta informasi kuota/stok tiket yang tersedia.
   - **PENGINAPAN & HOTEL (CHECK-IN & CHECK-OUT)**: Sebutkan jam Check-in (contoh: 14:00 WIB), jam Check-out (contoh: 12:00 WIB), tipe kamar, kapasitas tamu dewasa/anak, tipe ranjang, fasilitas kamar, dan harga sewa per malam.
   - **KULINER & RESTORAN (MENU MAKANAN & MINUMAN)**: Sebutkan tipe tempat makan, sistem reservasi meja, serta daftar menu makanan/minuman khas beserta harganya secara detail!
   - **RENTAL KENDARAAN (SEWA ARMADA)**: Sebutkan unit armada mobil/motor yang tersedia (merk, model, tahun), jenis transmisi (Manual/Matic), kapasitas kursi penumpang, opsi Lepas Kunci (Self-drive) atau Dengan Supir (Driver), serta rincian tarif sewa harian.
   - **FASILITAS**: Sebutkan fasilitas pendukung lengkap yang tersedia di lokasi (toilet, musholla, area parkir, wifi, kolam air panas, spot foto, gazebo, dll).
   - **MITRA PENGELOLA**: Sebutkan nama instansi/badan usaha pengelola mitra, kategori (Dinas Pemerintah atau Swasta/Umum), dan kontak telepon/email resmi jika tersedia.
3. REKOMENDASI BERDASARKAN RATING & POPULARITAS:
   - Jika pengguna menanyakan tempat paling populer / terbaik / favorit / rating tertinggi, utamakan destinasi dengan bintang rating tertinggi (misal ⭐ 4.8 atau 5.0) atau bertanda [⭐ REKOMENDASI UNGGULAN / TOP RATED].
   - Selalu sertakan rating dan jumlah ulasan jika tersedia.
4. TAUTAN HALAMAN:
   - Cantumkan tautan markdown jika merujuk pada produk katalog aktif (contoh format: [Lihat & Pesan](/wisata/slug-produk) atau [Cek Kamar](/penginapan/slug-hotel) atau [Cek Menu](/kuliner/slug-resto)).
5. FORMAT JAWABAN:
   - Gunakan format rapi dengan bullet points (-), ikon emoji yang relevan (📍, ⏰, 🎟️, 🛋️, 🍴, 🚗, 🏢), serta cetak tebal pada informasi penting agar nyaman dibaca di smartphone maupun desktop.{$customSection}

======================================================================
DATA ENSIKLOPEDIA & INFORMASI MASTER WILAYAH PARIWISATA TEGAL:
======================================================================
{$tegalMasterText}

======================================================================
DATA KATALOG AKTUAL DARI DATABASE PLATFORM JELAJAH TEGAL:
======================================================================
--- DESTINASI WISATA (DATABASE) ---
{$wisataText}

--- PENGINAPAN & HOTEL (DATABASE) ---
{$penginapanText}

--- KULINER & RESTORAN (DATABASE) ---
{$kulinerText}

--- EVENT & FESTIVAL (DATABASE) ---
{$eventText}

--- RENTAL KENDARAAN (DATABASE) ---
{$rentalText}

--- DIREKTORI MITRA RESMI TERDAFTAR ---
{$mitraText}
PROMPT;
    }

    /**
     * Mengambil ringkasan katalog aktual dari database untuk knowledge base AI.
     */
    public function getKnowledgeBase(): array
    {
        return Cache::remember('chatbot_knowledge_base', 120, function () {
            $catalogs = CatalogEntity::query()
                ->where('status', 'published')
                ->whereNull('deleted_at')
                ->with([
                    'serviceType',
                    'category',
                    'region',
                    'location',
                    'mitra.operatingHours',
                    'mitra.region',
                    'offers.ticketPackage',
                    'offers.availabilities',
                    'facilities',
                    'operatingHours',
                    'tourism.ticketPackages',
                    'accommodation.rooms.facilities',
                    'accommodation.rooms.offer',
                    'culinary.menuCategories.items',
                    'culinary.menuItems',
                    'culinary.tableSlots',
                    'event.schedules',
                    'event.ticketTypes',
                    'rentalVehicle.rates',
                    'rentalVehicle.availability',
                ])
                ->orderByDesc('is_featured')
                ->orderByDesc('rating_average')
                ->orderByDesc('rating_count')
                ->latest('published_at')
                ->get();

            $mitras = Mitra::query()
                ->publiclyVisible()
                ->with(['region', 'operatingHours', 'features.serviceType'])
                ->get();

            $knowledge = [
                'wisata' => [],
                'penginapan' => [],
                'kuliner' => [],
                'event' => [],
                'rental' => [],
                'wisata_texts' => [],
                'penginapan_texts' => [],
                'kuliner_texts' => [],
                'event_texts' => [],
                'rental_texts' => [],
                'mitra_texts' => [],
                'items_raw' => [],
            ];

            foreach ($mitras as $m) {
                $mHours = [];
                if ($m->operatingHours->isNotEmpty()) {
                    foreach ($m->operatingHours as $moh) {
                        $dayName = match((int)$moh->day_of_week) {
                            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0, 7 => 'Minggu', default => 'Hari ' . $moh->day_of_week
                        };
                        $mHours[] = $moh->is_closed ? "{$dayName}: Tutup" : "{$dayName}: {$moh->opens_at} - {$moh->closes_at} WIB";
                    }
                }
                $mHoursStr = !empty($mHours) ? implode('; ', $mHours) : 'Buka Setiap Hari Kerja (08:00 - 17:00 WIB)';
                $mCat = $m->isDinas() ? 'Pemerintah (Dinas)' : 'Swasta / Umum';
                $mRegion = $m->region?->name ?? 'Kabupaten Tegal';
                $mContact = $m->contact_phone ? "Telp/WA: {$m->contact_phone}" : "Email: " . ($m->contact_email ?? '-');

                $knowledge['mitra_texts'][] = "• **{$m->display_name}** ({$mCat} di {$mRegion}) | Alamat: " . ($m->address ?: 'Kabupaten Tegal') . " | {$mContact} | Jam Kerja: {$mHoursStr}";
            }

            foreach ($catalogs as $c) {
                $formatted = $this->formatEntityDetails($c);
                $code = $c->serviceType?->code ?? 'tourism';

                $knowledge['items_raw'][] = $formatted;

                if ($code === 'tourism') {
                    $knowledge['wisata'][] = $formatted['summary'];
                    $knowledge['wisata_texts'][] = $formatted['text'];
                } elseif ($code === 'accommodation') {
                    $knowledge['penginapan'][] = $formatted['summary'];
                    $knowledge['penginapan_texts'][] = $formatted['text'];
                } elseif ($code === 'culinary') {
                    $knowledge['kuliner'][] = $formatted['summary'];
                    $knowledge['kuliner_texts'][] = $formatted['text'];
                } elseif ($code === 'event') {
                    $knowledge['event'][] = $formatted['summary'];
                    $knowledge['event_texts'][] = $formatted['text'];
                } elseif ($code === 'rental') {
                    $knowledge['rental'][] = $formatted['summary'];
                    $knowledge['rental_texts'][] = $formatted['text'];
                }
            }

            return $knowledge;
        });
    }

    /**
     * Memformat rincian entitas katalog secara komprehensif.
     */
    protected function formatEntityDetails(CatalogEntity $c): array
    {
        $code = $c->serviceType?->code ?? 'tourism';
        $serviceName = $c->serviceType?->name ?? 'Wisata';
        $category = $c->category?->name ?? 'Umum';
        $region = $c->region?->name ?? 'Kabupaten Tegal';
        $address = $c->address ?: ($c->location?->formatted_address ?: ($c->region?->name ?? 'Kabupaten Tegal'));
        $mitraName = $c->mitra?->display_name ?? 'Pemerintah / Pengelola Resmi';
        $mitraCat = $c->mitra?->isDinas() ? 'Dinas Pemerintah' : 'Swasta / Umum';
        $mitraPhone = $c->mitra?->contact_phone ?? '-';
        $mitraEmail = $c->mitra?->contact_email ?? '-';
        $desc = $c->description ?? 'Destinasi layanan resmi terverifikasi di Jelajah Tegal.';

        // Rating & Popularitas
        $ratingAvg = (float) ($c->rating_average ?? 0);
        $ratingCount = (int) ($c->rating_count ?? 0);
        $isFeatured = (bool) ($c->is_featured ?? false);

        if ($ratingCount > 0) {
            $ratingFormatted = number_format($ratingAvg, 1, '.', '');
            $ratingStr = "⭐ Rating: {$ratingFormatted}/5.0 ({$ratingCount} ulasan wisatawan)";
        } else {
            $ratingStr = "⭐ Rating: Destinasi Baru (Belum ada ulasan)";
        }

        $popularityBadge = "";
        if ($isFeatured && $ratingCount > 0 && $ratingAvg >= 4.5) {
            $popularityBadge = " [🔥 PALING POPULER & FAVORIT]";
        } elseif ($isFeatured) {
            $popularityBadge = " [⭐ REKOMENDASI UNGGULAN]";
        } elseif ($ratingCount >= 5 && $ratingAvg >= 4.5) {
            $popularityBadge = " [⭐ TOP RATED]";
        }

        // Fasilitas
        $facilities = $c->facilities->pluck('name')->toArray();
        $facilitiesStr = !empty($facilities) ? implode(', ', $facilities) : 'Area parkir, toilet umum, musholla, dan fasilitas standar';

        // Jam Operasional
        $hoursArr = [];
        if ($c->operatingHours->isNotEmpty()) {
            foreach ($c->operatingHours as $oh) {
                $dayName = match((int)$oh->weekday) {
                    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0, 7 => 'Minggu', default => 'Hari ' . $oh->weekday
                };
                $hoursArr[] = $oh->is_closed ? "{$dayName}: Tutup" : "{$dayName}: {$oh->opens_at} - {$oh->closes_at} WIB";
            }
        } elseif ($c->mitra && $c->mitra->operatingHours->isNotEmpty()) {
            foreach ($c->mitra->operatingHours as $moh) {
                $dayName = match((int)$moh->day_of_week) {
                    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0, 7 => 'Minggu', default => 'Hari ' . $moh->day_of_week
                };
                $hoursArr[] = $moh->is_closed ? "{$dayName}: Tutup" : "{$dayName}: {$moh->opens_at} - {$moh->closes_at} WIB";
            }
        }
        $hoursStr = !empty($hoursArr) ? implode('; ', $hoursArr) : 'Buka Setiap Hari (08:00 - 17:00 WIB)';

        // Penawaran & Harga
        $offersArr = [];
        if ($c->offers->isNotEmpty()) {
            foreach ($c->offers as $off) {
                $p = number_format($off->price, 0, ',', '.');
                $stock = $off->stock_daily ? " (Stok kuota: {$off->stock_daily} tiket/hari)" : " (Stok: Tersedia)";
                $offersArr[] = "{$off->name}: Rp {$p}{$stock}";
            }
        }
        $priceMin = $c->offers->min('price');
        $priceSummary = $priceMin ? "Mulai Rp " . number_format($priceMin, 0, ',', '.') : "Harga bervariasi";
        $offersStr = !empty($offersArr) ? implode(' | ', $offersArr) : $priceSummary;

        // Prefix Tautan
        $routePrefix = match($code) {
            'tourism' => 'wisata',
            'accommodation' => 'penginapan',
            'culinary' => 'kuliner',
            'event' => 'event',
            'rental' => 'rental',
            default => 'wisata'
        };
        $link = "[Lihat & Pesan](/{$routePrefix}/{$c->slug})";

        $specificDetails = [];

        // Sub-domain spesifik
        if ($code === 'tourism' && $c->tourism) {
            $specificDetails[] = "Tipe Wisata: " . ($c->tourism->destination_type ?: 'Wisata Alam');
            $specificDetails[] = "Estimasi Durasi Kunjungan: {$c->tourism->visit_duration_minutes} menit";
            if ($c->tourism->badge) $specificDetails[] = "Badge: {$c->tourism->badge}";
        } elseif ($code === 'accommodation' && $c->accommodation) {
            $acc = $c->accommodation;
            $specificDetails[] = "Properti: {$acc->property_type}, Bintang: {$acc->star_rating} ⭐";
            $specificDetails[] = "⏰ Waktu Check-in: " . ($acc->check_in_time ?: '14:00') . " WIB | Waktu Check-out: " . ($acc->check_out_time ?: '12:00') . " WIB";
            $roomsArr = [];
            foreach ($acc->rooms as $r) {
                $rPrice = $r->offer ? ('Rp ' . number_format($r->offer->price, 0, ',', '.')) : '-';
                $rUnits = $r->total_units ? " (Total {$r->total_units} unit)" : "";
                $roomsArr[] = "Tipe '{$r->name}' (Kapasitas: {$r->capacity_adults} Dewasa, {$r->capacity_children} Anak | Tarif: {$rPrice}/malam{$rUnits})";
            }
            if (!empty($roomsArr)) {
                $specificDetails[] = "Pilihan Kamar: " . implode('; ', $roomsArr);
            }
        } elseif ($code === 'culinary' && $c->culinary) {
            $cul = $c->culinary;
            $specificDetails[] = "Tipe Tempat: " . ($cul->venue_type ?: 'Restoran & Rumah Makan');
            $specificDetails[] = "Reservasi Meja: " . ($cul->accepts_reservations ? "Menerima Reservasi (Telp: " . ($cul->phone ?: $mitraPhone) . ")" : "Langsung Datang (Dine-in)");
            if ($cul->reservation_notes) {
                $specificDetails[] = "Catatan Reservasi: {$cul->reservation_notes}";
            }
            $menuList = [];
            if ($cul->menuCategories->isNotEmpty()) {
                foreach ($cul->menuCategories as $cat) {
                    $catItems = [];
                    foreach ($cat->items as $item) {
                        $p = number_format($item->price, 0, ',', '.');
                        $feat = $item->is_featured ? " (⭐ Menu Favorit)" : "";
                        $descText = $item->description ? " - {$item->description}" : "";
                        $catItems[] = "{$item->name}: Rp {$p}{$feat}{$descText}";
                    }
                    if (!empty($catItems)) {
                        $menuList[] = "[{$cat->name}]: " . implode(', ', $catItems);
                    }
                }
            }
            if (!empty($menuList)) {
                $specificDetails[] = "Daftar Menu: " . implode(' | ', $menuList);
            }
        } elseif ($code === 'event' && $c->event) {
            $ev = $c->event;
            $specificDetails[] = "Lokasi Venue Acara: " . ($ev->venue_name ?: $address);
            $specificDetails[] = "Jadwal Pelaksanaan: {$ev->starts_at?->format('d M Y H:i')} s.d. {$ev->ends_at?->format('d M Y H:i')} WIB";
            if ($ev->registration_deadline) {
                $specificDetails[] = "Batas Pendaftaran: {$ev->registration_deadline->format('d M Y H:i')} WIB";
            }
            if ($ev->know_before_you_go) {
                $specificDetails[] = "Informasi Penting: {$ev->know_before_you_go}";
            }
            $tixArr = [];
            foreach ($ev->ticketTypes as $tt) {
                $p = number_format($tt->price, 0, ',', '.');
                $tixArr[] = "{$tt->name}: Rp {$p} (Sisa Kuota: {$tt->quota} tiket)";
            }
            if (!empty($tixArr)) {
                $specificDetails[] = "Pilihan Tiket Event: " . implode(', ', $tixArr);
            }
        } elseif ($code === 'rental' && $c->rentalVehicle) {
            $rv = $c->rentalVehicle;
            $specificDetails[] = "Unit Kendaraan: {$rv->brand} {$rv->model} ({$rv->vehicle_type}, Tahun {$rv->year})";
            $specificDetails[] = "Transmisi: " . ($rv->transmission ?: 'Manual / Matic') . " | Kapasitas: {$rv->seats} Kursi Penumpang";
            $specificDetails[] = "Opsi Sewa: Lepas Kunci = " . ($rv->self_drive_available ? 'Bisa' : 'Tidak') . ", Dengan Supir = " . ($rv->driver_available ? 'Bisa' : 'Tidak');
            $rateArr = [];
            foreach ($rv->rates as $rate) {
                $p = number_format($rate->price, 0, ',', '.');
                $rateArr[] = "{$rate->rate_type}: Rp {$p}";
            }
            if (!empty($rateArr)) {
                $specificDetails[] = "Tarif Sewa: " . implode('; ', $rateArr);
            }
            if ($rv->deposit_amount > 0) {
                $specificDetails[] = "Deposit Jaminan: Rp " . number_format($rv->deposit_amount, 0, ',', '.');
            }
            if ($rv->pickup_instructions) {
                $specificDetails[] = "Pengambilan Unit: {$rv->pickup_instructions}";
            }
        }

        $specStr = !empty($specificDetails) ? "\n  - 📋 Detail Spesifik: " . implode("\n    • ", $specificDetails) : "";

        $summary = "• **{$c->name}**{$popularityBadge} ({$category} - {$region}) | {$ratingStr} | {$priceSummary} | Link: {$link}";

        $text = "• **{$c->name}**{$popularityBadge} ({$serviceName} - {$category} di {$region})\n"
              . "  - 📍 Alamat & Lokasi: {$address}\n"
              . "  - 🏢 Mitra Pengelola: {$mitraName} ({$mitraCat} | Telp: {$mitraPhone}, Email: {$mitraEmail})\n"
              . "  - ⏰ Jadwal Jam Buka: {$hoursStr}\n"
              . "  - 🎟️ Harga & Stok Tiket: {$offersStr}\n"
              . "  - 🛋️ Fasilitas: {$facilitiesStr}\n"
              . "  - ℹ️ Tentang: {$desc}"
              . $specStr . "\n"
              . "  - 🔗 Link Halaman: {$link} | {$ratingStr}";

        return [
            'name' => $c->name,
            'slug' => $c->slug,
            'code' => $code,
            'service_name' => $serviceName,
            'category' => $category,
            'region' => $region,
            'address' => $address,
            'mitra' => $mitraName,
            'phone' => $mitraPhone,
            'hours' => $hoursStr,
            'prices' => $offersStr,
            'facilities' => $facilitiesStr,
            'desc' => $desc,
            'summary' => $summary,
            'text' => $text,
            'specific_details' => $specificDetails,
            'link' => $link,
            'rating_str' => $ratingStr,
        ];
    }

    /**
     * Ensiklopedia master informasi destinasi, kuliner, dan transportasi Kabupaten Tegal.
     */
    protected function getTegalMasterKnowledgeText(): string
    {
        return <<<MASTER
1. KAWASAN WISATA PEMANDIAN AIR PANAS GUCI (BUMIJAWA):
   - 📍 Lokasi & Alamat: Jl. Objek Wisata Guci, Kalisiwi, Desa Guci, Kecamatan Bumijawa, Kabupaten Tegal, Jawa Tengah (lereng utara Gunung Slamet, berjarak sekitar 43 km ke arah selatan dari Kota Slawi/Tegal).
   - ⏰ Jam Operasional: 24 Jam (Area kawasan), Wahana Pemandian Pancuran 13 & 7 buka 07:00 - 17:00 WIB, Guciku Hot Waterboom 07:00 - 18:00 WIB.
   - 🎟️ Estimasi Tiket Masuk: Retribusi gerbang utama Pemda Tegal sekitar Rp 15.000 (hari kerja / weekday) dan Rp 20.000 - Rp 25.000 (akhir pekan / weekend / hari libur nasional). Wahana privat/waterboom berbayar terpisah.
   - 🛋️ Fasilitas: Kolam air panas belerang alami jernih tanpa bau menyengat, Pancuran 13, Pancuran 7, hotel berbintang, villa keluarga, area glamping, pasar sayur & buah segar, musholla, toilet, warung sate kelinci & mendoan hangat.
   - 🏨 Penginapan Populer di Guci: Grand Dian Hotel Guci, Sun Q Ta Hotel, Guci Forest, Ashafana Villa & Resto, D'Gucian Glamping.

2. WADUK CACABAN (KEDUNGBANTENG):
   - 📍 Lokasi & Alamat: Desa Penujah, Kecamatan Kedungbanteng, Kabupaten Tegal, Jawa Tengah (berjarak sekitar 9 km dari pusat Slawi).
   - ⏰ Jam Buka: Setiap hari pukul 07:00 - 18:00 WIB.
   - 🎟️ Tiket Masuk: Sekitar Rp 5.000 - Rp 10.000 / orang.
   - 🛋️ Fasilitas & Daya Tarik: Gardu Pandang dengan panorama pulau-pulau kecil menyerupai Raja Ampat, dermaga perahu wisata keliling danau, sentra kuliner ikan bakar air tawar, area perkemahan (camping ground), dan jogging track tepi danau.

3. SITUS & MUSEUM PURBAKALA SEMEDO:
   - 📍 Lokasi & Alamat: Desa Semedo, Kecamatan Kedungbanteng, Kabupaten Tegal, Jawa Tengah.
   - ⏰ Jam Buka: Selasa - Minggu pukul 08:30 - 15:30 WIB (Hari Senin libur pemeliharaan).
   - 🎟️ Tiket Masuk: Gratis / tiket retribusi museum edukasi.
   - 🛋️ Daya Tarik: Museum cagar budaya jejak manusia purba Homo erectus Semedo, fosil gajah kerdil Stegodon, kudanil, badak purba, dan kapak batu zaman Paleolitikum.

4. PANTAI PURWAHAMBA INDAH (PURIN):
   - 📍 Lokasi & Alamat: Jl. Raya Pantura No. 1, Desa Purwahamba, Kecamatan Suradadi, Kabupaten Tegal, Jawa Tengah (Jalur Pantura Tegal - Pemalang).
   - ⏰ Jam Buka: Setiap hari pukul 07:00 - 18:00 WIB.
   - 🎟️ Tiket Masuk: Sekitar Rp 10.000 - Rp 15.000 / orang.
   - 🛋️ Daya Tarik: Monumen patung dinosaurus raksasa ikonik, kolam renang waterboom pesisir, gazebo rindang pohon cemara laut, arena bermain anak, dan dermaga pantai.

5. PANTAI ALAM INDAH (PAI TEGAL):
   - 📍 Lokasi & Alamat: Jl. Sangir, Kelurahan Mintaragen, Kecamatan Tegal Timur, Tegal.
   - ⏰ Jam Buka: 06:00 - 21:00 WIB.
   - 🎟️ Tiket Masuk: Sekitar Rp 5.000 - Rp 10.000 / orang.
   - 🛋️ Daya Tarik: Monumen Bahari (Alutsista TNI AL), dermaga apung / jembatan kayu, spot sunrise pesisir utara, dan sentra kuliner seafood pantai.

6. CURUG CANTEL & CURUG JEJEG:
   - 📍 Lokasi: Desa Batunyana / Kalipetung, Kecamatan Bumijawa, Kabupaten Tegal.
   - ⏰ Jam Buka: 08:00 - 16:30 WIB.
   - 🎟️ Tiket Masuk: Rp 10.000 / orang.
   - 🛋️ Daya Tarik: Air terjun alami 60 meter di lereng pegunungan dengan udara sangat sejuk.

7. TAMAN RAKYAT SLAWI AYU (TRASA) & ALUN-ALUN SLAWI:
   - 📍 Lokasi TRASA: Jl. Banjaranyar - Slawi (Depan Terminal Bus Slawi), Procot, Slawi, Kabupaten Tegal.
   - 📍 Lokasi Alun-alun Hanggawana: Jl. KH. Wahid Hasyim, Slawi Kulon, Slawi, Kabupaten Tegal.
   - ⏰ Jam Buka: 24 Jam (Sentra kuliner & UMKM ramai sore hingga malam hari).

8. SENTRA KULINER KHAS TEGAL LENGKAP:
   - 🍴 Sate Kambing Batibul / Balibul: Sate daging kambing bawah tiga bulan / bawah lima bulan empuk tanpa bau prengus, disajikan di atas hotplate dengan bumbu kecap pedas rawit bawang merah. Rekomendasi sentra: Adiwerna, Slawi, Bojong (Sate Wendy's, Sate Sari Mendo, Sate Batibul Bang Awi).
   - 🍴 Tahu Aci & Tahu Pletok Khas Slawi: Tahu kuning goreng gurih dengan adonan tepung aci kucai dan bawang putih yang renyah di luar kenyal di dalam. Sentra: Adiwerna & Jl. Jenderal Sudirman Slawi.
   - 🫖 Teh Poci Tegal: Tradisi teh melati pekat (*Wasgitel: Wangi, Panas, Sepet, Legi, Kentel*) diseduh dalam poci tanah liat dan cangkir gula batu khas Tegal.
   - 🍲 Soto Tauco (Sauto Tegal): Soto berkuah kaldu tauco gurih manis khas Tegal dengan suwiran ayam, daging sapi, atau babat goreng renyah dan taburan kerupuk usus.
   - 🥘 Kupat Glabed & Kupat Bongkok: Ketupat kuah kuning kental gurih khas Tegal dengan pendamping sate kerang manis pedas.
   - 🥟 Olos, Glotak, Latopia & Pilus: Camilan dan oleh-oleh legendaris khas Tegal.

9. AKSES TRANSPORTASI & TRANSIT HUB:
   - 🚆 Stasiun Besar Tegal: Jl. Semeru No. 1, Tegal Barat (Pusat transit kereta api eksekutif, bisnis, dan ekonomi lintas pulau Jawa).
   - 🚌 Terminal Bus Slawi: Jl. Banjaranyar, Dukuhsalam, Slawi, Kabupaten Tegal.
   - 🛣️ Gerbang Tol Adiwerna / Slawi: Pintu keluar Tol Trans-Jawa KM 279 penghubung utama ke Slawi dan kawasan wisata Guci.
MASTER;
    }

    /**
     * Fallback cerdas berbasis pencocokan intent NLP & data riil jika API Gemini tidak terhubung.
     */
    protected function getFallbackResponse(string $message, array $knowledge): string
    {
        $m = strtolower(trim($message));
        $rawItems = $knowledge['items_raw'] ?? [];

        // 1. Cek pencocokan spesifik terhadap item di Database
        foreach ($rawItems as $item) {
            $nameClean = strtolower($item['name']);
            $slugClean = strtolower(str_replace('-', ' ', $item['slug']));

            if (str_contains($m, $nameClean) || str_contains($m, $slugClean)) {
                // User menanyakan lokasi / alamat
                if (str_contains($m, 'lokasi') || str_contains($m, 'alamat') || str_contains($m, 'dimana') || str_contains($m, 'daerah') || str_contains($m, 'tempat')) {
                    return "📍 **Lokasi & Alamat {$item['name']}:**\n\n"
                         . "Tempat ini beralamat di **{$item['address']}** ({$item['region']}).\n\n"
                         . "⏰ **Jam Operasional:** {$item['hours']}\n"
                         . "🏢 **Mitra Pengelola:** {$item['mitra']} (Telp: {$item['phone']})\n"
                         . "🎟️ **Tarif / Harga:** {$item['prices']}\n\n"
                         . "Kunjungi tautan berikut untuk informasi lengkap dan pemesanan: {$item['link']}";
                }

                // User menanyakan jam buka / jadwal / tutup
                if (str_contains($m, 'jam') || str_contains($m, 'buka') || str_contains($m, 'tutup') || str_contains($m, 'jadwal') || str_contains($m, 'operasional') || str_contains($m, 'hari apa')) {
                    return "⏰ **Jadwal & Jam Buka {$item['name']}:**\n\n"
                         . "• **Jam Operasional:** {$item['hours']}\n"
                         . "• **Lokasi:** {$item['address']}\n"
                         . "• **Pengelola:** {$item['mitra']} (Telp: {$item['phone']})\n\n"
                         . "Kunjungi tautan berikut untuk informasi lengkap dan pemesanan: {$item['link']}";
                }

                // User menanyakan harga / tiket / stok / biaya / tarif
                if (str_contains($m, 'harga') || str_contains($m, 'tiket') || str_contains($m, 'biaya') || str_contains($m, 'tarif') || str_contains($m, 'stok') || str_contains($m, 'kuota') || str_contains($m, 'berapa')) {
                    return "🎟️ **Informasi Harga & Tiket {$item['name']}:**\n\n"
                         . "• **Rincian Harga / Tiket:** {$item['prices']}\n"
                         . "• **Lokasi:** {$item['address']}\n"
                         . "• **Fasilitas:** {$item['facilities']}\n\n"
                         . "Pesan sekarang secara online melalui: {$item['link']}";
                }

                // User menanyakan menu makanan / kuliner
                if (str_contains($m, 'menu') || str_contains($m, 'makan') || str_contains($m, 'minum') || str_contains($m, 'porsi')) {
                    $details = !empty($item['specific_details']) ? implode("\n• ", $item['specific_details']) : "Menu makanan khas lokal tersedia.";
                    return "🍴 **Daftar Menu & Kuliner {$item['name']}:**\n\n"
                         . "• {$details}\n\n"
                         . "• **Alamat:** {$item['address']}\n"
                         . "• **Jam Buka:** {$item['hours']}\n\n"
                         . "Lihat detail menu dan reservasi meja di: {$item['link']}";
                }

                // User menanyakan check-in / check-out penginapan
                if (str_contains($m, 'check in') || str_contains($m, 'check out') || str_contains($m, 'cekin') || str_contains($m, 'cekuot') || str_contains($m, 'kamar')) {
                    $details = !empty($item['specific_details']) ? implode("\n• ", $item['specific_details']) : "Informasi akomodasi dan kamar.";
                    return "🏨 **Informasi Kamar & Waktu Menginap {$item['name']}:**\n\n"
                         . "• {$details}\n\n"
                         . "• **Alamat:** {$item['address']}\n"
                         . "• **Fasilitas:** {$item['facilities']}\n\n"
                         . "Pesan kamar langsung di: {$item['link']}";
                }

                // Default detail lengkap item database
                return "ℹ️ **Informasi Lengkap {$item['name']}:**\n\n" . $item['text'];
            }
        }

        // 2. Pencocokan Cerdas Terhadap Destinasi Ikonik Tegal (Master Knowledge)
        if (str_contains($m, 'guci') || str_contains($m, 'air panas') || str_contains($m, 'guciku') || str_contains($m, 'pancuran 13')) {
            if (str_contains($m, 'lokasi') || str_contains($m, 'dimana') || str_contains($m, 'alamat') || str_contains($m, 'daerah') || str_contains($m, 'arah') || str_contains($m, 'rute')) {
                return "📍 **Lokasi & Alamat Kawasan Wisata Pemandian Air Panas Guci:**\n\n"
                     . "Kawasan Wisata Guci beralamat di **Jl. Objek Wisata Guci, Kalisiwi, Desa Guci, Kecamatan Bumijawa, Kabupaten Tegal, Jawa Tengah**.\n\n"
                     . "Terletak di lereng utara Gunung Slamet pada ketinggian ±1.050 mdpl dengan udara sejuk pegunungan, berjarak sekitar **43 km (±1,5 jam perjalanan)** ke arah selatan dari pusat Kota Slawi / Tegal.\n\n"
                     . "⏰ **Jam Buka:** 24 Jam (Kawasan), Wahana Pemandian 07:00 - 17:00 WIB\n"
                     . "🎟️ **Tiket Masuk:** Rp 15.000 (Weekday) / Rp 20.000 - Rp 25.000 (Weekend)\n"
                     . "🛋️ **Fasilitas:** Kolam air panas belerang alami, hotel, villa, glamping, pasar buah sayur, musholla, toilet.\n\n"
                     . "Jelajahi destinasi selengkapnya di menu [Jelajah Wisata](/wisata)!";
            }
            if (str_contains($m, 'jam') || str_contains($m, 'buka') || str_contains($m, 'tutup') || str_contains($m, 'jadwal')) {
                return "⏰ **Jam Buka & Operasional Wisata Guci Tegal:**\n\n"
                     . "• **Area Kawasan Wisata:** Buka **24 Jam** setiap hari (Senin s.d. Minggu).\n"
                     . "• **Wahana Pemandian Air Panas Pancuran 13 & 7:** Buka pukul **07:00 - 17:00 WIB**.\n"
                     . "• **Guciku Hot Waterboom:** Buka pukul **07:00 - 18:00 WIB**.\n\n"
                     . "📍 **Lokasi:** Desa Guci, Kec. Bumijawa, Kab. Tegal. Cek info selengkapnya di [Jelajah Wisata](/wisata)!";
            }
            if (str_contains($m, 'harga') || str_contains($m, 'tiket') || str_contains($m, 'biaya') || str_contains($m, 'tarif') || str_contains($m, 'berapa')) {
                return "🎟️ **Estimasi Harga Tiket Masuk Wisata Guci Tegal:**\n\n"
                     . "• **Hari Kerja (Senin - Jumat):** ± Rp 15.000 / orang\n"
                     . "• **Akhir Pekan (Sabtu - Minggu / Libur Nasional):** ± Rp 20.000 - Rp 25.000 / orang\n"
                     . "• **Parkir Kendaraan:** Motor Rp 5.000, Mobil Rp 10.000, Bus Wisata Rp 20.000\n"
                     . "• **Wahana Privat Waterboom:** Tiket terpisah mulai Rp 40.000 - Rp 50.000 / orang.\n\n"
                     . "📍 **Alamat:** Jl. Objek Wisata Guci, Kec. Bumijawa, Kab. Tegal.";
            }
            return "♨️ **Kawasan Wisata Pemandian Air Panas Guci Tegal:**\n\n"
                 . "Guci adalah destinasi wisata unggulan di Kabupaten Tegal dengan air panas belerang alami berkhasiat kesehatan dari lereng Gunung Slamet.\n\n"
                 . "📍 **Alamat:** Jl. Objek Wisata Guci, Desa Guci, Kec. Bumijawa, Kab. Tegal.\n"
                 . "⏰ **Jam Buka:** 24 Jam (Area Kawasan), 07:00 - 17:00 WIB (Pemandian Air Panas)\n"
                 . "🎟️ **Tiket Masuk:** Rp 15.000 - Rp 25.000 / orang\n"
                 . "🛋️ **Fasilitas:** Pancuran 13, Pancuran 7, Waterboom Air Panas, Hotel & Villa, Glamping, Pasar Sayur & Oleh-oleh.\n\n"
                 . "Temukan info penginapan dan tiket wisata di [Jelajah Tegal](/wisata)!";
        }

        // 3. Waduk Cacaban
        if (str_contains($m, 'cacaban') || str_contains($m, 'waduk')) {
            return "🏞️ **Destinasi Wisata Waduk Cacaban Tegal:**\n\n"
                 . "📍 **Alamat & Lokasi:** Desa Penujah, Kecamatan Kedungbanteng, Kabupaten Tegal, Jawa Tengah (sekitar 9 km ke arah timur dari Slawi).\n"
                 . "⏰ **Jam Buka:** Buka setiap hari pukul **07:00 - 18:00 WIB**.\n"
                 . "🎟️ **Tiket Masuk:** Rp 5.000 - Rp 10.000 / orang.\n"
                 . "🛋️ **Fasilitas & Wahana:** Gardu pandang danau panorama pulau-pulau kecil, dermaga sewa perahu keliling danau, sentra kuliner ikan bakar air tawar, jogging track, area camping ground, musholla, dan toilet.\n\n"
                 . "Cek rekomendasi wisata lainnya di [Jelajah Wisata](/wisata)!";
        }

        // 4. Situs Semedo
        if (str_contains($m, 'semedo') || str_contains($m, 'purba') || str_contains($m, 'museum')) {
            return "🏛️ **Situs & Museum Purbakala Semedo Tegal:**\n\n"
                 . "📍 **Alamat & Lokasi:** Desa Semedo, Kecamatan Kedungbanteng, Kabupaten Tegal, Jawa Tengah.\n"
                 . "⏰ **Jam Buka:** **Selasa s.d. Minggu pukul 08:30 - 15:30 WIB** (Hari Senin tutup untuk konservasi museum).\n"
                 . "🎟️ **Tiket Masuk:** Gratis / Tiket Retribusi Edukasi Museum Daerah.\n"
                 . "🛋️ **Daya Tarik:** Fosil manusia purba Homo erectus Semedo, gajah kerdil purba Stegodon, kudanil, artefak batu Paleolitikum, ruang pamer edukatif interaktif, dan bioskop mini.";
        }

        // 5. Pantai Purwahamba Indah / Purin
        if (str_contains($m, 'purin') || str_contains($m, 'purwahamba')) {
            return "🏖️ **Wisata Pantai Purwahamba Indah (Purin):**\n\n"
                 . "📍 **Alamat & Lokasi:** Jl. Raya Pantura No. 1, Desa Purwahamba, Kecamatan Suradadi, Kabupaten Tegal (Jalur Utama Pantura Tegal - Pemalang).\n"
                 . "⏰ **Jam Buka:** Setiap hari pukul **07:00 - 18:00 WIB**.\n"
                 . "🎟️ **Tiket Masuk:** Rp 10.000 - Rp 15.000 / orang.\n"
                 . "🛋️ **Fasilitas:** Patung dinosaurus T-Rex raksasa, kolam renang waterboom pesisir, gazebo rindang pohon cemara laut, taman bermain anak, dermaga pantai.";
        }

        // 6. Pantai Alam Indah / PAI
        if (str_contains($m, 'pantai alam indah') || str_contains($m, 'pai')) {
            return "🌊 **Pantai Alam Indah (PAI Tegal):**\n\n"
                 . "📍 **Alamat & Lokasi:** Jl. Sangir, Kelurahan Mintaragen, Kecamatan Tegal Timur, Tegal.\n"
                 . "⏰ **Jam Buka:** Setiap hari pukul **06:00 - 21:00 WIB**.\n"
                 . "🎟️ **Tiket Masuk:** Rp 5.000 - Rp 10.000 / orang.\n"
                 . "🛋️ **Daya Tarik:** Monumen Bahari TNI AL, jembatan apung dermaga kayu, spot sunrise pesisir utara, sentra warung seafood.";
        }

        // 7. Kuliner Khas Tegal (Sate, Tahu Aci, Teh Poci, Soto Tauco)
        if (str_contains($m, 'kuliner') || str_contains($m, 'makan') || str_contains($m, 'sate') || str_contains($m, 'tahu aci') || str_contains($m, 'teh poci') || str_contains($m, 'soto') || str_contains($m, 'tauco')) {
            $dbKuliner = !empty($knowledge['kuliner']) ? implode("\n", array_slice($knowledge['kuliner'], 0, 4)) : "";
            $extra = $dbKuliner ? "\n\n🍴 **Kuliner Terdaftar di Platform Jelajah Tegal:**\n" . $dbKuliner : "";

            return "🍲 **Rekomendasi Kuliner Otentik Khas Tegal:**\n\n"
                 . "1. 🍢 **Sate Kambing Batibul / Balibul**: Daging kambing bawah tiga/lima bulan empuk disajikan di atas hotplate bumbu kecap pedas (Sentra: Adiwerna, Slawi, Bojong).\n"
                 . "2. 🥟 **Tahu Aci & Tahu Pletok**: Tahu kuning goreng gurih dengan adonan tepung aci kucai renyah khas Slawi.\n"
                 . "3. 🫖 **Teh Poci Wasgitel**: Teh melati pekat diseduh poci tanah liat dengan gula batu khas Tegal.\n"
                 . "4. 🥣 **Soto Tauco (Sauto Tegal)**: Soto kuah kaldu tauco gurih manis dengan suwiran ayam, babat renyah, dan kerupuk usus.\n"
                 . "5. 🥘 **Kupat Glabed & Sate Kerang**: Ketupat kuah kuning kental gurih khas Alun-alun Tegal." . $extra . "\n\n"
                 . "Kunjungi menu [Kuliner Tegal](/kuliner) untuk melihat daftar rumah makan dan pesan menu!";
        }

        // 8. Penginapan, Hotel & Check-in/Check-out
        if (str_contains($m, 'hotel') || str_contains($m, 'penginapan') || str_contains($m, 'inap') || str_contains($m, 'villa') || str_contains($m, 'glamping') || str_contains($m, 'kamar') || str_contains($m, 'check in') || str_contains($m, 'check out')) {
            $dbInap = !empty($knowledge['penginapan']) ? implode("\n", array_slice($knowledge['penginapan'], 0, 4)) : "";
            $extra = $dbInap ? "\n\n🏨 **Penginapan Terdaftar di Platform:**\n" . $dbInap : "";

            return "🏨 **Panduan Penginapan, Hotel & Villa di Tegal:**\n\n"
                 . "• **Waktu Standar Check-in:** Pukul **14:00 WIB**\n"
                 . "• **Waktu Standar Check-out:** Pukul **12:00 WIB**\n"
                 . "• **Pilihan Akomodasi Populer:**\n"
                 . "  1. **Grand Dian Hotel Guci & Slawi**: Hotel berbintang dengan fasilitas kolam air panas, restoran, meeting room.\n"
                 . "  2. **Guci Forest**: Villa kayu estetik & area glamping di tengah hutan pinus sejuk.\n"
                 . "  3. **Sun Q Ta Hotel Guci**: Resort keluarga dengan waterpark air panas.\n"
                 . "  4. **Homestay & Villa Murah Guci**: Tarif ramah mulai Rp 150.000 - Rp 350.000/malam." . $extra . "\n\n"
                 . "Cek ketersediaan kamar dan reservasi online di menu [Penginapan & Hotel](/penginapan)!";
        }

        // 9. Rental Kendaraan & Sewa Armada
        if (str_contains($m, 'rental') || str_contains($m, 'sewa') || str_contains($m, 'mobil') || str_contains($m, 'motor') || str_contains($m, 'armada') || str_contains($m, 'kendaraan') || str_contains($m, 'lepas kunci') || str_contains($m, 'supir')) {
            $dbRental = !empty($knowledge['rental']) ? implode("\n", array_slice($knowledge['rental'], 0, 4)) : "";
            $extra = $dbRental ? "\n\n🚗 **Armada Rental Terdaftar di Platform:**\n" . $dbRental : "";

            return "🚗 **Layanan Rental & Sewa Kendaraan Jelajah Tegal:**\n\n"
                 . "• **Jenis Armada Tersedia:**\n"
                 . "  - **City Car & MPV Keluarga**: Toyota Avanza, Daihatsu Xenia, Innova Reborn, Mitsubishi Xpander (Kapasitas 6-7 Penumpang, Transmisi Manual/Matic).\n"
                 . "  - **Microbus Wisata**: Toyota HiAce Commuter/Premio, Isuzu Elf (Kapasitas 14-19 Penumpang).\n"
                 . "  - **Motor Wisata**: Honda Beat, Vario, Yamaha NMAX, Scoopy.\n"
                 . "• **Pilihan Layanan:**\n"
                 . "  - **Lepas Kunci (Self-drive)**: Syarat KTP + SIM A / SIM C + Jaminan deposit.\n"
                 . "  - **Dengan Supir (With Driver)**: Termasuk supir berpengalaman memahami rute tanjakan Guci.\n"
                 . "• **Estimasi Tarif:** Mobil mulai Rp 250.000 - Rp 450.000/hari; Motor mulai Rp 70.000 - Rp 100.000/hari." . $extra . "\n\n"
                 . "Pesan unit rental langsung di menu [Rental Kendaraan](/rental)!";
        }

        // 10. Event & Festival
        if (str_contains($m, 'event') || str_contains($m, 'acara') || str_contains($m, 'festival') || str_contains($m, 'konser') || str_contains($m, 'karnaval')) {
            $dbEvent = !empty($knowledge['event']) ? implode("\n", array_slice($knowledge['event'], 0, 4)) : "";
            $extra = $dbEvent ? "\n\n🎭 **Event Terdaftar di Platform:**\n" . $dbEvent : "";

            return "🎭 **Agenda Event & Festival Seni Budaya Kabupaten Tegal:**\n\n"
                 . "• **Event Ikonik Tahunan:**\n"
                 . "  1. **Karnaval Budaya Hari Jadi Kabupaten Tegal** (Kawasan Slawi & TRASA)\n"
                 . "  2. **Festival Ruwat Bumi Guci** (Pemandian Air Panas Guci)\n"
                 . "  3. **Festival Kuliner & Pameran UMKM Tegal** (Taman Rakyat Slawi Ayu)\n"
                 . "  4. **Larung Sesaji Sedekah Laut** (Pesisir Pantai Purin / PAI)" . $extra . "\n\n"
                 . "Lihat jadwal lengkap dan reservasi tiket event di menu [Event & Festival](/event)!";
        }

        // 11. Kategori Populer / Rating Tertinggi
        if (str_contains($m, 'populer') || str_contains($m, 'terbaik') || str_contains($m, 'rating') || str_contains($m, 'favorit') || str_contains($m, 'ramai') || str_contains($m, 'bagus')) {
            if (!empty($knowledge['wisata'])) {
                $list = implode("\n", array_slice($knowledge['wisata'], 0, 5));
                return "⭐ **Destinasi Wisata Paling Populer & Berating Tinggi di Jelajah Tegal:**\n\n" . $list . "\n\n Destinasi di atas diurutkan berdasarkan ulasan dan rating kepuasan wisatawan. Kunjungi menu [Jelajah Wisata](/wisata) untuk pesan tiket!";
            }
            return "⭐ **Destinasi Wisata Paling Populer di Tegal:**\n\n"
                 . "1. **Pemandian Air Panas Guci** (Bumijawa) - Pemandian air panas alami berkhasiat ⭐ 4.9\n"
                 . "2. **Waduk Cacaban** (Kedungbanteng) - Danau indah panorama pulau ⭐ 4.8\n"
                 . "3. **Situs Manusia Purba Semedo** (Kedungbanteng) - Museum fosil purbakala ⭐ 4.8\n"
                 . "4. **Pantai Purwahamba Indah (Purin)** (Suradadi) - Wisata pantai & waterboom ⭐ 4.7\n\n"
                 . "Jelajahi tiket dan lokasi di menu [Jelajah Wisata](/wisata)!";
        }

        // General Welcome
        return "Halo! Saya adalah **Asisten Wisata Cerdas Jelajah Tegal**. Saya siap memberikan informasi detail mengenai:\n\n"
             . "• 📍 **Lokasi & Alamat Lengkap** destinasi wisata, hotel, kuliner, dan rental\n"
             . "• ⏰ **Jadwal Jam Buka & Jam Tutup** harian dan akhir pekan\n"
             . "• 🎟️ **Harga Tiket Masuk & Stok Kuota** pemesanan online\n"
             . "• 🏨 **Jam Check-in/Out & Tipe Kamar** penginapan/villa di Guci & Tegal\n"
             . "• 🍴 **Daftar Menu Makanan, Minuman & Harga** kuliner khas Tegal\n"
             . "• 🚗 **Sewa Armada Rental Mobil/Motor** (Lepas Kunci / Dengan Supir)\n"
             . "• 🛋️ **Fasilitas Lengkap & Kontak Mitra Pengelola**\n\n"
             . "Silakan ketik pertanyaan spesifik Anda, contohnya: *\"dimana lokasi wisata guci\"* atau *\"harga sate ayam muda dan jam buka\"*!";
    }
}
