<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);

        Category::create($data);

        return redirect('/admin/danh-muc')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);

        $category->update($data);

        return redirect('/admin/danh-muc')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục có sản phẩm!');
        }
        $category->delete();
        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
