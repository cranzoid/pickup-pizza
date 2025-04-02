@extends('layouts.admin')

@section('title', 'Orders Report')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">Orders Report</h1>
            <p class="lead">Detailed analysis of orders and sales.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
            <a href="{{ route('admin.reports.export-orders', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Export to CSV
            </a>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Orders
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.orders') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Order Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready for Pickup</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="">All Payment Methods</option>
                        <option value="stripe" {{ request('payment_method') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="pickup" {{ request('payment_method') === 'pickup' ? 'selected' : '' }}>Pay at Pickup</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="min_amount" class="form-label">Min Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" class="form-control" id="min_amount" name="min_amount" value="{{ request('min_amount') }}">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="max_amount" class="form-label">Max Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" class="form-control" id="max_amount" name="max_amount" value="{{ request('max_amount') }}">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="discount_code" class="form-label">Discount Code</label>
                    <input type="text" class="form-control" id="discount_code" name="discount_code" value="{{ request('discount_code') }}">
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Search (Order #, Customer)</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}">
                </div>
                
                <div class="col-md-4">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="created_at_desc" {{ request('sort') === 'created_at_desc' ? 'selected' : '' }}>Date (Newest)</option>
                        <option value="created_at_asc" {{ request('sort') === 'created_at_asc' ? 'selected' : '' }}>Date (Oldest)</option>
                        <option value="total_desc" {{ request('sort') === 'total_desc' ? 'selected' : '' }}>Amount (Highest)</option>
                        <option value="total_asc" {{ request('sort') === 'total_asc' ? 'selected' : '' }}>Amount (Lowest)</option>
                    </select>
                </div>
                
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.reports.orders') }}" class="btn btn-outline-secondary">Reset Filters</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Orders</div>
                            <div class="display-6">{{ $summary['total_orders'] }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Revenue</div>
                            <div class="display-6">${{ number_format($summary['total_revenue'], 2) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Average Order Value</div>
                            <div class="display-6">${{ number_format($summary['average_order'], 2) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Discounted Orders</div>
                            <div class="display-6">{{ $summary['discounted_orders'] }} ({{ $summary['discount_percentage'] }}%)</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percent fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Orders Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Orders
        </div>
        <div class="card-body">
            @if($orders->isEmpty())
                <div class="alert alert-info">No orders found matching your filters.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Discount</th>
                                <th>Payment</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('M d, Y g:i A') }}</td>
                                    <td>
                                        {{ $order->customer_name }}<br>
                                        <small class="text-muted">{{ $order->customer_email }}</small>
                                    </td>
                                    <td>
                                        {{ $order->items_count }} items
                                        <button type="button" class="btn btn-sm btn-link p-0 ms-1" 
                                                data-bs-toggle="popover" 
                                                data-bs-trigger="focus" 
                                                title="Order Items" 
                                                data-bs-content="{{ $order->items_summary }}">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </td>
                                    <td>
                                        @if($order->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($order->status === 'processing')
                                            <span class="badge bg-primary">Processing</span>
                                        @elseif($order->status === 'ready')
                                            <span class="badge bg-info">Ready</span>
                                        @elseif($order->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->discount_code)
                                            <span class="badge bg-purple">{{ $order->discount_code }}</span>
                                            <small class="d-block">${{ number_format($order->discount_amount, 2) }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment_method === 'stripe')
                                            <span class="badge bg-secondary">Stripe</span>
                                        @else
                                            <span class="badge bg-light text-dark">Pay at Pickup</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                html: true
            })
        })
    });
</script>
@endsection 