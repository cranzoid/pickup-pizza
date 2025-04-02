@extends('layouts.admin')

@section('title', 'Edit Combo')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Edit Combo: {{ $combo->name }}</h1>
        <a href="{{ route('admin.combos.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Combos
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-utensils me-1"></i>
            Combo Details
        </div>
        <div class="card-body">
            <form action="{{ route('admin.combos.update', $combo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Combo Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $combo->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $combo->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $combo->category_id) == $category->id ? 'selected' : '' }}>
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
                        <div class="form-group mb-3">
                            <label for="price" class="form-label">Combo Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $combo->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="regular_price" class="form-label">Regular Price (for calculating savings)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('regular_price') is-invalid @enderror" id="regular_price" name="regular_price" value="{{ old('regular_price', $combo->regular_price) }}">
                                @error('regular_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Leave empty if not applicable</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="image" class="form-label">Combo Image</label>
                            @if($combo->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $combo->image) }}" alt="{{ $combo->name }}" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                    <label class="form-check-label" for="remove_image">Remove current image</label>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', $combo->active) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Products in this Combo</h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Select products to include in this combo. The combination should typically be worth more than the combo price.
                </div>
                
                <div class="row mb-4">
                    @foreach($products as $product)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input product-checkbox" type="checkbox" 
                                            name="products[]" 
                                            value="{{ $product->id }}" 
                                            id="product{{ $product->id }}"
                                            data-price="{{ $product->price }}"
                                            {{ in_array($product->id, old('products', $comboProducts)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="product{{ $product->id }}">
                                            <strong>{{ $product->name }}</strong>
                                            <span class="d-block text-muted">${{ number_format($product->price, 2) }}</span>
                                        </label>
                                    </div>
                                    
                                    <div class="mt-2 product-options" id="options{{ $product->id }}" style="{{ in_array($product->id, old('products', $comboProducts)) ? '' : 'display: none;' }}">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Qty</span>
                                            <input type="number" min="1" class="form-control" 
                                                name="product_quantities[{{ $product->id }}]" 
                                                value="{{ old('product_quantities.' . $product->id, $productQuantities[$product->id] ?? 1) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <hr>
                
                <h5 class="mb-3">Upsell Options (Optional)</h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Select products to suggest as add-ons when this combo is added to cart.
                </div>
                
                <div class="row mb-4">
                    @foreach($products as $product)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input upsell-checkbox" type="checkbox" 
                                            name="upsell_products[]" 
                                            value="{{ $product->id }}" 
                                            id="upsell{{ $product->id }}"
                                            {{ in_array($product->id, old('upsell_products', $upsellProducts)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="upsell{{ $product->id }}">
                                            <strong>{{ $product->name }}</strong>
                                            <span class="d-block text-muted">${{ number_format($product->price, 2) }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.combos.index') }}" class="btn btn-outline-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide product quantity inputs when checkboxes are clicked
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const productId = this.value;
                const optionsDiv = document.getElementById('options' + productId);
                if (this.checked) {
                    optionsDiv.style.display = 'block';
                } else {
                    optionsDiv.style.display = 'none';
                }
            });
        });
        
        // Calculate total value and savings dynamically
        const priceInput = document.getElementById('price');
        const regularPriceInput = document.getElementById('regular_price');
        
        function calculateTotalValue() {
            let totalValue = 0;
            productCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const productId = checkbox.value;
                    const quantity = parseInt(document.querySelector(`input[name="product_quantities[${productId}]"]`).value) || 1;
                    const price = parseFloat(checkbox.dataset.price);
                    totalValue += price * quantity;
                }
            });
            
            regularPriceInput.value = totalValue.toFixed(2);
        }
        
        // Calculate on checkbox change
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', calculateTotalValue);
        });
        
        // Calculate when quantity changes
        document.addEventListener('input', function(e) {
            if (e.target.name && e.target.name.startsWith('product_quantities[')) {
                calculateTotalValue();
            }
        });
    });
</script>
@endsection 