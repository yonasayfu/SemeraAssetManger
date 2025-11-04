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
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $r = $this->normalize($row);

        // Resolve foreign keys by name or id. Create taxonomy if missing (except staff).
        $siteId = $this->resolveSiteId(Arr::get($r, 'site_id'), Arr::get($r, 'site'));
        $locationId = $this->resolveLocationId(Arr::get($r, 'location_id'), Arr::get($r, 'location'), $siteId);
        $categoryId = $this->resolveCategoryId(Arr::get($r, 'category_id'), Arr::get($r, 'category'));
        $departmentId = $this->resolveDepartmentId(Arr::get($r, 'department_id'), Arr::get($r, 'department'));

        $staffId = $this->resolveStaffId(Arr::get($r, 'staff_id'), Arr::get($r, 'assigned_to'));

        $purchaseDate = $this->parseDate(Arr::get($r, 'purchase_date'));

        return new Asset([
            'asset_tag' => Arr::get($r, 'asset_tag'),
            'description' => Arr::get($r, 'description'),
            'purchase_date' => $purchaseDate,
            'cost' => Arr::get($r, 'cost'),
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
            'status' => Arr::get($r, 'status') ?: 'Available',
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
        if ($this->isValidId($id)) return (int) $id;
        $n = trim((string) $name);
        if ($n === '') return null;
        return Site::firstOrCreate(['name' => $n])->id;
    }

    protected function resolveLocationId($id, $name, ?int $siteId): ?int
    {
        if ($this->isValidId($id)) return (int) $id;
        $n = trim((string) $name);
        if ($n === '') return null;
        $query = Location::query()->where('name', $n);
        if ($siteId) $query->where('site_id', $siteId);
        $existing = $query->first();
        if ($existing) return $existing->id;
        return Location::create(['name' => $n, 'site_id' => $siteId])->id;
    }

    protected function resolveCategoryId($id, $name): ?int
    {
        if ($this->isValidId($id)) return (int) $id;
        $n = trim((string) $name);
        if ($n === '') return null;
        return Category::firstOrCreate(['name' => $n])->id;
    }

    protected function resolveDepartmentId($id, $name): ?int
    {
        if ($this->isValidId($id)) return (int) $id;
        $n = trim((string) $name);
        if ($n === '') return null;
        return Department::firstOrCreate(['name' => $n])->id;
    }

    protected function resolveStaffId($id, $value)
    {
        if ($this->isValidId($id)) return (int) $id;
        $v = trim((string) $value);
        if ($v === '') return null;
        // Try by email first, then by name. Do NOT create user automatically (password required).
        $byEmail = Staff::where('email', $v)->first();
        if ($byEmail) return $byEmail->id;
        $byName = Staff::where('name', $v)->first();
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

    protected function isValidId($value): bool
    {
        return is_numeric($value) && (int) $value > 0;
    }
}
