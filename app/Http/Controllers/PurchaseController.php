<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Resources\InventoryTransactionResource;
use App\Services\InventoryTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    public function __construct(private readonly InventoryTransactionService $transactionService) {}

    /**
     * List purchase transactions.
     */
    public function index(): AnonymousResourceCollection
    {
        return InventoryTransactionResource::collection($this->transactionService->purchases());
    }

    /**
     * Record a purchase transaction.
     */
    public function store(StorePurchaseRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->recordPurchase(
            $request->validated(),
            (int) $request->user()->getAuthIdentifier(),
        );

        return InventoryTransactionResource::make($transaction)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
