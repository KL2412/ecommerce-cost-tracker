<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\InventoryTransactionResource;
use App\Services\InventoryTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    public function __construct(private readonly InventoryTransactionService $transactionService) {}

    public function index(): AnonymousResourceCollection
    {
        return InventoryTransactionResource::collection($this->transactionService->sales());
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->recordSale(
            $request->validated(),
            (int) $request->user()->getAuthIdentifier(),
        );

        return InventoryTransactionResource::make($transaction)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
