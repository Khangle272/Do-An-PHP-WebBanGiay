@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="stat-grid">
        <div class="stat-card">
            <div class="label">Tổng doanh thu</div>
            <div class="value" style="color: #28a745;">{{ number_format($totalRevenue) }}₫</div>
        </div>
        <div class="stat-card">
            <div class="label">Tổng đơn hàng</div>
            <div class="value" style="color: #007bff;">{{ $totalOrders }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Sản phẩm</div>
            <div class="value" style="color: #e94560;">{{ $totalProducts }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Người dùng</div>
            <div class="value" style="color: #6610f2;">{{ $totalUsers }}</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
        <!-- Biểu đồ doanh thu -->
        <div class="chart-wrap">
            <h3 style="font-weight: 600; margin-bottom: 16px;">Doanh thu 7 ngày gần nhất</h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>

        <!-- Thống kê đơn hàng -->
        <div class="card">
            <h3 style="font-weight: 600; margin-bottom: 16px;">Đơn hàng theo trạng thái</h3>
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 14px;">Chờ xác nhận</span>
                    <span style="font-weight: 600;">{{ $pendingOrders }}</span>
                </div>
                <div style="height: 6px; background: #f0f0f0; border-radius: 3px;">
                    <div
                        style="height: 100%; width: {{ $totalOrders > 0 ? ($pendingOrders / $totalOrders) * 100 : 0 }}%; background: #ffc107; border-radius: 3px;">
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 14px;">Đang xử lý</span>
                    <span style="font-weight: 600;">{{ $processingOrders }}</span>
                </div>
                <div style="height: 6px; background: #f0f0f0; border-radius: 3px;">
                    <div
                        style="height: 100%; width: {{ $totalOrders > 0 ? ($processingOrders / $totalOrders) * 100 : 0 }}%; background: #007bff; border-radius: 3px;">
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 14px;">Hoàn thành</span>
                    <span style="font-weight: 600;">{{ $completedOrders }}</span>
                </div>
                <div style="height: 6px; background: #f0f0f0; border-radius: 3px;">
                    <div
                        style="height: 100%; width: {{ $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0 }}%; background: #28a745; border-radius: 3px;">
                    </div>
                </div>
            </div>
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 14px;">Đã hủy</span>
                    <span style="font-weight: 600;">{{ $cancelledOrders }}</span>
                </div>
                <div style="height: 6px; background: #f0f0f0; border-radius: 3px;">
                    <div
                        style="height: 100%; width: {{ $totalOrders > 0 ? ($cancelledOrders / $totalOrders) * 100 : 0 }}%; background: #dc3545; border-radius: 3px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đơn hàng gần đây -->
    <div class="card">
        <h3 style="font-weight: 600; margin-bottom: 16px;">Đơn hàng gần đây</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                        <tr>
                            <td style="font-weight: 600;">{{ $order->order_code }}</td>
                            <td>{{ $order->full_name }}</td>
                            <td style="font-weight: 600;">{{ number_format($order->total_price) }}₫</td>
                            <td>
                                <span
                                    class="badge badge-{{ $order->status == 'pending' ? 'yellow' : ($order->status == 'processing' ? 'blue' : ($order->status == 'completed' ? 'green' : 'red')) }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td style="font-size: 13px; color: #666;">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td><a href="/admin/don-hang/{{ $order->id }}" class="btn btn-sm btn-primary">Xem</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Doanh thu (₫)',
                    data: {!! json_encode($revenueData) !!},
                    borderColor: '#e94560',
                    backgroundColor: 'rgba(233, 69, 96, 0.1)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: { callback: v => v.toLocaleString('vi-VN') + '₫' },
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
@endpush