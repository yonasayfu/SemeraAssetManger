<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ToolsImportPreviewController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'entity' => ['required', 'string'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt'],
        ]);

        $uploaded = $request->file('file');
        $spreadsheet = IOFactory::load($uploaded->getRealPath());
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

        return Inertia::render('Tools/Import', [
            'entity' => $data['entity'],
            'preview' => [
                'headers' => $headers,
                'sample' => $rows,
            ],
            'suggestedMapping' => $this->suggestMapping($data['entity'], $headers),
        ]);
    }

    protected function suggestMapping(string $entity, array $headers): array
    {
        $norm = fn($s) => trim(preg_replace('/[^a-z0-9]+/i', '_', strtolower((string)$s)) ?? '', '_');

        $maps = [
            'staff' => [
                'name' => ['name','full_name'],
                'email' => ['email','work_email'],
                'phone' => ['phone','mobile'],
                'job_title' => ['job_title','title','position'],
                'account_type' => ['account_type','type','role'],
                'status' => ['status','account_status'],
            ],
            'sites' => [
                'name' => ['name','site'],
                'address' => ['address','street'],
                'city' => ['city','town'],
                'state' => ['state','province'],
                'postal_code' => ['postal_code','zip'],
                'country' => ['country'],
            ],
            'locations' => [
                'name' => ['name','location'],
                'site' => ['site','site_name'],
            ],
            'categories' => [ 'name' => ['name','category'] ],
            'departments' => [ 'name' => ['name','department'] ],
            'vendors' => [ 'name' => ['name','vendor','supplier'] ],
            'products' => [
                'name' => ['name','product'],
                'vendor' => ['vendor','supplier'],
                'warranty_months' => ['warranty_months','warranty'],
            ],
            'maintenances' => [
                'asset_tag' => ['asset_tag','asset'],
                'title' => ['title','summary'],
                'scheduled_for' => ['scheduled_for','schedule','date'],
            ],
            'warranties' => [
                'asset_tag' => ['asset_tag','asset'],
                'provider' => ['provider','vendor'],
                'expiry_date' => ['expiry_date','expires','end_date'],
            ],
            'contracts' => [ 'number' => ['number','contract_no','contract'] ],
            'purchase-orders' => [ 'number' => ['number','po_number','purchase_order'] ],
            'software' => [ 'name' => ['name','software','title'] ],
        ];

        $targets = $maps[$entity] ?? [];
        $result = [];
        foreach ($headers as $label) {
            $n = $norm($label);
            foreach ($targets as $target => $syns) {
                foreach ($syns as $syn) {
                    if ($n === $norm($syn)) { $result[$label] = $target; break 2; }
                }
            }
        }
        return $result;
    }
}

