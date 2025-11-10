<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\Site;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use App\Models\AssetImportPreset;

class AssetImportDryRunController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->authorize('create', \App\Models\Asset::class);

        $data = $request->validate([
            'token' => ['required', 'string'],
            'mapping' => ['required', 'array'],
            'options' => ['nullable', 'string'],
        ]);

        $options = isset($data['options']) ? json_decode($data['options'], true) : [];
        $createMissing = (bool)($options['create_missing_taxonomy'] ?? false);
        $updateExisting = (bool)($options['update_existing_by_tag'] ?? true);
        $autoGenerateTags = (bool)($options['auto_generate_tags'] ?? false);
        $tagPrefix = (string)($options['tag_prefix'] ?? 'AST-');
        $downloadPhotos = (bool)($options['download_photos'] ?? false);
        $token = (string) $data['token'];
        $path = $this->resolveTokenPath($token);
        if (!$path) {
            return back()
                ->with('flash.banner', 'Import file not found for the provided token. Please re-upload your file and try again.')
                ->with('flash.bannerStyle', 'danger');
        }

        $absolute = Storage::disk('local')->path($path);
        $spreadsheet = IOFactory::load($absolute);
        $sheet = $spreadsheet->getSheet(0);
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        $headerRow = $sheet->rangeToArray('A1:'.$highestColumn.'1', null, true, true, false)[0] ?? [];
        $headers = array_map(fn ($v) => is_string($v) ? trim($v) : (string)$v, $headerRow);

        $norm = fn ($s) => trim(preg_replace('/[^a-z0-9]+/i', '_', strtolower((string)$s)) ?? '', '_');

        $mapping = [];
        foreach ($data['mapping'] as $from => $to) {
            if ($to !== null && $to !== '') {
                $mapping[$norm($from)] = (string)$to; // map normalized header -> target field
            }
        }

        $results = [
            'total' => 0,
            'ready' => 0,
            'errors' => 0,
            'warnings' => 0,
            'rows' => [],
        ];

        $allowedStatuses = [
            'available','checked out','under repair','leased','disposed','lost','donated','sold'
        ];

        for ($r = 2; $r <= $highestRow; $r++) {
            $rowValues = $sheet->rangeToArray('A'.$r.':'.$highestColumn.$r, null, true, true, false)[0] ?? [];
            $assoc = [];
            foreach ($headers as $i => $label) {
                $assoc[$norm($label)] = $rowValues[$i] ?? null;
            }

            // Apply mapping -> normalized payload
            $payload = [];
            foreach ($mapping as $from => $to) {
                $payload[$to] = $assoc[$from] ?? null;
            }

            $rowErrors = [];
            $rowWarnings = [];

            if (empty($payload['asset_tag'])) {
                if ($autoGenerateTags) {
                    $rowWarnings[] = 'Missing Asset Tag -> will be auto-generated (prefix: '.$tagPrefix.')';
                } else {
                    $rowErrors[] = 'Missing Asset Tag';
                }
            }

            // Status
            if (!empty($payload['status'])) {
                if (!in_array(strtolower((string)$payload['status']), $allowedStatuses, true)) {
                    $rowWarnings[] = 'Unknown status: '.(string)$payload['status'];
                }
            }

            // Date
            if (!empty($payload['purchase_date'])) {
                if (!$this->parseDateSafe($payload['purchase_date'])) {
                    $rowWarnings[] = 'Unparseable purchase date: '.(string)$payload['purchase_date'];
                }
            }

            // Taxonomy checks (name-based)
            foreach ([
                'site' => Site::class,
                'location' => Location::class,
                'category' => Category::class,
                'department' => Department::class,
            ] as $key => $model) {
                if (!empty($payload[$key])) {
                    $exists = $model::where('name', (string)$payload[$key])->exists();
                    if (!$exists && !$createMissing) {
                        $rowErrors[] = ucfirst($key).' not found: '.(string)$payload[$key];
                    } elseif (!$exists && $createMissing) {
                        $rowWarnings[] = ucfirst($key).' would be created: '.(string)$payload[$key];
                    }
                }
            }

            // Staff
            if (!empty($payload['assigned_to'])) {
                $value = (string)$payload['assigned_to'];
                $staff = Staff::where('email', $value)->orWhere('name', $value)->first();
                if (!$staff) {
                    $rowWarnings[] = 'Assigned To not found: '.$value;
                }
            }

            // Photo download hint
            if (!empty($payload['asset_photo']) && $downloadPhotos) {
                $url = (string)$payload['asset_photo'];
                if (preg_match('/^https?:\/\//i', $url)) {
                    $rowWarnings[] = 'Photo would be downloaded from URL';
                }
            }

            $results['total']++;
            if (count($rowErrors) === 0) {
                $results['ready']++;
            } else {
                $results['errors'] += count($rowErrors);
            }
            $results['warnings'] += count($rowWarnings);

            if ($results['total'] <= 50) {
                $results['rows'][] = [
                    'row' => $r,
                    'errors' => $rowErrors,
                    'warnings' => $rowWarnings,
                ];
            }
        }

        $presets = AssetImportPreset::query()
            ->where('staff_id', $request->user()?->getKey())
            ->orderBy('name')
            ->get(['id','name','mapping','options']);

        return Inertia::render('Assets/Import', [
            'token' => $token,
            'dryRun' => $results,
            'preview' => [ 'headers' => $headers ],
            'suggestedMapping' => $data['mapping'], // persist chosen mapping on page reload
            'options' => [
                'create_missing_taxonomy' => $createMissing,
                'update_existing_by_tag' => $updateExisting,
                'auto_generate_tags' => $autoGenerateTags,
                'tag_prefix' => $tagPrefix,
                'download_photos' => $downloadPhotos,
            ],
            'presets' => $presets,
        ]);
    }

    protected function resolveTokenPath(string $token): ?string
    {
        $files = Storage::disk('local')->files('imports/tmp');
        foreach ($files as $file) {
            if (str_contains($file, $token.'.')) {
                return $file;
            }
        }
        return null;
    }

    protected function parseDateSafe($value): ?string
    {
        if ($value === null || $value === '') return null;
        try {
            if (is_numeric($value)) {
                return Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value))->toDateString();
            }
            return Carbon::parse((string)$value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
