<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $order = Order::with(['items.product', 'items.variant', 'user'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items.variant')->findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $data['status'];

        try {
            DB::transaction(function () use ($order, $oldStatus, $newStatus) {
                // [KHO] Kho đã được GIỮ CHỖ ngay lúc khách đặt hàng (xem
                // CheckoutController::placeOrder()), nên các trạng thái
                // pending/processing/completed không đụng vào kho nữa.

                // Đơn chuyển SANG "cancelled" (lần đầu) -> hoàn lại kho đã giữ chỗ
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->product_variant_id) {
                            $item->variant()->increment('stock', $item->quantity);
                        }
                    }
                }

                // Đơn đang "cancelled" được khôi phục lại (chuyển sang trạng thái khác)
                // -> giữ chỗ kho lại như lúc đặt hàng ban đầu. Nếu không đủ hàng
                // (đã bán cho đơn khác trong lúc đơn này bị hủy) -> chặn khôi phục.
                if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->product_variant_id) {
                            $affected = $item->variant()
                                ->where('stock', '>=', $item->quantity)
                                ->decrement('stock', $item->quantity);

                            if (!$affected) {
                                throw new \RuntimeException(
                                    "Không thể khôi phục đơn hàng: sản phẩm \"{$item->product_name}\" ({$item->variant->label}) không còn đủ tồn kho."
                                );
                            }
                        }
                    }
                }

                $order->update(['status' => $newStatus]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        // [QUEUE] Gửi email thông báo cho khách ở nền, không chặn admin.
        Mail::to($order->email)->send(new OrderStatusUpdatedMail($order));

        // [SOCKET] Khách đang mở trang chi tiết đơn hàng sẽ thấy trạng thái
        // đổi ngay lập tức mà không cần F5 (chỉ đúng khách đó nhận được -
        // xem quyền truy cập kênh trong routes/channels.php).
        broadcast(new OrderStatusUpdated($order))->toOthers();

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }
}