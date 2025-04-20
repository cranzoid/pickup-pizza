                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                <p><strong>Pickup Date:</strong> {{ $order->pickup_time->format('F j, Y') }}</p>
                <p><strong>Pickup Time:</strong> {{ $order->pickup_time->format('g:i a') }}</p>

                @if($order->order_status == 'ready')
                <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin-top: 20px; margin-bottom: 20px;">
                    <h4 style="margin-top: 0; color: #28a745;">Pickup Instructions</h4>
                    <p>Please arrive at our store at your selected pickup time. Your order will be ready and waiting for you.</p>
                    <p>If you have any questions, please call us at {{ App\Models\Setting::get('business_phone', '(905) 547-5777') }}.</p>
                    <p><strong>Store Address:</strong> {{ App\Models\Setting::get('business_address', '55 Parkdale Ave North, Hamilton, ON L8H 5W7') }}</p>
                </div>
                @endif 

        <div class="footer">
            <p>&copy; {{ date('Y') }} PISA Pizza. All rights reserved.</p>
            <p>{{ App\Models\Setting::get('business_address', '55 Parkdale Ave North, Hamilton, ON L8H 5W7') }} | {{ App\Models\Setting::get('business_phone', '(905) 547-5777') }} | {{ App\Models\Setting::get('business_email', 'support@pisapizza.ca') }}</p>
        </div> 