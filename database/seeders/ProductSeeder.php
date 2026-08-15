<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'WM-001',
                'name' => 'Wireless Mouse',
                'description' => 'Compact wireless mouse',
            ],
            [
                'sku' => 'MK-002',
                'name' => 'Mechanical Keyboard',
                'description' => 'Mechanical keyboard with tactile switches',
            ],
            [
                'sku' => 'UCH-003',
                'name' => 'USB-C Hub',
                'description' => 'Multi-port USB-C hub',
            ],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                $product,
            );
        }
    }
}
