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
                
                <!-- Size Selection -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Size</h5>
                    
                    @php
                        $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
                    @endphp
                    
                    <div class="row g-2">
                        @foreach($sizes as $size => $details)
                            <div class="col-md-4 col-6">
                                <div class="form-check size-option">
                                    <input class="form-check-input" type="radio" name="size" id="size-{{ $size }}" 
                                        value="{{ $size }}" data-price="{{ $details['price'] }}" 
                                        data-wings="{{ is_array($details) ? $details['wings'] : '' }}"
                                        {{ $size == $firstSize ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="size-{{ $size }}">
                                        <div class="card h-100">
                                            <div class="card-body py-2 px-3">
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-capitalize">{{ $size }}</span>
                                                    <span class="fw-bold">${{ number_format($details['price'], 2) }}</span>
                                                </div>
                                                <div class="wings-info">
                                                    <small class="text-muted">
                                                        Wings: {{ is_array($details) ? $details['wings'] : '12' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Pizza Toppings -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Pizza Toppings <small class="text-muted">(Max {{ $product->max_toppings }} toppings)</small></h5>
                    <div class="row g-2 pizza-toppings-container">
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
                                        <input class="form-check-input pizza-topping" type="checkbox" name="toppings[]" 
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
                                        <input class="form-check-input pizza-topping" type="checkbox" name="toppings[]" 
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
                                        <input class="form-check-input pizza-topping" type="checkbox" name="toppings[]" 
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
                                </div>
                            </div>
                        @endif
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
                
                <!-- Add 2nd Pizza Option (only for Jumbo) -->
                <div class="mb-4 mt-5 d-none" id="second-pizza-option">
                    <h5 class="fw-bold mb-3">Add a 2nd Pizza?</h5>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_second_pizza" id="add-second-pizza-no" value="no" checked>
                        <label class="form-check-label" for="add-second-pizza-no">No</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_second_pizza" id="add-second-pizza-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                        <label class="form-check-label" for="add-second-pizza-yes" style="color: #dc3545; font-weight: bold;">Yes (+$15.99)</label>
                    </div>
                    
                    <!-- Second Pizza Toppings (initially hidden) -->
                    <div id="second-pizza-container" class="mt-4 d-none">
                        <div class="my-4">
                            <hr>
                            <h4 class="text-center fw-bold py-3">SECOND PIZZA</h4>
                            <hr>
                        </div>
                        
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
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Extra Wings Option -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Add Extra Wings?</h5>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_extra_wings" id="add-extra-wings-no" value="no" checked>
                        <label class="form-check-label" for="add-extra-wings-no">No</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_extra_wings" id="add-extra-wings-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                        <label class="form-check-label" for="add-extra-wings-yes" style="color: #dc3545; font-weight: bold;">Yes (+$10.49/lb)</label>
                    </div>
                    
                    <div id="extra-wings-container" class="mt-3 d-none">
                        <div class="input-group" style="width: 150px;">
                            <button type="button" class="btn btn-outline-secondary decrease-wings">-</button>
                            <input type="number" name="extra_wings_quantity" class="form-control text-center" value="1" min="1" max="5" id="extra-wings-quantity">
                            <button type="button" class="btn btn-outline-secondary increase-wings">+</button>
                        </div>
                        <small class="d-block mt-1 text-muted">1 lb = 12 wings</small>
                    </div>
                </div>
                
                <!-- Add Pop Option -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Add Pop?</h5>
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_pop" id="add-pop-no" value="no" checked>
                        <label class="form-check-label" for="add-pop-no">No</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="add_pop" id="add-pop-yes" value="yes" style="background-color: #dc3545; border-color: #dc3545;">
                        <label class="form-check-label" for="add-pop-yes" style="color: #dc3545; font-weight: bold;">Yes (+$4.99)</label>
                    </div>
                    
                    <div id="pop-selection-container" class="mt-3 d-none">
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
                </div>
                
                <!-- Show included items in the combo -->
                <div class="mb-4 mt-5">
                    <h5 class="fw-bold mb-3">Included With Your Combo</h5>
                    <div class="row g-2">
                        <!-- 1 Pizza with toppings -->
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
                        
                        <!-- Wings -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm mb-2">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span id="included-wings">
                                            12 Wings
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Veggie Sticks & Blue Cheese -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm mb-2">
                                <div class="card-body py-2 px-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Veggie Sticks & Blue Cheese</span>
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
                        <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="10" id="quantity-input">
                        <button type="button" class="btn btn-outline-secondary increase-qty">+</button>
                    </div>
                </div>
                
                <!-- Price and Add to Cart -->
                <div class="d-flex align-items-center justify-content-between mt-4">
                    <div>
                        <span class="fs-4 fw-bold">Total: $<span id="total-price">{{ isset($sizes[$firstSize]['price']) ? number_format($sizes[$firstSize]['price'], 2) : number_format($product->price, 2) }}</span></span>
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
    let toppingCount = 0;
    let secondPizzaToppingCount = 0;
    
    // Get initial size details
    let currentSize = document.querySelector('input[name="size"]:checked').value;
    let basePrice = parseFloat(document.querySelector('input[name="size"]:checked').dataset.price);
    let wingsInfo = document.querySelector('input[name="size"]:checked').dataset.wings;
    
    // Size selection changes
    const sizeOptions = document.querySelectorAll('input[name="size"]');
    sizeOptions.forEach(radio => {
        radio.addEventListener('change', function() {
            currentSize = this.value;
            basePrice = parseFloat(this.dataset.price);
            wingsInfo = this.dataset.wings;
            
            // Update wing info display
            document.getElementById('included-wings').textContent = wingsInfo;
            
            // Show/hide second pizza option for Jumbo size only
            if (currentSize === 'jumbo') {
                document.getElementById('second-pizza-option').classList.remove('d-none');
            } else {
                document.getElementById('second-pizza-option').classList.add('d-none');
                document.getElementById('add-second-pizza-no').checked = true;
                document.getElementById('second-pizza-container').classList.add('d-none');
            }
            
            updateTotalPrice();
        });
    });
    
    // Set initial wings info display
    document.getElementById('included-wings').textContent = wingsInfo;
    
    // Show/hide second pizza option for Jumbo size only on page load
    if (currentSize === 'jumbo') {
        document.getElementById('second-pizza-option').classList.remove('d-none');
    }
    
    // Pizza toppings
    const pizzaToppings = document.querySelectorAll('.pizza-topping');
    const toppingCounter = document.getElementById('topping-count');
    
    pizzaToppings.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked && toppingCount >= maxToppings) {
                this.checked = false;
                alert(`You can only select up to ${maxToppings} toppings.`);
                return;
            }
            updateToppingCount();
        });
    });
    
    function updateToppingCount() {
        let totalCountsAs = 0;
        pizzaToppings.forEach(checkbox => {
            if (checkbox.checked) {
                totalCountsAs += parseInt(checkbox.dataset.countsAs || 1);
            }
        });
        
        toppingCount = totalCountsAs;
        toppingCounter.textContent = totalCountsAs;
        
        // Disable checkboxes if max reached
        if (maxToppings > 0) {
            pizzaToppings.forEach(checkbox => {
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
    
    // Second pizza toggle functionality
    const addSecondPizzaYes = document.getElementById('add-second-pizza-yes');
    const addSecondPizzaNo = document.getElementById('add-second-pizza-no');
    const secondPizzaContainer = document.getElementById('second-pizza-container');

    if (addSecondPizzaYes && addSecondPizzaNo && secondPizzaContainer) {
        addSecondPizzaYes.addEventListener('click', function() {
            secondPizzaContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addSecondPizzaNo.addEventListener('click', function() {
            secondPizzaContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    // Extra wings toggle functionality
    const addExtraWingsYes = document.getElementById('add-extra-wings-yes');
    const addExtraWingsNo = document.getElementById('add-extra-wings-no');
    const extraWingsContainer = document.getElementById('extra-wings-container');
    
    if (addExtraWingsYes && addExtraWingsNo && extraWingsContainer) {
        addExtraWingsYes.addEventListener('click', function() {
            extraWingsContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addExtraWingsNo.addEventListener('click', function() {
            extraWingsContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    // Extra wings quantity controls
    const decreaseWingsBtn = document.querySelector('.decrease-wings');
    const increaseWingsBtn = document.querySelector('.increase-wings');
    const extraWingsQuantityInput = document.getElementById('extra-wings-quantity');
    
    if (decreaseWingsBtn && increaseWingsBtn && extraWingsQuantityInput) {
        decreaseWingsBtn.addEventListener('click', function() {
            if (parseInt(extraWingsQuantityInput.value) > 1) {
                extraWingsQuantityInput.value = parseInt(extraWingsQuantityInput.value) - 1;
                updateTotalPrice();
            }
        });
        
        increaseWingsBtn.addEventListener('click', function() {
            if (parseInt(extraWingsQuantityInput.value) < 5) {
                extraWingsQuantityInput.value = parseInt(extraWingsQuantityInput.value) + 1;
                updateTotalPrice();
            }
        });
        
        extraWingsQuantityInput.addEventListener('change', function() {
            updateTotalPrice();
        });
    }
    
    // Pop toggle functionality
    const addPopYes = document.getElementById('add-pop-yes');
    const addPopNo = document.getElementById('add-pop-no');
    const popSelectionContainer = document.getElementById('pop-selection-container');
    
    if (addPopYes && addPopNo && popSelectionContainer) {
        addPopYes.addEventListener('click', function() {
            popSelectionContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addPopNo.addEventListener('click', function() {
            popSelectionContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    // Quantity controls
    const decreaseQtyBtn = document.querySelector('.decrease-qty');
    const increaseQtyBtn = document.querySelector('.increase-qty');
    const quantityInput = document.getElementById('quantity-input');
    
    if (decreaseQtyBtn && increaseQtyBtn && quantityInput) {
        decreaseQtyBtn.addEventListener('click', function() {
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
                updateTotalPrice();
            }
        });
        
        increaseQtyBtn.addEventListener('click', function() {
            if (parseInt(quantityInput.value) < 10) {
                quantityInput.value = parseInt(quantityInput.value) + 1;
                updateTotalPrice();
            }
        });
        
        quantityInput.addEventListener('change', function() {
            updateTotalPrice();
        });
    }
    
    // Total price calculation
    function updateTotalPrice() {
        let total = basePrice;
        
        // Add second pizza price if selected
        if (document.getElementById('add-second-pizza-yes') && 
            document.getElementById('add-second-pizza-yes').checked) {
            total += 15.99;
        }
        
        // Add extra wings price if selected
        if (document.getElementById('add-extra-wings-yes') && 
            document.getElementById('add-extra-wings-yes').checked) {
            const extraWingsQty = parseInt(document.getElementById('extra-wings-quantity').value);
            total += (10.49 * extraWingsQty);
        }
        
        // Add pop price if selected
        if (document.getElementById('add-pop-yes') && 
            document.getElementById('add-pop-yes').checked) {
            total += 4.99;
        }
        
        // Multiply by quantity
        const quantity = parseInt(document.getElementById('quantity-input').value);
        total *= quantity;
        
        // Update the displayed price
        document.getElementById('total-price').textContent = total.toFixed(2);
    }
    
    // Initialize counters and price
    updateToppingCount();
    updateSecondPizzaToppingCount();
    updateTotalPrice();
});
</script>
@endpush 