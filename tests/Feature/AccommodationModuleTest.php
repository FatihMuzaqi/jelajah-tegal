<?php

namespace Tests\Feature;

use App\Models\AccommodationRoom;
use App\Models\CatalogEntity;
use App\Models\Category;
use App\Models\MediaAsset;
use App\Models\Mitra;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\User;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccommodationModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FoundationReferenceSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_draft_property_is_owner_only_and_cross_tenant_is_denied(): void
    {
        [$ownerA,$mitraA,$refs] = $this->tenant();
        [$ownerB,$mitraB] = $this->tenant();
        $property = $this->property($ownerA, $mitraA, $refs);
        $this->actingAs($ownerA)->withSession(['active_mitra_id' => $mitraA->id])->get(route('mitra.accommodation.show', $property))->assertOk();
        $this->actingAs($ownerB)->withSession(['active_mitra_id' => $mitraB->id])->get(route('mitra.accommodation.show', $property))->assertForbidden();
        $this->get(route('accommodation.show', $property->slug))->assertNotFound();
    }

    public function test_room_belongs_to_property_and_price_offer(): void
    {
        [$owner,$mitra,$refs] = $this->tenant();
        $property = $this->property($owner, $mitra, $refs);
        $room = $this->room($owner, $mitra, $property);
        $this->assertSame($property->accommodation->id, $room->accommodation_id);
        $this->assertSame('250000.00', $room->offer->price);
        $this->assertSame($property->id, $room->offer->catalog_entity_id);
    }

    public function test_minimum_and_maximum_stay_range_is_validated(): void
    {
        [$owner,$mitra,$refs] = $this->tenant();
        $property = $this->property($owner, $mitra, $refs);
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.accommodation.rooms.store', $property), array_merge($this->roomData(), ['min_stay_nights' => 5, 'max_stay_nights' => 2]))->assertSessionHasErrors('max_stay_nights');
        $this->assertDatabaseCount('accommodation_rooms', 0);
    }

    public function test_availability_calendar_updates_valid_range_and_rejects_over_capacity(): void
    {
        [$owner,$mitra,$refs] = $this->tenant();
        $property = $this->property($owner, $mitra, $refs);
        $room = $this->room($owner, $mitra, $property);
        $start = now()->addDay()->toDateString();
        $end = now()->addDays(3)->toDateString();
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->put(route('mitra.accommodation.rooms.calendar.update', [$property, $room]), ['start_date' => $start, 'end_date' => $end, 'available_units' => 3, 'price_override' => 300000])->assertRedirect();
        $this->assertSame(3, $room->offer->availabilities()->count());
        $this->assertDatabaseHas('availabilities', ['catalog_offer_id' => $room->catalog_offer_id, 'capacity' => 3, 'price_override' => '300000.00']);
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->put(route('mitra.accommodation.rooms.calendar.update', [$property, $room]), ['start_date' => $start, 'end_date' => $end, 'available_units' => 6])->assertSessionHasErrors('available_units');
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->put(route('mitra.accommodation.rooms.calendar.update', [$property, $room]), ['start_date' => $end, 'end_date' => $start, 'available_units' => 1])->assertSessionHasErrors('end_date');
    }

    public function test_complete_property_can_be_moderated_and_published(): void
    {
        [$owner,$mitra,$refs] = $this->tenant();
        $property = $this->complete($this->property($owner, $mitra, $refs), $owner, $mitra);
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.accommodation.submit', $property))->assertRedirect();
        $this->assertSame('submitted', $property->fresh()->status);
        $admin = $this->admin();
        setPermissionsTeamId(null);
        $session = ['mfa_verified_at' => now()->timestamp];
        $this->actingAs($admin)->withSession($session)->patch(route('admin.accommodation.update', $property), ['decision' => 'reject'])->assertSessionHasErrors('reason');
        $this->actingAs($admin)->withSession($session)->patch(route('admin.accommodation.update', $property), ['decision' => 'approve'])->assertRedirect();
        $this->assertSame('published', $property->fresh()->status);
        $this->get(route('accommodation.show', $property->slug))->assertOk()->assertSee($property->name);
        $this->assertDatabaseHas('moderation_actions', ['to_status' => 'published']);
    }

    public function test_takedown_hides_published_property_and_room(): void
    {
        [$owner,$mitra,$refs] = $this->tenant();
        $property = $this->complete($this->property($owner, $mitra, $refs), $owner, $mitra);
        $property->update(['status' => 'published', 'published_at' => now()]);
        $room = $property->accommodation->rooms()->firstOrFail();
        $this->get(route('accommodation.rooms.show', [$property->slug, $room]))->assertOk();
        $admin = $this->admin();
        setPermissionsTeamId(null);
        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->patch(route('admin.accommodation.update', $property), ['decision' => 'takedown', 'reason' => 'Informasi properti tidak valid'])->assertRedirect();
        $this->get(route('accommodation.show', $property->slug))->assertNotFound();
        $this->get(route('accommodation.rooms.show', [$property->slug, $room]))->assertNotFound();
    }

    public function test_public_filters_by_query_price_capacity_and_category(): void
    {
        [$owner,$mitra,$refs] = $this->tenant();
        $property = $this->complete($this->property($owner, $mitra, $refs, 'Villa Pantai'), $owner, $mitra);
        $property->update(['status' => 'published', 'published_at' => now()]);
        $this->get(route('accommodation.index', ['q' => 'Pantai', 'max_price' => 260000, 'adults' => 2, 'category' => $refs['category']->slug]))->assertOk()->assertSee('Villa Pantai');
        $this->get(route('accommodation.index', ['max_price' => 100000]))->assertOk()->assertDontSee('Villa Pantai');
        $this->get(route('accommodation.index', ['adults' => 10]))->assertOk()->assertDontSee('Villa Pantai');
    }

    public function test_nearby_destination_filter_uses_spatial_location(): void
    {
        [$owner,$mitra,$refs] = $this->tenant();
        $property = $this->complete($this->property($owner, $mitra, $refs, 'Hotel Dekat'), $owner, $mitra);
        $property->update(['status' => 'published', 'published_at' => now()]);
        $tourismService = ServiceType::where('code', 'tourism')->firstOrFail();
        $mitra->features()->create(['service_type_id' => $tourismService->id, 'status' => 'enabled', 'enabled_at' => now()]);
        $destination = CatalogEntity::create(['mitra_id' => $mitra->id, 'service_type_id' => $tourismService->id, 'name' => 'Wisata Acuan', 'slug' => 'wisata-acuan', 'status' => 'published', 'published_at' => now()]);
        DB::statement('INSERT INTO catalog_locations (catalog_entity_id, location, latitude, longitude, created_at, updated_at) VALUES (?, ST_PointFromText(CONCAT("POINT(", ?, " ", ?, ")"), 4326), ?, ?, NOW(), NOW())', [$destination->id, 109.12, -6.87, -6.87, 109.12]);
        $this->get(route('accommodation.index', ['nearby_destination' => 'wisata-acuan', 'radius' => 5]))->assertOk()->assertSee('Hotel Dekat');
    }

    public function test_consumer_can_favorite_review_and_admin_can_publish_rating(): void
    {
        [$owner, $mitra, $refs] = $this->tenant();
        $property = $this->complete($this->property($owner, $mitra, $refs), $owner, $mitra);
        $property->update(['status' => 'published', 'published_at' => now()]);
        $consumer = User::factory()->create();
        setPermissionsTeamId(null);
        $consumer->assignRole('consumer');
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->actingAs($consumer)->post(route('accommodation.favorite', $property->slug))->assertRedirect();
        $this->actingAs($consumer)->post(route('accommodation.reviews.store', $property->slug), ['rating' => 4, 'body' => 'Kamar bersih dan nyaman.'])->assertRedirect();
        $review = $property->reviews()->firstOrFail();
        $this->assertSame('pending', $review->status);
        $admin = $this->admin();
        setPermissionsTeamId(null);
        $this->actingAs($admin)->withSession(['mfa_verified_at' => now()->timestamp])->patch(route('admin.accommodation.reviews.update', $review), ['decision' => 'publish'])->assertRedirect();
        $this->assertSame('published', $review->fresh()->status);
        $this->assertSame(1, $property->fresh()->rating_count);
        $this->assertSame('4.00', $property->fresh()->rating_average);
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
        $service = ServiceType::where('code', 'accommodation')->firstOrFail();
        $mitra->features()->create(['service_type_id' => $service->id, 'status' => 'enabled', 'enabled_at' => now()]);
        $category = Category::factory()->create(['service_type_id' => $service->id, 'is_active' => true]);
        $region = Region::factory()->create();

        return [$user, $mitra, compact('service', 'category', 'region')];
    }

    private function property(User $owner, Mitra $mitra, array $refs, string $name = 'Hotel Uji'): CatalogEntity
    {
        $slug = str()->slug($name).'-'.str()->lower(str()->random(6));
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.accommodation.store'), ['name' => $name, 'slug' => $slug, 'category_id' => $refs['category']->id, 'region_id' => $refs['region']->id, 'description' => 'Penginapan nyaman dekat pusat kota.', 'address' => 'Tegal', 'property_type' => 'hotel', 'check_in_time' => '14:00', 'check_out_time' => '12:00', 'latitude' => -6.87, 'longitude' => 109.12])->assertRedirect();

        return CatalogEntity::where('slug', $slug)->firstOrFail();
    }

    private function room(User $owner, Mitra $mitra, CatalogEntity $property): AccommodationRoom
    {
        $this->actingAs($owner)->withSession(['active_mitra_id' => $mitra->id])->post(route('mitra.accommodation.rooms.store', $property), $this->roomData())->assertRedirect();

        return $property->accommodation->rooms()->firstOrFail();
    }

    private function roomData(): array
    {
        return ['name' => 'Deluxe', 'room_type' => 'deluxe', 'kind' => 'room', 'capacity_adults' => 2, 'capacity_children' => 1, 'nightly_price' => 250000, 'total_units' => 5, 'min_stay_nights' => 1, 'max_stay_nights' => 7, 'advance_booking_days' => 90, 'status' => 'active'];
    }

    private function complete(CatalogEntity $property, User $owner, Mitra $mitra): CatalogEntity
    {
        $this->room($owner, $mitra, $property);
        $media = MediaAsset::factory()->create(['mitra_id' => $mitra->id, 'owner_user_id' => null, 'is_platform_owned' => false, 'visibility' => 'public', 'purpose' => 'accommodation']);
        $property->media()->attach($media->id, ['role' => 'cover', 'sort_order' => 0]);

        return $property->fresh();
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
