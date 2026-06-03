<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable CSRF verification during tests since it interferes with POST requests
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    // ==================== LOGIN ====================

    public function test_login_page_renders()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
        $response->assertSee('Sign In');
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'correct-password'),
        ]);

        $response = $this->post(route('login'), [
            'login' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_staff_user_can_login_with_email()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'correct-password'),
        ]);

        $response = $this->post(route('login'), [
            'login' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_with_email()
    {
        $admin = User::factory()->admin()->create([
            'password' => bcrypt($password = 'admin-password'),
            'username' => 'adminuser',
        ]);

        $response = $this->post(route('login'), [
            'login' => $admin->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_login_with_username()
    {
        $admin = User::factory()->admin()->create([
            'password' => bcrypt($password = 'admin-password'),
            'username' => 'adminuser',
        ]);

        $response = $this->post(route('login'), [
            'login' => 'adminuser',
            'password' => $password,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_user_cannot_login_with_invalid_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->post(route('login'), [
            'login' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_nonexistent_credentials()
    {
        $response = $this->post(route('login'), [
            'login' => 'nonexistent@example.com',
            'password' => 'some-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_login_validates_required_fields()
    {
        $response = $this->post(route('login'), []);

        $response->assertSessionHasErrors(['login', 'password']);
    }

    public function test_authenticated_user_is_redirected_from_login_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_redirected_from_login_page_to_admin_dashboard()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('login'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_remember_me_functionality()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'correct-password'),
        ]);

        $response = $this->post(route('login'), [
            'login' => $user->email,
            'password' => $password,
            'remember' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $response->assertSessionMissing('error');
    }

    // ==================== REGISTRATION ====================

    public function test_register_page_renders()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Create Account');
    }

    public function test_user_can_register()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'staff',
        ]);
    }

    public function test_registration_requires_unique_email()
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Another User',
            'email' => 'taken@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_password_confirmation()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_requires_minimum_password_length()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_validates_required_fields()
    {
        $response = $this->post(route('register'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    // ==================== LOGOUT ====================

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'You have been logged out successfully.');
        $this->assertGuest();
    }

    public function test_guest_cannot_logout()
    {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
    }

    // ==================== DASHBOARD ACCESS ====================

    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_staff_cannot_access_admin_dashboard()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    // ==================== PASSWORD RESET ====================

    public function test_forgot_password_page_renders()
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Forgot');
    }

    public function test_reset_password_page_renders_with_token()
    {
        $response = $this->get(route('password.reset', ['token' => 'test-token']));

        $response->assertStatus(200);
    }
}
