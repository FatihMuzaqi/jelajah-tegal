<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\MitraInvitation;
use App\Models\MitraKycDocument;
use App\Models\MitraMember;
use App\Models\ServiceType;
use App\Models\User;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MitraPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FoundationReferenceSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_admin_can_create_mitra_with_owner_invitation_audit_and_notification(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->post(route('admin.mitras.store'), [
            'owner_name' => 'Owner Baru', 'owner_email' => 'owner@example.test', 'legal_name' => 'PT Baru', 'display_name' => 'Mitra Baru', 'slug' => 'mitra-baru',
        ])->assertRedirect(route('admin.mitras.index'));

        $mitra = Mitra::where('slug', 'mitra-baru')->firstOrFail();
        $owner = User::where('email', 'owner@example.test')->firstOrFail();
        $this->assertSame('draft', $mitra->status);
        $this->assertDatabaseHas('mitra_members', ['mitra_id' => $mitra->id, 'user_id' => $owner->id, 'status' => 'invited']);
        $this->assertDatabaseHas('mitra_invitations', ['mitra_id' => $mitra->id, 'email' => 'owner@example.test']);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'mitra_id' => $mitra->id, 'type' => 'mitra.invited']);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'mitra.created']);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'mitra.member_invited']);
    }

    public function test_owner_activation_creates_password_and_token_is_one_use(): void
    {
        $token = 'known-activation-token';
        $owner = User::create(['name' => 'Owner', 'email' => 'activation@example.test', 'status' => 'invited']);
        $mitra = Mitra::factory()->for($owner, 'owner')->create(['status' => 'draft']);
        $mitra->members()->create(['user_id' => $owner->id, 'status' => 'invited']);
        $role = Role::whereNull('mitra_id')->where('name', 'mitra-owner')->firstOrFail();
        MitraInvitation::create(['mitra_id' => $mitra->id, 'email' => $owner->email, 'intended_role_id' => $role->id, 'token_hash' => hash('sha256', $token), 'invited_by' => User::factory()->create()->id, 'expires_at' => now()->addHour()]);

        $this->post(route('mitra.activation.store', $token), ['password' => 'secure123', 'password_confirmation' => 'secure123'])->assertRedirect(route('mitra.select'));
        $owner->refresh();
        $this->assertTrue(Hash::check('secure123', $owner->credential->password_hash));
        $this->assertNotNull($owner->email_verified_at);
        $this->assertSame('active', $owner->mitraMemberships()->firstOrFail()->status);
        setPermissionsTeamId($mitra->id);
        $this->assertTrue($owner->hasRole('mitra-owner'));
        setPermissionsTeamId(null);
        $this->assertDatabaseMissing('mitra_invitations', ['email' => $owner->email, 'accepted_at' => null]);

        auth()->logout();
        $this->post(route('mitra.activation.store', $token), ['password' => 'secure123', 'password_confirmation' => 'secure123'])->assertSessionHasErrors('token');
    }

    public function test_mitra_profile_and_staff_permissions_follow_tenant_role(): void
    {
        [$owner, $mitra] = $this->tenantUser('mitra-owner');
        [$staff] = $this->tenantUser('mitra-staff', $mitra);

        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->put(route('mitra.profile.update'), ['display_name' => 'Nama Diperbarui', 'contact_email' => 'business@example.test'])->assertRedirect();
        $this->assertSame('Nama Diperbarui', $mitra->fresh()->display_name);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'mitra.profile_updated']);

        $this->actingAs($staff)->withSession(['active_mitra_id' => $mitra->id])->get(route('mitra.profile.edit'))->assertOk();
        $this->actingAs($staff)->withSession(['active_mitra_id' => $mitra->id])->put(route('mitra.profile.update'), ['display_name' => 'Tidak Boleh'])->assertForbidden();
        $this->actingAs($staff)->withSession(['active_mitra_id' => $mitra->id])->get(route('mitra.members.index'))->assertForbidden();
    }

    public function test_kyc_is_private_authorized_and_cross_tenant_is_denied(): void
    {
        Storage::fake('local');
        [$ownerA, $mitraA] = $this->tenantUser('mitra-owner');
        [$ownerB, $mitraB] = $this->tenantUser('mitra-owner');

        $this->actingAs($ownerA)->withSession(['active_mitra_id' => $mitraA->id])->post(route('mitra.kyc.store'), ['document_type' => 'business_license', 'document' => UploadedFile::fake()->image('izin.png', 400, 300)])->assertRedirect();
        $document = MitraKycDocument::where('mitra_id', $mitraA->id)->firstOrFail();
        $media = $document->mediaAsset;
        $this->assertSame('local', $media->disk);
        $this->assertSame('private', $media->visibility);
        Storage::disk('local')->assertExists($media->object_key);

        $this->actingAs($ownerA)->withSession(['active_mitra_id' => $mitraA->id])->get(route('mitra.kyc.download', $document))->assertOk();
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitraA->id, 'event' => 'mitra.kyc_accessed']);
        $this->actingAs($ownerB)->withSession(['active_mitra_id' => $mitraB->id])->get(route('mitra.kyc.download', $document))->assertForbidden();
    }

    public function test_cross_tenant_membership_idor_is_hidden(): void
    {
        [$ownerA, $mitraA] = $this->tenantUser('mitra-owner');
        [$ownerB, $mitraB] = $this->tenantUser('mitra-owner');
        [$staffB] = $this->tenantUser('mitra-staff', $mitraB);
        $memberB = MitraMember::where('mitra_id', $mitraB->id)->where('user_id', $staffB->id)->firstOrFail();

        $this->actingAs($ownerA)->withSession(['active_mitra_id' => $mitraA->id])->delete(route('mitra.members.destroy', $memberB))->assertNotFound();
        $this->assertSame('active', $memberB->fresh()->status);
        $this->assertSame($ownerB->id, $mitraB->owner_user_id);
    }

    public function test_feature_request_approval_enables_feature_and_records_notifications_and_audit(): void
    {
        [$owner, $mitra] = $this->tenantUser('mitra-owner');
        $admin = $this->admin();
        $service = ServiceType::where('code', 'tourism')->firstOrFail();

        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.features.store'), ['service_type_id' => $service->id, 'reason' => 'Membuka layanan wisata'])->assertRedirect();
        $featureRequest = $mitra->featureRequests()->firstOrFail();
        $this->assertDatabaseHas('notifications', ['user_id' => $admin->id, 'mitra_id' => $mitra->id, 'type' => 'admin.feature_requested']);

        setPermissionsTeamId(null);
        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->patch(route('admin.features.update', $featureRequest), ['decision' => 'approved'])->assertRedirect();
        $this->assertDatabaseHas('mitra_features', ['mitra_id' => $mitra->id, 'service_type_id' => $service->id, 'status' => 'enabled']);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'type' => 'mitra.feature_reviewed']);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'admin.feature_request_reviewed']);
    }

    public function test_admin_can_review_kyc_and_owner_is_notified(): void
    {
        [$owner, $mitra] = $this->tenantUser('mitra-owner');
        $admin = $this->admin();
        $media = MediaAsset::factory()->create(['mitra_id' => $mitra->id, 'owner_user_id' => null, 'is_platform_owned' => false, 'visibility' => 'private', 'purpose' => 'kyc']);
        $document = MitraKycDocument::factory()->create(['mitra_id' => $mitra->id, 'media_asset_id' => $media->id, 'submitted_by' => $owner->id, 'status' => 'submitted']);

        setPermissionsTeamId(null);
        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->patch(route('admin.kyc.update', $document), ['decision' => 'rejected', 'reason' => 'Dokumen tidak terbaca'])->assertRedirect();
        $this->assertSame('rejected', $document->fresh()->status);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'type' => 'kyc.reviewed']);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'admin.kyc_reviewed']);
    }

    public function test_admin_activation_requires_approved_kyc_and_notifies_owner(): void
    {
        [$owner, $mitra] = $this->tenantUser('mitra-owner');
        $admin = $this->admin();

        setPermissionsTeamId(null);
        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->patch(route('admin.mitras.status', $mitra), ['status' => 'active'])->assertSessionHasErrors('status');

        $media = MediaAsset::factory()->create(['mitra_id' => $mitra->id, 'owner_user_id' => null, 'is_platform_owned' => false, 'visibility' => 'private', 'purpose' => 'kyc']);
        MitraKycDocument::factory()->create(['mitra_id' => $mitra->id, 'media_asset_id' => $media->id, 'submitted_by' => $owner->id, 'status' => 'approved']);
        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->patch(route('admin.mitras.status', $mitra), ['status' => 'active'])->assertRedirect();
        $this->assertSame('active', $mitra->fresh()->status);
        $this->assertNotNull($mitra->fresh()->approved_at);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'type' => 'mitra.status_changed']);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'admin.mitra_status_changed']);
    }

    public function test_owner_can_revoke_staff_and_role_is_removed(): void
    {
        [$owner, $mitra] = $this->tenantUser('mitra-owner');
        [$staff] = $this->tenantUser('mitra-staff', $mitra);
        $member = $mitra->members()->where('user_id', $staff->id)->firstOrFail();

        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->delete(route('mitra.members.destroy', $member))->assertRedirect();
        $this->assertSame('revoked', $member->fresh()->status);
        setPermissionsTeamId($mitra->id);
        $this->assertFalse($staff->hasRole('mitra-staff'));
        setPermissionsTeamId(null);
        $this->assertDatabaseHas('notifications', ['user_id' => $staff->id, 'type' => 'mitra.membership_revoked']);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'mitra.member_revoked']);
    }

    public function test_kyc_rejects_unsafe_mime_without_creating_media(): void
    {
        Storage::fake('local');
        [$owner, $mitra] = $this->tenantUser('mitra-owner');

        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.kyc.store'), ['document_type' => 'business_license', 'document' => UploadedFile::fake()->create('malware.exe', 20, 'application/x-msdownload')])->assertSessionHasErrors('document');
        $this->assertDatabaseMissing('media_assets', ['mitra_id' => $mitra->id, 'purpose' => 'kyc']);
        $this->assertDatabaseMissing('mitra_kyc_documents', ['mitra_id' => $mitra->id]);
    }

    public function test_bank_account_is_encrypted_and_operating_hours_are_tenant_scoped_and_audited(): void
    {
        [$owner, $mitra] = $this->tenantUser('mitra-owner');

        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.bank-accounts.store'), ['bank_code' => 'bca', 'account_name' => 'Owner Aman', 'account_number' => '1234567890', 'is_primary' => 1])->assertRedirect();
        $raw = DB::table('mitra_bank_accounts')->where('mitra_id', $mitra->id)->first();
        $this->assertNotSame('1234567890', $raw->account_number_encrypted);
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->get(route('mitra.bank-accounts.index'))->assertOk()->assertSee('•••• 7890');

        $hours = collect(range(0, 6))->map(fn ($day) => ['day_of_week' => $day, 'is_closed' => 0, 'opens_at' => '09:00', 'closes_at' => '17:00'])->all();
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->put(route('mitra.profile.hours'), ['hours' => $hours])->assertRedirect();
        $this->assertSame(7, $mitra->operatingHours()->count());
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'mitra.bank_account_added']);
        $this->assertDatabaseHas('audit_logs', ['mitra_id' => $mitra->id, 'event' => 'mitra.operating_hours_updated']);
    }

    private function admin(): User
    {
        setPermissionsTeamId(null);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $admin->credential()->update(['mfa_confirmed_at' => now()]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $admin;
    }

    private function tenantUser(string $role, ?Mitra $mitra = null): array
    {
        $user = User::factory()->create();
        $mitra ??= Mitra::factory()->for($user, 'owner')->create();
        $mitra->members()->updateOrCreate(['user_id' => $user->id], ['status' => 'active', 'joined_at' => now()]);
        setPermissionsTeamId($mitra->id);
        $user->assignRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        setPermissionsTeamId(null);

        return [$user, $mitra];
    }
}
