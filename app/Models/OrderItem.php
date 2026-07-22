<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_size_id',
        'product_color_id',
        'product_name',
        'product_price',
        'quantity',
    ];

    protected function casts(): array
    {
        return ['product_price' => 'integer'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }

    public function color()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->product_price * $this->quantity;
    }
}
