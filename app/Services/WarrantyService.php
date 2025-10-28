<?php

namespace App\Services;

use App\Models\Warranty;
use Illuminate\Support\Carbon;

class WarrantyService
{
    public function create(array $attributes): Warranty
    {
        $prepared = $this->prepareAttributes($attributes);

        $warranty = Warranty::create($prepared);

        $this->syncActiveState($warranty);

        return $warranty;
    }

    public function update(Warranty $warranty, array $attributes): Warranty
    {
        $prepared = $this->prepareAttributes($attributes);

        $warranty->fill($prepared);
        $warranty->save();

        $this->syncActiveState($warranty);

        return $warranty;
    }

    public function deactivateExpiredWarranties(): int
    {
        return Warranty::query()
            ->where('active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->update(['active' => false]);
    }

    protected function syncActiveState(Warranty $warranty): void
    {
        if (! $warranty->expiry_date) {
            $warranty->forceFill([
                'active' => true,
            ])->save();

            return;
        }

        $expiry = Carbon::parse($warranty->expiry_date);

        $warranty->forceFill([
            'active' => $expiry->isFuture(),
        ])->save();
    }

    protected function prepareAttributes(array $attributes): array
    {
        if (
            empty($attributes['expiry_date'])
            && ! empty($attributes['start_date'])
            && ! empty($attributes['length_months'])
        ) {
            $attributes['expiry_date'] = Carbon::parse($attributes['start_date'])
                ->addMonths((int) $attributes['length_months'])
                ->toDateString();
        }

        if (! array_key_exists('active', $attributes)) {
            $attributes['active'] = true;
        }

        return $attributes;
    }
}
