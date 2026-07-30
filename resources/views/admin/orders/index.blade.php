@extends('admin.layouts.admin')

@section('title', 'Quản lý Đơn hàng')

@section('content')
    <div class="page-header">
        <h2>📦 Quản lý Đơn hàng</h2>
    </div>

    {{-- [SOCKET] Vùng thông báo realtime - script bên dưới sẽ chèn thẻ
    thông báo mới vào đây mỗi khi có đơn hàng mới, không cần F5 --}}
    <div id="realtime-order-alert"></div>

    <div class="card">
        <form method="GET" style="display: flex; gap: 10px; margin-bottom: 16px;">
            <select name="status" class="form-control" style="max-width: 200px;" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
            <a href="/admin/don-hang" class="btn btn-sm btn-secondary">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td style="font-weight: 600;">{{ $order->order_code }}</td>
                            <td>{{ $order->full_name }}</td>
                            <td style="font-size: 13px;">{{ $order->email }}</td>
                            <td>{{ $order->phone }}</td>
                            <td style="font-weight: 600;">{{ number_format($order->total_price) }}₫</td>
                            <td>
                                <span
                                    class="badge badge-{{ $order->status == 'pending' ? 'yellow' : ($order->status == 'processing' ? 'blue' : ($order->status == 'completed' ? 'green' : 'red')) }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td style="font-size: 13px; color: #666;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td><a href="/admin/don-hang/{{ $order->id }}" class="btn btn-sm btn-primary">Chi tiết</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $orders->links() }}</div>
    </div>

    {{-- [SOCKET] Lắng nghe kênh riêng "admin.orders" (xem routes/channels.php).
    Chỉ hoạt động khi đã cài & bật Reverb (BROADCAST_CONNECTION=reverb),
    còn không thì trang vẫn chạy bình thường, chỉ là không có realtime. --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (!window.Echo) return; // Chưa cấu hình Reverb -> bỏ qua, không lỗi

                window.Echo.private('admin.orders')
                    .listen('.order.placed', (e) => {
                        const box = document.getElementById('realtime-order-alert');
                        const item = document.createElement('div');
                        item.className = 'alert alert-success';
                        item.style.cssText = 'margin-bottom: 12px; cursor: pointer;';
                        item.innerHTML = `🔔 Đơn hàng mới <strong>#${e.order_code}</strong> từ ${e.full_name} - ${Number(e.total_price).toLocaleString('vi-VN')}₫ lúc ${e.created_at}`;
                        item.addEventListener('click', () => location.reload());
                        box.prepend(item);
                    });
            });
        </script>
    @endpush
@endsection