<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'product' => [
                'id' => $this->product?->id,
                'name' => $this->product?->name,
                'slug' => $this->product?->slug,
                'thumbnail_url' => $this->product?->thumbnail_url,
                'final_price' => $this->product?->final_price,
            ],
            'size' => $this->size ? [
                'id' => $this->size->id,
                'size' => $this->size->size,
            ] : null,
            'color' => $this->color ? [
                'id' => $this->color->id,
                'color_name' => $this->color->color_name,
                'color_code' => $this->color->color_code,
            ] : null,
        ];
    }
}