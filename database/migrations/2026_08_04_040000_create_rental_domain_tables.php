<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_vehicles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('catalog_entity_id')->unique()->constrained('catalog_entities')->restrictOnDelete();
            $table->string('vehicle_type', 64);
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->unsignedSmallInteger('year')->nullable();
            $table->text('plate_number_encrypted')->nullable();
            $table->string('transmission', 32)->nullable();
            $table->unsignedTinyInteger('seats');
            $table->boolean('self_drive_available')->default(true);
            $table->boolean('driver_available')->default(false);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->text('insurance_policy')->nullable();
            $table->text('fuel_policy')->nullable();
            $table->text('pickup_instructions')->nullable();
            $table->string('status', 32)->default('active');
            $table->timestamps();
        });
        Schema::create('rental_rates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('rental_vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('catalog_offer_id')->unique()->constrained('catalog_offers')->restrictOnDelete();
            $table->string('drive_mode', 32);
            $table->string('duration_unit', 32)->default('day');
            $table->unsignedInteger('duration_value')->default(1);
            $table->timestamps();
            $table->unique(['rental_vehicle_id', 'drive_mode', 'duration_unit', 'duration_value'], 'rental_rates_vehicle_mode_duration_unique');
        });
        Schema::create('rental_vehicle_availabilities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('rental_vehicle_id')->constrained()->cascadeOnDelete();
            $table->date('service_date');
            $table->string('status', 32)->default('available');
            $table->decimal('price_override', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['rental_vehicle_id', 'service_date'], 'rental_availability_vehicle_date_unique');
            $table->index(['service_date', 'status']);
        });
        Schema::create('renter_documents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('media_asset_id')->constrained()->restrictOnDelete();
            $table->string('document_type', 32);
            $table->string('document_number_encrypted')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status', 32)->default('pending');
            $table->foreignUlid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'document_type', 'status']);
        });
        Schema::create('rental_bookings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('booking_number', 32)->unique();
            $table->foreignUlid('rental_vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('rental_rate_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('user_id')->constrained()->restrictOnDelete();
            $table->dateTime('pickup_at');
            $table->dateTime('return_at');
            $table->string('pickup_location', 255);
            $table->string('return_location', 255);
            $table->string('drive_mode', 32);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('status', 32)->default('requested');
            $table->foreignUlid('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamps();
            $table->index(['rental_vehicle_id', 'status', 'pickup_at', 'return_at'], 'rental_bookings_overlap_idx');
            $table->index(['user_id', 'status']);
            $table->index(['mitra_id', 'status']);
        });
        Schema::create('rental_booking_documents', function (Blueprint $table) {
            $table->foreignUlid('rental_booking_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('renter_document_id')->constrained()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['rental_booking_id', 'renter_document_id'], 'rental_booking_documents_primary');
        });
        DB::statement('ALTER TABLE rental_vehicles ADD CONSTRAINT rental_vehicles_money_check CHECK (deposit_amount >= 0 AND seats > 0)');
        DB::statement('ALTER TABLE rental_bookings ADD CONSTRAINT rental_bookings_date_check CHECK (return_at > pickup_at)');
        DB::statement('ALTER TABLE rental_bookings ADD CONSTRAINT rental_bookings_money_check CHECK (unit_price >= 0 AND deposit_amount >= 0 AND total_amount >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_booking_documents');
        Schema::dropIfExists('rental_bookings');
        Schema::dropIfExists('renter_documents');
        Schema::dropIfExists('rental_vehicle_availabilities');
        Schema::dropIfExists('rental_rates');
        Schema::dropIfExists('rental_vehicles');
    }
};
