<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Concerns\RecordsActivity;

class Asset extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'asset_tag',
        'description',
        'purchase_date',
        'cost',
        'currency',
        'purchased_from',
        'brand',
        'model',
        'serial_no',
        'project_code',
        'asset_condition',
        'vendor_id',
        'product_id',
        'purchase_order_item_id',
        'site_id',
        'location_id',
        'category_id',
        'department_id',
        'staff_id',
        'status',
        'photo',
        'custom_fields',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'custom_fields' => 'array',
    ];

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function upcomingMaintenance(): HasOne
    {
        return $this->hasOne(Maintenance::class)
            ->whereNull('completed_at')
            ->orderBy('scheduled_for');
    }

    public function warranties(): HasMany
    {
        return $this->hasMany(Warranty::class);
    }

    public function activeWarranty(): HasOne
    {
        return $this->hasOne(Warranty::class)
            ->where('active', true)
            ->orderBy('expiry_date');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AssetPhoto::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function auditAssets(): HasMany
    {
        return $this->hasMany(AuditAsset::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
            ->where('subject_type', static::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }
}
