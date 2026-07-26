<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with(['product', 'size', 'color'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->final_price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_size_id' => 'nullable|exists:product_sizes,id',
            'product_color_id' => 'nullable|exists:product_colors,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($data['product_id']);

        // Kiểm tra tồn kho
        $sizeStock = null;
        if ($data['product_size_id']) {
            $size = $product->sizes()->find($data['product_size_id']);
            $sizeStock = $size ? $size->stock : 0;
        }

        // Kiểm tra xem đã có item này trong giỏ chưa
        $existing = CartItem::where('user_id', Auth::id())
            ->where('product_id', $data['product_id'])
            ->where('product_size_id', $data['product_size_id'])
            ->where('product_color_id', $data['product_color_id'])
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $data['quantity'];
            if ($sizeStock !== null && $newQty > $sizeStock) {
                return back()->with('error', 'Số lượng vượt quá tồn kho!');
            }
            $existing->update(['quantity' => $newQty]);
        } else {
            if ($sizeStock !== null && $data['quantity'] > $sizeStock) {
                return back()->with('error', 'Số lượng vượt quá tồn kho!');
            }
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $data['product_id'],
                'product_size_id' => $data['product_size_id'],
                'product_color_id' => $data['product_color_id'],
                'quantity' => $data['quantity'],
            ]);
        }

        return redirect('/gio-hang')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function update(Request $request, $id)
    {
        $item = CartItem::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $item->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Đã cập nhật số lượng!');
    }

    public function remove($id)
    {
        $item = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $item->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }
}
