@extends('layouts.admin')

@section('title', 'View Product Extra')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">{{ $extra->name }}</h1>
            <p class="lead">Product extra details</p>
        </div>
        <div>
            <a href="{{ route('admin.extras.edit', $extra) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.extras.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Extras
            </a>
        </div>
    </div>
    
    <!-- Info Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Price</div>
                            <div class="h3">{{ $extra->formatted_price }}</div>
                        </div>
                        <div>
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card {{ $extra->active ? 'bg-success' : 'bg-secondary' }} text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Status</div>
                            <div class="h3">{{ $extra->active ? 'Active' : 'Inactive' }}</div>
                        </div>
                        <div>
                            <i class="fas {{ $extra->active ? 'fa-check-circle' : 'fa-times-circle' }} fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Category</div>
                            <div class="h3">{{ $extra->category->name ?? 'Uncategorized' }}</div>
                        </div>
                        <div>
                            <i class="fas fa-folder fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Max Quantity</div>
                            <div class="h3">{{ $extra->max_quantity }}</div>
                        </div>
                        <div>
                            <i class="fas fa-sort-amount-up fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Details Column -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Extra Details
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Description:</div>
                        <div class="col-md-8">{{ $extra->description ?: 'No description provided' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Default Selection:</div>
                        <div class="col-md-8">
                            @if($extra->is_default)
                                <span class="badge bg-success">Yes - Added by default</span>
                            @else
                                <span class="badge bg-secondary">No - Optional add-on</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Created:</div>
                        <div class="col-md-8">{{ $extra->created_at->format('F j, Y g:i A') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Last Updated:</div>
                        <div class="col-md-8">{{ $extra->updated_at->format('F j, Y g:i A') }}</div>
                    </div>
                    
                    @if($extra->image)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Image:</div>
                            <div class="col-md-8">
                                <img src="{{ asset('storage/' . $extra->image) }}" alt="{{ $extra->name }}" class="img-fluid img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i> Delete Extra
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Associated Products Column -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-link me-1"></i>
                    Associated Products
                </div>
                <div class="card-body">
                    @if($extra->products->count() > 0)
                        <div class="list-group">
                            @foreach($extra->products as $product)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <small>${{ number_format($product->price, 2) }}</small>
                                    </div>
                                    <p class="mb-1 small text-muted">
                                        {{ Str::limit($product->description, 100) }}
                                    </p>
                                    <small>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-primary">
                                            <i class="fas fa-edit me-1"></i> Edit Product
                                        </a>
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This extra is not associated with any products yet. 
                            <a href="{{ route('admin.extras.edit', $extra) }}" class="alert-link">Edit this extra</a> to associate it with products.
                        </div>
                    @endif
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
                <p>Are you sure you want to delete <strong>{{ $extra->name }}</strong>?</p>
                <p class="text-danger">This action cannot be undone. This will permanently remove the extra from the system.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.extras.destroy', $extra) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 