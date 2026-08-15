<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection($this->productService->getAll());
    }
}
