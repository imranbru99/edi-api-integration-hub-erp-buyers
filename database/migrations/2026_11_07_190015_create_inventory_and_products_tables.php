<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_catalog_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 15, 4)->nullable();
            $table->string('uom')->nullable();
            $table->json('attributes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'sku']);
        });

        Schema::create('inventory_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('location_code')->nullable();
            $table->decimal('quantity_on_hand', 15, 4)->default(0);
            $table->decimal('quantity_reserved', 15, 4)->default(0);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_records');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_catalogs');
    }
};
