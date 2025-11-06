<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClearanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'clearance_id', 'asset_id', 'description', 'action', 'result', 'condition_note', 'checked', 'resolved_at',
    ];

    protected $casts = [
        'checked' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function clearance(): BelongsTo
    {
        return $this->belongsTo(Clearance::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}

