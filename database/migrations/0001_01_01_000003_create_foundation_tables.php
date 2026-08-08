<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitras', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('legal_name', 191);
            $table->string('display_name', 191);
            $table->string('slug', 191)->unique();
            $table->foreignUlid('owner_user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 32)->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mitra_members', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('user_id')->constrained()->restrictOnDelete();
            $table->string('status', 32)->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['mitra_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('feature_flags', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('key_name', 191)->unique();
            $table->string('description', 500)->nullable();
            $table->string('status', 32)->default('disabled');
            $table->decimal('rollout_percentage', 5, 2)->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('rules')->nullable();
            $table->foreignUlid('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignUlid('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 100);
            $table->string('auditable_type')->nullable();
            $table->string('auditable_id', 64)->nullable();
            $table->string('request_id', 64)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->json('before_values')->nullable();
            $table->json('after_values')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['mitra_id', 'event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('mitra_members');
        Schema::dropIfExists('mitras');
    }
};
