<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::insert([
            ['sku' => 'SKU-8812', 'name' => 'Premium Coffee Beans 250g', 'price' => 85000, 'stock' => 42, 'created_at' => now(), 'updated_at' => now()],
            ['sku' => 'SKU-8813', 'name' => 'Artisan Espresso Blend 1kg', 'price' => 240000, 'stock' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['sku' => 'SKU-4402', 'name' => 'Digital Scale Dosing Tray', 'price' => 125000, 'stock' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['sku' => 'SKU-1299', 'name' => 'Maxto M2 Helmet Intercom', 'price' => 680000, 'stock' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['sku' => 'SKU-3101', 'name' => 'Waterproof Handlebar Phone Holder', 'price' => 95000, 'stock' => 20, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
