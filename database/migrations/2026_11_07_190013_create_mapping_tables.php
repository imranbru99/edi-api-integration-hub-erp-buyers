<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mapping_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('standard')->default('x12');
            $table->string('document_type');
            $table->string('direction')->default('inbound');
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('mapping_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapping_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('mapping_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapping_version_id')->constrained()->cascadeOnDelete();
            $table->string('source_path');
            $table->string('target_path');
            $table->string('transform')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('mapping_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapping_version_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->string('data_type')->default('string');
            $table->boolean('required')->default(false);
            $table->json('constraints')->nullable();
            $table->timestamps();
        });

        Schema::create('mapping_test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapping_version_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('input_payload')->nullable();
            $table->json('expected_output')->nullable();
            $table->string('last_result')->nullable();
            $table->timestamps();
        });

        Schema::create('mapping_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapping_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('comments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapping_approvals');
        Schema::dropIfExists('mapping_test_cases');
        Schema::dropIfExists('mapping_fields');
        Schema::dropIfExists('mapping_rules');
        Schema::dropIfExists('mapping_versions');
        Schema::dropIfExists('mapping_profiles');
    }
};
