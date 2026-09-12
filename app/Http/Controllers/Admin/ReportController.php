<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $days = (int) $request->input('days', 30);
        $startDate = Carbon::today()->subDays($days - 1);

        // Daily Sales Chart Data for Chart.js
        $salesRaw = Order::whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed'])
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        for ($i = 0; $i < $days; $i++) {
            $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($currentDate)->format('d M');
            $record = $salesRaw->get($currentDate);
            $chartRevenue[] = $record ? (float) $record->revenue : 0;
            $chartOrders[] = $record ? (int) $record->orders_count : 0;
        }

        // Top Selling Products (Calculated from paid orders)
        $topProducts = OrderItem::whereHas('order', function ($q) {
            $q->whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed']);
        })
            ->select('product_name_snapshot', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_name_snapshot')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Low stock alerts
        $lowStockVariants = ProductVariant::with('product')
            ->where('stock', '<=', 5)
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();

        // Overall Summary
        $summary = [
            'total_revenue' => Order::whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed'])->sum('total'),
            'total_orders' => Order::count(),
            'paid_orders' => Order::whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed'])->count(),
            'avg_order_value' => Order::whereIn('status', ['paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed'])->avg('total') ?? 0,
        ];

        return view('admin.reports.index', compact(
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'topProducts',
            'lowStockVariants',
            'summary',
            'days'
        ));
    }
}
