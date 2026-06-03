<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockInTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    // ==================== INDEX (LISTING) ====================

    public function test_stock_in_index_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        StockIn::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('stock.in.index'));

        $response->assertStatus(200);
        $response->assertSee('Stock In');
        $response->assertSee('Stock In Transactions');
        $response->assertSee('Add Stock');
    }

    public function test_stock_in_index_page_renders_for_staff()
    {
        $staff = User::factory()->create();
        StockIn::factory()->count(3)->create();

        $response = $this->actingAs($staff)->get(route('stock.in.index'));

        $response->assertStatus(200);
        $response->assertSee('Stock In');
        $response->assertDontSee('Add Stock');
    }

    public function test_guest_cannot_access_stock_in_index()
    {
        $response = $this->get(route('stock.in.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_stock_in_index_displays_transactions()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Widget A']);
        $stockIn = StockIn::factory()->create([
            'product_id' => $product->id,
            'quantity' => 50,
        ]);

        $response = $this->actingAs($admin)->get(route('stock.in.index'));

        $response->assertSee('Widget A');
        $response->assertSee('+50');
    }

    public function test_stock_in_index_shows_empty_state()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('stock.in.index'));

        $response->assertSee('No stock-in transactions found');
    }

    public function test_stock_in_index_search_by_product_name()
    {
        $admin = User::factory()->admin()->create();
        $productA = Product::factory()->create(['name' => 'Target Product']);
        $productB = Product::factory()->create(['name' => 'Other Product']);
        StockIn::factory()->create(['product_id' => $productA->id]);
        StockIn::factory()->create(['product_id' => $productB->id]);

        $response = $this->actingAs($admin)->get(route('stock.in.index', ['search' => 'Target']));

        $response->assertStatus(200);
        $response->assertSee('Target Product');
        $response->assertDontSee('Other Product');
    }

    public function test_stock_in_index_search_returns_empty_when_no_match()
    {
        $admin = User::factory()->admin()->create();
        StockIn::factory()->create();

        $response = $this->actingAs($admin)->get(route('stock.in.index', ['search' => 'Nonexistent']));

        $response->assertStatus(200);
        $response->assertSee('No stock-in transactions found');
    }

    public function test_stock_in_index_paginates_results()
    {
        $admin = User::factory()->admin()->create();
        StockIn::factory()->count(20)->create();

        $response = $this->actingAs($admin)->get(route('stock.in.index'));

        $response->assertStatus(200);
        $response->assertSee('page=2');
    }

    // ==================== CREATE (FORM) ====================

    public function test_create_stock_in_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(2)->create();
        Supplier::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('stock.in.create'));

        $response->assertStatus(200);
        $response->assertSee('Add Stock to Inventory');
        $response->assertSee('Product');
        $response->assertSee('Supplier');
        $response->assertSee('Quantity');
        $response->assertSee('Date');
    }

    public function test_create_stock_in_page_denied_for_staff()
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->get(route('stock.in.create'));

        $response->assertStatus(403);
    }

    public function test_create_stock_in_page_redirects_guest()
    {
        $response = $this->get(route('stock.in.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_create_stock_in_page_lists_products_and_suppliers()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Selectable Product']);
        $supplier = Supplier::factory()->create(['name' => 'Selectable Supplier']);

        $response = $this->actingAs($admin)->get(route('stock.in.create'));

        $response->assertSee('Selectable Product');
        $response->assertSee('Selectable Supplier');
    }

    // ==================== STORE (CREATE ACTION) ====================

    public function test_admin_can_create_stock_in()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 10]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'quantity' => 25,
            'date' => '2025-06-01',
        ]);

        $response->assertRedirect(route('stock.in.index'));
        $response->assertSessionHas('success', 'Stock added successfully.');

        $this->assertDatabaseHas('stock_in', [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'quantity' => 25,
            'date' => '2025-06-01',
        ]);

        // Product quantity should be incremented
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 35,
        ]);
    }

    public function test_admin_can_create_stock_in_without_supplier()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 10]);

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'supplier_id' => '',
            'quantity' => 5,
            'date' => '2025-06-01',
        ]);

        $response->assertRedirect(route('stock.in.index'));

        $this->assertDatabaseHas('stock_in', [
            'product_id' => $product->id,
            'supplier_id' => null,
            'quantity' => 5,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 15,
        ]);
    }

    public function test_staff_cannot_create_stock_in()
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['quantity' => 10]);

        $response = $this->actingAs($staff)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => '2025-06-01',
        ]);

        $response->assertStatus(403);

        // Product quantity should NOT be incremented
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 10,
        ]);
    }

    public function test_stock_in_product_id_is_required()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => '',
            'quantity' => 5,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_stock_in_product_id_must_exist()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => 99999,
            'quantity' => 5,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_stock_in_quantity_is_required()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'quantity' => '',
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stock_in_quantity_must_be_at_least_1()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'quantity' => 0,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stock_in_quantity_must_be_integer()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'quantity' => 5.5,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stock_in_date_is_required()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => '',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_stock_in_date_must_be_valid_format()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => '06/01/2025',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_stock_in_supplier_must_exist_when_provided()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'supplier_id' => 99999,
            'quantity' => 5,
            'date' => '2025-06-01',
        ]);

        $response->assertSessionHasErrors('supplier_id');
    }

    public function test_stock_in_creation_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Activity Widget']);
        $supplier = Supplier::factory()->create(['name' => 'Logistics Co']);

        $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'quantity' => 100,
            'date' => '2025-06-01',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'created',
            'model_type' => 'StockIn',
        ]);
    }

    // ==================== EDIT (FORM) ====================

    public function test_edit_stock_in_page_renders_for_admin()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['name' => 'Editable Product']);
        $stockIn = StockIn::factory()->create([
            'product_id' => $product->id,
            'quantity' => 30,
        ]);

        $response = $this->actingAs($admin)->get(route('stock.in.edit', $stockIn));

        $response->assertStatus(200);
        $response->assertSee('Edit Stock-In Record');
        $response->assertSee('Editable Product');
    }

    public function test_edit_stock_in_page_denied_for_staff()
    {
        $staff = User::factory()->create();
        $stockIn = StockIn::factory()->create();

        $response = $this->actingAs($staff)->get(route('stock.in.edit', $stockIn));

        $response->assertStatus(403);
    }

    public function test_edit_stock_in_page_redirects_guest()
    {
        $stockIn = StockIn::factory()->create();

        $response = $this->get(route('stock.in.edit', $stockIn));

        $response->assertRedirect(route('login'));
    }

    // ==================== UPDATE ====================

    public function test_admin_can_update_stock_in_increase_quantity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 50]);
        $stockIn = StockIn::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10,
        ]);

        // Increase quantity from 10 to 25 (diff = +15)
        $response = $this->actingAs($admin)->put(route('stock.in.update', $stockIn), [
            'product_id' => $product->id,
            'quantity' => 25,
            'date' => $stockIn->date->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('stock.in.index'));
        $response->assertSessionHas('success', 'Stock-in record updated successfully.');

        $this->assertDatabaseHas('stock_in', [
            'id' => $stockIn->id,
            'quantity' => 25,
        ]);

        // Product quantity should increase by 15 (50 + 15 = 65)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 65,
        ]);
    }

    public function test_admin_can_update_stock_in_decrease_quantity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 50]);
        $stockIn = StockIn::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        // Decrease quantity from 20 to 8 (diff = -12)
        $response = $this->actingAs($admin)->put(route('stock.in.update', $stockIn), [
            'product_id' => $product->id,
            'quantity' => 8,
            'date' => $stockIn->date->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('stock.in.index'));

        $this->assertDatabaseHas('stock_in', [
            'id' => $stockIn->id,
            'quantity' => 8,
        ]);

        // Product quantity should decrease by 12 (50 - 12 = 38)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 38,
        ]);
    }

    public function test_admin_can_update_stock_in_same_quantity()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 50]);
        $stockIn = StockIn::factory()->create([
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        // Same quantity (diff = 0) — product quantity unchanged
        $response = $this->actingAs($admin)->put(route('stock.in.update', $stockIn), [
            'product_id' => $product->id,
            'quantity' => 20,
            'date' => $stockIn->date->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('stock.in.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 50,
        ]);
    }

    public function test_staff_cannot_update_stock_in()
    {
        $staff = User::factory()->create();
        $stockIn = StockIn::factory()->create();

        $response = $this->actingAs($staff)->put(route('stock.in.update', $stockIn), [
            'product_id' => $stockIn->product_id,
            'quantity' => $stockIn->quantity,
            'date' => $stockIn->date->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_stock_in_update_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $stockIn = StockIn::factory()->create();

        $this->actingAs($admin)->put(route('stock.in.update', $stockIn), [
            'product_id' => $stockIn->product_id,
            'quantity' => $stockIn->quantity + 10,
            'date' => $stockIn->date->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'updated',
            'model_type' => 'StockIn',
        ]);
    }

    // ==================== DESTROY ====================

    public function test_admin_can_delete_stock_in()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 100]);
        $stockIn = StockIn::factory()->create([
            'product_id' => $product->id,
            'quantity' => 30,
        ]);

        $response = $this->actingAs($admin)->delete(route('stock.in.destroy', $stockIn));

        $response->assertRedirect(route('stock.in.index'));
        $response->assertSessionHas('success', 'Stock-in record deleted. Product quantity adjusted.');

        $this->assertDatabaseMissing('stock_in', ['id' => $stockIn->id]);

        // Product quantity should be decremented by 30 (100 - 30 = 70)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 70,
        ]);
    }

    public function test_staff_cannot_delete_stock_in()
    {
        $staff = User::factory()->create();
        $stockIn = StockIn::factory()->create();

        $response = $this->actingAs($staff)->delete(route('stock.in.destroy', $stockIn));

        $response->assertStatus(403);
        $this->assertDatabaseHas('stock_in', ['id' => $stockIn->id]);
    }

    public function test_guest_cannot_delete_stock_in()
    {
        $stockIn = StockIn::factory()->create();

        $response = $this->delete(route('stock.in.destroy', $stockIn));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('stock_in', ['id' => $stockIn->id]);
    }

    public function test_delete_nonexistent_stock_in_returns_404()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('stock.in.destroy', 99999));

        $response->assertStatus(404);
    }

    public function test_stock_in_deletion_logs_activity()
    {
        $admin = User::factory()->admin()->create();
        $stockIn = StockIn::factory()->create();

        $this->actingAs($admin)->delete(route('stock.in.destroy', $stockIn));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'deleted',
            'model_type' => 'StockIn',
        ]);
    }

    // ==================== AUTHORIZATION EDGE CASES ====================

    public function test_admin_can_perform_all_stock_in_actions()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();
        $stockIn = StockIn::factory()->create();

        // Index
        $this->actingAs($admin)->get(route('stock.in.index'))->assertStatus(200);

        // Create form
        $this->actingAs($admin)->get(route('stock.in.create'))->assertStatus(200);

        // Store
        $this->actingAs($admin)->post(route('stock.in.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'date' => '2025-06-01',
        ])->assertRedirect();

        // Edit form
        $this->actingAs($admin)->get(route('stock.in.edit', $stockIn))->assertStatus(200);

        // Update
        $this->actingAs($admin)->put(route('stock.in.update', $stockIn), [
            'product_id' => $stockIn->product_id,
            'quantity' => $stockIn->quantity,
            'date' => $stockIn->date->format('Y-m-d'),
        ])->assertRedirect();

        // Delete
        $this->actingAs($admin)->delete(route('stock.in.destroy', $stockIn))->assertRedirect();
    }

    public function test_staff_cannot_perform_admin_stock_in_actions()
    {
        $staff = User::factory()->create();
        $stockIn = StockIn::factory()->create();

        // Index — allowed (no can:admin middleware)
        $this->actingAs($staff)->get(route('stock.in.index'))->assertStatus(200);

        // Create form
        $this->actingAs($staff)->get(route('stock.in.create'))->assertStatus(403);

        // Store
        $this->actingAs($staff)->post(route('stock.in.store'), [
            'product_id' => $stockIn->product_id,
            'quantity' => 5,
            'date' => '2025-06-01',
        ])->assertStatus(403);

        // Edit form
        $this->actingAs($staff)->get(route('stock.in.edit', $stockIn))->assertStatus(403);

        // Update
        $this->actingAs($staff)->put(route('stock.in.update', $stockIn), [
            'product_id' => $stockIn->product_id,
            'quantity' => $stockIn->quantity,
            'date' => $stockIn->date->format('Y-m-d'),
        ])->assertStatus(403);

        // Delete
        $this->actingAs($staff)->delete(route('stock.in.destroy', $stockIn))->assertStatus(403);
    }

    // ==================== MODEL RELATIONSHIPS ====================

    public function test_stock_in_belongs_to_product()
    {
        $product = Product::factory()->create();
        $stockIn = StockIn::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $stockIn->product);
        $this->assertEquals($product->id, $stockIn->product->id);
    }

    public function test_stock_in_belongs_to_supplier()
    {
        $supplier = Supplier::factory()->create();
        $stockIn = StockIn::factory()->create(['supplier_id' => $supplier->id]);

        $this->assertInstanceOf(Supplier::class, $stockIn->supplier);
        $this->assertEquals($supplier->id, $stockIn->supplier->id);
    }

    public function test_stock_in_can_have_null_supplier()
    {
        $stockIn = StockIn::factory()->withoutSupplier()->create();

        $this->assertNull($stockIn->supplier);
    }

    // ==================== BULK DATA ====================

    public function test_stock_in_index_shows_correct_total_count()
    {
        $admin = User::factory()->admin()->create();
        StockIn::factory()->count(7)->create();

        $response = $this->actingAs($admin)->get(route('stock.in.index'));

        $response->assertStatus(200);
        $response->assertSee('(7)');
    }
}
