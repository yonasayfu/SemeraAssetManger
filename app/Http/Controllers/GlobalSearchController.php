<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Staff;
use App\Models\User;
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
                    'url' => route('users.edit', $user),
                    'icon' => 'user',
                ];
            }));
        }

        if ($request->user()->can('staff.view')) {
            $staffMatches = Staff::query()
                ->where(function ($query) use ($term) {
                    $query
                        ->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('job_title', 'like', "%{$term}%");
                })
                ->orderBy('last_name')
                ->limit(5)
                ->get();

            $results = $results->merge($staffMatches->map(function (Staff $staff) {
                return [
                    'type' => 'Staff',
                    'category' => 'Directory',
                    'title' => $staff->full_name,
                    'description' => $staff->email,
                    'url' => route('staff.edit', $staff),
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

            // Log the SQL query and bindings
            \Illuminate\Support\Facades\Log::info('Asset Search SQL: ' . $assetQuery->toSql());
            \Illuminate\Support\Facades\Log::info('Asset Search Bindings: ', $assetQuery->getBindings());

            $assetMatches = $assetQuery
                ->orderBy('asset_tag')
                ->limit(5)
                ->get();

            \Illuminate\Support\Facades\Log::info('Asset Search Term: ' . $term . ', Matches: ' . $assetMatches->count());

            $results = $results->merge($assetMatches->map(function (Asset $asset) {
                return [
                    'type' => 'Asset',
                    'category' => 'Inventory',
                    'title' => $asset->asset_tag,
                    'description' => $asset->description,
                    'url' => route('assets.show', $asset->id),
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
