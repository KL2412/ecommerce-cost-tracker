<?php

namespace App\Services;

use App\Enums\InventoryTransactionType;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Repositories\InventoryTransactionRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryTransactionService
{
    public function __construct(
        private readonly InventoryTransactionRepository $transactionRepository,
        private readonly ProductRepository $productRepository,
    ) {}

    /**
     * @return Collection<int, InventoryTransaction>
     */
    public function purchases(): Collection
    {
        return $this->transactionRepository->allByType(InventoryTransactionType::Purchase);
    }

    /**
     * @return Collection<int, InventoryTransaction>
     */
    public function sales(): Collection
    {
        return $this->transactionRepository->allByType(InventoryTransactionType::Sale);
    }

    /**
     * @param  array{product_id: int, transaction_date: string, quantity: int, unit_cost: string}  $attributes
     */
    public function recordPurchase(array $attributes, int $createdBy): InventoryTransaction
    {
        return DB::transaction(function () use ($attributes, $createdBy): InventoryTransaction {
            $this->acquireCreationLock();
            $this->ensureDateFollowsLatest($attributes['transaction_date']);

            $product = $this->productRepository->findAndLock($attributes['product_id']);
            $quantity = $attributes['quantity'];
            $unitCost = $this->toCents($attributes['unit_cost']);
            $purchaseValue = $unitCost * $quantity;
            $newQuantity = $product->quantity_on_hand + $quantity;
            $newInventoryValue = $this->toCents($product->inventory_value) + $purchaseValue;
            $newAverageCost = $this->roundDivision($newInventoryValue, $newQuantity);

            $product->forceFill([
                'quantity_on_hand' => $newQuantity,
                'inventory_value' => $this->fromCents($newInventoryValue),
                'average_cost' => $this->fromCents($newAverageCost),
            ]);
            $this->productRepository->save($product);

            return $this->transactionRepository->create([
                'product_id' => $product->id,
                'created_by' => $createdBy,
                'type' => InventoryTransactionType::Purchase,
                'transaction_date' => $attributes['transaction_date'],
                'quantity' => $quantity,
                'unit_cost' => $this->fromCents($unitCost),
                'total_cost' => $this->fromCents($purchaseValue),
            ]);
        }, 3);
    }

    /**
     * @param  array{product_id: int, transaction_date: string, quantity: int}  $attributes
     */
    public function recordSale(array $attributes, int $createdBy): InventoryTransaction
    {
        return DB::transaction(function () use ($attributes, $createdBy): InventoryTransaction {
            $this->acquireCreationLock();
            $this->ensureDateFollowsLatest($attributes['transaction_date']);

            $product = $this->productRepository->findAndLock($attributes['product_id']);
            $quantity = $attributes['quantity'];
            $this->ensureSufficientStock($product, $quantity);

            $unitCost = $this->toCents($product->average_cost);
            $saleCost = $unitCost * $quantity;
            $newQuantity = $product->quantity_on_hand - $quantity;
            $newInventoryValue = $newQuantity === 0
                ? 0
                : max(0, $this->toCents($product->inventory_value) - $saleCost);

            $product->forceFill([
                'quantity_on_hand' => $newQuantity,
                'inventory_value' => $this->fromCents($newInventoryValue),
                'average_cost' => $newQuantity === 0 ? '0.00' : $product->average_cost,
            ]);
            $this->productRepository->save($product);

            return $this->transactionRepository->create([
                'product_id' => $product->id,
                'created_by' => $createdBy,
                'type' => InventoryTransactionType::Sale,
                'transaction_date' => $attributes['transaction_date'],
                'quantity' => $quantity,
                'unit_cost' => $this->fromCents($unitCost),
                'total_cost' => $this->fromCents($saleCost),
            ]);
        }, 3);
    }

    private function acquireCreationLock(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::select("select pg_advisory_xact_lock(hashtext('inventory_transactions'))");
        }
    }

    private function ensureDateFollowsLatest(string $transactionDate): void
    {
        $latest = $this->transactionRepository->latest();

        if ($latest === null) {
            return;
        }

        $latestDate = $latest->transaction_date->format('Y-m-d');

        if ($transactionDate <= $latestDate) {
            throw ValidationException::withMessages([
                'transaction_date' => ["The transaction date must be later than {$latestDate}."],
            ]);
        }
    }

    private function ensureSufficientStock(Product $product, int $quantity): void
    {
        if ($quantity > $product->quantity_on_hand) {
            throw ValidationException::withMessages([
                'quantity' => ['The sale quantity exceeds the available stock.'],
            ]);
        }
    }

    private function toCents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }

    private function fromCents(int $amount): string
    {
        return intdiv($amount, 100).'.'.str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }

    private function roundDivision(int $value, int $quantity): int
    {
        return intdiv($value + intdiv($quantity, 2), $quantity);
    }
}
