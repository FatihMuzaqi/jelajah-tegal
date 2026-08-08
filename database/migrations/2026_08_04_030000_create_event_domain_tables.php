<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('catalog_entity_id')->unique()->constrained('catalog_entities')->restrictOnDelete();
            $table->string('event_type', 64);
            $table->string('venue_name', 191)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('registration_deadline')->nullable();
            $table->text('know_before_you_go')->nullable();
            $table->timestamps();
            $table->index(['starts_at', 'ends_at']);
        });
        Schema::create('event_schedules', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('event_id')->constrained()->cascadeOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('title', 191);
            $table->text('description')->nullable();
            $table->string('location_note', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['event_id', 'starts_at']);
        });
        Schema::create('event_ticket_types', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('event_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('catalog_offer_id')->unique()->constrained('catalog_offers')->restrictOnDelete();
            $table->string('name', 150);
            $table->unsignedInteger('quota');
            $table->unsignedInteger('issued_quantity')->default(0);
            $table->timestamp('sale_starts_at')->nullable();
            $table->timestamp('sale_ends_at')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'name']);
        });
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('ticket_number', 40)->unique();
            $table->foreignUlid('event_ticket_type_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('user_id')->constrained()->restrictOnDelete();
            $table->string('qr_token_hash', 64)->unique();
            $table->string('status', 32)->default('issued');
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->index(['event_ticket_type_id', 'status']);
            $table->index(['user_id', 'status']);
        });
        Schema::create('event_ticket_validation_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('event_ticket_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('gatekeeper_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUlid('gatekeeper_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('result', 32);
            $table->string('device_reference', 100)->nullable();
            $table->timestamp('validated_at');
            $table->timestamps();
            $table->index(['event_ticket_id', 'validated_at']);
        });
        DB::statement('ALTER TABLE events ADD CONSTRAINT events_date_check CHECK (ends_at > starts_at AND (registration_deadline IS NULL OR registration_deadline <= ends_at))');
        DB::statement('ALTER TABLE event_schedules ADD CONSTRAINT event_schedules_date_check CHECK (ends_at > starts_at)');
        DB::statement('ALTER TABLE event_ticket_types ADD CONSTRAINT event_ticket_types_quota_check CHECK (quota > 0 AND issued_quantity <= quota)');
    }

    public function down(): void
    {
        Schema::dropIfExists('event_ticket_validation_logs');
        Schema::dropIfExists('event_tickets');
        Schema::dropIfExists('event_ticket_types');
        Schema::dropIfExists('event_schedules');
        Schema::dropIfExists('events');
    }
};

