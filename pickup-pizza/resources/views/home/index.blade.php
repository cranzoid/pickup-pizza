@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <section class="hero position-relative py-0">
        <div class="container-fluid px-0">
            <div class="position-relative">
                <div class="overlay-dark position-absolute w-100 h-100" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.5));"></div>
                <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2000&q=80" class="w-100" style="max-height: 600px; object-fit: cover;" alt="Pizza Hero Image">
                <div class="position-absolute top-50 start-50 translate-middle text-center text-white w-100" style="max-width: 800px;">
                    <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInDown">
                        Delicious Pizza For<br>
                        <span class="text-danger">Pickup Only</span>
                    </h1>
                    <p class="lead mb-4 px-4 animate__animated animate__fadeInUp animate__delay-1s">Order online and pick up fresh, hot pizza made with premium ingredients at our convenient location.</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center animate__animated animate__fadeInUp animate__delay-2s">
                        <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg">Order Now</a>
                        <a href="#daily-specials" class="btn btn-outline-light btn-lg">Today's Specials</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Order Categories -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold mb-3">What Are You Craving Today?</h2>
                    <p class="text-muted">Browse our menu categories and find your perfect pizza</p>
                    <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
                </div>
            </div>
            
            <div class="row g-4">
                @foreach($categories as $category)
                    @if(!$category->is_daily_special)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('menu.category', $category->slug) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm category-card">
                                    <div class="card-body text-center p-4">
                                        <div class="icon-wrapper mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle" 
                                             style="width: 90px; height: 90px; background-color: rgba(233, 69, 50, 0.1);">
                                            @if($category->name == 'Specialty Pizzas')
                                                <i class="bi bi-circle-square text-danger" style="font-size: 2.5rem;"></i>
                                            @elseif($category->name == 'Build Your Own Pizza')
                                                <i class="bi bi-palette text-danger" style="font-size: 2.5rem;"></i>
                                            @elseif($category->name == 'Combos')
                                                <i class="bi bi-box2-heart text-danger" style="font-size: 2.5rem;"></i>
                                            @elseif($category->name == 'Wings')
                                                <i class="bi bi-egg-fried text-danger" style="font-size: 2.5rem;"></i>
                                            @elseif($category->name == 'Sides')
                                                <i class="bi bi-layout-sidebar text-danger" style="font-size: 2.5rem;"></i>
                                            @elseif($category->name == 'Drinks')
                                                <i class="bi bi-cup-straw text-danger" style="font-size: 2.5rem;"></i>
                                            @else
                                                <i class="bi bi-grid text-danger" style="font-size: 2.5rem;"></i>
                                            @endif
                                        </div>
                                        <h5 class="card-title fw-bold mb-0">{{ $category->name }}</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- Daily Specials Section -->
    <section id="daily-specials" class="py-5">
        <div class="container py-4">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold mb-3">Today's Special Deals</h2>
                    <p class="text-muted">Don't miss out on our delicious daily specials</p>
                    <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
                </div>
            </div>
            
            <div class="row g-4">
                @if($dailySpecial)
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
                                            <a href="{{ route('menu.product', [$product->category->slug, $product->slug]) }}" 
                                               class="btn btn-primary">Order Now</a>
                                        </div>
                                    @else
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <span class="h4 mb-0 fw-bold text-danger">${{ number_format($product->price, 2) }}</span>
                                            <a href="{{ route('menu.product', [$product->category->slug, $product->slug]) }}" 
                                               class="btn btn-primary">Order Now</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center">
                        <div class="py-5">
                            <i class="bi bi-calendar2-x text-muted mb-3" style="font-size: 4rem;"></i>
                            <p class="lead">No special deals available today. Check back tomorrow!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-5 bg-light">
        <div class="container py-4">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold mb-3">Why Choose PISA Pizza?</h2>
                    <p class="text-muted">We're committed to providing the best pizza experience</p>
                    <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 100px; height: 100px; background-color: rgba(233, 69, 50, 0.1);">
                                <i class="bi bi-award text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Premium Ingredients</h5>
                            <p class="card-text text-muted">We use only the freshest and highest quality ingredients in our pizzas, ensuring every bite is delicious.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 100px; height: 100px; background-color: rgba(233, 69, 50, 0.1);">
                                <i class="bi bi-clock text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Quick Pickup</h5>
                            <p class="card-text text-muted">Order online and pick up your pizza at your convenience. No waiting in line or delivery delays.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm feature-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 100px; height: 100px; background-color: rgba(233, 69, 50, 0.1);">
                                <i class="bi bi-cash-stack text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Great Value</h5>
                            <p class="card-text text-muted">Our pizzas offer excellent value with generous toppings and competitive prices. Check out our combo deals!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5">
        <div class="container py-4">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold mb-3">How It Works</h2>
                    <p class="text-muted">Ordering from PISA Pizza is quick and easy</p>
                    <div class="mx-auto" style="width: 80px; height: 4px; background-color: var(--primary-color); border-radius: 2px;"></div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm process-card">
                        <div class="card-body text-center p-4">
                            <div class="step-number mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 60px; height: 60px; background-color: var(--primary-color); color: white;">
                                <span class="fw-bold fs-4">1</span>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Browse Menu</h5>
                            <p class="card-text text-muted">Choose from our selection of specialty pizzas or create your own.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm process-card">
                        <div class="card-body text-center p-4">
                            <div class="step-number mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 60px; height: 60px; background-color: var(--primary-color); color: white;">
                                <span class="fw-bold fs-4">2</span>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Place Order</h5>
                            <p class="card-text text-muted">Add items to your cart and check out with your details.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm process-card">
                        <div class="card-body text-center p-4">
                            <div class="step-number mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 60px; height: 60px; background-color: var(--primary-color); color: white;">
                                <span class="fw-bold fs-4">3</span>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Receive Confirmation</h5>
                            <p class="card-text text-muted">Get an email with your order details and pickup time.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm process-card">
                        <div class="card-body text-center p-4">
                            <div class="step-number mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle" 
                                 style="width: 60px; height: 60px; background-color: var(--primary-color); color: white;">
                                <span class="fw-bold fs-4">4</span>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Pickup & Enjoy</h5>
                            <p class="card-text text-muted">Visit our store at the specified time and enjoy your pizza!</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg px-5">Order Your Pizza Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-dark text-white">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-3">Ready to Order Your Perfect Pizza?</h2>
                    <p class="lead mb-0">Our online ordering system makes it quick and convenient to get your favorite pizza.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg">View Full Menu</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .category-card:hover .icon-wrapper {
        background-color: var(--primary-color) !important;
        transition: all 0.3s ease;
    }
    
    .category-card:hover .icon-wrapper i {
        color: white !important;
        transition: all 0.3s ease;
    }
    
    .special-card {
        overflow: hidden;
    }
    
    .feature-card:hover .icon-wrapper {
        background-color: var(--primary-color) !important;
        transition: all 0.3s ease;
    }
    
    .feature-card:hover .icon-wrapper i {
        color: white !important;
        transition: all 0.3s ease;
    }
    
    .process-card:hover .step-number {
        transform: scale(1.1);
        transition: all 0.3s ease;
    }
</style>
@endpush 