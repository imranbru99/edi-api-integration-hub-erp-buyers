<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trading_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->index();
            $table->string('partner_type')->default('buyer');
            $table->string('status')->default('draft');
            $table->string('edi_id')->nullable();
            $table->string('isa_id')->nullable();
            $table->string('gs_id')->nullable();
            $table->string('edifact_id')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->default('USD');
            $table->string('default_standard')->default('x12');
            $table->string('communication_method')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedTinyInteger('onboarding_step')->default(0);
            $table->boolean('is_demo')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trading_partners');
    }
};
