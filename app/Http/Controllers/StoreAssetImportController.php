<?php

namespace App\Http\Controllers;

use App\Imports\AssetsImport;
use App\Models\Asset;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StoreAssetImportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $this->authorize('create', Asset::class);
        $validated = $request->validate([
            'file' => 'nullable|file|mimes:xlsx,xls,csv',
            'token' => 'nullable|string',
            'mapping' => 'nullable|array',
            'options' => 'nullable|array',
        ]);

        // Resolve file to import: either newly uploaded or by preview token
        $path = null;
        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $path = $uploaded->getRealPath();
        } elseif (!empty($validated['token'])) {
            $token = $validated['token'];
            $path = $this->resolveTokenPath($token);
            if ($path) {
                $path = storage_path('app/'.$path);
            }
        }

        if (!$path || !file_exists($path)) {
            return back()->with('error', 'No import file provided.');
        }

        $mapping = (array) ($validated['mapping'] ?? []);
        $options = (array) ($validated['options'] ?? []);

        Excel::import(new AssetsImport($mapping, $options), $path);

        return redirect()->route('assets.index')->with('success', 'Import completed.');
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
