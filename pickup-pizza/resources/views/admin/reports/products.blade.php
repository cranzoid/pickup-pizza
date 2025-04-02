@extends('layouts.admin')

@section('title', 'Products Report')

@section('styles')
<style>
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }
    .performance-indicator {
        font-size: 1.8rem;
        font-weight: 700;
    }
    .trend-indicator {
        font-size: 0.9rem;
    }
    .trend-up {
        color: #1cc88a;
    }
    .trend-down {
        color: #e74a3b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">Products Report</h1>
            <p class="lead">Analysis of product performance and sales.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
            <a href="{{ route('admin.reports.export-products', request()->query()) }}" class="btn btn-success">
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
            <form action="{{ route('admin.reports.products') }}" method="GET" class="row g-3 align-items-end">
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
                    <a href="{{ route('admin.reports.products') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Products Sold
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['total_quantity'] }}</div>
                            @if(isset($summary['quantity_change']))
                                <div class="small mt-2 {{ $summary['quantity_change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                                    <i class="fas fa-{{ $summary['quantity_change'] >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                    {{ number_format(abs($summary['quantity_change']), 1) }}% vs previous period
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-basket fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Product Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($summary['total_revenue'], 2) }}</div>
                            @if(isset($summary['revenue_change']))
                                <div class="small mt-2 {{ $summary['revenue_change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                                    <i class="fas fa-{{ $summary['revenue_change'] >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                    {{ number_format(abs($summary['revenue_change']), 1) }}% vs previous period
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Product Price
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($summary['avg_price'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Product Categories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['category_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
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
                    <i class="fas fa-chart-pie me-1"></i>
                    Sales by Category
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categorySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Top 10 Products
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Selling Products -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-star me-1"></i>
                Top Selling Products
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary active" id="sort-by-quantity">
                    Sort by Quantity
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="sort-by-revenue">
                    Sort by Revenue
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="top-products-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                            <th>% of Total Sales</th>
                            <th>Avg Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $index => $product)
                            <tr data-quantity="{{ $product->total_quantity }}" data-revenue="{{ $product->total_revenue }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                    {{ $product->name }}
                                </td>
                                <td>{{ $product->category ?? 'Uncategorized' }}</td>
                                <td>{{ $product->total_quantity }}</td>
                                <td>${{ number_format($product->total_revenue, 2) }}</td>
                                <td>{{ number_format($product->percentage, 1) }}%</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.reports.orders', ['product_id' => $product->id]) }}" class="btn btn-sm btn-info">
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
    
    <!-- Top Selling Combos -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-object-group me-1"></i>
            Top Selling Combos
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Combo</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                            <th>% of Total Sales</th>
                            <th>Savings</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCombos as $index => $combo)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($combo->image)
                                        <img src="{{ asset('storage/' . $combo->image) }}" alt="{{ $combo->name }}" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                    {{ $combo->name }}
                                </td>
                                <td>{{ $combo->total_quantity }}</td>
                                <td>${{ number_format($combo->total_revenue, 2) }}</td>
                                <td>{{ number_format($combo->percentage, 1) }}%</td>
                                <td>
                                    @php
                                        $savings = ($combo->regular_price - $combo->price) / $combo->regular_price * 100;
                                    @endphp
                                    {{ number_format($savings, 0) }}% (${{ number_format($combo->regular_price - $combo->price, 2) }})
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.combos.edit', $combo->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.reports.orders', ['combo_id' => $combo->id]) }}" class="btn btn-sm btn-info">
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
    
    <!-- Low Selling Products -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Low Selling Products
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                            <th>% of Total Sales</th>
                            <th>Last Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowSellingProducts as $product)
                            <tr>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                    {{ $product->name }}
                                </td>
                                <td>{{ $product->category ?? 'Uncategorized' }}</td>
                                <td>{{ $product->sold_count }}</td>
                                <td>${{ number_format($product->revenue ?? 0, 2) }}</td>
                                <td>{{ number_format($product->percentage ?? 0, 1) }}%</td>
                                <td>
                                    @if($product->last_order_date)
                                        {{ \Carbon\Carbon::parse($product->last_order_date)->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#createDiscountModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">
                                            <i class="fas fa-percent"></i>
                                        </button>
                                        <a href="{{ route('admin.reports.orders', ['product_id' => $product->id]) }}" class="btn btn-sm btn-info">
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
</div>

<!-- Create Discount Modal -->
<div class="modal fade" id="createDiscountModal" tabindex="-1" aria-labelledby="createDiscountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createDiscountModalLabel">Create Discount for <span id="productName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.discounts.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="productId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Discount Name</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., 'Summer Sale'">
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Discount Code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="code" name="code" required placeholder="e.g., 'SUMMER20'">
                            <button type="button" class="btn btn-outline-secondary" id="generateCode">Generate</button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="discount_type" class="form-label">Discount Type</label>
                        <select class="form-select" id="discount_type" name="type" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount ($)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="value" class="form-label">Discount Value</label>
                        <div class="input-group">
                            <span class="input-group-text" id="valueSymbol">%</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="value" name="value" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_uses" class="form-label">Maximum Uses (optional)</label>
                        <input type="number" min="1" class="form-control" id="max_uses" name="max_uses" placeholder="Leave blank for unlimited">
                    </div>
                    
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expiry Date (optional)</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category Sales Chart
        const categoryData = @json($salesByCategory);
        const categorySalesCtx = document.getElementById('categorySalesChart').getContext('2d');
        const categorySalesChart = new Chart(categorySalesCtx, {
            type: 'pie',
            data: {
                labels: categoryData.map(item => item.name || 'Uncategorized'),
                datasets: [{
                    data: categoryData.map(item => item.total_revenue),
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b',
                        '#fd7e14',
                        '#6f42c1',
                        '#20c9a6',
                        '#5a5c69',
                        '#858796'
                    ]
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Top Products Chart
        const topProductsData = @json($topProducts->take(10));
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        const topProductsChart = new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: topProductsData.map(item => item.name),
                datasets: [{
                    label: 'Units Sold',
                    data: topProductsData.map(item => item.total_quantity),
                    backgroundColor: '#4e73df'
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
        
        // Handle discount modal
        const discountModal = document.getElementById('createDiscountModal');
        if (discountModal) {
            discountModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const productId = button.getAttribute('data-product-id');
                const productName = button.getAttribute('data-product-name');
                
                document.getElementById('productId').value = productId;
                document.getElementById('productName').textContent = productName;
            });
        }
        
        // Handle discount type change
        const discountTypeSelect = document.getElementById('discount_type');
        const valueSymbol = document.getElementById('valueSymbol');
        
        if (discountTypeSelect && valueSymbol) {
            discountTypeSelect.addEventListener('change', function() {
                valueSymbol.textContent = this.value === 'percentage' ? '%' : '$';
            });
        }
        
        // Handle generate code button
        const generateCodeBtn = document.getElementById('generateCode');
        const codeInput = document.getElementById('code');
        
        if (generateCodeBtn && codeInput) {
            generateCodeBtn.addEventListener('click', function() {
                // Generate a random code
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let code = '';
                for (let i = 0; i < 8; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                codeInput.value = code;
            });
        }
        
        // Sort top products table
        const sortByQuantityBtn = document.getElementById('sort-by-quantity');
        const sortByRevenueBtn = document.getElementById('sort-by-revenue');
        const topProductsTable = document.getElementById('top-products-table');
        
        if (sortByQuantityBtn && sortByRevenueBtn && topProductsTable) {
            sortByQuantityBtn.addEventListener('click', function() {
                sortTable('quantity', true);
                sortByQuantityBtn.classList.add('active');
                sortByRevenueBtn.classList.remove('active');
            });
            
            sortByRevenueBtn.addEventListener('click', function() {
                sortTable('revenue', true);
                sortByRevenueBtn.classList.add('active');
                sortByQuantityBtn.classList.remove('active');
            });
            
            function sortTable(key, desc = false) {
                const tbody = topProductsTable.querySelector('tbody');
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