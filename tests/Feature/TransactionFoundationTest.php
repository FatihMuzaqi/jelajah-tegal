<?php

namespace Tests\Feature;

use App\Actions\Checkout\CreateCheckout;
use App\Actions\Checkout\ReleaseOrder;
use App\Actions\Payments\CapturePayment;
use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\EventTicketType;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\Voucher;
use App\Services\Vouchers\VoucherEngine;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TransactionFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FoundationReferenceSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_identical_checkout_is_replayed_without_double_reservation(): void
    {
        [$consumer, , , $type] = $this->eventFixture(quota: 5);
        $payload = $this->eventPayload($type, 'checkout-key-0001', 2);

        $first = app(CreateCheckout::class)->execute($consumer, $payload);
        $replay = app(CreateCheckout::class)->execute($consumer, $payload);

        $this->assertSame($first->id, $replay->id);
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(2, $type->fresh()->reserved_quantity);

        $this->expectException(ValidationException::class);
        app(CreateCheckout::class)->execute($consumer, array_merge($payload, ['quantity' => 3]));
    }

    public function test_row_locked_quota_rejects_second_checkout_and_preserves_counts(): void
    {
        [$consumer, , , $type] = $this->eventFixture(quota: 2);
        app(CreateCheckout::class)->execute($consumer, $this->eventPayload($type, 'quota-key-0001', 2));

        $other = User::factory()->create();
        try {
            app(CreateCheckout::class)->execute($other, $this->eventPayload($type, 'quota-key-0002', 1));
            $this->fail('Checkout melebihi kuota harus ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('orders', 1);
            $this->assertSame(2, $type->fresh()->reserved_quantity);
            $this->assertDatabaseCount('idempotency_keys', 1);
        }
    }

    public function test_voucher_percentage_is_claimed_limited_and_snapshotted(): void
    {
        [$consumer, , $service, $type] = $this->eventFixture(quota: 5);
        $voucher = $this->voucher([
            'code' => 'HEMAT25',
            'discount_type' => 'percentage',
            'flat_amount' => null,
            'percentage_basis_points' => 2500,
            'maximum_discount_amount' => '20000.00',
            'per_user_limit' => 1,
        ]);
        $voucher->serviceTypes()->attach($service->id);
        app(VoucherEngine::class)->claim($consumer, 'HEMAT25');

        $order = app(CreateCheckout::class)->execute($consumer, $this->eventPayload($type, 'voucher-key-0001', 1) + ['voucher_code' => 'HEMAT25']);

        $this->assertSame('20000.00', $order->discount_amount);
        $this->assertSame('80000.00', $order->total_amount);
        $this->assertSame('platform', $order->voucher_snapshot['sponsor']);
        $this->assertSame('20000.00', $order->items->first()->discount_amount);

        $this->expectException(ValidationException::class);
        app(CreateCheckout::class)->execute($consumer, $this->eventPayload($type, 'voucher-key-0002', 1) + ['voucher_code' => 'HEMAT25']);
    }

    public function test_expired_voucher_rolls_back_quota_order_and_idempotency_record(): void
    {
        [$consumer, , , $type] = $this->eventFixture(quota: 3);
        $voucher = $this->voucher(['code' => 'EXPIRED', 'ends_at' => now()->subMinute()]);
        $voucher->claims()->create(['user_id' => $consumer->id, 'status' => 'claimed', 'claimed_at' => now()->subDay(), 'expires_at' => now()->subMinute()]);

        try {
            app(CreateCheckout::class)->execute($consumer, $this->eventPayload($type, 'rollback-key-01', 2) + ['voucher_code' => 'EXPIRED']);
            $this->fail('Voucher kedaluwarsa harus ditolak.');
        } catch (ValidationException) {
            $this->assertSame(0, $type->fresh()->reserved_quantity);
            $this->assertDatabaseCount('orders', 0);
            $this->assertDatabaseCount('idempotency_keys', 0);
        }
    }

    public function test_http_checkout_ignores_client_price_and_marketplace_is_rejected(): void
    {
        [$consumer, , , $type] = $this->eventFixture();
        $consumer->assignRole('consumer');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($consumer)->post(route('consumer.checkout.store'), $this->eventPayload($type, 'http-key-000001', 1) + ['unit_price' => 1])->assertRedirect();
        $this->assertSame('100000.00', Order::firstOrFail()->items()->firstOrFail()->unit_price);

        $this->actingAs($consumer)->post(route('consumer.checkout.store'), [
            'idempotency_key' => 'market-key-0001',
            'domain' => 'marketplace',
            'reference_id' => $type->id,
            'quantity' => 1,
        ])->assertSessionHasErrors('domain');
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_payment_capture_is_exact_idempotent_and_posts_balanced_ledger_and_tickets(): void
    {
        [$consumer, $mitra, , $type] = $this->eventFixture(quota: 3);
        $order = app(CreateCheckout::class)->execute($consumer, $this->eventPayload($type, 'payment-key-001', 2));
        $payment = $order->payments()->firstOrFail();

        $captured = app(CapturePayment::class)->execute($payment, 'PAY-001', '200000.00');
        $replay = app(CapturePayment::class)->execute($captured, 'PAY-001', '200000.00');

        $this->assertSame($captured->id, $replay->id);
        $this->assertSame('paid', $order->fresh()->status->value);
        $this->assertSame(0, $type->fresh()->reserved_quantity);
        $this->assertSame(2, $type->fresh()->issued_quantity);
        $this->assertDatabaseCount('tickets', 2);
        $this->assertDatabaseCount('ledger_journals', 1);
        $debit = (string) DB::table('ledger_lines')->sum('debit_amount');
        $credit = (string) DB::table('ledger_lines')->sum('credit_amount');
        $this->assertSame($debit, $credit);
        $this->assertDatabaseHas('mitra_balances', ['mitra_id' => $mitra->id, 'available_amount' => '200000.00']);

        $this->expectException(ValidationException::class);
        app(CapturePayment::class)->execute($captured, 'PAY-DIFFERENT', '200000.00');
    }

    public function test_wrong_payment_amount_does_not_mutate_order_or_quota(): void
    {
        [$consumer, , , $type] = $this->eventFixture();
        $order = app(CreateCheckout::class)->execute($consumer, $this->eventPayload($type, 'payment-key-002', 1));

        try {
            app(CapturePayment::class)->execute($order->payments()->firstOrFail(), 'PAY-WRONG', '1.00');
            $this->fail('Manipulasi nominal payment harus ditolak.');
        } catch (ValidationException) {
            $this->assertSame('pending_payment', $order->fresh()->status->value);
            $this->assertSame(1, $type->fresh()->reserved_quantity);
            $this->assertDatabaseCount('ledger_journals', 0);
            $this->assertDatabaseCount('tickets', 0);
        }
    }

    public function test_expired_order_releases_quota_and_reactivates_exhausted_voucher(): void
    {
        [$consumer, , , $type] = $this->eventFixture(quota: 1);
        $voucher = $this->voucher(['code' => 'ONCEONLY', 'usage_limit' => 1]);
        app(VoucherEngine::class)->claim($consumer, 'ONCEONLY');
        $order = app(CreateCheckout::class)->execute($consumer, $this->eventPayload($type, 'release-key-001', 1) + ['voucher_code' => 'ONCEONLY']);
        $this->assertSame('exhausted', $voucher->fresh()->status->value);

        app(ReleaseOrder::class)->execute($order, 'expired');

        $this->assertSame(0, $type->fresh()->reserved_quantity);
        $this->assertSame('expired', $order->fresh()->status->value);
        $this->assertSame('active', $voucher->fresh()->status->value);
        $this->assertSame(0, $voucher->fresh()->used_count);
        $this->assertDatabaseHas('voucher_usages', ['order_id' => $order->id, 'status' => 'reversed']);
    }

    private function eventFixture(int $quota = 10): array
    {
        $consumer = User::factory()->create();
        $owner = User::factory()->create();
        $mitra = Mitra::factory()->for($owner, 'owner')->create(['status' => 'active', 'approved_at' => now()]);
        $service = ServiceType::where('code', 'event')->firstOrFail();
        $mitra->features()->create(['service_type_id' => $service->id, 'status' => 'enabled', 'enabled_at' => now()]);
        $entity = CatalogEntity::create([
            'mitra_id' => $mitra->id,
            'service_type_id' => $service->id,
            'name' => 'Festival Checkout',
            'slug' => 'festival-'.str()->lower(str()->random(8)),
            'status' => 'published',
            'published_at' => now(),
        ]);
        $event = $entity->event()->create([
            'event_type' => 'festival',
            'starts_at' => now()->addDays(7),
            'ends_at' => now()->addDays(7)->addHours(4),
            'registration_deadline' => now()->addDays(6),
        ]);
        $offer = CatalogOffer::create([
            'mitra_id' => $mitra->id,
            'catalog_entity_id' => $entity->id,
            'offer_type' => 'event_ticket',
            'sku' => 'EV-'.str()->upper(str()->random(8)),
            'name' => 'Tiket Reguler',
            'price' => '100000.00',
            'status' => 'active',
        ]);
        $type = EventTicketType::create([
            'event_id' => $event->id,
            'catalog_offer_id' => $offer->id,
            'name' => 'Reguler',
            'quota' => $quota,
            'sale_starts_at' => now()->subDay(),
            'sale_ends_at' => now()->addDays(5),
        ]);

        return [$consumer, $mitra, $service, $type];
    }

    private function eventPayload(EventTicketType $type, string $key, int $quantity): array
    {
        return ['idempotency_key' => $key, 'domain' => 'event', 'reference_id' => $type->id, 'quantity' => $quantity];
    }

    private function voucher(array $overrides = []): Voucher
    {
        return Voucher::create(array_merge([
            'created_by' => User::factory()->create()->id,
            'code' => 'PROMO'.str()->upper(str()->random(5)),
            'name' => 'Voucher Test',
            'discount_type' => 'flat',
            'flat_amount' => '10000.00',
            'minimum_order_amount' => '0.00',
            'usage_limit' => 10,
            'per_user_limit' => 2,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'status' => 'active',
        ], $overrides));
    }
}
