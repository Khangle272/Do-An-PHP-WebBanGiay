@extends('admin.layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('content')
    <div class="page-header">
        <h2>✏️ Sửa sản phẩm</h2>
        <a href="/admin/san-pham" class="btn btn-secondary">← Quay lại</a>
    </div>

    <div class="card">
        <form method="POST" action="/admin/san-pham/{{ $product->id }}/sua" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Danh mục *</label>
                        <select name="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Thương hiệu *</label>
                        <select name="brand_id" class="form-control" required>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control"
                    rows="4">{{ old('description', $product->description) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Giá gốc *</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}"
                        required min="0">
                </div>
                <div class="form-group">
                    <label>Giá khuyến mãi</label>
                    <input type="number" name="sale_price" class="form-control"
                        value="{{ old('sale_price', $product->sale_price) }}" min="0">
                </div>
            </div>

            <div class="form-group">
                <label>Ảnh đại diện</label>
                @if($product->thumbnail_url)
                    <div style="margin-bottom: 8px;">
                        <img src="{{ $product->thumbnail_url }}" alt="Current thumbnail"
                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #e0e0e0;">
                    </div>
                @endif
                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                <small style="color: #999;">Chọn file ảnh mới nếu muốn thay đổi (JPEG, PNG, JPG, GIF, WebP, tối đa
                    2MB).</small>
                @error('thumbnail') <small style="color: red;">{{ $message }}</small> @enderror
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 16px;">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="status" value="1" {{ $product->status ? 'checked' : '' }}> Hiển thị
                </label>
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="featured" value="1" {{ $product->featured ? 'checked' : '' }}> Nổi bật
                </label>
            </div>

            <hr style="margin: 20px 0;">

            <h4 style="margin-bottom: 16px;">📏 Kích cỡ hiện tại</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px;">
                @foreach($product->sizes as $size)
                    <span style="background: #f0f0f0; padding: 6px 14px; border-radius: 6px; font-size: 13px;">
                        Size {{ $size->size }} ({{ $size->stock }} cái)
                    </span>
                @endforeach
            </div>

            <h4 style="margin-bottom: 16px;">🎨 Màu sắc hiện tại</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px;">
                @foreach($product->colors as $color)
                    <span
                        style="background: #f0f0f0; padding: 6px 14px; border-radius: 6px; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                        <span
                            style="width: 14px; height: 14px; border-radius: 50%; background: {{ $color->color_code }}; border: 1px solid #ccc; display: inline-block;"></span>
                        {{ $color->color_name }} ({{ $color->stock }} cái)
                    </span>
                @endforeach
            </div>

            <p style="font-size: 13px; color: #999;">Để thay đổi size/màu, vui lòng xóa và tạo lại sản phẩm.</p>

            <div style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="/admin/san-pham" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection