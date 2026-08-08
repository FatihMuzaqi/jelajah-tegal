<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        setPermissionsTeamId(null);
    }

    public function test_application_boots_and_uses_mysql(): void
    {
        $this->get('/')->assertOk();
        $this->assertSame('mysql', DB::connection()->getDriverName());
        DB::select('select 1');
    }

    public function test_registration_assigns_consumer_and_sends_verification(): void
    {
        Notification::fake();
        $this->post('/register', ['name' => 'Ayu', 'email' => 'ayu@example.test', 'password' => 'secret123', 'password_confirmation' => 'secret123'])->assertRedirect(route('verification.notice'));
        $u = User::whereEmail('ayu@example.test')->firstOrFail();
        $this->assertTrue($u->hasRole('consumer'));
        Notification::assertSentTo($u, VerifyEmail::class);
    }

    public function test_login_and_logout(): void
    {
        $u = User::factory()->create();
        $this->post('/login', ['email' => $u->email, 'password' => 'password'])->assertRedirect(route('post-login'));
        $this->assertAuthenticatedAs($u);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_password_can_be_reset(): void
    {
        $u = User::factory()->create();
        $token = app('auth.password.broker')->createToken($u);
        $this->post('/reset-password', ['email' => $u->email, 'token' => $token, 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('new-password', $u->credential->fresh()->password_hash));
    }

    public function test_email_verification(): void
    {
        $u = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(10), ['id' => $u->id, 'hash' => sha1($u->email)]);
        $this->actingAs($u)->get($url)->assertRedirect(route('post-login'));
        $this->assertNotNull($u->fresh()->email_verified_at);
    }

    public function test_role_and_permission_seeders(): void
    {
        $this->assertCount(6, Role::all());
        $this->assertSame(count(RolesAndPermissionsSeeder::PERMISSIONS), Permission::count());
        $this->assertTrue(Role::findByName('super-admin')->hasPermissionTo('roles.manage'));
    }

    public function test_single_role_redirect(): void
    {
        $u = User::factory()->create();
        $u->assignRole('consumer');
        $this->actingAs($u)->get('/post-login')->assertRedirect(route('consumer.dashboard'));
    }

    public function test_multi_role_uses_surface_selector(): void
    {
        $u = User::factory()->create();
        $u->assignRole(['consumer', 'admin']);
        $this->actingAs($u)->get('/post-login')->assertRedirect(route('surfaces.select'));
    }

    public function test_forbidden_surface_returns_403(): void
    {
        $u = User::factory()->create();
        $u->assignRole('consumer');
        $this->actingAs($u)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_multi_mitra_selector_establishes_authorized_context(): void
    {
        $u = User::factory()->create();
        $a = Mitra::create(['owner_user_id' => $u->id, 'legal_name' => 'A', 'display_name' => 'A', 'slug' => 'a', 'status' => 'active']);
        $b = Mitra::create(['owner_user_id' => $u->id, 'legal_name' => 'B', 'display_name' => 'B', 'slug' => 'b', 'status' => 'active']);
        foreach ([$a, $b] as $m) {
            $u->mitraMemberships()->create(['mitra_id' => $m->id, 'status' => 'active']);
            setPermissionsTeamId($m->id);
            $u->assignRole('mitra-owner');
        }setPermissionsTeamId(null);
        $this->actingAs($u)->post('/select-mitra', ['mitra_id' => $b->id])->assertRedirect(route('mitra.dashboard'));
        $this->assertSame($b->id, session('active_mitra_id'));
    }

    public function test_password_reset_link_is_dispatched(): void
    {
        Notification::fake();
        $u = User::factory()->create();
        $this->post('/forgot-password', ['email' => $u->email])->assertSessionHas('status');
        Notification::assertSentTo($u, ResetPassword::class);
    }
}
