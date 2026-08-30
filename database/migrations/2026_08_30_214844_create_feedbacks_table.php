<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 32)->default('saran'); // saran, kritik, pertanyaan, apresiasi
            $table->string('category', 64)->default('umum'); // umum, wisata, penginapan, kuliner, event, rental, sistem
            $table->string('name', 120);
            $table->string('email', 150);
            $table->string('phone', 32)->nullable();
            $table->string('subject', 200);
            $table->text('message');
            $table->string('status', 32)->default('pending'); // pending, reviewed, replied, archived
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignUlid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
