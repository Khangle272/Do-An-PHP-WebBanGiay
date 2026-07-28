@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
    <div style="min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 60px 20px;">
        <div
            style="background: #fff; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); padding: 40px; width: 100%; max-width: 420px;">
            <h1 style="font-size: 24px; font-weight: 700; text-align: center; margin-bottom: 8px;">Đăng ký</h1>
            <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 14px;">Tạo tài khoản mới để mua sắm
            </p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/dang-ky">
                @csrf
                <div class="form-group">
                    <label for="name">Họ tên</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required
                        placeholder="Nhập họ tên của bạn">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required
                        placeholder="Nhập email">
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}"
                        placeholder="Nhập số điện thoại (không bắt buộc)">
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" name="password" id="password" class="form-control" required
                        placeholder="Ít nhất 6 ký tự">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                        required placeholder="Nhập lại mật khẩu">
                </div>
                <button type="submit" class="btn btn-primary w-full"
                    style="justify-content: center; padding: 12px; font-size: 16px;">Đăng ký</button>
            </form>

            <p style="text-align: center; margin-top: 20px; font-size: 14px; color: #666;">
                Đã có tài khoản? <a href="/dang-nhap" style="color: #e94560; text-decoration: none; font-weight: 600;">Đăng
                    nhập</a>
            </p>
        </div>
    </div>
@endsection