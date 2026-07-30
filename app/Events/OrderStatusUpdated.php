<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * [SOCKET] Bắn realtime riêng cho khách hàng đang xem trang chi tiết
 * đơn hàng của họ khi admin đổi trạng thái - không cần F5.
 * Dùng Private Channel để chỉ đúng chủ đơn hàng mới nhận được.
 */
class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.' . $this->order->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->order->status,
            'status_label' => $this->order->status_label,
            'status_color' => $this->order->status_color,
        ];
    }
}
