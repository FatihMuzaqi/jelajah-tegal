<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('snap_token')->nullable()->after('provider_reference');
            $table->string('snap_redirect_url', 500)->nullable()->after('snap_token');
            $table->timestamp('authorized_at')->nullable()->after('paid_at');
            $table->timestamp('cancelled_at')->nullable()->after('failed_at');
            $table->timestamp('refunded_at')->nullable()->after('cancelled_at');
            $table->timestamp('last_synced_at')->nullable()->after('refunded_at');
        });

        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('provider', 32);
            $table->string('provider_event_id', 191);
            $table->foreignUlid('payment_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignUlid('order_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('event_type', 64);
            $table->char('payload_hash', 64);
            $table->decimal('gross_amount', 15, 2)->nullable();
            $table->json('payload');
            $table->string('source', 32)->default('webhook');
            $table->timestamp('received_at');
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'provider_event_id']);
            $table->index(['payment_id', 'received_at']);
            $table->index(['event_type', 'processed_at']);
        });

        Schema::create('payment_reconciliations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('payment_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 32);
            $table->string('local_status', 32);
            $table->string('provider_status', 64)->nullable();
            $table->boolean('matched')->default(false);
            $table->json('provider_payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
            $table->index(['payment_id', 'checked_at']);
            $table->index(['matched', 'checked_at']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedSmallInteger('token_version')->default(1)->after('qr_token_hash');
            $table->foreignUlid('revoked_by')->nullable()->after('used_at')->constrained('users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable()->after('revoked_by');
            $table->string('revocation_reason', 500)->nullable()->after('revoked_at');
        });

        Schema::create('ticket_validation_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('ticket_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignUlid('gatekeeper_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUlid('gatekeeper_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('result', 64);
            $table->string('device_reference', 100)->nullable();
            $table->char('presented_token_hash', 64);
            $table->timestamp('scanned_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['ticket_id', 'scanned_at']);
            $table->index(['gatekeeper_user_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_validation_logs');
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('revoked_by');
            $table->dropColumn(['token_version', 'revoked_at', 'revocation_reason']);
        });
        Schema::dropIfExists('payment_reconciliations');
        Schema::dropIfExists('payment_webhook_events');
        Schema::table('payments', fn (Blueprint $table) => $table->dropColumn(['snap_token', 'snap_redirect_url', 'authorized_at', 'cancelled_at', 'refunded_at', 'last_synced_at']));
    }
};
