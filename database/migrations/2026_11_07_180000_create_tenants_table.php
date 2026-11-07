<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('legal_name')->nullable();
            $table->json('organization_types')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->default('USD');
            $table->string('logo_path')->nullable();
            $table->json('branding')->nullable();
            $table->enum('status', ['draft', 'active', 'suspended', 'cancelled'])->default('draft');
            $table->boolean('is_demo')->default(false);
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
