<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'pickup_method',
        'start_date',
        'end_date',
        'subtotal',
        'delivery_fee',
        'delivery_distance_km',
        'delivery_rt_rw',
        'delivery_house_number',
        'delivery_latitude',
        'delivery_longitude',
        'grand_total',
        'late_days',
        'late_fee_total',
        'notes',
        'returned_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'returned_at' => 'datetime',
        'delivery_latitude' => 'decimal:7',
        'delivery_longitude' => 'decimal:7',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function hasExpiredPayment(): bool
    {
        return $this->payment?->status === 'pending'
            && $this->payment->expires_at?->lte(now()) === true;
    }

    public function isBulkDeletable(): bool
    {
        return in_array($this->status, ['completed', 'returned', 'cancelled'], true)
            || ($this->status === 'pending' && $this->hasExpiredPayment());
    }
}
