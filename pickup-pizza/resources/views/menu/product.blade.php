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
            
            <!-- Product Details and Customization Form -->
            <div class="col-md-7">
                <h1 class="fw-bold mb-3">{{ $product->name }}</h1>
                <p class="lead mb-4">{{ $product->description }}</p>
                
                <form action="{{ route('cart.add') }}" method="POST" id="product-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <!-- Size Selection (if has size options) -->
                    @if($product->has_size_options)
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">Choose Size</h5>
                            <div class="row g-2">
                                @foreach($sizes as $size => $sizeData)
                                    @php
                                        $price = is_array($sizeData) && isset($sizeData['price']) ? $sizeData['price'] : $sizeData;
                                    @endphp
                                    <div class="col-6 col-md-3">
                                        <div class="form-check size-option">
                                            <input class="form-check-input" type="radio" name="size" id="size-{{ $size }}" 
                                                value="{{ $size }}" data-price="{{ $price }}" {{ $loop->first ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="size-{{ $size }}">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body text-center">
                                                        <span class="d-block fw-bold text-capitalize">{{ $size }}</span>
                                                        <span class="d-block price-display">${{ number_format($price, 2) }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                        @if($product->is_pizza)
                            @if(strpos($product->name, 'Ultimate Pizza & Wings Combo') !== false)
                                <!-- Ultimate Pizza & Wings Combo specific display -->
                                <div class="mb-4">
                                    <h4 class="fw-bold">Pizza Toppings</h4>
                                    <p class="mb-2">Select your toppings (Limit: {{ $product->max_toppings }})</p>
                                    <p class="mb-3"><span id="topping-count">0</span> toppings selected</p>
                                    
                                    <div class="d-none text-danger mb-3" id="extra-toppings-message">
                                        You have selected <span id="extra-toppings-count">0</span> extra toppings which will be charged additionally.
                                    </div>
                                    
                                    <div class="row g-2 toppings-container">
                                        @php
                                            $meatToppings = $toppings->where('category', 'meat');
                                            $veggieToppings = $toppings->where('category', 'veggie');
                                            $cheeseToppings = $toppings->where('category', 'cheese');
                                        @endphp
                                        
                                        @if($meatToppings->count() > 0)
                                            <div class="col-12 mb-2">
                                                <h6 class="fw-bold text-danger">Meats</h6>
                                            </div>
                                            @foreach($meatToppings as $topping)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input topping-checkbox" type="checkbox" name="toppings[]" 
                                                            id="topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                            data-counts-as="{{ $topping->counts_as }}"
                                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                                        <label class="form-check-label" for="topping-{{ $topping->id }}">
                                                            {{ $topping->name }}
                                                            @if($topping->counts_as > 1)
                                                                <span class="badge bg-danger">{{ $topping->counts_as }}x</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($veggieToppings->count() > 0)
                                            <div class="col-12 mb-2 mt-3">
                                                <h6 class="fw-bold text-success">Veggies</h6>
                                            </div>
                                            @foreach($veggieToppings as $topping)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input topping-checkbox" type="checkbox" name="toppings[]" 
                                                            id="topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                            data-counts-as="{{ $topping->counts_as }}"
                                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                                        <label class="form-check-label" for="topping-{{ $topping->id }}">
                                                            {{ $topping->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($cheeseToppings->count() > 0)
                                            <div class="col-12 mb-2 mt-3">
                                                <h6 class="fw-bold text-warning">Cheeses</h6>
                                            </div>
                                            @foreach($cheeseToppings as $topping)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input topping-checkbox" type="checkbox" name="toppings[]" 
                                                            id="topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                            data-counts-as="{{ $topping->counts_as }}"
                                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                                        <label class="form-check-label" for="topping-{{ $topping->id }}">
                                                            {{ $topping->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($product->max_toppings)
                                            <div class="col-12 mt-3">
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                    <span>Selected: <span id="topping-count">0</span> of {{ $product->max_toppings }} toppings</span>
                                                    <span id="extra-toppings-message" class="d-none">
                                                        (<span id="extra-toppings-count">0</span> extra topping(s) will be charged at $<span id="extra-topping-price">
                                                            @php
                                                                $addOns = json_decode($product->add_ons ?? '{}', true);
                                                                $extraToppingPrice = $addOns['extra_topping_price'] ?? 0;
                                                                echo number_format($extraToppingPrice, 2);
                                                            @endphp
                                                        </span> each)
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Show included wings for Ultimate Pizza & Wings Combo -->
                                <div class="mb-4 mt-5">
                                    <h5 class="fw-bold mb-3">Included With Your Combo</h5>
                                    <div class="row g-2">
                                        <!-- Pizza with toppings -->
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card border-0 shadow-sm mb-2">
                                                <div class="card-body py-2 px-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                        <span>1 Pizza with Cheese + 3 Toppings</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Wings quantity based on size -->
                                        <div class="col-md-4 col-sm-6" id="wings-quantity-card">
                                            <div class="card border-0 shadow-sm mb-2">
                                                <div class="card-body py-2 px-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                        <span id="wings-quantity-text">
                                                            @if($firstSize === 'medium')
                                                                12 Wings
                                                            @else
                                                                3 lbs Wings
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Veggie Sticks -->
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card border-0 shadow-sm mb-2">
                                                <div class="card-body py-2 px-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                        <span>Veggie Sticks</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Blue Cheese -->
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card border-0 shadow-sm mb-2">
                                                <div class="card-body py-2 px-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                        <span>Blue Cheese</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Free Dipping Sauce -->
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card border-0 shadow-sm mb-2">
                                                <div class="card-body py-2 px-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                        <span>Free Dipping Sauce</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Add-ons for Ultimate Pizza & Wings Combo -->
                                @if(isset($product->add_ons) && !empty($product->add_ons))
                                    @php
                                        $addOns = is_array($product->add_ons) ? $product->add_ons : json_decode($product->add_ons, true);
                                    @endphp
                                    
                                    <div class="mb-4">
                                        <h5 class="fw-bold mb-3">Add Extras to Your Combo</h5>
                                        <div class="row g-3">
                                            @foreach($addOns as $key => $addOn)
                                                <div class="col-md-6">
                                                    <div class="card border-0 shadow-sm h-100">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <h6 class="card-title fw-bold">
                                                                    @if($key == 'pop')
                                                                        4 Pops
                                                                    @elseif($key == 'extra_wings')
                                                                        Additional Wings
                                                                    @else
                                                                        {{ $addOn['name'] }}
                                                                    @endif
                                                                </h6>
                                                                <span class="text-danger fw-bold">
                                                                    @if($key == 'pop')
                                                                        $4.99
                                                                    @elseif($key == 'extra_wings')
                                                                        $10.49/lb
                                                                    @else
                                                                        ${{ number_format($addOn['price'], 2) }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <p class="card-text small text-muted mb-3">
                                                                @if($key == 'pop')
                                                                    Add 4 cans of pop to your order
                                                                @elseif($key == 'extra_wings')
                                                                    Add more wings to your combo for $10.49/lb
                                                                @elseif(isset($addOn['description']))
                                                                    {{ $addOn['description'] }}
                                                                @endif
                                                            </p>
                                                            
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="form-check">
                                                                    <input class="form-check-input addon-checkbox" 
                                                                        type="checkbox" 
                                                                        id="addon-{{ $key }}" 
                                                                        name="addons[{{ $key }}]"
                                                                        data-price="{{ $key == 'pop' ? 4.99 : ($key == 'extra_wings' ? 10.49 : $addOn['price']) }}">
                                                                    <label class="form-check-label" for="addon-{{ $key }}">
                                                                        Add to order
                                                                    </label>
                                                                </div>
                                                                
                                                                <div class="input-group quantity-selector addon-quantity-selector" style="width: 110px;">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary decrease-addon-qty" disabled>-</button>
                                                                    <input type="number" 
                                                                        class="form-control form-control-sm text-center addon-quantity"
                                                                        name="addon_quantity[{{ $key }}]"
                                                                        value="0" 
                                                                        min="0" 
                                                                        max="5"
                                                                        readonly>
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary increase-addon-qty" disabled>+</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @elseif(strpos($product->name, 'Two Medium Pizzas Combo') !== false || strpos($product->name, 'Two Large Pizzas Combo') !== false || strpos($product->name, 'Two XL Pizzas Combo') !== false)
                                <!-- Two-Pizza Combo specific display -->
                                @include('menu.two-pizza-combos')
                            @else
                                <!-- Regular Pizza Toppings -->
                                <div class="mb-4">
                                    <h5 class="fw-bold mb-3">
                                        @if($product->is_specialty)
                                            Included Toppings
                                        @else
                                            Choose Toppings
                                            @if($product->max_toppings)
                                                <small class="text-muted">(Max {{ $product->max_toppings }} toppings)</small>
                                            @endif
                                        @endif
                                    </h5>
                                    
                                    <!-- Selectable toppings for custom pizza -->
                                    <div class="row g-2 toppings-container">
                                        @php
                                            $meatToppings = $toppings->where('category', 'meat');
                                            $veggieToppings = $toppings->where('category', 'veggie');
                                            $cheeseToppings = $toppings->where('category', 'cheese');
                                        @endphp
                                        
                                        @if($meatToppings->count() > 0)
                                            <div class="col-12 mb-2">
                                                <h6 class="fw-bold text-danger">Meats</h6>
                                            </div>
                                            @foreach($meatToppings as $topping)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input topping-checkbox" type="checkbox" name="toppings[]" 
                                                            id="topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                            data-counts-as="{{ $topping->counts_as }}"
                                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                                        <label class="form-check-label" for="topping-{{ $topping->id }}">
                                                            {{ $topping->name }}
                                                            @if($topping->counts_as > 1)
                                                                <span class="badge bg-danger">{{ $topping->counts_as }}x</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($veggieToppings->count() > 0)
                                            <div class="col-12 mb-2 mt-3">
                                                <h6 class="fw-bold text-success">Veggies</h6>
                                            </div>
                                            @foreach($veggieToppings as $topping)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input topping-checkbox" type="checkbox" name="toppings[]" 
                                                            id="topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                            data-counts-as="{{ $topping->counts_as }}"
                                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                                        <label class="form-check-label" for="topping-{{ $topping->id }}">
                                                            {{ $topping->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($cheeseToppings->count() > 0)
                                            <div class="col-12 mb-2 mt-3">
                                                <h6 class="fw-bold text-warning">Cheeses</h6>
                                            </div>
                                            @foreach($cheeseToppings as $topping)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input topping-checkbox" type="checkbox" name="toppings[]" 
                                                            id="topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                            data-counts-as="{{ $topping->counts_as }}"
                                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                                        <label class="form-check-label" for="topping-{{ $topping->id }}">
                                                            {{ $topping->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if($product->max_toppings)
                                            <div class="col-12 mt-3">
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                    <span>Selected: <span id="topping-count">0</span> of {{ $product->max_toppings }} toppings</span>
                                                    <span id="extra-toppings-message" class="d-none">
                                                        (<span id="extra-toppings-count">0</span> extra topping(s) will be charged at $<span id="extra-topping-price">
                                                            @php
                                                                $addOns = json_decode($product->add_ons ?? '{}', true);
                                                                $extraToppingPrice = $addOns['extra_topping_price'] ?? 0;
                                                                echo number_format($extraToppingPrice, 2);
                                                            @endphp
                                                        </span> each)
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Extra Toppings Option - Only show if show_extra_toppings_toggle is enabled -->
                                @php
                                    $addOns = json_decode($product->add_ons ?? '{}', true);
                                    $showExtraToppingsToggle = $addOns['show_extra_toppings_toggle'] ?? false;
                                @endphp
                                
                                @if($showExtraToppingsToggle)
                                <div class="mt-4 mb-4">
                                    <h5 class="fw-bold mb-3">Add Extra Toppings?</h5>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="add_extra_toppings" id="add-extra-toppings-no" value="no" checked>
                                        <label class="form-check-label" for="add-extra-toppings-no">No</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="add_extra_toppings" id="add-extra-toppings-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                                        <label class="form-check-label" for="add-extra-toppings-yes" style="color: #dc3545; font-weight: bold;">Yes</label>
                                    </div>
                                    
                                    <!-- Extra toppings selection (initially hidden) -->
                                    <div id="extra-toppings-container" class="mt-3 d-none">
                                        <div class="row g-2">
                                            @if($meatToppings->count() > 0)
                                                <div class="col-12 mb-2">
                                                    <h6 class="fw-bold text-danger">Meats</h6>
                                                </div>
                                                @foreach($meatToppings as $topping)
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input extra-topping-checkbox" type="checkbox" name="extra_toppings[]" 
                                                                id="extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                                data-counts-as="{{ $topping->counts_as }}"
                                                                data-size="{{ isset($firstSize) ? $firstSize : '' }}">
                                                            <label class="form-check-label" for="extra-topping-{{ $topping->id }}">
                                                                {{ $topping->name }}
                                                                @if($topping->counts_as > 1)
                                                                    <span class="badge bg-danger">{{ $topping->counts_as }}x</span>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            
                                            @if($veggieToppings->count() > 0)
                                                <div class="col-12 mb-2 mt-3">
                                                    <h6 class="fw-bold text-success">Veggies</h6>
                                                </div>
                                                @foreach($veggieToppings as $topping)
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input extra-topping-checkbox" type="checkbox" name="extra_toppings[]" 
                                                                id="extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                                data-counts-as="{{ $topping->counts_as }}"
                                                                data-size="{{ isset($firstSize) ? $firstSize : '' }}">
                                                            <label class="form-check-label" for="extra-topping-{{ $topping->id }}">
                                                                {{ $topping->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            
                                            @if($cheeseToppings->count() > 0)
                                                <div class="col-12 mb-2 mt-3">
                                                    <h6 class="fw-bold text-warning">Cheeses</h6>
                                                </div>
                                                @foreach($cheeseToppings as $topping)
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input extra-topping-checkbox" type="checkbox" name="extra_toppings[]" 
                                                                id="extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                                data-counts-as="{{ $topping->counts_as }}"
                                                                data-size="{{ isset($firstSize) ? $firstSize : '' }}">
                                                            <label class="form-check-label" for="extra-topping-{{ $topping->id }}">
                                                                {{ $topping->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            
                                            <div class="col-12 mt-3">
                                                <div class="alert alert-danger">
                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                    <span>Extra toppings: <span id="extra-topping-count">0</span> selected ($<span id="extra-topping-price-display">
                                                        @php
                                                            $addOns = json_decode($product->add_ons ?? '{}', true);
                                                            $extraToppingPrice = 0;
                                                            if (isset($addOns['extra_topping_price'])) {
                                                                if (is_array($addOns['extra_topping_price']) && isset($addOns['extra_topping_price'][$firstSize])) {
                                                                    $extraToppingPrice = $addOns['extra_topping_price'][$firstSize];
                                                                } elseif (!is_array($addOns['extra_topping_price'])) {
                                                                    $extraToppingPrice = $addOns['extra_topping_price'];
                                                                }
                                                            }
                                                            echo number_format($extraToppingPrice, 2);
                                                        @endphp
                                                    </span> each)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endif
                        @endif
                    </div>
                    
                    <!-- Product Extras Selection -->
                    @if($product->has_extras && $product->extras->count() > 0)
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">Add Extras</h5>
                            <div class="row g-3 extras-container">
                                @foreach($product->extras as $extra)
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <h6 class="card-title fw-bold">{{ $extra->name }}</h6>
                                                    <span class="text-danger fw-bold">${{ number_format($extra->price, 2) }}</span>
                                                </div>
                                                @if($extra->description)
                                                    <p class="card-text small text-muted mb-3">{{ $extra->description }}</p>
                                                @endif
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input extra-checkbox" 
                                                            type="checkbox" 
                                                            id="extra-{{ $extra->id }}" 
                                                            data-price="{{ $extra->price }}"
                                                            data-max-quantity="{{ $extra->max_quantity }}"
                                                            data-extra-id="{{ $extra->id }}"
                                                            {{ $extra->is_default ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="extra-{{ $extra->id }}">
                                                            Add to order
                                                        </label>
                                                    </div>
                                                    
                                                    <div class="input-group quantity-selector extra-quantity-selector" style="width: 110px;">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary decrease-extra-qty" {{ $extra->is_default ? '' : 'disabled' }}>-</button>
                                                        <input type="number" 
                                                            class="form-control form-control-sm text-center extra-quantity"
                                                            value="{{ $extra->is_default ? 1 : 0 }}" 
                                                            min="0" 
                                                            max="{{ $extra->max_quantity }}"
                                                            readonly>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary increase-extra-qty" {{ $extra->is_default ? '' : 'disabled' }}>+</button>
                                                        
                                                        <!-- Hidden input to send with form -->
                                                        <input type="hidden" 
                                                            name="extras[{{ $loop->index }}][id]" 
                                                            value="{{ $extra->id }}">
                                                        <input type="hidden" 
                                                            name="extras[{{ $loop->index }}][quantity]" 
                                                            class="extra-quantity-input"
                                                            value="{{ $extra->is_default ? 1 : 0 }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Special Instructions -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Special Instructions</h5>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Any special requests? (e.g., extra sauce, well done, etc.)"></textarea>
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
                            <span class="fs-4 fw-bold">Total: $<span id="total-price">
                                @if($product->has_size_options)
                                    @php
                                        $sizeValue = $sizes[$firstSize];
                                        $priceValue = is_array($sizeValue) && isset($sizeValue['price']) ? $sizeValue['price'] : $sizeValue;
                                    @endphp
                                    {{ number_format($priceValue, 2) }}
                                @else
                                    {{ number_format($product->price, 2) }}
                                @endif
                            </span></span>
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg">
                            Add to Cart
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pre-select default toppings for specialty pizzas
        @if($product->is_specialty && $product->defaultToppings)
            // Get all the default toppings
            const defaultToppingIds = [
                @foreach($product->defaultToppings as $topping)
                    {{ $topping->id }},
                @endforeach
            ];
            
            // Check all the checkboxes that match default toppings
            document.querySelectorAll('.topping-checkbox').forEach(checkbox => {
                if (defaultToppingIds.includes(parseInt(checkbox.value))) {
                    checkbox.checked = true;
                    checkbox.disabled = true; // Disable changes for specialty pizza toppings
                }
            });
        @endif
        
        // Size selection
        const sizeRadios = document.querySelectorAll('input[name="size"]');
        const totalPriceElement = document.getElementById('total-price');
        const quantityInput = document.querySelector('input[name="quantity"]');
        const decreaseQtyBtn = document.querySelector('.decrease-qty');
        const increaseQtyBtn = document.querySelector('.increase-qty');
        
        // Initialize variables for pricing
        @if($product->is_specialty)
        let basePrice = {{ isset($sizes[$firstSize]) ? $sizes[$firstSize] : $product->getDisplayPrice() }};
        @else
        let basePrice = {{ isset($sizes[$firstSize]) ? $sizes[$firstSize] : $product->price }};
        @endif
        let toppingPrice = 0;
        let extrasPrice = 0;
        let specialtyExtraToppingPrice = 0;
        let addonPrice = 0;
        let secondPizzaPrice = 0;
        let secondPizzaToppingPrice = 0;
        let extraToppingPrice = 0;
        let quantity = 1;
        
        // Extra toppings functionality
        const addToppingsRadios = document.querySelectorAll('input[name="add_extra_toppings"]');
        const extraToppingsContainer = document.getElementById('extra-toppings-container');
        const extraToppingCheckboxes = document.querySelectorAll('.extra-topping-checkbox');
        const extraToppingCountElement = document.getElementById('extra-topping-count');
        
        // Extra toppings variables
        let extraToppingCount = 0;
        
        // Configure extra toppings toggle
        if (addToppingsRadios.length > 0) {
            addToppingsRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'yes') {
                        extraToppingsContainer.classList.remove('d-none');
                    } else {
                        extraToppingsContainer.classList.add('d-none');
                        // Uncheck all extra toppings when toggling off
                        extraToppingCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        extraToppingCount = 0;
                        extraToppingPrice = 0;
                        updateExtraToppingCount();
                        updateTotalPrice();
                    }
                });
            });
        }
        
        // Handle extra topping selection
        if (extraToppingCheckboxes.length > 0) {
            // Set the initial price based on size
            const updateExtraToppingPriceBySize = function(size) {
                @php
                $addOns = json_decode($product->add_ons ?? '{}', true);
                $extraToppingPriceValue = $addOns['extra_topping_price'] ?? 0;
                @endphp
                
                let toppingPriceBySize = {
                    @if(is_array($extraToppingPriceValue))
                        @foreach($extraToppingPriceValue as $size => $price)
                            '{{ $size }}': {{ $price }},
                        @endforeach
                    @else
                        'medium': {{ is_numeric($extraToppingPriceValue) ? $extraToppingPriceValue : 1.60 }},
                        'large': 2.10,
                        'xl': 2.30,
                        'jumbo': 2.90,
                        'slab': 2.90
                    @endif
                };
                
                return toppingPriceBySize[size] || toppingPriceBySize['medium'] || 1.60;
            };
            
            // Update price display whenever size changes
            if (sizeRadios.length > 0) {
                sizeRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        // Update data-size attribute on all extra topping checkboxes
                        extraToppingCheckboxes.forEach(checkbox => {
                            checkbox.setAttribute('data-size', this.value);
                        });
                        
                        // Recalculate extra topping prices based on new size
                        extraToppingPrice = 0;
                        extraToppingCheckboxes.forEach(checkbox => {
                            if (checkbox.checked) {
                                const size = checkbox.getAttribute('data-size') || this.value;
                                const countsAs = parseInt(checkbox.getAttribute('data-counts-as')) || 1;
                                const toppingPrice = updateExtraToppingPriceBySize(size);
                                extraToppingPrice += toppingPrice * countsAs;
                            }
                        });
                        
                        // Update price display
                        const extraToppingPriceDisplay = document.getElementById('extra-topping-price-display');
                        if (extraToppingPriceDisplay) {
                            extraToppingPriceDisplay.textContent = updateExtraToppingPriceBySize(this.value).toFixed(2);
                        }
                        
                        updateTotalPrice();
                    });
                });
            }
            
            extraToppingCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Get the size to determine pricing
                    let size = this.getAttribute('data-size') || '';
                    if (!size && sizeRadios.length > 0) {
                        for (let i = 0; i < sizeRadios.length; i++) {
                            if (sizeRadios[i].checked) {
                                size = sizeRadios[i].value;
                                break;
                            }
                        }
                    }
                    
                    // Get price based on size
                    const toppingPrice = updateExtraToppingPriceBySize(size);
                    let countsAs = parseInt(this.getAttribute('data-counts-as')) || 1;
                    
                    if (this.checked) {
                        extraToppingCount += countsAs;
                        extraToppingPrice += toppingPrice * countsAs;
                    } else {
                        extraToppingCount -= countsAs;
                        extraToppingPrice -= toppingPrice * countsAs;
                    }
                    
                    updateExtraToppingCount();
                    updateTotalPrice();
                });
            });
        }
        
        // Update extra topping count display
        function updateExtraToppingCount() {
            if (extraToppingCountElement) {
                extraToppingCountElement.textContent = extraToppingCount;
            }
        }
        
        // Calculate and update total price
        function updateTotalPrice() {
            const total = (basePrice + toppingPrice + extrasPrice + specialtyExtraToppingPrice + addonPrice + secondPizzaPrice + secondPizzaToppingPrice + extraToppingPrice) * quantity;
            totalPriceElement.textContent = total.toFixed(2);
        }
        
        // Size change handler
        if (sizeRadios.length > 0) {
            sizeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    basePrice = parseFloat(this.dataset.price);
                    updateTotalPrice();
                    
                    // Update wings quantity text for Ultimate Pizza & Wings Combo
                    const wingsQuantityText = document.getElementById('wings-quantity-text');
                    if (wingsQuantityText) {
                        const selectedSize = this.value;
                        if (selectedSize === 'medium') {
                            wingsQuantityText.textContent = '12 Wings';
                        } else {
                            wingsQuantityText.textContent = '3 lbs Wings';
                        }
                    }
                    
                    // Recalculate topping prices for the new size if needed
                    @if($product->is_pizza && !$product->is_specialty)
                    calculateToppingPrice();
                    @endif
                });
            });
        }
        
        // Quantity change handlers
        decreaseQtyBtn.addEventListener('click', function() {
            if (quantity > 1) {
                quantity--;
                quantityInput.value = quantity;
                updateTotalPrice();
            }
        });
        
        increaseQtyBtn.addEventListener('click', function() {
            if (quantity < 10) {
                quantity++;
                quantityInput.value = quantity;
                updateTotalPrice();
            }
        });
        
        quantityInput.addEventListener('change', function() {
            const newQty = parseInt(this.value);
            if (newQty >= 1 && newQty <= 10) {
                quantity = newQty;
                updateTotalPrice();
            } else {
                // Reset to valid value
                this.value = quantity;
            }
        });
        
        // Topping selection and pricing for non-specialty pizzas
        @if($product->is_pizza && !$product->is_specialty)
        const toppingCheckboxes = document.querySelectorAll('.topping-checkbox');
        const toppingCountElement = document.getElementById('topping-count');
        const extraToppingsMessage = document.getElementById('extra-toppings-message');
        const extraToppingsCount = document.getElementById('extra-toppings-count');
        
        // Get the base size to determine topping prices
        function getCurrentSize() {
            const selectedSize = document.querySelector('input[name="size"]:checked');
            return selectedSize ? selectedSize.value : '{{ $firstSize }}';
        }
        
        // Calculate the topping price based on selected toppings
        function calculateToppingPrice() {
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            toppingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (toppingCountElement) {
                toppingCountElement.textContent = totalCountsAs;
            }
            
            // Calculate extra toppings
            const maxToppings = {{ $product->max_toppings ?? 0 }};
            const freeToppings = {{ $product->free_toppings ?? 0 }};
            let extraToppingCount = 0;
            
            if (freeToppings > 0 && totalCountsAs > freeToppings) {
                extraToppingCount = totalCountsAs - freeToppings;
                
                if (extraToppingsMessage) {
                    extraToppingsMessage.classList.remove('d-none');
                    extraToppingsCount.textContent = extraToppingCount;
                }
            } else {
                if (extraToppingsMessage) {
                    extraToppingsMessage.classList.add('d-none');
                }
            }
            
            // Calculate price for extra toppings based on size - exact pricing from the menu document
            @php
                $addOns = json_decode($product->add_ons ?? '{}', true);
                $extraToppingPrice = $addOns['extra_topping_price'] ?? 0;
            @endphp
            const extraToppingPrice = {{ $extraToppingPrice }};
            
            toppingPrice = extraToppingCount * extraToppingPrice;
            updateTotalPrice();
        }
        
        // Topping selection handlers
        toppingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                calculateToppingPrice();
                
                // Check if max toppings reached
                if (checkbox.dataset.maxToppings) {
                    const maxToppings = parseInt(checkbox.dataset.maxToppings);
                    let totalCountsAs = 0;
                    
                    toppingCheckboxes.forEach(cb => {
                        if (cb.checked) {
                            totalCountsAs += parseInt(cb.dataset.countsAs);
                        }
                    });
                    
                    // Disable unchecked toppings if max is reached
                    if (totalCountsAs >= maxToppings) {
                        toppingCheckboxes.forEach(cb => {
                            if (!cb.checked) {
                                cb.disabled = true;
                            }
                        });
                    } else {
                        // Enable all toppings
                        toppingCheckboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                    }
                }
            });
        });
        @endif
        
        // Extra selection handlers
        const extraCheckboxes = document.querySelectorAll('.extra-checkbox');
        
        if (extraCheckboxes.length > 0) {
            extraCheckboxes.forEach(checkbox => {
                // Get the related elements
                const extraId = checkbox.dataset.extraId;
                const extraContainer = checkbox.closest('.col-md-6');
                const quantitySelector = extraContainer.querySelector('.extra-quantity-selector');
                const quantityInput = extraContainer.querySelector('.extra-quantity');
                const hiddenQuantityInput = extraContainer.querySelector('.extra-quantity-input');
                const decreaseBtn = extraContainer.querySelector('.decrease-extra-qty');
                const increaseBtn = extraContainer.querySelector('.increase-extra-qty');
                const extraPrice = parseFloat(checkbox.dataset.price);
                const maxQuantity = parseInt(checkbox.dataset.maxQuantity);
                
                // Initial state - if checkbox is default checked, add its price
                if (checkbox.checked) {
                    const qty = parseInt(quantityInput.value);
                    extrasPrice += extraPrice * qty;
                    updateTotalPrice();
                }
                
                // Checkbox change handler
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Enable quantity controls and set to 1
                        decreaseBtn.disabled = false;
                        increaseBtn.disabled = false;
                        quantityInput.value = 1;
                        hiddenQuantityInput.value = 1;
                        
                        // Update price
                        extrasPrice += extraPrice;
                    } else {
                        // Disable quantity controls and set to 0
                        decreaseBtn.disabled = true;
                        increaseBtn.disabled = true;
                        
                        // Update price - subtract the price for the current quantity
                        const currentQty = parseInt(quantityInput.value);
                        extrasPrice -= extraPrice * currentQty;
                        
                        // Reset quantity
                        quantityInput.value = 0;
                        hiddenQuantityInput.value = 0;
                    }
                    updateTotalPrice();
                });
                
                // Decrease quantity button
                decreaseBtn.addEventListener('click', function() {
                    let currentQty = parseInt(quantityInput.value);
                    if (currentQty > 1) {
                        currentQty--;
                        quantityInput.value = currentQty;
                        hiddenQuantityInput.value = currentQty;
                        
                        // Update price
                        extrasPrice -= extraPrice;
                        updateTotalPrice();
                    } else if (currentQty === 1) {
                        // Uncheck the checkbox if reducing to 0
                        checkbox.checked = false;
                        decreaseBtn.disabled = true;
                        increaseBtn.disabled = true;
                        quantityInput.value = 0;
                        hiddenQuantityInput.value = 0;
                        
                        // Update price
                        extrasPrice -= extraPrice;
                        updateTotalPrice();
                    }
                });
                
                // Increase quantity button
                increaseBtn.addEventListener('click', function() {
                    let currentQty = parseInt(quantityInput.value);
                    if (currentQty < maxQuantity) {
                        currentQty++;
                        quantityInput.value = currentQty;
                        hiddenQuantityInput.value = currentQty;
                        
                        // Update price
                        extrasPrice += extraPrice;
                        updateTotalPrice();
                    }
                });
            });
        }
        
        // Add-on checkbox handlers for Ultimate Pizza & Wings Combo
        const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
        if (addonCheckboxes.length > 0) {
            addonCheckboxes.forEach(checkbox => {
                const parent = checkbox.closest('.col-md-6');
                const quantityInput = parent.querySelector('.addon-quantity');
                const decreaseBtn = parent.querySelector('.decrease-addon-qty');
                const increaseBtn = parent.querySelector('.increase-addon-qty');
                const itemPrice = parseFloat(checkbox.dataset.price);
                
                // Checkbox change handler
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Enable quantity controls and set to 1
                        decreaseBtn.disabled = false;
                        increaseBtn.disabled = false;
                        quantityInput.value = 1;
                        
                        // Update price
                        addonPrice += itemPrice;
                    } else {
                        // Disable quantity controls and set to 0
                        decreaseBtn.disabled = true;
                        increaseBtn.disabled = true;
                        
                        // Update price - subtract the price for the current quantity
                        const currentQty = parseInt(quantityInput.value);
                        addonPrice -= itemPrice * currentQty;
                        
                        // Reset quantity
                        quantityInput.value = 0;
                    }
                    updateTotalPrice();
                });
                
                // Decrease quantity button
                decreaseBtn.addEventListener('click', function() {
                    let currentQty = parseInt(quantityInput.value);
                    if (currentQty > 1) {
                        currentQty--;
                        quantityInput.value = currentQty;
                        
                        // Update price
                        addonPrice -= itemPrice;
                        updateTotalPrice();
                    } else if (currentQty === 1) {
                        // Uncheck the checkbox if reducing to 0
                        checkbox.checked = false;
                        decreaseBtn.disabled = true;
                        increaseBtn.disabled = true;
                        quantityInput.value = 0;
                        
                        // Update price
                        addonPrice -= itemPrice;
                        updateTotalPrice();
                    }
                });
                
                // Increase quantity button
                increaseBtn.addEventListener('click', function() {
                    let currentQty = parseInt(quantityInput.value);
                    if (currentQty < 5) {
                        currentQty++;
                        quantityInput.value = currentQty;
                        
                        // Update price
                        addonPrice += itemPrice;
                        updateTotalPrice();
                    }
                });
            });
        }
        
        // Second Pizza Option handlers (for Jumbo size only)
        const secondPizzaOptionContainer = document.getElementById('second-pizza-option');
        const secondPizzaContainer = document.getElementById('second-pizza-container');
        const secondPizzaRadios = document.querySelectorAll('input[name="add_second_pizza"]');
        const secondPizzaYesRadio = document.getElementById('add-second-pizza-yes');
        const secondPizzaNoRadio = document.getElementById('add-second-pizza-no');
        const secondPizzaToppings = document.querySelectorAll('.second-pizza-topping');
        const secondPizzaToppingCount = document.getElementById('second-pizza-topping-count');
        const secondPizzaExtraToppings = document.querySelectorAll('.second-pizza-extra-topping');
        const secondPizzaExtraToppingCount = document.getElementById('second-pizza-extra-topping-count');
        const secondPizzaExtraRadios = document.querySelectorAll('input[name="add_second_pizza_extra_toppings"]');
        const secondPizzaExtraContainer = document.getElementById('second-pizza-extra-toppings-container');
        
        // Show/hide second pizza option based on size selection
        function toggleSecondPizzaOption() {
            const selectedSize = document.querySelector('input[name="size"]:checked').value;
            if (selectedSize === 'jumbo') {
                secondPizzaOptionContainer.classList.remove('d-none');
            } else {
                secondPizzaOptionContainer.classList.add('d-none');
                secondPizzaNoRadio.checked = true;
                secondPizzaContainer.classList.add('d-none');
                secondPizzaPrice = 0;
                updateTotalPrice();
            }
        }
        
        // Initial toggle based on default size
        toggleSecondPizzaOption();
        
        // Toggle on size change
        sizeRadios.forEach(radio => {
            radio.addEventListener('change', toggleSecondPizzaOption);
        });
        
        // Second pizza radio buttons
        secondPizzaRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'yes') {
                    secondPizzaContainer.classList.remove('d-none');
                    secondPizzaPrice = 15.99; // Price to add second pizza for Jumbo
                } else {
                    secondPizzaContainer.classList.add('d-none');
                    secondPizzaPrice = 0;
                    
                    // Reset all second pizza toppings
                    secondPizzaToppings.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Reset all second pizza extra toppings
                    secondPizzaExtraToppings.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Reset extra toppings section
                    document.getElementById('add-second-toppings-no').checked = true;
                    secondPizzaExtraContainer.classList.add('d-none');
                    
                    // Reset prices
                    secondPizzaToppingPrice = 0;
                }
                updateTotalPrice();
            });
        });
        
        // Second pizza extra toppings toggle
        secondPizzaExtraRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'yes') {
                    secondPizzaExtraContainer.classList.remove('d-none');
                } else {
                    secondPizzaExtraContainer.classList.add('d-none');
                    
                    // Reset all second pizza extra toppings
                    secondPizzaExtraToppings.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Reset price
                    calculateSecondPizzaExtraToppingPrice();
                }
            });
        });
        
        // Calculate second pizza toppings
        function calculateSecondPizzaToppings() {
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            secondPizzaToppings.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (secondPizzaToppingCount) {
                secondPizzaToppingCount.textContent = totalCountsAs;
            }
        }
        
        // Calculate second pizza extra toppings price
        function calculateSecondPizzaExtraToppingPrice() {
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            secondPizzaExtraToppings.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (secondPizzaExtraToppingCount) {
                secondPizzaExtraToppingCount.textContent = totalCountsAs;
            }
            
            // Calculate price based on size
            const size = getCurrentSize();
            let pricePerTopping = 0;
            
            if (selectedToppings > 0) {
                switch(size) {
                    case 'medium':
                        pricePerTopping = 1.60; // Medium pricing from menu
                        break;
                    case 'large':
                        pricePerTopping = 2.10; // Large pricing from menu
                        break;
                    case 'xl':
                        pricePerTopping = 2.30; // X-Large pricing from menu
                        break;
                    default:
                        pricePerTopping = 1.60; // Default to medium if unknown
                }
            }
            
            secondPizzaToppingPrice = totalCountsAs * pricePerTopping;
            updateTotalPrice();
        }
        
        // Second pizza topping handlers
        secondPizzaToppings.forEach(checkbox => {
            checkbox.addEventListener('change', calculateSecondPizzaToppings);
        });
        
        // Second pizza extra topping handlers
        secondPizzaExtraToppings.forEach(checkbox => {
            checkbox.addEventListener('change', calculateSecondPizzaExtraToppingPrice);
        });
        
        // Initialize the total price
        updateTotalPrice();

        // Two Pizza Combo Handlers
        @if(strpos($product->name, 'Two Medium Pizzas Combo') !== false || strpos($product->name, 'Two Large Pizzas Combo') !== false || strpos($product->name, 'Two XL Pizzas Combo') !== false)
        
        // First Pizza Toppings
        const firstPizzaToppingCheckboxes = document.querySelectorAll('.first-pizza-topping');
        const firstPizzaToppingCount = document.getElementById('first-pizza-topping-count');
        const firstPizzaExtraToppingsMessage = document.getElementById('first-pizza-extra-toppings-message');
        const firstPizzaExtraToppingsCount = document.getElementById('first-pizza-extra-toppings-count');
        let firstPizzaToppingPrice = 0;

        // First Pizza Extra Toppings
        const addFirstPizzaExtraToppingsRadios = document.querySelectorAll('input[name="add_first_pizza_extra_toppings"]');
        const firstPizzaExtraToppingsContainer = document.getElementById('first-pizza-extra-toppings-container');
        const firstPizzaExtraToppingCheckboxes = document.querySelectorAll('.first-pizza-extra-topping');
        const firstPizzaExtraToppingCount = document.getElementById('first-pizza-extra-topping-count');
        let firstPizzaExtraToppingPrice = 0;

        // Second Pizza Toppings
        const secondPizzaToppingCheckboxes = document.querySelectorAll('.second-pizza-topping');
        const secondPizzaToppingCount = document.getElementById('second-pizza-topping-count');
        const secondPizzaExtraToppingsMessage = document.getElementById('second-pizza-extra-toppings-message');
        const secondPizzaExtraToppingsCount = document.getElementById('second-pizza-extra-toppings-count');
        let secondPizzaToppingPrice = 0;

        // Second Pizza Extra Toppings
        const addSecondPizzaExtraToppingsRadios = document.querySelectorAll('input[name="add_second_pizza_extra_toppings"]');
        const secondPizzaExtraToppingsContainer = document.getElementById('second-pizza-extra-toppings-container');
        const secondPizzaExtraToppingCheckboxes = document.querySelectorAll('.second-pizza-extra-topping');
        const secondPizzaExtraToppingCount = document.getElementById('second-pizza-extra-topping-count');
        let secondPizzaExtraToppingPrice = 0;

        // Third Pizza Option
        const thirdPizzaRadios = document.querySelectorAll('input[name="add_third_pizza"]');
        const thirdPizzaYesRadio = document.getElementById('add-third-pizza-yes');
        const thirdPizzaNoRadio = document.getElementById('add-third-pizza-no');
        const thirdPizzaContainer = document.getElementById('third-pizza-container');
        let thirdPizzaPrice = 0;

        // Third Pizza Toppings
        const thirdPizzaToppingCheckboxes = document.querySelectorAll('.third-pizza-topping');
        const thirdPizzaToppingCount = document.getElementById('third-pizza-topping-count');
        let thirdPizzaToppingPrice = 0;

        // Third Pizza Extra Toppings
        const addThirdPizzaExtraToppingsRadios = document.querySelectorAll('input[name="add_third_pizza_extra_toppings"]');
        const thirdPizzaExtraToppingsContainer = document.getElementById('third-pizza-extra-toppings-container');
        const thirdPizzaExtraToppingCheckboxes = document.querySelectorAll('.third-pizza-extra-topping');
        const thirdPizzaExtraToppingCount = document.getElementById('third-pizza-extra-topping-count');
        let thirdPizzaExtraToppingPrice = 0;

        // Calculate first pizza toppings
        function calculateFirstPizzaToppings() {
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            firstPizzaToppingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (firstPizzaToppingCount) {
                firstPizzaToppingCount.textContent = totalCountsAs;
            }
            
            // Calculate extra toppings
            const maxToppings = {{ $product->max_toppings ?? 0 }};
            let extraToppingCount = 0;
            
            if (maxToppings > 0 && totalCountsAs > maxToppings) {
                extraToppingCount = totalCountsAs - maxToppings;
                
                if (firstPizzaExtraToppingsMessage) {
                    firstPizzaExtraToppingsMessage.classList.remove('d-none');
                    firstPizzaExtraToppingsCount.textContent = extraToppingCount;
                }
            } else {
                if (firstPizzaExtraToppingsMessage) {
                    firstPizzaExtraToppingsMessage.classList.add('d-none');
                }
            }
            
            // Calculate price
            const size = getCurrentSize();
            let pricePerTopping = 0;
            
            if (extraToppingCount > 0) {
                switch(size) {
                    case 'medium':
                        pricePerTopping = 1.60; // Medium pricing from menu
                        break;
                    case 'large':
                        pricePerTopping = 2.10; // Large pricing from menu
                        break;
                    case 'xl':
                        pricePerTopping = 2.30; // X-Large pricing from menu
                        break;
                    default:
                        pricePerTopping = 1.60; // Default to medium if unknown
                }
            }
            
            firstPizzaToppingPrice = extraToppingCount * pricePerTopping;
            updateTotalPrice();
        }

        // First pizza topping handlers
        firstPizzaToppingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                calculateFirstPizzaToppings();
                
                // Check if max toppings reached
                if (checkbox.dataset.maxToppings) {
                    const maxToppings = parseInt(checkbox.dataset.maxToppings);
                    let totalCountsAs = 0;
                    
                    firstPizzaToppingCheckboxes.forEach(cb => {
                        if (cb.checked) {
                            totalCountsAs += parseInt(cb.dataset.countsAs);
                        }
                    });
                    
                    // Disable unchecked toppings if max is reached
                    if (totalCountsAs >= maxToppings) {
                        firstPizzaToppingCheckboxes.forEach(cb => {
                            if (!cb.checked) {
                                cb.disabled = true;
                            }
                        });
                    } else {
                        // Enable all toppings
                        firstPizzaToppingCheckboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                    }
                }
            });
        });

        // First Pizza Extra Toppings Toggle
        addFirstPizzaExtraToppingsRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'yes') {
                    firstPizzaExtraToppingsContainer.classList.remove('d-none');
                } else {
                    firstPizzaExtraToppingsContainer.classList.add('d-none');
                    
                    // Uncheck all extra topping checkboxes when toggling to "No"
                    firstPizzaExtraToppingCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Reset price
                    firstPizzaExtraToppingPrice = 0;
                    updateTotalPrice();
                    
                    // Update display
                    if (firstPizzaExtraToppingCount) {
                        firstPizzaExtraToppingCount.textContent = '0';
                    }
                }
            });
        });

        // Calculate first pizza extra toppings price
        function calculateFirstPizzaExtraToppingPrice() {
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            firstPizzaExtraToppingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (firstPizzaExtraToppingCount) {
                firstPizzaExtraToppingCount.textContent = totalCountsAs;
            }
            
            // Calculate price based on size
            const size = getCurrentSize();
            let pricePerTopping = 0;
            
            if (selectedToppings > 0) {
                switch(size) {
                    case 'medium':
                        pricePerTopping = 1.60; // Medium pricing from menu
                        break;
                    case 'large':
                        pricePerTopping = 2.10; // Large pricing from menu
                        break;
                    case 'xl':
                        pricePerTopping = 2.30; // X-Large pricing from menu
                        break;
                    default:
                        pricePerTopping = 1.60; // Default to medium if unknown
                }
            }
            
            firstPizzaExtraToppingPrice = totalCountsAs * pricePerTopping;
            updateTotalPrice();
        }

        // First pizza extra topping handlers
        firstPizzaExtraToppingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                calculateFirstPizzaExtraToppingPrice();
            });
        });

        // Calculate second pizza toppings
        function calculateSecondPizzaToppings() {
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            secondPizzaToppingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (secondPizzaToppingCount) {
                secondPizzaToppingCount.textContent = totalCountsAs;
            }
            
            // Calculate extra toppings
            const maxToppings = {{ $product->max_toppings ?? 0 }};
            let extraToppingCount = 0;
            
            if (maxToppings > 0 && totalCountsAs > maxToppings) {
                extraToppingCount = totalCountsAs - maxToppings;
                
                if (secondPizzaExtraToppingsMessage) {
                    secondPizzaExtraToppingsMessage.classList.remove('d-none');
                    secondPizzaExtraToppingsCount.textContent = extraToppingCount;
                }
            } else {
                if (secondPizzaExtraToppingsMessage) {
                    secondPizzaExtraToppingsMessage.classList.add('d-none');
                }
            }
            
            // Calculate price
            const size = getCurrentSize();
            let pricePerTopping = 0;
            
            if (extraToppingCount > 0) {
                switch(size) {
                    case 'medium':
                        pricePerTopping = 1.60; // Medium pricing from menu
                        break;
                    case 'large':
                        pricePerTopping = 2.10; // Large pricing from menu
                        break;
                    case 'xl':
                        pricePerTopping = 2.30; // X-Large pricing from menu
                        break;
                    default:
                        pricePerTopping = 1.60; // Default to medium if unknown
                }
            }
            
            secondPizzaToppingPrice = extraToppingCount * pricePerTopping;
            updateTotalPrice();
        }

        // Second pizza topping handlers
        secondPizzaToppingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                calculateSecondPizzaToppings();
                
                // Check if max toppings reached
                if (checkbox.dataset.maxToppings) {
                    const maxToppings = parseInt(checkbox.dataset.maxToppings);
                    let totalCountsAs = 0;
                    
                    secondPizzaToppingCheckboxes.forEach(cb => {
                        if (cb.checked) {
                            totalCountsAs += parseInt(cb.dataset.countsAs);
                        }
                    });
                    
                    // Disable unchecked toppings if max is reached
                    if (totalCountsAs >= maxToppings) {
                        secondPizzaToppingCheckboxes.forEach(cb => {
                            if (!cb.checked) {
                                cb.disabled = true;
                            }
                        });
                    } else {
                        // Enable all toppings
                        secondPizzaToppingCheckboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                    }
                }
            });
        });

        // Second Pizza Extra Toppings Toggle
        addSecondPizzaExtraToppingsRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'yes') {
                    secondPizzaExtraToppingsContainer.classList.remove('d-none');
                } else {
                    secondPizzaExtraToppingsContainer.classList.add('d-none');
                    
                    // Uncheck all extra topping checkboxes when toggling to "No"
                    secondPizzaExtraToppingCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Reset price
                    secondPizzaExtraToppingPrice = 0;
                    updateTotalPrice();
                    
                    // Update display
                    if (secondPizzaExtraToppingCount) {
                        secondPizzaExtraToppingCount.textContent = '0';
                    }
                }
            });
        });

        // Calculate second pizza extra toppings price
        function calculateSecondPizzaExtraToppingPrice() {
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            secondPizzaExtraToppingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (secondPizzaExtraToppingCount) {
                secondPizzaExtraToppingCount.textContent = totalCountsAs;
            }
            
            // Calculate price based on size
            const size = getCurrentSize();
            let pricePerTopping = 0;
            
            if (selectedToppings > 0) {
                switch(size) {
                    case 'medium':
                        pricePerTopping = 1.60; // Medium pricing from menu
                        break;
                    case 'large':
                        pricePerTopping = 2.10; // Large pricing from menu
                        break;
                    case 'xl':
                        pricePerTopping = 2.30; // X-Large pricing from menu
                        break;
                    default:
                        pricePerTopping = 1.60; // Default to medium if unknown
                }
            }
            
            secondPizzaExtraToppingPrice = totalCountsAs * pricePerTopping;
            updateTotalPrice();
        }

        // Second pizza extra topping handlers
        secondPizzaExtraToppingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                calculateSecondPizzaExtraToppingPrice();
            });
        });

        // Third Pizza Options
        thirdPizzaRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'yes' && thirdPizzaContainer) {
                    thirdPizzaContainer.classList.remove('d-none');
                    
                    // Set price based on combo type
                    @if(strpos($product->name, 'Two Medium Pizzas Combo') !== false)
                        thirdPizzaPrice = 10.99;
                    @elseif(strpos($product->name, 'Two Large Pizzas Combo') !== false)
                        thirdPizzaPrice = 12.99;
                    @elseif(strpos($product->name, 'Two XL Pizzas Combo') !== false)
                        thirdPizzaPrice = 13.99;
                    @endif
                } else {
                    thirdPizzaContainer.classList.add('d-none');
                    thirdPizzaPrice = 0;
                    
                    // Reset all third pizza toppings
                    thirdPizzaToppingCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                        checkbox.disabled = false; // Re-enable all checkboxes
                    });
                    
                    // Reset all third pizza extra toppings
                    thirdPizzaExtraToppingCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Reset extra toppings section
                    const thirdToppingsNo = document.getElementById('add-third-toppings-no');
                    if (thirdToppingsNo) {
                        thirdToppingsNo.checked = true;
                    }
                    
                    if (thirdPizzaExtraToppingsContainer) {
                        thirdPizzaExtraToppingsContainer.classList.add('d-none');
                    }
                    
                    // Reset prices
                    thirdPizzaToppingPrice = 0;
                    thirdPizzaExtraToppingPrice = 0;
                }
                updateTotalPrice();
            });
        });

        // Third Pizza Extra Toppings Toggle
        addThirdPizzaExtraToppingsRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'yes' && thirdPizzaExtraToppingsContainer) {
                    thirdPizzaExtraToppingsContainer.classList.remove('d-none');
                } else if (thirdPizzaExtraToppingsContainer) {
                    thirdPizzaExtraToppingsContainer.classList.add('d-none');
                    
                    // Uncheck all extra topping checkboxes when toggling to "No"
                    if (thirdPizzaExtraToppingCheckboxes && thirdPizzaExtraToppingCheckboxes.length > 0) {
                        thirdPizzaExtraToppingCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                    }
                    
                    // Reset price
                    thirdPizzaExtraToppingPrice = 0;
                    updateTotalPrice();
                    
                    // Update display
                    if (thirdPizzaExtraToppingCount) {
                        thirdPizzaExtraToppingCount.textContent = '0';
                    }
                }
            });
        });

        // Calculate third pizza toppings
        function calculateThirdPizzaToppings() {
            if (!thirdPizzaToppingCheckboxes || thirdPizzaToppingCheckboxes.length === 0) return;
            
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            thirdPizzaToppingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (thirdPizzaToppingCount) {
                thirdPizzaToppingCount.textContent = totalCountsAs;
            }
            
            // Calculate extra toppings
            const maxToppings = {{ $product->max_toppings ?? 0 }};
            let extraToppingCount = 0;
            
            if (maxToppings > 0 && totalCountsAs > maxToppings) {
                extraToppingCount = totalCountsAs - maxToppings;
                thirdPizzaToppingPrice = extraToppingCount * getPricePerTopping();
            } else {
                thirdPizzaToppingPrice = 0;
            }
            
            updateTotalPrice();
        }

        // Helper function to get topping price based on size
        function getPricePerTopping() {
            @if(strpos($product->name, 'Two Medium Pizzas Combo') !== false)
                return 1.60;
            @elseif(strpos($product->name, 'Two Large Pizzas Combo') !== false)
                return 2.10;
            @elseif(strpos($product->name, 'Two XL Pizzas Combo') !== false)
                return 2.30;
            @else
                return 1.60;
            @endif
        }

        // Third pizza topping handlers
        if (thirdPizzaToppingCheckboxes && thirdPizzaToppingCheckboxes.length > 0) {
            thirdPizzaToppingCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    calculateThirdPizzaToppings();
                    
                    // Check if max toppings reached
                    if (checkbox.dataset.maxToppings) {
                        const maxToppings = parseInt(checkbox.dataset.maxToppings);
                        let totalCountsAs = 0;
                        
                        thirdPizzaToppingCheckboxes.forEach(cb => {
                            if (cb.checked) {
                                totalCountsAs += parseInt(cb.dataset.countsAs);
                            }
                        });
                        
                        // Disable unchecked toppings if max is reached
                        if (totalCountsAs >= maxToppings) {
                            thirdPizzaToppingCheckboxes.forEach(cb => {
                                if (!cb.checked) {
                                    cb.disabled = true;
                                }
                            });
                        } else {
                            // Enable all toppings
                            thirdPizzaToppingCheckboxes.forEach(cb => {
                                cb.disabled = false;
                            });
                        }
                    }
                });
            });
        }
        
        // Calculate third pizza extra toppings price
        function calculateThirdPizzaExtraToppingPrice() {
            if (!thirdPizzaExtraToppingCheckboxes || thirdPizzaExtraToppingCheckboxes.length === 0) return;
            
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            thirdPizzaExtraToppingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (thirdPizzaExtraToppingCount) {
                thirdPizzaExtraToppingCount.textContent = totalCountsAs;
            }
            
            // Calculate price
            thirdPizzaExtraToppingPrice = totalCountsAs * getPricePerTopping();
            updateTotalPrice();
        }

        // Third pizza extra topping handlers
        if (thirdPizzaExtraToppingCheckboxes && thirdPizzaExtraToppingCheckboxes.length > 0) {
            thirdPizzaExtraToppingCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    calculateThirdPizzaExtraToppingPrice();
                });
            });
        }

        // Update the total price calculation function to include all pizza prices
        function updateTotalPrice() {
            const total = (basePrice + 
                          firstPizzaToppingPrice + 
                          firstPizzaExtraToppingPrice + 
                          secondPizzaToppingPrice + 
                          secondPizzaExtraToppingPrice + 
                          thirdPizzaPrice + 
                          thirdPizzaToppingPrice + 
                          thirdPizzaExtraToppingPrice + 
                          extrasPrice + 
                          addonPrice) * quantity;
            totalPriceElement.textContent = total.toFixed(2);
        }
        @endif
    });

    // Ultimate Pizza & Wings Combo JS
    @if(strpos($product->name, 'Ultimate Pizza & Wings Combo') !== false)
    document.addEventListener('DOMContentLoaded', function() {
        // Size selection
        const sizeRadios = document.querySelectorAll('input[name="size"]');
        const totalPriceElement = document.getElementById('total-price');
        const quantityInput = document.querySelector('input[name="quantity"]');
        const decreaseQtyBtn = document.querySelector('.decrease-qty');
        const increaseQtyBtn = document.querySelector('.increase-qty');
        
        // Initialize variables for pricing
        @if($product->is_specialty)
        let basePrice = {{ isset($sizes[$firstSize]) ? $sizes[$firstSize] : $product->getDisplayPrice() }};
        @else
        let basePrice = {{ isset($sizes[$firstSize]) ? $sizes[$firstSize] : $product->price }};
        @endif
        
        let toppingPrice = 0;
        let addonPrice = 0;
        let quantity = 1;
        
        // Calculate and update total price
        function updateTotalPrice() {
            const total = (basePrice + toppingPrice + addonPrice) * quantity;
            totalPriceElement.textContent = total.toFixed(2);
        }
        
        // Size change handler
        if (sizeRadios.length > 0) {
            sizeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    basePrice = parseFloat(this.dataset.price);
                    updateTotalPrice();
                    
                    // Update wings quantity text
                    const wingsQuantityText = document.getElementById('wings-quantity-text');
                    if (wingsQuantityText) {
                        const selectedSize = this.value;
                        if (selectedSize === 'medium') {
                            wingsQuantityText.textContent = '12 Wings';
                        } else {
                            wingsQuantityText.textContent = '3 lbs Wings';
                        }
                    }
                    
                    // Recalculate topping prices for the new size
                    calculateToppingPrice();
                });
            });
        }
        
        // Quantity change handlers
        decreaseQtyBtn.addEventListener('click', function() {
            if (quantity > 1) {
                quantity--;
                quantityInput.value = quantity;
                updateTotalPrice();
            }
        });
        
        increaseQtyBtn.addEventListener('click', function() {
            if (quantity < 10) {
                quantity++;
                quantityInput.value = quantity;
                updateTotalPrice();
            }
        });
        
        quantityInput.addEventListener('change', function() {
            const newQty = parseInt(this.value);
            if (newQty >= 1 && newQty <= 10) {
                quantity = newQty;
                updateTotalPrice();
            } else {
                // Reset to valid value
                this.value = quantity;
            }
        });
        
        // Pizza topping handlers
        const toppingCheckboxes = document.querySelectorAll('.topping-checkbox');
        const toppingCountElement = document.getElementById('topping-count');
        const extraToppingsMessage = document.getElementById('extra-toppings-message');
        const extraToppingsCount = document.getElementById('extra-toppings-count');
        
        // Get the base size to determine topping prices
        function getCurrentSize() {
            const selectedSize = document.querySelector('input[name="size"]:checked');
            return selectedSize ? selectedSize.value : 'medium';
        }
        
        // Calculate the topping price based on selected toppings
        function calculateToppingPrice() {
            let selectedToppings = 0;
            let totalCountsAs = 0;
            
            toppingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const countsAs = parseInt(checkbox.dataset.countsAs);
                    totalCountsAs += countsAs;
                    selectedToppings++;
                }
            });
            
            // Display topping count
            if (toppingCountElement) {
                toppingCountElement.textContent = totalCountsAs;
            }
            
            // Calculate extra toppings
            const maxToppings = {{ $product->max_toppings ?? 0 }};
            let extraToppingCount = 0;
            
            if (maxToppings > 0 && totalCountsAs > maxToppings) {
                extraToppingCount = totalCountsAs - maxToppings;
                
                if (extraToppingsMessage) {
                    extraToppingsMessage.classList.remove('d-none');
                    extraToppingsCount.textContent = extraToppingCount;
                }
            } else {
                if (extraToppingsMessage) {
                    extraToppingsMessage.classList.add('d-none');
                }
            }
            
            // Calculate price for extra toppings based on size
            const size = getCurrentSize();
            let pricePerTopping = 0;
            
            if (extraToppingCount > 0) {
                switch(size) {
                    case 'medium':
                        pricePerTopping = 1.60; // Medium pricing from menu
                        break;
                    case 'large':
                        pricePerTopping = 2.10; // Large pricing from menu
                        break;
                    case 'xl':
                        pricePerTopping = 2.30; // X-Large pricing from menu
                        break;
                    case 'jumbo':
                        pricePerTopping = 2.90; // Jumbo pricing from menu
                        break;
                    case 'slab':
                        pricePerTopping = 2.90; // Slab pricing from menu
                        break;
                    default:
                        pricePerTopping = 1.60; // Default to medium if unknown
                }
            }
            
            toppingPrice = extraToppingCount * pricePerTopping;
            updateTotalPrice();
        }
        
        // Topping selection handlers
        toppingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                calculateToppingPrice();
                
                // Check if max toppings reached
                if (checkbox.dataset.maxToppings) {
                    const maxToppings = parseInt(checkbox.dataset.maxToppings);
                    let totalCountsAs = 0;
                    
                    toppingCheckboxes.forEach(cb => {
                        if (cb.checked) {
                            totalCountsAs += parseInt(cb.dataset.countsAs);
                        }
                    });
                    
                    // Disable unchecked toppings if max is reached
                    if (totalCountsAs >= maxToppings) {
                        toppingCheckboxes.forEach(cb => {
                            if (!cb.checked) {
                                cb.disabled = true;
                            }
                        });
                    } else {
                        // Enable all toppings
                        toppingCheckboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                    }
                }
            });
        });
        
        // Add-on selection and pricing
        const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
        const addonQuantityInputs = document.querySelectorAll('.addon-quantity');
        const increaseAddonQtyBtns = document.querySelectorAll('.increase-addon-qty');
        const decreaseAddonQtyBtns = document.querySelectorAll('.decrease-addon-qty');
        
        // Add-on checkbox handlers
        addonCheckboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', function() {
                const quantityInput = addonQuantityInputs[index];
                const increaseBtn = increaseAddonQtyBtns[index];
                const decreaseBtn = decreaseAddonQtyBtns[index];
                
                if (this.checked) {
                    // Enable quantity controls
                    quantityInput.value = 1;
                    increaseBtn.disabled = false;
                    decreaseBtn.disabled = false;
                    
                    // Update price
                    calculateAddonPrice();
                } else {
                    // Disable quantity controls and reset
                    quantityInput.value = 0;
                    increaseBtn.disabled = true;
                    decreaseBtn.disabled = true;
                    
                    // Update price
                    calculateAddonPrice();
                }
            });
        });
        
        // Increase add-on quantity
        increaseAddonQtyBtns.forEach((btn, index) => {
            btn.addEventListener('click', function() {
                const quantityInput = addonQuantityInputs[index];
                const currentQty = parseInt(quantityInput.value);
                const maxQty = parseInt(quantityInput.max);
                
                if (currentQty < maxQty) {
                    quantityInput.value = currentQty + 1;
                    calculateAddonPrice();
                }
            });
        });
        
        // Decrease add-on quantity
        decreaseAddonQtyBtns.forEach((btn, index) => {
            btn.addEventListener('click', function() {
                const quantityInput = addonQuantityInputs[index];
                const currentQty = parseInt(quantityInput.value);
                
                if (currentQty > 1) {
                    quantityInput.value = currentQty - 1;
                    calculateAddonPrice();
                } else if (currentQty === 1) {
                    // If quantity becomes 0, uncheck the addon
                    addonCheckboxes[index].checked = false;
                    quantityInput.value = 0;
                    this.disabled = true;
                    increaseAddonQtyBtns[index].disabled = true;
                    calculateAddonPrice();
                }
            });
        });
        
        // Calculate add-on price
        function calculateAddonPrice() {
            addonPrice = 0;
            
            addonCheckboxes.forEach((checkbox, index) => {
                if (checkbox.checked) {
                    const price = parseFloat(checkbox.dataset.price);
                    const qty = parseInt(addonQuantityInputs[index].value);
                    addonPrice += price * qty;
                }
            });
            
            updateTotalPrice();
        }
        
        // Initialize price calculation
        calculateToppingPrice();
        calculateAddonPrice();
    });
    @endif
</script>
@endpush
@endsection