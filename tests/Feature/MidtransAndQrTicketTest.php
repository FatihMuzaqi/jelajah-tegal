<?php

namespace Tests\Feature;

use App\Actions\Checkout\CreateCheckout;
use App\Actions\Checkout\ExpirePendingPayments;
use App\Actions\Payments\CreateSnapTransaction;
use App\Actions\Payments\SyncPaymentStatus;
use App\Actions\Tickets\RevokeTicket;
use App\Actions\Tickets\ValidateQrTicket;
use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\EventTicketType;
use App\Models\GatekeeperAssignment;
use App\Models\Mitra;
use App\Models\ServiceType;
use App\Models\Ticket;
use App\Models\User;
use App\Services\Payments\MidtransConfiguration;
use App\Support\TicketToken;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use LogicException;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MidtransAndQrTicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FoundationReferenceSeeder::class);
        setPermissionsTeamId(null);
        config([
            'midtrans.enabled' => true,
            'midtrans.production' => false,
            'midtrans.server_key' => 'server-test-key',
            'midtrans.client_key' => 'client-test-key',
            'midtrans.snap_base_url' => 'https://snap.test',
            'midtrans.api_base_url' => 'https://api.test',
        ]);
    }

    public function test_snap_transaction_uses_server_price_and_is_idempotent(): void
    {
        [$consumer, , , , $order] = $this->pendingOrder(1);
        Http::fake(['https://snap.test/snap/v1/transactions' => Http::response(['token' => 'snap-token', 'redirect_url' => 'https://snap.test/pay/token'], 201)]);

        $payment = app(CreateSnapTransaction::class)->execute($order->payments()->firstOrFail());
        $again = app(CreateSnapTransaction::class)->execute($payment);

        $this->assertSame($payment->id, $again->id);
        $this->assertSame('midtrans', $payment->provider);
        $this->assertSame('snap-token', $payment->snap_token);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['transaction_details']['order_id'] === $order->order_number && $request['transaction_details']['gross_amount'] === 100000);
    }

    public function test_valid_and_duplicate_webhook_mints_ticket_once(): void
    {
        [, , , $type, $order] = $this->pendingOrder(1);
        $payload = $this->notification($order->order_number, '100000.00', 'settlement', 'notification-001');

        $this->postJson(route('api.midtrans.webhook'), $payload)->assertOk()->assertJson(['accepted' => true]);
        $this->postJson(route('api.midtrans.webhook'), $payload)->assertOk()->assertJson(['accepted' => true]);

        $this->assertSame('paid', $order->fresh()->payment_status->value);
        $this->assertSame(1, $type->fresh()->issued_quantity);
        $this->assertSame(0, $type->fresh()->reserved_quantity);
        $this->assertDatabaseCount('payment_webhook_events', 1);
        $this->assertDatabaseCount('ledger_journals', 1);
        $this->assertDatabaseCount('tickets', 1);
        $this->assertSame('unused', Ticket::firstOrFail()->status);
    }

    public function test_invalid_signature_is_rejected_without_event_or_payment_mutation(): void
    {
        [, , , , $order] = $this->pendingOrder(1);
        $payload = $this->notification($order->order_number, '100000.00', 'settlement', 'notification-invalid');
        $payload['signature_key'] = str_repeat('0', 128);

        $this->postJson(route('api.midtrans.webhook'), $payload)->assertUnauthorized();
        $this->assertDatabaseCount('payment_webhook_events', 0);
        $this->assertSame('pending', $order->payments()->firstOrFail()->fresh()->status->value);
    }

    public function test_refund_revokes_ticket_and_posts_idempotent_ledger_reversal(): void
    {
        [, $mitra, , , $order] = $this->pendingOrder(1);
        $this->postJson(route('api.midtrans.webhook'), $this->notification($order->order_number, '100000.00', 'settlement', 'payment-before-refund'))->assertOk();
        $refund = $this->notification($order->order_number, '100000.00', 'refund', 'refund-event-001');

        $this->postJson(route('api.midtrans.webhook'), $refund)->assertOk();
        $this->postJson(route('api.midtrans.webhook'), $refund)->assertOk();

        $this->assertSame('refunded', $order->payments()->firstOrFail()->fresh()->status->value);
        $this->assertSame('refunded', $order->fresh()->status->value);
        $this->assertSame('revoked', Ticket::firstOrFail()->status);
        $this->assertDatabaseCount('ledger_journals', 2);
        $this->assertDatabaseHas('mitra_balances', ['mitra_id' => $mitra->id, 'available_amount' => '0.00']);
    }

    public function test_amount_mismatch_is_recorded_and_rejected(): void
    {
        [, , , $type, $order] = $this->pendingOrder(1);
        $payload = $this->notification($order->order_number, '1.00', 'settlement', 'notification-amount');

        $this->postJson(route('api.midtrans.webhook'), $payload)->assertUnprocessable();

        $this->assertDatabaseHas('payment_webhook_events', ['provider_event_id' => 'notification-amount', 'gross_amount' => '1.00']);
        $this->assertNotNull(\App\Models\PaymentWebhookEvent::firstOrFail()->processing_error);
        $this->assertSame('pending_payment', $order->fresh()->status->value);
        $this->assertSame(1, $type->fresh()->reserved_quantity);
    }

    public function test_expiration_worker_cancels_payment_releases_quota_audits_and_notifies(): void
    {
        [$consumer, , , $type, $order] = $this->pendingOrder(2);
        $order->update(['expires_at' => now()->subSecond()]);

        $this->assertSame(1, app(ExpirePendingPayments::class)->execute());

        $this->assertSame('expired', $order->fresh()->status->value);
        $this->assertSame('expired', $order->payments()->firstOrFail()->fresh()->status->value);
        $this->assertSame(0, $type->fresh()->reserved_quantity);
        $this->assertDatabaseHas('audit_logs', ['event' => 'checkout.expired']);
        $this->assertDatabaseHas('notifications', ['user_id' => $consumer->id, 'type' => 'order.expired']);
    }

    public function test_manual_sync_records_reconciliation_and_provider_event(): void
    {
        [, , , , $order] = $this->pendingOrder(1);
        $payment = $order->payments()->firstOrFail();
        $payload = $this->notification($order->order_number, '100000.00', 'pending', 'sync-event-001');
        Http::fake(['https://api.test/v2/*/status' => Http::response($payload)]);

        $reconciliation = app(SyncPaymentStatus::class)->execute($payment, null, 'manual');

        $this->assertTrue($reconciliation->matched);
        $this->assertSame('pending', $reconciliation->provider_status);
        $this->assertNotNull($payment->fresh()->last_synced_at);
        $this->assertDatabaseHas('payment_webhook_events', ['provider_event_id' => 'sync-event-001', 'source' => 'manual']);
    }

    public function test_qr_is_generated_and_duplicate_scan_is_prevented_with_log(): void
    {
        [$consumer, $mitra, , , $order] = $this->pendingOrder(1);
        $this->postJson(route('api.midtrans.webhook'), $this->notification($order->order_number, '100000.00', 'settlement', 'notification-qr'))->assertOk();
        $ticket = Ticket::firstOrFail();
        $ticket->update(['valid_from' => now()->subMinute()]);
        $token = TicketToken::for($ticket->id, $ticket->token_version);
        $consumer->assignRole('consumer');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAs($consumer)->get(route('consumer.tickets.qr', $ticket))->assertOk()->assertHeader('content-type', 'image/svg+xml')->assertSee('<svg', false);
        [$gatekeeper, $assignment] = $this->gatekeeper($mitra);

        app(ValidateQrTicket::class)->execute($token, $gatekeeper, $mitra->id, 'scanner-1');
        $this->assertSame('used', $ticket->fresh()->status);
        try {
            app(ValidateQrTicket::class)->execute($token, $gatekeeper, $mitra->id, 'scanner-1');
            $this->fail('Scan kedua harus ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('ticket_validation_logs', ['ticket_id' => $ticket->id, 'gatekeeper_assignment_id' => $assignment->id, 'result' => 'duplicate']);
        }
    }

    public function test_unauthorized_gatekeeper_and_revoked_ticket_are_rejected_and_logged(): void
    {
        [, $mitra, , , $order] = $this->pendingOrder(1);
        $this->postJson(route('api.midtrans.webhook'), $this->notification($order->order_number, '100000.00', 'settlement', 'notification-revoke'))->assertOk();
        $ticket = Ticket::firstOrFail();
        $token = TicketToken::for($ticket->id, $ticket->token_version);
        $outsider = User::factory()->create();
        try {
            app(ValidateQrTicket::class)->execute($token, $outsider, $mitra->id);
            $this->fail('Gatekeeper tanpa assignment harus ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('ticket_validation_logs', ['ticket_id' => $ticket->id, 'gatekeeper_user_id' => $outsider->id, 'result' => 'unauthorized']);
        }

        [$gatekeeper] = $this->gatekeeper($mitra);
        app(RevokeTicket::class)->execute($ticket, $mitra->owner, 'Refund operasional');
        try {
            app(ValidateQrTicket::class)->execute($token, $gatekeeper, $mitra->id);
            $this->fail('Tiket revoked harus ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('ticket_validation_logs', ['ticket_id' => $ticket->id, 'result' => 'revoked']);
        }
    }

    public function test_disabled_or_incomplete_configuration_fails_fast_without_dummy_key(): void
    {
        config(['midtrans.enabled' => false, 'midtrans.server_key' => null, 'midtrans.client_key' => null]);
        $this->expectException(LogicException::class);
        app(MidtransConfiguration::class)->assertReady();
    }

    private function pendingOrder(int $quantity): array
    {
        $consumer = User::factory()->create();
        $owner = User::factory()->create();
        $mitra = Mitra::factory()->for($owner, 'owner')->create(['status' => 'active', 'approved_at' => now()]);
        $service = ServiceType::where('code', 'event')->firstOrFail();
        $mitra->features()->create(['service_type_id' => $service->id, 'status' => 'enabled', 'enabled_at' => now()]);
        $entity = CatalogEntity::create(['mitra_id' => $mitra->id, 'service_type_id' => $service->id, 'name' => 'Event Midtrans', 'slug' => 'event-'.str()->lower(str()->random(8)), 'status' => 'published', 'published_at' => now()]);
        $event = $entity->event()->create(['event_type' => 'festival', 'starts_at' => now()->addWeek(), 'ends_at' => now()->addWeek()->addHours(2), 'registration_deadline' => now()->addDays(6)]);
        $offer = CatalogOffer::create(['mitra_id' => $mitra->id, 'catalog_entity_id' => $entity->id, 'offer_type' => 'event_ticket', 'sku' => 'MID-'.str()->upper(str()->random(8)), 'name' => 'Tiket Midtrans', 'price' => '100000.00', 'status' => 'active']);
        $type = EventTicketType::create(['event_id' => $event->id, 'catalog_offer_id' => $offer->id, 'name' => 'Reguler', 'quota' => 10, 'sale_starts_at' => now()->subDay(), 'sale_ends_at' => now()->addDays(5)]);
        $order = app(CreateCheckout::class)->execute($consumer, ['idempotency_key' => 'mid-'.str()->random(16), 'domain' => 'event', 'reference_id' => $type->id, 'quantity' => $quantity]);
        return [$consumer, $mitra, $service, $type, $order];
    }

    private function notification(string $orderNumber, string $amount, string $status, string $eventId): array
    {
        $payload = ['notification_id' => $eventId, 'order_id' => $orderNumber, 'transaction_id' => 'trx-'.$eventId, 'transaction_status' => $status, 'status_code' => '200', 'gross_amount' => $amount, 'fraud_status' => 'accept'];
        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'server-test-key');
        return $payload;
    }

    private function gatekeeper(Mitra $mitra): array
    {
        $user = User::factory()->create();
        $member = $mitra->members()->create(['user_id' => $user->id, 'status' => 'active', 'joined_at' => now()]);
        $assignment = GatekeeperAssignment::create(['mitra_id' => $mitra->id, 'member_id' => $member->id, 'scope_type' => 'mitra', 'valid_from' => now()->subMinute(), 'valid_until' => now()->addDay(), 'assigned_by' => $mitra->owner_user_id]);
        return [$user, $assignment];
    }
}
