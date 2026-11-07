<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\InventoryRecordResource;
use App\Models\InventoryRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = InventoryRecord::query()
            ->with('product')
            ->when($request->filled('sku'), function ($q) use ($request) {
                $q->whereHas('product', fn ($p) => $p->where('sku', $request->string('sku')));
            })
            ->paginate((int) $request->integer('per_page', 25));

        return InventoryRecordResource::collection($records)->response();
    }
}
