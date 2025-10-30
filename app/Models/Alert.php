<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'asset_id',
        'source_id',
        'source_type',
        'message',
        'due_date',
        'sent',
        'sent_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'sent' => 'boolean',
        'sent_at' => 'datetime',
    ];
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->morphTo();
    }
}
