<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edi_standards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('edi_document_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edi_standard_id')->constrained('edi_standards')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('direction')->default('both');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['edi_standard_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edi_document_types');
        Schema::dropIfExists('edi_standards');
    }
};
