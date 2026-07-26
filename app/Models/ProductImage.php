<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image', 'sort_order'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://via.placeholder.com/400x400?text=No+Image';
        }
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        return asset('storage/' . $this->image);
    }
}
