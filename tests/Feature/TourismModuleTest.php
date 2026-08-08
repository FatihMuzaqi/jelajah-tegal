<?php

namespace Tests\Feature;

use App\Models\CatalogEntity;
use App\Models\CatalogOffer;
use App\Models\Category;
use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\TourismTicketPackage;
use App\Models\User;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TourismModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FoundationReferenceSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_draft_is_visible_only_to_owning_mitra_and_not_public(): void
    {
        [$ownerA, $mitraA, $references] = $this->tenant();
        [$ownerB, $mitraB] = $this->tenant();
        $entity = $this->createDraft($ownerA, $mitraA, $references);

        $this->actingAs($ownerA)->withSession(['active_mitra_id' => $mitraA->id])->get(route('mitra.tourism.show', $entity))->assertOk();
        $this->actingAs($ownerB)->withSession(['active_mitra_id' => $mitraB->id])->get(route('mitra.tourism.show', $entity))->assertForbidden();
        $this->get(route('tourism.show', $entity->slug))->assertNotFound();
    }

    public function test_submission_requires_cover_and_operating_hours(): void
    {
        [$owner, $mitra, $references] = $this->tenant();
        $entity = $this->createDraft($owner, $mitra, $references);
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.tourism.submit', $entity))->assertSessionHasErrors('status');
        $this->assertSame('draft', $entity->fresh()->status);
    }

    public function test_complete_destination_can_be_moderated_and_reject_requires_reason(): void
    {
        [$owner, $mitra, $references] = $this->tenant();
        $entity = $this->complete($this->createDraft($owner, $mitra, $references), $mitra);
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.tourism.submit', $entity))->assertRedirect();
        $this->assertSame('submitted', $entity->fresh()->status);

        $admin = $this->admin();
        setPermissionsTeamId(null);
        $session = ['mfa_verified_at' => now()->timestamp];
        $this->actingAs($admin)->withSession($session)->patch(route('admin.tourism.update', $entity), ['decision' => 'reject'])->assertSessionHasErrors('reason');
        $this->actingAs($admin)->withSession($session)->patch(route('admin.tourism.update', $entity), ['decision' => 'approve'])->assertRedirect();
        $this->assertSame('published', $entity->fresh()->status);
        $this->assertDatabaseHas('moderation_actions', ['to_status' => 'published']);
        $this->assertDatabaseHas('audit_logs', ['event' => 'tourism.approve']);
        $this->get(route('tourism.show', $entity->slug))->assertOk()->assertSee($entity->name);
    }

    public function test_taken_down_destination_is_removed_from_public_surface(): void
    {
        [$owner, $mitra, $references] = $this->tenant();
        $entity = $this->complete($this->createDraft($owner, $mitra, $references), $mitra);
        $entity->update(['status' => 'published', 'published_at' => now()]);
        $admin = $this->admin();
        setPermissionsTeamId(null);
        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->patch(route('admin.tourism.update', $entity), ['decision' => 'takedown', 'reason' => 'Informasi keselamatan tidak valid'])->assertRedirect();
        $this->get(route('tourism.show', $entity->slug))->assertNotFound();
    }

    public function test_quota_cannot_drop_below_reservations_or_cross_tenant(): void
    {
        [$owner, $mitra, $references] = $this->tenant();
        $entity = $this->createDraft($owner, $mitra, $references);
        $offer = CatalogOffer::create(['mitra_id' => $mitra->id, 'catalog_entity_id' => $entity->id, 'offer_type' => 'tourism_ticket', 'sku' => 'TICKET-A', 'name' => 'Reguler', 'price' => 10000, 'status' => 'active']);
        $package = TourismTicketPackage::create(['tourism_destination_id' => $entity->tourism->id, 'catalog_offer_id' => $offer->id, 'name' => 'Reguler', 'quota_per_day' => 20]);
        DB::table('availabilities')->insert(['id' => (string) str()->ulid(), 'mitra_id' => $mitra->id, 'catalog_offer_id' => $offer->id, 'service_date' => now()->addDay()->toDateString(), 'capacity' => 10, 'reserved_quantity' => 8, 'status' => 'available', 'created_at' => now(), 'updated_at' => now()]);
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->put(route('mitra.tourism.quota', [$entity, $package]), ['service_date' => now()->addDay()->toDateString(), 'capacity' => 7])->assertSessionHasErrors('capacity');
        [$otherOwner, $otherMitra] = $this->tenant();
        $this->actingAs($otherOwner)->withSession(['active_mitra_id' => $otherMitra->id])->put(route('mitra.tourism.quota', [$entity, $package]), ['service_date' => now()->addDay()->toDateString(), 'capacity' => 10])->assertForbidden();
    }

    public function test_media_rejects_unsafe_mime(): void
    {
        Storage::fake('public');
        [$owner, $mitra, $references] = $this->tenant();
        $entity = $this->createDraft($owner, $mitra, $references);
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.tourism.media', $entity), ['role' => 'cover', 'image' => UploadedFile::fake()->create('bad.exe', 10, 'application/x-msdownload')])->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('media_assets', ['mitra_id' => $mitra->id, 'purpose' => 'tourism']);
    }

    public function test_consumer_can_favorite_and_submit_pending_review_for_published_tourism(): void
    {
        [$owner, $mitra, $references] = $this->tenant();
        $entity = $this->createDraft($owner, $mitra, $references);
        $entity->update(['status' => 'published', 'published_at' => now()]);
        $consumer = User::factory()->create();
        setPermissionsTeamId(null);
        $consumer->assignRole('consumer');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($consumer)->post(route('tourism.favorite', $entity->slug))->assertRedirect();
        $this->actingAs($consumer)->post(route('tourism.reviews.store', $entity->slug), ['rating' => 5, 'body' => 'Destinasi sangat baik.'])->assertRedirect();
        $this->assertDatabaseHas('favorites', ['user_id' => $consumer->id, 'catalog_entity_id' => $entity->id]);
        $this->assertDatabaseHas('reviews', ['user_id' => $consumer->id, 'catalog_entity_id' => $entity->id, 'status' => 'pending']);
        $review = $entity->reviews()->firstOrFail();
        $admin = $this->admin();
        setPermissionsTeamId(null);
        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->patch(route('admin.tourism.reviews.update', $review), ['decision' => 'publish'])->assertRedirect();
        $this->assertSame('published', $review->fresh()->status);
        $this->assertSame(1, $entity->fresh()->rating_count);
        $this->assertSame('5.00', $entity->fresh()->rating_average);
    }

    public function test_nearby_filter_uses_real_coordinates(): void
    {
        [$owner, $mitra, $references] = $this->tenant();
        $entity = $this->createDraft($owner, $mitra, $references);
        $entity->update(['status' => 'published', 'published_at' => now()]);

        $this->get(route('tourism.index', ['latitude' => -6.87, 'longitude' => 109.12, 'radius' => 5]))->assertOk()->assertSee($entity->name);
        $this->get(route('tourism.index', ['latitude' => -7.80, 'longitude' => 110.36, 'radius' => 5]))->assertOk()->assertDontSee($entity->name);
    }

    private function tenant(): array
    {
        $user = User::factory()->create();
        $mitra = Mitra::factory()->for($user, 'owner')->create(['status' => 'active', 'approved_at' => now()]);
        $mitra->members()->create(['user_id' => $user->id, 'status' => 'active', 'joined_at' => now()]);
        setPermissionsTeamId($mitra->id);
        $user->assignRole('mitra-owner');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        setPermissionsTeamId(null);
        $service = ServiceType::where('code', 'tourism')->firstOrFail();
        $mitra->features()->create(['service_type_id' => $service->id, 'status' => 'enabled', 'enabled_at' => now()]);
        $category = Category::factory()->create(['service_type_id' => $service->id, 'is_active' => true]);
        $region = Region::factory()->create();

        return [$user, $mitra, compact('service', 'category', 'region')];
    }

    private function createDraft(User $owner, Mitra $mitra, array $refs): CatalogEntity
    {
        $slug = 'wisata-'.str()->lower(str()->random(8));
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.tourism.store'), ['name' => 'Wisata Uji', 'slug' => $slug, 'category_id' => $refs['category']->id, 'region_id' => $refs['region']->id, 'description' => 'Destinasi yang layak dikunjungi.', 'address' => 'Tegal', 'destination_type' => 'nature', 'latitude' => -6.87, 'longitude' => 109.12])->assertRedirect();

        return CatalogEntity::where('slug', $slug)->firstOrFail();
    }

    private function complete(CatalogEntity $entity, Mitra $mitra): CatalogEntity
    {
        $media = MediaAsset::factory()->create(['mitra_id' => $mitra->id, 'owner_user_id' => null, 'is_platform_owned' => false, 'visibility' => 'public', 'purpose' => 'tourism']);
        $entity->media()->attach($media->id, ['role' => 'cover', 'sort_order' => 0]);
        $entity->operatingHours()->create(['weekday' => 1, 'sequence' => 1, 'opens_at' => '09:00', 'closes_at' => '17:00', 'is_closed' => false]);

        return $entity->fresh();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        setPermissionsTeamId(null);
        $admin->assignRole('admin');
        $admin->credential()->update(['mfa_confirmed_at' => now()]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $admin;
    }
}
