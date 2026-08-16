<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * @return Collection<int, Product>
     */
    public function all(): Collection
    {
        return Product::query()
            ->orderBy('id')
            ->get();
    }

    public function findAndLock(int $id): Product
    {
        return Product::query()
            ->lockForUpdate()
            ->findOrFail($id);
    }

    public function save(Product $product): Product
    {
        $product->save();

        return $product;
    }
}
