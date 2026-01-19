<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::sum('total'),
            'total_products' => Product::count(),
            'total_customers' => User::where('role', 'user')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
        ];

        // Recent orders
        $recent_orders = Order::with('user', 'items.product')
            ->latest()
            ->limit(10)
            ->get();

        // Order status breakdown
        $order_statuses = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'order_statuses' => $order_statuses,
        ]);
    }
}
