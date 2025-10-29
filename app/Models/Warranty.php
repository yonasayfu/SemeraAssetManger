<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\RecordsActivity;

class Warranty extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'asset_id',
        'description',
        'provider',
        'length_months',
        'start_date',
        'expiry_date',
        'active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'active' => 'boolean',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
