<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportWarrantiesJob;

class WarrantyImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');

        ImportWarrantiesJob::dispatch($path, $request->user());

        return redirect()->back()->with('success', 'Your import has been queued.');
    }
}
