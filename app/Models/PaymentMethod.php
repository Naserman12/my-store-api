<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'logo',
        'is_active',
        'sort_order',
        'config'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean'
    ];
}