<?php

namespace App\Jobs;

use App\Models\SavedReport;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReportGeneratedMail; // Assuming you'll create this Mailable

class RunSavedReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected SavedReport $savedReport;

    /**
     * Create a new job instance.
     */
    public function __construct(SavedReport $savedReport)
    {
        $this->savedReport = $savedReport;
    }

    /**
     * Execute the job.
     */
    public function handle(ReportService $reportService): void
    {
        try {
            $query = $reportService->getReportQuery(
                $this->savedReport->family,
                $this->savedReport->definition_json['filters'] ?? []
            );
        } catch (\InvalidArgumentException $exception) {
            \Log::error('Saved report failed: unknown family', [
                'saved_report_id' => $this->savedReport->id,
                'family' => $this->savedReport->family,
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        $results = $query->get();

        Mail::to($this->savedReport->owner->email)->send(
            new ReportGeneratedMail($this->savedReport, $results)
        );

        $this->savedReport->forceFill(['last_run_at' => now()])->save();
    }
}
