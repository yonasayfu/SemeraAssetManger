<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\Site;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Validators\Failure;

class AssetsImport implements ToModel, WithHeadingRow, SkipsOnFailure, SkipsOnError, WithUpserts, WithUpsertColumns
{
    use SkipsFailures, SkipsErrors;

    /** @var array<string,string> mapping from incoming header key to internal field key */
    protected array $mapping = [];

    /** @var array<string,mixed> */
    protected array $options = [];

    protected bool $createMissingTaxonomy = true;

    public function __construct(array $mapping = [], array $options = [])
    {
        $this->mapping = $mapping;
        $this->options = $options;
        $this->createMissingTaxonomy = (bool)($options['create_missing_taxonomy'] ?? true);
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like. For now, we'll just store them.
        $this->failures = array_merge($this->failures, $failures);
    }

    public function uniqueBy()
    {
        // Upsert based on asset_tag to avoid duplicate constraint failures
        return 'asset_tag';
    }

    public function upsertColumns(): array
    {
        // Only update mutable columns, avoid changing creator and primary identifiers
        return [
            'description', 'purchase_date', 'cost', 'currency', 'purchased_from',
            'brand', 'model', 'serial_no', 'project_code', 'asset_condition',
            'site_id', 'location_id', 'category_id', 'department_id', 'staff_id',
            'status', 'photo', 'custom_fields', 'updated_at',
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        \Illuminate\Support\Facades\Log::info('AssetsImport model method hit for row:', $row);
        $row = $this->applyMapping($row);
        $r = $this->normalize($row);

        // Resolve foreign keys by name or id. Create taxonomy if missing (except staff).
        $siteId = $this->resolveSiteId(Arr::get($r, 'site_id'), Arr::get($r, 'site'));

        // If a location is provided but no site is resolved, create a default site if createMissingTaxonomy is enabled.
        if (is_null($siteId) && !empty(Arr::get($r, 'location')) && $this->createMissingTaxonomy) {
            $siteId = Site::firstOrCreate(
                ['name' => 'Default Site for Imports'],
                [
                    'description' => 'Automatically created for imported locations without a specified site.',
                    'address' => 'N/A',
                    'city' => 'N/A',
                    'state' => 'N/A',
                    'postal_code' => 'N/A',
                    'country' => 'N/A',
                ]
            )->id;
        }

        $locationId = $this->resolveLocationId(Arr::get($r, 'location_id'), Arr::get($r, 'location'), $siteId);
        $categoryId = $this->resolveCategoryId(Arr::get($r, 'category_id'), Arr::get($r, 'category'));
        $departmentId = $this->resolveDepartmentId(Arr::get($r, 'department_id'), Arr::get($r, 'department'));

        $staffId = $this->resolveStaffId(Arr::get($r, 'staff_id'), Arr::get($r, 'assigned_to'));

        $purchaseDate = $this->parseDate(Arr::get($r, 'purchase_date'));

        return new Asset([
            'asset_tag' => Arr::get($r, 'asset_tag'),
            'description' => Arr::get($r, 'description'),
            'purchase_date' => $purchaseDate,
            'cost' => $this->parseCost(Arr::get($r, 'cost')),
            'currency' => Arr::get($r, 'currency'),
            'purchased_from' => Arr::get($r, 'purchased_from'),
            'brand' => Arr::get($r, 'brand'),
            'model' => Arr::get($r, 'model'),
            'serial_no' => Arr::get($r, 'serial_no'),
            'project_code' => Arr::get($r, 'project_code'),
            'asset_condition' => Arr::get($r, 'asset_condition'),
            'site_id' => $siteId,
            'location_id' => $locationId,
            'category_id' => $categoryId,
            'department_id' => $departmentId,
            'staff_id' => $staffId,
            'status' => $this->normalizeStatus(Arr::get($r, 'status')),
            'photo' => Arr::get($r, 'photo') ?: Arr::get($r, 'asset_photo'),
            'created_by' => Auth::id(),
        ]);
    }

    protected function normalize(array $row): array
    {
        // Keys may be like "Asset Tag ID" or "Asset Tag"; HeadingRow converts to snake_cased versions.
        // Accept common variants from legacy exports.
        $data = [];

        $get = function (array $keys) use ($row) {
            foreach ($keys as $key) {
                if (array_key_exists($key, $row) && $row[$key] !== '') {
                    return $row[$key];
                }
            }
            return null;
        };

        $data['asset_tag'] = $get(['asset_tag', 'asset_tag_id', 'asset_id']);
        $data['description'] = $get(['description', 'asset_description']);
        $data['purchase_date'] = $get(['purchase_date', 'purchased_at']);
        $data['cost'] = $get(['cost', 'cost_minor']);
        $data['currency'] = $get(['currency']);
        $data['purchased_from'] = $get(['purchased_from', 'purchased_from_']); // legacy header may include trailing underscore
        $data['brand'] = $get(['brand']);
        $data['model'] = $get(['model']);
        $data['serial_no'] = $get(['serial_no', 'serial']);
        $data['project_code'] = $get(['project_code']);
        $data['asset_condition'] = $get(['asset_condition', 'condition']);
        $data['status'] = $get(['status']);
        $data['photo'] = $get(['photo']);
        $data['asset_photo'] = $get(['asset_photo']);

        // Relationships: accept either *_id (numeric) or name columns.
        $data['site_id'] = $get(['site_id']);
        $data['site'] = $get(['site']);

        $data['location_id'] = $get(['location_id']);
        $data['location'] = $get(['location']);

        $data['category_id'] = $get(['category_id']);
        $data['category'] = $get(['category']);

        $data['department_id'] = $get(['department_id']);
        $data['department'] = $get(['department']);

        $data['staff_id'] = $get(['staff_id', 'assigned_to_id']);
        $data['assigned_to'] = $get(['assigned_to']); // name or email

        return $data;
    }

    protected function resolveSiteId($id, $name): ?int
    {
        \Illuminate\Support\Facades\Log::info('Resolving Site: ', ['id' => $id, 'name' => $name]);
        if ($this->isValidId($id)) return (int) $id;
        $n = trim((string) $name);
        if ($n === '') return null;
        if ($this->createMissingTaxonomy) {
            // Sites table requires several non-null fields; create minimal placeholders.
            $site = Site::firstOrCreate(
                ['name' => $n],
                [
                    'description' => null,
                    'address' => '',
                    'suite' => null,
                    'city' => '',
                    'state' => '',
                    'postal_code' => '',
                    'country' => '',
                ]
            );
            \Illuminate\Support\Facades\Log::info('Site resolved/created: ', ['name' => $n, 'id' => $site->id]);
            return $site->id;
        }
        $site = Site::where('name', $n)->first();
        \Illuminate\Support\Facades\Log::info('Site resolved: ', ['name' => $n, 'id' => optional($site)->id]);
        return optional(Site::where('name', $n)->first())->id;
    }

    protected function resolveLocationId($id, $name, ?int $siteId): ?int
    {
        \Illuminate\Support\Facades\Log::info('Resolving Location: ', ['id' => $id, 'name' => $name, 'site_id' => $siteId]);
        if ($this->isValidId($id)) return (int) $id;
        $n = trim((string) $name);
        if ($n === '') return null;
        $query = Location::query()->where('name', $n);
        if ($siteId) $query->where('site_id', $siteId);
        $existing = $query->first();
        if ($existing) {
            \Illuminate\Support\Facades\Log::info('Location resolved: ', ['name' => $n, 'id' => $existing->id]);
            return $existing->id;
        }
        if (!$this->createMissingTaxonomy) return null;
        if (is_null($siteId)) {
            \Illuminate\Support\Facades\Log::warning('Cannot create location without a site_id: '.$n);
            return null;
        }
        $location = Location::create(['name' => $n, 'site_id' => $siteId]);
        \Illuminate\Support\Facades\Log::info('Location created: ', ['name' => $n, 'id' => $location->id, 'site_id' => $siteId]);
        return $location->id;
    }

    protected function resolveCategoryId($id, $name): ?int
    {
        \Illuminate\Support\Facades\Log::info('Resolving Category: ', ['id' => $id, 'name' => $name]);
        if ($this->isValidId($id)) return (int) $id;
        $n = trim((string) $name);
        if ($n === '') return null;
        if ($this->createMissingTaxonomy) {
            $category = Category::firstOrCreate(['name' => $n]);
            \Illuminate\Support\Facades\Log::info('Category resolved/created: ', ['name' => $n, 'id' => $category->id]);
            return $category->id;
        }
        $category = Category::where('name', $n)->first();
        \Illuminate\Support\Facades\Log::info('Category resolved: ', ['name' => $n, 'id' => optional($category)->id]);
        return optional(Category::where('name', $n)->first())->id;
    }

    protected function resolveDepartmentId($id, $name): ?int
    {
        \Illuminate\Support\Facades\Log::info('Resolving Department: ', ['id' => $id, 'name' => $name]);
        if ($this->isValidId($id)) return (int) $id;
        $n = trim((string) $name);
        if ($n === '') return null;
        if ($this->createMissingTaxonomy) {
            $department = Department::firstOrCreate(['name' => $n]);
            \Illuminate\Support\Facades\Log::info('Department resolved/created: ', ['name' => $n, 'id' => $department->id]);
            return $department->id;
        }
        $department = Department::where('name', $n)->first();
        \Illuminate\Support\Facades\Log::info('Department resolved: ', ['name' => $n, 'id' => optional($department)->id]);
        return optional(Department::where('name', $n)->first())->id;
    }

    protected function resolveStaffId($id, $value)
    {
        \Illuminate\Support\Facades\Log::info('Resolving Staff: ', ['id' => $id, 'value' => $value]);
        if ($this->isValidId($id)) return (int) $id;
        $v = trim((string) $value);
        if ($v === '') return null;
        // Try by email first, then by name. Do NOT create user automatically (password required).
        $byEmail = Staff::where('email', $v)->first();
        if ($byEmail) {
            \Illuminate\Support\Facades\Log::info('Staff resolved by email: ', ['email' => $v, 'id' => $byEmail->id]);
            return $byEmail->id;
        }
        $byName = Staff::where('name', $v)->first();
        \Illuminate\Support\Facades\Log::info('Staff resolved by name: ', ['name' => $v, 'id' => optional($byName)->id]);
        return $byName?->id;
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) return null;
        try {
            // Accept Excel date serials or string formats.
            if (is_numeric($value)) {
                return Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->toDateString();
            }
            return Carbon::parse((string) $value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function parseCost($value): ?float
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) return (float) $value;
        $v = (string) $value;
        // Strip currency symbols and thousand separators
        $v = preg_replace('/[^0-9.,-]/', '', $v ?? '') ?? '';
        // If both comma and dot present, assume comma is thousands
        if (str_contains($v, ',') && str_contains($v, '.')) {
            $v = str_replace(',', '', $v);
        } else {
            // If only comma present, treat it as decimal separator
            if (str_contains($v, ',') && !str_contains($v, '.')) {
                $v = str_replace(',', '.', $v);
            }
        }
        // Remove any remaining thousand separators spaces
        $v = str_replace([' '], '', $v);
        if ($v === '' || $v === '.' || $v === ',') return null;
        return (float) $v;
    }

    protected function normalizeStatus($value): string
    {
        $default = 'Available';
        if (!$value) return $default;
        $map = [
            'available' => 'Available',
            'checked out' => 'Checked Out',
            'under repair' => 'Under Repair',
            'leased' => 'Leased',
            'disposed' => 'Disposed',
            'lost' => 'Lost',
            'donated' => 'Donated',
            'sold' => 'Sold',
        ];
        $k = strtolower(trim((string) $value));
        return $map[$k] ?? $default;
    }

    protected function isValidId($value): bool
    {
        return is_numeric($value) && (int) $value > 0;
    }

    /**
     * Apply user-provided mapping to row keys.
     * Accepts mapping keys in any case/format by normalizing both sides.
     */
    protected function applyMapping(array $row): array
    {
        if (empty($this->mapping)) {
            return $row;
        }

        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $normalizedRow[$this->normalizeHeading($key)] = $value;
        }

        foreach ($this->mapping as $from => $to) {
            $fromKey = $this->normalizeHeading($from);
            $toKey = $this->normalizeHeading($to);
            if (!array_key_exists($toKey, $normalizedRow) && array_key_exists($fromKey, $normalizedRow)) {
                $normalizedRow[$toKey] = $normalizedRow[$fromKey];
            }
        }

        return $normalizedRow;
    }

    protected function normalizeHeading(string $key): string
    {
        // Convert to snake-like: lowercase, non-alnum to underscores, collapse repeats
        $k = strtolower($key);
        $k = preg_replace('/[^a-z0-9]+/i', '_', $k ?? '') ?? '';
        $k = trim($k, '_');
        return $k;
    }
}
