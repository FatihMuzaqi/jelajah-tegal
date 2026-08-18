<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\CatalogEntity;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripNavigatorController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Ambil seluruh pesanan lunas / terbayar milik consumer
        $paidOrders = Order::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereIn('payment_status', ['paid', 'settlement', 'capture'])
                    ->orWhereIn('status', ['paid', 'confirmed', 'completed']);
            })
            ->with([
                'mitra',
                'items.tickets',
                'items.catalogOffer.catalogEntity.location',
                'items.catalogOffer.catalogEntity.serviceType',
                'items.catalogOffer.catalogEntity.category',
                'items.catalogOffer.catalogEntity.region',
                'items.catalogOffer.catalogEntity.media',
            ])
            ->latest('paid_at')
            ->get();

        $destinations = collect();
        $processedEntityIds = [];

        foreach ($paidOrders as $order) {
            foreach ($order->items as $item) {
                $entity = null;

                // Coba ambil dari relasi catalogOffer
                if ($item->catalogOffer?->catalogEntity) {
                    $entity = $item->catalogOffer->catalogEntity;
                } elseif ($item->reference_id) {
                    // Fallback jika direct reference_id
                    $entity = CatalogEntity::with(['location', 'serviceType', 'category', 'region', 'media'])->find($item->reference_id);
                }

                // Jika entitas valid dan belum dimasukkan (atau beda tanggal pesanan)
                if ($entity && !in_array($entity->id, $processedEntityIds, true)) {
                    $loc = $entity->location;

                    // Fallback koordinat jika belum terisi di DB (koordinat default Tegal)
                    $lat = $loc?->latitude ? (float) $loc->latitude : -6.8797000;
                    $lng = $loc?->longitude ? (float) $loc->longitude : 109.1256000;

                    $serviceCode = $entity->serviceType?->code ?? 'tourism';
                    $serviceLabel = match ($serviceCode) {
                        'accommodation' => 'Akomodasi',
                        'culinary' => 'Kuliner',
                        'event' => 'Event',
                        'rental' => 'Rental',
                        default => 'Wisata',
                    };

                    $icon = match ($serviceCode) {
                        'accommodation' => 'fa-solid fa-hotel',
                        'culinary' => 'fa-solid fa-utensils',
                        'event' => 'fa-solid fa-calendar-days',
                        'rental' => 'fa-solid fa-car',
                        default => 'fa-solid fa-umbrella-beach',
                    };

                    $badgeColor = match ($serviceCode) {
                        'accommodation' => '#8b5cf6', // purple
                        'culinary' => '#f59e0b',    // amber
                        'event' => '#ec4899',       // pink
                        'rental' => '#3b82f6',      // blue
                        default => '#047857',       // emerald
                    };

                    $coverMedia = $entity->media->where('pivot.role', 'cover')->first() ?? $entity->media->first();
                    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->object_key) : null;

                    $ticket = $item->tickets->first();
                    $ticketQrUrl = $ticket ? route('consumer.tickets.qr', $ticket) : null;

                    $destinations->push([
                        'id' => $entity->id,
                        'name' => $entity->name,
                        'slug' => $entity->slug,
                        'service_code' => $serviceCode,
                        'service_label' => $serviceLabel,
                        'icon' => $icon,
                        'badge_color' => $badgeColor,
                        'category_name' => $entity->category?->name ?? $serviceLabel,
                        'region_name' => $entity->region?->name ?? 'Tegal',
                        'address' => $entity->address ?: 'Wilayah Kabupaten / Kota Tegal',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'cover_url' => $coverUrl,
                        'order_number' => $order->order_number,
                        'booking_date' => $item->booking_date?->translatedFormat('d M Y') ?? $order->created_at->translatedFormat('d M Y'),
                        'ticket_qr_url' => $ticketQrUrl,
                        'mitra_name' => $order->mitra?->display_name ?? 'Mitra Jelajah Tegal',
                    ]);

                    $processedEntityIds[] = $entity->id;
                }
            }
        }

        return view('consumer.trip-navigator', [
            'destinations' => $destinations,
            'totalPaid' => $destinations->count(),
            'destinationsJson' => $destinations->toJson(),
        ]);
    }
}
