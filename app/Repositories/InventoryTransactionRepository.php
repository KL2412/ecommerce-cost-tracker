<?php

namespace App\Repositories;

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
            ->orderBy('transaction_date')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): InventoryTransaction
    {
        return InventoryTransaction::query()->create($attributes);
    }
}
