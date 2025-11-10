<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetImportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id','token','file_path','mapping','options','status','total_rows','processed_rows','failures','message','report_path','started_at','finished_at'
    ];

    protected $casts = [
        'mapping' => 'array',
        'options' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'cancelled' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
