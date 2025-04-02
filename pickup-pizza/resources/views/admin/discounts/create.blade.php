@extends('layouts.admin')

@section('title', 'Create Discount')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Create New Discount</h1>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Discounts
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-percent me-1"></i>
            Discount Details
        </div>
        <div class="card-body">
            <form action="{{ route('admin.discounts.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Discount Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">A descriptive name for internal reference</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="code" class="form-label">Discount Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}">
                                <button class="btn btn-outline-secondary" type="button" id="generate-code">Generate</button>
                            </div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave empty to auto-generate a code</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional description (internal use only)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">-- Select Type --</option>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%) off</option>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($) off</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" required>
                                <span class="input-group-text" id="value-addon">%</span>
                            </div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                            <small class="text-muted">Uncheck to disable this discount temporarily</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Restrictions & Limitations</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="min_order_amount" class="form-label">Minimum Order Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('min_order_amount') is-invalid @enderror" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount') }}">
                            </div>
                            @error('min_order_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave empty for no minimum</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="max_discount_amount" class="form-label">Maximum Discount Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('max_discount_amount') is-invalid @enderror" id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount') }}">
                            </div>
                            @error('max_discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Cap on discount amount (useful for percentage discounts)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave empty to start immediately</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave empty for no expiry</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_uses" class="form-label">Usage Limit (Total)</label>
                            <input type="number" min="1" class="form-control @error('max_uses') is-invalid @enderror" id="max_uses" name="max_uses" value="{{ old('max_uses') }}">
                            @error('max_uses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum number of times this code can be used (leave empty for unlimited)</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_uses_per_customer" class="form-label">Usage Limit (Per Customer)</label>
                            <input type="number" min="1" class="form-control @error('max_uses_per_customer') is-invalid @enderror" id="max_uses_per_customer" name="max_uses_per_customer" value="{{ old('max_uses_per_customer') }}">
                            @error('max_uses_per_customer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">How many times each customer can use this code</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h5 class="mb-3">Product Restrictions</h5>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input apply-to-radio" type="radio" name="apply_to" id="apply_to_all" value="all_products" {{ old('apply_to', 'all_products') == 'all_products' ? 'checked' : '' }}>
                        <label class="form-check-label" for="apply_to_all">
                            Apply to all products
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input apply-to-radio" type="radio" name="apply_to" id="apply_to_categories" value="specific_categories" {{ old('apply_to') == 'specific_categories' ? 'checked' : '' }}>
                        <label class="form-check-label" for="apply_to_categories">
                            Apply to specific categories
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input apply-to-radio" type="radio" name="apply_to" id="apply_to_products" value="specific_products" {{ old('apply_to') == 'specific_products' ? 'checked' : '' }}>
                        <label class="form-check-label" for="apply_to_products">
                            Apply to specific products
                        </label>
                    </div>
                </div>
                
                <div id="categories_container" class="mb-4 restriction-container" style="{{ old('apply_to') == 'specific_categories' ? '' : 'display: none;' }}">
                    <label class="form-label">Select Categories</label>
                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="category{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div id="products_container" class="mb-4 restriction-container" style="{{ old('apply_to') == 'specific_products' ? '' : 'display: none;' }}">
                    <label class="form-label">Select Products</label>
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="{{ $product->id }}" id="product{{ $product->id }}"
                                        {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="product{{ $product->id }}">
                                        {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate random code
        const generateCodeBtn = document.getElementById('generate-code');
        const codeInput = document.getElementById('code');
        
        generateCodeBtn.addEventListener('click', function() {
            const randomCode = Math.random().toString(36).substring(2, 10).toUpperCase();
            codeInput.value = randomCode;
        });
        
        // Toggle discount type indicator
        const typeSelect = document.getElementById('type');
        const valueAddon = document.getElementById('value-addon');
        
        typeSelect.addEventListener('change', function() {
            if (this.value === 'percentage') {
                valueAddon.textContent = '%';
            } else if (this.value === 'fixed') {
                valueAddon.textContent = '$';
            }
        });
        
        // Show/hide product/category selections based on apply_to selection
        const applyToRadios = document.querySelectorAll('.apply-to-radio');
        const categoriesContainer = document.getElementById('categories_container');
        const productsContainer = document.getElementById('products_container');
        
        applyToRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'specific_categories') {
                    categoriesContainer.style.display = 'block';
                    productsContainer.style.display = 'none';
                } else if (this.value === 'specific_products') {
                    productsContainer.style.display = 'block';
                    categoriesContainer.style.display = 'none';
                } else {
                    categoriesContainer.style.display = 'none';
                    productsContainer.style.display = 'none';
                }
            });
        });
        
        // Set initial state of value addon based on selected discount type
        if (typeSelect.value === 'percentage') {
            valueAddon.textContent = '%';
        } else if (typeSelect.value === 'fixed') {
            valueAddon.textContent = '$';
        }
    });
</script>
@endsection 