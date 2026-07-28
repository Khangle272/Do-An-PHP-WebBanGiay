<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $hasBought = $request->user()->orders()
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })->exists();

        if (!$hasBought) {
            return response()->json(['message' => 'Bạn cần mua sản phẩm này để có thể đánh giá!'], 403);
        }

        $hasReviewed = $request->user()->reviews()->where('product_id', $product->id)->exists();

        if ($hasReviewed) {
            return response()->json(['message' => 'Bạn đã đánh giá sản phẩm này rồi!'], 422);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $review = $request->user()->reviews()->create([
            'product_id' => $product->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return response()->json([
            'message' => 'Cảm ơn bạn đã đánh giá sản phẩm!',
            'data' => new ReviewResource($review->load('user')),
        ], 201);
    }
}