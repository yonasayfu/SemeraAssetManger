<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Column labels available for export in default order.
     * Keys are user-facing labels; values are callbacks extracting the value from Asset.
     */
    protected array $columnMap;

    /** @var string[] */
    protected array $selectedLabels;

    public function __construct(array $selectedLabels = [])
    {
        $this->columnMap = [
            'Asset Photo' => function (Asset $a) {
                $path = $a->photo;
                if (!$path) return null;
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }
                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                // Make absolute if returned URL is relative
                if (str_starts_with($url, '/')) {
                    $base = rtrim(config('app.url'), '/');
                    return $base.$url;
                }
                return $url;
            },
            'Asset Tag ID' => fn (Asset $a) => $a->asset_tag,
            'Description' => fn (Asset $a) => $a->description,
            'Purchase Date' => fn (Asset $a) => optional($a->purchase_date)->toDateString(),
            'Cost' => fn (Asset $a) => $a->cost,
            'Status' => fn (Asset $a) => $a->status,
            'Purchased from' => fn (Asset $a) => $a->purchased_from,
            'Serial No' => fn (Asset $a) => $a->serial_no,
            'Site' => fn (Asset $a) => optional($a->site)->name,
            'Location' => fn (Asset $a) => optional($a->location)->name,
            'Category' => fn (Asset $a) => optional($a->category)->name,
            'Department' => fn (Asset $a) => optional($a->department)->name,
            'Assigned to' => fn (Asset $a) => optional($a->assignee)->name,
            'Project code' => fn (Asset $a) => $a->project_code,
        ];

        // Sanitize the selected labels against the map; default to all
        $labels = array_keys($this->columnMap);
        $this->selectedLabels = array_values(
            count($selectedLabels) > 0
                ? array_intersect($labels, $selectedLabels)
                : $labels
        );
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Asset::with(['site:id,name', 'location:id,name', 'category:id,name', 'department:id,name', 'assignee:id,name'])->get();
    }

    public function headings(): array
    {
        // Use selected labels in the order provided
        return $this->selectedLabels;
    }

    public function map($asset): array
    {
        // Build the row according to selected labels
        $row = [];
        foreach ($this->selectedLabels as $label) {
            $callback = $this->columnMap[$label] ?? null;
            $row[] = $callback ? $callback($asset) : null;
        }

        return $row;
    }
}
