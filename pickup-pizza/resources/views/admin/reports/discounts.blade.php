@extends('layouts.admin')

@section('title', 'Discounts Report')

@section('styles')
<style>
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }
    .metric-card {
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        padding: 1.5rem;
        transition: transform 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-5px);
    }
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
    }
    .roi-indicator {
        font-size: 1.5rem;
        font-weight: 600;
    }
    .roi-good {
        color: #1cc88a;
    }
    .roi-medium {
        color: #f6c23e;
    }
    .roi-poor {
        color: #e74a3b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">Discounts Report</h1>
            <p class="lead">Analysis of discount usage and impact on sales.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
            <a href="{{ route('admin.reports.export-discounts', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Export to CSV
            </a>
        </div>
    </div>
    
    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Date Range
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.discounts') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('admin.reports.discounts') }}" class="btn btn-outline-secondary">Reset</a>
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
                            <div class="small text-white-50">Total Discounted Orders</div>
                            <div class="display-6">{{ $summary['total_discounted_orders'] }}</div>
                            <div class="small mt-2">
                                {{ number_format($summary['discount_order_percentage'], 1) }}% of all orders
                            </div>
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
                            <div class="small text-white-50">Total Discount Amount</div>
                            <div class="display-6">${{ number_format($summary['total_discount_amount'], 2) }}</div>
                            <div class="small mt-2">
                                {{ number_format($summary['discount_revenue_percentage'], 1) }}% of total revenue
                            </div>
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
                            <div class="small text-white-50">Average Discount Value</div>
                            <div class="display-6">${{ number_format($summary['average_discount'], 2) }}</div>
                            <div class="small mt-2">
                                Per discounted order
                            </div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x opacity-50"></i>
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
                            <div class="small text-white-50">Average Order Increase</div>
                            <div class="display-6">{{ number_format($summary['avg_order_increase'], 1) }}%</div>
                            <div class="small mt-2">
                                vs non-discounted orders
                            </div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ROI Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Discount ROI Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-4 mb-md-0">
                            <div class="metric-card bg-gradient-primary text-white">
                                <h6 class="text-uppercase mb-1 text-white-50">Overall ROI</h6>
                                <div class="roi-indicator {{ $summary['overall_roi'] >= 4 ? 'roi-good' : ($summary['overall_roi'] >= 2 ? 'roi-medium' : 'roi-poor') }}">
                                    {{ number_format($summary['overall_roi'], 1) }}x
                                </div>
                                <div class="small mt-2">
                                    $1 discount = ${{ number_format($summary['overall_roi'], 2) }} revenue
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 mb-md-0">
                            <div class="metric-card bg-white">
                                <h6 class="text-uppercase mb-1 text-primary">Average Cart Size</h6>
                                <div class="metric-value">
                                    ${{ number_format($summary['avg_discounted_order'], 2) }}
                                </div>
                                <div class="small mt-2 {{ $summary['avg_order_increase'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-{{ $summary['avg_order_increase'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ number_format(abs($summary['avg_order_increase']), 1) }}% vs non-discounted
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 mb-md-0">
                            <div class="metric-card bg-white">
                                <h6 class="text-uppercase mb-1 text-primary">Total Discounted Revenue</h6>
                                <div class="metric-value">
                                    ${{ number_format($summary['discounted_orders_revenue'], 2) }}
                                </div>
                                <div class="small mt-2">
                                    From {{ $summary['total_discounted_orders'] }} orders
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card bg-white">
                                <h6 class="text-uppercase mb-1 text-primary">Active Discounts</h6>
                                <div class="metric-value">
                                    {{ $summary['active_discounts_count'] }}
                                </div>
                                <div class="small mt-2">
                                    of {{ $summary['total_discounts_count'] }} total
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Top Discount Codes by Usage
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="discountUsageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Discounted Orders Over Time
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="discountTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Discount Codes -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-star me-1"></i>
                Top Performing Discount Codes
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary active" id="sort-by-usage">
                    Sort by Usage
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="sort-by-revenue">
                    Sort by Revenue
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="sort-by-roi">
                    Sort by ROI
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="discounts-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Discount Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Uses</th>
                            <th>Total Discount</th>
                            <th>Avg Order Value</th>
                            <th>ROI</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topDiscounts as $index => $discount)
                            <tr data-usage="{{ $discount->used_count }}" data-revenue="{{ $discount->total_order_revenue }}" data-roi="{{ $discount->roi }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $discount->code }}</strong><br>
                                    <small class="text-muted">{{ $discount->name }}</small>
                                </td>
                                <td>
                                    @if($discount->type === 'percentage')
                                        Percentage
                                    @elseif($discount->type === 'fixed')
                                        Fixed Amount
                                    @endif
                                </td>
                                <td>
                                    @if($discount->type === 'percentage')
                                        {{ number_format($discount->value, 0) }}%
                                    @elseif($discount->type === 'fixed')
                                        ${{ number_format($discount->value, 2) }}
                                    @endif
                                </td>
                                <td>{{ $discount->used_count }}</td>
                                <td>${{ number_format($discount->total_discount_amount, 2) }}</td>
                                <td>${{ number_format($discount->average_order_value, 2) }}</td>
                                <td>
                                    <span class="{{ $discount->roi >= 4 ? 'text-success' : ($discount->roi >= 2 ? 'text-warning' : 'text-danger') }}">
                                        {{ number_format($discount->roi, 1) }}x
                                    </span>
                                </td>
                                <td>
                                    @if($discount->active)
                                        @if($discount->is_expired)
                                            <span class="badge bg-danger">Expired</span>
                                        @elseif($discount->is_max_reached)
                                            <span class="badge bg-warning text-dark">Max Uses Reached</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.discounts.show', $discount->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.discounts.edit', $discount->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.reports.orders', ['discount_code' => $discount->code]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Orders with Discounts -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-receipt me-1"></i>
                Recent Orders with Discounts
            </div>
            <a href="{{ route('admin.reports.orders', ['has_discount' => 1]) }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Discount Code</th>
                            <th>Discount Amount</th>
                            <th>Order Total</th>
                            <th>% Saved</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discountedOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y g:i A') }}</td>
                                <td>
                                    {{ $order->customer_name }}<br>
                                    <small class="text-muted">{{ $order->customer_email }}</small>
                                </td>
                                <td><span class="badge bg-purple">{{ $order->discount_code }}</span></td>
                                <td>${{ number_format($order->discount_amount, 2) }}</td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>
                                    @php
                                        $originalTotal = $order->total + $order->discount_amount;
                                        $savedPercentage = $originalTotal > 0 ? ($order->discount_amount / $originalTotal) * 100 : 0;
                                    @endphp
                                    {{ number_format($savedPercentage, 1) }}%
                                </td>
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
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Discount Usage Chart
    const discountUsageCtx = document.getElementById('discountUsageChart').getContext('2d');
    const discountData = @json($topDiscounts->take(6));
    
    const discountUsageChart = new Chart(discountUsageCtx, {
        type: 'bar',
        data: {
            labels: discountData.map(d => d.code),
            datasets: [{
                label: 'Usage Count',
                data: discountData.map(d => d.used_count),
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
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
            }
        }
    });
    
    // Discount Time Chart
    const discountTimeCtx = document.getElementById('discountTimeChart').getContext('2d');
    const chartData = @json($chartData);
    
    const discountTimeChart = new Chart(discountTimeCtx, {
        type: 'line',
        data: {
            labels: chartData.dates,
            datasets: [
                {
                    label: 'Orders with Discounts',
                    data: chartData.counts,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Discount Amount ($)',
                    data: chartData.amounts,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    ticks: {
                        precision: 0
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
    
    // Sorting functionality for the discounts table
    const sortByUsageBtn = document.getElementById('sort-by-usage');
    const sortByRevenueBtn = document.getElementById('sort-by-revenue');
    const sortByRoiBtn = document.getElementById('sort-by-roi');
    const discountsTable = document.getElementById('discounts-table');
    
    if (sortByUsageBtn && sortByRevenueBtn && sortByRoiBtn && discountsTable) {
        sortByUsageBtn.addEventListener('click', function() {
            sortTable('usage', true);
            this.classList.add('active');
            sortByRevenueBtn.classList.remove('active');
            sortByRoiBtn.classList.remove('active');
        });
        
        sortByRevenueBtn.addEventListener('click', function() {
            sortTable('revenue', true);
            this.classList.add('active');
            sortByUsageBtn.classList.remove('active');
            sortByRoiBtn.classList.remove('active');
        });
        
        sortByRoiBtn.addEventListener('click', function() {
            sortTable('roi', true);
            this.classList.add('active');
            sortByUsageBtn.classList.remove('active');
            sortByRevenueBtn.classList.remove('active');
        });
        
        function sortTable(key, desc = false) {
            const tbody = discountsTable.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                const aValue = parseFloat(a.getAttribute(`data-${key}`));
                const bValue = parseFloat(b.getAttribute(`data-${key}`));
                
                return desc ? bValue - aValue : aValue - bValue;
            });
            
            // Clear the tbody
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }
            
            // Update the rank column and re-append the sorted rows
            rows.forEach((row, index) => {
                row.querySelector('td:first-child').textContent = index + 1;
                tbody.appendChild(row);
            });
        }
    }
});
</script>
@endpush 