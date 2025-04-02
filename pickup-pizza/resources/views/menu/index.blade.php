@extends('layouts.app')

@section('title', 'Menu')

@section('content')
    <!-- Menu Header -->
    <section class="py-5 bg-dark text-white">
        <div class="container py-3">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInDown">Our Menu</h1>
                    <p class="lead mb-0 animate__animated animate__fadeInUp animate__delay-1s">
                        Browse our delicious selection of pizzas and sides, available for pickup at our location
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Navigation -->
    <section class="py-4">
        <div class="container">
            <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
                @foreach($categories as $category)
                    @if(!$category->is_daily_special)
                        <a href="#{{ $category->slug }}" class="btn btn-outline-primary">
                            {{ $category->name }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- Menu Categories -->
    <section class="py-4">
        <div class="container">
            @foreach($categories as $category)
                @if(!$category->is_daily_special && $category->products->count() > 0)
                    <div id="{{ $category->slug }}" class="mb-5 pb-4 border-bottom">
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <h2 class="fw-bold mb-3">{{ $category->name }}</h2>
                                <p class="text-muted">{{ $category->description }}</p>
                                <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
                            </div>
                        </div>
                        
                        <div class="row g-4">
                            @foreach($category->products as $product)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm product-card animate__animated animate__fadeIn">
                                        @if($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}" class="card-img-top" alt="{{ $product->name }}">
                                        @endif
                                        <div class="card-body p-4">
                                            <h5 class="card-title fw-bold mb-3">{{ $product->name }}</h5>
                                            <p class="card-text text-muted mb-4">{{ $product->description }}</p>
                                            
                                            <div class="mt-auto">
                                                @if($product->has_size_options)
                                                    <div class="mb-3">
                                                        <span class="text-muted">Starting at</span>
                                                        @php
                                                            $displayPrice = 0;
                                                            if ($product->is_specialty) {
                                                                $displayPrice = $product->getDisplayPrice();
                                                            } else {
                                                                $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
                                                                if (!empty($sizes)) {
                                                                    $priceValues = [];
                                                                    foreach ($sizes as $size => $value) {
                                                                        if (is_array($value) && isset($value['price'])) {
                                                                            $priceValues[] = $value['price'];
                                                                        } elseif (is_numeric($value)) {
                                                                            $priceValues[] = $value;
                                                                        }
                                                                    }
                                                                    $displayPrice = !empty($priceValues) ? min($priceValues) : $product->price;
                                                                } else {
                                                                    $displayPrice = $product->price;
                                                                }
                                                            }
                                                        @endphp
                                                        <span class="h4 mb-0 ms-2 fw-bold text-danger">${{ number_format($displayPrice, 2) }}</span>
                                                    </div>
                                                    <a href="{{ route('menu.product', [$category->slug, $product->slug]) }}" class="btn btn-primary w-100">Order Now</a>
                                                @else
                                                    <div class="mb-3">
                                                        <span class="h4 mb-0 fw-bold text-danger">${{ number_format($product->getDisplayPrice(), 2) }}</span>
                                                    </div>
                                                    
                                                    @if($product->is_pizza || $product->has_toppings)
                                                        <a href="{{ route('menu.product', [$category->slug, $product->slug]) }}" class="btn btn-primary w-100">Customize</a>
                                                    @else
                                                        <form action="{{ route('cart.add') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                            <input type="hidden" name="quantity" value="1">
                                                            <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    <!-- Daily Specials Section -->
    @if($dailySpecial)
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="fw-bold mb-3">Today's Special Deals</h2>
                        <p class="text-muted">Don't miss out on our delicious daily specials</p>
                        <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
                    </div>
                </div>
                
                <div class="row g-4">
                    @foreach($dailySpecial->products as $product)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm special-card position-relative overflow-hidden">
                                <div class="card-badge position-absolute" 
                                     style="top: 1rem; left: -3rem; background-color: var(--primary-color); color: white; transform: rotate(-45deg); padding: 0.5rem 3rem;">
                                    <span class="fw-bold">SPECIAL</span>
                                </div>
                                <div class="card-header text-white py-3" style="background-color: var(--primary-color)">
                                    <h5 class="mb-0 fw-bold">{{ $dailySpecial->name }}</h5>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold">{{ $product->name }}</h5>
                                    <p class="card-text">{{ $product->description }}</p>
                                    
                                    @if($product->has_size_options)
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div>
                                                <span class="text-muted">Starting at</span>
                                                @php
                                                    $displayPrice = 0;
                                                    if ($product->is_specialty) {
                                                        $displayPrice = $product->getDisplayPrice();
                                                    } else {
                                                        $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
                                                        if (!empty($sizes)) {
                                                            $priceValues = [];
                                                            foreach ($sizes as $size => $value) {
                                                                if (is_array($value) && isset($value['price'])) {
                                                                    $priceValues[] = $value['price'];
                                                                } elseif (is_numeric($value)) {
                                                                    $priceValues[] = $value;
                                                                }
                                                            }
                                                            $displayPrice = !empty($priceValues) ? min($priceValues) : $product->price;
                                                        } else {
                                                            $displayPrice = $product->price;
                                                        }
                                                    }
                                                @endphp
                                                <span class="h4 mb-0 ms-2 fw-bold text-danger">${{ number_format($displayPrice, 2) }}</span>
                                            </div>
                                            <a href="{{ route('menu.product', [$product->category->slug, $product->slug]) }}" 
                                               class="btn btn-primary">Order Now</a>
                                        </div>
                                    @else
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <span class="h4 mb-0 fw-bold text-danger">${{ number_format($product->getDisplayPrice(), 2) }}</span>
                                            <a href="{{ route('menu.product', [$product->category->slug, $product->slug]) }}" 
                                               class="btn btn-primary">Order Now</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Popular Items -->
    @if(isset($popularProducts) && $popularProducts->count() > 0)
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold mb-3">Most Popular Items</h2>
                    <p class="text-muted">Customer favorites you don't want to miss</p>
                    <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
                </div>
            </div>
            
            <div class="row g-4">
                @foreach($popularProducts as $product)
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm popular-item animate__animated animate__fadeIn">
                            @if(isset($product->is_popular) && $product->is_popular)
                                <div class="position-absolute top-0 end-0 bg-primary text-white fw-bold px-3 py-2 rounded-bottom-start" 
                                     style="background-color: var(--primary-color) !important; z-index: 1;">
                                    Popular
                                </div>
                            @endif
                            
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-3">{{ $product->name }}</h5>
                                <p class="card-text text-muted mb-4">{{ Str::limit($product->description, 80) }}</p>
                                
                                <div class="mt-auto">
                                    @if($product->has_size_options)
                                        <div class="mb-3">
                                            <span class="text-muted">From</span>
                                            @php
                                                $displayPrice = 0;
                                                if ($product->is_specialty) {
                                                    $displayPrice = $product->getDisplayPrice();
                                                } else {
                                                    $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
                                                    if (!empty($sizes)) {
                                                        $priceValues = [];
                                                        foreach ($sizes as $size => $value) {
                                                            if (is_array($value) && isset($value['price'])) {
                                                                $priceValues[] = $value['price'];
                                                            } elseif (is_numeric($value)) {
                                                                $priceValues[] = $value;
                                                            }
                                                        }
                                                        $displayPrice = !empty($priceValues) ? min($priceValues) : $product->price;
                                                    } else {
                                                        $displayPrice = $product->price;
                                                    }
                                                }
                                            @endphp
                                            <span class="h4 mb-0 ms-2 fw-bold text-danger">${{ number_format($displayPrice, 2) }}</span>
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <span class="h5 mb-0 fw-bold text-danger">${{ number_format($product->getDisplayPrice(), 2) }}</span>
                                        </div>
                                    @endif
                                    
                                    <a href="{{ route('menu.product', [$product->category->slug, $product->slug]) }}" class="btn btn-primary w-100">Order Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-5 bg-dark text-white">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-3">Ready to Order Your Perfect Pizza?</h2>
                    <p class="lead mb-0">Our online ordering system makes it quick and convenient to get your favorite pizza.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('menu.category', 'specialty-pizzas') }}" class="btn btn-primary btn-lg">Order Pizza Now</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
    }
    
    .special-card {
        overflow: hidden;
    }
    
    .popular-item:hover {
        transform: translateY(-5px);
    }
</style>
@endpush 