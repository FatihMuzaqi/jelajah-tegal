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

        $allData = [
            'accommodations' => in_array('accommodation', $categories) ? CatalogOffer::whereHas('catalogEntity', fn ($q) => $q->publicDomain('accommodation'))->whereIn('status', ['active', 'published'])->get() : collect(),
            'tourisms' => in_array('tourism', $categories) ? CatalogOffer::whereHas('catalogEntity', fn ($q) => $q->publicDomain('tourism'))->whereIn('status', ['active', 'published'])->get() : collect(),
            'culinaries' => in_array('culinary', $categories) ? CatalogOffer::whereHas('catalogEntity', fn ($q) => $q->publicDomain('culinary'))->whereIn('status', ['active', 'published'])->get() : collect(),
            'events' => in_array('event', $categories) ? CatalogOffer::whereHas('catalogEntity', fn ($q) => $q->publicDomain('event'))->whereIn('status', ['active', 'published'])->get() : collect(),
            'rentals' => in_array('rental', $categories) ? CatalogOffer::whereHas('catalogEntity', fn ($q) => $q->publicDomain('rental'))->whereIn('status', ['active', 'published'])->get() : collect(),
        ];

        $options = [
            'economy' => $this->buildOption($allData, $budget * 0.9, $budget, $pax, $nights, 'Paket Hemat'),
            'optimal' => $this->buildOption($allData, $budget, $budget, $pax, $nights, 'Paket Pas Budget'),
            'premium' => $this->buildOption($allData, $budget * 1.1, $budget, $pax, $nights, 'Paket Premium'),
        ];

        return array_filter($options);
    }

    private function buildOption(array $allData, float $targetBudget, float $actualBudget, int $pax, int $nights, string $name): ?array
    {
        $items = [];
        $totalCost = 0;
        $remainingBudget = $targetBudget;

        // =========================================================================
        // PASS 1: GUARANTEED REPRESENTATION (Wajib ada minimal 1 per kategori pilihan)
        // =========================================================================

        // 1. Penginapan / Hotel (Jika dipilih)
        if ($allData['accommodations']->isNotEmpty()) {
            $accTarget = $targetBudget * 0.35;
            $bestAcc = $this->findClosest($allData['accommodations'], $accTarget, $nights * ceil($pax / 2));
            if ($bestAcc) {
                $cost = (float) $bestAcc->price * $nights * ceil($pax / 2);
                if ($cost <= $remainingBudget) {
                    $items[] = [
                        'type' => 'accommodation',
                        'offer' => $bestAcc,
                        'quantity' => (int) ceil($pax / 2),
                        'days' => $nights,
                        'unit_price' => (float) $bestAcc->price,
                        'subtotal' => $cost
                    ];
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                }
            }
        }

        // 2. Rental Kendaraan (Jika dipilih)
        if ($allData['rentals']->isNotEmpty()) {
            $vehiclesNeeded = (int) ceil($pax / 5);
            $rentTarget = $targetBudget * 0.25;
            $bestRental = $this->findClosest($allData['rentals'], $rentTarget, $nights * $vehiclesNeeded);
            if ($bestRental) {
                $cost = (float) $bestRental->price * $nights * $vehiclesNeeded;
                if ($cost <= $remainingBudget) {
                    $items[] = [
                        'type' => 'rental',
                        'offer' => $bestRental,
                        'quantity' => $vehiclesNeeded,
                        'days' => $nights,
                        'unit_price' => (float) $bestRental->price,
                        'subtotal' => $cost
                    ];
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                }
            }
        }

        // 3. Destinasi Wisata (Jika dipilih - Garansi 1-2 destinasi)
        $tourismCount = 0;
        if ($allData['tourisms']->isNotEmpty()) {
            $shuffledTourisms = $allData['tourisms']->shuffle();
            foreach ($shuffledTourisms as $tour) {
                $cost = (float) $tour->price * $pax;
                if ($cost <= $remainingBudget && $tourismCount < min(2, $nights * 2)) {
                    $items[] = [
                        'type' => 'tourism',
                        'offer' => $tour,
                        'quantity' => $pax,
                        'days' => 1,
                        'unit_price' => (float) $tour->price,
                        'subtotal' => $cost
                    ];
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                    $tourismCount++;
                }
            }
        }

        // 4. Event & Festival (Jika dipilih - Garansi 1 event jika budget cukup)
        $eventCount = 0;
        if ($allData['events']->isNotEmpty()) {
            $shuffledEvents = $allData['events']->shuffle();
            foreach ($shuffledEvents as $evt) {
                $cost = (float) $evt->price * $pax;
                if ($cost <= $remainingBudget && $eventCount < 1) {
                    $items[] = [
                        'type' => 'event',
                        'offer' => $evt,
                        'quantity' => $pax,
                        'days' => 1,
                        'unit_price' => (float) $evt->price,
                        'subtotal' => $cost
                    ];
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                    $eventCount++;
                }
            }
        }

        // 5. Kuliner / Restoran (Jika dipilih - Garansi 1 voucher makan)
        $culinaryCount = 0;
        if ($allData['culinaries']->isNotEmpty()) {
            $shuffledCulinaries = $allData['culinaries']->shuffle();
            foreach ($shuffledCulinaries as $cul) {
                $cost = (float) $cul->price * $pax;
                if ($cost <= $remainingBudget && $culinaryCount < 1) {
                    $items[] = [
                        'type' => 'culinary',
                        'offer' => $cul,
                        'quantity' => $pax,
                        'days' => 1,
                        'unit_price' => (float) $cul->price,
                        'subtotal' => $cost
                    ];
                    $totalCost += $cost;
                    $remainingBudget -= $cost;
                    $culinaryCount++;
                }
            }
        }

        // =========================================================================
        // PASS 2: BALANCED FILLING (Mengisi sisa budget secara merata tanpa duplikat)
        // =========================================================================
        $usedOfferIds = collect($items)->pluck('offer.id')->toArray();
        $maxTourism = 3 * $nights;
        $maxCulinary = 2 * $nights;
        $maxEvent = 1 * $nights;

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
                $items[] = [
                    'type' => $type,
                    'offer' => $poolItem['offer'],
                    'quantity' => $poolItem['multiplier'],
                    'days' => 1,
                    'unit_price' => (float) $poolItem['offer']->price,
                    'subtotal' => $cost
                ];
                $totalCost += $cost;
                $remainingBudget -= $cost;

                if ($type === 'tourism') $tourismCount++;
                if ($type === 'culinary') $culinaryCount++;
                if ($type === 'event') $eventCount++;
            }
        }

        if (count($items) === 0) {
            return null;
        }

        return [
            'name' => $name,
            'total_cost' => $totalCost,
            'remaining_budget' => $actualBudget - $totalCost,
            'items' => $items
        ];
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
