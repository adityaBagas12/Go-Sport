<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\SalesChartService;

class DashboardController extends Controller
{
    public function __invoke(SalesChartService $salesChart)
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::countedAsRevenue()->sum('total');
        $pendingOrders = Order::where('status', 'menunggu_pembayaran')->count();
        $paidOrders = Order::where('payment_status', 'paid')->count();

        $bestSellers = Product::orderByDesc('sold')->take(5)->get();

        $latestOrders = Order::with('user')->latest()->take(8)->get();

        $chart = $salesChart->build();

        return view('admin.dashboard', compact(
            'totalProducts', 'totalCategories', 'totalCustomers', 'totalOrders',
            'totalRevenue', 'pendingOrders', 'paidOrders', 'bestSellers', 'latestOrders', 'chart'
        ));
    }
}
