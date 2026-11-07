<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreShipmentRequest;
use App\Http\Resources\Api\V1\ShipmentResource;
use App\Models\Shipment;
use App\Services\Business\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(protected ShipmentService $service) {}

    public function index(Request $request): JsonResponse
    {
        $shipments = Shipment::query()->with('items')->latest()->paginate((int) $request->integer('per_page', 15));

        return ShipmentResource::collection($shipments)->response();
    }

    public function store(StoreShipmentRequest $request): JsonResponse
    {
        $shipment = $this->service->create($request->validated());

        return (new ShipmentResource($shipment))->response()->setStatusCode(201);
    }

    public function show(Shipment $shipment): JsonResponse
    {
        $shipment->load('items');

        return (new ShipmentResource($shipment))->response();
    }
}
