<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function store(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        // Kiểm tra đã mua hàng chưa
        $hasBought = Auth::user()->orders()
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })->exists();

        if (!$hasBought) {
            return back()->with('error', 'Bạn cần mua sản phẩm này để có thể đánh giá!');
        }

        // Kiểm tra đã đánh giá chưa
        $hasReviewed = Auth::user()->reviews()
            ->where('product_id', $product->id)
            ->exists();

        if ($hasReviewed) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi!');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Auth::user()->reviews()->create([
            'product_id' => $product->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
