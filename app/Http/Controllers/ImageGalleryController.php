<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Inertia\Inertia;

class ImageGalleryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return Inertia::render('Tools/Images/Index', [
            'assets' => Asset::whereNotNull('photo')->latest()->get(),
        ]);
    }
}
