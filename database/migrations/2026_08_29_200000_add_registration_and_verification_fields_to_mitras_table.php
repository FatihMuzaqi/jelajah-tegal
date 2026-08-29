<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->text('owner_nik_encrypted')->nullable()->after('owner_user_id');
            $table->foreignId('service_type_id')->nullable()->after('category')->constrained('service_types')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('service_type_id')->constrained('categories')->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedSmallInteger('founded_year')->nullable()->after('longitude');
            $table->string('nib', 50)->nullable()->after('founded_year');
            $table->string('npwp', 50)->nullable()->after('nib');
            $table->boolean('is_verified')->default(false)->after('status');
            $table->text('rejection_reason')->nullable()->after('is_verified');
            $table->text('admin_notes')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropForeign(['service_type_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'owner_nik_encrypted',
                'service_type_id',
                'category_id',
                'latitude',
                'longitude',
                'founded_year',
                'nib',
                'npwp',
                'is_verified',
                'rejection_reason',
                'admin_notes',
            ]);
        });
    }
};
