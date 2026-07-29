<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color_name',
        'color_code',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Nhãn hiển thị gộp, vd: "Size 39 - Đen"
    public function getLabelAttribute(): string
    {
        $parts = [];
        if ($this->size) {
            $parts[] = 'Size ' . $this->size;
        }
        if ($this->color_name) {
            $parts[] = $this->color_name;
        }
        return implode(' - ', $parts) ?: '—';
    }
}
