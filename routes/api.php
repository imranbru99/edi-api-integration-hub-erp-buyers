<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EdiTransactionController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\MappingController;
use App\Http\Controllers\Api\V1\PurchaseOrderController;
use App\Http\Controllers\Api\V1\ShipmentController;
use App\Http\Controllers\Api\V1\TradingPartnerController;
use App\Http\Controllers\Api\V1\WebhookController;
use App\Http\Middleware\ApiRateLimit;
use App\Http\Middleware\AuthenticateApiKey;
use App\Http\Middleware\EnsureTenantContext;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/token', [AuthController::class, 'token']);

    Route::middleware(['auth:sanctum', EnsureTenantContext::class])->group(function (): void {
        Route::get('auth/me', [AuthController::class, 'me']);
    });

    Route::middleware([AuthenticateApiKey::class, ApiRateLimit::class])->group(function (): void {
        Route::apiResource('trading-partners', TradingPartnerController::class)->only(['index', 'store', 'show']);
        Route::post('trading-partners/{trading_partner}/activate', [TradingPartnerController::class, 'activate']);

        Route::apiResource('edi-transactions', EdiTransactionController::class)->only(['index', 'show']);
        Route::apiResource('purchase-orders', PurchaseOrderController::class)->only(['index', 'store', 'show']);
        Route::apiResource('shipments', ShipmentController::class)->only(['index', 'store', 'show']);
        Route::apiResource('invoices', InvoiceController::class)->only(['index', 'store', 'show']);
        Route::get('inventory', [InventoryController::class, 'index']);

        Route::apiResource('webhooks', WebhookController::class)->only(['index', 'store', 'destroy']);

        Route::get('mappings', [MappingController::class, 'index']);
        Route::get('mappings/{mapping_profile}', [MappingController::class, 'show']);
        Route::post('mappings/suggest', [MappingController::class, 'suggest']);
    });
});
