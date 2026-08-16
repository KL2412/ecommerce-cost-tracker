<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTransactionApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->product = Product::query()->create([
            'sku' => 'WAC-001',
            'name' => 'WAC Product',
        ]);
        $this->actingAs($this->user, 'api');
    }

    public function test_purchase_and_sale_workflow_uses_weighted_average_cost(): void
    {
        $this->postJson('/api/purchases', [
            'product_id' => $this->product->id,
            'transaction_date' => '2022-01-01',
            'quantity' => 150,
            'unit_cost' => '2.00',
        ])->assertCreated()
            ->assertJsonPath('data.total_cost', '300.00')
            ->assertJsonPath('data.created_by', $this->user->id);

        $this->postJson('/api/purchases', [
            'product_id' => $this->product->id,
            'transaction_date' => '2022-01-05',
            'quantity' => 10,
            'unit_cost' => '1.50',
        ])->assertCreated()
            ->assertJsonPath('data.total_cost', '15.00');

        $this->product->refresh();
        $this->assertSame(160, $this->product->quantity_on_hand);
        $this->assertSame('315.00', $this->product->inventory_value);
        $this->assertSame('1.97', $this->product->average_cost);

        $this->postJson('/api/sales', [
            'product_id' => $this->product->id,
            'transaction_date' => '2022-01-07',
            'quantity' => 5,
        ])->assertCreated()
            ->assertJsonPath('data.type', 'sale')
            ->assertJsonPath('data.unit_cost', '1.97')
            ->assertJsonPath('data.total_cost', '9.85');

        $this->product->refresh();
        $this->assertSame(155, $this->product->quantity_on_hand);
        $this->assertSame('305.15', $this->product->inventory_value);
        $this->assertSame('1.97', $this->product->average_cost);
    }

    public function test_lists_are_filtered_and_ordered_by_transaction_date(): void
    {
        $this->purchase('2022-01-01', 10, '2.00');
        $this->sale('2022-01-02', 1);
        $this->purchase('2022-01-03', 5, '3.00');

        $this->getJson('/api/purchases')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.transaction_date', '2022-01-01')
            ->assertJsonPath('data.1.transaction_date', '2022-01-03');

        $this->getJson('/api/sales')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.transaction_date', '2022-01-02')
            ->assertJsonPath('data.0.total_cost', '2.00');
    }

    public function test_duplicate_and_out_of_order_dates_are_rejected(): void
    {
        $this->purchase('2022-01-05', 10, '2.00');

        $this->postJson('/api/sales', [
            'product_id' => $this->product->id,
            'transaction_date' => '2022-01-05',
            'quantity' => 1,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('transaction_date');

        $this->postJson('/api/sales', [
            'product_id' => $this->product->id,
            'transaction_date' => '2022-01-04',
            'quantity' => 1,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('transaction_date');

        $this->assertDatabaseCount('inventory_transactions', 1);
    }

    public function test_insufficient_stock_rolls_back_the_sale(): void
    {
        $this->purchase('2022-01-01', 2, '4.00');

        $this->postJson('/api/sales', [
            'product_id' => $this->product->id,
            'transaction_date' => '2022-01-02',
            'quantity' => 3,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');

        $this->product->refresh();
        $this->assertSame(2, $this->product->quantity_on_hand);
        $this->assertSame('8.00', $this->product->inventory_value);
        $this->assertDatabaseCount('inventory_transactions', 1);
    }

    public function test_selling_all_stock_resets_inventory_balances(): void
    {
        $this->purchase('2022-01-01', 3, '1.25');
        $this->sale('2022-01-02', 3);

        $this->product->refresh();
        $this->assertSame(0, $this->product->quantity_on_hand);
        $this->assertSame('0.00', $this->product->inventory_value);
        $this->assertSame('0.00', $this->product->average_cost);
    }

    public function test_transaction_payloads_are_validated(): void
    {
        $this->postJson('/api/purchases', [
            'product_id' => 999,
            'transaction_date' => '01-01-2022',
            'quantity' => 0,
            'unit_cost' => '1.999',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'product_id',
                'transaction_date',
                'quantity',
                'unit_cost',
            ]);
    }

    public function test_transactions_cannot_be_updated_or_deleted(): void
    {
        $transactionId = $this->purchase('2022-01-01', 1, '2.00');

        $this->putJson("/api/purchases/{$transactionId}", [])->assertNotFound();
        $this->deleteJson("/api/purchases/{$transactionId}")->assertNotFound();
    }

    private function purchase(string $date, int $quantity, string $unitCost): int
    {
        return (int) $this->postJson('/api/purchases', [
            'product_id' => $this->product->id,
            'transaction_date' => $date,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
        ])->assertCreated()->json('data.id');
    }

    private function sale(string $date, int $quantity): int
    {
        return (int) $this->postJson('/api/sales', [
            'product_id' => $this->product->id,
            'transaction_date' => $date,
            'quantity' => $quantity,
        ])->assertCreated()->json('data.id');
    }
}
