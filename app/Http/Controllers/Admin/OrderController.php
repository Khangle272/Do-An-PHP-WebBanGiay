<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'items.size', 'items.color', 'user'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update(['status' => $data['status']]);

        // [QUEUE] Gửi email thông báo cho khách ở nền, không chặn admin.
        Mail::to($order->email)->send(new OrderStatusUpdatedMail($order));

        // [SOCKET] Khách đang mở trang chi tiết đơn hàng sẽ thấy trạng thái
        // đổi ngay lập tức mà không cần F5 (chỉ đúng khách đó nhận được -
        // xem quyền truy cập kênh trong routes/channels.php).
        broadcast(new OrderStatusUpdated($order))->toOthers();

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }
}
