<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edi_envelopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edi_transaction_id')->constrained()->cascadeOnDelete();
            $table->string('envelope_type');
            $table->string('sender_id')->nullable();
            $table->string('receiver_id')->nullable();
            $table->string('control_number')->nullable();
            $table->json('headers')->nullable();
            $table->timestamps();
        });

        Schema::create('edi_control_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trading_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->string('standard');
            $table->string('number_type');
            $table->unsignedBigInteger('last_value')->default(0);
            $table->timestamps();
            $table->unique(['tenant_id', 'trading_partner_id', 'standard', 'number_type'], 'edi_ctrl_unique');
        });

        Schema::create('edi_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edi_transaction_id')->constrained()->cascadeOnDelete();
            $table->string('ack_type');
            $table->string('status')->default('pending');
            $table->longText('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('edi_transaction_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edi_transaction_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->string('level')->default('info');
            $table->text('message')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('edi_validation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('standard');
            $table->string('document_type');
            $table->string('rule_code');
            $table->string('severity')->default('error');
            $table->json('definition')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edi_validation_rules');
        Schema::dropIfExists('edi_transaction_events');
        Schema::dropIfExists('edi_acknowledgments');
        Schema::dropIfExists('edi_control_numbers');
        Schema::dropIfExists('edi_envelopes');
    }
};
