<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Combo;
use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show the sales dashboard.
     */
    public function dashboard()
    {
        // Get summary statistics for today
        $today = Carbon::today();
        $todayStats = $this->getDayStats($today);
        
        // Get summary statistics for yesterday
        $yesterday = Carbon::yesterday();
        $yesterdayStats = $this->getDayStats($yesterday);
        
        // Get sales for last 7 days
        $last7Days = $this->getDateRangeStats(
            Carbon::today()->subDays(6),
            Carbon::today()
        );
        
        // Get sales for last 30 days
        $last30Days = $this->getDateRangeStats(
            Carbon::today()->subDays(29),
            Carbon::today()
        );
        
        // Get sales for current month
        $currentMonthStart = Carbon::today()->startOfMonth();
        $currentMonthStats = $this->getDateRangeStats(
            $currentMonthStart,
            Carbon::today()
        );
        
        // Get daily sales for chart
        $dailySales = $this->getDailySalesData(Carbon::today()->subDays(30), Carbon::today());
        
        return view('admin.reports.dashboard', compact(
            'todayStats',
            'yesterdayStats',
            'last7Days',
            'last30Days',
            'currentMonthStats',
            'dailySales'
        ));
    }
    
    /**
     * Show the orders report.
     */
    public function orders(Request $request)
    {
        $query = Order::query()->with(['user', 'items.product', 'discount']);
        
        // Apply filters
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('min_amount')) {
            $query->where('total', '>=', $request->min_amount);
        }
        
        if ($request->filled('max_amount')) {
            $query->where('total', '<=', $request->max_amount);
        }
        
        if ($request->filled('discount_code')) {
            $discount = Discount::where('code', $request->discount_code)->first();
            if ($discount) {
                $query->where('discount_id', $discount->id);
            }
        }
        
        if ($request->filled('product_id')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }
        
        if ($request->filled('combo_id')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('combo_id', $request->combo_id);
            });
        }
        
        // Sort orders
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);
        
        $orders = $query->paginate(15)->withQueryString();
        
        // Get data for filter dropdowns
        $statuses = Order::distinct()->pluck('status');
        $paymentMethods = Order::distinct()->pluck('payment_method');
        $products = Product::orderBy('name')->get();
        $combos = Combo::orderBy('name')->get();
        
        return view('admin.reports.orders', compact(
            'orders',
            'statuses',
            'paymentMethods',
            'products',
            'combos'
        ));
    }
    
    /**
     * Show the products report.
     */
    public function products(Request $request)
    {
        // Set date range (default to last 30 days)
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->subDays(29)->startOfDay();
            
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();
        
        // Get top selling products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', function($join) {
                $join->on('order_items.item_id', '=', 'products.id')
                     ->where('order_items.item_type', '=', 'product');
            })
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.image_path as image',
                'products.price',
                'categories.name as category',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.unit_price * order_items.quantity) as total_revenue')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('order_items.item_type', '=', 'product')
            ->where('orders.order_status', '<>', 'cancelled')
            ->groupBy('products.id', 'products.name', 'products.image_path', 'products.price', 'categories.name')
            ->orderByDesc('total_quantity')
            ->limit(50)
            ->get();
        
        // Comment out the combos query since the combo_id field doesn't exist
        /*
        // Get top selling combos
        $topCombos = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('combos', 'order_items.combo_id', '=', 'combos.id')
            ->select(
                'combos.id',
                'combos.name',
                'combos.image_path as image',
                'combos.price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.unit_price * order_items.quantity) as total_revenue'),
                DB::raw('combos.regular_price')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotNull('order_items.combo_id')
            ->where('orders.order_status', '<>', 'cancelled')
            ->groupBy('combos.id', 'combos.name', 'combos.image_path', 'combos.price', 'combos.regular_price')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
        */
        
        // Initialize an empty collection for topCombos
        $topCombos = collect();
        
        // Get product sales by category
        $salesByCategory = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', function($join) {
                $join->on('order_items.item_id', '=', 'products.id')
                     ->where('order_items.item_type', '=', 'product');
            })
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.unit_price * order_items.quantity) as total_revenue')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('order_items.item_type', '=', 'product')
            ->where('orders.order_status', '<>', 'cancelled')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
        
        // Get products with low sales (sold less than 5 times)
        $lowSellingProducts = DB::table('products')
            ->leftJoin(DB::raw("(
                SELECT item_id as product_id, SUM(quantity) as sold_count, MAX(orders.created_at) as last_order_date,
                       SUM(order_items.unit_price * order_items.quantity) as revenue
                FROM order_items
                JOIN orders ON order_items.order_id = orders.id
                WHERE orders.created_at BETWEEN '{$startDate}' AND '{$endDate}'
                AND orders.order_status <> 'cancelled'
                AND order_items.item_type = 'product'
                GROUP BY item_id
            ) sales"), 'products.id', '=', 'sales.product_id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.image_path as image',
                'products.price',
                'categories.name as category',
                DB::raw('COALESCE(sales.sold_count, 0) as sold_count'),
                DB::raw('sales.revenue'),
                DB::raw('sales.last_order_date')
            )
            ->where('products.active', true)
            ->where(function($query) {
                $query->whereNull('sales.sold_count')
                      ->orWhere('sales.sold_count', '<', 5);
            })
            ->orderBy('sold_count')
            ->get();
            
        // Calculate total revenue for percentage calculations
        $totalOrderItemsRevenue = $topProducts->sum('total_revenue') + $topCombos->sum('total_revenue');
        
        // Add percentage calculations
        foreach ($topProducts as $product) {
            $product->percentage = $totalOrderItemsRevenue > 0 
                ? ($product->total_revenue / $totalOrderItemsRevenue) * 100 
                : 0;
        }
        
        foreach ($topCombos as $combo) {
            $combo->percentage = $totalOrderItemsRevenue > 0 
                ? ($combo->total_revenue / $totalOrderItemsRevenue) * 100 
                : 0;
        }
        
        foreach ($lowSellingProducts as $product) {
            $product->percentage = $product->revenue && $totalOrderItemsRevenue > 0 
                ? ($product->revenue / $totalOrderItemsRevenue) * 100 
                : 0;
        }
        
        // Get previous period data for comparisons
        $previousPeriodStart = (clone $startDate)->subDays($endDate->diffInDays($startDate) + 1);
        $previousPeriodEnd = (clone $startDate)->subDay();
        
        $previousPeriodStats = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.item_type', '=', 'product')
            ->whereBetween('orders.created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->where('orders.order_status', '<>', 'cancelled')
            ->select(
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.unit_price * order_items.quantity) as total_revenue')
            )
            ->first();
            
        // Prepare summary statistics
        $totalQuantity = $topProducts->sum('total_quantity');
        $totalRevenue = $topProducts->sum('total_revenue');
        $avgPrice = $totalQuantity > 0 ? $totalRevenue / $totalQuantity : 0;
        $categoryCount = $salesByCategory->count();
        
        // Calculate changes compared to previous period
        $previousQuantity = $previousPeriodStats->total_quantity ?? 0;
        $previousRevenue = $previousPeriodStats->total_revenue ?? 0;
        
        $quantityChange = $previousQuantity > 0 
            ? (($totalQuantity - $previousQuantity) / $previousQuantity) * 100 
            : 0;
            
        $revenueChange = $previousRevenue > 0 
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;
            
        $summary = [
            'total_quantity' => $totalQuantity,
            'total_revenue' => $totalRevenue,
            'avg_price' => $avgPrice,
            'category_count' => $categoryCount,
            'quantity_change' => $quantityChange,
            'revenue_change' => $revenueChange
        ];
        
        return view('admin.reports.products', compact(
            'topProducts',
            'topCombos',
            'salesByCategory',
            'lowSellingProducts',
            'startDate',
            'endDate',
            'summary'
        ));
    }
    
    /**
     * Show the discounts report.
     */
    public function discounts(Request $request)
    {
        // Set date range (default to last 90 days)
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->subDays(89)->startOfDay();
            
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();
        
        // Get discount usage statistics
        $discountStats = DB::table('orders')
            ->join('discounts', 'orders.discount_id', '=', 'discounts.id')
            ->select(
                'discounts.id',
                'discounts.name',
                'discounts.code',
                'discounts.type',
                'discounts.value',
                'discounts.max_uses',
                'discounts.expires_at',
                'discounts.active',
                DB::raw('COUNT(orders.id) as used_count'),
                DB::raw('SUM(orders.discount_amount) as total_discount_amount'),
                DB::raw('AVG(orders.discount_amount) as avg_discount_amount'),
                DB::raw('SUM(orders.total) as total_order_revenue'),
                DB::raw('AVG(orders.total) as average_order_value')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.order_status', '<>', 'cancelled')
            ->groupBy('discounts.id', 'discounts.name', 'discounts.code', 'discounts.type', 'discounts.value', 'discounts.max_uses', 'discounts.expires_at', 'discounts.active')
            ->orderByDesc('used_count')
            ->get();
        
        // Add ROI calculation and expiry status to each discount
        $now = Carbon::now();
        foreach ($discountStats as $discount) {
            // Calculate ROI (Return on Investment)
            $discount->roi = $discount->total_discount_amount > 0 
                ? $discount->total_order_revenue / $discount->total_discount_amount 
                : 0;
                
            // Check if discount is expired or max usage reached
            $discount->is_expired = $discount->expires_at && Carbon::parse($discount->expires_at)->isPast();
            $discount->is_max_reached = $discount->max_uses && $discount->used_count >= $discount->max_uses;
            
            // Additional metrics
            $discount->avg_items_per_order = 0; // This would require additional query to calculate
        }
        
        // Get overall statistics
        $allOrders = DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('order_status', '<>', 'cancelled')
            ->get();
            
        $ordersWithDiscount = $allOrders->where('discount_id', '!=', null);
        $ordersWithoutDiscount = $allOrders->where('discount_id', null);
        
        $totalOrders = $allOrders->count();
        $totalDiscountedOrders = $ordersWithDiscount->count();
        $totalOrdersRevenue = $allOrders->sum('total');
        $totalDiscountAmount = $ordersWithDiscount->sum('discount_amount');
        $discountedOrdersRevenue = $ordersWithDiscount->sum('total');
        
        // Calculate comparison metrics
        $avgDiscountedOrder = $totalDiscountedOrders > 0 ? $discountedOrdersRevenue / $totalDiscountedOrders : 0;
        $avgNonDiscountedOrder = $ordersWithoutDiscount->count() > 0 ? $ordersWithoutDiscount->sum('total') / $ordersWithoutDiscount->count() : 0;
        $avgOrderIncrease = $avgNonDiscountedOrder > 0 ? (($avgDiscountedOrder - $avgNonDiscountedOrder) / $avgNonDiscountedOrder) * 100 : 0;
        
        // Get overall ROI
        $overallROI = $totalDiscountAmount > 0 ? $discountedOrdersRevenue / $totalDiscountAmount : 0;
        
        // Discount counts
        $activeDiscountsCount = DB::table('discounts')
            ->where('active', true)
            ->where(function($query) use ($now) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', $now);
            })
            ->count();
            
        $totalDiscountsCount = DB::table('discounts')->count();
        
        // Get orders with discounts for the recent orders table
        $discountedOrders = DB::table('orders')
            ->join('discounts', 'orders.discount_id', '=', 'discounts.id')
            ->select(
                'orders.id',
                'orders.order_number',
                'orders.created_at',
                'orders.customer_name',
                'orders.customer_email',
                'orders.total',
                'orders.discount_amount',
                'discounts.code as discount_code'
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.order_status', '<>', 'cancelled')
            ->orderByDesc('orders.created_at')
            ->limit(10)
            ->get();
        
        // Get orders with discounts over time for chart
        $timeSeriesData = DB::table('orders')
            ->join('discounts', 'orders.discount_id', '=', 'discounts.id')
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.discount_amount) as discount_amount')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.order_status', '<>', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $chartData = [
            'dates' => $timeSeriesData->pluck('date')->toArray(),
            'counts' => $timeSeriesData->pluck('order_count')->toArray(),
            'amounts' => $timeSeriesData->pluck('discount_amount')->toArray(),
        ];
        
        // Prepare summary data
        $summary = [
            'total_discounted_orders' => $totalDiscountedOrders,
            'total_discount_amount' => $totalDiscountAmount,
            'average_discount' => $totalDiscountedOrders > 0 ? $totalDiscountAmount / $totalDiscountedOrders : 0,
            'discounted_orders_revenue' => $discountedOrdersRevenue,
            'avg_discounted_order' => $avgDiscountedOrder,
            'avg_non_discounted_order' => $avgNonDiscountedOrder,
            'avg_order_increase' => $avgOrderIncrease,
            'discount_order_percentage' => $totalOrders > 0 ? ($totalDiscountedOrders / $totalOrders) * 100 : 0,
            'discount_revenue_percentage' => $totalOrdersRevenue > 0 ? ($totalDiscountAmount / $totalOrdersRevenue) * 100 : 0,
            'active_discounts_count' => $activeDiscountsCount,
            'total_discounts_count' => $totalDiscountsCount,
            'overall_roi' => $overallROI
        ];
        
        return view('admin.reports.discounts', compact(
            'discountStats',
            'discountedOrders',
            'chartData',
            'summary',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Export orders report as CSV.
     */
    public function exportOrders(Request $request)
    {
        // Similar filter logic as the orders method
        $query = Order::query()->with(['user', 'items.product', 'discount']);
        
        // Apply filters (same as in orders method)
        // ...
        
        $orders = $query->get();
        
        // Generate CSV
        $filename = 'orders_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Order ID',
                'Date',
                'Customer',
                'Status',
                'Payment Method',
                'Items',
                'Subtotal',
                'Tax',
                'Discount',
                'Total'
            ]);
            
            // Add order data
            foreach ($orders as $order) {
                $row = [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user ? $order->user->name : 'Guest',
                    $order->status,
                    $order->payment_method,
                    $order->items->count(),
                    '$' . number_format($order->subtotal, 2),
                    '$' . number_format($order->tax, 2),
                    '$' . number_format($order->discount_amount, 2),
                    '$' . number_format($order->total, 2),
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get statistics for a specific day.
     */
    private function getDayStats(Carbon $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();
        
        $stats = [
            'date' => $date->format('Y-m-d'),
            'formatted_date' => $date->format('M d, Y'),
            'orders' => Order::whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
            'revenue' => Order::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->where('order_status', '<>', 'cancelled')
                ->sum('total'),
            'average_order' => 0,
            'completed_orders' => Order::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->where('status', 'completed')
                ->count(),
        ];
        
        // Calculate average order value
        if ($stats['orders'] > 0) {
            $stats['average_order'] = $stats['revenue'] / $stats['orders'];
        }
        
        return $stats;
    }
    
    /**
     * Get statistics for a date range.
     */
    private function getDateRangeStats(Carbon $startDate, Carbon $endDate)
    {
        $startOfRange = $startDate->copy()->startOfDay();
        $endOfRange = $endDate->copy()->endOfDay();
        
        $stats = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'formatted_start_date' => $startDate->format('M d, Y'),
            'formatted_end_date' => $endDate->format('M d, Y'),
            'orders' => Order::whereBetween('created_at', [$startOfRange, $endOfRange])->count(),
            'revenue' => Order::whereBetween('created_at', [$startOfRange, $endOfRange])
                ->where('order_status', '<>', 'cancelled')
                ->sum('total'),
            'average_order' => 0,
            'completed_orders' => Order::whereBetween('created_at', [$startOfRange, $endOfRange])
                ->where('status', 'completed')
                ->count(),
        ];
        
        // Calculate average order value
        if ($stats['orders'] > 0) {
            $stats['average_order'] = $stats['revenue'] / $stats['orders'];
        }
        
        return $stats;
    }
    
    /**
     * Get daily sales data for charts.
     */
    private function getDailySalesData(Carbon $startDate, Carbon $endDate)
    {
        $dailySales = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->where('order_status', '<>', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'dates' => $dailySales->pluck('date')->toArray(),
            'orders' => $dailySales->pluck('order_count')->toArray(),
            'revenue' => $dailySales->pluck('revenue')->toArray(),
        ];
    }

    /**
     * Export products report as CSV.
     */
    public function exportProducts(Request $request)
    {
        // Set date range (default to last 30 days)
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->subDays(29)->startOfDay();
            
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();
        
        // Get top selling products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', function($join) {
                $join->on('order_items.item_id', '=', 'products.id')
                     ->where('order_items.item_type', '=', 'product');
            })
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'categories.name as category',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.unit_price * order_items.quantity) as total_revenue')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('order_items.item_type', '=', 'product')
            ->where('orders.order_status', '<>', 'cancelled')
            ->groupBy('products.id', 'products.name', 'categories.name', 'products.price')
            ->orderByDesc('total_quantity')
            ->get();
        
        // Calculate total revenue to get percentages
        $totalRevenue = $topProducts->sum('total_revenue');
        
        // Add percentage calculation
        foreach ($topProducts as $product) {
            $product->percentage = $totalRevenue > 0 ? ($product->total_revenue / $totalRevenue) * 100 : 0;
        }
        
        // Generate CSV
        $filename = 'products_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($topProducts, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers with date range
            fputcsv($file, [
                'Product Sales Report', 
                'Date Range: ' . $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')
            ]);
            
            fputcsv($file, []); // Empty row
            
            // Add column headers
            fputcsv($file, [
                'Product ID',
                'Product Name',
                'Category',
                'Base Price',
                'Units Sold',
                'Total Revenue',
                'Revenue Percentage'
            ]);
            
            // Add product data
            foreach ($topProducts as $product) {
                $row = [
                    $product->id,
                    $product->name,
                    $product->category ?? 'Uncategorized',
                    '$' . number_format($product->price, 2),
                    $product->total_quantity,
                    '$' . number_format($product->total_revenue, 2),
                    number_format($product->percentage, 2) . '%'
                ];
                
                fputcsv($file, $row);
            }
            
            // Add low selling products section
            fputcsv($file, []); // Empty row
            fputcsv($file, ['Low Performing Products (Less than 5 units sold)']);
            
            // Get low selling products
            $lowSellingProducts = DB::table('products')
                ->leftJoin(DB::raw("(
                    SELECT item_id as product_id, SUM(quantity) as sold_count
                    FROM order_items
                    JOIN orders ON order_items.order_id = orders.id
                    WHERE orders.created_at BETWEEN '{$startDate}' AND '{$endDate}'
                    AND orders.order_status <> 'cancelled'
                    AND order_items.item_type = 'product'
                    GROUP BY item_id
                ) sales"), 'products.id', '=', 'sales.product_id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.price',
                    'categories.name as category',
                    DB::raw('COALESCE(sales.sold_count, 0) as sold_count')
                )
                ->where('products.active', true)
                ->where(function($query) {
                    $query->whereNull('sales.sold_count')
                          ->orWhere('sales.sold_count', '<', 5);
                })
                ->orderBy('sold_count')
                ->get();
                
            // Add column headers for low selling products
            fputcsv($file, [
                'Product ID',
                'Product Name',
                'Category',
                'Base Price',
                'Units Sold'
            ]);
            
            // Add low selling product data
            foreach ($lowSellingProducts as $product) {
                $row = [
                    $product->id,
                    $product->name,
                    $product->category ?? 'Uncategorized',
                    '$' . number_format($product->price, 2),
                    $product->sold_count
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export discounts report as CSV.
     */
    public function exportDiscounts(Request $request)
    {
        // Set date range (default to last 90 days)
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->subDays(89)->startOfDay();
            
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfDay();
        
        // Get discount usage statistics
        $discountStats = DB::table('orders')
            ->join('discounts', 'orders.discount_id', '=', 'discounts.id')
            ->select(
                'discounts.id',
                'discounts.name',
                'discounts.code',
                'discounts.type',
                'discounts.value',
                'discounts.max_uses',
                'discounts.expires_at',
                DB::raw('COUNT(orders.id) as usage_count'),
                DB::raw('SUM(orders.discount_amount) as total_discount_amount'),
                DB::raw('AVG(orders.discount_amount) as avg_discount_amount'),
                DB::raw('SUM(orders.total) as total_order_amount'),
                DB::raw('AVG(orders.total) as avg_order_amount')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.order_status', '<>', 'cancelled')
            ->groupBy('discounts.id', 'discounts.name', 'discounts.code', 'discounts.type', 'discounts.value', 'discounts.max_uses', 'discounts.expires_at')
            ->orderByDesc('usage_count')
            ->get();
            
        // Generate CSV
        $filename = 'discounts_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($discountStats, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers with date range
            fputcsv($file, [
                'Discount Usage Report', 
                'Date Range: ' . $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')
            ]);
            
            fputcsv($file, []); // Empty row
            
            // Add column headers
            fputcsv($file, [
                'Discount ID',
                'Discount Name',
                'Discount Code',
                'Type',
                'Value',
                'Usage Count',
                'Max Uses',
                'Expiry Date',
                'Total Discount Amount',
                'Avg Discount Amount',
                'Total Order Amount',
                'Avg Order Value',
                'ROI'
            ]);
            
            // Add discount data
            foreach ($discountStats as $discount) {
                $discountValue = $discount->type === 'percentage' 
                    ? $discount->value . '%' 
                    : '$' . number_format($discount->value, 2);
                    
                $expiryDate = $discount->expires_at 
                    ? Carbon::parse($discount->expires_at)->format('M d, Y') 
                    : 'No Expiry';
                    
                $roi = $discount->total_discount_amount > 0 
                    ? ($discount->total_order_amount / $discount->total_discount_amount) 
                    : 0;
                
                $row = [
                    $discount->id,
                    $discount->name,
                    $discount->code,
                    ucfirst($discount->type),
                    $discountValue,
                    $discount->usage_count,
                    $discount->max_uses ?? 'Unlimited',
                    $expiryDate,
                    '$' . number_format($discount->total_discount_amount, 2),
                    '$' . number_format($discount->avg_discount_amount, 2),
                    '$' . number_format($discount->total_order_amount, 2),
                    '$' . number_format($discount->avg_order_amount, 2),
                    number_format($roi, 2) . 'x'
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 