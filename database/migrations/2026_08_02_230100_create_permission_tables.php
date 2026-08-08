<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('guard_name')->default('web');
            $t->string('description')->nullable();
            $t->string('risk_level')->default('normal');
            $t->timestamps();
            $t->unique(['name', 'guard_name']);
        });
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->foreignUlid('mitra_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('name');
            $t->string('guard_name')->default('web');
            $t->boolean('is_system')->default(false);
            $t->string('scope_key', 26)->storedAs('coalesce(mitra_id, \'00000000000000000000000000\')');
            $t->timestamps();
            $t->unique(['scope_key', 'name', 'guard_name']);
        });
        $this->pivot('model_has_permissions', 'permission_id', 'permissions');
        $this->pivot('model_has_roles', 'role_id', 'roles');
        Schema::create('role_has_permissions', function (Blueprint $t) {
            $t->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $t->foreignId('role_id')->constrained()->cascadeOnDelete();
            $t->timestamp('created_at')->useCurrent();
            $t->primary(['permission_id', 'role_id']);
        });
    }

    public function down(): void
    {
        foreach (['role_has_permissions', 'model_has_roles', 'model_has_permissions', 'roles', 'permissions'] as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function pivot(string $name, string $key, string $parent): void
    {
        Schema::create($name, function (Blueprint $t) use ($name, $key, $parent) {
            $t->unsignedBigInteger($key);
            $t->string('model_type');
            $t->ulid('model_id');
            $t->foreignUlid('mitra_id')->nullable()->constrained()->restrictOnDelete();
            $t->string('scope_key', 26)->storedAs('coalesce(mitra_id, \'00000000000000000000000000\')');
            $t->foreign($key)->references('id')->on($parent)->cascadeOnDelete();
            $t->foreign('model_id')->references('id')->on('users')->cascadeOnDelete();
            $t->unique(['scope_key', $key, 'model_id', 'model_type'], $name.'_unique');
        });
    }
};
