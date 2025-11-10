<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetImportReportController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $this->authorize('create', Asset::class);
        $path = "imports/reports/{$token}.csv";
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }
        return Storage::disk('local')->download($path, "import-issues-{$token}.csv");
    }
}

