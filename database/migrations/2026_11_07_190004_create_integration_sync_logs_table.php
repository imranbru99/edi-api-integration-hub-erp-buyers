<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_connection_id')->constrained()->cascadeOnDelete();
            $table->string('direction')->default('inbound');
            $table->string('entity_type')->nullable();
            $table->string('status')->default('started');
            $table->unsignedInteger('records_processed')->default(0);
            $table->unsignedInteger('records_failed')->default(0);
            $table->text('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_sync_logs');
    }
};
