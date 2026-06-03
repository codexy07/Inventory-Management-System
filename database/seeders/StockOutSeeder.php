<?php

namespace Database\Seeders;

use App\Models\StockOut;
use Illuminate\Database\Seeder;

class StockOutSeeder extends Seeder
{
    public function run()
    {
        $records = [
            ['product_id' => 1, 'quantity' => 3, 'date' => now()->subDays(40)->format('Y-m-d')],
            ['product_id' => 2, 'quantity' => 5, 'date' => now()->subDays(35)->format('Y-m-d')],
            ['product_id' => 3, 'quantity' => 20, 'date' => now()->subDays(30)->format('Y-m-d')],
            ['product_id' => 4, 'quantity' => 10, 'date' => now()->subDays(25)->format('Y-m-d')],
            ['product_id' => 1, 'quantity' => 2, 'date' => now()->subDays(20)->format('Y-m-d')],
            ['product_id' => 6, 'quantity' => 5, 'date' => now()->subDays(18)->format('Y-m-d')],
            ['product_id' => 3, 'quantity' => 30, 'date' => now()->subDays(12)->format('Y-m-d')],
            ['product_id' => 7, 'quantity' => 50, 'date' => now()->subDays(8)->format('Y-m-d')],
            ['product_id' => 8, 'quantity' => 15, 'date' => now()->subDays(3)->format('Y-m-d')],
            ['product_id' => 4, 'quantity' => 8, 'date' => now()->format('Y-m-d')],
        ];

        foreach ($records as $record) {
            StockOut::create($record);
        }
    }
}
