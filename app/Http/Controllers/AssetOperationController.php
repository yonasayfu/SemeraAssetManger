<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetOperationController extends Controller
{
    public function select(Request $request, string $operation)
    {
        $search = $request->input('search');

        $assets = Asset::query()
            ->when($search, function ($query, $search) {
                $query->where('asset_tag', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhere('serial_no', 'like', '%' . $search . '%');
            })
            ->paginate(10);

        return Inertia::render('Assets/AssetSelect', [
            'assets' => $assets,
            'operation' => $operation,
            'search' => $search,
        ]);
    }
}
