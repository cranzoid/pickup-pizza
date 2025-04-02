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
                            <p><strong>Name:</strong> {{ $order->name }}</p>
                            <p><strong>Email:</strong> {{ $order->email }}</p>
                            <p><strong>Phone:</strong> {{ $order->phone }}</p>
                            
                            @if($order->order_type === 'pickup')
                                <h6 class="mt-3">Pickup Details</h6>
                                <p><strong>Date:</strong> {{ date('F j, Y', strtotime($order->pickup_date)) }}</p>
                                <p><strong>Time:</strong> {{ date('g:i a', strtotime($order->pickup_time)) }}</p>
                            @else
                                <h6 class="mt-3">Delivery Details</h6>
                                <p><strong>Address:</strong> {{ $order->address }}</p>
                                <p><strong>City:</strong> {{ $order->city }}</p>
                                <p><strong>Postal Code:</strong> {{ $order->postal_code }}</p>
                                @if($order->delivery_instructions)
                                    <p><strong>Instructions:</strong> {{ $order->delivery_instructions }}</p>
                                @endif
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
                                        $options = json_decode($item->options, true);
                                    @endphp
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if(!empty($options['size']))
                                                <span class="d-block">Size: {{ ucfirst($options['size']) }}</span>
                                            @endif
                                            
                                            @if(!empty($options['toppings']) && count($options['toppings']) > 0)
                                                <span class="d-block">Toppings: {{ implode(', ', $options['toppings']) }}</span>
                                            @endif
                                            
                                            @if(!empty($options['extra_toppings']) && count($options['extra_toppings']) > 0)
                                                <span class="d-block">Extra Toppings: {{ implode(', ', $options['extra_toppings']) }}</span>
                                            @endif
                                            
                                            @if(!empty($options['notes']))
                                                <span class="d-block text-muted">Note: {{ $options['notes'] }}</span>
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