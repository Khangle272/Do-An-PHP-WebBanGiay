@extends('admin.layouts.admin')

@section('title', 'Thêm sản phẩm')

@section('content')
    <div class="page-header">
        <h2>➕ Thêm sản phẩm</h2>
        <a href="/admin/san-pham" class="btn btn-secondary">← Quay lại</a>
    </div>

    <div class="card">
        <form method="POST" action="/admin/san-pham" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Tên sản phẩm *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Danh mục *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">— Chọn —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Thương hiệu *</label>
                        <select name="brand_id" class="form-control" required>
                            <option value="">— Chọn —</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Giá gốc *</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" required min="0">
                </div>
                <div class="form-group">
                    <label>Giá khuyến mãi</label>
                    <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price') }}" min="0">
                </div>
            </div>

            <div class="form-group">
                <label>Ảnh đại diện</label>
                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                <small style="color: #999;">Chọn file ảnh (JPEG, PNG, JPG, GIF, WebP, tối đa 2MB).</small>
                @error('thumbnail') <small style="color: red;">{{ $message }}</small> @enderror
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 16px;">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="status" value="1" checked> Hiển thị
                </label>
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="featured" value="1"> Nổi bật
                </label>
            </div>

            <hr style="margin: 20px 0;">

            <h4 style="margin-bottom: 16px;">📏🎨 Biến thể (Size + Màu + Tồn kho)</h4>
            <p style="font-size: 13px; color: #999; margin-bottom: 8px;">
                Mỗi dòng là 1 tổ hợp size + màu cụ thể với số lượng tồn kho riêng của tổ hợp đó.
                Có thể để trống Size hoặc Màu nếu sản phẩm không phân loại theo chiều đó.
            </p>
            <div id="variants-wrapper">
                <div class="variant-row" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">
                    <input type="text" name="variants[0][size]" class="form-control" placeholder="Size (VD: 39)" style="max-width: 130px;">
                    <input type="text" name="variants[0][color_name]" class="form-control" placeholder="Tên màu (VD: Đen)" style="max-width: 150px;">
                    <input type="number" name="variants[0][stock]" class="form-control" placeholder="Số lượng" style="max-width: 120px;" min="0" required>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="this.parentElement.remove()">Xóa</button>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addVariant()">+ Thêm biến thể</button>

            <hr style="margin: 20px 0;">

            <h4 style="margin-bottom: 16px;">🖼️ Ảnh gallery (upload nhiều file)</h4>
            <div id="images-wrapper">
                <div class="image-row" style="margin-bottom: 8px;">
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple style="padding: 8px;">
                </div>
            </div>
            <small style="color: #999;">Có thể chọn nhiều file cùng lúc (JPEG, PNG, JPG, GIF, WebP, tối đa 2MB mỗi
                file).</small>
            @error('images.*') <small style="color: red;">{{ $message }}</small> @enderror

            <div style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                <a href="/admin/san-pham" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>

    <script>
        let variantIndex = 1;

        function addVariant() {
            const html = `<div class="variant-row" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">
                    <input type="text" name="variants[${variantIndex}][size]" class="form-control" placeholder="Size (VD: 39)" style="max-width: 130px;">
                    <input type="text" name="variants[${variantIndex}][color_name]" class="form-control" placeholder="Tên màu (VD: Đen)" style="max-width: 150px;">
                    <input type="number" name="variants[${variantIndex}][stock]" class="form-control" placeholder="Số lượng" style="max-width: 120px;" min="0" required>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="this.parentElement.remove()">Xóa</button>
                </div>`;
            document.getElementById('variants-wrapper').insertAdjacentHTML('beforeend', html);
            variantIndex++;
        }
    </script>
@endsection