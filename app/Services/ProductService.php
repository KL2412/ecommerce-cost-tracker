<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(private readonly ProductRepository $productRepository) {}

    /**
     * @return Collection<int, Product>
     */
    public function getAll(): Collection
    {
        return $this->productRepository->all();
    }
}
