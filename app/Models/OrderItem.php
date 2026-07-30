<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_size_id',   // giữ lại cột cũ trong lúc chuyển tiếp, chưa xóa DB
        'product_color_id',  // giữ lại cột cũ trong lúc chuyển tiếp, chưa xóa DB
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

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Giữ tạm 2 quan hệ cũ (phòng khi còn đơn hàng cũ chưa có product_variant_id)
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