<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'transaction_date' => $this->transaction_date->format('Y-m-d'),
            'product' => [
                'id' => $this->product->id,
                'sku' => $this->product->sku,
                'name' => $this->product->name,
            ],
            'quantity' => $this->quantity,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'created_by' => $this->created_by,
        ];
    }
}
