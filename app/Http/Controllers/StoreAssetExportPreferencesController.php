<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StoreAssetExportPreferencesController extends Controller
{
    /**
     * Persist the user's preferred Asset export columns.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        abort_unless($user instanceof Staff, 403);

        $validated = $request->validate([
            'columns' => ['required', 'array'],
            'columns.*' => ['string'],
        ]);

        $allowed = [
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

        $columns = array_values(array_intersect($allowed, $validated['columns']));

        $prefs = $user->list_preferences ?? [];
        if (!is_array($prefs)) {
            $prefs = [];
        }

        // Store under assets.export.columns
        data_set($prefs, 'assets.export.columns', $columns);
        $user->list_preferences = $prefs;
        $user->save();

        return back()->with('success', 'Export column defaults saved.');
    }
}

