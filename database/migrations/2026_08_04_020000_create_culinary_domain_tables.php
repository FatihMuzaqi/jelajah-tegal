<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('culinary_venues', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('catalog_entity_id')->unique()->constrained('catalog_entities')->restrictOnDelete();
            $table->string('venue_type', 32)->default('restaurant');
            $table->boolean('accepts_reservations')->default(false);
            $table->string('phone', 32)->nullable();
            $table->text('reservation_notes')->nullable();
            $table->timestamps();
        });
        Schema::create('culinary_menu_categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('culinary_venue_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['culinary_venue_id', 'name']);
        });
        Schema::create('culinary_menu_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('culinary_venue_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('culinary_menu_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->boolean('is_featured')->default(false);
            $table->string('status', 32)->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['culinary_venue_id', 'name']);
            $table->index(['culinary_venue_id', 'status', 'is_featured']);
        });
        Schema::create('culinary_table_slots', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('culinary_venue_id')->constrained()->cascadeOnDelete();
            $table->date('service_date');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->unsignedInteger('capacity_tables');
            $table->unsignedInteger('capacity_guests');
            $table->string('status', 32)->default('available');
            $table->timestamps();
            $table->unique(['culinary_venue_id', 'service_date', 'starts_at'], 'culinary_slots_venue_date_start_unique');
            $table->index(['service_date', 'status']);
        });
        Schema::create('culinary_reservations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('reservation_number', 32)->unique();
            $table->foreignUlid('culinary_venue_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('culinary_table_slot_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('user_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('party_size');
            $table->string('contact_name', 150);
            $table->string('contact_phone', 32);
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('requested');
            $table->foreignUlid('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamps();
            $table->index(['culinary_table_slot_id', 'status']);
            $table->index(['user_id', 'status', 'created_at']);
        });
        Schema::create('review_replies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('review_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('replied_by')->constrained('users')->restrictOnDelete();
            $table->text('body');
            $table->string('status', 32)->default('published');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('ALTER TABLE culinary_menu_items ADD CONSTRAINT culinary_menu_items_price_check CHECK (price >= 0)');
        DB::statement('ALTER TABLE culinary_table_slots ADD CONSTRAINT culinary_table_slots_capacity_check CHECK (capacity_tables > 0 AND capacity_guests > 0 AND ends_at > starts_at)');
        DB::statement('ALTER TABLE culinary_reservations ADD CONSTRAINT culinary_reservations_party_check CHECK (party_size > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('culinary_reservations');
        Schema::dropIfExists('culinary_table_slots');
        Schema::dropIfExists('culinary_menu_items');
        Schema::dropIfExists('culinary_menu_categories');
        Schema::dropIfExists('culinary_venues');
    }
};
