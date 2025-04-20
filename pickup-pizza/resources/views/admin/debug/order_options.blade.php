@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Debug Order #{{ $order->order_number }}</h2>
                    <div>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">Back to Order</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h3>Order Information</h3>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Order ID:</strong> {{ $order->id }}</p>
                                        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                                        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y g:i a') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                                        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                                        <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Order Status:</strong> {{ ucfirst($order->order_status) }}</p>
                                        <p><strong>Payment Method:</strong> {{ str_replace('_', ' ', ucfirst($order->payment_method)) }}</p>
                                        <p><strong>Pickup Time:</strong> {{ $order->pickup_time->format('F j, Y g:i a') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h3>Order Items ({{ $order->items->count() }} items)</h3>
                    <div class="row">
                        @foreach($order->items as $item)
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>{{ $item->name }} ({{ $item->size ?? 'No Size' }})</strong>
                                            <span>Item ID: {{ $item->id }} | Quantity: {{ $item->quantity }} | Unit Price: ${{ number_format($item->unit_price, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>Raw Options JSON:</h5>
                                                <pre class="bg-light p-3 border rounded">{{ $item->raw_options }}</pre>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Decoded Options:</h5>
                                                <pre class="bg-light p-3 border rounded">{{ print_r($item->decoded_options, true) }}</pre>
                                            </div>
                                        </div>
                                        
                                        @if(isset($item->decoded_options) && !isset($item->decoded_options['error']))
                                            <div class="mt-4">
                                                <h5>Interpreted Options:</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Option Key</th>
                                                                <th>Option Value</th>
                                                                <th>Type</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($item->decoded_options as $key => $value)
                                                                <tr>
                                                                    <td><code>{{ $key }}</code></td>
                                                                    <td>
                                                                        @if(is_array($value))
                                                                            <ul class="mb-0">
                                                                                @foreach($value as $subValue)
                                                                                    <li>{{ $subValue }}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @else
                                                                            {{ is_bool($value) ? ($value ? 'true' : 'false') : $value }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ gettype($value) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <p class="text-muted mb-0">Debug information for development purposes only.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 