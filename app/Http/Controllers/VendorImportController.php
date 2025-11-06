<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportVendorsJob;

class VendorImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');
        ImportVendorsJob::dispatch($path, $request->user());
        return redirect()->back()->with('success', 'Your vendor import has been queued.');
    }
}

