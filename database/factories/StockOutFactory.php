<?php

namespace Database\Factories;

use App\Models\StockOut;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockOut>
 */
class StockOutFactory extends Factory
{
    protected $model = StockOut::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 100),
            'date' => fake()->date('Y-m-d'),
        ];
    }
}
