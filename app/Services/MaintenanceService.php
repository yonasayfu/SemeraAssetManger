<?php

namespace App\Services;

use App\Models\Maintenance;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class MaintenanceService
{
    public function create(array $attributes): Maintenance
    {
        $maintenance = Maintenance::create($attributes);

        $this->synchroniseRecurringSettings($maintenance);

        return $maintenance;
    }

    public function update(Maintenance $maintenance, array $attributes): Maintenance
    {
        $maintenance->fill($attributes);
        $maintenance->save();

        $this->synchroniseRecurringSettings($maintenance);

        return $maintenance;
    }

    public function generateNextOccurrence(Maintenance $maintenance): ?Maintenance
    {
        if (! $maintenance->is_recurring || ! $maintenance->next_scheduled_for) {
            return null;
        }

        $nextDate = Carbon::parse($maintenance->next_scheduled_for);

        if ($nextDate->greaterThan(now())) {
            return null;
        }

        $attributes = Arr::only(
            $maintenance->getAttributes(),
            [
                'asset_id',
                'title',
                'description',
                'maintenance_type',
                'vendor',
                'cost',
                'labor_cost',
                'parts_cost',
                'is_recurring',
                'recurrence_frequency',
                'recurrence_interval',
            ]
        );

        $clone = Maintenance::create(array_merge($attributes, [
            'status' => 'Scheduled',
            'scheduled_for' => $nextDate,
            'completed_at' => null,
        ]));

        $maintenance->forceFill([
            'last_generated_at' => now(),
            'next_scheduled_for' => $this->calculateNextOccurrence($maintenance, $nextDate),
        ])->save();

        $this->synchroniseRecurringSettings($clone);

        return $clone;
    }

    protected function synchroniseRecurringSettings(Maintenance $maintenance): void
    {
        if (! $maintenance->is_recurring) {
            $maintenance->forceFill([
                'next_scheduled_for' => null,
                'last_generated_at' => null,
                'recurrence_frequency' => null,
                'recurrence_interval' => null,
            ])->save();

            return;
        }

        $scheduledFor = $maintenance->scheduled_for
            ? Carbon::parse($maintenance->scheduled_for)
            : now();

        $next = $this->calculateNextOccurrence($maintenance, $scheduledFor);

        $maintenance->forceFill([
            'next_scheduled_for' => $next,
        ])->save();
    }

    protected function calculateNextOccurrence(Maintenance $maintenance, Carbon $reference): ?Carbon
    {
        $frequency = $maintenance->recurrence_frequency ?: 'monthly';
        $interval = max((int) ($maintenance->recurrence_interval ?? 1), 1);

        $next = $reference->copy();

        switch ($frequency) {
            case 'daily':
                $next->addDays($interval);
                break;
            case 'weekly':
                $next->addWeeks($interval);
                break;
            case 'yearly':
                $next->addYears($interval);
                break;
            case 'monthly':
            default:
                $next->addMonths($interval);
                break;
        }

        return $next;
    }
}
