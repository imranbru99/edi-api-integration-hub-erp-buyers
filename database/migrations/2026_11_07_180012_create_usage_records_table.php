<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('metric');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->timestamps();

            $table->index(['tenant_id', 'metric', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_records');
    }
};
