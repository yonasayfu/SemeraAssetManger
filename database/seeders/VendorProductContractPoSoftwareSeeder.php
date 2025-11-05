<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Contract;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Software;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VendorProductContractPoSoftwareSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = collect([
            ['name' => 'Acme Supplies', 'email' => 'sales@acme.test', 'phone' => '+1-555-1001', 'website' => 'https://acme.test'],
            ['name' => 'Globex Tools', 'email' => 'hello@globex.test', 'phone' => '+1-555-1002', 'website' => 'https://globex.test'],
            ['name' => 'Initech', 'email' => 'support@initech.test', 'phone' => '+1-555-1003', 'website' => 'https://initech.test'],
        ])->map(fn ($v) => Vendor::firstOrCreate(['name' => $v['name']], $v));

        $usd = 'USD';

        $products = collect([
            ['vendor' => 'Acme Supplies', 'name' => 'AC-UPS Pro 1500', 'sku' => 'AC-UPS-1500', 'warranty_months' => 24, 'unit_cost_minor' => 35000, 'currency' => $usd],
            ['vendor' => 'Acme Supplies', 'name' => 'AC-Laptop Dock', 'sku' => 'AC-DOCK-01', 'warranty_months' => 12, 'unit_cost_minor' => 12000, 'currency' => $usd],
            ['vendor' => 'Globex Tools',  'name' => 'GX-Scanner', 'sku' => 'GX-SCN-200', 'warranty_months' => 12, 'unit_cost_minor' => 22000, 'currency' => $usd],
            ['vendor' => 'Initech',       'name' => 'IT-Monitor 27"', 'sku' => 'IT-MON-27', 'warranty_months' => 36, 'unit_cost_minor' => 28000, 'currency' => $usd],
        ])->map(function ($p) use ($vendors) {
            $vendor = $vendors->firstWhere('name', $p['vendor']);
            return Product::firstOrCreate([
                'vendor_id' => $vendor->id,
                'name' => $p['name'],
            ], [
                'sku' => $p['sku'],
                'warranty_months' => $p['warranty_months'],
                'unit_cost_minor' => $p['unit_cost_minor'],
                'currency' => $p['currency'],
            ]);
        });

        // Link a few assets to vendor/product for realism
        $assets = Asset::query()->take(6)->get();
        foreach ($assets as $i => $asset) {
            $p = $products[$i % $products->count()];
            $asset->vendor_id = $p->vendor_id;
            $asset->product_id = $p->id;
            $asset->save();
        }

        // Purchase Orders with items
        $acme = $vendors->firstWhere('name', 'Acme Supplies');
        $globex = $vendors->firstWhere('name', 'Globex Tools');

        $po1 = PurchaseOrder::firstOrCreate([
            'number' => 'PO-1001',
        ], [
            'name' => 'Office UPS and Docks',
            'vendor_id' => $acme->id,
            'expected_delivery_at' => Carbon::now()->addDays(10),
            'status' => 'open',
            'total_minor' => 0,
            'currency' => $usd,
        ]);
        $po2 = PurchaseOrder::firstOrCreate([
            'number' => 'PO-1002',
        ], [
            'name' => 'Warehouse Scanners',
            'vendor_id' => $globex->id,
            'expected_delivery_at' => Carbon::now()->addDays(3),
            'status' => 'open',
            'total_minor' => 0,
            'currency' => $usd,
        ]);

        $pUps = $products->firstWhere('name', 'AC-UPS Pro 1500');
        $pDock = $products->firstWhere('name', 'AC-Laptop Dock');
        $pScn = $products->firstWhere('name', 'GX-Scanner');

        $items = [
            [$po1, $pUps, 2],
            [$po1, $pDock, 4],
            [$po2, $pScn, 5],
        ];
        foreach ($items as [$po, $product, $qty]) {
            PurchaseOrderItem::firstOrCreate([
                'purchase_order_id' => $po->id,
                'product_id' => $product->id,
            ], [
                'qty' => $qty,
                'received_qty' => 0,
                'unit_cost_minor' => $product->unit_cost_minor,
                'currency' => $product->currency,
            ]);
        }

        // Recalculate PO totals
        foreach ([$po1, $po2] as $po) {
            $sum = $po->items()->get()->sum(fn ($i) => ($i->unit_cost_minor ?? 0) * max(1, ($i->qty ?? 1)));
            $po->total_minor = $sum;
            $po->save();
        }

        // Contracts across types
        $types = ['lease', 'maintenance', 'license', 'warranty'];
        foreach ($assets->take(4) as $i => $asset) {
            $product = $products[$i % $products->count()];
            Contract::firstOrCreate([
                'type' => $types[$i],
                'asset_id' => $asset->id,
            ], [
                'vendor_id' => $product->vendor_id,
                'product_id' => $product->id,
                'start_at' => Carbon::now()->subMonths(6),
                'end_at' => Carbon::now()->addMonths( ($i % 2) ? 2 : 8 ),
                'amount_minor' => $product->unit_cost_minor ? $product->unit_cost_minor * (($i % 2) ? 6 : 12) : null,
                'currency' => $product->currency,
                'status' => 'active',
            ]);
        }

        // Software entries
        $soft = [
            ['vendor' => 'Initech', 'name' => 'Initech CRM', 'type' => 'saas', 'seats_total' => 50, 'seats_used' => 42, 'status' => 'active'],
            ['vendor' => 'Globex Tools', 'name' => 'Globex BI', 'type' => 'saas', 'seats_total' => 20, 'seats_used' => 18, 'status' => 'active'],
            ['vendor' => 'Acme Supplies', 'name' => 'Acme CAD', 'type' => 'on-prem', 'seats_total' => 10, 'seats_used' => 9, 'status' => 'active'],
        ];
        foreach ($soft as $s) {
            $vendor = $vendors->firstWhere('name', $s['vendor']);
            Software::firstOrCreate([
                'vendor_id' => $vendor->id,
                'name' => $s['name'],
            ], [
                'type' => $s['type'],
                'seats_total' => $s['seats_total'],
                'seats_used' => $s['seats_used'],
                'status' => $s['status'],
            ]);
        }
    }
}
