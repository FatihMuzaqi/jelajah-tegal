<?php

namespace Tests\Feature;

use App\Actions\Mitras\CreateMitra;
use App\Actions\Mitras\SubmitKycDocument;
use App\Models\ApplicationSetting;
use App\Models\AuditLog;
use App\Models\DatabaseNotification;
use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\MitraBankAccount;
use App\Models\MitraMember;
use App\Models\User;
use App\Support\MitraContext;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FoundationDatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FoundationReferenceSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_user_has_multiple_mitra_memberships_and_relations(): void
    {
        $u = User::factory()->create();
        $a = Mitra::factory()->create(['owner_user_id' => $u->id]);
        $b = Mitra::factory()->create(['owner_user_id' => $u->id]);
        MitraMember::factory()->for($a)->for($u)->create();
        MitraMember::factory()->for($b)->for($u)->create();
        $this->assertCount(2, $u->mitraMemberships);
        $this->assertTrue($a->members->first()->user->is($u));
    }

    public function test_membership_unique_constraint(): void
    {
        $m = Mitra::factory()->create();
        $u = User::factory()->create();
        MitraMember::factory()->for($m)->for($u)->create();
        $this->expectException(QueryException::class);
        MitraMember::factory()->for($m)->for($u)->create();
    }

    public function test_foreign_key_restricts_hard_delete(): void
    {
        $m = Mitra::factory()->create();
        MitraMember::factory()->for($m)->create();
        $this->expectException(QueryException::class);
        DB::table('mitras')->where('id', $m->id)->delete();
    }

    public function test_explicit_tenant_scope_never_returns_other_mitra(): void
    {
        $a = Mitra::factory()->create();
        $b = Mitra::factory()->create();
        MitraBankAccount::factory()->for($a)->create();
        MitraBankAccount::factory()->for($b)->create();
        $this->assertSame([$a->id], MitraBankAccount::forMitra($a)->pluck('mitra_id')->unique()->all());
    }

    public function test_tenant_permission_and_policy_deny_cross_tenant(): void
    {
        $u = User::factory()->create();
        $a = Mitra::factory()->create();
        $b = Mitra::factory()->create();
        MitraMember::factory()->for($a)->for($u)->create();
        setPermissionsTeamId($a->id);
        $u->assignRole('mitra-owner');
        $own = MitraBankAccount::factory()->for($a)->create();
        $other = MitraBankAccount::factory()->for($b)->create();
        $context = app(MitraContext::class);
        $context->activate($a->id);
        try {
            $this->assertTrue($u->can('view', $own));
            $this->assertFalse($u->can('view', $other));
        } finally {
            $context->clear();
        }
    }

    public function test_kyc_requires_matching_media_ownership_and_versions_atomically(): void
    {
        $u = User::factory()->create();
        $m = Mitra::factory()->create();
        MitraMember::factory()->for($m)->for($u)->create();
        $wrong = MediaAsset::factory()->create();
        try {
            app(SubmitKycDocument::class)->execute($m, $u, $wrong, 'business_license');
            $this->fail('Expected ownership rejection');
        } catch (ValidationException) {
        }$media = MediaAsset::factory()->forMitra($m)->create();
        $first = app(SubmitKycDocument::class)->execute($m, $u, $media, 'business_license');
        $second = app(SubmitKycDocument::class)->execute($m, $u, $media, 'business_license');
        $this->assertSame(2, $second->version);
        $this->assertSame('superseded', $first->fresh()->status);
    }

    public function test_media_database_constraint_requires_exactly_one_owner(): void
    {
        $this->expectException(QueryException::class);
        MediaAsset::factory()->create(['owner_user_id' => null, 'mitra_id' => null, 'is_platform_owned' => false]);
    }

    public function test_create_mitra_transaction_creates_membership_permission_and_audit(): void
    {
        $u = User::factory()->create();
        $m = app(CreateMitra::class)->execute($u, ['legal_name' => 'PT Aman', 'display_name' => 'Aman', 'slug' => 'aman']);
        $this->assertDatabaseHas('mitra_members', ['mitra_id' => $m->id, 'user_id' => $u->id]);
        $this->assertDatabaseHas('model_has_roles', ['mitra_id' => $m->id, 'model_id' => $u->id]);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $m->id, 'event' => 'mitra.created']);
    }

    public function test_all_foundation_tables_exist(): void
    {
        foreach (['users', 'user_profiles', 'roles', 'permissions', 'mfa_recovery_codes', 'mitras', 'mitra_members', 'mitra_features', 'mitra_feature_requests', 'mitra_bank_accounts', 'mitra_kyc_documents', 'gatekeeper_assignments', 'regions', 'categories', 'facilities', 'media_assets', 'notifications', 'audit_logs', 'application_settings', 'feature_flags'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }
    }

    public function test_reference_seeder_creates_no_business_dummy(): void
    {
        $this->assertDatabaseCount('service_types', 5);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('mitras', 0);
    }

    public function test_notification_relationship_and_scoped_setting_uniqueness(): void
    {
        $u = User::factory()->create();
        DatabaseNotification::factory()->for($u)->create();
        $this->assertCount(1, $u->notifications);
        ApplicationSetting::factory()->create(['key_name' => 'locale']);
        $this->expectException(QueryException::class);
        ApplicationSetting::factory()->create(['key_name' => 'locale']);
    }

    public function test_audit_log_is_append_only(): void
    {
        $u = User::factory()->create();
        $m = app(CreateMitra::class)->execute($u, ['legal_name' => 'PT Tetap', 'display_name' => 'Tetap', 'slug' => 'tetap']);
        $log = $m->hasMany(AuditLog::class)->firstOrFail();
        $this->expectException(\LogicException::class);
        $log->update(['event' => 'changed']);
    }
}
