<?php

namespace App\Http\Controllers;

use App\Events\NewOrderPlaced;
use App\Mail\OrderPlacedMail;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with(['product', 'size', 'color'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect('/gio-hang')->with('error', 'Giỏ hàng trống!');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->final_price * $item->quantity;
        });

        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        $total = $subtotal + $shippingFee;

        return view('checkout.index', compact('cartItems', 'subtotal', 'shippingFee', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000',
        ], [
            'full_name.required' => 'Vui lòng nhập họ tên người nhận',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'address.required' => 'Vui lòng nhập địa chỉ giao hàng',
        ]);

        $cartItems = CartItem::with(['product', 'size', 'color'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống!');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->final_price * $item->quantity;
        });

        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        $total = $subtotal + $shippingFee;

        // Tạo mã đơn hàng
        $orderCode = 'DH' . date('Ymd') . strtoupper(\Illuminate\Support\Str::random(6));

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => $orderCode,
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'] ?? null,
                'shipping_fee' => $shippingFee,
                'total_price' => $total,
                'status' => 'pending',
                'payment_method' => 'cod',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_size_id' => $item->product_size_id,
                    'product_color_id' => $item->product_color_id,
                    'product_name' => $item->product->name,
                    'product_price' => $item->product->final_price,
                    'quantity' => $item->quantity,
                ]);

                // Giảm stock
                if ($item->product_size_id) {
                    $item->size->decrement('stock', $item->quantity);
                }
            }

            // Xóa giỏ hàng
            CartItem::where('user_id', Auth::id())->delete();

            DB::commit();

            // [QUEUE] OrderPlacedMail implements ShouldQueue -> lệnh send()
            // này chỉ ghi 1 job vào bảng `jobs` rồi trả về ngay lập tức,
            // việc gửi email thật sự do "php artisan queue:work" xử lý ở nền.
            Mail::to($order->email)->send(new OrderPlacedMail($order));

            // [SOCKET] Event implements ShouldBroadcast -> cũng được đẩy
            // vào queue rồi mới bắn qua WebSocket, không làm chậm response.
            broadcast(new NewOrderPlaced($order))->toOthers();

            return redirect('/dat-hang/thanh-cong/' . $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!');
        }
    }

    public function success($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        return view('checkout.success', compact('order'));
    }
}
