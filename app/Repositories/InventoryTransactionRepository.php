<?php

namespace App\Repositories;

use App\Enums\InventoryTransactionType;
use App\Models\InventoryTransaction;
use Illuminate\Database\Eloquent\Collection;

class InventoryTransactionRepository
{
    /**
     * @return Collection<int, InventoryTransaction>
     */
    public function all(): Collection
    {
        return InventoryTransaction::query()
            ->with('product')
            ->orderBy('transaction_date')
            ->get();
    }

    /**
     * @return Collection<int, InventoryTransaction>
     */
    public function allByType(InventoryTransactionType $type): Collection
    {
        return InventoryTransaction::query()
            ->with('product')
            ->where('type', $type->value)
            ->orderBy('transaction_date')
            ->get();
    }

    public function latest(): ?InventoryTransaction
    {
        return InventoryTransaction::query()
            ->orderByDesc('transaction_date')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): InventoryTransaction
    {
        return InventoryTransaction::query()
            ->create($attributes)
            ->load('product');
    }
}
