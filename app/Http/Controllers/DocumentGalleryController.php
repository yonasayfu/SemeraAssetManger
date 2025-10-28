<?php

namespace App\Http\Controllers;

use App\Models\AssetDocument;
use Inertia\Inertia;

class DocumentGalleryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return Inertia::render('Tools/Documents/Index', [
            'documents' => AssetDocument::with('asset')->latest()->get(),
        ]);
    }
}
