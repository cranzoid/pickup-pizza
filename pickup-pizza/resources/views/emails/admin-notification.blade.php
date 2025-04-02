<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PISA Pizza Admin Notification</title>
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
            margin: 0;
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
        .alert {
            padding: 15px;
            border-left: 4px solid #dc3545;
            margin-bottom: 20px;
            background-color: #f8d7da;
            color: #721c24;
        }
        .alert-warning {
            border-left: 4px solid #ffc107;
            background-color: #fff3cd;
            color: #856404;
        }
        .alert-info {
            border-left: 4px solid #17a2b8;
            background-color: #d1ecf1;
            color: #0c5460;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        table th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PISA Pizza Admin</h1>
        </div>
        
        <div class="content">
            <h2>{{ $subject }}</h2>
            
            @if(isset($alertType) && !empty($alertType))
                <div class="alert alert-{{ $alertType }}">
                    {{ $alertMessage }}
                </div>
            @endif
            
            @if(isset($message) && !empty($message))
                <p>{{ $message }}</p>
            @endif
            
            @if(isset($order) && $order)
                <h3>Related Order Information</h3>
                <table>
                    <tr>
                        <th>Order Number</th>
                        <td>{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td>{{ $order->name }}</td>
                    </tr>
                    <tr>
                        <th>Order Date</th>
                        <td>{{ $order->created_at->format('F j, Y, g:i a') }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>${{ number_format($order->total, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst($order->status) }}</td>
                    </tr>
                </table>
                
                <a href="{{ url('/admin/orders/' . $order->id) }}" class="action-button">View Order Details</a>
            @endif
            
            @if(isset($actionText) && isset($actionUrl))
                <p style="margin-top: 30px;">
                    <a href="{{ $actionUrl }}" class="action-button">{{ $actionText }}</a>
                </p>
            @endif
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} PISA Pizza. All rights reserved.</p>
            <p>This is an automated system notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 