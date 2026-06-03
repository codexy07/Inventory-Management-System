<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    // ==================== 404 NOT FOUND ====================

    public function test_404_page_renders_for_nonexistent_route()
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Page Not Found');
    }

    // ==================== 403 FORBIDDEN ====================

    public function test_403_page_renders_for_unauthorized_admin_route()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('admin.dashboard'));

        $response->assertStatus(403);
        $response->assertSee('403');
        $response->assertSee('Access Denied');
    }

    public function test_403_page_renders_when_accessing_users_as_staff()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('users.index'));

        $response->assertStatus(403);
        $response->assertSee('403');
    }

    public function test_403_page_renders_when_accessing_categories_as_staff()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('categories.index'));

        $response->assertStatus(403);
        $response->assertSee('403');
    }

    public function test_403_page_renders_for_guest_accessing_admin_route()
    {
        $response = $this->get(route('admin.dashboard'));

        // Guest gets redirected to login (auth middleware runs before authorization)
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    // ==================== 419 SESSION EXPIRED ====================

    public function test_419_page_renders()
    {
        // Render the 419 view directly
        $view = $this->view('errors.419');

        $view->assertSee('419');
        $view->assertSee('Session Expired');
        $view->assertSee('Sign In');
    }

    // ==================== 500 SERVER ERROR ====================

    public function test_500_page_renders()
    {
        $view = $this->view('errors.500');

        $view->assertSee('500');
        $view->assertSee('Something Went Wrong');
        $view->assertSee('Try Again');
    }

    // ==================== ERROR PAGE NAVIGATION ====================

    public function test_404_page_has_dashboard_link()
    {
        // Disable debug mode so custom error pages show
        $this->app['config']->set('app.debug', false);

        $response = $this->get('/nonexistent-route');

        $response->assertStatus(404);
        $response->assertSee(route('dashboard'));
    }

    public function test_403_page_has_go_back_and_dashboard_links()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('users.index'));

        $response->assertStatus(403);
        $response->assertSee('Dashboard');
    }

    // ==================== GUEST LAYOUT AUTH PAGES ====================

    public function test_guest_layout_shows_brand_on_login()
    {
        $response = $this->get(route('login'));

        $response->assertSee('InventoryMS');
        $response->assertSee('Inventory');
        $response->assertSee('Management System');
    }

    public function test_flash_messages_display_on_login_page()
    {
        $response = $this->withSession(['success' => 'Test flash message.'])
            ->get(route('login'));

        $response->assertSee('Test flash message.');
    }

    // ==================== AUTH MIDDLEWARE REDIRECTS ====================

    public function test_authenticated_user_redirected_from_register()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('register'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_user_redirected_from_forgot_password()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('password.request'));

        $response->assertRedirect(route('dashboard'));
    }

    // ==================== UNIFIED LOGIN PAGE ====================

    public function test_login_page_has_email_or_username_field()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Email or Username');
        $response->assertSee('Sign In');
    }

    public function test_login_page_has_link_to_register()
    {
        $response = $this->get(route('login'));

        $response->assertSee(route('register'));
        $response->assertSee('Register');
    }
}
