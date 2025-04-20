@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Order Confirmation</h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <h2>Thank You for Your Order!</h2>
                        <p class="lead">Your order has been received and is being processed.</p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                            <p><strong>Order Status:</strong> <span class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span></p>
                            <p><strong>Order Type:</strong> {{ ucfirst($order->order_type) }}</p>
                            <p><strong>Payment Method:</strong> {{ $order->payment_method === 'credit_card' ? 'Credit Card' : 'Pay in Store' }}</p>
                            <p><strong>Payment Status:</strong> 
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            @if($order->name)
                                <p><strong>Name:</strong> {{ $order->name }}</p>
                            @endif
                            @if($order->email)
                                <p><strong>Email:</strong> {{ $order->email }}</p>
                            @endif
                            @if($order->phone)
                                <p><strong>Phone:</strong> {{ $order->phone }}</p>
                            @endif
                            
                            @if($order->order_type === 'pickup')
                                <h6 class="mt-3">Pickup Details</h6>
                                <p><strong>Date:</strong> {{ $order->pickup_time ? $order->pickup_time->format('F j, Y') : 'Not specified' }}</p>
                                <p><strong>Time:</strong> {{ $order->pickup_time ? $order->pickup_time->format('g:i a') : 'Not specified' }}</p>
                                @php
                                    $settings = new \App\Models\Setting();
                                    $store_address = $settings->get('business_address', '123 Pizza St, Toronto, ON M4M 1H1');
                                    // Extract city and postal code from address if available
                                    $addressParts = explode(',', $store_address);
                                    $cityPostalParts = count($addressParts) > 1 ? explode(' ', trim($addressParts[1]), 2) : ['Toronto', 'ON M4M 1H1'];
                                    $city = $cityPostalParts[0] ?? 'Toronto';
                                    $postal = count($cityPostalParts) > 1 ? $cityPostalParts[1] : 'ON M4M 1H1';
                                @endphp
                                <p><strong>Store Address:</strong> {{ $store_address }}</p>
                                <p><strong>City:</strong> {{ $city }}</p>
                                <p><strong>Postal Code:</strong> {{ $postal }}</p>
                            @else
                                <h6 class="mt-3">Pickup Details</h6>
                                <p><strong>Date:</strong> {{ $order->pickup_time ? $order->pickup_time->format('F j, Y') : 'Not specified' }}</p>
                                <p><strong>Time:</strong> {{ $order->pickup_time ? $order->pickup_time->format('g:i a') : 'Not specified' }}</p>
                                @php
                                    $settings = new \App\Models\Setting();
                                    $store_address = $settings->get('business_address', '123 Pizza St, Toronto, ON M4M 1H1');
                                    // Extract city and postal code from address if available
                                    $addressParts = explode(',', $store_address);
                                    $cityPostalParts = count($addressParts) > 1 ? explode(' ', trim($addressParts[1]), 2) : ['Toronto', 'ON M4M 1H1'];
                                    $city = $cityPostalParts[0] ?? 'Toronto';
                                    $postal = count($cityPostalParts) > 1 ? $cityPostalParts[1] : 'ON M4M 1H1';
                                @endphp
                                <p><strong>Store Address:</strong> {{ $store_address }}</p>
                                <p><strong>City:</strong> {{ $city }}</p>
                                <p><strong>Postal Code:</strong> {{ $postal }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Details</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php
                                        $options = json_decode($item->options, true) ?? [];
                                    @endphp
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if(!empty($options['size']))
                                                <span class="d-block"><strong>Size:</strong> {{ ucfirst($options['size']) }}</span>
                                            @endif
                                            
                                            @if(!empty($options['crust']))
                                                <span class="d-block"><strong>Crust:</strong> {{ ucfirst($options['crust']) }}</span>
                                            @endif
                                            
                                            @if(!empty($options['base']))
                                                <span class="d-block"><strong>Base:</strong> {{ ucfirst($options['base']) }}</span>
                                            @endif
                                            
                                            @if(!empty($options['toppings']) && count($options['toppings']) > 0)
                                                <span class="d-block"><strong>Toppings:</strong> {{ implode(', ', $options['toppings']) }}</span>
                                            @endif
                                            
                                            @if(!empty($options['extra_toppings']) && count($options['extra_toppings']) > 0)
                                                <span class="d-block"><strong>Extra Toppings:</strong> {{ implode(', ', $options['extra_toppings']) }}</span>
                                            @endif
                                            
                                            @if(!empty($options['includes']) && count($options['includes']) > 0)
                                                <span class="d-block"><strong>Includes:</strong> {{ implode(', ', $options['includes']) }}</span>
                                            @endif

                                            @if(!empty($options['cheese']) && $options['cheese'] == true)
                                                <span class="d-block"><strong>Add Cheese:</strong> Yes</span>
                                            @endif
                                            
                                            <!-- Wing Options -->
                                            @if(!empty($options['wing_flavors']) || !empty($options['wings_flavor']))
                                                @php
                                                    $wingFlavor = $options['wing_flavors'] ?? $options['wings_flavor'] ?? '';
                                                    $wingFlavors = [
                                                        '1' => 'Plain',
                                                        '2' => 'Mild',
                                                        '3' => 'Medium',
                                                        '4' => 'Hot',
                                                        '5' => 'Suicide',
                                                        '6' => 'Honey Garlic',
                                                        '7' => 'BBQ',
                                                        '8' => 'Sweet & Sour',
                                                        '9' => 'Honey Hot',
                                                        '10' => 'Dry Cajun'
                                                    ];
                                                    if (is_numeric($wingFlavor) && isset($wingFlavors[$wingFlavor])) {
                                                        $wingFlavor = $wingFlavors[$wingFlavor];
                                                    }
                                                @endphp
                                                <span class="d-block"><strong>Wing Flavor:</strong> {{ $wingFlavor }}</span>
                                            @endif
                                            
                                            @if(!empty($options['add_extra_wings']) && $options['add_extra_wings'] == 'yes')
                                                <span class="d-block"><strong>Extra Wings:</strong> Yes</span>
                                            @endif
                                            
                                            <!-- Garlic Bread Options -->
                                            @if(!empty($options['garlic_bread']) && $options['garlic_bread'] == 'yes')
                                                <span class="d-block"><strong>Garlic Bread:</strong> Yes
                                                    @if(!empty($options['garlic_bread_add_cheese']) && $options['garlic_bread_add_cheese'] == 'yes')
                                                        (with Cheese)
                                                    @endif
                                                </span>
                                            @endif
                                            
                                            <!-- Pop Selections -->
                                            @php
                                                $pops = [];
                                                foreach(['pop1', 'pop2', 'pop3', 'pop4'] as $pop) {
                                                    if(!empty($options[$pop]) && $options[$pop] != 'none') {
                                                        $pops[] = $options[$pop];
                                                    }
                                                }
                                            @endphp
                                            
                                            @if(!empty($pops))
                                                <span class="d-block"><strong>Pop Selections:</strong> {{ implode(', ', $pops) }}</span>
                                            @endif
                                            
                                            <!-- 2-for-1 Pizza Options -->
                                            @php
                                                $hasTwoForOneOptions = false;
                                                foreach($options as $key => $value) {
                                                    if(strpos($key, 'first_pizza_') === 0 || strpos($key, 'second_pizza_') === 0) {
                                                        $hasTwoForOneOptions = true;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            
                                            @if($hasTwoForOneOptions)
                                                <strong class="d-block mt-2">2-for-1 Special:</strong>
                                                <div class="ms-3">
                                                    <!-- First Pizza -->
                                                    <strong class="d-block mt-1">First Pizza:</strong>
                                                    <ul class="ps-3 mb-2">
                                                        @if(!empty($options['first_pizza_size']))
                                                            <li>Size: {{ $options['first_pizza_size'] }}</li>
                                                        @elseif(!empty($options['size']))
                                                            <li>Size: {{ $options['size'] }}</li>
                                                        @endif
                                                        
                                                        @if(!empty($options['first_pizza_toppings']))
                                                            <li>Toppings: {{ implode(', ', $options['first_pizza_toppings']) }}</li>
                                                        @endif
                                                        
                                                        @if(!empty($options['first_pizza_extra_toppings']))
                                                            <li>Extra Toppings: {{ implode(', ', $options['first_pizza_extra_toppings']) }}</li>
                                                        @endif
                                                        
                                                        @if(!empty($options['first_pizza_base']))
                                                            <li>Base: {{ $options['first_pizza_base'] }}</li>
                                                        @endif
                                                        
                                                        @if(!empty($options['first_pizza_crust']))
                                                            <li>Crust: {{ $options['first_pizza_crust'] }}</li>
                                                        @endif
                                                    </ul>
                                                    
                                                    <!-- Second Pizza -->
                                                    @if(!empty($options['second_pizza_toppings']) || !empty($options['second_pizza_extra_toppings']) || 
                                                        !empty($options['second_pizza_size']) || !empty($options['second_pizza_base']) || 
                                                        !empty($options['second_pizza_crust']) || (!empty($options['add_second_pizza']) && $options['add_second_pizza'] == 'yes'))
                                                        <strong class="d-block mt-1">Second Pizza:</strong>
                                                        <ul class="ps-3 mb-2">
                                                            @if(!empty($options['second_pizza_size']))
                                                                <li>Size: {{ $options['second_pizza_size'] }}</li>
                                                            @endif
                                                            
                                                            @if(!empty($options['second_pizza_toppings']))
                                                                <li>Toppings: {{ implode(', ', $options['second_pizza_toppings']) }}</li>
                                                            @endif
                                                            
                                                            @if(!empty($options['second_pizza_extra_toppings']))
                                                                <li>Extra Toppings: {{ implode(', ', $options['second_pizza_extra_toppings']) }}</li>
                                                            @endif
                                                            
                                                            @if(!empty($options['second_pizza_base']))
                                                                <li>Base: {{ $options['second_pizza_base'] }}</li>
                                                            @endif
                                                            
                                                            @if(!empty($options['second_pizza_crust']))
                                                                <li>Crust: {{ $options['second_pizza_crust'] }}</li>
                                                            @endif
                                                        </ul>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <!-- Special Instructions or Notes -->
                                            @if(!empty($options['special_instructions']))
                                                <span class="d-block mt-1"><strong>Special Instructions:</strong> {{ $options['special_instructions'] }}</span>
                                            @endif
                                            
                                            @if(!empty($options['notes']))
                                                <span class="d-block mt-1 text-muted"><strong>Note:</strong> {{ $options['notes'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                
                                @if($order->tax_amount > 0)
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                                        <td class="text-end">${{ number_format($order->tax_amount, 2) }}</td>
                                    </tr>
                                @endif
                                
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="4" class="text-end text-success"><strong>Discount:</strong></td>
                                        <td class="text-end text-success">-${{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>${{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>What's Next?</h6>
                        <p class="mb-0">
                            Please arrive at our store at your selected time. Your order will be ready for pickup. Please bring your order number with you.
                        </p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('menu.index') }}" class="btn btn-primary">
                            <i class="fas fa-pizza-slice me-2"></i>Order More Food
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 