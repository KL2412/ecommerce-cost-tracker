<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'transaction_date' => [
                'required',
                'date_format:Y-m-d',
                'unique:inventory_transactions,transaction_date',
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'decimal:0,2', 'gt:0'],
        ];
    }
}
