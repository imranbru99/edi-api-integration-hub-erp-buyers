<?php

namespace App\Services\Business;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function createFromCanonical(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $po = PurchaseOrder::create([
                'tenant_id' => $data['tenant_id'],
                'trading_partner_id' => $data['trading_partner_id'] ?? null,
                'edi_transaction_id' => $data['edi_transaction_id'] ?? null,
                'po_number' => $data['po_number'],
                'status' => $data['status'] ?? 'open',
                'order_date' => $data['order_date'] ?? now()->toDateString(),
                'total_amount' => $data['total_amount'] ?? 0,
                'currency' => $data['currency'] ?? 'USD',
                'ship_to' => $data['ship_to'] ?? null,
                'meta' => $data['meta'] ?? null,
            ]);

            foreach ($data['items'] ?? [] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'line_number' => (string) ($item['line_number'] ?? ''),
                    'sku' => $item['sku'] ?? null,
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'uom' => $item['uom'] ?? null,
                ]);
            }

            return $po->load('items');
        });
    }

    public function acknowledge(PurchaseOrder $order, array $details = []): PurchaseOrder
    {
        $order->status = 'acknowledged';
        $order->meta = array_merge($order->meta ?? [], ['acknowledgment' => $details]);
        $order->save();

        return $order;
    }
}
