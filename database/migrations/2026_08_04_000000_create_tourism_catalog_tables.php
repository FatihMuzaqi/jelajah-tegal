<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_entities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 191);
            $table->string('slug', 191);
            $table->longText('description')->nullable();
            $table->text('address')->nullable();
            $table->string('status', 32)->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['mitra_id', 'service_type_id', 'slug']);
            $table->unique(['service_type_id', 'slug']);
            $table->index(['service_type_id', 'status', 'published_at']);
            $table->index(['mitra_id', 'status', 'updated_at']);
            $table->index(['category_id', 'status']);
            $table->index(['region_id', 'status']);
        });

        Schema::create('catalog_locations', function (Blueprint $table) {
            $table->foreignUlid('catalog_entity_id')->primary()->constrained('catalog_entities')->cascadeOnDelete();
            $table->geometry('location', 'point', 4326);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
            $table->spatialIndex('location');
        });
        DB::statement('ALTER TABLE catalog_locations ADD CONSTRAINT catalog_locations_latitude_check CHECK (latitude BETWEEN -90 AND 90)');
        DB::statement('ALTER TABLE catalog_locations ADD CONSTRAINT catalog_locations_longitude_check CHECK (longitude BETWEEN -180 AND 180)');

        Schema::create('tourism_destinations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('catalog_entity_id')->unique()->constrained('catalog_entities')->restrictOnDelete();
            $table->string('destination_type', 32);
            $table->unsignedInteger('visit_duration_minutes')->nullable();
            $table->string('badge', 64)->nullable();
            $table->boolean('is_hidden_gem')->default(false);
            $table->timestamps();
        });

        Schema::create('catalog_offers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('catalog_entity_id')->constrained()->restrictOnDelete();
            $table->string('offer_type', 32);
            $table->string('sku', 100)->nullable();
            $table->string('name', 191);
            $table->char('currency', 3)->default('IDR');
            $table->decimal('price', 15, 2);
            $table->string('status', 32)->default('draft');
            $table->timestamp('purchasable_from')->nullable();
            $table->timestamp('purchasable_until')->nullable();
            $table->unsignedInteger('min_quantity')->default(1);
            $table->unsignedInteger('max_quantity')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['mitra_id', 'sku']);
            $table->index(['catalog_entity_id', 'status']);
        });

        Schema::create('tourism_ticket_packages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tourism_destination_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('catalog_offer_id')->unique()->constrained('catalog_offers')->restrictOnDelete();
            $table->string('name', 150);
            $table->unsignedInteger('quota_per_day')->nullable();
            $table->timestamps();
            $table->unique(['tourism_destination_id', 'name']);
        });

        Schema::create('availabilities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('catalog_offer_id')->constrained('catalog_offers')->restrictOnDelete();
            $table->date('service_date');
            $table->time('starts_at')->nullable();
            $table->time('slot_key')->storedAs('COALESCE(starts_at, \'00:00:00\')');
            $table->time('ends_at')->nullable();
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->decimal('price_override', 15, 2)->nullable();
            $table->string('status', 32)->default('available');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['catalog_offer_id', 'service_date', 'slot_key']);
            $table->index(['catalog_offer_id', 'service_date', 'status']);
        });
        DB::statement('ALTER TABLE availabilities ADD CONSTRAINT availabilities_capacity_check CHECK (reserved_quantity <= capacity)');

        Schema::create('catalog_media', function (Blueprint $table) {
            $table->foreignUlid('catalog_entity_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('media_asset_id')->constrained()->restrictOnDelete();
            $table->string('role', 32)->default('gallery');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('caption', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['catalog_entity_id', 'media_asset_id']);
            $table->unique(['catalog_entity_id', 'role', 'sort_order']);
        });

        Schema::create('catalog_operating_hours', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('catalog_entity_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->unsignedTinyInteger('sequence')->default(1);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->unique(['catalog_entity_id', 'weekday', 'sequence'], 'catalog_hours_entity_day_seq_unique');
        });
        DB::statement('ALTER TABLE catalog_operating_hours ADD CONSTRAINT catalog_hours_weekday_check CHECK (weekday BETWEEN 1 AND 7)');

        Schema::create('catalog_facilities', function (Blueprint $table) {
            $table->foreignUlid('catalog_entity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->string('notes', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['catalog_entity_id', 'facility_id']);
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('catalog_entity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'catalog_entity_id']);
            $table->index(['catalog_entity_id', 'created_at']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('catalog_entity_id')->constrained()->restrictOnDelete();
            $table->ulid('order_item_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title', 191)->nullable();
            $table->text('body')->nullable();
            $table->string('status', 32)->default('pending');
            $table->foreignUlid('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'catalog_entity_id']);
            $table->index(['catalog_entity_id', 'status', 'created_at']);
        });
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_rating_check CHECK (rating BETWEEN 1 AND 5)');

        Schema::create('moderation_reports', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('reporter_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('mitra_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignUlid('catalog_entity_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignUlid('review_id')->nullable()->constrained('reviews')->restrictOnDelete();
            $table->string('reason_code', 64);
            $table->text('description')->nullable();
            $table->string('status', 32)->default('open');
            $table->foreignUlid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'assigned_to', 'created_at']);
        });
        DB::statement('ALTER TABLE moderation_reports ADD CONSTRAINT moderation_reports_one_target_check CHECK ((catalog_entity_id IS NOT NULL) + (review_id IS NOT NULL) = 1)');

        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('report_id')->constrained('moderation_reports')->restrictOnDelete();
            $table->foreignUlid('actor_user_id')->constrained('users')->restrictOnDelete();
            $table->string('action_type', 64);
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32)->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_actions');
        Schema::dropIfExists('moderation_reports');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('catalog_facilities');
        Schema::dropIfExists('catalog_operating_hours');
        Schema::dropIfExists('catalog_media');
        Schema::dropIfExists('availabilities');
        Schema::dropIfExists('tourism_ticket_packages');
        Schema::dropIfExists('catalog_offers');
        Schema::dropIfExists('tourism_destinations');
        Schema::dropIfExists('catalog_locations');
        Schema::dropIfExists('catalog_entities');
    }
};
