<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = CartItem::with(['product', 'size', 'color'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $subtotal = $cartItems->sum(fn ($item) => $item->product->final_price * $item->quantity);

        return response()->json([
            'data' => CartItemResource::collection($cartItems),
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_size_id' => 'nullable|exists:product_sizes,id',
            'product_color_id' => 'nullable|exists:product_colors,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($data['product_id']);

        $sizeStock = null;
        if (!empty($data['product_size_id'])) {
            $size = $product->sizes()->find($data['product_size_id']);
            $sizeStock = $size ? $size->stock : 0;
        }

        $existing = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $data['product_id'])
            ->where('product_size_id', $data['product_size_id'] ?? null)
            ->where('product_color_id', $data['product_color_id'] ?? null)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $data['quantity'];
            if ($sizeStock !== null && $newQty > $sizeStock) {
                return response()->json(['message' => 'Số lượng vượt quá tồn kho!'], 422);
            }
            $existing->update(['quantity' => $newQty]);
            $item = $existing;
        } else {
            if ($sizeStock !== null && $data['quantity'] > $sizeStock) {
                return response()->json(['message' => 'Số lượng vượt quá tồn kho!'], 422);
            }
            $item = CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $data['product_id'],
                'product_size_id' => $data['product_size_id'] ?? null,
                'product_color_id' => $data['product_color_id'] ?? null,
                'quantity' => $data['quantity'],
            ]);
        }

        return response()->json([
            'message' => 'Đã thêm vào giỏ hàng!',
            'data' => new CartItemResource($item->load(['product', 'size', 'color'])),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $item = CartItem::where('user_id', $request->user()->id)->findOrFail($id);

        $data = $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $item->update(['quantity' => $data['quantity']]);

        return response()->json([
            'message' => 'Đã cập nhật số lượng!',
            'data' => new CartItemResource($item->load(['product', 'size', 'color'])),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $item = CartItem::where('user_id', $request->user()->id)->findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng!']);
    }
}