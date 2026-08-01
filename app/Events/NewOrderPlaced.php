<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * [SOCKET] Bắn realtime cho toàn bộ admin đang mở trang quản trị
 * khi có 1 đơn hàng mới được đặt - không cần F5 vẫn thấy ngay.
 *
 * ShouldBroadcast -> Laravel tự đẩy việc gửi socket vào QUEUE
 * (bảng jobs) thay vì chặn request đặt hàng của khách để chờ
 * kết nối tới WebSocket server.
 */
class NewOrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'full_name' => $this->order->full_name,
            'total_price' => $this->order->total_price,
            'created_at' => $this->order->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y'),
        ];
    }
}
