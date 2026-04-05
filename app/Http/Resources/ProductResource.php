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

            'image' => $this->images
                ->where('is_primary', true)
                ->first()?->image_url,

            'is_featured' => $this->is_featured,

            'created_at' => $this->created_at,
        ];
    }
}