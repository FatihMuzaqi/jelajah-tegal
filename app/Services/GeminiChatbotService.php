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
        $wisataText = empty($knowledge['wisata']) ? 'Data wisata dalam pembaruan.' : implode("\n", $knowledge['wisata']);
        $penginapanText = empty($knowledge['penginapan']) ? 'Data penginapan dalam pembaruan.' : implode("\n", $knowledge['penginapan']);
        $kulinerText = empty($knowledge['kuliner']) ? 'Data kuliner dalam pembaruan.' : implode("\n", $knowledge['kuliner']);
        $eventText = empty($knowledge['event']) ? 'Data event dalam pembaruan.' : implode("\n", $knowledge['event']);
        $rentalText = empty($knowledge['rental']) ? 'Data rental dalam pembaruan.' : implode("\n", $knowledge['rental']);

        $customSection = $customAddition ? "\n\nINSTRUKSI & PENGUMUMAN KHUSUS DARI PENGELOLA:\n" . $customAddition : '';

        return <<<PROMPT
Anda adalah "Asisten Wisata Jelajah Tegal", pemandu wisata virtual resmi, cerdas, ramah, dan solutif untuk platform pariwisata Jelajah Tegal (Jawa Tengah).

TUGAS DAN ATURAN ANDA:
1. Jawab pertanyaan wisatawan dengan ramah, hangat, antusias, dan menggunakan Bahasa Indonesia yang santun.
2. Berikan rekomendasi terbaik untuk 5 domain layanan Jelajah Tegal:
   - 🏖️ Destinasi Wisata (Pemandian Air Panas Guci, Curug Cantel, Pantai Purwahamba Indah / Purin, Waduk Cacaban, dsb.)
   - 🏨 Penginapan & Hotel (Villa di Guci, Hotel di Kota Tegal, Glamping, Homestay)
   - 🍲 Kuliner Khas Tegal (Sate Kambing Balibul/Batibul muda, Kupat Glabed, Tahu Aci/Pletok, Soto Tauco, Teh Poci Slawi)
   - 🎪 Event & Festival (Pesta Budaya, Konser Musik, Pameran UMKM)
   - 🚗 Rental Kendaraan (Sewa Mobil & Motor lepas kunci atau dengan driver)
3. Sertakan tautan relevan menggunakan format markdown jika merekomendasikan produk (misal: [Lihat & Pesan](/wisata/guci-hot-spring)).
4. Gunakan bullet points atau penomoran agar jawaban mudah dibaca di layar HP/smartphone.
5. Jika ditanya hal di luar topik wisata/kuliner/Tegal, jawab dengan sopan dan kembalikan fokus ke pariwisata Tegal.{$customSection}

DATA KATALOG AKTUAL DI PLATFORM JELAJAH TEGAL:
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
     */
    public function getKnowledgeBase(): array
    {
        return Cache::remember('chatbot_knowledge_base', 120, function () {
            $catalogs = CatalogEntity::query()
                ->where('status', 'published')
                ->with(['serviceType', 'category', 'region', 'offers', 'facilities', 'tourism', 'accommodation', 'culinary', 'event', 'rentalVehicle'])
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

                // Map route prefix
                $routePrefix = match($code) {
                    'tourism' => 'wisata',
                    'accommodation' => 'penginapan',
                    'culinary' => 'kuliner',
                    'event' => 'event',
                    'rental' => 'rental',
                    default => 'wisata'
                };

                $summary = "• **{$c->name}** ({$category} - {$region}) | {$priceStr} | Link: [Lihat & Pesan](/{$routePrefix}/{$c->slug}) | Keterangan: " . str($c->description)->limit(150);

                if ($code === 'tourism') {
                    $knowledge['wisata'][] = $summary;
                } elseif ($code === 'accommodation') {
                    $propType = $c->accommodation?->property_type ?? 'Penginapan';
                    $knowledge['penginapan'][] = $summary . " (Tipe: {$propType})";
                } elseif ($code === 'culinary') {
                    $venueType = $c->culinary?->venue_type ?? 'Restoran';
                    $knowledge['kuliner'][] = $summary . " (Tipe: {$venueType})";
                } elseif ($code === 'event') {
                    $startDate = $c->event?->starts_at?->format('d M Y') ?? 'Segera';
                    $knowledge['event'][] = $summary . " (Jadwal: {$startDate})";
                } elseif ($code === 'rental') {
                    $vType = $c->rentalVehicle?->vehicle_type ?? 'Kendaraan';
                    $brand = $c->rentalVehicle?->brand ?? '';
                    $model = $c->rentalVehicle?->model ?? '';
                    $knowledge['rental'][] = $summary . " ({$brand} {$model} - {$vType})";
                } else {
                    $knowledge['wisata'][] = $summary;
                }
            }

            return $knowledge;
        });
    }

    /**
     * Fallback cerdas jika API tidak dapat dihubungi.
     */
    protected function getFallbackResponse(string $message, array $knowledge): string
    {
        $m = strtolower($message);

        if (str_contains($m, 'guci') || str_contains($m, 'wisata') || str_contains($m, 'pantai') || str_contains($m, 'curug')) {
            return "🏖️ **Rekomendasi Wisata Populer di Tegal:**\n\n" .
                   "1. **Pemandian Air Panas Guci** - Relaksasi air panas alami di kaki Gunung Slamet.\n" .
                   "2. **Pantai Purwahamba Indah (Purin)** - Pantai asri dengan wahana rekreasi keluarga.\n" .
                   "3. **Waduk Cacaban** - Danau indah dengan panorama perbukitan.\n\n" .
                   "👉 Anda bisa melihat dan memesan tiket langsung di menu [Jelajah Wisata](/wisata)!";
        }

        if (str_contains($m, 'makan') || str_contains($m, 'kuliner') || str_contains($m, 'sate') || str_contains($m, 'soto')) {
            return "🍲 **Kuliner Khas Tegal Wajib Coba:**\n\n" .
                   "1. **Sate Kambing Muda (Balibul/Batibul)** - Daging super empuk tanpa bau prengus.\n" .
                   "2. **Kupat Glabed & Blengong** - Ketupat kuah gurih berpadu sate blengong khas Tegal.\n" .
                   "3. **Tahu Aci / Pletok** - Camilan renyah gurih khas Slawi.\n\n" .
                   "👉 Cek daftar tempat makan favorit di menu [Kuliner Tegal](/kuliner)!";
        }

        if (str_contains($m, 'hotel') || str_contains($m, 'inap') || str_contains($m, 'villa') || str_contains($m, 'homestay')) {
            return "🏨 **Pilihan Penginapan Nyaman di Tegal:**\n\n" .
                   "• **Villa & Resort Guci**: Cocok untuk liburan keluarga dengan akses langsung pemandian air panas.\n" .
                   "• **Hotel Pusat Kota Tegal**: Pilihan strategis dekat stasiun dan pusat perbelanjaan.\n\n" .
                   "👉 Temukan kamar terbaik di menu [Penginapan & Hotel](/penginapan)!";
        }

        if (str_contains($m, 'rental') || str_contains($m, 'mobil') || str_contains($m, 'motor') || str_contains($m, 'sewa')) {
            return "🚗 **Layanan Rental Kendaraan di Tegal:**\n\n" .
                   "Kami menyediakan sewa mobil & motor lepas kunci maupun dengan driver berpengalaman untuk menemani perjalanan wisata Anda.\n\n" .
                   "👉 Cek tarif & ketersediaan unit di menu [Rental Kendaraan](/rental)!";
        }

        return "Halo! Saya Asisten Wisata **Jelajah Tegal** siap membantu Anda. Anda bisa menanyakan rekomendasi **Tempat Wisata**, **Kuliner Enak**, **Penginapan/Hotel**, **Jadwal Event**, atau **Rental Kendaraan** di wilayah Tegal. Apa yang ingin Anda cari hari ini?";
    }
}
