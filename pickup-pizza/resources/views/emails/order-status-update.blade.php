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
            <p>Dear {{ $order->customer_name }},</p>
            
            @if($order->order_status == 'ready')
            <p>Great news! Your order is now <span class="status-badge status-ready">READY FOR PICKUP</span></p>
            <p>You can come to our store to pick up your order. We're looking forward to serving you!</p>
            @elseif($order->order_status == 'preparing')
            <p>Your order is now <span class="status-badge status-preparing">BEING PREPARED</span></p>
            <p>Our kitchen team is working on your order, and it will be ready for pickup soon.</p>
            @elseif($order->order_status == 'cancelled')
            <p>Your order has been <span class="status-badge status-cancelled">CANCELLED</span></p>
            <p>If you didn't request this cancellation, please contact us immediately.</p>
            @else
            <p>Your order status has been updated to <span class="status-badge">{{ strtoupper(str_replace('_', ' ', $order->order_status)) }}</span></p>
            @endif
            
            <div class="order-details">
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                <p><strong>Pickup Date:</strong> {{ date('F j, Y', strtotime($order->pickup_date)) }}</p>
                <p><strong>Pickup Time:</strong> {{ date('g:i a', strtotime($order->pickup_time)) }}</p>
            </div>
            
            <!-- Order Items Summary -->
            @if(isset($order->items) && $order->items->count() > 0)
            <div style="margin-top: 20px; margin-bottom: 20px;">
                <h3 style="color: #dc3545;">Your Order</h3>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                    <thead>
                        <tr>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Item</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Size</th>
                            <th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Quantity</th>
                            <th style="padding: 8px; text-align: right; border-bottom: 1px solid #dee2e6; background-color: #f8f9fa;">Price</th>
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
                                <td style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">
                                    <div style="font-weight: bold;">{{ $item->name }}</div>
                                    @if(!empty($decodedOptions))
                                        <!-- Display toppings -->
                                        @if(isset($decodedOptions['toppings']) && is_array($decodedOptions['toppings']) && !empty($decodedOptions['toppings']))
                                            <div style="font-size: 0.9em; color: #6c757d;">Toppings: {{ implode(', ', $decodedOptions['toppings']) }}</div>
                                        @endif
                                        
                                        <!-- Display extra toppings -->
                                        @if(isset($decodedOptions['extra_toppings']) && is_array($decodedOptions['extra_toppings']) && !empty($decodedOptions['extra_toppings']))
                                            <div style="font-size: 0.9em; color: #6c757d;">Extra Toppings: {{ implode(', ', $decodedOptions['extra_toppings']) }}</div>
                                        @endif

                                        <!-- Display cheese option -->
                                        @if(isset($decodedOptions['cheese']) && $decodedOptions['cheese'] == true)
                                            <div style="font-size: 0.9em; color: #6c757d;">Add Cheese: Yes</div>
                                        @endif
                                        
                                        <!-- Wing Options -->
                                        @if(isset($decodedOptions['wing_flavors']) || isset($decodedOptions['wings_flavor']))
                                            <div style="font-size: 0.9em; color: #6c757d;">
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
                                                <div style="font-size: 0.9em; color: #6c757d;">Extra Wings: Yes</div>
                                            @endif
                                        @endif

                                        <!-- Garlic Bread Options -->
                                        @if(isset($decodedOptions['garlic_bread']) && $decodedOptions['garlic_bread'] == 'yes')
                                            <div style="font-size: 0.9em; color: #6c757d;">
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
                                            <div style="font-size: 0.9em; color: #6c757d;">
                                                <strong>Pop Selections:</strong> {{ implode(', ', $pops) }}
                                            </div>
                                        @endif
                                        
                                        <!-- Display 2-for-1 options -->
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
                                            <div style="font-size: 0.9em; color: #6c757d; margin-top: 4px;">
                                                <div><strong>2-for-1 Special:</strong></div>
                                                <div style="margin-left: 8px;">
                                                    <!-- First Pizza -->
                                                    <div>First Pizza: 
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
                                                    
                                                    <!-- Second Pizza -->
                                                    @if(isset($decodedOptions['second_pizza_toppings']) || isset($decodedOptions['second_pizza_extra_toppings']) || 
                                                       isset($decodedOptions['second_pizza_size']) || (isset($decodedOptions['add_second_pizza']) && $decodedOptions['add_second_pizza'] == 'yes'))
                                                        <div>Second Pizza: 
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
                                            <div>Third Pizza: 
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

                                        <!-- Special Instructions -->
                                        @if(isset($decodedOptions['special_instructions']) && !empty($decodedOptions['special_instructions']))
                                            <div style="font-size: 0.9em; color: #6c757d; margin-top: 4px;">
                                                <strong>Special Instructions:</strong> {{ $decodedOptions['special_instructions'] }}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">{{ $item->size ?? 'N/A' }}</td>
                                <td style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">{{ $item->quantity }}</td>
                                <td style="padding: 8px; text-align: right; border-bottom: 1px solid #dee2e6;">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="padding: 8px; text-align: right; border-top: 2px solid #dee2e6;"><strong>Total:</strong></td>
                            <td style="padding: 8px; text-align: right; border-top: 2px solid #dee2e6;"><strong>${{ number_format($order->total, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
            
            @if($order->order_status == 'ready')
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