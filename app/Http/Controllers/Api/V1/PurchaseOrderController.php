<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePurchaseOrderRequest;
use App\Http\Resources\Api\V1\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Services\Business\PurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(protected PurchaseOrderService $service) {}

    public function index(Request $request): JsonResponse
    {
        $orders = PurchaseOrder::query()->with('items')->latest()->paginate((int) $request->integer('per_page', 15));

        return PurchaseOrderResource::collection($orders)->response();
    }

    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $order = $this->service->createFromCanonical($request->validated());

        return (new PurchaseOrderResource($order))->response()->setStatusCode(201);
    }

    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->load('items', 'tradingPartner');

        return (new PurchaseOrderResource($purchaseOrder))->response();
    }
}
