<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => fake()->unique()->word(),
            'sku' => 'SKU-' . strtoupper(fake()->unique()->bothify('???-#####')),
            'quantity' => fake()->numberBetween(0, 200),
            'price' => fake()->randomFloat(2, 1, 9999),
            'reorder_level' => fake()->numberBetween(1, 20),
            'category_id' => null,
            'supplier_id' => null,
            'warehouse_location' => fake()->optional(0.7)->sentence(3),
            'image' => null,
            'status' => 'active',
        ];
    }

    public function withCategory()
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => Category::factory(),
        ]);
    }

    public function lowStock()
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 1,
            'reorder_level' => 10,
        ]);
    }

    public function outOfStock()
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 0,
            'reorder_level' => 5,
        ]);
    }
}
