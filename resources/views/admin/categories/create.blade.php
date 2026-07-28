@extends('admin.layouts.admin')

@section('title', 'Thêm danh mục')

@section('content')
    <div class="page-header">
        <h2>➕ Thêm danh mục</h2>
        <a href="/admin/danh-muc" class="btn btn-secondary">← Quay lại</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form method="POST" action="/admin/danh-muc">
            @csrf
            <div class="form-group">
                <label>Tên danh mục *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="status" value="1" checked> Hiển thị
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="/admin/danh-muc" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
@endsection