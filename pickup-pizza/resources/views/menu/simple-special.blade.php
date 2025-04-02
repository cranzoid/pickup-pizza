@extends('layouts.app')

@section('title', $product->name)

@section('content')
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
        
        <!-- Product Details -->
        <div class="col-md-7">
            <h1 class="fw-bold mb-3">{{ $product->name }}</h1>
            <p class="lead mb-4">{{ $product->description }}</p>
            
            <form action="{{ route('cart.add') }}" method="POST" id="product-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <!-- Special Instructions -->
                <div class="mb-4 mt-4">
                    <h5 class="fw-bold mb-3">Special Instructions</h5>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any special requests?"></textarea>
                </div>
                
                <!-- Quantity -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Quantity</h5>
                    <div class="input-group quantity-selector" style="width: 150px;">
                        <button type="button" class="btn btn-outline-secondary decrease-qty">-</button>
                        <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="10">
                        <button type="button" class="btn btn-outline-secondary increase-qty">+</button>
                    </div>
                </div>
                
                <!-- Price and Add to Cart -->
                <div class="d-flex align-items-center justify-content-between mt-4">
                    <div>
                        <span class="fs-4 fw-bold">Price: ${{ number_format($product->price, 2) }}</span>
                    </div>
                    <button type="submit" class="btn btn-danger btn-lg">
                        Add to Cart
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity selector functionality
    const decreaseBtn = document.querySelector('.decrease-qty');
    const increaseBtn = document.querySelector('.increase-qty');
    const qtyInput = document.querySelector('input[name="quantity"]');
    
    decreaseBtn.addEventListener('click', function() {
        let currentValue = parseInt(qtyInput.value);
        if (currentValue > 1) {
            qtyInput.value = currentValue - 1;
        }
    });
    
    increaseBtn.addEventListener('click', function() {
        let currentValue = parseInt(qtyInput.value);
        if (currentValue < 10) {
            qtyInput.value = currentValue + 1;
        }
    });
});
</script>
@endpush 