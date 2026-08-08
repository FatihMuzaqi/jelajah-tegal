<?php

namespace Tests\Feature;

use App\Models\ApplicationSetting;
use App\Models\FeatureFlag;
use App\Models\Mitra;
use App\Models\Region;
use App\Models\ServiceType;
use App\Models\User;
use Database\Seeders\FoundationReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FoundationReferenceSeeder::class);
    }

    public function test_guest_can_open_database_backed_landing_and_public_pages(): void
    {
        $this->get('/')->assertOk()->assertSee('Temukan pengalaman lokal');

        foreach (['/tentang', '/faq', '/kontak', '/kebijakan-privasi', '/syarat-ketentuan'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_only_approved_active_non_suspended_mitra_is_public(): void
    {
        $this->mitra('Mitra Terbit', 'active', true);
        $this->mitra('Mitra Draft', 'draft', false);
        $this->mitra('Mitra Taken Down', 'taken_down', true);
        $suspended = $this->mitra('Mitra Suspended', 'active', true);
        $suspended->update(['suspended_at' => now()]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Mitra Terbit')
            ->assertDontSee('Mitra Draft')
            ->assertDontSee('Mitra Taken Down')
            ->assertDontSee('Mitra Suspended');
    }

    public function test_search_and_region_filter_work(): void
    {
        $tegal = Region::factory()->create(['name' => 'Kota Tegal']);
        $brebes = Region::factory()->create(['name' => 'Kabupaten Brebes']);
        $this->mitra('Bahari Tegal', 'active', true, $tegal);
        $this->mitra('Kebun Brebes', 'active', true, $brebes);

        $this->get('/?q=Bahari')->assertOk()->assertSee('Bahari Tegal')->assertDontSee('Kebun Brebes');
        $this->get('/?region='.$brebes->id)->assertOk()->assertSee('Kebun Brebes')->assertDontSee('Bahari Tegal');
        $this->get('/?q=Kota%20Tegal')->assertOk()->assertSee('Bahari Tegal');
    }

    public function test_service_filter_uses_enabled_mitra_feature(): void
    {
        $tourism = ServiceType::where('code', 'tourism')->firstOrFail();
        $accommodation = ServiceType::where('code', 'accommodation')->firstOrFail();
        $tourismMitra = $this->mitra('Wisata Bahari', 'active', true);
        $hotelMitra = $this->mitra('Hotel Bahari', 'active', true);
        $tourismMitra->features()->create(['service_type_id' => $tourism->id, 'status' => 'enabled']);
        $hotelMitra->features()->create(['service_type_id' => $accommodation->id, 'status' => 'enabled']);

        $this->get('/?service=tourism')
            ->assertOk()
            ->assertSee('Wisata Bahari')
            ->assertDontSee('Hotel Bahari');
    }

    public function test_public_directory_is_paginated(): void
    {
        foreach (range(1, 10) as $number) {
            $this->mitra('Mitra '.str_pad((string) $number, 2, '0', STR_PAD_LEFT), 'active', true);
        }

        $this->get('/')->assertOk()->assertSee('Mitra 01')->assertDontSee('Mitra 10');
        $this->get('/?page=2')->assertOk()->assertSee('Mitra 10');
    }

    public function test_private_mitra_fields_never_leak(): void
    {
        $mitra = $this->mitra('Mitra Aman', 'active', true);
        $mitra->update([
            'legal_name' => 'PT Sangat Rahasia',
            'registration_number' => 'SECRET-REG-001',
            'tax_number_encrypted' => 'SECRET-TAX-001',
            'contact_email' => 'private@example.test',
            'contact_phone' => '081234567890',
        ]);

        $this->get('/')->assertOk()
            ->assertSee('Mitra Aman')
            ->assertDontSee('PT Sangat Rahasia')
            ->assertDontSee('SECRET-REG-001')
            ->assertDontSee('SECRET-TAX-001')
            ->assertDontSee('private@example.test')
            ->assertDontSee('081234567890');
    }

    public function test_public_content_requires_published_non_secret_setting(): void
    {
        $this->setting('public.about', ['published' => false, 'summary' => 'Draft internal']);
        $this->get('/tentang')->assertDontSee('Draft internal')->assertSee('belum tersedia');

        ApplicationSetting::query()->delete();
        $this->setting('public.about', ['published' => true, 'summary' => 'Profil resmi', 'paragraphs' => ['Konten resmi Lokantara.']]);
        $this->get('/tentang')->assertSee('Profil resmi')->assertSee('Konten resmi Lokantara.');
    }

    public function test_feature_flags_fail_closed_and_tourism_routes_follow_domain_flag(): void
    {
        FeatureFlag::where('key_name', 'public-ai-planner')->update(['status' => 'disabled']);
        $this->get('/')->assertDontSee('Mulai merencanakan');

        $this->assertTrue(Route::has('tourism.index'));
        $this->assertTrue(Route::has('tourism.show'));
        FeatureFlag::where('key_name', 'public-tourism')->update(['status' => 'disabled']);
        $this->get('/wisata')->assertNotFound();
        $this->get('/wisata/contoh')->assertNotFound();
        FeatureFlag::where('key_name', 'public-tourism')->update(['status' => 'enabled']);
        $this->get('/wisata')->assertOk();
    }

    private function mitra(string $name, string $status, bool $approved, ?Region $region = null): Mitra
    {
        return Mitra::factory()->create([
            'owner_user_id' => User::factory(),
            'display_name' => $name,
            'legal_name' => $name,
            'slug' => str($name)->slug().'-'.str()->lower(str()->random(6)),
            'description' => 'Deskripsi publik '.$name,
            'region_id' => $region?->id,
            'status' => $status,
            'approved_at' => $approved ? now() : null,
        ]);
    }

    private function setting(string $key, array $value): ApplicationSetting
    {
        return ApplicationSetting::create([
            'key_name' => $key,
            'value_json' => $value,
            'value_type' => 'json',
            'is_secret' => false,
        ]);
    }
}
