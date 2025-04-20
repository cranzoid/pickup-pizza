                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                <p><strong>Pickup Date:</strong> {{ $order->pickup_time->format('F j, Y') }}</p>
                <p><strong>Pickup Time:</strong> {{ $order->pickup_time->format('g:i a') }}</p>
                <p><strong>Payment Method:</strong> {{ $order->payment_method === 'credit_card' ? 'Credit Card (Paid)' : 'Pay in Store' }}</p>
                <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PISA Pizza. All rights reserved.</p>
            <p>{{ App\Models\Setting::get('business_address', '55 Parkdale Ave North, Hamilton, ON L8H 5W7') }} | {{ App\Models\Setting::get('business_phone', '(905) 547-5777') }} | {{ App\Models\Setting::get('business_email', 'support@pisapizza.ca') }}</p>
        </div> 