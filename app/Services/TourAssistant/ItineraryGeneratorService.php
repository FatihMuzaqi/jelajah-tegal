<?php

namespace App\Services\TourAssistant;

use App\Models\CatalogOffer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ItineraryGeneratorService
{
    public function generate(string $startDate, string $endDate, float $budget, int $pax, array $categories = ['accommodation', 'tourism', 'culinary', 'event', 'rental']): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $nights = max(1, $start->diffInDays($end));
        $daysCount = max(1, $start->diffInDays($end) + 1);

        $allData = [
            'accommodations' => in_array('accommodation', $categories) ? CatalogOffer::with(['catalogEntity.region', 'catalogEntity.media', 'catalogEntity.category'])->whereHas('catalogEntity', fn ($q) => $q->publicDomain('accommodation'))->whereIn('status', ['active', 'published'])->get() : collect(),
            'tourisms' => in_array('tourism', $categories) ? CatalogOffer::with(['catalogEntity.region', 'catalogEntity.media', 'catalogEntity.category'])->whereHas('catalogEntity', fn ($q) => $q->publicDomain('tourism'))->whereIn('status', ['active', 'published'])->get() : collect(),
            'culinaries' => in_array('culinary', $categories) ? CatalogOffer::with(['catalogEntity.region', 'catalogEntity.media', 'catalogEntity.category'])->whereHas('catalogEntity', fn ($q) => $q->publicDomain('culinary'))->whereIn('status', ['active', 'published'])->get() : collect(),
            'events' => in_array('event', $categories) ? CatalogOffer::with(['catalogEntity.region', 'catalogEntity.media', 'catalogEntity.category'])->whereHas('catalogEntity', fn ($q) => $q->publicDomain('event'))->whereIn('status', ['active', 'published'])->get() : collect(),
            'rentals' => in_array('rental', $categories) ? CatalogOffer::with(['catalogEntity.region', 'catalogEntity.media', 'catalogEntity.category'])->whereHas('catalogEntity', fn ($q) => $q->publicDomain('rental'))->whereIn('status', ['active', 'published'])->get() : collect(),
        ];

        $options = [
            'economy' => $this->buildOption($allData, $budget * 0.9, $budget, $pax, $nights, $daysCount, $start, 'Paket Hemat', 'Tegal Hemat & Terjangkau'),
            'optimal' => $this->buildOption($allData, $budget, $budget, $pax, $nights, $daysCount, $start, 'Paket Pas Budget (Rekomendasi AI)', 'Tegal: Bahari, Kuliner & Relaksasi Guci'),
            'premium' => $this->buildOption($allData, $budget * 1.1, $budget, $pax, $nights, $daysCount, $start, 'Paket Premium', 'Tegal Eksklusif & Lengkap'),
        ];

        return array_filter($options);
    }

    private function buildOption(array $allData, float $targetBudget, float $actualBudget, int $pax, int $nights, int $daysCount, Carbon $startDate, string $name, string $headline): ?array
    {
        $items = [];
        $totalCost = 0;
        $remainingBudget = $targetBudget;

        // =========================================================================
        // PASS 1: GUARANTEED REPRESENTATION (Minimal 1 per kategori pilihan)
        // =========================================================================

        // 1. Penginapan / Hotel (Jika dipilih)
        $selectedAcc = null;
        if ($allData['accommodations']->isNotEmpty()) {
            $accTarget = $targetBudget * 0.35;
            $bestAcc = $this->findClosest($allData['accommodations'], $accTarget, $nights * ceil($pax / 2));
            if ($bestAcc) {
                $cost = (float) $bestAcc->price * $nights * ceil($pax / 2);
                if ($cost <= $remainingBudget) {
                    $item = [
                        'type' => 'accommodation',
                        'offer' => $bestAcc,
                        'quantity' => (int) ceil($pax / 2),
                        'days' => $nights,
                        'unit_price' => (float) $bestAcc->price,
                        'subtotal' => $cost
                    ];
                    $items[] = $item;
                    $selectedAcc = $item;
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                }
            }
        }

        // 2. Rental Kendaraan (Jika dipilih)
        $selectedRental = null;
        if ($allData['rentals']->isNotEmpty()) {
            $vehiclesNeeded = (int) ceil($pax / 5);
            $rentTarget = $targetBudget * 0.25;
            $bestRental = $this->findClosest($allData['rentals'], $rentTarget, $nights * $vehiclesNeeded);
            if ($bestRental) {
                $cost = (float) $bestRental->price * $nights * $vehiclesNeeded;
                if ($cost <= $remainingBudget) {
                    $item = [
                        'type' => 'rental',
                        'offer' => $bestRental,
                        'quantity' => $vehiclesNeeded,
                        'days' => $nights,
                        'unit_price' => (float) $bestRental->price,
                        'subtotal' => $cost
                    ];
                    $items[] = $item;
                    $selectedRental = $item;
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                }
            }
        }

        // 3. Destinasi Wisata
        $tourismCount = 0;
        $selectedTourisms = [];
        if ($allData['tourisms']->isNotEmpty()) {
            $shuffledTourisms = $allData['tourisms']->shuffle();
            foreach ($shuffledTourisms as $tour) {
                $cost = (float) $tour->price * $pax;
                if ($cost <= $remainingBudget && $tourismCount < min(2, $daysCount * 2)) {
                    $item = [
                        'type' => 'tourism',
                        'offer' => $tour,
                        'quantity' => $pax,
                        'days' => 1,
                        'unit_price' => (float) $tour->price,
                        'subtotal' => $cost
                    ];
                    $items[] = $item;
                    $selectedTourisms[] = $item;
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                    $tourismCount++;
                }
            }
        }

        // 4. Event & Festival
        $eventCount = 0;
        $selectedEvents = [];
        if ($allData['events']->isNotEmpty()) {
            $shuffledEvents = $allData['events']->shuffle();
            foreach ($shuffledEvents as $evt) {
                $cost = (float) $evt->price * $pax;
                if ($cost <= $remainingBudget && $eventCount < 1) {
                    $item = [
                        'type' => 'event',
                        'offer' => $evt,
                        'quantity' => $pax,
                        'days' => 1,
                        'unit_price' => (float) $evt->price,
                        'subtotal' => $cost
                    ];
                    $items[] = $item;
                    $selectedEvents[] = $item;
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                    $eventCount++;
                }
            }
        }

        // 5. Kuliner / Restoran
        $culinaryCount = 0;
        $selectedCulinaries = [];
        if ($allData['culinaries']->isNotEmpty()) {
            $shuffledCulinaries = $allData['culinaries']->shuffle();
            foreach ($shuffledCulinaries as $cul) {
                $cost = (float) $cul->price * $pax;
                if ($cost <= $remainingBudget && $culinaryCount < 1) {
                    $item = [
                        'type' => 'culinary',
                        'offer' => $cul,
                        'quantity' => $pax,
                        'days' => 1,
                        'unit_price' => (float) $cul->price,
                        'subtotal' => $cost
                    ];
                    $items[] = $item;
                    $selectedCulinaries[] = $item;
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                    $culinaryCount++;
                }
            }
        }

        // =========================================================================
        // PASS 2: BALANCED FILLING (Mengisi sisa budget secara seimbang)
        // =========================================================================
        $usedOfferIds = collect($items)->pluck('offer.id')->toArray();
        $maxTourism = 3 * $daysCount;
        $maxCulinary = 2 * $daysCount;
        $maxEvent = 1 * $daysCount;

        $extraPool = collect()
            ->concat($allData['tourisms']->filter(fn($o) => !in_array($o->id, $usedOfferIds))->map(fn($o) => ['type' => 'tourism', 'offer' => $o, 'multiplier' => $pax]))
            ->concat($allData['events']->filter(fn($o) => !in_array($o->id, $usedOfferIds))->map(fn($o) => ['type' => 'event', 'offer' => $o, 'multiplier' => $pax]))
            ->concat($allData['culinaries']->filter(fn($o) => !in_array($o->id, $usedOfferIds))->map(fn($o) => ['type' => 'culinary', 'offer' => $o, 'multiplier' => $pax]))
            ->shuffle();

        foreach ($extraPool as $poolItem) {
            $type = $poolItem['type'];

            if ($type === 'tourism' && $tourismCount >= $maxTourism) continue;
            if ($type === 'culinary' && $culinaryCount >= $maxCulinary) continue;
            if ($type === 'event' && $eventCount >= $maxEvent) continue;

            $cost = (float) $poolItem['offer']->price * $poolItem['multiplier'];
            if ($cost <= $remainingBudget) {
                $item = [
                    'type' => $type,
                    'offer' => $poolItem['offer'],
                    'quantity' => $poolItem['multiplier'],
                    'days' => 1,
                    'unit_price' => (float) $poolItem['offer']->price,
                    'subtotal' => $cost
                ];
                $items[] = $item;
                $totalCost += $cost;
                $remainingBudget -= $cost;

                if ($type === 'tourism') {
                    $selectedTourisms[] = $item;
                    $tourismCount++;
                } elseif ($type === 'culinary') {
                    $selectedCulinaries[] = $item;
                    $culinaryCount++;
                } elseif ($type === 'event') {
                    $selectedEvents[] = $item;
                    $eventCount++;
                }
            }
        }

        if (count($items) === 0) {
            return null;
        }

        // =========================================================================
        // PASS 3: CHRONOLOGICAL TIMELINE ASSEMBLY (Menyusun jadwal per hari & jam)
        // =========================================================================
        $days = [];
        $tourPool = $selectedTourisms;
        $culPool = $selectedCulinaries;
        $eventPool = $selectedEvents;

        for ($d = 1; $d <= $daysCount; $d++) {
            $currentDate = $startDate->copy()->addDays($d - 1);
            $dayActivities = [];

            // 1. Hari 1 - Pagi 09:00 WIB: Rental Kendaraan
            if ($d === 1 && $selectedRental) {
                $dayActivities[] = [
                    'time' => '09:00 WIB',
                    'time_period' => 'Pagi',
                    'color' => '#0284c7', // Sky Blue
                    'type' => 'rental',
                    'title' => 'Penjemputan / Ambil ' . ($selectedRental['offer']->catalogEntity->name ?? 'Armada Rental Kendaraan'),
                    'description' => 'Mobilisasi perjalanan liburan lebih fleksibel, hemat waktu, dan nyaman bersama rombongan.',
                    'location' => $selectedRental['offer']->catalogEntity->address ?? 'Kota Tegal & Sekitarnya',
                    'item' => $selectedRental,
                ];
            }

            // 2. Eksplorasi Wisata Pagi (10:00 WIB)
            if (!empty($tourPool)) {
                $tourItem = array_shift($tourPool);
                $entityName = $tourItem['offer']->catalogEntity->name ?? 'Destinasi Wisata';
                $dayActivities[] = [
                    'time' => ($d === 1 && $selectedRental) ? '10:30 WIB' : '09:30 WIB',
                    'time_period' => 'Pagi',
                    'color' => '#3b82f6', // Royal Blue
                    'type' => 'tourism',
                    'title' => 'Eksplorasi ' . $entityName,
                    'description' => $this->generateStory('tourism', $entityName),
                    'location' => $tourItem['offer']->catalogEntity->address ?? 'Tegal',
                    'item' => $tourItem,
                ];
            }

            // 3. Makan Siang Kuliner Khas (12:30 WIB)
            if (!empty($culPool)) {
                $culItem = array_shift($culPool);
                $culName = $culItem['offer']->catalogEntity->name ?? 'Kuliner Khas Tegal';
                $dayActivities[] = [
                    'time' => '12:30 WIB',
                    'time_period' => 'Siang',
                    'color' => '#f59e0b', // Amber
                    'type' => 'culinary',
                    'title' => 'Makan Siang di ' . $culName,
                    'description' => $this->generateStory('culinary', $culName),
                    'location' => $culItem['offer']->catalogEntity->address ?? 'Pusat Kuliner Tegal',
                    'item' => $culItem,
                ];
            }

            // 4. Check-in Penginapan / Hotel (Hari 1, 14:00 WIB)
            if ($d === 1 && $selectedAcc) {
                $accName = $selectedAcc['offer']->catalogEntity->name ?? 'Penginapan & Hotel';
                $dayActivities[] = [
                    'time' => '14:30 WIB',
                    'time_period' => 'Siang',
                    'color' => '#10b981', // Emerald
                    'type' => 'accommodation',
                    'title' => 'Check-in ' . $accName,
                    'description' => 'Istirahat dan simpan barang bawaan di akomodasi yang nyaman dan strategis.',
                    'location' => $selectedAcc['offer']->catalogEntity->address ?? 'Tegal',
                    'item' => $selectedAcc,
                ];
            }

            // 5. Wisata Sore / Sunset (16:30 WIB)
            if (!empty($tourPool)) {
                $tourItem = array_shift($tourPool);
                $entityName = $tourItem['offer']->catalogEntity->name ?? 'Destinasi Sore';
                $dayActivities[] = [
                    'time' => '16:30 WIB',
                    'time_period' => 'Sore',
                    'color' => '#8b5cf6', // Violet
                    'type' => 'tourism',
                    'title' => 'Menikmati Sore & Sunset di ' . $entityName,
                    'description' => $this->generateStory('sunset', $entityName),
                    'location' => $tourItem['offer']->catalogEntity->address ?? 'Tegal',
                    'item' => $tourItem,
                ];
            }

            // 6. Malam: Event Seni / Kuliner Malam (19:30 WIB)
            if (!empty($eventPool)) {
                $evtItem = array_shift($eventPool);
                $evtName = $evtItem['offer']->catalogEntity->name ?? 'Event & Festival';
                $dayActivities[] = [
                    'time' => '19:30 WIB',
                    'time_period' => 'Malam',
                    'color' => '#ec4899', // Pink
                    'type' => 'event',
                    'title' => 'Menghadiri ' . $evtName,
                    'description' => 'Menyaksikan kemeriahan atraksi seni budaya dan panggung festival lokal.',
                    'location' => $evtItem['offer']->catalogEntity->address ?? 'Tegal',
                    'item' => $evtItem,
                ];
            } elseif (!empty($culPool)) {
                $culItem = array_shift($culPool);
                $culName = $culItem['offer']->catalogEntity->name ?? 'Kuliner Malam';
                $dayActivities[] = [
                    'time' => '19:30 WIB',
                    'time_period' => 'Malam',
                    'color' => '#f59e0b',
                    'type' => 'culinary',
                    'title' => 'Santap Malam di ' . $culName,
                    'description' => 'Menikmati suasana malam kota Tegal ditemani hidangan hangat dan teh poci poci khas poci tanah liat.',
                    'location' => $culItem['offer']->catalogEntity->address ?? 'Tegal',
                    'item' => $culItem,
                ];
            }

            // Jika hari ke-2 atau ke-3 masih kosong, isi kegiatan santai
            if (empty($dayActivities)) {
                $dayActivities[] = [
                    'time' => '10:00 WIB',
                    'time_period' => 'Pagi',
                    'color' => '#3b82f6',
                    'type' => 'tourism',
                    'title' => 'Jelajah Budaya & Sentra Oleh-oleh Khas Tegal',
                    'description' => 'Belanja cinderamata, kerajinan lokal, serta oleh-oleh ikonik khas Tegal sebelum kembali pulang.',
                    'location' => 'Pusat Kota Tegal',
                    'item' => null,
                ];
            }

            $days[] = [
                'day_number' => $d,
                'date' => $currentDate->format('Y-m-d'),
                'formatted_date' => $currentDate->translatedFormat('l, d F Y'),
                'title' => $this->getDayTitle($d, $daysCount),
                'activities' => $dayActivities,
            ];
        }

        $costPerPax = round($totalCost / max(1, $pax));

        return [
            'name' => $name,
            'headline' => $headline,
            'total_cost' => $totalCost,
            'cost_per_pax' => $costPerPax,
            'remaining_budget' => max(0, $actualBudget - $totalCost),
            'total_days' => $daysCount,
            'nights' => $nights,
            'pax' => $pax,
            'is_optimized' => true,
            'days' => $days,
            'items' => $items, // Untuk kelancaran checkout invoice
        ];
    }

    private function getDayTitle(int $day, int $totalDays): string
    {
        if ($totalDays === 1) {
            return 'Hari 1: Eksplorasi Kilat Wisata & Kuliner Tegal';
        }
        if ($day === 1) {
            return 'Hari 1: Kedatangan, Wisata Pesisir & Kuliner Legendaris';
        }
        if ($day === $totalDays) {
            return "Hari {$day}: Jelajah Budaya, Belanja Oleh-Oleh & Kepulangan";
        }
        return "Hari {$day}: Relaksasi Alam Pegunungan Guci & Panorama Tegal";
    }

    private function generateStory(string $type, string $name): string
    {
        return match ($type) {
            'tourism' => "Menikmati keindahan alam dan atraksi wisata terfavorit di {$name} dengan pemandu lokal.",
            'sunset' => "Menyaksikan pemandangan sunset memukau di {$name} dari spot panorama terbaik.",
            'culinary' => "Mencicipi sajian cita rasa autentik khas Tegal di {$name} yang legendaris dan menggugah selera.",
            default => "Aktivitas liburan terencana untuk memaksimalkan pengalaman berwisata di Tegal.",
        };
    }

    private function findClosest(Collection $items, float $targetTotal, int $multiplier): ?CatalogOffer
    {
        $best = null;
        $bestDiff = INF;
        foreach ($items as $item) {
            $total = (float) $item->price * $multiplier;
            $diff = abs($targetTotal - $total);
            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $best = $item;
            }
        }
        return $best;
    }
}
