<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTradingPartnerRequest;
use App\Http\Resources\Api\V1\TradingPartnerResource;
use App\Models\TradingPartner;
use App\Services\Partners\TradingPartnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradingPartnerController extends Controller
{
    public function __construct(protected TradingPartnerService $service) {}

    public function index(Request $request): JsonResponse
    {
        $partners = TradingPartner::query()
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate((int) $request->integer('per_page', 15));

        return TradingPartnerResource::collection($partners)->response();
    }

    public function store(StoreTradingPartnerRequest $request): JsonResponse
    {
        $partner = $this->service->create($request->validated());

        return (new TradingPartnerResource($partner))->response()->setStatusCode(201);
    }

    public function show(TradingPartner $tradingPartner): JsonResponse
    {
        return (new TradingPartnerResource($tradingPartner))->response();
    }

    public function activate(TradingPartner $tradingPartner): JsonResponse
    {
        $partner = $this->service->activate($tradingPartner);

        return (new TradingPartnerResource($partner))->response();
    }
}
