<?php

namespace App\Http\Controllers;

use App\Models\AssetDocument;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentDetailController extends Controller
{
    /**
     * Display a document detail page.
     */
    public function __invoke(AssetDocument $document)
    {
        $document->load('asset:id,asset_tag,description');

        return Inertia::render('Tools/Documents/Show', [
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'mime_type' => $document->mime_type,
                'url' => $document->file_path ? Storage::disk('public')->url($document->file_path) : null,
                'asset' => $document->asset ? [
                    'id' => $document->asset->id,
                    'asset_tag' => $document->asset->asset_tag,
                    'description' => $document->asset->description,
                ] : null,
            ],
        ]);
    }
}

