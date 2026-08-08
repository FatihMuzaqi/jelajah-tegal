<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->references();
        $this->expandMitras();
        $this->media();
        $this->tenantOperations();
        $this->platform();
    }

    private function references(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 100);
            $table->boolean('is_transactional')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('regions')->restrictOnDelete();
            $table->string('level', 32);
            $table->string('code', 32)->unique();
            $table->string('name', 150);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['parent_id', 'level', 'name']);
        });
        foreach (['categories', 'facilities'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name) {
                $table->id();
                $table->foreignId('service_type_id')->constrained()->restrictOnDelete();
                if ($name === 'categories') {
                    $table->foreignId('parent_id')->nullable()->constrained('categories')->restrictOnDelete();
                }
                $table->string('name', 150);
                $table->string('slug', 191);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['service_type_id', 'slug']);
            });
        }
    }

    private function expandMitras(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->string('registration_number', 100)->nullable()->unique()->after('slug');
            $table->text('tax_number_encrypted')->nullable();
            $table->text('description')->nullable();
            $table->string('contact_email', 191)->nullable();
            $table->string('contact_phone', 32)->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->text('address')->nullable();
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->index(['status', 'owner_user_id']);
        });
        Schema::table('mitra_members', function (Blueprint $table) {
            $table->foreignUlid('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['mitra_id', 'status']);
        });
    }

    private function media(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignUlid('owner_user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->boolean('is_platform_owned')->default(false);
            $table->string('disk', 32);
            $table->string('object_key', 500);
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 127);
            $table->unsignedBigInteger('size_bytes');
            $table->char('checksum_sha256', 64);
            $table->string('visibility', 32)->default('private');
            $table->string('purpose', 64);
            $table->string('status', 32)->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['disk', 'object_key']);
            $table->index(['mitra_id', 'purpose', 'status']);
            $table->index(['owner_user_id', 'purpose', 'status']);
        });
        DB::statement('ALTER TABLE media_assets ADD CONSTRAINT media_assets_one_owner CHECK ((mitra_id IS NOT NULL) + (owner_user_id IS NOT NULL) + (is_platform_owned = 1) = 1)');
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->foreignUlid('avatar_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->date('birth_date')->nullable();
            $table->string('gender', 32)->nullable();
        });
        Schema::table('mitras', function (Blueprint $table) {
            $table->foreignUlid('logo_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignUlid('banner_media_id')->nullable()->constrained('media_assets')->nullOnDelete();
        });
    }

    private function tenantOperations(): void
    {
        Schema::create('mitra_features', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_type_id')->constrained()->restrictOnDelete();
            $table->string('status', 32)->default('disabled');
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->foreignUlid('enabled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['mitra_id', 'service_type_id']);
        });
        Schema::create('mitra_feature_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_type_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignUlid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 32)->default('requested');
            $table->text('reason')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['mitra_id', 'status']);
        });
        Schema::create('mitra_bank_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->string('bank_code', 32);
            $table->text('account_name_encrypted');
            $table->text('account_number_encrypted');
            $table->char('account_fingerprint', 64);
            $table->string('status', 32)->default('pending');
            $table->boolean('is_primary')->default(false);
            $table->foreignUlid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['mitra_id', 'account_fingerprint']);
            $table->index(['mitra_id', 'status', 'is_primary']);
        });
        Schema::create('mitra_kyc_documents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('media_asset_id')->constrained()->restrictOnDelete();
            $table->string('document_type', 32);
            $table->unsignedSmallInteger('version')->default(1);
            $table->text('document_number_encrypted')->nullable();
            $table->char('document_fingerprint', 64)->nullable();
            $table->string('status', 32)->default('submitted');
            $table->foreignUlid('submitted_by')->constrained('users')->restrictOnDelete();
            $table->foreignUlid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->date('expires_on')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->ulid('superseded_by_id')->nullable();
            $table->timestamps();
            $table->unique(['mitra_id', 'document_type', 'version']);
            $table->index(['mitra_id', 'status', 'expires_on']);
            $table->index(['status', 'created_at']);
        });
        Schema::table('mitra_kyc_documents', fn (Blueprint $table) => $table->foreign('superseded_by_id')->references('id')->on('mitra_kyc_documents')->nullOnDelete());
        Schema::create('gatekeeper_assignments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('member_id')->constrained('mitra_members')->restrictOnDelete();
            $table->string('scope_type', 32)->default('mitra');
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->foreignUlid('assigned_by')->constrained('users')->restrictOnDelete();
            $table->foreignUlid('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->unique(['member_id', 'scope_type', 'valid_from']);
            $table->index(['mitra_id', 'scope_type', 'revoked_at']);
        });
        DB::statement('ALTER TABLE gatekeeper_assignments ADD CONSTRAINT gatekeeper_foundation_scope CHECK (scope_type = \'mitra\')');
    }

    private function platform(): void
    {
        Schema::create('application_settings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('mitra_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('key_name', 191);
            $table->longText('value_encrypted')->nullable();
            $table->json('value_json')->nullable();
            $table->string('value_type', 32);
            $table->boolean('is_secret')->default(false);
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('scope_key', 26)->storedAs('coalesce(mitra_id, \'00000000000000000000000000\')');
            $table->timestamps();
            $table->unique(['scope_key', 'key_name']);
        });
        DB::statement('ALTER TABLE application_settings ADD CONSTRAINT application_settings_one_value CHECK ((value_encrypted IS NOT NULL) + (value_json IS NOT NULL) <= 1)');
        Schema::create('notifications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('type');
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('mitra_id')->nullable()->constrained()->restrictOnDelete();
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at', 'created_at']);
            $table->index(['mitra_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('application_settings');
        Schema::dropIfExists('gatekeeper_assignments');
        Schema::dropIfExists('mitra_kyc_documents');
        Schema::dropIfExists('mitra_bank_accounts');
        Schema::dropIfExists('mitra_feature_requests');
        Schema::dropIfExists('mitra_features');
        Schema::table('mitras', fn (Blueprint $table) => $table->dropConstrainedForeignId('logo_media_id'));
        Schema::table('mitras', fn (Blueprint $table) => $table->dropConstrainedForeignId('banner_media_id'));
        Schema::table('user_profiles', fn (Blueprint $table) => $table->dropConstrainedForeignId('avatar_media_id'));
        Schema::dropIfExists('media_assets');
        Schema::table('mitra_members', fn (Blueprint $table) => $table->dropConstrainedForeignId('invited_by'));
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['registration_number', 'tax_number_encrypted', 'description', 'contact_email', 'contact_phone', 'region_id', 'address', 'approved_by', 'approved_at', 'suspended_at']);
        });
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('service_types');
    }
};
