@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <div style="min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 60px 20px;">
        <div
            style="background: #fff; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); padding: 40px; width: 100%; max-width: 420px;">
            <h1 style="font-size: 24px; font-weight: 700; text-align: center; margin-bottom: 8px;">Đặt lại mật khẩu</h1>
            <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 14px;">Nhập mật khẩu mới cho tài khoản
                của bạn!</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $email) }}"
                        required placeholder="Nhập email">
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu mới</label>
                    <input type="password" name="password" id="password" class="form-control" required
                        placeholder="Ít nhất 6 ký tự">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                        required placeholder="Nhập lại mật khẩu mới">
                </div>
                <button type="submit" class="btn btn-primary w-full"
                    style="justify-content: center; padding: 12px; font-size: 16px;">Đặt lại mật khẩu</button>
            </form>
        </div>
    </div>
@endsection
