<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_sales' => Order::whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed'])->sum('total'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending_payment')->count(),
            'pending_shipments' => Order::whereIn('status', ['paid', 'processing', 'ready_to_ship'])->count(),
            'total_products' => Product::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'low_stock_count' => ProductVariant::where('stock', '<=', 5)->where('is_active', true)->count(),
        ];

        // 7-day sales chart data
        $startDate = Carbon::today()->subDays(6);
        $salesRaw = Order::whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed'])
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartRevenue = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($currentDate)->format('d M');
            $record = $salesRaw->get($currentDate);
            $chartRevenue[] = $record ? (float) $record->revenue : 0;
        }

        // Top 5 selling products
        $topProducts = OrderItem::whereHas('order', function ($q) {
            $q->whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed']);
        })
            ->select('product_name_snapshot', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_name_snapshot')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with(['user', 'items.variant.product'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'chartLabels', 'chartRevenue', 'topProducts'));
    }
}
