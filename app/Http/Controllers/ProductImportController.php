<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportProductsJob;

class ProductImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');
        $mapping = (array) $request->input('mapping', []);
        $options = (array) json_decode((string) $request->input('options', '[]'), true);

        ImportProductsJob::dispatch($path, $request->user(), $mapping, $options);
        return redirect()->back()->with('success', 'Your product import has been queued.');
    }
}
