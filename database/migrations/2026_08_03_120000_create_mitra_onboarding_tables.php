<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra_invitations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->string('email', 191);
            $table->foreignId('intended_role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->char('token_hash', 64)->unique();
            $table->foreignUlid('invited_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['mitra_id', 'email', 'expires_at']);
        });

        Schema::create('mitra_operating_hours', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->unique(['mitra_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_operating_hours');
        Schema::dropIfExists('mitra_invitations');
    }
};
