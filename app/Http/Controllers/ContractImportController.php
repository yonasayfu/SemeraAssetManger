<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportContractsJob;

class ContractImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');
        ImportContractsJob::dispatch($path, $request->user());
        return redirect()->back()->with('success', 'Your contract import has been queued.');
    }
}

