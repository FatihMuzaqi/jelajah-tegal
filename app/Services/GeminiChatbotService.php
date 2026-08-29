<?php

namespace App\Services;

use App\Models\CatalogEntity;
use App\Models\ChatbotSetting;
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
                    ['text' => "Halo! Saya adalah Asisten Wisata Jelajah Tegal. Saya siap membantu Anda menjelajahi keindahan wisata, penginapan nyaman, kuliner lezat, event seru, dan rental kendaraan di Tegal! Ada yang bisa saya bantu?"]
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
                'maxOutputTokens' => max((int) ($setting->max_tokens ?? 1500), 1200),
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
     * Membangun system prompt persona asisten wisata Jelajah Tegal.
     */
    protected function buildSystemPrompt(array $knowledge, ?string $customAddition = null): string
    {
        $wisataText = empty($knowledge['wisata']) ? 'Belum ada data destinasi wisata aktif.' : implode("\n", $knowledge['wisata']);
        $penginapanText = empty($knowledge['penginapan']) ? 'Belum ada data penginapan/hotel aktif.' : implode("\n", $knowledge['penginapan']);
        $kulinerText = empty($knowledge['kuliner']) ? 'Belum ada data tempat kuliner aktif.' : implode("\n", $knowledge['kuliner']);
        $eventText = empty($knowledge['event']) ? 'Belum ada data event/festival aktif.' : implode("\n", $knowledge['event']);
        $rentalText = empty($knowledge['rental']) ? 'Belum ada data rental kendaraan aktif.' : implode("\n", $knowledge['rental']);

        $customSection = $customAddition ? "\n\nINSTRUKSI & PENGUMUMAN KHUSUS DARI PENGELOLA:\n" . $customAddition : '';

        return <<<PROMPT
Anda adalah "Asisten Wisata Jelajah Tegal", pemandu virtual resmi, cerdas, ramah, dan solutif untuk platform pariwisata Jelajah Tegal (Jawa Tengah).

TUGAS DAN ATURAN ANDA:
1. Jawab pertanyaan wisatawan dengan ramah, hangat, antusias, dan menggunakan Bahasa Indonesia yang santun.
2. REKOMENDASI BERDASARKAN RATING & POPULARITAS WISATAWAN:
   - Anda MEMILIKI AKSES DATA RATING RATA-RATA (skala 1.0 - 5.0), JUMLAH ULASAN, serta label POPULER / UNGGULAN pada setiap entitas di daftar data di bawah.
   - Jika pengguna menanyakan:
     * "Destinasi paling populer", "wisata favorit", "rekomendasi terbaik", "rating tertinggi", "tempat paling ramai/disukai":
     * Prioritaskan merekomendasikan destinasi yang memiliki rating bintang tertinggi (misal ⭐ 4.8 atau 5.0) atau bertanda [🔥 PALING POPULER] / [⭐ REKOMENDASI UNGGULAN] / [⭐ TOP RATED].
   - Selalu sertakan informasi rating & ulasan saat merekomendasikan tempat (contoh: "⭐ Rating 5.0/5.0 dari 12 ulasan wisatawan") agar rekomendasi Anda akurat, informatif, dan terpercaya.
3. PENTING - ATURAN DATA AKTUAL (DATABASE):
   - Hanya rekomendasikan produk, tempat wisata, penginapan, kuliner, event, dan rental yang TERDAFTAR PADA BAGIAN "DATA KATALOG AKTUAL DI PLATFORM JELAJAH TEGAL" DI BAWAH INI.
   - JANGAN PERNAH mengarang, merekayasa, atau merekomendasikan tempat wisata/produk yang TIDAK ADA atau SUDAH DIHAPUS dari daftar katalog aktual di bawah ini.
   - Jika pengguna bertanya tentang kategori yang belum ada datanya, sampaikan dengan ramah bahwa saat ini belum ada data terdaftar di platform untuk kategori tersebut.
4. Sertakan tautan relevan menggunakan format markdown jika merekomendasikan produk (contoh format tautan: [Lihat & Pesan](/wisata/slug-produk)).
5. Gunakan bullet points atau penomoran agar jawaban mudah dibaca di layar HP/smartphone.
6. Jika ditanya hal di luar topik pariwisata Tegal, jawab dengan sopan dan arahkan kembali ke pariwisata Tegal.{$customSection}

DATA KATALOG AKTUAL DI PLATFORM JELAJAH TEGAL (TERURUT BERDASARKAN RATING & POPULARITAS TERTINGGI):
--- DESTINASI WISATA ---
{$wisataText}

--- PENGINAPAN & HOTEL ---
{$penginapanText}

--- KULINER & RESTORAN ---
{$kulinerText}

--- EVENT & FESTIVAL ---
{$eventText}

--- RENTAL KENDARAAN ---
{$rentalText}
PROMPT;
    }

    /**
     * Mengambil ringkasan katalog aktual dari database untuk knowledge base AI.
     * Mengurutkan berdasarkan rating_average, rating_count, dan is_featured untuk deteksi popularitas.
     */
    public function getKnowledgeBase(): array
    {
        return Cache::remember('chatbot_knowledge_base', 120, function () {
            $catalogs = CatalogEntity::query()
                ->where('status', 'published')
                ->whereNull('deleted_at')
                ->with(['serviceType', 'category', 'region', 'offers', 'facilities', 'tourism', 'accommodation', 'culinary', 'event', 'rentalVehicle'])
                ->orderByDesc('is_featured')
                ->orderByDesc('rating_average')
                ->orderByDesc('rating_count')
                ->latest('published_at')
                ->get();

            $knowledge = [
                'wisata' => [],
                'penginapan' => [],
                'kuliner' => [],
                'event' => [],
                'rental' => [],
            ];

            foreach ($catalogs as $c) {
                $code = $c->serviceType?->code ?? 'tourism';
                $priceMin = $c->offers->min('price');
                $priceStr = $priceMin ? 'Mulai Rp ' . number_format($priceMin, 0, ',', '.') : 'Harga bervariasi';
                $region = $c->region?->name ?? 'Tegal';
                $category = $c->category?->name ?? 'Umum';

                // Format Rating & Popularitas
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

                // Map route prefix
                $routePrefix = match($code) {
                    'tourism' => 'wisata',
                    'accommodation' => 'penginapan',
                    'culinary' => 'kuliner',
                    'event' => 'event',
                    'rental' => 'rental',
                    default => 'wisata'
                };

                $summary = "• **{$c->name}**{$popularityBadge} ({$category} - {$region}) | {$ratingStr} | {$priceStr} | Link: [Lihat & Pesan](/{$routePrefix}/{$c->slug}) | Keterangan: " . str($c->description)->limit(140);

                if ($code === 'tourism') {
                    if ($c->tourism) {
                        $knowledge['wisata'][] = $summary;
                    }
                } elseif ($code === 'accommodation') {
                    if ($c->accommodation) {
                        $propType = $c->accommodation->property_type ?? 'Penginapan';
                        $knowledge['penginapan'][] = $summary . " (Tipe: {$propType})";
                    }
                } elseif ($code === 'culinary') {
                    if ($c->culinary) {
                        $venueType = $c->culinary->venue_type ?? 'Restoran';
                        $knowledge['kuliner'][] = $summary . " (Tipe: {$venueType})";
                    }
                } elseif ($code === 'event') {
                    if ($c->event) {
                        $startDate = $c->event->starts_at?->format('d M Y') ?? 'Segera';
                        $knowledge['event'][] = $summary . " (Jadwal: {$startDate})";
                    }
                } elseif ($code === 'rental') {
                    if ($c->rentalVehicle) {
                        $vType = $c->rentalVehicle->vehicle_type ?? 'Kendaraan';
                        $brand = $c->rentalVehicle->brand ?? '';
                        $model = $c->rentalVehicle->model ?? '';
                        $knowledge['rental'][] = $summary . " ({$brand} {$model} - {$vType})";
                    }
                }
            }

            return $knowledge;
        });
    }

    /**
     * Fallback cerdas berbasis data riil database jika API Gemini tidak terhubung.
     */
    protected function getFallbackResponse(string $message, array $knowledge): string
    {
        $m = strtolower($message);

        // 1. Kategori Populer / Rating Tertinggi / Terbaik
        if (str_contains($m, 'populer') || str_contains($m, 'terbaik') || str_contains($m, 'rating') || str_contains($m, 'favorit') || str_contains($m, 'ramai') || str_contains($m, 'bagus')) {
            if (!empty($knowledge['wisata'])) {
                $list = implode("\n", array_slice($knowledge['wisata'], 0, 5));
                return "⭐ **Destinasi Wisata Paling Populer & Berating Tinggi di Jelajah Tegal:**\n\n" . $list . "\n\n👉 Destinasi di atas diurutkan berdasarkan ulasan dan rating kepuasan wisatawan. Kunjungi menu [Jelajah Wisata](/wisata) untuk pesan tiket!";
            }
            return "⭐ **Destinasi Paling Populer di Jelajah Tegal:**\n\nSaat ini belum ada destinasi yang memiliki ulasan dan publikasi aktif di sistem. Silakan periksa berkala di menu [Jelajah Wisata](/wisata).";
        }

        // 2. Kategori Wisata Umum
        if (str_contains($m, 'wisata') || str_contains($m, 'pantai') || str_contains($m, 'curug') || str_contains($m, 'alam') || str_contains($m, 'libur')) {
            if (!empty($knowledge['wisata'])) {
                $list = implode("\n", array_slice($knowledge['wisata'], 0, 5));
                return "🏖️ **Destinasi Wisata Terdaftar di Jelajah Tegal:**\n\n" . $list . "\n\n👉 Kunjungi menu [Jelajah Wisata](/wisata) untuk informasi lengkap dan pemesanan tiket!";
            }
            return "🏖️ **Destinasi Wisata Jelajah Tegal:**\n\nSaat ini belum ada destinasi wisata yang terdaftar/terpublikasi aktif di sistem. Silakan periksa kembali berkala di menu [Jelajah Wisata](/wisata).";
        }

        // 3. Kategori Kuliner
        if (str_contains($m, 'makan') || str_contains($m, 'kuliner') || str_contains($m, 'sate') || str_contains($m, 'soto') || str_contains($m, 'resto')) {
            if (!empty($knowledge['kuliner'])) {
                $list = implode("\n", array_slice($knowledge['kuliner'], 0, 5));
                return "🍲 **Rekomendasi Kuliner Terdaftar di Tegal:**\n\n" . $list . "\n\n👉 Cek daftar resto favorit di menu [Kuliner Tegal](/kuliner)!";
            }
            return "🍲 **Kuliner Khas Tegal:**\n\nSaat ini belum ada data restoran/kuliner yang terpublikasi di sistem. Anda dapat melihat informasi kuliner selengkapnya di menu [Kuliner Tegal](/kuliner).";
        }

        // 4. Kategori Penginapan & Hotel
        if (str_contains($m, 'hotel') || str_contains($m, 'inap') || str_contains($m, 'villa') || str_contains($m, 'homestay') || str_contains($m, 'kamar')) {
            if (!empty($knowledge['penginapan'])) {
                $list = implode("\n", array_slice($knowledge['penginapan'], 0, 5));
                return "🏨 **Pilihan Penginapan Terdaftar di Tegal:**\n\n" . $list . "\n\n👉 Temukan kamar terbaik di menu [Penginapan & Hotel](/penginapan)!";
            }
            return "🏨 **Penginapan & Hotel di Tegal:**\n\nSaat ini belum ada data penginapan yang terpublikasi di sistem. Silakan cek menu [Penginapan & Hotel](/penginapan) untuk informasi lebih lanjut.";
        }

        // 5. Kategori Rental Kendaraan
        if (str_contains($m, 'rental') || str_contains($m, 'mobil') || str_contains($m, 'motor') || str_contains($m, 'sewa') || str_contains($m, 'kendaraan')) {
            if (!empty($knowledge['rental'])) {
                $list = implode("\n", array_slice($knowledge['rental'], 0, 5));
                return "🚗 **Layanan Rental Kendaraan Terdaftar:**\n\n" . $list . "\n\n👉 Cek ketersediaan armada di menu [Rental Kendaraan](/rental)!";
            }
            return "🚗 **Rental Kendaraan di Tegal:**\n\nSaat ini belum ada unit rental kendaraan yang terpublikasi di sistem. Silakan kunjungi menu [Rental Kendaraan](/rental).";
        }

        // 6. Kategori Event
        if (str_contains($m, 'event') || str_contains($m, 'acara') || str_contains($m, 'festival') || str_contains($m, 'konser')) {
            if (!empty($knowledge['event'])) {
                $list = implode("\n", array_slice($knowledge['event'], 0, 5));
                return "🎪 **Agenda Event & Festival Terdaftar:**\n\n" . $list . "\n\n👉 Cek jadwal selengkapnya di menu [Event & Festival](/event)!";
            }
            return "🎪 **Event & Festival di Tegal:**\n\nSaat ini belum ada event aktif yang terpublikasi. Silakan cek berkala di menu [Event & Festival](/event).";
        }

        // General Welcome
        return "Halo! Saya Asisten Wisata **Jelajah Tegal** siap membantu Anda. Anda dapat menanyakan rekomendasi **Tempat Wisata Populer**, **Kuliner Enak**, **Penginapan/Hotel Terbaik**, **Jadwal Event**, atau **Rental Kendaraan** yang aktif terdaftar di sistem. Apa yang ingin Anda cari?";
    }
}
