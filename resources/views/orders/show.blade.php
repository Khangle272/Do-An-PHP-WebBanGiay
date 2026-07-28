@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="container" style="margin-top: 30px; min-height: 60vh;">
        <ul class="breadcrumb">
            <li><a href="/">Trang chủ</a></li>
            <li><a href="/don-hang">Đơn hàng</a></li>
            <li>{{ $order->order_code }}</li>
        </ul>

        <div style="display: grid; grid-template-columns: 1fr 360px; gap: 30px;">
            <!-- Chi tiết sản phẩm -->
            <div>
                <div
                    style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h2 style="font-size: 20px; font-weight: 700;">Đơn hàng #{{ $order->order_code }}</h2>
                        @php
                            $statusColors = ['pending' => 'warning', 'processing' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'];
                            $color = $statusColors[$order->status] ?? 'secondary';
                        @endphp
                        <span id="order-status-badge" class="btn btn-{{ $color }} btn-sm" style="cursor: default;">{{ $order->status_label }}</span>
                    </div>
                    <p style="font-size: 14px; color: #666;">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>

                    <div class="table-wrap" style="margin-top: 16px;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Size/Màu</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <a href="/san-pham/{{ $item->product->slug ?? '#' }}"
                                                style="color: #1a1a2e; text-decoration: none;">
                                                {{ $item->product_name }}
                                            </a>
                                        </td>
                                        <td style="font-size: 13px; color: #666;">
                                            @if($item->size) Size {{ $item->size->size }} @endif
                                            @if($item->color) / {{ $item->color->color_name }} @endif
                                        </td>
                                        <td>{{ number_format($item->product_price) }}₫</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td style="font-weight: 600;">{{ number_format($item->subtotal) }}₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Thông tin giao hàng & Tổng tiền -->
            <div>
                <div
                    style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 24px;">
                    <h3
                        style="font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0;">
                        Thông tin giao hàng</h3>
                    <p style="font-size: 14px; margin-bottom: 4px;"><strong>{{ $order->full_name }}</strong></p>
                    <p style="font-size: 14px; color: #666; margin-bottom: 4px;">📞 {{ $order->phone }}</p>
                    <p style="font-size: 14px; color: #666; margin-bottom: 4px;">✉️ {{ $order->email }}</p>
                    <p style="font-size: 14px; color: #666;">📍 {{ $order->address }}</p>
                    @if($order->note)
                        <p
                            style="font-size: 14px; color: #666; margin-top: 8px; padding: 8px; background: #f9f9f9; border-radius: 6px;">
                            📝 Ghi chú: {{ $order->note }}</p>
                    @endif
                </div>

                <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                    <h3
                        style="font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0;">
                        Thanh toán</h3>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span style="color: #666;">Tạm tính</span>
                        <span>{{ number_format($order->total_price - $order->shipping_fee) }}₫</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span style="color: #666;">Phí vận chuyển</span>
                        <span>{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee) . '₫' : 'Miễn phí' }}</span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; padding-top: 12px; border-top: 2px solid #f0f0f0;">
                        <span>Tổng cộng</span>
                        <span style="color: #e94560;">{{ number_format($order->total_price) }}₫</span>
                    </div>
                    <p style="font-size: 13px; color: #999; margin-top: 12px;">Thanh toán: <strong>COD (khi nhận
                            hàng)</strong></p>
                </div>
            </div>
        </div>
    </div>

    {{-- [SOCKET] Lắng nghe kênh riêng "order.{id}" (xem routes/channels.php)
    - chỉ chính khách hàng đặt đơn này mới được phép nghe. Khi admin đổi
    trạng thái, badge bên trên tự cập nhật mà không cần reload trang. --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (!window.Echo) return; // Chưa cấu hình Reverb -> bỏ qua, không lỗi

                window.Echo.private('order.{{ $order->id }}')
                    .listen('.status.updated', (e) => {
                        const badge = document.getElementById('order-status-badge');
                        badge.textContent = e.status_label;
                        badge.className = 'btn btn-' + ({
                            yellow: 'warning',
                            blue: 'primary',
                            green: 'success',
                            red: 'danger',
                        } [e.status_color] ?? 'secondary') + ' btn-sm';
                    });
            });
        </script>
    @endpush
@endsection