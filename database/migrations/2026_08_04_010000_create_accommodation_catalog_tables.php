<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('catalog_entity_id')->unique()->constrained('catalog_entities')->restrictOnDelete();
            $table->string('property_type', 32);
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->unsignedTinyInteger('star_rating')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE accommodations ADD CONSTRAINT accommodations_star_check CHECK (star_rating IS NULL OR star_rating BETWEEN 1 AND 5)');

        Schema::create('accommodation_rooms', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('accommodation_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('catalog_offer_id')->unique()->constrained('catalog_offers')->restrictOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('room_type', 64);
            $table->string('kind', 32)->default('room');
            $table->unsignedSmallInteger('capacity_adults');
            $table->unsignedSmallInteger('capacity_children')->default(0);
            $table->unsignedSmallInteger('total_units');
            $table->unsignedSmallInteger('plot_count')->nullable();
            $table->unsignedSmallInteger('min_stay_nights')->nullable();
            $table->unsignedSmallInteger('max_stay_nights')->nullable();
            $table->unsignedSmallInteger('advance_booking_days')->nullable();
            $table->text('availability_notes')->nullable();
            $table->string('status', 32)->default('draft');
            $table->json('bed_config')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['accommodation_id', 'name'], 'accommodation_rooms_property_name_unique');
            $table->index(['accommodation_id', 'status']);
        });
        DB::statement('ALTER TABLE accommodation_rooms ADD CONSTRAINT accommodation_rooms_capacity_check CHECK (capacity_adults > 0 AND total_units > 0)');
        DB::statement('ALTER TABLE accommodation_rooms ADD CONSTRAINT accommodation_rooms_stay_check CHECK (min_stay_nights IS NULL OR max_stay_nights IS NULL OR max_stay_nights >= min_stay_nights)');

        Schema::create('accommodation_room_media', function (Blueprint $table) {
            $table->foreignUlid('accommodation_room_id')->constrained('accommodation_rooms')->cascadeOnDelete();
            $table->foreignUlid('media_asset_id')->constrained()->restrictOnDelete();
            $table->string('role', 32)->default('gallery');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('caption', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['accommodation_room_id', 'media_asset_id'], 'accommodation_room_media_primary');
            $table->unique(['accommodation_room_id', 'role', 'sort_order'], 'accommodation_room_media_order_unique');
        });

        Schema::create('accommodation_room_facilities', function (Blueprint $table) {
            $table->foreignUlid('accommodation_room_id')->constrained('accommodation_rooms')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->string('notes', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['accommodation_room_id', 'facility_id'], 'accommodation_room_facilities_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_room_facilities');
        Schema::dropIfExists('accommodation_room_media');
        Schema::dropIfExists('accommodation_rooms');
        Schema::dropIfExists('accommodations');
    }
};
