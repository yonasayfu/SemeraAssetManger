<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportMaintenancesJob;

class MaintenanceImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');

        ImportMaintenancesJob::dispatch($path, $request->user());

        return redirect()->back()->with('success', 'Your import has been queued.');
    }
}
