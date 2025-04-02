<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Order Notification</title>
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
            background: #343a40;
            padding: 10px 20px;
            text-align: center;
            border-bottom: 3px solid #dc3545;
        }
        .header h1 {
            color: #fff;
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
        .order-details {
            margin-bottom: 20px;
        }
        .order-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-items th, .order-items td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .order-items th {
            background-color: #f8f9fa;
        }
        .total-row {
            font-weight: bold;
        }
        .action-button {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
        h2 {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PISA Pizza Admin</h1>
        </div>
        
        <div class="content">
            <h2>New Order Received!</h2>
            <p>A new order has been placed and requires processing.</p>
            
            <div class="order-details">
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                <p><strong>Customer:</strong> {{ $order->name }}</p>
                <p><strong>Phone:</strong> {{ $order->phone }}</p>
                <p><strong>Email:</strong> {{ $order->email }}</p>
                <p><strong>Pickup Date:</strong> {{ date('F j, Y', strtotime($order->pickup_date)) }}</p>
                <p><strong>Pickup Time:</strong> {{ date('g:i a', strtotime($order->pickup_time)) }}</p>
                <p><strong>Payment Method:</strong> {{ $order->payment_method === 'credit_card' ? 'Credit Card (Paid)' : 'Pay in Store' }}</p>
                <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
            </div>
            
            <h3>Order Items</h3>
            <table class="order-items">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Details</th>
                        <th style="text-align: right;">Price</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        @php
                            $options = json_decode($item->options, true);
                        @endphp
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>
                                @if(!empty($options['size']))
                                    <span style="display: block;">Size: {{ ucfirst($options['size']) }}</span>
                                @endif
                                
                                @if(!empty($options['toppings']) && count($options['toppings']) > 0)
                                    <span style="display: block;">Toppings: {{ implode(', ', $options['toppings']) }}</span>
                                @endif
                                
                                @if(!empty($options['extra_toppings']) && count($options['extra_toppings']) > 0)
                                    <span style="display: block;">Extra Toppings: {{ implode(', ', $options['extra_toppings']) }}</span>
                                @endif
                                
                                @if(!empty($options['notes']))
                                    <span style="display: block; font-style: italic;">{{ $options['notes'] }}</span>
                                @endif
                            </td>
                            <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">${{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;"><strong>Subtotal:</strong></td>
                        <td style="text-align: right;">${{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    
                    @if($order->tax > 0)
                        <tr>
                            <td colspan="4" style="text-align: right;"><strong>Tax:</strong></td>
                            <td style="text-align: right;">${{ number_format($order->tax, 2) }}</td>
                        </tr>
                    @endif
                    
                    @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="4" style="text-align: right; color: green;"><strong>Discount:</strong></td>
                            <td style="text-align: right; color: green;">-${{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                        <td style="text-align: right;"><strong>${{ number_format($order->total, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <p>Please login to the admin dashboard to manage this order:</p>
            <a href="{{ url('/admin/orders/' . $order->id) }}" class="action-button">View Order Details</a>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} PISA Pizza. All rights reserved.</p>
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html> 