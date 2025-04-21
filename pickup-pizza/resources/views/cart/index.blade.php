@extends('layouts.app')

@section('title', 'Cart')

@section('content')
    <!-- Cart Header -->
    <section class="bg-dark text-white py-4">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-2 animate__animated animate__fadeIn">Your Cart</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}" class="text-white-50">Menu</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Cart</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    
    <div class="container py-5">
        @if(!empty($cartItems) && count($cartItems) > 0)
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <!-- Upsell Suggestion Card -->
                    @if(session()->has('upsell'))
                        <div class="card border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div class="d-flex align-items-center">
                                        @php
                                            $upsell = session('upsell');
                                            $hasImage = !empty($upsell['product']['image_path'] ?? null);
                                        @endphp
                                        @if($hasImage)
                                            <img src="{{ asset('storage/'.$upsell['product']['image_path']) }}" alt="{{ $upsell['product']['name'] }}" class="me-3 rounded" width="70">
                                        @else
                                            <div class="bg-light rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background-color: rgba(233, 69, 50, 0.1) !important;">
                                                <i class="bi bi-plus-circle-fill" style="font-size: 2rem; color: var(--primary-color);"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="mb-1 text-muted">Would you like to add:</p>
                                            <h5 class="mb-1 fw-bold">{{ $upsell['product']['name'] }}</h5>
                                            <p class="mb-0 fw-bold text-danger">${{ number_format($upsell['price'], 2) }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('cart.add-upsell') }}" method="POST" class="mt-3 mt-md-0">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $upsell['product']['id'] }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i> Add to Order
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="card border-0 shadow-sm animate__animated animate__fadeIn">
                        <div class="card-header bg-transparent py-3 border-bottom">
                            <h4 class="mb-0 fw-bold">Order Items ({{ count($cartItems) }})</h4>
                        </div>
                        <div class="card-body p-0">
                            @foreach($cartItems as $index => $item)
                                <div class="cart-item p-4 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="d-flex align-items-center">
                                                @if(!empty($item['image'] ?? null))
                                                    <img src="{{ asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}" class="me-3 rounded" width="60" height="60" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(233, 69, 50, 0.1);">
                                                        <i class="bi bi-circle-square" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h5 class="mb-1 fw-bold">{{ $item['name'] }}</h5>
                                                    @if(!empty($item['size'] ?? null))
                                                        <span class="badge bg-light text-dark mb-1 text-capitalize">Size: {{ $item['size'] }}</span>
                                                    @endif
                                                    
                                                    @if(!empty($item['options']['is_two_for_one'] ?? false))
                                                        <span class="badge bg-danger mb-1">2-for-1 Special</span>
                                                    @endif
                                                    
                                                    @php
                                                        $hasToppings = !empty($item['options']['toppings'] ?? []) && count($item['options']['toppings'] ?? []) > 0;
                                                    @endphp
                                                    @if($hasToppings)
                                                        <div>
                                                            <small class="text-muted">
                                                                <span class="fw-bold">Toppings:</span> 
                                                                {{ implode(', ', $item['options']['toppings']) }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Display extra toppings for specialty pizzas if any -->
                                                    @php
                                                        $hasExtraToppings = !empty($item['options']['extra_toppings'] ?? []) && count($item['options']['extra_toppings'] ?? []) > 0;
                                                    @endphp
                                                    @if($hasExtraToppings)
                                                        <div>
                                                            <small class="text-muted">
                                                                <span class="fw-bold">Extra Toppings:</span> 
                                                                {{ implode(', ', $item['options']['extra_toppings']) }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Display extras if any -->
                                                    @php
                                                        $hasExtras = !empty($item['options']['extras'] ?? []) && count($item['options']['extras'] ?? []) > 0;
                                                    @endphp
                                                    @if($hasExtras)
                                                    <div class="extras mt-1">
                                                        <small class="text-muted fw-bold">Extras:</small>
                                                        <ul class="list-unstyled small ps-3 mb-0">
                                                            @foreach($item['options']['extras'] as $extra)
                                                                <li>
                                                                    <span>{{ $extra['name'] }}</span>
                                                                    @if($extra['quantity'] > 1)
                                                                        <span class="text-muted"> (x{{ $extra['quantity'] }})</span>
                                                                    @endif
                                                                    <span class="text-muted"> +${{ number_format($extra['price'] * $extra['quantity'], 2) }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    @endif
                                                    
                                                    @if(!empty($item['notes'] ?? null))
                                                        <div class="mt-1">
                                                            <small class="text-muted fst-italic">
                                                                "{{ $item['notes'] }}"
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3 mb-md-0 text-md-center">
                                            <div class="d-flex d-md-block">
                                                <span class="text-muted me-2 d-md-none">Price:</span>
                                                <span class="fw-bold">${{ number_format($item['unit_price'], 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-3 mb-md-0">
                                            <div class="d-flex justify-content-start justify-content-md-center align-items-center">
                                                <span class="text-muted me-2 d-md-none">Qty:</span>
                                                <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center">
                                                    @csrf
                                                    <input type="hidden" name="index" value="{{ $index }}">
                                                    <button type="submit" name="update_action" value="decrease" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 30px; height: 30px; padding: 0;">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <span class="mx-2 fw-bold">{{ $item['quantity'] }}</span>
                                                    <button type="submit" name="update_action" value="increase" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 30px; height: 30px; padding: 0;">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-muted me-2 d-md-none">Total:</span>
                                                <span class="fw-bold text-danger">${{ number_format($item['quantity'] * $item['unit_price'], 2) }}</span>
                                            </div>
                                            <form action="{{ route('cart.remove') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="index" value="{{ $index }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="p-4">
                                <div class="d-flex flex-wrap justify-content-between">
                                    <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary mb-2 mb-md-0">
                                        <i class="bi bi-arrow-left me-2"></i> Continue Shopping
                                    </a>
                                    
                                    <form action="{{ route('cart.clear') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-cart-x me-2"></i> Clear Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4 sticky-top animate__animated animate__fadeIn" style="top: 20px; z-index: 100;">
                        <div class="card-header py-3" style="background-color: var(--primary-color);">
                            <h5 class="mb-0 fw-bold text-white">Order Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal</span>
                                <span class="fw-bold">${{ number_format($cartTotal, 2) }}</span>
                            </div>
                            
                            @if($settings->get('tax_enabled', true))
                                <div class="d-flex justify-content-between mb-3">
                                    <span>{{ $settings->get('tax_name', 'Tax') }} ({{ $settings->get('tax_rate', 13) }}%)</span>
                                    <span class="fw-bold">${{ number_format($taxAmount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if(session()->has('discount'))
                                <div class="d-flex justify-content-between mb-3 text-success">
                                    <span>Discount ({{ session('discount.code') }})</span>
                                    <span class="fw-bold">-${{ number_format(session('discount.amount'), 2) }}</span>
                                </div>
                            @endif
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 mb-0">Total</span>
                                <span class="h5 mb-0 text-danger fw-bold">${{ number_format($orderTotal, 2) }}</span>
                            </div>
                            
                            @if(!session()->has('discount') && $settings->get('discount_enabled', true))
                                <div class="mb-4">
                                    <form action="{{ route('cart.apply-discount') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="discount_code" class="form-control" placeholder="Discount code">
                                            <button type="submit" class="btn btn-secondary">Apply</button>
                                        </div>
                                        @error('discount_code')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </form>
                                </div>
                            @endif
                            
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-credit-card me-2"></i> Proceed to Checkout
                            </a>
                            
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <i class="bi bi-shield-lock me-1"></i> Secure checkout. Your information is protected.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Support Card -->
                    <div class="card border-0 shadow-sm animate__animated animate__fadeIn animate__delay-1s">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(233, 69, 50, 0.1);">
                                    <i class="bi bi-headset" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                </div>
                                <h5 class="fw-bold mb-0">Need Help?</h5>
                            </div>
                            <p class="text-muted mb-0">Our customer support team is here to help you with your order.</p>
                            <div class="mt-3">
                                <a href="tel:{{ $settings->get('support_phone', '905-547-5777') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-telephone me-2"></i> {{ $settings->get('support_phone', '905-547-5777') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-md-8 text-center py-5 animate__animated animate__fadeIn">
                    <div class="mb-4">
                        <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; background-color: rgba(233, 69, 50, 0.1);">
                            <i class="bi bi-cart-x" style="font-size: 3rem; color: var(--primary-color);"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-3">Your Cart is Empty</h2>
                    <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                    <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-grid me-2"></i> Browse Menu
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- You Might Also Like Section -->
    @if(!empty($cartItems) && count($cartItems) > 0 && isset($relatedProducts) && !empty($relatedProducts) && count($relatedProducts) > 0)
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="fw-bold mb-3">You Might Also Like</h2>
                        <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
                    </div>
                </div>
                
                <div class="row g-4">
                    @foreach($relatedProducts->take(4) as $product)
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm animate__animated animate__fadeIn related-product">
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-3">{{ $product->name }}</h5>
                                    <p class="card-text text-muted mb-4">{{ Str::limit($product->description, 80) }}</p>
                                    
                                    <div class="mt-auto">
                                        @if($product->has_size_options)
                                            <div class="mb-3">
                                                <span class="text-muted">From</span>
                                                <span class="h5 mb-0 ms-2 fw-bold text-danger">${{ number_format(is_array($product->sizes) ? 
                                                    (!empty($product->sizes) ? 
                                                      (($prices = array_column($product->sizes, 'price')) && !empty($prices) ? min($prices) : $product->price) 
                                                      : $product->price) 
                                                    : (!empty(json_decode($product->sizes, true)) ? 
                                                        (($prices = array_column(json_decode($product->sizes, true), 'price')) && !empty($prices) ? min($prices) : $product->price) 
                                                        : $product->price), 
                                                    2) }}</span>
                                            </div>
                                        @else
                                            <div class="mb-3">
                                                <span class="h5 mb-0 fw-bold text-danger">${{ number_format($product->price, 2) }}</span>
                                            </div>
                                        @endif
                                        
                                        <a href="{{ route('menu.product', [$product->category->slug, $product->slug]) }}" class="btn btn-primary w-100">Add to Cart</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('styles')
<style>
    .cart-item {
        transition: all 0.3s ease;
    }
    
    .cart-item:hover {
        background-color: rgba(0,0,0,0.01);
    }
    
    .related-product {
        transition: all 0.3s ease;
    }
    
    .related-product:hover {
        transform: translateY(-5px);
    }
    
    @media (min-width: 992px) {
        .sticky-top {
            position: sticky;
            top: 100px;
        }
    }
</style>
@endpush 