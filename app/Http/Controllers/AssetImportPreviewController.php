<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AssetImportPreviewController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->authorize('create', \App\Models\Asset::class);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $uploaded = $request->file('file');
        $token = 'imp_'.Str::random(20);
        $ext = $uploaded->getClientOriginalExtension() ?: 'xlsx';
        $path = 'imports/tmp/'.$token.'.'.$ext;
        Storage::disk('local')->put($path, file_get_contents($uploaded->getRealPath()));

        // Read headings + sample rows via PhpSpreadsheet for speed.
        $absolute = Storage::disk('local')->path($path);
        $spreadsheet = IOFactory::load($absolute);
        $sheet = $spreadsheet->getSheet(0);
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = min($sheet->getHighestRow(), 26); // header + 25 rows

        $headerRow = $sheet->rangeToArray('A1:'.$highestColumn.'1', null, true, true, false)[0] ?? [];
        $headers = array_map(fn ($v) => is_string($v) ? trim($v) : (string)$v, $headerRow);

        $rows = [];
        for ($r = 2; $r <= $highestRow; $r++) {
            $rowValues = $sheet->rangeToArray('A'.$r.':'.$highestColumn.$r, null, true, true, false)[0] ?? [];
            $assoc = [];
            foreach ($headers as $i => $label) {
                $assoc[$label] = $rowValues[$i] ?? null;
            }
            $rows[] = $assoc;
        }

        // Suggest initial mapping by simple label matching
        $suggestions = $this->suggestMapping($headers);

        return Inertia::render('Assets/Import', [
            'preview' => [
                'headers' => $headers,
                'sample' => $rows,
            ],
            'token' => $token,
            'suggestedMapping' => $suggestions,
        ]);
    }

    protected function suggestMapping(array $headers): array
    {
        $targets = [
            'asset_tag' => ['asset tag id', 'asset tag', 'asset id', 'tag'],
            'description' => ['description', 'asset description', 'name'],
            'purchase_date' => ['purchase date', 'purchased on', 'purchased at', 'date purchased'],
            'cost' => ['cost', 'price', 'cost minor', 'unit cost'],
            'currency' => ['currency'],
            'status' => ['status', 'state'],
            'purchased_from' => ['purchased from', 'vendor', 'supplier'],
            'serial_no' => ['serial no', 'serial', 'serial number'],
            'site' => ['site'],
            'location' => ['location'],
            'category' => ['category'],
            'department' => ['department'],
            'assigned_to' => ['assigned to', 'assignee', 'user', 'employee'],
            'project_code' => ['project code', 'project'],
            'asset_photo' => ['asset photo', 'photo', 'image url'],
        ];

        $map = [];

        foreach ($headers as $label) {
            $norm = $this->normalize($label);
            $matched = '';
            foreach ($targets as $target => $syns) {
                foreach ($syns as $syn) {
                    if ($norm === $this->normalize($syn)) {
                        $matched = $target; break 2;
                    }
                }
            }
            if ($matched !== '') {
                $map[$label] = $matched;
            }
        }

        return $map;
    }

    protected function normalize(string $v): string
    {
        $v = strtolower($v);
        $v = preg_replace('/[^a-z0-9]+/i', '_', $v) ?? '';
        return trim($v, '_');
    }
}

