<?php

namespace App\Services\Business;

use App\Models\Shipment;
use App\Models\ShipmentItem;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    public function create(array $data): Shipment
    {
        return DB::transaction(function () use ($data) {
            $shipment = Shipment::create([
                'tenant_id' => $data['tenant_id'],
                'trading_partner_id' => $data['trading_partner_id'] ?? null,
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'edi_transaction_id' => $data['edi_transaction_id'] ?? null,
                'shipment_number' => $data['shipment_number'],
                'status' => $data['status'] ?? 'pending',
                'shipped_at' => $data['shipped_at'] ?? null,
                'carrier' => $data['carrier'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'meta' => $data['meta'] ?? null,
            ]);

            foreach ($data['items'] ?? [] as $item) {
                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'line_number' => $item['line_number'] ?? null,
                    'sku' => $item['sku'] ?? null,
                    'quantity_shipped' => $item['quantity_shipped'] ?? 0,
                ]);
            }

            return $shipment->load('items');
        });
    }

    public function markShipped(Shipment $shipment, ?string $trackingNumber = null): Shipment
    {
        $shipment->status = 'shipped';
        $shipment->shipped_at = now();
        if ($trackingNumber) {
            $shipment->tracking_number = $trackingNumber;
        }
        $shipment->save();

        return $shipment;
    }
}
