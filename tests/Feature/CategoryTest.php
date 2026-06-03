<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    // ==================== INDEX (LISTING) ====================

    public function test_categories_index_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Categories');
        $response->assertSee('Product Categories');
        $response->assertSee('Add Category');
    }

    public function test_categories_index_page_denied_for_staff()
    {
        $staff = User::factory()->create();
        Category::factory()->count(3)->create();

        $response = $this->actingAs($staff)->get(route('categories.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_categories_index()
    {
        $response = $this->get(route('categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_categories_index_displays_all_categories()
    {
        $admin = User::factory()->admin()->create();
        $categories = Category::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get(route('categories.index'));

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    public function test_categories_index_shows_empty_state()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('categories.index'));

        $response->assertSee('No categories yet');
    }

    public function test_categories_index_shows_product_count()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->get(route('categories.index'));

        $response->assertSee('3');
    }

    public function test_categories_index_paginates_results()
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->count(15)->create();

        $response = $this->actingAs($admin)->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('page=2');
    }

    // ==================== CREATE (FORM) ====================

    public function test_create_category_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('categories.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Category');
        $response->assertSee('New Category Details');
        $response->assertSee('Category Name');
        $response->assertSee('Description');
    }

    public function test_create_category_page_denied_for_staff()
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->get(route('categories.create'));

        $response->assertStatus(403);
    }

    public function test_create_category_page_redirects_guest()
    {
        $response = $this->get(route('categories.create'));

        $response->assertRedirect(route('login'));
    }

    // ==================== STORE (CREATE ACTION) ====================

    public function test_admin_can_create_category()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('categories.store'), [
            'name' => 'Test Category',
            'description' => 'This is a test category description.',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success', 'Category created successfully.');

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'description' => 'This is a test category description.',
        ]);
    }

    public function test_admin_can_create_category_without_description()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('categories.store'), [
            'name' => 'No Description Category',
        ]);

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'No Description Category',
            'description' => null,
        ]);
    }

    public function test_staff_cannot_create_category()
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->post(route('categories.store'), [
            'name' => 'Unauthorized Category',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('categories', ['name' => 'Unauthorized Category']);
    }

    public function test_category_name_is_required()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_category_name_must_be_unique()
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Existing Category']);

        $response = $this->actingAs($admin)->post(route('categories.store'), [
            'name' => 'Existing Category',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_category_name_max_length()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('categories.store'), [
            'name' => str_repeat('A', 256),
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_category_description_max_length()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('categories.store'), [
            'name' => 'Valid Name',
            'description' => str_repeat('A', 1001),
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_category_creation_logs_activity()
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('categories.store'), [
            'name' => 'Activity Logged Category',
            'description' => 'Check activity log',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'created',
            'model_type' => 'Category',
            'description' => 'Created Category: "Activity Logged Category"',
        ]);
    }

    // ==================== EDIT (FORM) ====================

    public function test_edit_category_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create([
            'name' => 'Editable Category',
            'description' => 'Will be edited',
        ]);

        $response = $this->actingAs($admin)->get(route('categories.edit', $category));

        $response->assertStatus(200);
        $response->assertSee('Edit Category');
        $response->assertSee('Editable Category');
        $response->assertSee('Will be edited');
    }

    public function test_edit_category_page_denied_for_staff()
    {
        $staff = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($staff)->get(route('categories.edit', $category));

        $response->assertStatus(403);
    }

    public function test_edit_category_page_redirects_guest()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('categories.edit', $category));

        $response->assertRedirect(route('login'));
    }

    // ==================== UPDATE ====================

    public function test_admin_can_update_category()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original description.',
        ]);

        $response = $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => 'Updated Name',
            'description' => 'Updated description.',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success', 'Category updated successfully.');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'description' => 'Updated description.',
        ]);
    }

    public function test_admin_can_update_category_clear_description()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create([
            'name' => 'Has Description',
            'description' => 'Something here',
        ]);

        $response = $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => 'Has Description',
            'description' => '',
        ]);

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'description' => null,
        ]);
    }

    public function test_staff_cannot_update_category()
    {
        $staff = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Original']);

        $response = $this->actingAs($staff)->put(route('categories.update', $category), [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', ['name' => 'Original']);
    }

    public function test_category_update_name_unique_excludes_self()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Self Name']);

        // Should allow updating with the same name (unique rule excludes self)
        $response = $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => 'Self Name',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Self Name',
        ]);
    }

    public function test_category_update_name_must_be_unique_across_others()
    {
        $admin = User::factory()->admin()->create();
        $categoryA = Category::factory()->create(['name' => 'Category A']);
        Category::factory()->create(['name' => 'Category B']);

        $response = $this->actingAs($admin)->put(route('categories.update', $categoryA), [
            'name' => 'Category B',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_category_update_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create([
            'name' => 'Before Update',
        ]);

        $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => 'After Update',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'updated',
            'model_type' => 'Category',
        ]);
    }

    public function test_category_update_name_must_not_be_too_long()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => str_repeat('A', 256),
        ]);

        $response->assertSessionHasErrors('name');
    }

    // ==================== DESTROY ====================

    public function test_admin_can_delete_category()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Deletable Category']);

        $response = $this->actingAs($admin)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success', 'Category deleted successfully.');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_staff_cannot_delete_category()
    {
        $staff = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($staff)->delete(route('categories.destroy', $category));

        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_guest_cannot_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_delete_nonexistent_category_returns_404()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('categories.destroy', 99999));

        $response->assertStatus(404);
    }

    public function test_deleting_category_unassigns_its_products()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Products Category']);
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);

        $this->actingAs($admin)->delete(route('categories.destroy', $category));

        // Category should be deleted
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        // Products should still exist with category_id set to null
        foreach ($products as $product) {
            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'category_id' => null,
            ]);
        }
    }

    public function test_category_deletion_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Activity Test Category']);

        $this->actingAs($admin)->delete(route('categories.destroy', $category));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'deleted',
            'model_type' => 'Category',
            'description' => 'Deleted Category: "Activity Test Category"',
        ]);
    }

    // ==================== AUTHORIZATION EDGE CASES ====================

    public function test_admin_role_can_perform_all_actions()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        // Index
        $this->actingAs($admin)->get(route('categories.index'))->assertStatus(200);

        // Create form
        $this->actingAs($admin)->get(route('categories.create'))->assertStatus(200);

        // Store
        $this->actingAs($admin)->post(route('categories.store'), [
            'name' => 'Another Category',
        ])->assertRedirect();

        // Edit form
        $this->actingAs($admin)->get(route('categories.edit', $category))->assertStatus(200);

        // Update
        $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => 'Updated Category',
        ])->assertRedirect();

        // Delete
        $this->actingAs($admin)->delete(route('categories.destroy', $category))->assertRedirect();
    }

    public function test_staff_role_cannot_perform_any_admin_actions()
    {
        $staff = User::factory()->create();
        $category = Category::factory()->create();

        // Index is denied entirely for staff (can:admin middleware)
        $this->actingAs($staff)->get(route('categories.index'))->assertStatus(403);

        // Create form
        $this->actingAs($staff)->get(route('categories.create'))->assertStatus(403);

        // Store
        $this->actingAs($staff)->post(route('categories.store'), [
            'name' => 'Staff Category',
        ])->assertStatus(403);

        // Edit form
        $this->actingAs($staff)->get(route('categories.edit', $category))->assertStatus(403);

        // Update
        $this->actingAs($staff)->put(route('categories.update', $category), [
            'name' => 'Staff Update',
        ])->assertStatus(403);

        // Delete
        $this->actingAs($staff)->delete(route('categories.destroy', $category))->assertStatus(403);
    }

    // ==================== MODEL RELATIONSHIPS ====================

    public function test_category_has_products_relationship()
    {
        $category = Category::factory()->create();
        $products = Product::factory()->count(2)->create(['category_id' => $category->id]);

        $this->assertCount(2, $category->products);
        $this->assertInstanceOf(Product::class, $category->products->first());
    }

    public function test_category_can_have_zero_products()
    {
        $category = Category::factory()->create();

        $this->assertCount(0, $category->products);
        $this->assertEquals(0, $category->products_count ?? 0);
    }

    // ==================== BULK DATA ====================

    public function test_categories_index_shows_correct_total_count()
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->count(7)->create();

        $response = $this->actingAs($admin)->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('(7)');
    }
}
