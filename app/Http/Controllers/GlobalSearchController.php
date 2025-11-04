<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Staff;
use App\Models\Staff as User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request->user()?->can('users.manage') && !$request->user()?->can('staff.view')) {
            abort(403);
        }

        $term = trim((string) $request->query('q', ''));

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $results = collect();

        if ($request->user()->can('users.manage')) {
            $userMatches = User::query()
                ->where(function ($query) use ($term) {
                    $query
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })
                ->orderBy('name')
                ->limit(5)
                ->get();

            $results = $results->merge($userMatches->map(function (User $user) {
                return [
                    'type' => 'User',
                    'category' => 'Identity',
                    'title' => $user->name,
                    'description' => $user->email,
                    'url' => url("/staff/{$user->id}/edit"),
                    'icon' => 'user',
                ];
            }));
        }

        if ($request->user()->can('staff.view')) {
            $staffMatches = Staff::query()
                ->where(function ($query) use ($term) {
                    $query
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })
                ->orderBy('name')
                ->limit(5)
                ->get();

            $results = $results->merge($staffMatches->map(function (Staff $staff) {
                return [
                    'type' => 'Staff',
                    'category' => 'Directory',
                    'title' => $staff->full_name,
                    'description' => $staff->email,
                    'url' => url("/staff/{$staff->id}/edit"),
                    'icon' => 'users',
                ];
            }));
        }

        // Asset Search Integration
        if ($request->user()->can('assets.view')) {
            $assetQuery = Asset::query()
                ->where(function ($query) use ($term) {
                    $query
                        ->where('asset_tag', 'like', "%" . $term . "%")
                        ->orWhere('description', 'like', "%" . $term . "%")
                        ->orWhere('serial_no', 'like', "%" . $term . "%");
                });

            $assetMatches = $assetQuery
                ->orderBy('asset_tag')
                ->limit(5)
                ->get();

            $results = $results->merge($assetMatches->map(function (Asset $asset) {
                return [
                    'type' => 'Asset',
                    'category' => 'Inventory',
                    'title' => $asset->asset_tag,
                    'description' => $asset->description,
                    'url' => url("/assets/{$asset->id}"),
                    'icon' => 'box',
                ];
            }));
        }

        if ($results->isEmpty()) {
            $results->push([
                'type' => 'No Results',
                'category' => 'Search',
                'title' => 'No results found.',
                'description' => 'Try a different search term.',
                'url' => '#',
                'icon' => 'search',
            ]);
        }

        return response()->json($results->take(15)->values());
    }
}
