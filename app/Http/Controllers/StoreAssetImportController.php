<?php

namespace App\Http\Controllers;

use App\Imports\AssetsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StoreAssetImportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new AssetsImport, $request->file('file'));

        return redirect()->route('assets.index');
    }
}
