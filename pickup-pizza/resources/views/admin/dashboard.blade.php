@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm me-2">
                <i class="fas fa-list fa-sm text-white-50 me-1"></i> View Orders
            </a>
            <a href="{{ route('admin.reports.orders') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 me-1"></i> Generate Report
            </a>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Orders (Today)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayOrdersCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Revenue (Today)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($todayRevenue, 2) }}</div>
                            <div class="text-xs mt-1 {{ $revenueTrend >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $revenueTrend >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($revenueTrend), 1) }}% from yesterday
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Order Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($todayAverageOrder, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Weekly Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($thisWeekRevenue, 2) }}</div>
                            <div class="text-xs mt-1 {{ $weeklyRevenueTrend >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $weeklyRevenueTrend >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                {{ number_format(abs($weeklyRevenueTrend), 1) }}% from last week
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Pending Orders -->
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <h4 class="mb-2">{{ $pendingOrdersCount }}</h4>
                            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning">View All</a>
                        </div>
                        
                        <!-- Preparing Orders -->
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Preparing</div>
                            <h4 class="mb-2">{{ $preparingOrdersCount }}</h4>
                            <a href="{{ route('admin.orders.index', ['status' => 'preparing']) }}" class="btn btn-sm btn-info">View All</a>
                        </div>
                        
                        <!-- Ready Orders -->
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ready for Pickup</div>
                            <h4 class="mb-2">{{ $readyOrdersCount }}</h4>
                            <a href="{{ route('admin.orders.index', ['status' => 'ready']) }}" class="btn btn-sm btn-success">View All</a>
                        </div>
                        
                        <!-- Picked Up Orders (Today) -->
                        <div class="col-md-3 text-center">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Picked Up (Today)</div>
                            <h4 class="mb-2">{{ $pickedUpOrdersCount }}</h4>
                            <a href="{{ route('admin.orders.index', ['status' => 'picked_up']) }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-block btn-primary">
                                <i class="fas fa-pizza-slice me-1"></i> Add New Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-block btn-success">
                                <i class="fas fa-tags me-1"></i> Manage Categories
                            </a>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="{{ route('admin.discounts.create') }}" class="btn btn-block btn-warning">
                                <i class="fas fa-percent me-1"></i> Create Discount
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-block btn-secondary">
                                <i class="fas fa-cog me-1"></i> Update Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Orders & Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Overview (Last 7 Days)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">View Options:</div>
                            <a class="dropdown-item" href="#" onclick="toggleChartView('orders'); return false;">Orders</a>
                            <a class="dropdown-item" href="#" onclick="toggleChartView('revenue'); return false;">Revenue</a>
                            <a class="dropdown-item" href="#" onclick="toggleChartView('both'); return false;">Both</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Methods</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($paymentMethods as $index => $method)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ $chartColors[$index % count($chartColors)] }}"></i> 
                                {{ $method->payment_method == 'stripe' ? 'Online (Stripe)' : 'Pay on Pickup' }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Items Chart -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Popular Items</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="chart-bar">
                                <canvas id="popularItemsBarChart"></canvas>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5">
                            <div class="chart-pie mb-4">
                                <canvas id="popularItemsPieChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                @foreach($popularItems as $index => $item)
                                    <span class="mr-2">
                                        <i class="fas fa-circle" style="color: {{ $chartColors[$index % count($chartColors)] }}"></i> {{ $item->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders and Upcoming Pickups -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($order->status == 'preparing')
                                            <span class="badge bg-info text-dark">Preparing</span>
                                        @elseif($order->status == 'ready')
                                            <span class="badge bg-success">Ready for Pickup</span>
                                        @elseif($order->status == 'picked_up')
                                            <span class="badge bg-primary">Picked Up</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown no-arrow">
                                            <a class="dropdown-toggle btn btn-sm btn-primary" href="#" role="button" id="dropdownMenuLink-{{ $order->id }}"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                aria-labelledby="dropdownMenuLink-{{ $order->id }}">
                                                <a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}">
                                                    <i class="fas fa-eye fa-sm fa-fw mr-2"></i> View Details
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                @if($order->status == 'pending')
                                                <a class="dropdown-item text-info" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('status-preparing-{{ $order->id }}').submit();">
                                                    <i class="fas fa-fire fa-sm fa-fw mr-2"></i> Mark as Preparing
                                                </a>
                                                <form id="status-preparing-{{ $order->id }}" action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="preparing">
                                                </form>
                                                @endif
                                                
                                                @if($order->status == 'preparing')
                                                <a class="dropdown-item text-success" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('status-ready-{{ $order->id }}').submit();">
                                                    <i class="fas fa-check fa-sm fa-fw mr-2"></i> Mark as Ready
                                                </a>
                                                <form id="status-ready-{{ $order->id }}" action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="ready">
                                                </form>
                                                @endif
                                                
                                                @if($order->status == 'ready')
                                                <a class="dropdown-item text-primary" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('status-picked-{{ $order->id }}').submit();">
                                                    <i class="fas fa-shopping-bag fa-sm fa-fw mr-2"></i> Mark as Picked Up
                                                </a>
                                                <form id="status-picked-{{ $order->id }}" action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="picked_up">
                                                </form>
                                                @endif
                                                
                                                @if($order->status != 'cancelled' && $order->status != 'picked_up')
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('status-cancel-{{ $order->id }}').submit();">
                                                    <i class="fas fa-times fa-sm fa-fw mr-2"></i> Cancel Order
                                                </a>
                                                <form id="status-cancel-{{ $order->id }}" action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="cancelled">
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent orders found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Pickups -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Pickups (Today)</h6>
                    <a href="{{ route('admin.orders.index', ['date' => today()->format('Y-m-d')]) }}" class="btn btn-sm btn-primary">View All Today</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Pickup Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingPickups as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($order->status == 'preparing')
                                            <span class="badge bg-info text-dark">Preparing</span>
                                        @elseif($order->status == 'ready')
                                            <span class="badge bg-success">Ready for Pickup</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown no-arrow">
                                            <a class="dropdown-toggle btn btn-sm btn-primary" href="#" role="button" id="dropdownMenuLink-pickup-{{ $order->id }}"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v fa-sm fa-fw"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                aria-labelledby="dropdownMenuLink-pickup-{{ $order->id }}">
                                                <a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}">
                                                    <i class="fas fa-eye fa-sm fa-fw mr-2"></i> View Details
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                @if($order->status == 'pending')
                                                <a class="dropdown-item text-info" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('pickup-status-preparing-{{ $order->id }}').submit();">
                                                    <i class="fas fa-fire fa-sm fa-fw mr-2"></i> Mark as Preparing
                                                </a>
                                                <form id="pickup-status-preparing-{{ $order->id }}" action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="preparing">
                                                </form>
                                                @endif
                                                
                                                @if($order->status == 'preparing')
                                                <a class="dropdown-item text-success" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('pickup-status-ready-{{ $order->id }}').submit();">
                                                    <i class="fas fa-check fa-sm fa-fw mr-2"></i> Mark as Ready
                                                </a>
                                                <form id="pickup-status-ready-{{ $order->id }}" action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="ready">
                                                </form>
                                                @endif
                                                
                                                @if($order->status == 'ready')
                                                <a class="dropdown-item text-primary" href="#" 
                                                   onclick="event.preventDefault(); document.getElementById('pickup-status-picked-{{ $order->id }}').submit();">
                                                    <i class="fas fa-shopping-bag fa-sm fa-fw mr-2"></i> Mark as Picked Up
                                                </a>
                                                <form id="pickup-status-picked-{{ $order->id }}" action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="status" value="picked_up">
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No upcoming pickups for today</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart (Orders & Revenue)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($orderChartLabels) !!},
            datasets: [
                {
                    label: 'Orders',
                    type: 'line',
                    data: {!! json_encode($orderChartData) !!},
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    borderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue ($)',
                    type: 'bar',
                    data: {!! json_encode($revenueChartData) !!},
                    backgroundColor: "rgba(28, 200, 138, 0.4)",
                    borderColor: "rgba(28, 200, 138, 1)",
                    borderWidth: 1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                },
                y: {
                    beginAtZero: true,
                    position: 'left',
                    grid: {
                        color: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    },
                    ticks: {
                        precision: 0,
                        stepSize: 1
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        display: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    titleMarginBottom: 10,
                    titleColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.raw;
                            if (label === 'Revenue ($)') {
                                return label + ': $' + parseFloat(value).toFixed(2);
                            } else {
                                return label + ': ' + value;
                            }
                        }
                    }
                }
            }
        }
    });

    // Function to toggle between orders, revenue, and both on the sales chart
    window.toggleChartView = function(type) {
        if (type === 'orders') {
            salesChart.data.datasets[0].hidden = false;
            salesChart.data.datasets[1].hidden = true;
        } else if (type === 'revenue') {
            salesChart.data.datasets[0].hidden = true;
            salesChart.data.datasets[1].hidden = false;
        } else {
            salesChart.data.datasets[0].hidden = false;
            salesChart.data.datasets[1].hidden = false;
        }
        salesChart.update();
    };

    // Payment Methods Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    const paymentMethodsChart = new Chart(paymentMethodsCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($paymentMethods->pluck('payment_method')->map(function($method) {
                return $method == 'stripe' ? 'Online (Stripe)' : 'Pay on Pickup';
            })) !!},
            datasets: [{
                data: {!! json_encode($paymentMethods->pluck('count')) !!},
                backgroundColor: {!! json_encode(array_slice($chartColors, 0, count($paymentMethods))) !!},
                hoverBackgroundColor: ['#2e59d9', '#17a673'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                }
            }
        }
    });

    // Popular Items Bar Chart
    const popularItemsBarCtx = document.getElementById('popularItemsBarChart').getContext('2d');
    const popularItemsBarChart = new Chart(popularItemsBarCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($popularItems->pluck('name')) !!},
            datasets: [{
                label: 'Quantity Sold',
                data: {!! json_encode($popularItems->pluck('count')) !!},
                backgroundColor: {!! json_encode(array_slice($chartColors, 0, count($popularItems))) !!},
                borderColor: {!! json_encode(array_slice($chartColors, 0, count($popularItems))) !!},
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Popular Items Pie Chart
    const popularItemsPieCtx = document.getElementById('popularItemsPieChart').getContext('2d');
    const popularItemsPieChart = new Chart(popularItemsPieCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($popularItems->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($popularItems->pluck('count')) !!},
                backgroundColor: {!! json_encode(array_slice($chartColors, 0, count($popularItems))) !!},
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f6c23e', '#e74a3b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                }
            }
        }
    });
});
</script>
@endpush

@endsection 