<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('x509');
            $table->string('fingerprint')->nullable()->index();
            $table->timestamp('not_before')->nullable();
            $table->timestamp('not_after')->nullable();
            $table->string('public_cert_path')->nullable();
            $table->text('private_key_encrypted')->nullable();
            $table->string('status')->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
