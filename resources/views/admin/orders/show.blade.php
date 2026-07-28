@extends('admin.layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="page-header">
        <h2>📦 Chi tiết đơn hàng #{{ $order->order_code }}</h2>
        <a href="/admin/don-hang" class="btn btn-secondary">← Quay lại</a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Sản phẩm -->
        <div class="card">
            <h3 style="font-weight: 600; margin-bottom: 16px;">Sản phẩm đã đặt</h3>
            <div class="table-wrap">
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
                                <td>{{ $item->product_name }}</td>
                                <td style="font-size: 13px;">
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

        <!-- Thông tin -->
        <div>
            <div class="card" style="margin-bottom: 24px;">
                <h3 style="font-weight: 600; margin-bottom: 16px;">Cập nhật trạng thái</h3>
                <form method="POST" action="/admin/don-hang/{{ $order->id }}/cap-nhat">
                    @csrf
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </form>
            </div>

            <div class="card">
                <h3 style="font-weight: 600; margin-bottom: 16px;">Thông tin khách hàng</h3>
                <p style="font-size: 14px; margin-bottom: 6px;"><strong>{{ $order->full_name }}</strong></p>
                <p style="font-size: 14px; color: #666; margin-bottom: 4px;">📞 {{ $order->phone }}</p>
                <p style="font-size: 14px; color: #666; margin-bottom: 4px;">✉️ {{ $order->email }}</p>
                <p style="font-size: 14px; color: #666;">📍 {{ $order->address }}</p>
                @if($order->note)
                    <p style="font-size: 14px; margin-top: 8px; padding: 8px; background: #f9f9f9; border-radius: 6px;">📝 Ghi
                        chú: {{ $order->note }}</p>
                @endif
                <hr style="margin: 16px 0;">
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 6px;">
                    <span style="color: #666;">Tạm tính</span>
                    <span>{{ number_format($order->total_price - $order->shipping_fee) }}₫</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 6px;">
                    <span style="color: #666;">Phí ship</span>
                    <span>{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee) . '₫' : 'Miễn phí' }}</span>
                </div>
                <div
                    style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; padding-top: 8px; border-top: 2px solid #f0f0f0;">
                    <span>Tổng</span>
                    <span style="color: #e94560;">{{ number_format($order->total_price) }}₫</span>
                </div>
                <p style="font-size: 13px; color: #999; margin-top: 8px;">💳 COD (Thanh toán khi nhận hàng)</p>
            </div>
        </div>
    </div>
@endsection