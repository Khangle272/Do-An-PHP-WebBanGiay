<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'final_price' => $this->final_price,
            'sale_percentage' => $this->sale_percentage,
            'thumbnail_url' => $this->thumbnail_url,
            'avg_rating' => $this->avg_rating,
            'featured' => (bool) $this->featured,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($image) => [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'sort_order' => $image->sort_order,
                ]);
            }),
            'sizes' => $this->whenLoaded('sizes', function () {
                return $this->sizes->map(fn ($size) => [
                    'id' => $size->id,
                    'size' => $size->size,
                    'stock' => $size->stock,
                ]);
            }),
            'colors' => $this->whenLoaded('colors', function () {
                return $this->colors->map(fn ($color) => [
                    'id' => $color->id,
                    'color_name' => $color->color_name,
                    'color_code' => $color->color_code,
                    'stock' => $color->stock,
                ]);
            }),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}