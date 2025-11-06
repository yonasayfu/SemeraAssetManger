<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Jobs\ImportProductsJob;

class ProductImportController extends Controller
{
    public function __invoke(ImportRequest $request)
    {
        $path = $request->file('file')->store('imports');
        ImportProductsJob::dispatch($path, $request->user());
        return redirect()->back()->with('success', 'Your product import has been queued.');
    }
}

