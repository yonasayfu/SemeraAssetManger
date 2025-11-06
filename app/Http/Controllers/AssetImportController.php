<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Asset;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /**
     * Download a CSV template with the recommended AssetTiger-compatible columns.
     */
    public function template(): StreamedResponse
    {
        $this->authorize('create', Asset::class);

        $headers = [
            'Asset Photo',
            'Asset Tag ID',
            'Description',
            'Purchase Date',
            'Cost',
            'Status',
            'Purchased from',
            'Serial No',
            'Site',
            'Location',
            'Category',
            'Department',
            'Assigned to',
            'Project code',
        ];

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            fclose($handle);
        }, 'asset-import-template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
