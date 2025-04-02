<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #f8f9fa;
            padding: 10px 20px;
            text-align: center;
            border-bottom: 3px solid #dc3545;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background: #f8f9fa;
            padding: 10px 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
            background-color: #17a2b8;
        }
        .status-ready {
            background-color: #28a745;
        }
        .status-preparing {
            background-color: #ffc107;
            color: #333;
        }
        .status-pending {
            background-color: #6c757d;
        }
        .status-cancelled {
            background-color: #dc3545;
        }
        .status-picked-up {
            background-color: #343a40;
        }
        h1 {
            color: #dc3545;
        }
        h2 {
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PISA Pizza</h1>
        </div>
        
        <div class="content">
            <h2>Order Status Update</h2>
            <p>Dear {{ $order->name }},</p>
            
            @if($order->status == 'ready')
            <p>Great news! Your order is now <span class="status-badge status-ready">READY FOR PICKUP</span></p>
            <p>You can come to our store to pick up your order. We're looking forward to serving you!</p>
            @elseif($order->status == 'preparing')
            <p>Your order is now <span class="status-badge status-preparing">BEING PREPARED</span></p>
            <p>Our kitchen team is working on your order, and it will be ready for pickup soon.</p>
            @elseif($order->status == 'cancelled')
            <p>Your order has been <span class="status-badge status-cancelled">CANCELLED</span></p>
            <p>If you didn't request this cancellation, please contact us immediately.</p>
            @else
            <p>Your order status has been updated to <span class="status-badge">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span></p>
            @endif
            
            <div class="order-details">
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                <p><strong>Pickup Date:</strong> {{ date('F j, Y', strtotime($order->pickup_date)) }}</p>
                <p><strong>Pickup Time:</strong> {{ date('g:i a', strtotime($order->pickup_time)) }}</p>
            </div>
            
            @if($order->status == 'ready')
            <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin-top: 20px; margin-bottom: 20px;">
                <h4 style="margin-top: 0; color: #28a745;">Pickup Instructions</h4>
                <p>Please arrive at our store at your selected pickup time. Your order will be ready and waiting for you.</p>
                <p>If you have any questions, please call us at (416) 555-1234.</p>
                <p><strong>Store Address:</strong> 123 Pizza Street, Toronto, ON M4M 1H1</p>
            </div>
            @endif
            
            <p>Thank you for choosing PISA Pizza! We appreciate your business.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} PISA Pizza. All rights reserved.</p>
            <p>123 Pizza Street, Toronto, ON M4M 1H1 | (416) 555-1234 | info@pisapizza.ca</p>
        </div>
    </div>
</body>
</html> 