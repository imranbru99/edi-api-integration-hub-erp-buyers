<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edi_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('edi_transactions', 'raw_content')) {
                $table->longText('raw_content')->nullable()->after('meta');
            }
            if (! Schema::hasColumn('edi_transactions', 'parsed_content')) {
                $table->json('parsed_content')->nullable();
            }
            if (! Schema::hasColumn('edi_transactions', 'canonical_content')) {
                $table->json('canonical_content')->nullable();
            }
            if (! Schema::hasColumn('edi_transactions', 'transformed_content')) {
                $table->json('transformed_content')->nullable();
            }
            if (! Schema::hasColumn('edi_transactions', 'error_message')) {
                $table->text('error_message')->nullable();
            }
            if (! Schema::hasColumn('edi_transactions', 'idempotency_key')) {
                $table->string('idempotency_key', 64)->nullable()->index();
            }
            if (! Schema::hasColumn('edi_transactions', 'acknowledgment_id')) {
                $table->unsignedBigInteger('acknowledgment_id')->nullable();
            }
            if (! Schema::hasColumn('edi_transactions', 'delivery_attempts')) {
                $table->unsignedInteger('delivery_attempts')->default(0);
            }
            if (! Schema::hasColumn('edi_transactions', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('edi_transactions', function (Blueprint $table) {
            $cols = [
                'raw_content', 'parsed_content', 'canonical_content', 'transformed_content',
                'error_message', 'idempotency_key', 'acknowledgment_id', 'delivery_attempts', 'delivered_at',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('edi_transactions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
