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
                    @if(isset($alertMessage) && !empty($alertMessage) && is_string($alertMessage))
                        {{ $alertMessage }}
                    @endif
                </div>
            @endif
            
            @if(isset($message) && !empty($message) && is_string($message))
                <p>{{ $message }}</p>
            @endif
            
            @if(isset($order) && $order && is_object($order) && get_class($order) === 'App\\Models\\Order')
                <h3>Related Order Information</h3>
                <table>
                    <tr>
                        <th>Order Number</th>
                        <td>{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td>{{ $order->customer_name }}</td>
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
                        <td>{{ ucfirst($order->order_status) }}</td>
                    </tr>
                </table>
                
                @if(isset($order->items) && $order->items->count() > 0)
                <h3 style="margin-top: 20px; color: #343a40;">Order Items</h3>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Item</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Size</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Qty</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Price</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            @php
                                $decodedOptions = $item->decoded_options ?? [];
                                if (!is_array($decodedOptions) && is_string($item->options)) {
                                    $decodedOptions = json_decode($item->options, true) ?: [];
                                }
                            @endphp
                            <tr>
                                <td style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">{{ $item->name }}</td>
                                <td style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">{{ $item->size ?? 'N/A' }}</td>
                                <td style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">{{ $item->quantity }}</td>
                                <td style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">${{ number_format($item->unit_price, 2) }}</td>
                                <td style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">
                                    @if(!empty($decodedOptions))
                                        <div>
                                            @if(isset($decodedOptions['toppings']) && is_array($decodedOptions['toppings']) && !empty($decodedOptions['toppings']))
                                                <div style="margin-bottom: 5px;"><strong>Toppings:</strong> {{ implode(', ', $decodedOptions['toppings']) }}</div>
                                            @endif
                                            
                                            @if(isset($decodedOptions['extra_toppings']) && is_array($decodedOptions['extra_toppings']) && !empty($decodedOptions['extra_toppings']))
                                                <div style="margin-bottom: 5px;"><strong>Extra Toppings:</strong> {{ implode(', ', $decodedOptions['extra_toppings']) }}</div>
                                            @endif

                                            @if(isset($decodedOptions['cheese']) && $decodedOptions['cheese'] == true)
                                                <div style="margin-bottom: 5px;"><strong>Add Cheese:</strong> Yes</div>
                                            @endif
                                            
                                            <!-- Wing Options -->
                                            @if(isset($decodedOptions['wing_flavors']) || isset($decodedOptions['wings_flavor']))
                                                <div style="margin-bottom: 5px;">
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
                                                    <div style="margin-bottom: 5px;"><strong>Extra Wings:</strong> Yes</div>
                                                @endif
                                            @endif

                                            <!-- Garlic Bread Options -->
                                            @if(isset($decodedOptions['garlic_bread']) && $decodedOptions['garlic_bread'] == 'yes')
                                                <div style="margin-bottom: 5px;">
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
                                                <div style="margin-bottom: 5px;"><strong>Pop Selections:</strong> {{ implode(', ', $pops) }}</div>
                                            @endif
                                            
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
                                                <div style="margin-top: 8px;">
                                                    <strong>2-for-1 Special:</strong>
                                                    <div style="margin-left: 10px;">
                                                        <div style="margin-bottom: 5px;"><strong>First Pizza:</strong> 
                                                            @if(isset($decodedOptions['first_pizza_size']) || isset($decodedOptions['size']))
                                                                {{ isset($decodedOptions['first_pizza_size']) ? $decodedOptions['first_pizza_size'] : $decodedOptions['size'] }}
                                                            @endif
                                                            
                                                            @if(isset($decodedOptions['first_pizza_toppings']) && is_array($decodedOptions['first_pizza_toppings']) && !empty($decodedOptions['first_pizza_toppings']))
                                                                with {{ implode(', ', $decodedOptions['first_pizza_toppings']) }}
                                                            @elseif(isset($decodedOptions['toppings']) && is_array($decodedOptions['toppings']) && !empty($decodedOptions['toppings']))
                                                                with {{ implode(', ', $decodedOptions['toppings']) }}
                                                            @endif
                                                            
                                                            @if(isset($decodedOptions['first_pizza_extra_toppings']) && is_array($decodedOptions['first_pizza_extra_toppings']) && !empty($decodedOptions['first_pizza_extra_toppings']))
                                                                + extra {{ implode(', ', $decodedOptions['first_pizza_extra_toppings']) }}
                                                            @elseif(isset($decodedOptions['extra_toppings']) && is_array($decodedOptions['extra_toppings']) && !empty($decodedOptions['extra_toppings']))
                                                                + extra {{ implode(', ', $decodedOptions['extra_toppings']) }}
                                                            @endif
                                                        </div>
                                                        
                                                        @if(isset($decodedOptions['second_pizza_toppings']) || isset($decodedOptions['second_pizza_extra_toppings']) || 
                                                            isset($decodedOptions['second_pizza_size']) || (isset($decodedOptions['add_second_pizza']) && $decodedOptions['add_second_pizza'] == 'yes'))
                                                            <div style="margin-bottom: 5px;"><strong>Second Pizza:</strong> 
                                                                @if(isset($decodedOptions['second_pizza_size']))
                                                                    {{ $decodedOptions['second_pizza_size'] }}
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['second_pizza_toppings']) && is_array($decodedOptions['second_pizza_toppings']) && !empty($decodedOptions['second_pizza_toppings']))
                                                                    with {{ implode(', ', $decodedOptions['second_pizza_toppings']) }}
                                                                @endif
                                                                
                                                                @if(isset($decodedOptions['second_pizza_extra_toppings']) && is_array($decodedOptions['second_pizza_extra_toppings']) && !empty($decodedOptions['second_pizza_extra_toppings']))
                                                                    + extra {{ implode(', ', $decodedOptions['second_pizza_extra_toppings']) }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Third Pizza -->
                                            @if(isset($decodedOptions['third_pizza']) && $decodedOptions['third_pizza'] == 'yes' || 
                                                isset($decodedOptions['third_pizza_toppings']) || isset($decodedOptions['third_pizza_extra_toppings']) || 
                                                isset($decodedOptions['third_pizza_size']) || isset($decodedOptions['pizza_3_toppings']))
                                                <div style="margin-bottom: 5px;"><strong>Third Pizza:</strong> 
                                                    @if(isset($decodedOptions['third_pizza_size']))
                                                        {{ $decodedOptions['third_pizza_size'] }}
                                                    @endif
                                                    
                                                    @if(isset($decodedOptions['third_pizza_toppings']) && is_array($decodedOptions['third_pizza_toppings']) && !empty($decodedOptions['third_pizza_toppings']))
                                                        with 
                                                        @php
                                                            $toppingNames = [];
                                                            $toppingsMap = [
                                                                '1' => 'Pepperoni',
                                                                '2' => 'Mushrooms',
                                                                '3' => 'Green Peppers',
                                                                '4' => 'Real Chicken',
                                                                '5' => 'Ground Beef',
                                                                '6' => 'Onions',
                                                                '7' => 'Italian Sausage',
                                                                '8' => 'Bacon',
                                                                '9' => 'Extra Cheese',
                                                                '10' => 'Cheddar Cheese',
                                                                '11' => 'Feta Cheese',
                                                                '12' => 'Sliced Tomatoes',
                                                                '13' => 'Black Olives',
                                                                '14' => 'Pineapple',
                                                                '15' => 'Hot Peppers',
                                                                '16' => 'Anchovies',
                                                                '17' => 'Spinach',
                                                                '18' => 'Broccoli',
                                                                '19' => 'Ham',
                                                                '20' => 'Artichokes',
                                                                '21' => 'Sun-Dried Tomatoes',
                                                                '22' => 'Grilled Zucchini',
                                                                '23' => 'Ground Pork',
                                                                '24' => 'Bacon Bits',
                                                                '25' => 'Meatballs'
                                                            ];
                                                            
                                                            foreach($decodedOptions['third_pizza_toppings'] as $toppingId) {
                                                                if(isset($toppingsMap[$toppingId])) {
                                                                    $toppingNames[] = $toppingsMap[$toppingId];
                                                                } else {
                                                                    $toppingNames[] = $toppingId . " (unknown)";
                                                                }
                                                            }
                                                        @endphp
                                                        {{ implode(', ', $toppingNames) }}
                                                    @elseif(isset($decodedOptions['pizza_3_toppings']) && is_array($decodedOptions['pizza_3_toppings']) && !empty($decodedOptions['pizza_3_toppings']))
                                                        with 
                                                        @php
                                                            $toppingNames = [];
                                                            $toppingsMap = [
                                                                '1' => 'Pepperoni',
                                                                '2' => 'Mushrooms',
                                                                '3' => 'Green Peppers',
                                                                '4' => 'Real Chicken',
                                                                '5' => 'Ground Beef',
                                                                '6' => 'Onions',
                                                                '7' => 'Italian Sausage',
                                                                '8' => 'Bacon',
                                                                '9' => 'Extra Cheese',
                                                                '10' => 'Cheddar Cheese',
                                                                '11' => 'Feta Cheese',
                                                                '12' => 'Sliced Tomatoes',
                                                                '13' => 'Black Olives',
                                                                '14' => 'Pineapple',
                                                                '15' => 'Hot Peppers',
                                                                '16' => 'Anchovies',
                                                                '17' => 'Spinach',
                                                                '18' => 'Broccoli',
                                                                '19' => 'Ham',
                                                                '20' => 'Artichokes',
                                                                '21' => 'Sun-Dried Tomatoes',
                                                                '22' => 'Grilled Zucchini',
                                                                '23' => 'Ground Pork',
                                                                '24' => 'Bacon Bits',
                                                                '25' => 'Meatballs'
                                                            ];
                                                            
                                                            foreach($decodedOptions['pizza_3_toppings'] as $toppingId) {
                                                                if(isset($toppingsMap[$toppingId])) {
                                                                    $toppingNames[] = $toppingsMap[$toppingId];
                                                                } else {
                                                                    $toppingNames[] = $toppingId . " (unknown)";
                                                                }
                                                            }
                                                        @endphp
                                                        {{ implode(', ', $toppingNames) }}
                                                    @endif
                                                    
                                                    @if(isset($decodedOptions['third_pizza_extra_toppings']) && is_array($decodedOptions['third_pizza_extra_toppings']) && !empty($decodedOptions['third_pizza_extra_toppings']))
                                                        + extra {{ implode(', ', $decodedOptions['third_pizza_extra_toppings']) }}
                                                    @endif
                                                </div>
                                            @endif

                                            @if(isset($decodedOptions['special_instructions']) && !empty($decodedOptions['special_instructions']))
                                                <div style="margin-top: 8px;">
                                                    <strong>Special Instructions:</strong> {{ $decodedOptions['special_instructions'] }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: #6c757d;">No options</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
                
                <a href="{{ url('/admin/orders/' . $order->id) }}" class="action-button">View Order Details</a>
            @endif
            
            @if(isset($actionText) && !empty($actionText) && isset($actionUrl) && !empty($actionUrl))
                <div style="text-align: center; margin-top: 30px;">
                    <a href="{{ $actionUrl }}" class="action-button" target="_blank">{{ $actionText }}</a>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} PISA Pizza. All rights reserved.</p>
            <p>This is an automated system notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 