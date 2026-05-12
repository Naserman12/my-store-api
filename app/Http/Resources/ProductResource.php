<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,

            'price' => $this->price,
            'sale_price' => $this->sale_price,

            'quantity' => $this->quantity,

            'category' => $this->category?->name,

             // 👇 عرض كل الصور
            'images' => $this->images->map(fn($img) => [
                'id' => $img->id,
                'image_url' => $img->image_url,
                'public_id' => $img->public_id,
                'is_primary' => $img->is_primary,
            ]),

            'is_featured' => $this->is_featured,

            'created_at' => $this->created_at,
        ];
    }
}