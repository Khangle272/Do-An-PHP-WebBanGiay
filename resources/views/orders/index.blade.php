@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
    <div class="container" style="margin-top: 30px; min-height: 60vh;">
        <ul class="breadcrumb">
            <li><a href="/">Trang chủ</a></li>
            <li>Đơn hàng của tôi</li>
        </ul>

        <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 30px;">📋 Đơn hàng của tôi</h1>

        @if($orders->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td style="font-weight: 600;">{{ $order->order_code }}</td>
                                <td style="font-size: 13px; color: #666;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $order->items->count() }} sản phẩm</td>
                                <td style="font-weight: 600; color: #e94560;">{{ number_format($order->total_price) }}₫</td>
                                <td>
                                    @php
                                        $statusColors = ['pending' => 'warning', 'processing' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'];
                                        $label = $order->status_label;
                                        $color = $statusColors[$order->status] ?? 'secondary';
                                    @endphp
                                    <span class="btn btn-{{ $color }} btn-sm"
                                        style="cursor: default; font-size: 12px; padding: 4px 12px;">{{ $label }}</span>
                                </td>
                                <td><a href="/don-hang/{{ $order->id }}" class="btn btn-outline btn-sm">Chi tiết</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $orders->links() }}</div>
        @else
            <div style="text-align: center; padding: 80px 20px; background: #fff; border-radius: 12px;">
                <div style="font-size: 64px; margin-bottom: 16px;">📦</div>
                <h2 style="margin-bottom: 8px;">Chưa có đơn hàng</h2>
                <p style="color: #666; margin-bottom: 24px;">Bạn chưa đặt đơn hàng nào. Hãy mua sắm ngay!</p>
                <a href="/san-pham" class="btn btn-primary">Mua sắm ngay</a>
            </div>
        @endif
    </div>
@endsection