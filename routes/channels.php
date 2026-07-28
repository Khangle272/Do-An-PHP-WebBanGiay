<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels (Socket)
|--------------------------------------------------------------------------
| Mỗi channel dưới đây quyết định "ai được phép lắng nghe" kênh đó.
| Trả về true/dữ liệu => cho phép kết nối; false/null => từ chối.
*/

// Chỉ user có is_admin = true mới được nghe kênh thông báo đơn hàng mới
Broadcast::channel('admin.orders', function ($user) {
    return (bool) $user->is_admin;
});

// Chỉ đúng khách đã đặt đơn hàng đó (hoặc admin) mới được nghe
// cập nhật trạng thái của đơn hàng #{orderId}
Broadcast::channel('order.{orderId}', function ($user, int $orderId) {
    $order = Order::find($orderId);

    if (!$order) {
        return false;
    }

    return $user->is_admin || $order->user_id === $user->id;
});
