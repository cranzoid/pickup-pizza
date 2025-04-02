@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Product: {{ $product->name }}</h1>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>New Product
            </a>
        </div>
    </div>
    
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Basic Information</h5>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category *</label>
                                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="display_order" class="form-label">Display Order</label>
                                        <input type="number" class="form-control @error('display_order') is-invalid @enderror" id="display_order" name="display_order" value="{{ old('display_order', $product->display_order) }}" min="0">
                                        @error('display_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Lower numbers will display first</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pricing Options -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Pricing Options</h5>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="has_sizes" name="has_sizes" value="1" {{ old('has_sizes', $product->has_sizes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_sizes">This product has different sizes</label>
                                </div>
                            </div>
                            
                            <div id="single_price_container" class="{{ old('has_sizes', $product->has_sizes) ? 'd-none' : '' }}">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}">
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div id="sizes_container" class="{{ old('has_sizes', $product->has_sizes) ? '' : 'd-none' }}">
                                <div class="card">
                                    <div class="card-body bg-light">
                                        <div class="row mb-2">
                                            <div class="col-md-4"><strong>Size</strong></div>
                                            <div class="col-md-4"><strong>Price</strong></div>
                                            <div class="col-md-4"><strong>Description</strong></div>
                                        </div>
                                        
                                        @foreach(['small', 'medium', 'large', 'extra_large'] as $size)
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    @php
                                                        $isActive = old("sizes.{$size}.active", isset($product->sizes[$size]));
                                                        $price = old("sizes.{$size}.price", $product->sizes[$size]['price'] ?? '');
                                                        $description = old("sizes.{$size}.description", $product->sizes[$size]['description'] ?? '');
                                                    @endphp
                                                    
                                                    <input class="form-check-input size-checkbox" type="checkbox" id="size_{{ $size }}" 
                                                        name="sizes[{{ $size }}][active]" value="1" 
                                                        {{ $isActive ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="size_{{ $size }}">
                                                        {{ ucwords(str_replace('_', ' ', $size)) }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0" 
                                                        class="form-control size-price" 
                                                        name="sizes[{{ $size }}][price]" 
                                                        value="{{ $price }}"
                                                        {{ $isActive ? '' : 'disabled' }}>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control size-description" 
                                                    name="sizes[{{ $size }}][description]" 
                                                    value="{{ $description }}" 
                                                    placeholder="e.g., 10-inch"
                                                    {{ $isActive ? '' : 'disabled' }}>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Options -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Options</h5>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="has_toppings" name="has_toppings" value="1" {{ old('has_toppings', $product->has_toppings) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_toppings">Allow toppings selection</label>
                                </div>
                                <div class="form-text">Enable if customers can add toppings to this item</div>
                            </div>
                            
                            <div id="toppings_container" class="{{ old('has_toppings', $product->has_toppings) ? '' : 'd-none' }}">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="max_toppings" class="form-label">Maximum number of toppings</label>
                                        <input type="number" class="form-control" id="max_toppings" name="max_toppings" value="{{ old('max_toppings', $product->max_toppings) }}" min="1">
                                        <div class="form-text">Enter 0 for unlimited</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="free_toppings" class="form-label">Number of free toppings</label>
                                        <input type="number" class="form-control" id="free_toppings" name="free_toppings" value="{{ old('free_toppings', $product->free_toppings) }}" min="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="has_extras" name="has_extras" value="1" {{ old('has_extras', $product->has_extras) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_extras">Allow extras selection</label>
                                </div>
                                <div class="form-text">Enable if customers can add extras (like dipping sauces, etc.) to this item</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Image Upload -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Product Image</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    @if($product->image_path)
                                        <img id="image_preview" src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                                    @else
                                        <img id="image_preview" src="{{ asset('img/no-image.png') }}" alt="Product image preview" class="img-fluid rounded mb-3" style="max-height: 200px;">
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Upload Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Recommended size: 600x400 pixels, max 2MB</div>
                                </div>
                                
                                @if($product->image_path)
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                        <label class="form-check-label text-danger" for="remove_image">
                                            Remove current image
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Status -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Status & Visibility</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                    <div class="form-text">Inactive products won't appear on the menu</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Featured Product</label>
                                    </div>
                                    <div class="form-text">Featured products appear on the homepage</div>
                                </div>
                            </div>
                        </div>
                        
                        @if($product->orders_count > 0)
                        <div class="card mb-4 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Order History</h5>
                            </div>
                            <div class="card-body">
                                <p>This product has been ordered <strong>{{ $product->orders_count }}</strong> times.</p>
                                <p>Last ordered: <strong>{{ $product->last_ordered_at->format('M d, Y') }}</strong></p>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Changing this product may affect order history.
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Size pricing toggle
    const hasSizesCheckbox = document.getElementById('has_sizes');
    const singlePriceContainer = document.getElementById('single_price_container');
    const sizesContainer = document.getElementById('sizes_container');
    
    hasSizesCheckbox.addEventListener('change', function() {
        singlePriceContainer.classList.toggle('d-none', this.checked);
        sizesContainer.classList.toggle('d-none', !this.checked);
    });
    
    // Toppings toggle
    const hasToppingsCheckbox = document.getElementById('has_toppings');
    const toppingsContainer = document.getElementById('toppings_container');
    
    hasToppingsCheckbox.addEventListener('change', function() {
        toppingsContainer.classList.toggle('d-none', !this.checked);
    });
    
    // Size checkbox enable/disable fields
    const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
    sizeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('.row');
            const priceInput = row.querySelector('.size-price');
            const descriptionInput = row.querySelector('.size-description');
            
            priceInput.disabled = !this.checked;
            descriptionInput.disabled = !this.checked;
        });
    });
    
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image_preview');
    
    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
@endpush

@endsection 