<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Laptop HP EliteBook',     'sku' => 'PRD-001', 'category_id' => 1, 'supplier_id' => 1, 'quantity' => 25,  'price' => 1200.00, 'reorder_level' => 5,  'warehouse_location' => 'Aisle A, Shelf 1', 'status' => 'active'],
            ['name' => 'Dell Monitor 24"',         'sku' => 'PRD-002', 'category_id' => 1, 'supplier_id' => 3, 'quantity' => 40,  'price' => 250.00,  'reorder_level' => 10, 'warehouse_location' => 'Aisle A, Shelf 2', 'status' => 'active'],
            ['name' => 'Logitech Wireless Mouse',  'sku' => 'PRD-003', 'category_id' => 1, 'supplier_id' => 1, 'quantity' => 100, 'price' => 29.99,  'reorder_level' => 20, 'warehouse_location' => 'Aisle B, Shelf 1', 'status' => 'active'],
            ['name' => 'Samsung SSD 1TB',          'sku' => 'PRD-004', 'category_id' => 1, 'supplier_id' => 3, 'quantity' => 50,  'price' => 149.99, 'reorder_level' => 10, 'warehouse_location' => 'Aisle B, Shelf 2', 'status' => 'active'],
            ['name' => 'Corsair RAM 16GB',         'sku' => 'PRD-005', 'category_id' => 1, 'supplier_id' => 3, 'quantity' => 3,   'price' => 89.99,  'reorder_level' => 5,  'warehouse_location' => 'Aisle C, Shelf 1', 'status' => 'active'],
            ['name' => 'TP-Link Router',           'sku' => 'PRD-006', 'category_id' => 1, 'supplier_id' => 5, 'quantity' => 15,  'price' => 79.99,  'reorder_level' => 5,  'warehouse_location' => 'Aisle D, Shelf 1', 'status' => 'active'],
            ['name' => 'Microsoft Office License', 'sku' => 'PRD-007', 'category_id' => 5, 'supplier_id' => 2, 'quantity' => 200, 'price' => 149.99, 'reorder_level' => 50, 'warehouse_location' => NULL,                'status' => 'active'],
            ['name' => 'Anker USB Hub',            'sku' => 'PRD-008', 'category_id' => 1, 'supplier_id' => 4, 'quantity' => 60,  'price' => 34.99,  'reorder_level' => 15, 'warehouse_location' => 'Aisle B, Shelf 3', 'status' => 'active'],
            ['name' => 'Logitech Webcam C920',     'sku' => 'PRD-009', 'category_id' => 1, 'supplier_id' => 1, 'quantity' => 2,   'price' => 99.99,  'reorder_level' => 5,  'warehouse_location' => 'Aisle C, Shelf 2', 'status' => 'active'],
            ['name' => 'CyberPower UPS 1500VA',    'sku' => 'PRD-010', 'category_id' => 1, 'supplier_id' => 4, 'quantity' => 8,   'price' => 299.99, 'reorder_level' => 3,  'warehouse_location' => 'Aisle E, Shelf 1', 'status' => 'active'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
