<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
    protected $fillable = [
        'product_id',
        'feature',
        'sort_order'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}