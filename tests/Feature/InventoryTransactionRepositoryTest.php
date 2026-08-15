<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Repositories\InventoryTransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_can_be_created_and_retrieved(): void
    {
        $product = Product::query()->create([
            'sku' => 'TEST-001',
            'name' => 'Test Product',
        ]);
        $user = User::factory()->create();
        $repository = app(InventoryTransactionRepository::class);

        $transaction = $repository->create([
            'product_id' => $product->id,
            'created_by' => $user->id,
            'type' => 'purchase',
            'transaction_date' => '2026-01-01',
            'quantity' => 10,
            'unit_cost' => '2.00',
            'total_cost' => '20.00',
        ]);

        $this->assertTrue($transaction->product->is($product));
        $this->assertTrue($transaction->creator->is($user));
        $this->assertTrue($repository->all()->first()->is($transaction));
    }
}
