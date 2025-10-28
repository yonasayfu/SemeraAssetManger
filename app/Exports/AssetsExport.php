<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Asset::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asset Tag',
            'Description',
            'Purchase Date',
            'Cost',
            'Currency',
            'Purchased From',
            'Brand',
            'Model',
            'Serial No',
            'Project Code',
            'Asset Condition',
            'Site ID',
            'Location ID',
            'Category ID',
            'Department ID',
            'Assigned To',
            'Status',
            'Photo',
            'Created By',
            'Created At',
            'Updated At',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->id,
            $asset->asset_tag,
            $asset->description,
            $asset->purchase_date,
            $asset->cost,
            $asset->currency,
            $asset->purchased_from,
            $asset->brand,
            $asset->model,
            $asset->serial_no,
            $asset->project_code,
            $asset->asset_condition,
            $asset->site_id,
            $asset->location_id,
            $asset->category_id,
            $asset->department_id,
            $asset->assigned_to,
            $asset->status,
            $asset->photo,
            $asset->created_by,
            $asset->created_at,
            $asset->updated_at,
        ];
    }
}
