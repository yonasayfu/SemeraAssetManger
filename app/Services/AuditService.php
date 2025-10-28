<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Audit;
use App\Models\AuditAsset;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AuditService
{
    public const STATUS_DRAFT = 'Draft';
    public const STATUS_ONGOING = 'Ongoing';
    public const STATUS_COMPLETED = 'Completed';

    /**
     * Create an audit and seed the initial asset checklist.
     *
     * @param  array{
     *     name:string,
     *     site_id:int,
     *     location_id?:int|null,
     *     category_ids?:array<int>,
     *     asset_ids?:array<int>
     * }  $payload
     */
    public function start(array $payload): Audit
    {
        return DB::transaction(function () use ($payload) {
            $audit = Audit::create([
                'name' => $payload['name'],
                'site_id' => $payload['site_id'],
                'location_id' => $payload['location_id'] ?? null,
                'status' => self::STATUS_ONGOING,
                'started_at' => now(),
            ]);

            $assets = $this->resolveAssetsForAudit($audit, $payload);

            if ($assets->isEmpty()) {
                $audit->forceFill(['status' => self::STATUS_DRAFT])->save();

                return $audit->fresh(['site:id,name', 'location:id,name']);
            }

            $audit->auditAssets()->createMany(
                $assets->map(fn (Asset $asset) => [
                    'asset_id' => $asset->id,
                ])->all()
            );

            return $audit->fresh([
                'site:id,name',
                'location:id,name',
                'auditAssets.asset:id,asset_tag,description,status',
            ]);
        });
    }

    /**
     * Update the status of a single audit asset.
     *
     * @param  array{found?:bool,notes?:string|null}  $attributes
     */
    public function updateAuditAsset(AuditAsset $auditAsset, array $attributes): AuditAsset
    {
        $auditAsset->forceFill([
            'found' => (bool) ($attributes['found'] ?? false),
            'notes' => $attributes['notes'] ?? null,
            'checked_at' => now(),
        ])->save();

        return $auditAsset->refresh();
    }

    public function complete(Audit $audit): Audit
    {
        $audit->forceFill([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ])->save();

        return $audit->refresh(['auditAssets']);
    }

    public function summarise(Audit $audit): array
    {
        if ($audit->relationLoaded('auditAssets')) {
            $total = $audit->auditAssets->count();
            $found = $audit->auditAssets->where('found', true)->count();
        } else {
            $total = $audit->auditAssets()->count();
            $found = $audit->auditAssets()->where('found', true)->count();
        }

        return [
            'total' => $total,
            'found' => $found,
            'missing' => max($total - $found, 0),
        ];
    }

    public function varianceReport(Audit $audit): array
    {
        $audit->loadMissing([
            'auditAssets.asset' => function ($query) {
                $query->with(['category:id,name', 'site:id,name', 'location:id,name']);
            },
        ]);

        $found = $audit->auditAssets->filter(fn (AuditAsset $item) => (bool) $item->found);
        $missing = $audit->auditAssets->reject(fn (AuditAsset $item) => (bool) $item->found);

        return [
            'summary' => $this->summarise($audit),
            'found' => $this->mapAuditAssets($found),
            'missing' => $this->mapAuditAssets($missing),
            'extras' => [],
        ];
    }

    protected function mapAuditAssets(\Illuminate\Support\Collection $items): array
    {
        return $items
            ->values()
            ->map(function (AuditAsset $auditAsset) {
                $asset = $auditAsset->asset;

                return [
                    'id' => $auditAsset->id,
                    'asset_tag' => $asset?->asset_tag,
                    'description' => $asset?->description,
                    'status' => $asset?->status,
                    'category' => $asset?->category?->name,
                    'site' => $asset?->site?->name,
                    'location' => $asset?->location?->name,
                    'notes' => $auditAsset->notes,
                    'found' => (bool) $auditAsset->found,
                    'checked_at' => optional($auditAsset->checked_at)->toDateTimeString(),
                ];
            })
            ->all();
    }

    /**
     * Resolve the list of assets that should be part of the audit.
     *
     * @param  array{category_ids?:array<int>,asset_ids?:array<int>}  $payload
     */
    protected function resolveAssetsForAudit(Audit $audit, array $payload): Collection
    {
        $query = Asset::query()
            ->where('site_id', $audit->site_id)
            ->when(
                $audit->location_id,
                fn ($builder) => $builder->where('location_id', $audit->location_id)
            )
            ->when(
                ! empty($payload['category_ids']),
                fn ($builder) => $builder->whereIn('category_id', $payload['category_ids'])
            );

        $assetIds = $query->pluck('id');

        $manualIds = collect($payload['asset_ids'] ?? [])
            ->filter()
            ->map(fn ($value) => (int) $value);

        $ids = $assetIds->merge($manualIds)->unique()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Asset::query()
            ->whereIn('id', $ids)
            ->with([
                'category:id,name',
                'site:id,name',
                'location:id,name',
            ])
            ->get();
    }
}
