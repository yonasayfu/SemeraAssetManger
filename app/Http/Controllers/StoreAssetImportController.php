<?php

namespace App\Http\Controllers;

use App\Imports\AssetsImport;
use App\Models\Asset;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class StoreAssetImportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        Log::info('StoreAssetImportController __invoke method hit.');
        $this->authorize('create', Asset::class);
        $validated = $request->validate([
            'file' => 'nullable|file|mimes:xlsx,xls,csv',
            'token' => 'nullable|string',
            'mapping' => 'nullable|array',
            'options' => 'nullable|string',
        ]);

        $mapping = (array) ($validated['mapping'] ?? []);
        $options = isset($validated['options']) ? json_decode($validated['options'], true) : [];

        // Resolve input file: prefer token from preview flow; otherwise accept direct upload
        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        $relativePath = null;

        if (!empty($validated['token'])) {
            $relativePath = $this->resolveTokenPath((string) $validated['token']);
            if (!$relativePath) {
                return back()
                    ->with('flash.banner', 'Import file not found for the provided token. Please re-upload your file and try again.')
                    ->with('flash.bannerStyle', 'danger');
            }
        } elseif ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $token = 'imp_'.\Illuminate\Support\Str::random(20);
            $ext = $uploaded->getClientOriginalExtension() ?: 'xlsx';
            $relativePath = 'imports/tmp/'.$token.'.'.$ext;
            $disk->put($relativePath, file_get_contents($uploaded->getRealPath()));
        } else {
            return back()
                ->with('flash.banner', 'No file provided. Please upload a CSV/XLSX or use the preview flow first.')
                ->with('flash.bannerStyle', 'danger');
        }

        $assetsImport = new AssetsImport($mapping, $options);

        $before = Asset::count();
        Log::info('Assets count before import: '.$before);
        try {
            // Use the local disk so Excel can resolve the file by relative path
            Excel::import($assetsImport, $relativePath, 'local');
        } catch (\Throwable $e) {
            return back()
                ->with('flash.banner', 'Import failed: '.$e->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }

        $imported = max(0, Asset::count() - $before);
        $failures = $assetsImport->failures();
        $errors = method_exists($assetsImport, 'errors') ? $assetsImport->errors() : collect();

        // Write issue report if any
        $reportToken = ($validated['token'] ?? null) ?: ($token ?? 'no_token');
        $reportPath = "imports/reports/{$reportToken}.csv";
        if ((count($failures) > 0 || count($errors) > 0)) {
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
            \Illuminate\Support\Facades\Storage::disk('local')->put($reportPath, implode("\n", $lines));
        }

        if (count($failures) > 0 || count($errors) > 0) {
            $reportLink = (count($failures) + count($errors)) > 0 ? route('assets.import.report', ['token' => $reportToken]) : null;
            $failureMessages = $failures->map(function ($failure) {
                return 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . ' (Value: ' . implode(', ', $failure->values()) . ')';
            });
            $errorMessages = $errors->map(function ($e) {
                return e(class_basename($e)).': '.e($e->getMessage());
            });
            $allMessages = $failureMessages->merge($errorMessages)->implode('<br>');

            $successMessage = '';
            if ($imported > 0) {
                $successMessage = 'Successfully created ' . $imported . ' new record(s). ';
            } else {
                $successMessage = 'Import completed (existing assets updated where tags matched). ';
            }

            $downloadNote = $reportLink ? ('<br><a href="'.$reportLink.'" class="underline text-indigo-600">Download issue report</a>') : '';
            return redirect()
                ->route('assets.index')
                ->with('flash.banner', $successMessage . 'Import completed with ' . (count($failures) + count($errors)) . ' issue(s):<br>' . $allMessages . $downloadNote)
                ->with('flash.bannerStyle', 'warning');
        }

        $message = $imported > 0
            ? ('Import completed. Imported '.$imported.' record(s).')
            : 'Import completed. Existing assets updated where tags matched.';

        return redirect()
            ->route('assets.index')
            ->with('flash.banner', $message)
            ->with('flash.bannerStyle', 'success');
    }

    protected function resolveTokenPath(string $token): ?string
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        $files = $disk->files('imports/tmp');
        foreach ($files as $file) {
            if (str_contains($file, $token.'.')) {
                return $file;
            }
        }
        return null;
    }
}
