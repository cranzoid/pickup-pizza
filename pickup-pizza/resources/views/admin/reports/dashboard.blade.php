@extends('layouts.admin')

@section('title', 'Sales Dashboard')

@section('styles')
<style>
.stat-card {
    transition: transform 0.3s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.stat-card .card-body {
    position: relative;
    z-index: 1;
}
.stat-card .card-icon {
    position: absolute;
    right: 15px;
    bottom: 15px;
    font-size: 3rem;
    opacity: 0.15;
    z-index: 0;
}
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Sales Dashboard</h1>
    <p class="lead">Overview of your business performance.</p>
    
    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reports.dashboard') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Today's Revenue</div>
                            <div class="display-6">${{ number_format($todayStats['revenue'], 2) }}</div>
                        </div>
                    </div>
                    <div class="mt-2 small">
                        @php
                            $yesterdayRevenue = $yesterdayStats['revenue'] ?? 0;
                            $change = $yesterdayRevenue > 0 ? (($todayStats['revenue'] - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0;
                        @endphp
                        
                        @if($change > 0)
                            <i class="fas fa-arrow-up me-1"></i>{{ number_format(abs($change), 1) }}% vs yesterday
                        @elseif($change < 0)
                            <i class="fas fa-arrow-down me-1"></i>{{ number_format(abs($change), 1) }}% vs yesterday
                        @else
                            No change vs yesterday
                        @endif
                    </div>
                    <i class="fas fa-dollar-sign card-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Today's Orders</div>
                            <div class="display-6">{{ $todayStats['orders'] }}</div>
                        </div>
                    </div>
                    <div class="mt-2 small">
                        @php
                            $yesterdayOrders = $yesterdayStats['orders'] ?? 0;
                            $change = $yesterdayOrders > 0 ? (($todayStats['orders'] - $yesterdayOrders) / $yesterdayOrders) * 100 : 0;
                        @endphp
                        
                        @if($change > 0)
                            <i class="fas fa-arrow-up me-1"></i>{{ number_format(abs($change), 1) }}% vs yesterday
                        @elseif($change < 0)
                            <i class="fas fa-arrow-down me-1"></i>{{ number_format(abs($change), 1) }}% vs yesterday
                        @else
                            No change vs yesterday
                        @endif
                    </div>
                    <i class="fas fa-shopping-cart card-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Avg Order Value</div>
                            <div class="display-6">${{ number_format($todayStats['average_order'], 2) }}</div>
                        </div>
                    </div>
                    <div class="mt-2 small">
                        @php
                            $yesterdayAvg = $yesterdayStats['average_order'] ?? 0;
                            $change = $yesterdayAvg > 0 ? (($todayStats['average_order'] - $yesterdayAvg) / $yesterdayAvg) * 100 : 0;
                        @endphp
                        
                        @if($change > 0)
                            <i class="fas fa-arrow-up me-1"></i>{{ number_format(abs($change), 1) }}% vs yesterday
                        @elseif($change < 0)
                            <i class="fas fa-arrow-down me-1"></i>{{ number_format(abs($change), 1) }}% vs yesterday
                        @else
                            No change vs yesterday
                        @endif
                    </div>
                    <i class="fas fa-tags card-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Completion Rate</div>
                            <div class="display-6">
                                @php
                                    $completionRate = $todayStats['orders'] > 0 ? ($todayStats['completed_orders'] / $todayStats['orders']) * 100 : 0;
                                @endphp
                                {{ number_format($completionRate, 0) }}%
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 small">
                        {{ $todayStats['completed_orders'] }} out of {{ $todayStats['orders'] }} orders completed
                    </div>
                    <i class="fas fa-check-circle card-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Time Period Comparison -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-calendar-week me-1"></i>
                    Last 7 Days
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="small text-muted">Revenue</div>
                            <div class="h4 mb-0">${{ number_format($last7Days['revenue'], 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Orders</div>
                            <div class="h4 mb-0">{{ $last7Days['orders'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Avg Order</div>
                            <div class="h4 mb-0">${{ number_format($last7Days['average_order'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-primary stretched-link" href="{{ route('admin.reports.orders', ['start_date' => $last7Days['start_date'], 'end_date' => $last7Days['end_date']]) }}">View Details</a>
                    <div class="small text-primary"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Last 30 Days
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="small text-muted">Revenue</div>
                            <div class="h4 mb-0">${{ number_format($last30Days['revenue'], 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Orders</div>
                            <div class="h4 mb-0">{{ $last30Days['orders'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Avg Order</div>
                            <div class="h4 mb-0">${{ number_format($last30Days['average_order'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-primary stretched-link" href="{{ route('admin.reports.orders', ['start_date' => $last30Days['start_date'], 'end_date' => $last30Days['end_date']]) }}">View Details</a>
                    <div class="small text-primary"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-calendar-check me-1"></i>
                    {{ Carbon\Carbon::today()->format('F Y') }}
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="small text-muted">Revenue</div>
                            <div class="h4 mb-0">${{ number_format($currentMonthStats['revenue'], 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Orders</div>
                            <div class="h4 mb-0">{{ $currentMonthStats['orders'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Avg Order</div>
                            <div class="h4 mb-0">${{ number_format($currentMonthStats['average_order'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-primary stretched-link" href="{{ route('admin.reports.orders', ['start_date' => $currentMonthStats['start_date'], 'end_date' => $currentMonthStats['end_date']]) }}">View Details</a>
                    <div class="small text-primary"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Daily Sales (Last 30 Days)
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" width="100%" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-alt me-1"></i>
                    Reports
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.reports.orders') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Orders Report</h6>
                                <small><i class="fas fa-chevron-right"></i></small>
                            </div>
                            <p class="mb-1 small text-muted">View and filter detailed order data</p>
                        </a>
                        <a href="{{ route('admin.reports.products') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Products Report</h6>
                                <small><i class="fas fa-chevron-right"></i></small>
                            </div>
                            <p class="mb-1 small text-muted">Analyze product performance</p>
                        </a>
                        <a href="{{ route('admin.reports.discounts') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Discounts Report</h6>
                                <small><i class="fas fa-chevron-right"></i></small>
                            </div>
                            <p class="mb-1 small text-muted">Track discount code usage and impact</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog me-1"></i>
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-4">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-lg d-block py-3">
                                <i class="fas fa-pizza-slice fa-2x mb-2"></i><br>
                                Manage Orders
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-4">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary btn-lg d-block py-3">
                                <i class="fas fa-box fa-2x mb-2"></i><br>
                                Manage Products
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-4">
                            <a href="{{ route('admin.combos.index') }}" class="btn btn-outline-primary btn-lg d-block py-3">
                                <i class="fas fa-object-group fa-2x mb-2"></i><br>
                                Manage Combos
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-4">
                            <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-primary btn-lg d-block py-3">
                                <i class="fas fa-percent fa-2x mb-2"></i><br>
                                Manage Discounts
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Daily Sales Chart
    const salesCtx = document.getElementById('dailySalesChart').getContext('2d');
    
    const dailySalesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySales['dates']) !!},
            datasets: [
                {
                    label: 'Revenue',
                    data: {!! json_encode($dailySales['revenue']) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(0, 123, 255, 1)',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.2,
                    yAxisID: 'y'
                },
                {
                    label: 'Orders',
                    data: {!! json_encode($dailySales['orders']) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: false,
                    tension: 0.2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: {
                        drawOnChartArea: false,
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    },
                    grid: {
                        drawOnChartArea: true,
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Orders'
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label === 'Revenue') {
                                label += '$' + parseFloat(context.raw).toFixed(2);
                            } else {
                                label += context.raw;
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection 