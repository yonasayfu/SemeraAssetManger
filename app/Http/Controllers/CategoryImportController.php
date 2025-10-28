<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportCategoriesJob;

class CategoryImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');

        ImportCategoriesJob::dispatch($path, $request->user());

        return redirect()->back()->with('success', 'Your import has been queued.');
    }
}
