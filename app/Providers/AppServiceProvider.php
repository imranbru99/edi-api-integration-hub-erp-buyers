<?php

namespace App\Providers;

use App\Domain\Edi\Contracts\DeliveryGatewayInterface;
use App\Domain\Edi\Delivery\As2DeliveryGateway;
use App\Domain\Edi\Delivery\MockDeliveryGateway;
use App\Domain\Edi\Delivery\RestDeliveryGateway;
use App\Domain\Edi\Delivery\SftpDeliveryGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DeliveryGatewayInterface::class, function () {
            $driver = (string) config('edi-hub.delivery_driver', 'mock');

            return match ($driver) {
                'as2' => $this->app->make(As2DeliveryGateway::class),
                'sftp' => $this->app->make(SftpDeliveryGateway::class),
                'rest' => $this->app->make(RestDeliveryGateway::class),
                default => $this->app->make(MockDeliveryGateway::class),
            };
        });

        $this->app->singleton(\App\Services\Audit\AuditLogger::class);
        $this->app->singleton(\App\Services\Modules\ModuleRegistryService::class);
        $this->app->singleton(\App\Services\Billing\UsageMeteringService::class);
        $this->app->singleton(\App\Services\Tenancy\TenantRegistrationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
