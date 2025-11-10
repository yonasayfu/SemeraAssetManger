<?php

namespace App\Jobs;

use App\Http\Controllers\StoreAssetImportController; // for resolveTokenPath logic if needed
use App\Imports\AssetsImport;
use App\Models\Asset;
use App\Models\AssetImportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class RunAssetImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200; // 20 minutes

    public function __construct(public int $jobId)
    {
    }

    public function handle(): void
    {
        $job = AssetImportJob::findOrFail($this->jobId);
        if ($job->cancelled) {
            $job->update(['status' => 'failed', 'message' => 'Cancelled before start', 'finished_at' => now()]);
            return;
        }
        $job->update(['status' => 'running', 'started_at' => now(), 'message' => null]);

        $mapping = (array)($job->mapping ?? []);
        $options = (array)($job->options ?? []);
        $options['job_id'] = $job->getKey();

        $assetsImport = new AssetsImport($mapping, $options);

        $before = Asset::count();
        try {
            Excel::import($assetsImport, $job->file_path, 'local');
        } catch (\Throwable $e) {
            $job->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'finished_at' => now(),
            ]);
            return;
        }

        // Build issue report if any
        $failures = $assetsImport->failures();
        $errors = method_exists($assetsImport, 'errors') ? $assetsImport->errors() : collect();
        $reportPath = null;
        if ((count($failures) + count($errors)) > 0) {
            $reportPath = "imports/reports/{$job->token}.csv";
            $lines = [];
            $lines[] = 'row,type,message,values';
            foreach ($failures as $f) {
                $msg = str_replace(["\r","\n"], ' ', implode('; ', $f->errors()));
                $vals = str_replace([",","\r","\n"], ' ', implode(' | ', $f->values()));
                $lines[] = $f->row().',validation,'.str_replace(',', ' ', $msg).','.str_replace(',', ' ', $vals);
            }
            foreach ($errors as $e) {
                $msg = get_class($e).': '.$e->getMessage();
                $lines[] = ',error,'.str_replace(',', ' ', $msg).',';
            }
            Storage::disk('local')->put($reportPath, implode("\n", $lines));
        }

        $created = max(0, Asset::count() - $before);
        $job->update([
            'status' => 'succeeded',
            'message' => 'Import completed. Created '.$created.' new assets.',
            'failures' => count($failures) + count($errors),
            'report_path' => $reportPath,
            'finished_at' => now(),
        ]);
    }
}
