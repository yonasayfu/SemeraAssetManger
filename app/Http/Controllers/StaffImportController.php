<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportStaffJob;

class StaffImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');
        $mapping = (array) $request->input('mapping', []);
        $options = (array) json_decode((string) $request->input('options', '[]'), true);

        ImportStaffJob::dispatch($path, $request->user(), $mapping, $options);

        return redirect()->back()->with('success', 'Your import has been queued.');
    }
}
