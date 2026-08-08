<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DashboardShellTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_public_layout_can_be_rendered(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Lokantara')
            ->assertSee('public-header', false);
    }

    public function test_consumer_dashboard_renders_permission_aware_shell_and_empty_state(): void
    {
        $user = $this->globalUser('consumer');

        $this->actingAs($user)->get(route('consumer.dashboard'))
            ->assertOk()
            ->assertSee('Portal Consumer')
            ->assertSee('dashboard-sidebar', false)
            ->assertSee('data-mobile-navigation', false)
            ->assertSee('Notifikasi')
            ->assertDontSee('Feature Flag')
            ->assertSee('Belum memiliki Mitra');
    }

    public function test_mitra_dashboard_uses_active_tenant_and_renders_actual_membership(): void
    {
        [$user, $mitra] = $this->tenantUser('mitra-owner');

        $this->actingAs($user)->withSession(['active_mitra_id' => $mitra->id])
            ->get(route('mitra.dashboard'))
            ->assertOk()
            ->assertSee('Portal Mitra')
            ->assertSee($mitra->display_name)
            ->assertSee($user->name)
            ->assertSee('data-sidebar-open', false)
            ->assertSee('data-sidebar-close', false);
    }

    public function test_gatekeeper_dashboard_renders_empty_assignment_without_error(): void
    {
        [$user, $mitra] = $this->tenantUser('gatekeeper');

        $this->actingAs($user)->withSession(['active_mitra_id' => $mitra->id])
            ->get(route('gatekeeper.dashboard'))
            ->assertOk()
            ->assertSee('Portal Gatekeeper')
            ->assertSee('Belum ada assignment');
    }

    public function test_admin_and_super_admin_dashboards_render_after_mfa(): void
    {
        foreach (['admin', 'super-admin'] as $role) {
            $user = $this->globalUser($role);
            $user->credential()->update(['mfa_confirmed_at' => now()]);

            $this->actingAs($user)->withSession(['mfa_verified_at' => now()->timestamp])
                ->get(route($role.'.dashboard'))
                ->assertOk()
                ->assertSee('dashboard-shell', false);
        }
    }

    public function test_all_dashboard_routes_are_protected_from_guests(): void
    {
        foreach (['consumer', 'mitra', 'gatekeeper', 'admin', 'super-admin'] as $surface) {
            $this->get('/'.$surface.'/dashboard')->assertRedirect(route('login'));
        }
    }

    public function test_user_without_surface_permission_receives_403(): void
    {
        $user = $this->globalUser('consumer');

        $this->actingAs($user)->get(route('admin.dashboard'))
            ->assertForbidden()
            ->assertSee('Akses ditolak');
    }

    private function globalUser(string $role): User
    {
        setPermissionsTeamId(null);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function tenantUser(string $role): array
    {
        $user = User::factory()->create();
        $mitra = Mitra::factory()->for($user, 'owner')->create();
        $user->mitraMemberships()->create([
            'mitra_id' => $mitra->id,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        setPermissionsTeamId($mitra->id);
        $user->assignRole($role);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        setPermissionsTeamId(null);

        return [$user, $mitra];
    }
}
