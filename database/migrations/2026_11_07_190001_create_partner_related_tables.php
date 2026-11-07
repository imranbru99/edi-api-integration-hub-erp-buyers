<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_partner_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('role')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('partner_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_partner_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('billing');
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->nullable();
            $table->timestamps();
        });

        Schema::create('partner_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_partner_id')->constrained()->cascadeOnDelete();
            $table->string('qualifier');
            $table->string('value');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('partner_document_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_partner_id')->constrained()->cascadeOnDelete();
            $table->string('standard');
            $table->string('document_code');
            $table->string('direction')->default('inbound');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('partner_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_partner_id')->constrained()->cascadeOnDelete();
            $table->string('protocol')->default('as2');
            $table->string('host')->nullable();
            $table->unsignedSmallInteger('port')->nullable();
            $table->text('credentials_encrypted')->nullable();
            $table->json('directories')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_connections');
        Schema::dropIfExists('partner_document_types');
        Schema::dropIfExists('partner_identifiers');
        Schema::dropIfExists('partner_addresses');
        Schema::dropIfExists('partner_contacts');
    }
};
