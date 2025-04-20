<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Today's stats
        $todayOrdersCount = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');
        $todayAverageOrder = $todayOrdersCount > 0 
            ? $todayRevenue / $todayOrdersCount 
            : 0;
        
        // Yesterday's stats for comparison
        $yesterdayRevenue = Order::whereDate('created_at', today()->subDay())
            ->where('payment_status', 'paid')
            ->sum('total');
        $revenueTrend = $yesterdayRevenue > 0 
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 
            : 100;
        
        // Last week's stats for comparison
        $lastWeekRevenue = Order::whereBetween('created_at', [
                today()->subDays(13), 
                today()->subDays(7)
            ])
            ->where('payment_status', 'paid')
            ->sum('total');
        $thisWeekRevenue = Order::whereBetween('created_at', [
                today()->subDays(6), 
                today()
            ])
            ->where('payment_status', 'paid')
            ->sum('total');
        $weeklyRevenueTrend = $lastWeekRevenue > 0 
            ? (($thisWeekRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100 
            : 100;
        
        // Orders by status
        $pendingOrdersCount = Order::where('order_status', 'pending')->count();
        $preparingOrdersCount = Order::where('order_status', 'preparing')->count();
        $readyOrdersCount = Order::where('order_status', 'ready')->count();
        $pickedUpOrdersCount = Order::where('order_status', 'picked_up')->count();
        
        // Recent orders
        $recentOrders = Order::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Upcoming pickups for today
        $upcomingPickups = Order::whereDate('pickup_time', today())
            ->whereIn('order_status', ['pending', 'preparing', 'ready'])
            ->orderBy('pickup_time')
            ->take(10)
            ->get();
        
        // Orders for chart - last 7 days
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();
        
        $ordersByDay = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate->copy()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
            
        $revenueByDay = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate->copy()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        
        // Create array for all 7 days with zeros for days with no orders
        $orderChartData = [];
        $revenueChartData = [];
        $orderChartLabels = [];
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();
            $orderChartLabels[] = $date->format('M d');
            $orderChartData[] = $ordersByDay->has($dateString) ? $ordersByDay->get($dateString)->count : 0;
            $revenueChartData[] = $revenueByDay->has($dateString) ? $revenueByDay->get($dateString)->total : 0;
        }
        
        // Popular items
        $popularItems = OrderItem::select('name', DB::raw('SUM(quantity) as count'))
            ->groupBy('name')
            ->orderByDesc('count')
            ->take(5)
            ->get();
            
        // Payment method distribution
        $paymentMethods = Order::select('payment_method', DB::raw('COUNT(*) as count'))
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('payment_method')
            ->orderBy('count', 'desc')
            ->get();
        
        // Chart colors
        $chartColors = [
            '#4e73df', // Primary
            '#1cc88a', // Success
            '#36b9cc', // Info
            '#f6c23e', // Warning
            '#e74a3b', // Danger
        ];
        
        return view('admin.dashboard', compact(
            'todayOrdersCount',
            'todayRevenue',
            'todayAverageOrder',
            'revenueTrend',
            'thisWeekRevenue',
            'weeklyRevenueTrend',
            'pendingOrdersCount',
            'preparingOrdersCount',
            'readyOrdersCount',
            'pickedUpOrdersCount',
            'recentOrders',
            'upcomingPickups',
            'orderChartLabels',
            'orderChartData',
            'revenueChartData',
            'popularItems',
            'paymentMethods',
            'chartColors'
        ));
    }
}
