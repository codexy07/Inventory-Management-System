<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    // ==================== INDEX (LISTING) ====================

    public function test_stock_out_index_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        StockOut::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('stock.out.index'));

        $response->assertStatus(200);
        $response->assertSee('Stock Out');
        $response->assertSee('Stock Out Transactions');
        $response->assertSee('Remove Stock');
    }

    public function test_stock_out_index_page_renders_for_staff()
    {
        $staff = User::factory()->create();
        StockOut::factory()->count(3)->create();

        $response = $this->actingAs($staff)->get(route('stock.out.index'));

        $response->assertStatus(200);
        $response->assertSee('Stock Out');
        $response->assertDontSee('Remove Stock');
    }

    public function test_guest_cannot_access_stock_out_index()
    {
        $response = $this->get(route('stock.out.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_stock_out_index_displays_transactions()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Widget B']);
        $stockOut = StockOut::factory()->create([
            'product_id' => $product->id,
            'quantity' => 15,
        ]);

        $response = $this->actingAs($admin)->get(route('stock.out.index'));

        $response->assertSee('Widget B');
        $response->assertSee('-15');
    }

    public function test_stock_out_index_shows_empty_state()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('stock.out.index'));

        $response->assertSee('No stock-out transactions found');
    }

    public function test_stock_out_index_search_by_product_name()
    {
        $admin = User::factory()->admin()->create();
        $productA = Product::factory()->create(['name' => 'Searchable Product']);
        $productB = Product::factory()->create(['name' => 'Another Product']);
        StockOut::factory()->create(['product_id' => $productA->id]);
        StockOut::factory()->create(['product_id' => $productB->id]);

        $response = $this->actingAs($admin)->get(route('stock.out.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
        $response->assertSee('Searchable Product');
        $response->assertDontSee('Another Product');
    }

    public function test_stock_out_index_search_returns_empty_when_no_match()
    {
        $admin = User::factory()->admin()->create();
        StockOut::factory()->create();

        $response = $this->actingAs($admin)->get(route('stock.out.index', ['search' => 'Nonexistent']));

        $response->assertStatus(200);
        $response->assertSee('No stock-out transactions found');
    }

    public function test_stock_out_index_paginates_results()
    {
        $admin = User::factory()->admin()->create();
        StockOut::factory()->count(20)->create();

        $response = $this->actingAs($admin)->get(route('stock.out.index'));

        $response->assertStatus(200);
        $response->assertSee('page=2');
    }

    // ==================== CREATE (FORM) ====================

    public function test_create_stock_out_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        // Create a product with stock so it appears in the "only > 0" filter
        Product::factory()->create(['quantity' => 10, 'name' => 'Stocked Product']);

        $response = $this->actingAs($admin)->get(route('stock.out.create'));

        $response->assertStatus(200);
        $response->assertSee('Remove Stock from Inventory');
        $response->assertSee('Product');
        $response->assertSee('Quantity');
        $response->assertSee('Date');
        $response->assertSee('Stocked Product');
    }

    public function test_create_stock_out_page_only_shows_products_with_stock()
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->create(['name' => 'In Stock Product', 'quantity' => 10]);
        Product::factory()->create(['name' => 'Out Of Stock Product', 'quantity' => 0]);

        $response = $this->actingAs($admin)->get(route('stock.out.create'));

        $response->assertSee('In Stock Product');
        $response->assertDontSee('Out Of Stock Product');
    }

    public function test_create_stock_out_page_denied_for_staff()
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->get(route('stock.out.create'));

        $response->assertStatus(403);
    }

    public function test_create_stock_out_page_redirects_guest()
    {
        $response = $this->get(route('stock.out.create'));

        $response->assertRedirect(route('login'));
    }

    // ==================== STORE (CREATE ACTION) ====================

    public function test_admin_can_create_stock_out()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 50]);

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 20,
            'date' => '2025-06-01',
        ]);

        $response->assertRedirect(route('stock.out.index'));
        $response->assertSessionHas('success', 'Stock removed successfully.');

        $this->assertDatabaseHas('stock_out', [
            'product_id' => $product->id,
            'quantity' => 20,
            'date' => '2025-06-01',
        ]);

        // Product quantity should be decremented
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 30,
        ]);
    }

    public function test_staff_cannot_create_stock_out()
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['quantity' => 50]);

        $response = $this->actingAs($staff)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => '2025-06-01',
        ]);

        $response->assertStatus(403);

        // Product quantity should NOT be decremented
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 50,
        ]);
    }

    public function test_stock_out_rejects_insufficient_stock()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 10, 'name' => 'Limited Product']);

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 50,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('quantity');
        $response->assertRedirect();

        // Product quantity should remain unchanged
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 10,
        ]);
    }

    public function test_stock_out_product_id_is_required()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => '',
            'quantity' => 5,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_stock_out_product_id_must_exist()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => 99999,
            'quantity' => 5,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_stock_out_quantity_is_required()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => '',
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stock_out_quantity_must_be_at_least_1()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 10]);

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 0,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stock_out_quantity_must_be_integer()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 10]);

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 5.5,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stock_out_date_is_required()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => '',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_stock_out_date_must_be_valid_format()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => '06/01/2025',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_stock_out_creation_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 100, 'name' => 'Activity Out Item']);

        $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 10,
            'date' => '2025-06-01',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'created',
            'model_type' => 'StockOut',
        ]);
    }

    // ==================== EDIT (FORM) ====================

    public function test_edit_stock_out_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Editable Out Product']);
        $stockOut = StockOut::factory()->create([
            'product_id' => $product->id,
            'quantity' => 12,
        ]);

        $response = $this->actingAs($admin)->get(route('stock.out.edit', $stockOut));

        $response->assertStatus(200);
        $response->assertSee('Edit Stock-Out Record');
        $response->assertSee('Editable Out Product');
    }

    public function test_edit_stock_out_page_denied_for_staff()
    {
        $staff = User::factory()->create();
        $stockOut = StockOut::factory()->create();

        $response = $this->actingAs($staff)->get(route('stock.out.edit', $stockOut));

        $response->assertStatus(403);
    }

    public function test_edit_stock_out_page_redirects_guest()
    {
        $stockOut = StockOut::factory()->create();

        $response = $this->get(route('stock.out.edit', $stockOut));

        $response->assertRedirect(route('login'));
    }

    // ==================== UPDATE ====================

    public function test_admin_can_update_stock_out_decrease_quantity_removes_more_stock()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 50]);
        $stockOut = StockOut::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        // Change quantity from 10 to 15 (diff = -5, more stock removed)
        $response = $this->actingAs($admin)->put(route('stock.out.update', $stockOut), [
            'product_id' => $product->id,
            'quantity' => 15,
            'date' => $stockOut->date->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('stock.out.index'));
        $response->assertSessionHas('success', 'Stock-out record updated successfully.');

        $this->assertDatabaseHas('stock_out', [
            'id' => $stockOut->id,
            'quantity' => 15,
        ]);

        // Product quantity: 50 - 5 (extra) = 45
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 45,
        ]);
    }

    public function test_admin_can_update_stock_out_increase_quantity_restores_stock()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 50]);
        $stockOut = StockOut::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        // Change quantity from 20 to 8 (diff = +12, stock restored)
        $response = $this->actingAs($admin)->put(route('stock.out.update', $stockOut), [
            'product_id' => $product->id,
            'quantity' => 8,
            'date' => $stockOut->date->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('stock.out.index'));

        $this->assertDatabaseHas('stock_out', [
            'id' => $stockOut->id,
            'quantity' => 8,
        ]);

        // Product quantity: 50 + 12 (restored) = 62
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 62,
        ]);
    }

    public function test_admin_can_update_stock_out_same_quantity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 50]);
        $stockOut = StockOut::factory()->create([
            'product_id' => $product->id,
            'quantity' => 15,
        ]);

        // Same quantity (diff = 0) — product quantity unchanged
        $response = $this->actingAs($admin)->put(route('stock.out.update', $stockOut), [
            'product_id' => $product->id,
            'quantity' => 15,
            'date' => $stockOut->date->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('stock.out.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 50,
        ]);
    }

    public function test_stock_out_update_rejects_insufficient_stock_when_removing_more()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 10]);
        $stockOut = StockOut::factory()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        // Try changing quantity from 5 to 20 (needs 15 more, only 10 available)
        $response = $this->actingAs($admin)->put(route('stock.out.update', $stockOut), [
            'product_id' => $product->id,
            'quantity' => 20,
            'date' => $stockOut->date->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('quantity');
        $response->assertRedirect();

        // Product quantity should remain unchanged
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 10,
        ]);
    }

    public function test_staff_cannot_update_stock_out()
    {
        $staff = User::factory()->create();
        $stockOut = StockOut::factory()->create();

        $response = $this->actingAs($staff)->put(route('stock.out.update', $stockOut), [
            'product_id' => $stockOut->product_id,
            'quantity' => $stockOut->quantity,
            'date' => $stockOut->date->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_stock_out_update_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 100]);
        $stockOut = StockOut::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        $this->actingAs($admin)->put(route('stock.out.update', $stockOut), [
            'product_id' => $stockOut->product_id,
            'quantity' => 15,
            'date' => $stockOut->date->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'updated',
            'model_type' => 'StockOut',
        ]);
    }

    // ==================== DESTROY ====================

    public function test_admin_can_delete_stock_out()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 100]);
        $stockOut = StockOut::factory()->create([
            'product_id' => $product->id,
            'quantity' => 25,
        ]);

        $response = $this->actingAs($admin)->delete(route('stock.out.destroy', $stockOut));

        $response->assertRedirect(route('stock.out.index'));
        $response->assertSessionHas('success', 'Stock-out record deleted. Product quantity restored.');

        $this->assertDatabaseMissing('stock_out', ['id' => $stockOut->id]);

        // Product quantity should be incremented (restored) by 25 (100 + 25 = 125)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 125,
        ]);
    }

    public function test_staff_cannot_delete_stock_out()
    {
        $staff = User::factory()->create();
        $stockOut = StockOut::factory()->create();

        $response = $this->actingAs($staff)->delete(route('stock.out.destroy', $stockOut));

        $response->assertStatus(403);
        $this->assertDatabaseHas('stock_out', ['id' => $stockOut->id]);
    }

    public function test_guest_cannot_delete_stock_out()
    {
        $stockOut = StockOut::factory()->create();

        $response = $this->delete(route('stock.out.destroy', $stockOut));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('stock_out', ['id' => $stockOut->id]);
    }

    public function test_delete_nonexistent_stock_out_returns_404()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('stock.out.destroy', 99999));

        $response->assertStatus(404);
    }

    public function test_stock_out_deletion_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $stockOut = StockOut::factory()->create();

        $this->actingAs($admin)->delete(route('stock.out.destroy', $stockOut));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'deleted',
            'model_type' => 'StockOut',
        ]);
    }

    // ==================== AUTHORIZATION EDGE CASES ====================

    public function test_admin_can_perform_all_stock_out_actions()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 50]);
        $stockOut = StockOut::factory()->create();

        // Index
        $this->actingAs($admin)->get(route('stock.out.index'))->assertStatus(200);

        // Create form
        $this->actingAs($admin)->get(route('stock.out.create'))->assertStatus(200);

        // Store
        $this->actingAs($admin)->post(route('stock.out.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => '2025-06-01',
        ])->assertRedirect();

        // Edit form
        $this->actingAs($admin)->get(route('stock.out.edit', $stockOut))->assertStatus(200);

        // Update
        $this->actingAs($admin)->put(route('stock.out.update', $stockOut), [
            'product_id' => $stockOut->product_id,
            'quantity' => $stockOut->quantity,
            'date' => $stockOut->date->format('Y-m-d'),
        ])->assertRedirect();

        // Delete
        $this->actingAs($admin)->delete(route('stock.out.destroy', $stockOut))->assertRedirect();
    }

    public function test_staff_cannot_perform_admin_stock_out_actions()
    {
        $staff = User::factory()->create();
        $stockOut = StockOut::factory()->create();

        // Index — allowed (no can:admin middleware)
        $this->actingAs($staff)->get(route('stock.out.index'))->assertStatus(200);

        // Create form
        $this->actingAs($staff)->get(route('stock.out.create'))->assertStatus(403);

        // Store
        $this->actingAs($staff)->post(route('stock.out.store'), [
            'product_id' => $stockOut->product_id,
            'quantity' => 5,
            'date' => '2025-06-01',
        ])->assertStatus(403);

        // Edit form
        $this->actingAs($staff)->get(route('stock.out.edit', $stockOut))->assertStatus(403);

        // Update
        $this->actingAs($staff)->put(route('stock.out.update', $stockOut), [
            'product_id' => $stockOut->product_id,
            'quantity' => $stockOut->quantity,
            'date' => $stockOut->date->format('Y-m-d'),
        ])->assertStatus(403);

        // Delete
        $this->actingAs($staff)->delete(route('stock.out.destroy', $stockOut))->assertStatus(403);
    }

    // ==================== MODEL RELATIONSHIPS ====================

    public function test_stock_out_belongs_to_product()
    {
        $product = Product::factory()->create();
        $stockOut = StockOut::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $stockOut->product);
        $this->assertEquals($product->id, $stockOut->product->id);
    }

    // ==================== BULK DATA ====================

    public function test_stock_out_index_shows_correct_total_count()
    {
        $admin = User::factory()->admin()->create();
        StockOut::factory()->count(7)->create();

        $response = $this->actingAs($admin)->get(route('stock.out.index'));

        $response->assertStatus(200);
        $response->assertSee('(7)');
    }
}
