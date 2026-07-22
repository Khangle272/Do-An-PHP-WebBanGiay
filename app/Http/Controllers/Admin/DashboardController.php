<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalUsers = User::count();

        // Doanh thu 7 ngày gần nhất
        $revenueData = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            $revenue = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total_price');
            $revenueData[] = $revenue;
        }

        // Đơn hàng theo trạng thái
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Đơn hàng gần đây
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalUsers',
            'labels',
            'revenueData',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'cancelledOrders',
            'recentOrders'
        ));
    }
}
