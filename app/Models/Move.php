<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'from_location_id',
        'to_location_id',
        'moved_by',
        'moved_at',
        'reason',
    ];

    protected $casts = [
        'moved_at' => 'datetime',
    ];
}
