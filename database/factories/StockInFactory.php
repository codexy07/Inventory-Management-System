<?php

namespace Database\Factories;

use App\Models\StockIn;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockIn>
 */
class StockInFactory extends Factory
{
    protected $model = StockIn::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'supplier_id' => Supplier::factory(),
            'quantity' => fake()->numberBetween(1, 500),
            'date' => fake()->date('Y-m-d'),
        ];
    }

    public function withSupplier(?Supplier $supplier = null)
    {
        return $this->state(fn (array $attributes) => [
            'supplier_id' => $supplier ?? Supplier::factory(),
        ]);
    }

    public function withoutSupplier()
    {
        return $this->state(fn (array $attributes) => [
            'supplier_id' => null,
        ]);
    }
}
