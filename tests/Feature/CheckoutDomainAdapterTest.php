<?php

namespace Tests\Feature;

use App\Actions\Checkout\CreateCheckout;
use App\Actions\Culinary\CreateCulinaryReservation;
use App\Models\AccommodationRoom;
use App\Models\Availability;
use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\CulinaryTableSlot;
use App\Models\Mitra;
use App\Models\RentalBooking;
use App\Models\RentalRate;
use App\Models\ServiceType;
use App\Models\TourismTicketPackage;
use App\Models\User;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CheckoutDomainAdapterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FoundationReferenceSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_tourism_uses_date_override_and_reserves_exact_quota(): void
    {
        [$mitra, $entity] = $this->entity('tourism');
        $destination = $entity->tourism()->create(['destination_type' => 'nature']);
        $offer = $this->offer($mitra, $entity, 'tourism_ticket', '50000.00');
        $package = TourismTicketPackage::create(['tourism_destination_id' => $destination->id, 'catalog_offer_id' => $offer->id, 'name' => 'Reguler', 'quota_per_day' => 4]);
        $date = now()->addDay()->toDateString();
        $availability = Availability::create(['mitra_id' => $mitra->id, 'catalog_offer_id' => $offer->id, 'service_date' => $date, 'capacity' => 4, 'reserved_quantity' => 0, 'price_override' => '60000.00', 'status' => 'available']);

        $order = app(CreateCheckout::class)->execute(User::factory()->create(), [
            'idempotency_key' => 'tourism-key-001', 'domain' => 'tourism', 'reference_id' => $package->id, 'quantity' => 2, 'service_date' => $date,
        ]);

        $this->assertSame('120000.00', $order->subtotal);
        $this->assertSame('60000.00', $order->items->first()->unit_price);
        $this->assertSame(2, $availability->fresh()->reserved_quantity);
    }

    public function test_accommodation_locks_every_night_and_snapshots_daily_prices(): void
    {
        [$mitra, $entity] = $this->entity('accommodation');
        $accommodation = $entity->accommodation()->create(['property_type' => 'hotel']);
        $offer = $this->offer($mitra, $entity, 'room', '250000.00');
        $room = AccommodationRoom::create(['accommodation_id' => $accommodation->id, 'catalog_offer_id' => $offer->id, 'name' => 'Deluxe', 'room_type' => 'deluxe', 'capacity_adults' => 2, 'total_units' => 2, 'min_stay_nights' => 1, 'max_stay_nights' => 7, 'status' => 'active']);
        $start = now()->addDays(2)->startOfDay();
        foreach ([0 => '250000.00', 1 => '300000.00'] as $offset => $price) {
            Availability::create(['mitra_id' => $mitra->id, 'catalog_offer_id' => $offer->id, 'service_date' => $start->copy()->addDays($offset), 'capacity' => 2, 'reserved_quantity' => 0, 'price_override' => $price, 'status' => 'available']);
        }

        $order = app(CreateCheckout::class)->execute(User::factory()->create(), [
            'idempotency_key' => 'room-key-000001', 'domain' => 'accommodation', 'reference_id' => $room->id, 'quantity' => 1,
            'start_date' => $start->toDateString(), 'end_date' => $start->copy()->addDays(2)->toDateString(), 'adults' => 2,
        ]);

        $this->assertSame('550000.00', $order->subtotal);
        $this->assertCount(2, $order->items->first()->holds);
        $this->assertSame(['250000.00', '300000.00'], array_values($order->items->first()->details['daily_prices']));
        $this->assertSame(2, (int) Availability::sum('reserved_quantity'));
    }

    public function test_culinary_reservation_is_user_bound_and_can_only_have_one_order(): void
    {
        [$mitra, $entity] = $this->entity('culinary');
        $venue = $entity->culinary()->create(['venue_type' => 'restaurant', 'accepts_reservations' => true]);
        $slot = CulinaryTableSlot::create(['culinary_venue_id' => $venue->id, 'service_date' => now()->addDay(), 'starts_at' => '18:00', 'ends_at' => '20:00', 'capacity_tables' => 2, 'capacity_guests' => 6, 'status' => 'available']);
        $consumer = User::factory()->create();
        $reservation = app(CreateCulinaryReservation::class)->execute($slot, $consumer, ['party_size' => 2, 'contact_name' => 'Budi', 'contact_phone' => '081234567890']);

        try {
            app(CreateCheckout::class)->execute(User::factory()->create(), ['idempotency_key' => 'culinary-bad-01', 'domain' => 'culinary', 'reference_id' => $reservation->id]);
            $this->fail('Reservasi pengguna lain harus ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('orders', 0);
        }

        $order = app(CreateCheckout::class)->execute($consumer, ['idempotency_key' => 'culinary-good-1', 'domain' => 'culinary', 'reference_id' => $reservation->id]);
        $this->assertSame('paid', $order->status->value);
        $this->assertSame('0.00', $order->total_amount);

        $this->expectException(ValidationException::class);
        app(CreateCheckout::class)->execute($consumer, ['idempotency_key' => 'culinary-good-2', 'domain' => 'culinary', 'reference_id' => $reservation->id]);
    }

    public function test_rental_uses_approved_server_snapshot_and_rejects_cross_user_or_duplicate(): void
    {
        [$mitra, $entity] = $this->entity('rental');
        $vehicle = $entity->rentalVehicle()->create(['vehicle_type' => 'car', 'brand' => 'Toyota', 'model' => 'Avanza', 'plate_number' => 'G 9999 ZZ', 'seats' => 7, 'self_drive_available' => true, 'driver_available' => false, 'deposit_amount' => '500000.00', 'status' => 'active']);
        $offer = $this->offer($mitra, $entity, 'rental_rate', '350000.00');
        $rate = RentalRate::create(['rental_vehicle_id' => $vehicle->id, 'catalog_offer_id' => $offer->id, 'drive_mode' => 'self_drive', 'duration_unit' => 'day', 'duration_value' => 1]);
        $consumer = User::factory()->create();
        $booking = RentalBooking::create(['booking_number' => 'RNT-TEST-001', 'rental_vehicle_id' => $vehicle->id, 'rental_rate_id' => $rate->id, 'mitra_id' => $mitra->id, 'user_id' => $consumer->id, 'pickup_at' => now()->addDay(), 'return_at' => now()->addDays(2), 'pickup_location' => 'Tegal', 'return_location' => 'Tegal', 'drive_mode' => 'self_drive', 'unit_price' => '350000.00', 'deposit_amount' => '500000.00', 'total_amount' => '850000.00', 'status' => 'approved']);

        try {
            app(CreateCheckout::class)->execute(User::factory()->create(), ['idempotency_key' => 'rental-bad-0001', 'domain' => 'rental', 'reference_id' => $booking->id]);
            $this->fail('Booking pengguna lain harus ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('orders', 0);
        }

        $order = app(CreateCheckout::class)->execute($consumer, ['idempotency_key' => 'rental-good-001', 'domain' => 'rental', 'reference_id' => $booking->id]);
        $this->assertSame('850000.00', $order->subtotal);
        $this->assertSame('850000.00', $order->items->first()->unit_price);

        $this->expectException(ValidationException::class);
        app(CreateCheckout::class)->execute($consumer, ['idempotency_key' => 'rental-good-002', 'domain' => 'rental', 'reference_id' => $booking->id]);
    }

    private function entity(string $domain): array
    {
        $owner = User::factory()->create();
        $mitra = Mitra::factory()->for($owner, 'owner')->create(['status' => 'active', 'approved_at' => now()]);
        $service = ServiceType::where('code', $domain)->firstOrFail();
        $mitra->features()->create(['service_type_id' => $service->id, 'status' => 'enabled', 'enabled_at' => now()]);
        $entity = CatalogEntity::create(['mitra_id' => $mitra->id, 'service_type_id' => $service->id, 'name' => ucfirst($domain).' Checkout', 'slug' => $domain.'-'.str()->lower(str()->random(8)), 'description' => 'Data aktif untuk pengujian checkout.', 'status' => 'published', 'published_at' => now()]);

        return [$mitra, $entity];
    }

    private function offer(Mitra $mitra, CatalogEntity $entity, string $type, string $price): CatalogOffer
    {
        return CatalogOffer::create(['mitra_id' => $mitra->id, 'catalog_entity_id' => $entity->id, 'offer_type' => $type, 'sku' => str()->upper(substr($type, 0, 3)).'-'.str()->upper(str()->random(8)), 'name' => 'Offer Checkout', 'price' => $price, 'status' => 'active']);
    }
}
