@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="fw-bold mb-3">{{ $category->name }}</h1>
                <p class="lead">{{ $category->description }}</p>
                <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
            </div>
        </div>
        
        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm product-card animate__animated animate__fadeIn">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                            @endif
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-3">{{ $product->name }}</h5>
                                <p class="card-text text-muted mb-4">{{ $product->description }}</p>
                                
                                <div class="mt-auto">
                                    @if($product->has_size_options)
                                        <div class="mb-3">
                                            <span class="text-muted">Starting at</span>
                                            <span class="h4 mb-0 ms-2 fw-bold text-danger">${{ number_format(is_array($product->sizes) ? 
                                                (!empty($product->sizes) ? 
                                                  (($prices = array_column($product->sizes, 'price')) && !empty($prices) ? min($prices) : $product->price) 
                                                  : $product->price) 
                                                : (!empty(json_decode($product->sizes, true)) ? 
                                                    (($prices = array_column(json_decode($product->sizes, true), 'price')) && !empty($prices) ? min($prices) : $product->price) 
                                                    : $product->price), 
                                                2) }}</span>
                                        </div>
                                        <a href="{{ route('menu.product', [$category->slug, $product->slug]) }}" class="btn btn-primary w-100">Order Now</a>
                                    @else
                                        <div class="mb-3">
                                            <span class="h4 mb-0 fw-bold text-danger">${{ number_format(
                                                $product->is_specialty 
                                                ? (is_array($product->sizes) 
                                                    ? (isset($product->sizes['medium']) ? $product->sizes['medium'] : reset($product->sizes))
                                                    : (!empty(json_decode($product->sizes, true)) 
                                                        ? (($prices = array_column(json_decode($product->sizes, true), 'price')) && !empty($prices) ? min($prices) : $product->price)
                                                        : $product->price)) 
                                                : $product->price
                                            , 2) }}</span>
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
        @else
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info p-5 rounded-3 shadow-sm animate__animated animate__fadeIn">
                        <i class="bi bi-exclamation-circle mb-3" style="font-size: 3rem;"></i>
                        <h4 class="alert-heading fw-bold">No Products Available</h4>
                        <p>There are currently no products in this category. Please check back later or browse other categories.</p>
                        <hr>
                        <a href="{{ route('menu.index') }}" class="btn btn-primary mt-2">Browse Menu</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Related Categories CTA -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-4">
                <div class="col-12">
                    <h2 class="fw-bold mb-3">Explore More Categories</h2>
                    <p class="text-muted">Find more delicious options on our menu</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        @foreach($categories as $cat)
                            @if($cat->id != $category->id)
                                <a href="{{ route('menu.category', $cat->slug) }}" class="btn btn-outline-secondary m-1">{{ $cat->name }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .product-card .card-body {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    
    .product-card:hover {
        transform: translateY(-10px);
    }
</style>
@endpush 