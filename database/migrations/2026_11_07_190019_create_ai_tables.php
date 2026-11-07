<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_provider_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('openai');
            $table->string('model')->nullable();
            $table->boolean('enabled')->default(false);
            $table->boolean('authorized')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('feature');
            $table->unsignedInteger('tokens_in')->default(0);
            $table->unsignedInteger('tokens_out')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject')->nullable();
            $table->json('messages')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_approval_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id');
            $table->string('suggestion_type');
            $table->json('suggestion_payload')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_approval_records');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_provider_settings');
    }
};
