<?php

namespace Tests\Feature;

use App\Enums\InventoryTransactionType;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_is_recent_consistent_and_idempotent(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-08-16 12:00:00'));

        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('products', 6);
        $this->assertDatabaseCount('inventory_transactions', 8);

        $user = User::query()->firstOrFail();
        $this->assertSame('demo@example.com', $user->email);
        $this->assertTrue(Hash::check('password', $user->password));

        $transactions = InventoryTransaction::query()
            ->orderBy('transaction_date')
            ->get();

        $this->assertSame('2026-08-08', $transactions->first()->transaction_date->toDateString());
        $this->assertSame('2026-08-15', $transactions->last()->transaction_date->toDateString());
        $this->assertSame(4, $transactions->where('type', InventoryTransactionType::Purchase)->count());
        $this->assertSame(4, $transactions->where('type', InventoryTransactionType::Sale)->count());

        $mouse = Product::query()->where('sku', 'WM-001')->firstOrFail();
        $this->assertSame(100, $mouse->quantity_on_hand);
        $this->assertSame('4453.68', $mouse->inventory_value);
        $this->assertSame('44.54', $mouse->average_cost);
    }
}
