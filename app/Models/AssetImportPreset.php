<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetImportPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'name', 'mapping', 'options',
    ];

    protected $casts = [
        'mapping' => 'array',
        'options' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}

