<?php

namespace App\Http\Controllers;

use App\Jobs\RunAssetImportJob;
use App\Models\Asset;
use App\Models\AssetImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AssetImportJobController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Asset::class);

        $data = $request->validate([
            'token' => ['required','string'],
            'mapping' => ['required','array'],
            'options' => ['nullable'],
            'expected_total' => ['nullable','integer','min:0'],
        ]);

        $token = (string)$data['token'];
        $path = $this->resolveTokenPath($token);
        if (!$path) {
            return response()->json(['message' => 'Import file not found for token.'], 422);
        }

        $options = $data['options'];
        if (is_string($options)) {
            $decoded = json_decode($options, true);
            $options = is_array($decoded) ? $decoded : [];
        }

        $total = (int)($data['expected_total'] ?? 0);
        if ($total <= 0) {
            // Try to compute from spreadsheet quickly
            try {
                $absolute = Storage::disk('local')->path($path);
                $spreadsheet = IOFactory::load($absolute);
                $sheet = $spreadsheet->getSheet(0);
                $rows = max(0, (int)$sheet->getHighestRow() - 1);
                $total = $rows;
            } catch (\Throwable) {
                $total = 0;
            }
        }

        $job = AssetImportJob::create([
            'staff_id' => $request->user()->getKey(),
            'token' => $token,
            'file_path' => $path,
            'mapping' => $data['mapping'],
            'options' => $options,
            'status' => 'pending',
            'total_rows' => $total,
            'processed_rows' => 0,
        ]);

        RunAssetImportJob::dispatch($job->getKey());

        return response()->json(['job' => $job]);
    }

    public function show(Request $request, AssetImportJob $job)
    {
        $this->authorize('create', Asset::class);
        abort_unless($job->staff_id === $request->user()->getKey(), 403);
        return response()->json(['job' => $job]);
    }

    public function cancel(Request $request, AssetImportJob $job)
    {
        $this->authorize('create', Asset::class);
        abort_unless($job->staff_id === $request->user()->getKey(), 403);
        if (in_array($job->status, ['succeeded', 'failed'])) {
            return response()->json(['job' => $job, 'message' => 'Job already finished.'], 422);
        }
        $job->update(['cancelled' => true]);
        if ($job->status === 'pending') {
            $job->update(['status' => 'failed', 'message' => 'Cancelled by user', 'finished_at' => now()]);
        }
        return response()->json(['job' => $job]);
    }

    protected function resolveTokenPath(string $token): ?string
    {
        $files = Storage::disk('local')->files('imports/tmp');
        foreach ($files as $file) {
            if (str_contains($file, $token.'.')) {
                return $file;
            }
        }
        return null;
    }
}
