<?php

namespace App\Services\Partners;

use App\Models\EdiTransaction;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Shipment;
use App\Models\TradingPartner;
use App\Models\TransactionError;
use Illuminate\Support\Facades\DB;

class PartnerDashboardService
{
    public function stats(?int $tradingPartnerId = null): array
    {
        $partnerQuery = TradingPartner::query();
        $transactionQuery = EdiTransaction::query();
        $errorQuery = TransactionError::query()->where('resolved', false);

        if ($tradingPartnerId) {
            $transactionQuery->where('trading_partner_id', $tradingPartnerId);
            $errorQuery->whereHas('ediTransaction', fn ($q) => $q->where('trading_partner_id', $tradingPartnerId));
        }

        $transactionsByStatus = (clone $transactionQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'partners_total' => $partnerQuery->count(),
            'partners_active' => (clone $partnerQuery)->where('status', 'active')->count(),
            'edi_transactions_total' => $transactionQuery->count(),
            'edi_transactions_by_status' => $transactionsByStatus,
            'open_errors' => $errorQuery->count(),
            'purchase_orders_open' => PurchaseOrder::query()->where('status', 'open')->count(),
            'shipments_pending' => Shipment::query()->where('status', 'pending')->count(),
            'invoices_draft' => Invoice::query()->where('status', 'draft')->count(),
        ];
    }
}
