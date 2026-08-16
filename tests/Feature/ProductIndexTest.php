<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_retrieved(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $this->seed(ProductSeeder::class);
        $this->seed(ProductSeeder::class);

        $this->assertDatabaseCount('products', 6);

        $this->getJson('/api/products')
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    [
                        'id' => 1,
                        'sku' => 'WM-001',
                        'name' => 'Wireless Mouse',
                        'description' => 'Compact wireless mouse',
                        'quantity_on_hand' => 0,
                        'inventory_value' => '0.00',
                        'average_cost' => '0.00',
                    ],
                    [
                        'id' => 2,
                        'sku' => 'MK-002',
                        'name' => 'Mechanical Keyboard',
                        'description' => 'Mechanical keyboard with tactile switches',
                        'quantity_on_hand' => 0,
                        'inventory_value' => '0.00',
                        'average_cost' => '0.00',
                    ],
                    [
                        'id' => 3,
                        'sku' => 'UCH-003',
                        'name' => 'USB-C Hub',
                        'description' => 'Multi-port USB-C hub',
                        'quantity_on_hand' => 0,
                        'inventory_value' => '0.00',
                        'average_cost' => '0.00',
                    ],
                    [
                        'id' => 4,
                        'sku' => 'LS-004',
                        'name' => 'Laptop Stand',
                        'description' => 'Adjustable aluminium laptop stand',
                        'quantity_on_hand' => 0,
                        'inventory_value' => '0.00',
                        'average_cost' => '0.00',
                    ],
                    [
                        'id' => 5,
                        'sku' => 'WC-005',
                        'name' => 'Webcam',
                        'description' => 'Full HD webcam with built-in microphone',
                        'quantity_on_hand' => 0,
                        'inventory_value' => '0.00',
                        'average_cost' => '0.00',
                    ],
                    [
                        'id' => 6,
                        'sku' => 'NCH-006',
                        'name' => 'Noise-Cancelling Headphones',
                        'description' => 'Wireless over-ear headphones',
                        'quantity_on_hand' => 0,
                        'inventory_value' => '0.00',
                        'average_cost' => '0.00',
                    ],
                ],
            ]);
    }
}
