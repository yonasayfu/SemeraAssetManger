<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'title',
        'description',
        'maintenance_type',
        'scheduled_for',
        'completed_at',
        'status',
        'cost',
        'labor_cost',
        'parts_cost',
        'vendor',
        'is_recurring',
        'recurrence_frequency',
        'recurrence_interval',
        'next_scheduled_for',
        'last_generated_at',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'completed_at' => 'datetime',
        'next_scheduled_for' => 'datetime',
        'last_generated_at' => 'datetime',
        'is_recurring' => 'boolean',
        'cost' => 'float',
        'labor_cost' => 'float',
        'parts_cost' => 'float',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
