@extends('admin.layouts.admin')

@section('title', 'Quản lý Đơn hàng')

@section('content')
    <div class="page-header">
        <h2>📦 Quản lý Đơn hàng</h2>
    </div>

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
@endsection