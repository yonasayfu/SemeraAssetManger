<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportSoftwareJob;

class SoftwareImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');
        ImportSoftwareJob::dispatch($path, $request->user());
        return redirect()->back()->with('success', 'Your software import has been queued.');
    }
}

