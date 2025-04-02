@extends('layouts.admin')

@section('title', 'Manage Discounts')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Discount Codes</h1>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Discount
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-percent me-1"></i>
            All Discount Codes
        </div>
        <div class="card-body">
            @if($discounts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Usage</th>
                                <th>Valid Until</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discounts as $discount)
                                <tr>
                                    <td><span class="badge bg-dark">{{ $discount->code }}</span></td>
                                    <td>{{ $discount->name }}</td>
                                    <td>{{ ucfirst($discount->type) }}</td>
                                    <td>
                                        @if($discount->type === 'percentage')
                                            {{ $discount->value }}%
                                        @else
                                            ${{ number_format($discount->value, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->max_uses)
                                            {{ $discount->used_count }}/{{ $discount->max_uses }}
                                        @else
                                            {{ $discount->used_count }} uses
                                        @endif
                                    </td>
                                    <td>
                                        @if($discount->end_date)
                                            {{ $discount->end_date->format('M d, Y') }}
                                        @else
                                            No expiry
                                        @endif
                                    </td>
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
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.discounts.show', $discount) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $discount->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $discount->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $discount->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $discount->id }}">Confirm Delete</h5>
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $discounts->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No discount codes found. <a href="{{ route('admin.discounts.create') }}">Create your first discount code</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 