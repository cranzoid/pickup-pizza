@extends('layouts.admin')

@section('title', 'View Combo')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">{{ $combo->name }}</h1>
        <div>
            <a href="{{ route('admin.combos.edit', $combo) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Edit Combo
            </a>
            <a href="{{ route('admin.combos.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Combos
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Combo Details
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 150px;">Name:</th>
                                    <td>{{ $combo->name }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $combo->description ?? 'No description' }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $combo->category->name ?? 'No Category' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($combo->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 150px;">Price:</th>
                                    <td>${{ number_format($combo->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Regular Price:</th>
                                    <td>
                                        @if($combo->regular_price)
                                            ${{ number_format($combo->regular_price, 2) }}
                                            @php
                                                $savings = $combo->regular_price - $combo->price;
                                                $savingsPercent = ($savings / $combo->regular_price) * 100;
                                            @endphp
                                            <div class="mt-1">
                                                <span class="badge bg-success">
                                                    Save ${{ number_format($savings, 2) }} ({{ number_format($savingsPercent, 0) }}%)
                                                </span>
                                            </div>
                                        @else
                                            Not set
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $combo->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $combo->updated_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-box me-1"></i>
                    Products in this Combo
                </div>
                <div class="card-body">
                    @if($combo->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalValue = 0; @endphp
                                    @foreach($combo->products as $product)
                                        @php 
                                            $quantity = $product->pivot->quantity ?? 1;
                                            $itemTotal = $product->price * $quantity;
                                            $totalValue += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>${{ number_format($product->price, 2) }}</td>
                                            <td>{{ $quantity }}</td>
                                            <td>${{ number_format($itemTotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <th colspan="3">Total Value:</th>
                                        <th>${{ number_format($totalValue, 2) }}</th>
                                    </tr>
                                    <tr class="table-success">
                                        <th colspan="3">Combo Price:</th>
                                        <th>${{ number_format($combo->price, 2) }}</th>
                                    </tr>
                                    @if($totalValue > $combo->price)
                                        <tr class="table-info">
                                            <th colspan="3">Customer Savings:</th>
                                            <th>${{ number_format($totalValue - $combo->price, 2) }}</th>
                                        </tr>
                                    @endif
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>No products have been added to this combo.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-arrow-up me-1"></i>
                    Upsell Suggestions
                </div>
                <div class="card-body">
                    @if($combo->upsellProducts->count() > 0)
                        <div class="row">
                            @foreach($combo->upsellProducts as $product)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $product->name }}</h6>
                                            <p class="card-text text-muted mb-1">Price: ${{ number_format($product->price, 2) }}</p>
                                            <p class="card-text text-muted">{{ Str::limit($product->description, 50) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No upsell products have been set for this combo.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-image me-1"></i>
                    Combo Image
                </div>
                <div class="card-body text-center">
                    @if($combo->image)
                        <img src="{{ asset('storage/' . $combo->image) }}" alt="{{ $combo->name }}" class="img-fluid rounded mb-3" style="max-height: 300px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center p-5 mb-3" style="height: 300px;">
                            <i class="fas fa-pizza-slice fa-3x text-secondary"></i>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Order Statistics
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Total Orders
                            <span class="badge bg-primary rounded-pill">{{ $orderStats['total'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            This Month
                            <span class="badge bg-info rounded-pill">{{ $orderStats['thisMonth'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Last Month
                            <span class="badge bg-secondary rounded-pill">{{ $orderStats['lastMonth'] ?? 0 }}</span>
                        </div>
                    </div>
                    
                    @if(($orderStats['total'] ?? 0) > 0)
                        <div class="mt-3">
                            <a href="{{ route('admin.reports.orders', ['combo_id' => $combo->id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-chart-line me-2"></i>View Detailed Report
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog me-1"></i>
                    Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.combos.edit', $combo) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Combo
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Delete Combo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the <strong>{{ $combo->name }}</strong> combo?</p>
                
                @if(($orderStats['total'] ?? 0) > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This combo has been ordered {{ $orderStats['total'] }} times. Deleting it may affect order history.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.combos.destroy', $combo) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection 