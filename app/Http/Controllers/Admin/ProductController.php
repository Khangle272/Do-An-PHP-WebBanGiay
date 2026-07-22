<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string|max:500',
            'status' => 'boolean',
            'featured' => 'boolean',
            'sizes' => 'nullable|array',
            'sizes.*.size' => 'required|string',
            'sizes.*.stock' => 'required|integer|min:0',
            'colors' => 'nullable|array',
            'colors.*.name' => 'required|string',
            'colors.*.code' => 'nullable|string',
            'colors.*.stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string|max:500',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . time();
        $data['status'] = $request->boolean('status', true);
        $data['featured'] = $request->boolean('featured', false);

        $product = Product::create($data);

        // Thêm sizes
        if ($request->has('sizes')) {
            foreach ($request->sizes as $sizeData) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size' => $sizeData['size'],
                    'stock' => $sizeData['stock'],
                ]);
            }
        }

        // Thêm colors
        if ($request->has('colors')) {
            foreach ($request->colors as $colorData) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'color_name' => $colorData['name'],
                    'color_code' => $colorData['code'] ?? null,
                    'stock' => $colorData['stock'],
                ]);
            }
        }

        // Thêm images
        if ($request->has('images')) {
            foreach ($request->images as $index => $imageUrl) {
                if (!empty($imageUrl)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $imageUrl,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        return redirect('/admin/san-pham')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit($id)
    {
        $product = Product::with(['sizes', 'colors', 'images'])->findOrFail($id);
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|string|max:500',
            'status' => 'boolean',
            'featured' => 'boolean',
        ]);

        $data['status'] = $request->boolean('status', true);
        $data['featured'] = $request->boolean('featured', false);

        $product->update($data);

        return redirect('/admin/san-pham')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->sizes()->delete();
        $product->colors()->delete();
        $product->images()->delete();
        $product->delete();

        return back()->with('success', 'Xóa sản phẩm thành công!');
    }
}
