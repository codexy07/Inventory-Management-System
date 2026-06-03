<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{
    public function run()
    {
        $suppliers = [
            ['name' => 'TechWorld Distributors', 'phone' => '555-0101', 'email' => 'contact@techworld.com', 'address' => '123 Tech Park, Silicon Valley, CA'],
            ['name' => 'Office Supplies Co.', 'phone' => '555-0102', 'email' => 'sales@officesupplies.com', 'address' => '456 Commerce Ave, New York, NY'],
            ['name' => 'Global Electronics Inc.', 'phone' => '555-0103', 'email' => 'info@globalelectronics.com', 'address' => '789 Innovation Dr, Austin, TX'],
            ['name' => 'Data Solutions Ltd.', 'phone' => '555-0104', 'email' => 'support@datasolutions.com', 'address' => '321 Data Center Rd, Seattle, WA'],
            ['name' => 'Network Pro Systems', 'phone' => '555-0105', 'email' => 'hello@networkpro.com', 'address' => '654 Network Blvd, Chicago, IL'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
