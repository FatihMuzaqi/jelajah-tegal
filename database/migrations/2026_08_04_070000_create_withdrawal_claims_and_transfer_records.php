<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_claims', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('withdrawal_number', 32)->unique();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('mitra_bank_account_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('submitted_by')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('IDR');
            $table->string('status', 32)->default('submitted');
            $table->string('idempotency_key', 191);
            $table->char('request_fingerprint', 64);
            $table->json('bank_snapshot');
            $table->text('notes')->nullable();
            $table->foreignUlid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUlid('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignUlid('processing_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processing_at')->nullable();
            $table->foreignUlid('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->foreignUlid('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->unique(['mitra_id', 'submitted_by', 'idempotency_key'], 'withdrawals_mitra_user_idem_unique');
            $table->index(['mitra_id', 'status', 'created_at']);
            $table->index(['status', 'created_at']);
        });
        DB::statement("ALTER TABLE withdrawal_claims ADD CONSTRAINT withdrawal_amount_check CHECK (amount > 0)");
        DB::statement("ALTER TABLE withdrawal_claims ADD CONSTRAINT withdrawal_status_check CHECK (status IN ('submitted','under_review','approved','rejected','processing','paid','cancelled'))");

        Schema::create('withdrawal_transfers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('withdrawal_claim_id')->unique()->constrained()->restrictOnDelete();
            $table->string('transfer_reference', 191)->unique();
            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('IDR');
            $table->json('bank_snapshot');
            $table->foreignUlid('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('transferred_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE withdrawal_transfers ADD CONSTRAINT withdrawal_transfer_amount_check CHECK (amount > 0)');

        Schema::table('ledger_journals', function (Blueprint $table) {
            $table->foreignUlid('withdrawal_claim_id')->nullable()->after('payment_id')->constrained()->restrictOnDelete();
            $table->index(['withdrawal_claim_id', 'effective_at']);
        });
        DB::statement('ALTER TABLE mitra_balances ADD CONSTRAINT mitra_balances_non_negative_check CHECK (available_amount >= 0 AND held_amount >= 0)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE mitra_balances DROP CHECK mitra_balances_non_negative_check');
        Schema::table('ledger_journals', fn (Blueprint $table) => $table->dropConstrainedForeignId('withdrawal_claim_id'));
        Schema::dropIfExists('withdrawal_transfers');
        Schema::dropIfExists('withdrawal_claims');
    }
};
