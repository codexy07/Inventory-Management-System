<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic components, devices, and equipment'],
            ['name' => 'Furniture', 'description' => 'Office and warehouse furniture'],
            ['name' => 'Raw Materials', 'description' => 'Raw materials used in manufacturing'],
            ['name' => 'Packaging', 'description' => 'Packaging supplies and materials'],
            ['name' => 'Office Supplies', 'description' => 'General office and administrative supplies'],
            ['name' => 'Tools & Hardware', 'description' => 'Tools, hardware, and maintenance equipment'],
            ['name' => 'Safety Equipment', 'description' => 'PPE and safety-related items'],
            ['name' => 'Cleaning Supplies', 'description' => 'Cleaning products and janitorial supplies'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
