<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'assignee_type',
        'assignee_id',
        'due_at',
        'checked_out_at',
        'checked_in_at',
        'condition_out_id',
        'condition_in_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'due_at' => 'date',
        'checked_out_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo('assignee', 'assignee_type', 'assignee_id');
    }
}
