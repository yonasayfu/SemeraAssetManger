<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\Site;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find related models or create if not exists
        $site = Site::firstOrCreate(['name' => $row['site_name']]);
        $location = Location::firstOrCreate(['name' => $row['location_name']]);
        $category = Category::firstOrCreate(['name' => $row['category_name']]);
        $department = Department::firstOrCreate(['name' => $row['department_name']]);

        return new Asset([
            'asset_tag' => $row['asset_tag'],
            'name' => $row['name'],
            'status' => $row['status'] ?? Asset::STATUS_DEPLOYED,
            'site_id' => $site->id,
            'location_id' => $location->id,
            'category_id' => $category->id,
            'department_id' => $department->id,
            'purchase_date' => $row['purchase_date'] ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['purchase_date']) : null,
            'purchase_price' => $row['purchase_price'] ?? 0,
            'warranty_months' => $row['warranty_months'] ?? 0,
        ]);
    }
}
