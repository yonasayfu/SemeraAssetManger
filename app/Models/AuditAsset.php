<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_id',
        'asset_id',
        'found',
        'notes',
        'checked_at',
    ];

    protected $casts = [
        'found' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
