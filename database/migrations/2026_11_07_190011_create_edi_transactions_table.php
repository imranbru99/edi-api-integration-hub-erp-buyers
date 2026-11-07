<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edi_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->string('standard')->default('x12');
            $table->string('document_type');
            $table->string('direction')->default('inbound');
            $table->string('control_number')->nullable()->index();
            $table->string('interchange_control_number')->nullable();
            $table->string('group_control_number')->nullable();
            $table->string('transaction_set_control_number')->nullable();
            $table->string('status')->default('received');
            $table->longText('raw_payload')->nullable();
            $table->json('parsed_payload')->nullable();
            $table->json('canonical_payload')->nullable();
            $table->string('checksum')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->unsignedInteger('validation_errors_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edi_transactions');
    }
};
