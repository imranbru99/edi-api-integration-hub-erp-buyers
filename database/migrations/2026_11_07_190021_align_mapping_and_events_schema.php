<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mapping_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('mapping_profiles', 'trading_partner_id')) {
                $table->foreignId('trading_partner_id')->nullable()->after('tenant_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('mapping_profiles', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
        });

        Schema::table('mapping_rules', function (Blueprint $table) {
            if (! Schema::hasColumn('mapping_rules', 'mapping_profile_id')) {
                $table->foreignId('mapping_profile_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('mapping_rules', 'condition')) {
                $table->json('condition')->nullable();
            }
            if (! Schema::hasColumn('mapping_rules', 'default_value')) {
                $table->string('default_value')->nullable();
            }
        });

        Schema::table('edi_transaction_events', function (Blueprint $table) {
            if (! Schema::hasColumn('edi_transaction_events', 'status')) {
                $table->string('status')->nullable()->after('event');
            }
        });

        Schema::table('edi_acknowledgments', function (Blueprint $table) {
            if (! Schema::hasColumn('edi_acknowledgments', 'standard')) {
                $table->string('standard')->nullable()->after('ack_type');
            }
            if (! Schema::hasColumn('edi_acknowledgments', 'document_type')) {
                $table->string('document_type')->nullable()->after('standard');
            }
        });

        Schema::table('edi_control_numbers', function (Blueprint $table) {
            if (! Schema::hasColumn('edi_control_numbers', 'counter_type')) {
                $table->string('counter_type')->nullable()->after('number_type');
            }
        });
    }

    public function down(): void
    {
        // Non-destructive rollback omitted for SQLite compatibility during local demos.
    }
};
