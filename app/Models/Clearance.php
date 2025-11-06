<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'requested_by', 'status', 'submitted_at', 'approved_at', 'approved_by', 'hr_email', 'pdf_path', 'remarks', 'meta',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'meta' => 'array',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClearanceItem::class);
    }
}

