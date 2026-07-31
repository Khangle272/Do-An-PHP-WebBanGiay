@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
    <div style="min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 60px 20px;">
        <div
            style="background: #fff; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); padding: 40px; width: 100%; max-width: 420px;">
            <h1 style="font-size: 24px; font-weight: 700; text-align: center; margin-bottom: 8px;">Đăng nhập</h1>
            <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 14px;">Chào mừng bạn trở lại!</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/dang-nhap">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required
                        placeholder="Nhập email của bạn">
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" name="password" id="password" class="form-control" required
                        placeholder="Nhập mật khẩu">
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="remember" id="remember" style="width: 16px; height: 16px;">
                    <label for="remember" style="margin: 0; font-size: 14px;">Ghi nhớ đăng nhập</label>
                    <a href="{{ route('password.request') }}"
                        style="margin-left: auto; color: #e94560; text-decoration: none; font-size: 14px;">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn btn-primary w-full"
                    style="justify-content: center; padding: 12px; font-size: 16px;">Đăng nhập</button>
            </form>

            <p style="text-align: center; margin-top: 20px; font-size: 14px; color: #666;">
                Chưa có tài khoản? <a href="/dang-ky" style="color: #e94560; text-decoration: none; font-weight: 600;">Đăng
                    ký ngay</a>
            </p>
        </div>
    </div>
@endsection