<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
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
            <h2>Thank You for Your Order!</h2>
            <p>Dear {{ $order->customer_name }},</p>
            <p>We've received your order and are preparing it for pickup. Please find your order details below:</p>
            
            <div class="order-details">
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                <p><strong>Pickup Date:</strong> {{ date('F j, Y', strtotime($order->pickup_date)) }}</p>
                <p><strong>Pickup Time:</strong> {{ date('g:i a', strtotime($order->pickup_time)) }}</p>
                <p><strong>Payment Method:</strong> {{ $order->payment_method === 'credit_card' ? 'Credit Card (Paid)' : 'Pay in Store' }}</p>
            </div>
            
            <h3>Order Summary</h3>
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
                    @foreach($order->items as $item)
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
            
            <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #17a2b8; margin-bottom: 20px;">
                <h4 style="margin-top: 0; color: #17a2b8;">Pickup Instructions</h4>
                <p>Please arrive at our store at your selected pickup time. Your order will be ready and waiting for you. If you have any questions or need to make changes to your order, please call us at (416) 555-1234.</p>
                <p><strong>Store Address:</strong> 123 Pizza Street, Toronto, ON M4M 1H1</p>
            </div>
            
            <p>Thank you for choosing PISA Pizza! We appreciate your business.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} PISA Pizza. All rights reserved.</p>
            <p>123 Pizza Street, Toronto, ON M4M 1H1 | (416) 555-1234 | info@pisapizza.ca</p>
        </div>
    </div>
</body>
</html> 