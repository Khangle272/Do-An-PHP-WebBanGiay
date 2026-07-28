<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = $request->user()->wishlists()
            ->with('product.category', 'product.brand')
            ->latest()
            ->get()
            ->pluck('product');

        return ProductResource::collection($wishlist);
    }

    public function toggle(Request $request, $productId)
    {
        Product::findOrFail($productId);

        $existing = $request->user()->wishlists()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            $wished = false;
        } else {
            $request->user()->wishlists()->create(['product_id' => $productId]);
            $wished = true;
        }

        return response()->json(['wished' => $wished]);
    }
}