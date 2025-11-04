<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Staff;
use Illuminate\Support\Str;

class DataExport extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'type',
        'format',
        'status',
        'file_path',
        'record_count',
        'filters',
        'completed_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (DataExport $export) {
            if (blank($export->uuid)) {
                $export->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        // In this application, Staff is the authenticatable model
        return $this->belongsTo(Staff::class, 'user_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
