<?php

namespace Database\Seeders;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryTransactionService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class InventoryTransactionSeeder extends Seeder
{
    public function run(): void
    {
        if (InventoryTransaction::query()->exists()) {
            return;
        }

        $user = User::query()->where('email', 'demo@example.com')->firstOrFail();
        $products = Product::query()
            ->whereIn('sku', ['WM-001', 'MK-002', 'UCH-003'])
            ->pluck('id', 'sku');
        $today = CarbonImmutable::today();
        $service = app(InventoryTransactionService::class);

        $service->recordPurchase([
            'product_id' => $products['WM-001'],
            'transaction_date' => $today->subDays(8)->toDateString(),
            'quantity' => 100,
            'unit_cost' => '45.00',
        ], $user->id);

        $service->recordPurchase([
            'product_id' => $products['MK-002'],
            'transaction_date' => $today->subDays(7)->toDateString(),
            'quantity' => 50,
            'unit_cost' => '120.00',
        ], $user->id);

        $service->recordSale([
            'product_id' => $products['WM-001'],
            'transaction_date' => $today->subDays(6)->toDateString(),
            'quantity' => 12,
        ], $user->id);

        $service->recordPurchase([
            'product_id' => $products['WM-001'],
            'transaction_date' => $today->subDays(5)->toDateString(),
            'quantity' => 20,
            'unit_cost' => '42.50',
        ], $user->id);

        $service->recordPurchase([
            'product_id' => $products['UCH-003'],
            'transaction_date' => $today->subDays(4)->toDateString(),
            'quantity' => 75,
            'unit_cost' => '65.00',
        ], $user->id);

        $service->recordSale([
            'product_id' => $products['MK-002'],
            'transaction_date' => $today->subDays(3)->toDateString(),
            'quantity' => 5,
        ], $user->id);

        $service->recordSale([
            'product_id' => $products['WM-001'],
            'transaction_date' => $today->subDays(2)->toDateString(),
            'quantity' => 8,
        ], $user->id);

        $service->recordSale([
            'product_id' => $products['UCH-003'],
            'transaction_date' => $today->subDay()->toDateString(),
            'quantity' => 10,
        ], $user->id);
    }
}
