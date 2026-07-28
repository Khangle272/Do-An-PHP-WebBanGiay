<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public Order $order;

    /**
     * Nhận đơn hàng vừa tạo
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Kênh admin lắng nghe
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.orders'),
        ];
    }

    /**
     * Tên event phía JS nhận
     */
    public function broadcastAs(): string
    {
        return 'order.created';
    }
}