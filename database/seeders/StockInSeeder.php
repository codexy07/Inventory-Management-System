<?php

namespace Database\Seeders;

use App\Models\StockIn;
use Illuminate\Database\Seeder;

class StockInSeeder extends Seeder
{
    public function run()
    {
        $records = [
            ['product_id' => 1, 'supplier_id' => 1, 'quantity' => 10, 'date' => now()->subDays(45)->format('Y-m-d')],
            ['product_id' => 2, 'supplier_id' => 2, 'quantity' => 20, 'date' => now()->subDays(40)->format('Y-m-d')],
            ['product_id' => 3, 'supplier_id' => 1, 'quantity' => 50, 'date' => now()->subDays(35)->format('Y-m-d')],
            ['product_id' => 4, 'supplier_id' => 3, 'quantity' => 25, 'date' => now()->subDays(30)->format('Y-m-d')],
            ['product_id' => 5, 'supplier_id' => 3, 'quantity' => 5, 'date' => now()->subDays(25)->format('Y-m-d')],
            ['product_id' => 6, 'supplier_id' => 5, 'quantity' => 10, 'date' => now()->subDays(20)->format('Y-m-d')],
            ['product_id' => 7, 'supplier_id' => 2, 'quantity' => 100, 'date' => now()->subDays(15)->format('Y-m-d')],
            ['product_id' => 8, 'supplier_id' => 1, 'quantity' => 30, 'date' => now()->subDays(10)->format('Y-m-d')],
            ['product_id' => 9, 'supplier_id' => 3, 'quantity' => 3, 'date' => now()->subDays(5)->format('Y-m-d')],
            ['product_id' => 10, 'supplier_id' => 4, 'quantity' => 5, 'date' => now()->format('Y-m-d')],
        ];

        foreach ($records as $record) {
            StockIn::create($record);
        }
    }
}
