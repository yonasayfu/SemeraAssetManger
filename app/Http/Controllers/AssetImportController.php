<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Asset;

class AssetImportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke()
    {
        $this->authorize('create', Asset::class);
        return Inertia::render('Assets/Import');
    }
}
