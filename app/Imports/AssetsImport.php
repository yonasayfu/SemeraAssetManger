<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class AssetsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Asset([
            'asset_tag' => $row['asset_tag'],
            'description' => $row['description'],
            'purchase_date' => $row['purchase_date'],
            'cost' => $row['cost'],
            'currency' => $row['currency'],
            'purchased_from' => $row['purchased_from'],
            'brand' => $row['brand'],
            'model' => $row['model'],
            'serial_no' => $row['serial_no'],
            'project_code' => $row['project_code'],
            'asset_condition' => $row['asset_condition'],
            'site_id' => $row['site_id'],
            'location_id' => $row['location_id'],
            'category_id' => $row['category_id'],
            'department_id' => $row['department_id'],
            'assigned_to' => $row['assigned_to'],
            'status' => $row['status'],
            'photo' => $row['photo'],
            'created_by' => Auth::id(),
        ]);
    }
}
