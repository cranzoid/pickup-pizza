@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Category: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Categories
        </a>
    </div>
    
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control @error('display_order') is-invalid @enderror" id="display_order" name="display_order" value="{{ old('display_order', $category->display_order) }}" min="0">
                        @error('display_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Lower numbers will display first</div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="day_specific" name="day_specific" value="1" {{ old('day_specific', $category->day_specific) ? 'checked' : '' }}>
                        <label class="form-check-label" for="day_specific">Day-Specific Category</label>
                    </div>
                    <div class="form-text">Enable if this category should only appear on specific days (e.g., "Monday Specials")</div>
                </div>
                
                <div class="mb-3" id="specific_day_section" style="{{ old('day_specific', $category->day_specific) ? '' : 'display: none;' }}">
                    <label for="specific_day" class="form-label">Specific Day</label>
                    <select class="form-select @error('specific_day') is-invalid @enderror" id="specific_day" name="specific_day">
                        <option value="">Select Day</option>
                        <option value="monday" {{ old('specific_day', $category->specific_day) == 'monday' ? 'selected' : '' }}>Monday</option>
                        <option value="tuesday" {{ old('specific_day', $category->specific_day) == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                        <option value="wednesday" {{ old('specific_day', $category->specific_day) == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                        <option value="thursday" {{ old('specific_day', $category->specific_day) == 'thursday' ? 'selected' : '' }}>Thursday</option>
                        <option value="friday" {{ old('specific_day', $category->specific_day) == 'friday' ? 'selected' : '' }}>Friday</option>
                        <option value="saturday" {{ old('specific_day', $category->specific_day) == 'saturday' ? 'selected' : '' }}>Saturday</option>
                        <option value="sunday" {{ old('specific_day', $category->specific_day) == 'sunday' ? 'selected' : '' }}>Sunday</option>
                    </select>
                    @error('specific_day')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @if($category->products_count > 0)
    <div class="mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Products in this Category ({{ $category->products_count }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->name }}</td>
                                <td>
                                    @if($product->sizes)
                                        @foreach($product->sizes as $size => $details)
                                            {{ ucfirst($size) }}: ${{ number_format($details['price'], 2) }}<br>
                                        @endforeach
                                    @else
                                        ${{ number_format($product->price, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
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
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const daySpecificCheckbox = document.getElementById('day_specific');
    const specificDaySection = document.getElementById('specific_day_section');
    
    daySpecificCheckbox.addEventListener('change', function() {
        specificDaySection.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endpush

@endsection 