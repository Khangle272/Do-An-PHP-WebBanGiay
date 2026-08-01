@extends('layouts.app')

@section('title', 'Đặt hàng')

@section('content')
    <div class="container" style="margin-top: 30px; min-height: 60vh;">
        <ul class="breadcrumb">
            <li><a href="/">Trang chủ</a></li>
            <li><a href="/gio-hang">Giỏ hàng</a></li>
            <li>Đặt hàng</li>
        </ul>

        <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 30px;">📦 Đặt hàng</h1>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 380px; gap: 30px;">
            <!-- Thông tin giao hàng -->
            <div style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Thông tin giao hàng</h3>

                <form method="POST" action="/dat-hang" id="checkout-form">
                    @csrf

                    <div class="form-group">
                        <label for="full_name">Họ tên người nhận *</label>
                        <input type="text" name="full_name" id="full_name" class="form-control"
                            value="{{ old('full_name', auth()->user()->name) }}" required>
                        @error('full_name') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group">
                            <label for="phone">Số điện thoại *</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                value="{{ old('phone', auth()->user()->phone) }}" required>
                            @error('phone') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Địa chỉ giao hàng *</label>
                        <textarea name="address" id="address" class="form-control"
                            required>{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label for="note">Ghi chú (không bắt buộc)</label>
                        <textarea name="note" id="note" class="form-control"
                            placeholder="Ghi chú cho đơn hàng...">{{ old('note') }}</textarea>
                    </div>

                    <div style="margin-top: 20px;">
                        <p style="font-size: 13px; color: #999;">Phương thức thanh toán: <strong>Thanh toán khi nhận hàng
                                (COD)</strong></p>
                    </div>

                    <button type="submit" class="btn btn-primary w-full"
                        style="justify-content: center; padding: 14px; font-size: 16px; margin-top: 20px;">
                        ✅ Xác nhận đặt hàng
                    </button>
                </form>
            </div>

            <script>
                (function () {
                    const form = document.getElementById('checkout-form');
                    if (!form) return;

                    const STORAGE_KEY = 'checkout_form_data';
                    const fields = ['full_name', 'email', 'phone', 'address', 'note'];

                    function save() {
                        const data = {};
                        fields.forEach(name => {
                            const el = form.elements[name];
                            if (el) data[name] = el.value;
                        });
                        try {
                            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
                        } catch (e) {}
                    }

                    try {
                        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
                        fields.forEach(name => {
                            const el = form.elements[name];
                            if (el && saved[name] !== undefined) {
                                el.value = saved[name];
                            }
                        });
                    } catch (e) {}

                    form.addEventListener('input', save);

                    form.addEventListener('submit', function () {
                        localStorage.removeItem(STORAGE_KEY);
                    });
                })();
            </script>

            <!-- Tóm tắt đơn hàng -->
            <div
                style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); height: fit-content; position: sticky; top: 100px;">
                <h3
                    style="font-size: 18px; font-weight: 600; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0;">
                    Đơn hàng của bạn</h3>

                @foreach($cartItems as $item)
                    <div style="display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f5f5f5;">
                        <img src="{{ $item->product->thumbnail_url }}" alt=""
                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                        <div style="flex: 1;">
                            <div style="font-size: 13px; font-weight: 500;">{{ $item->product->name }}</div>
                            <div style="font-size: 12px; color: #999;">
                                @if($item->size) Size {{ $item->size->size }} @endif
                                @if($item->color) / {{ $item->color->color_name }} @endif
                                × {{ $item->quantity }}
                            </div>
                            <div style="font-size: 13px; font-weight: 600; color: #e94560;">
                                {{ number_format($item->subtotal) }}₫
                            </div>
                        </div>
                    </div>
                @endforeach

                <div style="margin-top: 16px;">
                    <div
                        style="display: flex; justify-content: space-between; font-size: 14px; color: #666; margin-bottom: 8px;">
                        <span>Tạm tính</span>
                        <span>{{ number_format($subtotal) }}₫</span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; font-size: 14px; color: #666; margin-bottom: 8px;">
                        <span>Phí vận chuyển</span>
                        <span>{{ $shippingFee > 0 ? number_format($shippingFee) . '₫' : 'Miễn phí' }}</span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; font-size: 22px; font-weight: 700; padding-top: 12px; border-top: 2px solid #f0f0f0;">
                        <span>Tổng cộng</span>
                        <span style="color: #e94560;">{{ number_format($total) }}₫</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection