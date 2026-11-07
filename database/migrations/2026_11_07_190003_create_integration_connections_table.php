<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('connector_type', [
                'sap', 'oracle', 'dynamics365', 'netsuite', 'odoo',
                'business_central', 'custom', 'mock', 'csv', 'xml', 'json_api',
            ])->default('mock');
            $table->string('status')->default('inactive');
            $table->text('config_encrypted')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->string('health_status')->default('unknown');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_connections');
    }
};
