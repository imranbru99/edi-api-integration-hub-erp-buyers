<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_platform_admin')->default(false)->after('password');
            $table->string('platform_role')->nullable()->after('is_platform_admin');
            $table->foreignId('current_tenant_id')->nullable()->after('platform_role')->constrained('tenants')->nullOnDelete();
            $table->string('phone')->nullable()->after('current_tenant_id');
            $table->string('job_title')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('job_title');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->text('two_factor_secret')->nullable()->after('last_login_at');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_secret');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_tenant_id');
            $table->dropColumn([
                'is_platform_admin',
                'platform_role',
                'phone',
                'job_title',
                'is_active',
                'last_login_at',
                'two_factor_secret',
                'two_factor_confirmed_at',
            ]);
        });
    }
};
