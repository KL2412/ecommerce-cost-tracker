<?php

namespace App\Models;

use App\Enums\InventoryTransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_id',
    'created_by',
    'type',
    'transaction_date',
    'quantity',
    'unit_cost',
    'total_cost',
])]
class InventoryTransaction extends Model
{
    protected function casts(): array
    {
        return [
            'type' => InventoryTransactionType::class,
            'transaction_date' => 'date',
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
