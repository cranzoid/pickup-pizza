@extends('layouts.admin')

@section('title', 'View Discount')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Discount: {{ $discount->code }}</h1>
        <div>
            <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Edit Discount
            </a>
            <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Discounts
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Discount Details
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 150px;">Code:</th>
                                    <td><span class="badge bg-dark">{{ $discount->code }}</span></td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $discount->name }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $discount->description ?? 'No description' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($discount->active)
                                            @if($discount->end_date && $discount->end_date->isPast())
                                                <span class="badge bg-secondary">Expired</span>
                                            @elseif($discount->start_date && $discount->start_date->isFuture())
                                                <span class="badge bg-info">Scheduled</span>
                                            @elseif($discount->max_uses && $discount->used_count >= $discount->max_uses)
                                                <span class="badge bg-warning">Exhausted</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width: 150px;">Type:</th>
                                    <td>{{ ucfirst($discount->type) }}</td>
                                </tr>
                                <tr>
                                    <th>Value:</th>
                                    <td>{{ $discount->formatted_value }}</td>
                                </tr>
                                <tr>
                                    <th>Min Order:</th>
                                    <td>
                                        @if($discount->min_order_amount)
                                            ${{ number_format($discount->min_order_amount, 2) }}
                                        @else
                                            No minimum
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Max Discount:</th>
                                    <td>
                                        @if($discount->max_discount_amount)
                                            ${{ number_format($discount->max_discount_amount, 2) }}
                                        @else
                                            No maximum
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-clock me-1"></i>
                    Time & Usage Restrictions
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Time Restrictions</h6>
                            <table class="table">
                                <tr>
                                    <th style="width: 150px;">Start Date:</th>
                                    <td>
                                        @if($discount->start_date)
                                            {{ $discount->start_date->format('M d, Y') }}
                                            
                                            @if($discount->start_date->isFuture())
                                                <span class="badge bg-info ms-2">Future</span>
                                            @endif
                                        @else
                                            No start date
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>End Date:</th>
                                    <td>
                                        @if($discount->end_date)
                                            {{ $discount->end_date->format('M d, Y') }}
                                            
                                            @if($discount->end_date->isPast())
                                                <span class="badge bg-danger ms-2">Expired</span>
                                            @elseif($discount->end_date->diffInDays(now()) < 7)
                                                <span class="badge bg-warning ms-2">Expiring soon</span>
                                            @endif
                                        @else
                                            No end date
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Usage Restrictions</h6>
                            <table class="table">
                                <tr>
                                    <th style="width: 150px;">Total Uses:</th>
                                    <td>
                                        @if($discount->max_uses)
                                            {{ $discount->used_count }} / {{ $discount->max_uses }}
                                            
                                            <div class="progress mt-1" style="height: 6px;">
                                                <div class="progress-bar {{ $discount->used_count >= $discount->max_uses ? 'bg-danger' : 'bg-success' }}" 
                                                    role="progressbar" 
                                                    style="width: {{ ($discount->used_count / $discount->max_uses) * 100 }}%"
                                                    aria-valuenow="{{ $discount->used_count }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="{{ $discount->max_uses }}">
                                                </div>
                                            </div>
                                        @else
                                            {{ $discount->used_count }} (unlimited)
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Uses Per Customer:</th>
                                    <td>
                                        @if($discount->max_uses_per_customer)
                                            {{ $discount->max_uses_per_customer }}
                                        @else
                                            Unlimited
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-box me-1"></i>
                    Product Restrictions
                </div>
                <div class="card-body">
                    @if($discount->products->count() > 0)
                        <h6>Applies to {{ $discount->products->count() }} specific products:</h6>
                        <div class="row">
                            @foreach($discount->products as $product)
                                <div class="col-md-4 mb-2">
                                    <div class="small">
                                        <i class="fas fa-tag text-secondary me-2"></i>
                                        {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($discount->categories->count() > 0)
                        <h6>Applies to {{ $discount->categories->count() }} specific categories:</h6>
                        <div class="row">
                            @foreach($discount->categories as $category)
                                <div class="col-md-4 mb-2">
                                    <div class="small">
                                        <i class="fas fa-folder text-secondary me-2"></i>
                                        {{ $category->name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This discount applies to all products.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Usage Statistics
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Total Uses
                            <span class="badge bg-primary rounded-pill">{{ $usageStats['total'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            This Month
                            <span class="badge bg-info rounded-pill">{{ $usageStats['thisMonth'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Last Month
                            <span class="badge bg-secondary rounded-pill">{{ $usageStats['lastMonth'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Total Savings
                            <span class="badge bg-success rounded-pill">${{ number_format($usageStats['totalSavings'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    
                    @if(($usageStats['total'] ?? 0) > 0)
                        <div class="mt-3">
                            <a href="{{ route('admin.reports.orders', ['discount_code' => $discount->code]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-chart-line me-2"></i>View Detailed Report
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Timeline
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <span class="timeline-point"></span>
                            <div class="timeline-content">
                                <h6 class="mb-0">Created</h6>
                                <p class="small text-muted mb-0">{{ $discount->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </li>
                        
                        @if($discount->start_date)
                            <li class="timeline-item">
                                <span class="timeline-point {{ $discount->start_date->isPast() ? 'bg-success' : 'bg-info' }}"></span>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Starts</h6>
                                    <p class="small text-muted mb-0">{{ $discount->start_date->format('M d, Y') }}</p>
                                </div>
                            </li>
                        @endif
                        
                        @if($discount->end_date)
                            <li class="timeline-item">
                                <span class="timeline-point {{ $discount->end_date->isPast() ? 'bg-danger' : 'bg-warning' }}"></span>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Expires</h6>
                                    <p class="small text-muted mb-0">{{ $discount->end_date->format('M d, Y') }}</p>
                                </div>
                            </li>
                        @endif
                        
                        @if($discount->updated_at->gt($discount->created_at))
                            <li class="timeline-item">
                                <span class="timeline-point bg-secondary"></span>
                                <div class="timeline-content">
                                    <h6 class="mb-0">Last Updated</h6>
                                    <p class="small text-muted mb-0">{{ $discount->updated_at->format('M d, Y g:i A') }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog me-1"></i>
                    Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Discount
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Delete Discount
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
                <p>Are you sure you want to delete the discount code <strong>{{ $discount->code }}</strong>?</p>
                
                @if($discount->used_count > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This code has been used {{ $discount->used_count }} times. Deleting it may affect order history.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 32px;
    list-style: none;
}

.timeline-item {
    position: relative;
    margin-bottom: 24px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-point {
    position: absolute;
    left: -32px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #0d6efd;
    top: 5px;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: -26px;
    height: 100%;
    width: 2px;
    background-color: #e9ecef;
}
</style>

@endsection 