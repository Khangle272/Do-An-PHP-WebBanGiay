<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'brand', 'reviews']);

        // Filter theo danh mục
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter theo thương hiệu
        if ($request->filled('brand')) {
            $brand = Brand::where('slug', $request->brand)->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        // Filter theo giá
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price)
                    ->orWhere('sale_price', '>=', $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price)
                    ->orWhere('sale_price', '<=', $request->max_price);
            });
        }

        // Filter theo size
        if ($request->filled('size')) {
            $query->whereHas('sizes', function ($q) use ($request) {
                $q->where('size', $request->size);
            });
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sắp xếp
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $sizes = ['39', '40', '41', '42', '43', '44'];

        return view('products.index', compact('products', 'categories', 'brands', 'sizes'));
    }

    public function show($slug)
    {
        $product = Product::active()
            ->with(['category', 'brand', 'images', 'sizes', 'colors', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
