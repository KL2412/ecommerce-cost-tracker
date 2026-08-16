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
            [
                'sku' => 'LS-004',
                'name' => 'Laptop Stand',
                'description' => 'Adjustable aluminium laptop stand',
            ],
            [
                'sku' => 'WC-005',
                'name' => 'Webcam',
                'description' => 'Full HD webcam with built-in microphone',
            ],
            [
                'sku' => 'NCH-006',
                'name' => 'Noise-Cancelling Headphones',
                'description' => 'Wireless over-ear headphones',
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
