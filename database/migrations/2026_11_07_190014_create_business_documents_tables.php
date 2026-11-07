<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('edi_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('po_number')->index();
            $table->string('status')->default('open');
            $table->date('order_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->json('ship_to')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'po_number']);
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('line_number');
            $table->string('sku')->nullable();
            $table->string('description')->nullable();
            $table->decimal('quantity', 15, 4)->default(0);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->string('uom')->nullable();
            $table->timestamps();
        });

        Schema::create('order_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('edi_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ack_number')->nullable();
            $table->string('status')->default('accepted');
            $table->json('details')->nullable();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('edi_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('shipment_number')->index();
            $table->string('status')->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->string('carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'shipment_number']);
        });

        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('line_number')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('quantity_shipped', 15, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('asn_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('edi_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('asn_number')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('edi_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->index();
            $table->string('status')->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('invoice_date')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'invoice_number']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('line_number')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('quantity', 15, 4)->default(0);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('asn_documents');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('order_acknowledgments');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
