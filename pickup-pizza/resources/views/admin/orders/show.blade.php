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
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready for Pickup</option>
                                <option value="picked_up" {{ $order->status === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <span class="fw-bold">Current Status:</span>
                        <span class="badge bg-{{ 
                            $order->status === 'pending' ? 'warning' : 
                            ($order->status === 'preparing' ? 'info' : 
                            ($order->status === 'ready' ? 'primary' : 
                            ($order->status === 'picked_up' ? 'success' : 'danger'))) 
                        }} ms-2">
                            {{ ucfirst($order->status) }}
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
                            <p>{{ date('F j, Y', strtotime($order->pickup_date)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Pickup Time:</strong></p>
                            <p>{{ date('g:i a', strtotime($order->pickup_time)) }}</p>
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
                            <p>{{ $order->name }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Email:</strong></p>
                            <p><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Phone:</strong></p>
                            <p><a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Order Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Options</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>
                                @php
                                    $options = json_decode($item->options, true);
                                @endphp
                                @if(isset($options['size']))
                                    <div><strong>Size:</strong> {{ ucfirst($options['size']) }}</div>
                                @endif
                                
                                @if(isset($options['toppings']) && count($options['toppings']) > 0)
                                    <div><strong>Toppings:</strong> {{ implode(', ', $options['toppings']) }}</div>
                                @endif
                                
                                @if(isset($options['extra_toppings']) && count($options['extra_toppings']) > 0)
                                    <div><strong>Extra Toppings:</strong> {{ implode(', ', $options['extra_toppings']) }}</div>
                                @endif
                                
                                @if(isset($options['extras']) && count($options['extras']) > 0)
                                    <div><strong>Extras:</strong>
                                        <ul class="list-unstyled mb-0 ps-3">
                                            @foreach($options['extras'] as $extra)
                                                <li>
                                                    {{ $extra['name'] }}
                                                    @if($extra['quantity'] > 1)
                                                        (x{{ $extra['quantity'] }})
                                                    @endif
                                                    - ${{ number_format($extra['price'] * $extra['quantity'], 2) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if(isset($options['notes']) && !empty($options['notes']))
                                    <div><strong>Notes:</strong> {{ $options['notes'] }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
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
                            <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                            <td class="text-end">-${{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end fw-bold">${{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 