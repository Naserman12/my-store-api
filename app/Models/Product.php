<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'quantity',
        'sku',
        'category_id',
        'is_featured',
        'is_hidden',

    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    /* relations */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function features()
    {
        return $this->hasMany(ProductFeature::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
