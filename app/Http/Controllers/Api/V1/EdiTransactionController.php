<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EdiTransactionResource;
use App\Models\EdiTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EdiTransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $transactions = EdiTransaction::query()
            ->with('tradingPartner')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('document_type'), fn ($q) => $q->where('document_type', $request->string('document_type')))
            ->latest()
            ->paginate((int) $request->integer('per_page', 20));

        return EdiTransactionResource::collection($transactions)->response();
    }

    public function show(EdiTransaction $ediTransaction): JsonResponse
    {
        $ediTransaction->load(['tradingPartner', 'events']);

        return (new EdiTransactionResource($ediTransaction))->response();
    }
}
