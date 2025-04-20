@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order #{{ $order->order_number }}</h1>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>
    </div>
    
    <!-- Order Status Update -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-8">
                            <select name="status" class="form-select">
                                <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }}>Ready for Pickup</option>
                                <option value="picked_up" {{ $order->order_status === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <span class="fw-bold">Current Status:</span>
                        <span class="badge bg-{{ 
                            $order->order_status === 'pending' ? 'warning' : 
                            ($order->order_status === 'preparing' ? 'info' : 
                            ($order->order_status === 'ready' ? 'primary' : 
                            ($order->order_status === 'picked_up' ? 'success' : 'danger'))) 
                        }} ms-2">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Order Details -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Order Date:</strong></p>
                            <p>{{ $order->created_at->format('F j, Y, g:i a') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Order Type:</strong></p>
                            <p>Pickup</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Pickup Date:</strong></p>
                            <p>{{ $order->pickup_time->format('F j, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Pickup Time:</strong></p>
                            <p>{{ $order->pickup_time->format('g:i a') }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Payment Method:</strong></p>
                            <p>{{ $order->payment_method === 'credit_card' ? 'Credit Card' : 'Pay in Store' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Payment Status:</strong></p>
                            <p><span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span></p>
                        </div>
                    </div>
                    
                    @if($order->payment_method === 'credit_card' && $order->payment_id)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="mb-1"><strong>Payment ID:</strong></p>
                            <p>{{ $order->payment_id }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Customer Details -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p class="mb-1"><strong>Name:</strong></p>
                            <p>{{ $order->customer_name }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Email:</strong></p>
                            <p><a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Phone:</strong></p>
                            <p><a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Display order items -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Order Items</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        @php
                            $decodedOptions = $item->decoded_options ?? [];
                            if (!is_array($decodedOptions) && is_string($item->options)) {
                                $decodedOptions = json_decode($item->options, true) ?: [];
                            }
                        @endphp
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->size ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->subtotal, 2) }}</td>
                            <td>
                                @if (!empty($decodedOptions))
                                    <div class="options-list">
                                        @if(isset($decodedOptions['toppings']) && is_array($decodedOptions['toppings']) && !empty($decodedOptions['toppings']))
                                            <div class="mb-2">
                                                <strong>Toppings:</strong> {{ implode(', ', $decodedOptions['toppings']) }}
                                            </div>
                                        @endif
                                        
                                        @if(isset($decodedOptions['extra_toppings']) && is_array($decodedOptions['extra_toppings']) && !empty($decodedOptions['extra_toppings']))
                                            <div class="mb-2">
                                                <strong>Extra Toppings:</strong> {{ implode(', ', $decodedOptions['extra_toppings']) }}
                                            </div>
                                        @endif

                                        @if(isset($decodedOptions['cheese']) && $decodedOptions['cheese'] == true)
                                            <div class="mb-2">
                                                <strong>Add Cheese:</strong> Yes
                                            </div>
                                        @endif
                                        
                                        @if(isset($decodedOptions['base']) && !empty($decodedOptions['base']))
                                            <div class="mb-2">
                                                <strong>Base:</strong> {{ $decodedOptions['base'] }}
                                            </div>
                                        @endif
                                        
                                        @if(isset($decodedOptions['crust']) && !empty($decodedOptions['crust']))
                                            <div class="mb-2">
                                                <strong>Crust:</strong> {{ $decodedOptions['crust'] }}
                                            </div>
                                        @endif
                                        
                                        <!-- Wing Options -->
                                        @if(isset($decodedOptions['wing_flavors']) || isset($decodedOptions['wings_flavor']))
                                            <div class="mb-2">
                                                <strong>Wing Flavor:</strong>
                                                @php
                                                    $wingFlavor = $decodedOptions['wing_flavors'] ?? $decodedOptions['wings_flavor'] ?? '';
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
                                                {{ $wingFlavor }}
                                            </div>
                                            
                                            @if(isset($decodedOptions['add_extra_wings']) && $decodedOptions['add_extra_wings'] == 'yes')
                                                <div class="mb-2">
                                                    <strong>Extra Wings:</strong> Yes
                                                </div>
                                            @endif
                                        @endif

                                        <!-- Garlic Bread Options -->
                                        @if(isset($decodedOptions['garlic_bread']) && $decodedOptions['garlic_bread'] == 'yes')
                                            <div class="mb-2">
                                                <strong>Garlic Bread:</strong> Yes
                                                @if(isset($decodedOptions['garlic_bread_add_cheese']) && $decodedOptions['garlic_bread_add_cheese'] == 'yes')
                                                    (with Cheese)
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Pop Selections -->
                                        @php
                                            $pops = [];
                                            foreach(['pop1', 'pop2', 'pop3', 'pop4'] as $pop) {
                                                if(isset($decodedOptions[$pop]) && !empty($decodedOptions[$pop]) && $decodedOptions[$pop] != 'none') {
                                                    $pops[] = $decodedOptions[$pop];
                                                }
                                            }
                                        @endphp

                                        @if(!empty($pops))
                                            <div class="mb-2">
                                                <strong>Pop Selections:</strong> {{ implode(', ', $pops) }}
                                            </div>
                                        @endif
                                        
                                        <!-- Handle 2-for-1 special options -->
                                        @php
                                            $hasTwoForOneOptions = false;
                                            foreach($decodedOptions as $key => $value) {
                                                if(strpos($key, 'first_pizza_') === 0 || strpos($key, 'second_pizza_') === 0) {
                                                    $hasTwoForOneOptions = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        
                                        @if($hasTwoForOneOptions)
                                            <div class="mt-2">
                                                <strong>2-for-1 Special:</strong>
                                                <div class="ms-3 mt-1">
                                                    <!-- First Pizza -->
                                                    <div class="mb-2">
                                                        <strong>First Pizza:</strong>
                                                        <ul class="mb-1 ps-3">
                                                            @if(isset($decodedOptions['first_pizza_size']))
                                                                <li>Size: {{ $decodedOptions['first_pizza_size'] }}</li>
                                                            @elseif(isset($decodedOptions['size']))
                                                                <li>Size: {{ $decodedOptions['size'] }}</li>
                                                            @endif
                                                            
                                                            @if(isset($decodedOptions['first_pizza_toppings']) && is_array($decodedOptions['first_pizza_toppings']) && !empty($decodedOptions['first_pizza_toppings']))
                                                                <li>Toppings: {{ implode(', ', $decodedOptions['first_pizza_toppings']) }}</li>
                                                            @elseif(isset($decodedOptions['toppings']) && is_array($decodedOptions['toppings']) && !empty($decodedOptions['toppings']))
                                                                <li>Toppings: {{ implode(', ', $decodedOptions['toppings']) }}</li>
                                                            @endif
                                                            
                                                            @if(isset($decodedOptions['first_pizza_extra_toppings']) && is_array($decodedOptions['first_pizza_extra_toppings']) && !empty($decodedOptions['first_pizza_extra_toppings']))
                                                                <li>Extra Toppings: {{ implode(', ', $decodedOptions['first_pizza_extra_toppings']) }}</li>
                                                            @elseif(isset($decodedOptions['extra_toppings']) && is_array($decodedOptions['extra_toppings']) && !empty($decodedOptions['extra_toppings']))
                                                                <li>Extra Toppings: {{ implode(', ', $decodedOptions['extra_toppings']) }}</li>
                                                            @endif
                                                            
                                                            @if(isset($decodedOptions['first_pizza_base']))
                                                                <li>Base: {{ $decodedOptions['first_pizza_base'] }}</li>
                                                            @elseif(isset($decodedOptions['base']))
                                                                <li>Base: {{ $decodedOptions['base'] }}</li>
                                                            @endif
                                                            
                                                            @if(isset($decodedOptions['first_pizza_crust']))
                                                                <li>Crust: {{ $decodedOptions['first_pizza_crust'] }}</li>
                                                            @elseif(isset($decodedOptions['crust']))
                                                                <li>Crust: {{ $decodedOptions['crust'] }}</li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                    
                                                    <!-- Second Pizza -->
                                                    @if(isset($decodedOptions['second_pizza_toppings']) || isset($decodedOptions['second_pizza_extra_toppings']) || 
                                                        isset($decodedOptions['second_pizza_size']) || isset($decodedOptions['second_pizza_base']) || 
                                                        isset($decodedOptions['second_pizza_crust']) || (isset($decodedOptions['add_second_pizza']) && $decodedOptions['add_second_pizza'] == 'yes'))
                                                        <div class="mb-2">
                                                            <strong>Second Pizza:</strong>
                                                            <ul class="mb-1 ps-3">
                                                                @if(isset($decodedOptions['second_pizza_size']))
                                                                    <li>Size: {{ $decodedOptions['second_pizza_size'] }}</li>
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['second_pizza_toppings']) && is_array($decodedOptions['second_pizza_toppings']) && !empty($decodedOptions['second_pizza_toppings']))
                                                                    <li>Toppings: {{ implode(', ', $decodedOptions['second_pizza_toppings']) }}</li>
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['second_pizza_extra_toppings']) && is_array($decodedOptions['second_pizza_extra_toppings']) && !empty($decodedOptions['second_pizza_extra_toppings']))
                                                                    <li>Extra Toppings: {{ implode(', ', $decodedOptions['second_pizza_extra_toppings']) }}</li>
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['second_pizza_base']))
                                                                    <li>Base: {{ $decodedOptions['second_pizza_base'] }}</li>
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['second_pizza_crust']))
                                                                    <li>Crust: {{ $decodedOptions['second_pizza_crust'] }}</li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    <!-- Third Pizza -->
                                                    @if(isset($decodedOptions['third_pizza']) && $decodedOptions['third_pizza'] == 'yes' || 
                                                        isset($decodedOptions['third_pizza_toppings']) || isset($decodedOptions['third_pizza_extra_toppings']) || 
                                                        isset($decodedOptions['third_pizza_size']) || isset($decodedOptions['pizza_3_toppings']))
                                                        <div class="mb-2">
                                                            <strong>Third Pizza:</strong>
                                                            <ul class="mb-1 ps-3">
                                                                @if(isset($decodedOptions['third_pizza_size']))
                                                                    <li>Size: {{ $decodedOptions['third_pizza_size'] }}</li>
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['third_pizza_toppings']) && is_array($decodedOptions['third_pizza_toppings']) && !empty($decodedOptions['third_pizza_toppings']))
                                                                    <li>Toppings: 
                                                                        @php
                                                                            // Use the actual topping names from the database if possible
                                                                            // Otherwise, fall back to a more accurate mapping
                                                                            $toppingIds = $decodedOptions['third_pizza_toppings'];
                                                                            $toppingNames = [];
                                                                            
                                                                            // Try to fetch the actual toppings from the database
                                                                            $actualToppings = \App\Models\Topping::whereIn('id', $toppingIds)->pluck('name', 'id')->toArray();
                                                                            
                                                                            if (count($actualToppings) > 0) {
                                                                                // We found the actual toppings in the database
                                                                                foreach($toppingIds as $toppingId) {
                                                                                    if(isset($actualToppings[$toppingId])) {
                                                                                        $toppingNames[] = $actualToppings[$toppingId];
                                                                                    } else {
                                                                                        $toppingNames[] = "Topping ID: " . $toppingId;
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                // Fall back to a more accurate mapping based on the seeders
                                                                                $toppingsMap = [
                                                                                    '1' => 'Pepperoni',
                                                                                    '2' => 'Italian Sausage',
                                                                                    '3' => 'Bacon',
                                                                                    '4' => 'Ham',
                                                                                    '5' => 'Ground Beef',
                                                                                    '6' => 'Real Chicken',
                                                                                    '7' => 'Salami',
                                                                                    '8' => 'Anchovies',
                                                                                    '10' => 'Mushrooms',
                                                                                    '11' => 'Onions',
                                                                                    '12' => 'Green Peppers',
                                                                                    '13' => 'Black Olives',
                                                                                    '14' => 'Tomatoes',
                                                                                    '15' => 'Pineapple', 
                                                                                    '16' => 'Jalapeños',
                                                                                    '17' => 'Spinach',
                                                                                    '18' => 'Banana Peppers',
                                                                                    '19' => 'Roasted Red Peppers',
                                                                                    '20' => 'Sun-Dried Tomatoes',
                                                                                    '30' => 'Extra Cheese',
                                                                                    '31' => 'Feta Cheese',
                                                                                    '32' => 'Mozzarella',
                                                                                    '33' => 'Parmesan',
                                                                                    '34' => 'Cheddar'
                                                                                ];
                                                                                
                                                                                foreach($toppingIds as $toppingId) {
                                                                                    if(isset($toppingsMap[$toppingId])) {
                                                                                        $toppingNames[] = $toppingsMap[$toppingId];
                                                                                    } else {
                                                                                        $toppingNames[] = "Topping ID: " . $toppingId;
                                                                                    }
                                                                                }
                                                                            }
                                                                            
                                                                            // Debug - print the actual topping IDs for verification
                                                                            echo "<!-- Debug - Third Pizza Topping IDs: " . implode(', ', $decodedOptions['third_pizza_toppings']) . " -->";
                                                                        @endphp
                                                                        {{ implode(', ', $toppingNames) }}
                                                                    </li>
                                                                @elseif(isset($decodedOptions['pizza_3_toppings']) && is_array($decodedOptions['pizza_3_toppings']) && !empty($decodedOptions['pizza_3_toppings']))
                                                                    <li>Toppings: 
                                                                        @php
                                                                            // Use the actual topping names from the database if possible
                                                                            // Otherwise, fall back to a more accurate mapping
                                                                            $toppingIds = $decodedOptions['pizza_3_toppings'];
                                                                            $toppingNames = [];
                                                                            
                                                                            // Try to fetch the actual toppings from the database
                                                                            $actualToppings = \App\Models\Topping::whereIn('id', $toppingIds)->pluck('name', 'id')->toArray();
                                                                            
                                                                            if (count($actualToppings) > 0) {
                                                                                // We found the actual toppings in the database
                                                                                foreach($toppingIds as $toppingId) {
                                                                                    if(isset($actualToppings[$toppingId])) {
                                                                                        $toppingNames[] = $actualToppings[$toppingId];
                                                                                    } else {
                                                                                        $toppingNames[] = "Topping ID: " . $toppingId;
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                // Fall back to a more accurate mapping based on the seeders
                                                                                $toppingsMap = [
                                                                                    '1' => 'Pepperoni',
                                                                                    '2' => 'Italian Sausage',
                                                                                    '3' => 'Bacon',
                                                                                    '4' => 'Ham',
                                                                                    '5' => 'Ground Beef',
                                                                                    '6' => 'Real Chicken',
                                                                                    '7' => 'Salami',
                                                                                    '8' => 'Anchovies',
                                                                                    '10' => 'Mushrooms',
                                                                                    '11' => 'Onions',
                                                                                    '12' => 'Green Peppers',
                                                                                    '13' => 'Black Olives',
                                                                                    '14' => 'Tomatoes',
                                                                                    '15' => 'Pineapple', 
                                                                                    '16' => 'Jalapeños',
                                                                                    '17' => 'Spinach',
                                                                                    '18' => 'Banana Peppers',
                                                                                    '19' => 'Roasted Red Peppers',
                                                                                    '20' => 'Sun-Dried Tomatoes',
                                                                                    '30' => 'Extra Cheese',
                                                                                    '31' => 'Feta Cheese',
                                                                                    '32' => 'Mozzarella',
                                                                                    '33' => 'Parmesan',
                                                                                    '34' => 'Cheddar'
                                                                                ];
                                                                                
                                                                                foreach($toppingIds as $toppingId) {
                                                                                    if(isset($toppingsMap[$toppingId])) {
                                                                                        $toppingNames[] = $toppingsMap[$toppingId];
                                                                                    } else {
                                                                                        $toppingNames[] = "Topping ID: " . $toppingId;
                                                                                    }
                                                                                }
                                                                            }
                                                                            
                                                                            // Debug - print the actual topping IDs for verification
                                                                            echo "<!-- Debug - Pizza 3 Topping IDs: " . implode(', ', $decodedOptions['pizza_3_toppings']) . " -->";
                                                                        @endphp
                                                                        {{ implode(', ', $toppingNames) }}
                                                                    </li>
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['third_pizza_extra_toppings']) && is_array($decodedOptions['third_pizza_extra_toppings']) && !empty($decodedOptions['third_pizza_extra_toppings']))
                                                                    <li>Extra Toppings: {{ implode(', ', $decodedOptions['third_pizza_extra_toppings']) }}</li>
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['third_pizza_base']))
                                                                    <li>Base: {{ $decodedOptions['third_pizza_base'] }}</li>
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['third_pizza_crust']))
                                                                    <li>Crust: {{ $decodedOptions['third_pizza_crust'] }}</li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if(isset($decodedOptions['special_instructions']) && !empty($decodedOptions['special_instructions']))
                                            <div class="mt-2">
                                                <strong>Special Instructions:</strong> {{ $decodedOptions['special_instructions'] }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">No options</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 