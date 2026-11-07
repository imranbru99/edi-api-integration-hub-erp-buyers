<?php

namespace App\Services\Business;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'tenant_id' => $data['tenant_id'],
                'trading_partner_id' => $data['trading_partner_id'] ?? null,
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'edi_transaction_id' => $data['edi_transaction_id'] ?? null,
                'invoice_number' => $data['invoice_number'],
                'status' => $data['status'] ?? 'draft',
                'total_amount' => $data['total_amount'] ?? 0,
                'currency' => $data['currency'] ?? 'USD',
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'meta' => $data['meta'] ?? null,
            ]);

            foreach ($data['items'] ?? [] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'line_number' => $item['line_number'] ?? null,
                    'sku' => $item['sku'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'line_total' => $item['line_total'] ?? 0,
                ]);
            }

            return $invoice->load('items');
        });
    }

    public function post(Invoice $invoice): Invoice
    {
        $invoice->status = 'posted';
        $invoice->save();

        return $invoice;
    }
}
