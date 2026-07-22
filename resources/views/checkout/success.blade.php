@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
    <div class="container"
        style="margin-top: 30px; min-height: 60vh; display: flex; align-items: center; justify-content: center;">
        <div
            style="text-align: center; background: #fff; border-radius: 16px; padding: 60px 40px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); max-width: 500px; width: 100%;">
            <div style="font-size: 80px; margin-bottom: 16px;">✅</div>
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 8px;">Đặt hàng thành công!</h1>
            <p style="color: #666; margin-bottom: 8px;">Cảm ơn bạn đã mua hàng tại SneakerShop.</p>
            <p style="color: #666; margin-bottom: 24px;">Mã đơn hàng của bạn là:</p>

            <div
                style="font-size: 24px; font-weight: 800; color: #e94560; background: #fde8e8; display: inline-block; padding: 12px 30px; border-radius: 10px; letter-spacing: 2px; margin-bottom: 24px;">
                {{ $order->order_code }}
            </div>

            <p style="font-size: 14px; color: #999; margin-bottom: 24px;">
                Chúng tôi sẽ liên hệ với bạn qua số điện thoại để xác nhận đơn hàng trong thời gian sớm nhất.
            </p>

            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="/don-hang/{{ $order->id }}" class="btn btn-primary">📋 Xem chi tiết đơn hàng</a>
                <a href="/san-pham" class="btn btn-outline">← Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>
@endsection