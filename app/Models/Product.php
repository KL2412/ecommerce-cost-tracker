<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'sku',
    'name',
    'description',
    'quantity_on_hand',
    'inventory_value',
    'average_cost',
])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'integer',
            'inventory_value' => 'decimal:2',
            'average_cost' => 'decimal:2',
        ];
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
