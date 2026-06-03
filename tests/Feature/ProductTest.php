<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    // ==================== INDEX (LISTING) ====================

    public function test_products_index_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Products');
        $response->assertSee('Add Product');
    }

    public function test_products_index_page_renders_for_staff()
    {
        $staff = User::factory()->create();
        Product::factory()->count(3)->create();

        $response = $this->actingAs($staff)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Products');
        $response->assertDontSee('Add Product');
    }

    public function test_guest_cannot_access_products_index()
    {
        $response = $this->get(route('products.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_products_index_displays_all_products()
    {
        $admin = User::factory()->admin()->create();
        $products = Product::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get(route('products.index'));

        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

    public function test_products_index_shows_empty_state()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('products.index'));

        $response->assertSee('No products found');
    }

    public function test_products_index_search_by_name()
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->create(['name' => 'Unique Widget', 'sku' => 'UW-001']);
        Product::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('products.index', ['search' => 'Unique']));

        $response->assertStatus(200);
        $response->assertSee('Unique Widget');
    }

    public function test_products_index_search_returns_empty_when_no_match()
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->create(['name' => 'Existing Product', 'sku' => 'EXIST-001']);

        $response = $this->actingAs($admin)->get(route('products.index', ['search' => 'Nonexistent']));

        $response->assertStatus(200);
        $response->assertSee('No products found');
    }

    public function test_products_index_paginates_results()
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(16)->create();

        $response = $this->actingAs($admin)->get(route('products.index'));

        $response->assertStatus(200);
        // Pagination links should appear with page 2 (15 per page)
        $response->assertSee('page=2');
    }

    // ==================== CREATE (FORM) ====================

    public function test_create_product_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('products.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Product');
        $response->assertSee('Product Name');
        $response->assertSee('Price');
    }

    public function test_create_product_page_denied_for_staff()
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->get(route('products.create'));

        $response->assertStatus(403);
        $response->assertSee('403');
    }

    public function test_create_product_page_redirects_guest()
    {
        $response = $this->get(route('products.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_create_product_page_shows_categories()
    {
        $admin = User::factory()->admin()->create();
        $categories = Category::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('products.create'));

        foreach ($categories as $cat) {
            $response->assertSee($cat->name);
        }
    }

    // ==================== STORE (CREATE ACTION) ====================

    public function test_admin_can_create_product()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'New Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'quantity' => 50,
            'reorder_level' => 10,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Product created successfully.');

        $this->assertDatabaseHas('products', [
            'name' => 'New Test Product',
            'price' => 99.99,
            'quantity' => 50,
            'reorder_level' => 10,
        ]);
    }

    public function test_admin_can_create_product_with_category()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Categorized Product',
            'sku' => 'CAT-001',
            'price' => 49.99,
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Categorized Product',
            'category_id' => $category->id,
        ]);
    }

    public function test_staff_cannot_create_product()
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->post(route('products.store'), [
            'name' => 'Unauthorized Product',
            'sku' => 'UNAUTH-001',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', ['name' => 'Unauthorized Product']);
    }

    public function test_product_name_is_required()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => '',
            'sku' => 'REQ-001',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_price_is_required()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Product Without Price',
            'sku' => 'NOPRICE-001',
            'price' => '',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_product_name_must_be_unique()
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->create(['name' => 'Existing Product']);

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Existing Product',
            'sku' => 'UNIQUE-001',
            'price' => 10.00,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_price_cannot_be_negative()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Negative Price Product',
            'sku' => 'NEG-001',
            'price' => -5.00,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_product_quantity_cannot_be_negative()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Negative Qty Product',
            'sku' => 'NEGQTY-001',
            'price' => 10.00,
            'quantity' => -1,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_product_creation_logs_activity()
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Activity Logged Product',
            'sku' => 'LOG-001',
            'price' => 25.00,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'created',
            'model_type' => 'Product',
            'description' => 'Created Product: "Activity Logged Product"',
        ]);
    }

    // ==================== EDIT (FORM) ====================

    public function test_edit_product_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();
        Category::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('products.edit', $product));

        $response->assertStatus(200);
        $response->assertSee('Edit');
        $response->assertSee($product->name);
        $response->assertSee((string) $product->price);
    }

    public function test_edit_product_page_denied_for_staff()
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($staff)->get(route('products.edit', $product));

        $response->assertStatus(403);
    }

    public function test_edit_product_page_redirects_guest()
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.edit', $product));

        $response->assertRedirect(route('login'));
    }

    // ==================== UPDATE ====================

    public function test_admin_can_update_product()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 10.00,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($admin)->put(route('products.update', $product), [
            'name' => 'Updated Name',
            'sku' => $product->sku,
            'price' => 20.00,
            'quantity' => 5,
            'reorder_level' => 3,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Product updated successfully.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 20.00,
        ]);
    }

    public function test_admin_can_update_product_with_category()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->put(route('products.update', $product), [
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price,
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_staff_cannot_update_product()
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Original']);

        $response = $this->actingAs($staff)->put(route('products.update', $product), [
            'name' => 'Hacked Name',
            'sku' => $product->sku,
            'price' => 1.00,
            'status' => 'active',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['name' => 'Original']);
    }

    public function test_product_update_name_unique_excludes_self()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Self Name']);

        // Should allow updating with the same name (unique rule excludes self)
        $response = $this->actingAs($admin)->put(route('products.update', $product), [
            'name' => 'Self Name',
            'sku' => $product->sku,
            'price' => 15.00,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Self Name',
        ]);
    }

    public function test_product_update_name_must_be_unique_across_others()
    {
        $admin = User::factory()->admin()->create();
        $productA = Product::factory()->create(['name' => 'Product A']);
        Product::factory()->create(['name' => 'Product B']);

        $response = $this->actingAs($admin)->put(route('products.update', $productA), [
            'name' => 'Product B',
            'sku' => $productA->sku,
            'price' => 10.00,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_update_quantity_cannot_be_negative()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->put(route('products.update', $product), [
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price,
            'quantity' => -5,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_product_update_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create([
            'name' => 'Before Update',
            'price' => 5.00,
        ]);

        $this->actingAs($admin)->put(route('products.update', $product), [
            'name' => 'After Update',
            'sku' => $product->sku,
            'price' => 10.00,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'updated',
            'model_type' => 'Product',
        ]);
    }

    // ==================== DESTROY ====================

    public function test_admin_can_delete_product()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Product deleted successfully.');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_staff_cannot_delete_product()
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($staff)->delete(route('products.destroy', $product));

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_guest_cannot_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_delete_nonexistent_product_returns_404()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('products.destroy', 99999));

        $response->assertStatus(404);
    }

    public function test_product_deletion_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Deletable Product']);

        $this->actingAs($admin)->delete(route('products.destroy', $product));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'deleted',
            'model_type' => 'Product',
            'description' => 'Deleted Product: "Deletable Product"',
        ]);
    }

    // ==================== AUTHORIZATION EDGE CASES ====================

    

    // ==================== MODEL RELATIONSHIPS ====================

    public function test_product_belongs_to_category()
    {
        $category = Category::factory()->create(['name' => 'Electronics']);
        $product = Product::factory()->withCategory()->create();

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertNotNull($product->category);
    }

    public function test_product_total_value_is_calculated()
    {
        $product = Product::factory()->create([
            'quantity' => 10,
            'price' => 25.00,
        ]);

        $this->assertEqualsWithDelta(250.00, $product->total_value, 0.001);
    }

    public function test_product_can_have_null_category()
    {
        $product = Product::factory()->create(['category_id' => null]);

        $this->assertNull($product->category);
    }

    public function test_product_has_default_values()
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Default Values Product',
            'sku' => 'DEF-001',
            'price' => 100.00,
            'status' => 'active',
            // quantity and reorder_level not provided
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Default Values Product',
            'quantity' => 0,   // default from migration
            'reorder_level' => 5, // default from migration
        ]);
    }

    // ==================== BULK DATA ====================

    public function test_products_index_shows_correct_count()
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(7)->create();

        $response = $this->actingAs($admin)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('7 total products');
    }
}
