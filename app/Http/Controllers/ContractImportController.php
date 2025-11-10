<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportContractsJob;

class ContractImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');
        $mapping = (array) $request->input('mapping', []);
        $options = (array) json_decode((string) $request->input('options', '[]'), true);

        ImportContractsJob::dispatch($path, $request->user(), $mapping, $options);
        return redirect()->back()->with('success', 'Your contract import has been queued.');
    }
}
