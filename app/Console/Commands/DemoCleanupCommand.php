<?php

namespace App\Console\Commands;

use App\Services\DemoCleanupService;
use Illuminate\Console\Command;

class DemoCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cleanup {--all : Force cleanup all demo activities immediately regardless of 10-minute expiry}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically clean up and revert content created or modified by demo accounts after 10 minutes';

    /**
     * Execute the console command.
     */
    public function handle(DemoCleanupService $service): int
    {
        $forceAll = $this->option('all');

        if ($forceAll) {
            $this->info('Force-cleaning all demo modifications...');
            $count = $service->resetAllDemoData();
            $this->info("Successfully reset and cleaned {$count} demo activities.");

            return Command::SUCCESS;
        }

        $count = $service->cleanupExpired();

        if ($count > 0) {
            $this->info("Successfully cleaned {$count} expired demo activities (>= 10 minutes old).");
        } else {
            $this->line('No expired demo activities to clean.');
        }

        return Command::SUCCESS;
    }
}
