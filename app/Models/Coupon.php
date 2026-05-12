<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'expires_at',
        'usage_limit'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];
}