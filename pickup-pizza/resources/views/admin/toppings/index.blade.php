@extends('layouts.admin')

@section('title', 'Toppings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Toppings</h1>
        <a href="{{ route('admin.toppings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Topping
        </a>
    </div>
    
    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Toppings</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.toppings.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="meat" {{ request('type') == 'meat' ? 'selected' : '' }}>Meat</option>
                        <option value="veggie" {{ request('type') == 'veggie' ? 'selected' : '' }}>Vegetable</option>
                        <option value="cheese" {{ request('type') == 'cheese' ? 'selected' : '' }}>Cheese</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name...">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.toppings.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Toppings Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Display Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($toppings as $topping)
                        <tr>
                            <td>{{ $topping->id }}</td>
                            <td>{{ $topping->name }}</td>
                            <td>
                                <span class="badge bg-{{ $topping->type == 'meat' ? 'danger' : 
                                    ($topping->type == 'veggie' ? 'success' : 
                                    ($topping->type == 'cheese' ? 'warning' : 'info')) }}">
                                    {{ ucfirst($topping->type) }}
                                </span>
                            </td>
                            <td>${{ number_format($topping->price, 2) }}</td>
                            <td>{{ $topping->display_order }}</td>
                            <td>
                                <span class="badge bg-{{ $topping->is_active ? 'success' : 'danger' }}">
                                    {{ $topping->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.toppings.edit', $topping) }}" class="btn btn-sm btn-info" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $topping->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $topping->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $topping->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $topping->id }}">Delete Topping</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete <strong>{{ $topping->name }}</strong>?</p>
                                                <p class="text-danger">This action cannot be undone.</p>
                                                @if($topping->orders_count > 0)
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    This topping has been used in {{ $topping->orders_count }} orders. Deleting it may affect order history.
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.toppings.destroy', $topping) }}" method="POST">
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
            
            @if($toppings->isEmpty())
            <div class="text-center py-4">
                <i class="fas fa-cheese fa-3x text-muted mb-3"></i>
                <p class="lead">No toppings found</p>
                @if(request('search') || request('type') || request('status'))
                    <p>Try changing the search filters.</p>
                @else
                    <p>Start by adding your first topping.</p>
                @endif
            </div>
            @endif
            
            <div class="d-flex justify-content-center mt-4">
                {{ $toppings->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 