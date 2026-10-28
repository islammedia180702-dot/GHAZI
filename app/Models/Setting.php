<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_address',
        'latitude',
        'longitude',
        'whatsapp',
        'operational_hours',
        'denda_per_hari',
        'pending_expiry_minutes',
        'free_delivery_km',
        'fee_per_km',
        'max_delivery_km',
        'terms',
        'privacy_policy',
    ];
}
