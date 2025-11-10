<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetImportPreset;
use Illuminate\Http\Request;

class AssetImportPresetController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', Asset::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'mapping' => ['required', 'array'],
            'options' => ['nullable'],
        ]);

        $options = $data['options'];
        if (is_string($options)) {
            $decoded = json_decode($options, true);
            $options = is_array($decoded) ? $decoded : [];
        }

        AssetImportPreset::updateOrCreate(
            ['staff_id' => (int)$request->user()->getKey(), 'name' => $data['name']],
            ['mapping' => $data['mapping'], 'options' => $options]
        );

        return back()
            ->with('flash.banner', 'Preset saved.')
            ->with('flash.bannerStyle', 'success');
    }

    public function destroy(Request $request, AssetImportPreset $preset)
    {
        $this->authorize('create', Asset::class);
        abort_unless($preset->staff_id === $request->user()->getKey(), 403);
        $preset->delete();

        return back()
            ->with('flash.banner', 'Preset deleted.')
            ->with('flash.bannerStyle', 'success');
    }
}

