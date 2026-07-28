<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);

        Brand::create($data);

        CacheService::forgetBrands();

        return redirect('/admin/thuong-hieu')->with('success', 'Thêm thương hiệu thành công!');
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);

        $brand->update($data);

        CacheService::forgetBrands();

        return redirect('/admin/thuong-hieu')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa thương hiệu có sản phẩm!');
        }
        $brand->delete();

        CacheService::forgetBrands();

        return back()->with('success', 'Xóa thương hiệu thành công!');
    }
}
