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
        Schema::table('review_replies', function (Blueprint $table) {
            $table->dropForeign(['review_id']);
            $table->dropForeign(['mitra_id']);
        });

        Schema::table('review_replies', function (Blueprint $table) {
            $table->dropUnique('review_replies_review_id_unique');
            $table->foreignUlid('mitra_id')->nullable()->change();
        });

        Schema::table('review_replies', function (Blueprint $table) {
            $table->foreign('review_id')->references('id')->on('reviews')->cascadeOnDelete();
            $table->foreign('mitra_id')->references('id')->on('mitras')->nullOnDelete();
            $table->index(['review_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('review_replies', function (Blueprint $table) {
            $table->dropForeign(['review_id']);
            $table->dropForeign(['mitra_id']);
            $table->dropIndex(['review_id', 'created_at']);
        });

        Schema::table('review_replies', function (Blueprint $table) {
            $table->unique('review_id');
            $table->foreignUlid('mitra_id')->nullable(false)->change();
        });

        Schema::table('review_replies', function (Blueprint $table) {
            $table->foreign('review_id')->references('id')->on('reviews')->cascadeOnDelete();
            $table->foreign('mitra_id')->references('id')->on('mitras')->restrictOnDelete();
        });
    }
};
