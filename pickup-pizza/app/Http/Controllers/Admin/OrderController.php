<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $date = $request->get('date');
        
        $query = Order::query()->with('items');
        
        // Filter by status
        if ($status) {
            $query->where('order_status', $status);
        }
        
        // Filter by date
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        
        $orders = $query->orderBy('created_at', 'desc')
                       ->paginate(20)
                       ->withQueryString();
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load('items');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,picked_up,cancelled'
        ]);
        
        $previousStatus = $order->order_status;
        $order->order_status = $request->status;
        $order->save();
        
        // Send status update email for significant status changes
        if ($previousStatus != $request->status && in_array($request->status, ['preparing', 'ready', 'cancelled'])) {
            \Mail::to($order->customer_email)->send(new \App\Mail\OrderStatusUpdate($order));
        }
        
        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }
    
    /**
     * Generate an order report.
     */
    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Get orders within date range
        $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        // Calculate total revenue
        $totalRevenue = $orders->sum('total');
        
        // Calculate average order value
        $averageOrderValue = $orders->count() > 0 ? $totalRevenue / $orders->count() : 0;
        
        // Get orders by status
        $ordersByStatus = $orders->groupBy('order_status')
                                ->map(function ($statusOrders) {
                                    return $statusOrders->count();
                                });
        
        // Get orders by payment method
        $ordersByPaymentMethod = $orders->groupBy('payment_method')
                                      ->map(function ($paymentOrders) {
                                          return $paymentOrders->count();
                                      });
        
        // Get top products
        $topProducts = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productName = $item->name;
                if (!isset($topProducts[$productName])) {
                    $topProducts[$productName] = [
                        'quantity' => 0,
                        'revenue' => 0
                    ];
                }
                
                $topProducts[$productName]['quantity'] += $item->quantity;
                $topProducts[$productName]['revenue'] += $item->subtotal;
            }
        }
        
        // Sort products by quantity
        uasort($topProducts, function ($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });
        
        // Limit to top 10
        $topProducts = array_slice($topProducts, 0, 10, true);
        
        return view('admin.orders.report', compact(
            'startDate',
            'endDate',
            'orders',
            'totalRevenue',
            'averageOrderValue',
            'ordersByStatus',
            'ordersByPaymentMethod',
            'topProducts'
        ));
    }
    
    /**
     * Export orders to CSV.
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Get orders within date range
        $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        // Create CSV
        $filename = 'orders-' . $startDate . '-to-' . $endDate . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $columns = [
            'Order #', 'Date', 'Customer', 'Email', 'Phone', 'Items', 'Subtotal', 'Tax', 'Discount', 'Total', 'Status', 'Payment Method', 'Payment Status'
        ];
        
        $callback = function() use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($orders as $order) {
                $items = $order->items->count() . ' items';
                
                $row = [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->customer_name,
                    $order->customer_email,
                    $order->customer_phone,
                    $items,
                    number_format($order->subtotal, 2),
                    number_format($order->tax_amount, 2),
                    number_format($order->discount_amount, 2),
                    number_format($order->total, 2),
                    ucfirst($order->order_status),
                    ucfirst(str_replace('_', ' ', $order->payment_method)),
                    ucfirst($order->payment_status)
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send a payment failure notification to admin.
     */
    public function sendPaymentFailureNotification(Order $order, $errorMessage)
    {
        // Get admin email from settings or use a default
        $adminEmail = config('mail.admin_email', 'admin@pisapizza.ca');
        
        \Mail::to($adminEmail)->send(new \App\Mail\AdminNotification(
            'Payment Processing Failed',
            'There was an issue processing payment for an order.',
            'error',
            $errorMessage,
            $order,
            'View Order Details',
            url('/admin/orders/' . $order->id)
        ));
        
        return redirect()->route('admin.orders.show', $order)
            ->with('error', 'Payment processing failed. Admin has been notified.');
    }
} 