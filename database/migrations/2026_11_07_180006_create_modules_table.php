<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('module_categories')->nullOnDelete();
            $table->string('icon')->nullable();
            $table->string('version')->default('1.0.0');
            $table->boolean('is_core')->default(false);
            $table->boolean('default_enabled')->default(false);
            $table->json('plan_restrictions')->nullable();
            $table->json('dependencies')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
