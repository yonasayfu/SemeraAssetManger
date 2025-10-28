<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'family',
        'definition_json',
        'schedule_cron',
        'last_run_at',
    ];

    protected $casts = [
        'definition_json' => 'array',
        'last_run_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
