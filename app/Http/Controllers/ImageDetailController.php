<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ImageDetailController extends Controller
{
    /**
     * Display an image detail page for an asset.
     */
    public function __invoke(Asset $asset)
    {
        return Inertia::render('Tools/Images/Show', [
            'asset' => [
                'id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'description' => $asset->description,
                'photo' => $asset->photo ? (str_starts_with($asset->photo, 'http') ? $asset->photo : Storage::disk('public')->url($asset->photo)) : null,
            ],
        ]);
    }
}

