<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product', 'items.size', 'items.color'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000',
        ]);

        $cartItems = CartItem::with(['product', 'size', 'color'])
            ->where('user_id', $request->user()->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng trống!'], 422);
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->product->final_price * $item->quantity);
        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        $total = $subtotal + $shippingFee;
        $orderCode = 'DH' . date('Ymd') . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $request->user()->id,
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

                if ($item->product_size_id) {
                    $item->size->decrement('stock', $item->quantity);
                }
            }

            CartItem::where('user_id', $request->user()->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Đặt hàng thành công!',
                'data' => new OrderResource($order->load('items')),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại!'], 500);
        }
    }
}