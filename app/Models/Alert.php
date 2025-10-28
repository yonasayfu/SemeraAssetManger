<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'asset_id',
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
