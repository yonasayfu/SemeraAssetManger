<?php

namespace App\Console;

use App\Jobs\CheckWarrantyExpiryJob;
use App\Jobs\CreateRecurringMaintenanceJob;
use App\Jobs\RunScheduledReportJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->job(new RunScheduledReportJob())
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer()
            ->name('reports.run-scheduled');

        $schedule
            ->job(new CreateRecurringMaintenanceJob())
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer()
            ->name('maintenance.generate-recurring');

        $schedule
            ->job(new CheckWarrantyExpiryJob())
            ->dailyAt('01:00')
            ->onOneServer()
            ->name('warranties.check-expiry');

        $schedule
            ->job(new \App\Jobs\GenerateAlertsJob())
            ->daily()
            ->name('alerts.generate');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
