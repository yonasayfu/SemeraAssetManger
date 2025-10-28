<?php

namespace App\Jobs;

use App\Jobs\RunSavedReportJob;
use App\Models\SavedReport;
use Cron\CronExpression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunScheduledReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SavedReport::query()
            ->whereNotNull('schedule_cron')
            ->with('owner')
            ->chunkById(50, function ($reports) {
                foreach ($reports as $report) {
                    if (! $this->shouldRun($report)) {
                        continue;
                    }

                    RunSavedReportJob::dispatch($report);
                }
            });
    }

    protected function shouldRun(SavedReport $report): bool
    {
        $cronExpression = $report->schedule_cron;

        if (blank($cronExpression)) {
            return false;
        }

        try {
            $cron = CronExpression::factory($cronExpression);
        } catch (\InvalidArgumentException $exception) {
            Log::error('Invalid cron expression for saved report.', [
                'saved_report_id' => $report->id,
                'expression' => $cronExpression,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }

        $timezone = optional($report->owner)->timezone ?? config('app.timezone');
        $now = now($timezone)->startOfMinute();
        $lastRun = optional($report->last_run_at)
            ? $report->last_run_at->copy()->setTimezone($timezone)->startOfMinute()
            : null;

        if ($lastRun && $lastRun->equalTo($now)) {
            // Already processed during this minute.
            return false;
        }

        return $cron->isDue($now, $timezone);
    }
}
