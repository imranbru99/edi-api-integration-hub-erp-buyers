<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreInvoiceRequest;
use App\Http\Resources\Api\V1\InvoiceResource;
use App\Models\Invoice;
use App\Services\Business\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $service) {}

    public function index(Request $request): JsonResponse
    {
        $invoices = Invoice::query()->with('items')->latest()->paginate((int) $request->integer('per_page', 15));

        return InvoiceResource::collection($invoices)->response();
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->service->create($request->validated());

        return (new InvoiceResource($invoice))->response()->setStatusCode(201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load('items');

        return (new InvoiceResource($invoice))->response();
    }
}
