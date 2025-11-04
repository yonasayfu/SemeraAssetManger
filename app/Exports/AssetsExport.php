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
        return Asset::with(['site:id,name', 'location:id,name', 'category:id,name', 'department:id,name', 'assignee:id,name'])->get();
    }

    public function headings(): array
    {
        // Match legacy/default export headers to ease re-import
        return [
            'Asset Photo',
            'Asset Tag ID',
            'Description',
            'Purchase Date',
            'Cost',
            'Status',
            'Purchased from',
            'Serial No',
            'Site',
            'Location',
            'Category',
            'Department',
            'Assigned to',
            'Project code',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->photo,
            $asset->asset_tag,
            $asset->description,
            optional($asset->purchase_date)->toDateString(),
            $asset->cost,
            $asset->status,
            $asset->purchased_from,
            $asset->serial_no,
            $asset->site?->name,
            $asset->location?->name,
            $asset->category?->name,
            $asset->department?->name,
            $asset->assignee?->name,
            $asset->project_code,
        ];
    }
}
