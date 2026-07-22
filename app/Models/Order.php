<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_code',
        'full_name',
        'email',
        'phone',
        'address',
        'note',
        'shipping_fee',
        'total_price',
        'status',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'shipping_fee' => 'integer',
            'total_price' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ xác nhận',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
