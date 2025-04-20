@extends('layouts.app')

@section('title', $product->name)

@section('content')
    @php
        // Define default size
        $firstSize = 'medium';
        if($product->has_size_options) {
            $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
            $firstSize = array_key_first($sizes);
        }
    @endphp
    
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('menu.category', $category->slug) }}">{{ $category->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-5 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="img-fluid rounded">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                                <i class="bi bi-circle-square text-secondary" style="font-size: 5rem;"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Product Details and Size Selection Form -->
            <div class="col-md-7">
                <h1 class="fw-bold mb-3">{{ $product->name }}</h1>
                <p class="lead mb-4">{{ $product->description }}</p>
                
                <!-- Display included toppings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 fw-bold">Included Toppings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $defaultToppings = $product->defaultToppings()->get();
                            @endphp
                            @foreach($defaultToppings as $topping)
                                <div class="col-md-4 col-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>{{ $topping->name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 fw-bold">Specialty Pizza</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Select your specialty pizza size.</p>
                        
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            
                            <div class="row mb-4">
                                @php
                                    $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
                                @endphp
                                @foreach($sizes as $size => $price)
                                    <div class="col-md-6 mb-3">
                                        <div class="card size-selection-card h-100 border {{ $loop->first ? 'border-primary' : '' }}">
                                            <div class="card-body p-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="size" id="size-{{ $size }}" value="{{ $size }}" {{ $loop->first ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="size-{{ $size }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold text-capitalize">{{ ucfirst($size) }}</span>
                                                            <span class="price-display text-danger fw-bold">${{ number_format(is_array($price) ? $price['price'] : $price, 2) }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Add to Cart section -->
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary decrease-qty">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="10" readonly>
                                        <button type="button" class="btn btn-outline-secondary increase-qty">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary w-100 py-3">
                                        <i class="bi bi-cart-plus me-2"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Size selection
        const sizeCards = document.querySelectorAll('.size-selection-card');
        const sizeRadios = document.querySelectorAll('input[name="size"]');
        
        // Highlight selected size card
        sizeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                sizeCards.forEach(card => {
                    card.classList.remove('border-primary');
                });
                
                this.closest('.size-selection-card').classList.add('border-primary');
            });
        });
        
        // Quantity controls
        const decreaseQtyBtn = document.querySelector('.decrease-qty');
        const increaseQtyBtn = document.querySelector('.increase-qty');
        const quantityInput = document.querySelector('input[name="quantity"]');
        
        decreaseQtyBtn.addEventListener('click', function() {
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
        
        increaseQtyBtn.addEventListener('click', function() {
            if (parseInt(quantityInput.value) < 10) {
                quantityInput.value = parseInt(quantityInput.value) + 1;
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .size-selection-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .size-selection-card:hover {
        border-color: var(--primary-color) !important;
        transform: translateY(-3px);
    }
    
    .size-selection-card.border-primary {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .form-check-input:checked + .form-check-label {
        font-weight: bold;
    }
    
    .price-display {
        font-size: 1.25rem;
    }
</style>
@endpush 