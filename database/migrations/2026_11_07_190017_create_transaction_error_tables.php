<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('edi_transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('error_code')->nullable();
            $table->string('severity')->default('error');
            $table->text('message');
            $table->json('context')->nullable();
            $table->boolean('resolved')->default(false);
            $table->timestamps();
        });

        Schema::create('transaction_retries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edi_transaction_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->string('status')->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->text('result_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_retries');
        Schema::dropIfExists('transaction_errors');
    }
};
