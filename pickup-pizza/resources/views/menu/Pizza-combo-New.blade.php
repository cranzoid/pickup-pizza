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
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
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
                
                <!-- First Pizza Toppings -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">First Pizza Toppings <small class="text-muted">(Max {{ $product->max_toppings }} toppings)</small></h5>
                    <div class="row g-2 first-pizza-toppings-container">
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
                                        <input class="form-check-input first-pizza-topping" type="checkbox" name="first_pizza_toppings[]" 
                                            id="first-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                        <label class="form-check-label" for="first-pizza-topping-{{ $topping->id }}">
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
                                        <input class="form-check-input first-pizza-topping" type="checkbox" name="first_pizza_toppings[]" 
                                            id="first-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                        <label class="form-check-label" for="first-pizza-topping-{{ $topping->id }}">
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
                                        <input class="form-check-input first-pizza-topping" type="checkbox" name="first_pizza_toppings[]" 
                                            id="first-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                        <label class="form-check-label" for="first-pizza-topping-{{ $topping->id }}">
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
                                    <span>Selected: <span id="first-pizza-topping-count">0</span> of {{ $product->max_toppings }} toppings</span>
                                    <span id="first-pizza-extra-toppings-message" class="d-none">
                                        (<span id="first-pizza-extra-toppings-count">0</span> extra topping(s) will be charged)
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- First Pizza Extra Toppings option -->
                <div class="mt-4 mb-4">
                    <h5 class="fw-bold mb-3">Add Extra Toppings to First Pizza?</h5>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_first_pizza_extra_toppings" id="add-first-pizza-toppings-no" value="no" checked>
                        <label class="form-check-label" for="add-first-pizza-toppings-no">No</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_first_pizza_extra_toppings" id="add-first-pizza-toppings-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                        <label class="form-check-label" for="add-first-pizza-toppings-yes" style="color: #dc3545; font-weight: bold;">Yes</label>
                    </div>
                    <small class="d-block mt-1 text-muted">
                        @php
                            $addOns = json_decode($product->add_ons ?? '{}', true);
                            $extraToppingPrice = $addOns['extra_topping_price'] ?? 0;
                        @endphp
                        Extra toppings cost ${{ number_format($extraToppingPrice, 2) }} each
                    </small>
                    
                    <!-- Extra toppings selection (initially hidden) -->
                    <div id="first-pizza-extra-toppings-container" class="mt-3 d-none">
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <h6 class="fw-bold text-danger">Meats</h6>
                            </div>
                            @foreach($meatToppings as $topping)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input first-pizza-extra-topping" type="checkbox" name="first_pizza_extra_toppings[]" 
                                            id="first-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="first-pizza-extra-topping-{{ $topping->id }}">
                                            {{ $topping->name }}
                                            @if($topping->counts_as > 1)
                                                <span class="badge bg-danger">{{ $topping->counts_as }}x</span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="col-12 mb-2 mt-3">
                                <h6 class="fw-bold text-success">Veggies</h6>
                            </div>
                            @foreach($veggieToppings as $topping)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input first-pizza-extra-topping" type="checkbox" name="first_pizza_extra_toppings[]" 
                                            id="first-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="first-pizza-extra-topping-{{ $topping->id }}">
                                            {{ $topping->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="col-12 mb-2 mt-3">
                                <h6 class="fw-bold text-warning">Cheeses</h6>
                            </div>
                            @foreach($cheeseToppings as $topping)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input first-pizza-extra-topping" type="checkbox" name="first_pizza_extra_toppings[]" 
                                            id="first-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="first-pizza-extra-topping-{{ $topping->id }}">
                                            {{ $topping->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="col-12 mt-3">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <span>Extra toppings: <span id="first-pizza-extra-topping-count">0</span> selected</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual divider between pizzas -->
                <div class="my-5">
                    <hr>
                    <h4 class="text-center fw-bold py-3">SECOND PIZZA</h4>
                    <hr>
                </div>

                <!-- Second Pizza Toppings -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Second Pizza Toppings <small class="text-muted">(Max {{ $product->max_toppings }} toppings)</small></h5>
                    <div class="row g-2 second-pizza-toppings-container">
                        @if($meatToppings->count() > 0)
                            <div class="col-12 mb-2">
                                <h6 class="fw-bold text-danger">Meats</h6>
                            </div>
                            @foreach($meatToppings as $topping)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input second-pizza-topping" type="checkbox" name="second_pizza_toppings[]" 
                                            id="second-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                        <label class="form-check-label" for="second-pizza-topping-{{ $topping->id }}">
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
                                        <input class="form-check-input second-pizza-topping" type="checkbox" name="second_pizza_toppings[]" 
                                            id="second-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                        <label class="form-check-label" for="second-pizza-topping-{{ $topping->id }}">
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
                                        <input class="form-check-input second-pizza-topping" type="checkbox" name="second_pizza_toppings[]" 
                                            id="second-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                        <label class="form-check-label" for="second-pizza-topping-{{ $topping->id }}">
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
                                    <span>Selected: <span id="second-pizza-topping-count">0</span> of {{ $product->max_toppings }} toppings</span>
                                    <span id="second-pizza-extra-toppings-message" class="d-none">
                                        (<span id="second-pizza-extra-toppings-count">0</span> extra topping(s) will be charged)
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Second Pizza Extra Toppings option -->
                <div class="mt-4 mb-4">
                    <h5 class="fw-bold mb-3">Add Extra Toppings to Second Pizza?</h5>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_second_pizza_extra_toppings" id="add-second-pizza-toppings-no" value="no" checked>
                        <label class="form-check-label" for="add-second-pizza-toppings-no">No</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_second_pizza_extra_toppings" id="add-second-pizza-toppings-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                        <label class="form-check-label" for="add-second-pizza-toppings-yes" style="color: #dc3545; font-weight: bold;">Yes</label>
                    </div>
                    <small class="d-block mt-1 text-muted">
                        Extra toppings cost ${{ number_format($extraToppingPrice, 2) }} each
                    </small>
                    
                    <!-- Extra toppings selection for second pizza (initially hidden) -->
                    <div id="second-pizza-extra-toppings-container" class="mt-3 d-none">
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <h6 class="fw-bold text-danger">Meats</h6>
                            </div>
                            @foreach($meatToppings as $topping)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input second-pizza-extra-topping" type="checkbox" name="second_pizza_extra_toppings[]" 
                                            id="second-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="second-pizza-extra-topping-{{ $topping->id }}">
                                            {{ $topping->name }}
                                            @if($topping->counts_as > 1)
                                                <span class="badge bg-danger">{{ $topping->counts_as }}x</span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="col-12 mb-2 mt-3">
                                <h6 class="fw-bold text-success">Veggies</h6>
                            </div>
                            @foreach($veggieToppings as $topping)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input second-pizza-extra-topping" type="checkbox" name="second_pizza_extra_toppings[]" 
                                            id="second-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="second-pizza-extra-topping-{{ $topping->id }}">
                                            {{ $topping->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="col-12 mb-2 mt-3">
                                <h6 class="fw-bold text-warning">Cheeses</h6>
                            </div>
                            @foreach($cheeseToppings as $topping)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input second-pizza-extra-topping" type="checkbox" name="second_pizza_extra_toppings[]" 
                                            id="second-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="second-pizza-extra-topping-{{ $topping->id }}">
                                            {{ $topping->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="col-12 mt-3">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <span>Extra toppings: <span id="second-pizza-extra-topping-count">0</span> selected</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wing Flavors -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Wing Flavors</h5>
                    <p class="mb-2">Select your wing flavors</p>
                    
                    <select class="form-select" name="wing_flavors">
                        <option value="1">Plain</option>
                        <option value="2">Mild</option>
                        <option value="3">Medium</option>
                        <option value="4">Hot</option>
                        <option value="5">Suicide</option>
                        <option value="6">Honey Garlic</option>
                        <option value="7">BBQ</option>
                        <option value="8">Sweet & Sour</option>
                        <option value="9">Honey Hot</option>
                        <option value="10">Dry Cajun</option>
                    </select>
                </div>

                <!-- Add 3rd Pizza Option -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Add a 3rd Pizza?</h5>
                    
                    @php
                        $thirdPizzaPrice = 0;
                        if (strpos($product->name, 'Medium') !== false) {
                            $thirdPizzaPrice = 10.99;
                        } elseif (strpos($product->name, 'Large') !== false) {
                            $thirdPizzaPrice = 12.99;
                        } elseif (strpos($product->name, 'X-Large') !== false) {
                            $thirdPizzaPrice = 13.99;
                        }
                    @endphp
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_third_pizza" id="add-third-pizza-no" value="no" checked>
                        <label class="form-check-label" for="add-third-pizza-no">No</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_third_pizza" id="add-third-pizza-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                        <label class="form-check-label" for="add-third-pizza-yes" style="color: #dc3545; font-weight: bold;">Yes (+${{ number_format($thirdPizzaPrice, 2) }})</label>
                    </div>
                    
                    <!-- Third Pizza Toppings (initially hidden) -->
                    <div id="third-pizza-container" class="mt-4 d-none">
                        <div class="my-4">
                            <hr>
                            <h4 class="text-center fw-bold py-3">THIRD PIZZA</h4>
                            <hr>
                        </div>
                        
                        <h5 class="fw-bold mb-3">Third Pizza Toppings <small class="text-muted">(Max {{ $product->max_toppings }} toppings)</small></h5>
                        <div class="row g-2 third-pizza-toppings-container">
                            @if($meatToppings->count() > 0)
                                <div class="col-12 mb-2">
                                    <h6 class="fw-bold text-danger">Meats</h6>
                                </div>
                                @foreach($meatToppings as $topping)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input third-pizza-topping" type="checkbox" name="third_pizza_toppings[]" 
                                                id="third-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                data-counts-as="{{ $topping->counts_as }}"
                                                @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                            <label class="form-check-label" for="third-pizza-topping-{{ $topping->id }}">
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
                                            <input class="form-check-input third-pizza-topping" type="checkbox" name="third_pizza_toppings[]" 
                                                id="third-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                data-counts-as="{{ $topping->counts_as }}"
                                                @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                            <label class="form-check-label" for="third-pizza-topping-{{ $topping->id }}">
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
                                            <input class="form-check-input third-pizza-topping" type="checkbox" name="third_pizza_toppings[]" 
                                                id="third-pizza-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                data-counts-as="{{ $topping->counts_as }}"
                                                @if($product->max_toppings) data-max-toppings="{{ $product->max_toppings }}" @endif>
                                            <label class="form-check-label" for="third-pizza-topping-{{ $topping->id }}">
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
                                        <span>Selected: <span id="third-pizza-topping-count">0</span> of {{ $product->max_toppings }} toppings</span>
                                        <span id="third-pizza-extra-toppings-message" class="d-none">
                                            (<span id="third-pizza-extra-toppings-count">0</span> extra topping(s) will be charged)
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Third Pizza Extra Toppings option -->
                        <div class="mt-4 mb-4">
                            <h5 class="fw-bold mb-3">Add Extra Toppings to Third Pizza?</h5>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="add_third_pizza_extra_toppings" id="add-third-pizza-toppings-no" value="no" checked>
                                <label class="form-check-label" for="add-third-pizza-toppings-no">No</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="add_third_pizza_extra_toppings" id="add-third-pizza-toppings-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                                <label class="form-check-label" for="add-third-pizza-toppings-yes" style="color: #dc3545; font-weight: bold;">Yes</label>
                            </div>
                            <small class="d-block mt-1 text-muted">
                                Extra toppings cost ${{ number_format($extraToppingPrice, 2) }} each
                            </small>
                            
                            <!-- Extra toppings selection for third pizza (initially hidden) -->
                            <div id="third-pizza-extra-toppings-container" class="mt-3 d-none">
                                <div class="row g-2">
                                    <div class="col-12 mb-2">
                                        <h6 class="fw-bold text-danger">Meats</h6>
                                    </div>
                                    @foreach($meatToppings as $topping)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input third-pizza-extra-topping" type="checkbox" name="third_pizza_extra_toppings[]" 
                                                    id="third-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                    data-counts-as="{{ $topping->counts_as }}"
                                                    data-size="{{ $firstSize }}">
                                                <label class="form-check-label" for="third-pizza-extra-topping-{{ $topping->id }}">
                                                    {{ $topping->name }}
                                                    @if($topping->counts_as > 1)
                                                        <span class="badge bg-danger">{{ $topping->counts_as }}x</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <div class="col-12 mb-2 mt-3">
                                        <h6 class="fw-bold text-success">Veggies</h6>
                                    </div>
                                    @foreach($veggieToppings as $topping)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input third-pizza-extra-topping" type="checkbox" name="third_pizza_extra_toppings[]" 
                                                    id="third-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                    data-counts-as="{{ $topping->counts_as }}"
                                                    data-size="{{ $firstSize }}">
                                                <label class="form-check-label" for="third-pizza-extra-topping-{{ $topping->id }}">
                                                    {{ $topping->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <div class="col-12 mb-2 mt-3">
                                        <h6 class="fw-bold text-warning">Cheeses</h6>
                                    </div>
                                    @foreach($cheeseToppings as $topping)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input third-pizza-extra-topping" type="checkbox" name="third_pizza_extra_toppings[]" 
                                                    id="third-pizza-extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                                    data-counts-as="{{ $topping->counts_as }}"
                                                    data-size="{{ $firstSize }}">
                                                <label class="form-check-label" for="third-pizza-extra-topping-{{ $topping->id }}">
                                                    {{ $topping->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <div class="col-12 mt-3">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            <span>Extra toppings: <span id="third-pizza-extra-topping-count">0</span> selected</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pop Selection -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Pop Selection</h5>
                    <p class="mb-2">Choose your 4 included pops:</p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pop1" class="form-label">Pop 1:</label>
                            <select class="form-select" name="pop1" id="pop1">
                                <option value="Coke">Coke</option>
                                <option value="Pepsi">Pepsi</option>
                                <option value="Sprite">Sprite</option>
                                <option value="Diet Coke">Diet Coke</option>
                                <option value="Diet Pepsi">Diet Pepsi</option>
                                <option value="Dr Pepper">Dr Pepper</option>
                                <option value="Orange Crush">Orange Crush</option>
                                <option value="Cream Soda">Cream Soda</option>
                                <option value="Brisk Ice Tea">Brisk Ice Tea</option>
                                <option value="Canada Dry">Canada Dry</option>
                                <option value="Water bottle">Water bottle</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pop2" class="form-label">Pop 2:</label>
                            <select class="form-select" name="pop2" id="pop2">
                                <option value="Coke">Coke</option>
                                <option value="Pepsi">Pepsi</option>
                                <option value="Sprite">Sprite</option>
                                <option value="Diet Coke">Diet Coke</option>
                                <option value="Diet Pepsi">Diet Pepsi</option>
                                <option value="Dr Pepper">Dr Pepper</option>
                                <option value="Orange Crush">Orange Crush</option>
                                <option value="Cream Soda">Cream Soda</option>
                                <option value="Brisk Ice Tea">Brisk Ice Tea</option>
                                <option value="Canada Dry">Canada Dry</option>
                                <option value="Water bottle">Water bottle</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pop3" class="form-label">Pop 3:</label>
                            <select class="form-select" name="pop3" id="pop3">
                                <option value="Coke">Coke</option>
                                <option value="Pepsi">Pepsi</option>
                                <option value="Sprite">Sprite</option>
                                <option value="Diet Coke">Diet Coke</option>
                                <option value="Diet Pepsi">Diet Pepsi</option>
                                <option value="Dr Pepper">Dr Pepper</option>
                                <option value="Orange Crush">Orange Crush</option>
                                <option value="Cream Soda">Cream Soda</option>
                                <option value="Brisk Ice Tea">Brisk Ice Tea</option>
                                <option value="Canada Dry">Canada Dry</option>
                                <option value="Water bottle">Water bottle</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pop4" class="form-label">Pop 4:</label>
                            <select class="form-select" name="pop4" id="pop4">
                                <option value="Coke">Coke</option>
                                <option value="Pepsi">Pepsi</option>
                                <option value="Sprite">Sprite</option>
                                <option value="Diet Coke">Diet Coke</option>
                                <option value="Diet Pepsi">Diet Pepsi</option>
                                <option value="Dr Pepper">Dr Pepper</option>
                                <option value="Orange Crush">Orange Crush</option>
                                <option value="Cream Soda">Cream Soda</option>
                                <option value="Brisk Ice Tea">Brisk Ice Tea</option>
                                <option value="Canada Dry">Canada Dry</option>
                                <option value="Water bottle">Water bottle</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Garlic Bread Options -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Free Garlic Bread</h5>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_garlic_bread" id="add-garlic-bread-yes" value="yes" checked>
                        <label class="form-check-label" for="add-garlic-bread-yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_garlic_bread" id="add-garlic-bread-no" value="no">
                        <label class="form-check-label" for="add-garlic-bread-no">No</label>
                    </div>
                    
                    <div id="garlic-bread-cheese-container" class="mt-3">
                        <p class="mb-2">Add cheese to your garlic bread?</p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="add_cheese_to_garlic_bread" id="add-cheese-no" value="no" checked>
                            <label class="form-check-label" for="add-cheese-no">No ($0.00)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="add_cheese_to_garlic_bread" id="add-cheese-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                            <label class="form-check-label" for="add-cheese-yes" style="color: #dc3545; font-weight: bold;">Yes (+$1.50)</label>
                        </div>
                    </div>
                </div>

                <!-- Show included items in the combo -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Included With Your Combo</h5>
                    <div class="row g-2">
                        <!-- 2 Pizzas with toppings -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm mb-2">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>2 Pizzas with Cheese + 3 Toppings each</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Wings -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm mb-2">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>
                                            @if(strpos($product->name, 'Two Medium Pizzas Combo') !== false)
                                                2 lb Chicken Wings
                                            @else
                                                3 lb Chicken Wings
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 4 Pops -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm mb-2">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>4 Pops</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Free Garlic Bread -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm mb-2">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Free Garlic Bread</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Special Instructions -->
                <div class="mb-4 mt-4">
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
                        <span class="fs-4 fw-bold">Total: $<span id="total-price">{{ number_format($product->price, 2) }}</span></span>
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
    const maxToppings = {{ $product->max_toppings ?? 0 }};
    let firstPizzaToppingCount = 0;
    let secondPizzaToppingCount = 0;
    let thirdPizzaToppingCount = 0;
    let firstPizzaExtraToppingCount = 0;
    let secondPizzaExtraToppingCount = 0;
    let thirdPizzaExtraToppingCount = 0;
    
    // Base price from product
    let basePrice = {{ $product->price ?? 0 }};
    let extraToppingPrice = {{ $addOns['extra_topping_price'] ?? 0 }};
    
    // Get topping price by size
    function getToppingPrice(size) {
        switch(size) {
            case 'medium':
                return 1.60;
            case 'large':
                return 2.10;
            case 'xl':
                return 2.30;
            default:
                return 1.60;
        }
    }
    
    // First pizza toppings
    const firstPizzaToppings = document.querySelectorAll('.first-pizza-topping');
    const firstPizzaToppingCounter = document.getElementById('first-pizza-topping-count');
    
    firstPizzaToppings.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked && firstPizzaToppingCount >= maxToppings) {
                this.checked = false;
                alert(`You can only select up to ${maxToppings} toppings.`);
                return;
            }
            updateFirstPizzaToppingCount();
        });
    });
    
    function updateFirstPizzaToppingCount() {
        let totalCountsAs = 0;
        firstPizzaToppings.forEach(checkbox => {
            if (checkbox.checked) {
                totalCountsAs += parseInt(checkbox.dataset.countsAs || 1);
            }
        });
        
        firstPizzaToppingCount = totalCountsAs;
        firstPizzaToppingCounter.textContent = totalCountsAs;
        
        // Disable checkboxes if max reached
        if (maxToppings > 0) {
            firstPizzaToppings.forEach(checkbox => {
                if (!checkbox.checked && totalCountsAs >= maxToppings) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });
        }
    }
    
    // Second pizza toppings
    const secondPizzaToppings = document.querySelectorAll('.second-pizza-topping');
    const secondPizzaToppingCounter = document.getElementById('second-pizza-topping-count');
    
    secondPizzaToppings.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked && secondPizzaToppingCount >= maxToppings) {
                this.checked = false;
                alert(`You can only select up to ${maxToppings} toppings.`);
                return;
            }
            updateSecondPizzaToppingCount();
        });
    });
    
    function updateSecondPizzaToppingCount() {
        let totalCountsAs = 0;
        secondPizzaToppings.forEach(checkbox => {
            if (checkbox.checked) {
                totalCountsAs += parseInt(checkbox.dataset.countsAs || 1);
            }
        });
        
        secondPizzaToppingCount = totalCountsAs;
        secondPizzaToppingCounter.textContent = totalCountsAs;
        
        // Disable checkboxes if max reached
        if (maxToppings > 0) {
            secondPizzaToppings.forEach(checkbox => {
                if (!checkbox.checked && totalCountsAs >= maxToppings) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });
        }
    }
    
    // First pizza extra toppings
    const firstPizzaExtraToppings = document.querySelectorAll('.first-pizza-extra-topping');
    const firstPizzaExtraToppingCounter = document.getElementById('first-pizza-extra-topping-count');
    
    firstPizzaExtraToppings.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateFirstPizzaExtraToppingCount();
            updateTotalPrice();
        });
    });
    
    function updateFirstPizzaExtraToppingCount() {
        let totalSelected = 0;
        let totalPrice = 0;
        firstPizzaExtraToppings.forEach(checkbox => {
            if (checkbox.checked) {
                const countsAs = parseInt(checkbox.dataset.countsAs || 1);
                totalSelected += countsAs;
                totalPrice += getToppingPrice(checkbox.dataset.size) * countsAs;
            }
        });
        
        firstPizzaExtraToppingCount = totalSelected;
        firstPizzaExtraToppingCounter.textContent = totalSelected;
        return totalPrice;
    }
    
    // Second pizza extra toppings
    const secondPizzaExtraToppings = document.querySelectorAll('.second-pizza-extra-topping');
    const secondPizzaExtraToppingCounter = document.getElementById('second-pizza-extra-topping-count');
    
    secondPizzaExtraToppings.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSecondPizzaExtraToppingCount();
            updateTotalPrice();
        });
    });
    
    function updateSecondPizzaExtraToppingCount() {
        let totalSelected = 0;
        let totalPrice = 0;
        secondPizzaExtraToppings.forEach(checkbox => {
            if (checkbox.checked) {
                const countsAs = parseInt(checkbox.dataset.countsAs || 1);
                totalSelected += countsAs;
                totalPrice += getToppingPrice(checkbox.dataset.size) * countsAs;
            }
        });
        
        secondPizzaExtraToppingCount = totalSelected;
        secondPizzaExtraToppingCounter.textContent = totalSelected;
        return totalPrice;
    }
    
    // Add price indicators to the page
    const priceDisplay = document.createElement('div');
    priceDisplay.className = 'alert alert-success mt-4';
    priceDisplay.innerHTML = `
        <h5 class="mb-2">Order Summary</h5>
        <div>Base Price: $<span id="base-price">${basePrice.toFixed(2)}</span></div>
        <div class="extra-toppings-price">Extra Toppings: $<span id="extra-toppings-price">0.00</span></div>
        <div class="mt-2 fw-bold">Total: $<span id="total-price">${basePrice.toFixed(2)}</span></div>
    `;
    
    // Insert price display before the submit button
    const form = document.querySelector('form');
    form.appendChild(priceDisplay);
    
    // Total price calculation
    function updateTotalPrice() {
        const basePrice = {{ $product->price ?? 0 }};
        const basePriceElement = document.getElementById('base-price');
        const extraToppingsPriceElement = document.getElementById('extra-toppings-price');
        const totalPriceElement = document.getElementById('total-price');
        
        let extraToppingsTotal = 0;
        let addOnsTotal = 0;
        
        // Calculate extra toppings price
        if (document.getElementById('add-first-pizza-toppings-yes').checked) {
            extraToppingsTotal += updateFirstPizzaExtraToppingCount();
        }
        
        if (document.getElementById('add-second-pizza-toppings-yes').checked) {
            extraToppingsTotal += updateSecondPizzaExtraToppingCount();
        }
        
        // Add 3rd pizza price if selected
        if (document.getElementById('add-third-pizza-yes').checked) {
            let thirdPizzaPrice = {{ $thirdPizzaPrice ?? 0 }};
            addOnsTotal += thirdPizzaPrice;
            
            // Calculate extra toppings for third pizza
            if (document.getElementById('add-third-pizza-toppings-yes') && 
                document.getElementById('add-third-pizza-toppings-yes').checked) {
                extraToppingsTotal += updateThirdPizzaExtraToppingCount();
            }
        }
        
        // Add garlic bread cheese price if selected
        if (document.getElementById('add-garlic-bread-yes').checked && 
            document.getElementById('add-cheese-yes').checked) {
            addOnsTotal += 1.50; // Cheese price
        }
        
        // Update the displayed prices
        basePriceElement.textContent = basePrice.toFixed(2);
        let totalAddOns = extraToppingsTotal + addOnsTotal;
        extraToppingsPriceElement.textContent = totalAddOns.toFixed(2);
        
        const totalPrice = basePrice + totalAddOns;
        totalPriceElement.textContent = totalPrice.toFixed(2);
    }
    
    // Extra toppings toggle functionality
    const addFirstExtraToppingsYes = document.getElementById('add-first-pizza-toppings-yes');
    const addFirstExtraToppingsNo = document.getElementById('add-first-pizza-toppings-no');
    const firstPizzaExtraToppingsContainer = document.getElementById('first-pizza-extra-toppings-container');
    
    if (addFirstExtraToppingsYes && addFirstExtraToppingsNo && firstPizzaExtraToppingsContainer) {
        addFirstExtraToppingsYes.addEventListener('click', function() {
            firstPizzaExtraToppingsContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addFirstExtraToppingsNo.addEventListener('click', function() {
            firstPizzaExtraToppingsContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    const addSecondExtraToppingsYes = document.getElementById('add-second-pizza-toppings-yes');
    const addSecondExtraToppingsNo = document.getElementById('add-second-pizza-toppings-no');
    const secondPizzaExtraToppingsContainer = document.getElementById('second-pizza-extra-toppings-container');
    
    if (addSecondExtraToppingsYes && addSecondExtraToppingsNo && secondPizzaExtraToppingsContainer) {
        addSecondExtraToppingsYes.addEventListener('click', function() {
            secondPizzaExtraToppingsContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addSecondExtraToppingsNo.addEventListener('click', function() {
            secondPizzaExtraToppingsContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    // Initialize topping counts
    updateFirstPizzaToppingCount();
    updateSecondPizzaToppingCount();
    updateTotalPrice();
    
    // Third pizza toggle functionality
    const addThirdPizzaYes = document.getElementById('add-third-pizza-yes');
    const addThirdPizzaNo = document.getElementById('add-third-pizza-no');
    const thirdPizzaContainer = document.getElementById('third-pizza-container');

    if (addThirdPizzaYes && addThirdPizzaNo && thirdPizzaContainer) {
        addThirdPizzaYes.addEventListener('click', function() {
            thirdPizzaContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addThirdPizzaNo.addEventListener('click', function() {
            thirdPizzaContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    // Third pizza toppings
    const thirdPizzaToppings = document.querySelectorAll('.third-pizza-topping');
    const thirdPizzaToppingCounter = document.getElementById('third-pizza-topping-count');
    
    thirdPizzaToppings.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked && thirdPizzaToppingCount >= maxToppings) {
                this.checked = false;
                alert(`You can only select up to ${maxToppings} toppings.`);
                return;
            }
            updateThirdPizzaToppingCount();
        });
    });
    
    function updateThirdPizzaToppingCount() {
        let totalCountsAs = 0;
        thirdPizzaToppings.forEach(checkbox => {
            if (checkbox.checked) {
                totalCountsAs += parseInt(checkbox.dataset.countsAs || 1);
            }
        });
        
        thirdPizzaToppingCount = totalCountsAs;
        thirdPizzaToppingCounter.textContent = totalCountsAs;
        
        // Disable checkboxes if max reached
        if (maxToppings > 0) {
            thirdPizzaToppings.forEach(checkbox => {
                if (!checkbox.checked && totalCountsAs >= maxToppings) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });
        }
    }
    
    // Third pizza extra toppings
    const thirdPizzaExtraToppings = document.querySelectorAll('.third-pizza-extra-topping');
    const thirdPizzaExtraToppingCounter = document.getElementById('third-pizza-extra-topping-count');
    
    thirdPizzaExtraToppings.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateThirdPizzaExtraToppingCount();
            updateTotalPrice();
        });
    });
    
    function updateThirdPizzaExtraToppingCount() {
        let totalSelected = 0;
        let totalPrice = 0;
        thirdPizzaExtraToppings.forEach(checkbox => {
            if (checkbox.checked) {
                const countsAs = parseInt(checkbox.dataset.countsAs || 1);
                totalSelected += countsAs;
                totalPrice += getToppingPrice(checkbox.dataset.size) * countsAs;
            }
        });
        
        thirdPizzaExtraToppingCount = totalSelected;
        thirdPizzaExtraToppingCounter.textContent = totalSelected;
        return totalPrice;
    }
    
    // Third pizza extra toppings toggle
    const addThirdExtraToppingsYes = document.getElementById('add-third-pizza-toppings-yes');
    const addThirdExtraToppingsNo = document.getElementById('add-third-pizza-toppings-no');
    const thirdPizzaExtraToppingsContainer = document.getElementById('third-pizza-extra-toppings-container');
    
    if (addThirdExtraToppingsYes && addThirdExtraToppingsNo && thirdPizzaExtraToppingsContainer) {
        addThirdExtraToppingsYes.addEventListener('click', function() {
            thirdPizzaExtraToppingsContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addThirdExtraToppingsNo.addEventListener('click', function() {
            thirdPizzaExtraToppingsContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    // Garlic bread toggle functionality
    const addGarlicBreadYes = document.getElementById('add-garlic-bread-yes');
    const addGarlicBreadNo = document.getElementById('add-garlic-bread-no');
    const garlicBreadCheeseContainer = document.getElementById('garlic-bread-cheese-container');
    
    if (addGarlicBreadYes && addGarlicBreadNo && garlicBreadCheeseContainer) {
        addGarlicBreadYes.addEventListener('click', function() {
            garlicBreadCheeseContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addGarlicBreadNo.addEventListener('click', function() {
            garlicBreadCheeseContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    // Garlic bread cheese toggle functionality
    const addCheeseYes = document.getElementById('add-cheese-yes');
    const addCheeseNo = document.getElementById('add-cheese-no');
    
    if (addCheeseYes && addCheeseNo) {
        addCheeseYes.addEventListener('click', function() {
            updateTotalPrice();
        });
        
        addCheeseNo.addEventListener('click', function() {
            updateTotalPrice();
        });
    }
});
</script>
@endpush 