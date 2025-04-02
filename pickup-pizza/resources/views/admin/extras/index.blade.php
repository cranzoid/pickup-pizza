@extends('layouts.admin')

@section('title', 'Manage Product Extras')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mt-4">Product Extras</h1>
            <p class="lead">Manage product add-ons like dipping sauces, drinks, and toppings.</p>
        </div>
        <div>
            <a href="{{ route('admin.extras.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Extra
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Filters and Bulk Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <form action="{{ route('admin.extras.index') }}" method="GET" class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Search extras..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form id="bulkActionForm" action="{{ route('admin.extras.bulk-toggle-active') }}" method="POST" class="row g-2">
                        @csrf
                        <div class="col-md-8">
                            <select id="bulkAction" name="action" class="form-select">
                                <option value="">Bulk Actions</option>
                                <option value="activate">Activate</option>
                                <option value="deactivate">Deactivate</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="applyBulkAction" class="btn btn-outline-secondary w-100">Apply</button>
                        </div>
                        <input type="hidden" name="ids" id="selectedIds" value="">
                        <input type="hidden" name="active" id="toggleActive" value="1">
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Extras Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Product Extras
        </div>
        <div class="card-body">
            @if($extras->isEmpty())
                <div class="alert alert-info">No product extras found. Create your first product extra!</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th width="80">Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Max Qty</th>
                                <th>Status</th>
                                <th>Default</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($extras as $extra)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input item-checkbox" type="checkbox" value="{{ $extra->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        @if($extra->image)
                                            <img src="{{ asset('storage/' . $extra->image) }}" alt="{{ $extra->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-secondary"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.extras.edit', $extra->id) }}">{{ $extra->name }}</a>
                                        @if($extra->description)
                                            <small class="d-block text-muted">{{ Str::limit($extra->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $extra->category->name ?? 'Uncategorized' }}</td>
                                    <td>{{ $extra->formatted_price }}</td>
                                    <td>{{ $extra->max_quantity }}</td>
                                    <td>
                                        @if($extra->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($extra->is_default)
                                            <span class="badge bg-info">Default</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.extras.edit', $extra->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-extra-id="{{ $extra->id }}" data-extra-name="{{ $extra->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $extras->appends(request()->query())->links() }}
                </div>
            @endif
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
                Are you sure you want to delete <strong id="extraName"></strong>? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Bulk actions
        const applyBulkActionBtn = document.getElementById('applyBulkAction');
        const bulkActionSelect = document.getElementById('bulkAction');
        const selectedIdsInput = document.getElementById('selectedIds');
        const toggleActiveInput = document.getElementById('toggleActive');
        const bulkActionForm = document.getElementById('bulkActionForm');
        
        applyBulkActionBtn.addEventListener('click', function() {
            const selectedAction = bulkActionSelect.value;
            if (!selectedAction) {
                alert('Please select an action');
                return;
            }
            
            const selectedIds = Array.from(itemCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);
            
            if (selectedIds.length === 0) {
                alert('Please select at least one item');
                return;
            }
            
            selectedIdsInput.value = selectedIds.join(',');
            
            if (selectedAction === 'activate') {
                toggleActiveInput.value = '1';
            } else if (selectedAction === 'deactivate') {
                toggleActiveInput.value = '0';
            }
            
            bulkActionForm.submit();
        });
        
        // Delete modal
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const extraId = button.getAttribute('data-extra-id');
                const extraName = button.getAttribute('data-extra-name');
                
                const extraNameElement = document.getElementById('extraName');
                const deleteForm = document.getElementById('deleteForm');
                
                extraNameElement.textContent = extraName;
                deleteForm.action = "{{ route('admin.extras.index') }}/" + extraId;
            });
        }
    });
</script>
@endsection 