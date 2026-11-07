<?php

namespace App\Console\Commands;

use App\Services\Demo\DemoResetService;
use Database\Seeders\DemoSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Console\Command;

class DemoSetupCommand extends Command
{
    protected $signature = 'edi-hub:demo-setup {--reset : Reset demo tenant data before seeding}';

    protected $description = 'Seed EDI Hub demo tenant, partners, transactions, and demo users';

    public function handle(DemoResetService $reset): int
    {
        if ($this->option('reset')) {
            if (! $reset->enabled()) {
                $this->error('Cannot reset: set APP_DEMO_MODE=true in your environment.');

                return self::FAILURE;
            }
            $reset->reset();
            $this->info('Demo environment reset and re-seeded.');

            return self::SUCCESS;
        }

        $this->call('db:seed', ['--class' => PlanSeeder::class, '--force' => true]);
        $this->call('db:seed', ['--class' => DemoSeeder::class, '--force' => true]);
        $this->info('Demo data seeded. Visit /demo to explore.');

        return self::SUCCESS;
    }
}
