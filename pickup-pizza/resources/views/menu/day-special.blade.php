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
        
        <!-- Product Details and Customization Form -->
        <div class="col-md-7">
            <h1 class="fw-bold mb-3">{{ $product->name }}</h1>
            <p class="lead mb-4">{{ $product->description }}</p>
            
            <form action="{{ route('cart.add') }}" method="POST" id="product-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                @if($product->has_toppings && $product->is_pizza)
                <!-- Pizza Toppings -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Pizza Toppings <small class="text-muted">(Max {{ $product->max_toppings }} toppings)</small></h5>
                    <div class="row g-2">
                        @php
                            $meatToppings = $toppings->where('category', 'meat');
                            $veggieToppings = $toppings->where('category', 'veggie');
                            $cheeseToppings = $toppings->where('category', 'cheese');
                            $preselected = isset($preselectedToppings) ? $preselectedToppings : [];
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
                                            @if(in_array($topping->id, $preselected)) checked @endif
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
                                            @if(in_array($topping->id, $preselected)) checked @endif
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
                                            @if(in_array($topping->id, $preselected)) checked @endif
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
                                    <span>Selected: <span id="topping-count">{{ count($preselected) }}</span> of {{ $product->max_toppings }} toppings</span>
                                    <span id="extra-toppings-message" class="d-none">
                                        (<span id="extra-toppings-count">0</span> extra topping(s) will be charged)
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Extra Toppings option -->
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
                    <small class="d-block mt-1 text-muted">
                        @php
                            $extraToppingPrice = $product->getExtraToppingPrice($firstSize);
                        @endphp
                        Extra toppings cost ${{ number_format($extraToppingPrice, 2) }} each
                    </small>
                    
                    <!-- Extra toppings selection (initially hidden) -->
                    <div id="extra-toppings-container" class="mt-3 d-none">
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <h6 class="fw-bold text-danger">Meats</h6>
                            </div>
                            @foreach($meatToppings as $topping)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input extra-topping" type="checkbox" name="extra_toppings[]" 
                                            id="extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="extra-topping-{{ $topping->id }}">
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
                                        <input class="form-check-input extra-topping" type="checkbox" name="extra_toppings[]" 
                                            id="extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="extra-topping-{{ $topping->id }}">
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
                                        <input class="form-check-input extra-topping" type="checkbox" name="extra_toppings[]" 
                                            id="extra-topping-{{ $topping->id }}" value="{{ $topping->id }}"
                                            data-counts-as="{{ $topping->counts_as }}"
                                            data-size="{{ $firstSize }}">
                                        <label class="form-check-label" for="extra-topping-{{ $topping->id }}">
                                            {{ $topping->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="col-12 mt-3">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <span>Extra toppings: <span id="extra-topping-count">0</span> selected</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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
    let toppingCount = {{ isset($preselectedToppings) ? count($preselectedToppings) : 0 }};
    let extraToppingCount = 0;
    
    // Base price from product
    let basePrice = {{ $product->price ?? 0 }};
    let extraToppingPrice = {{ $product->getExtraToppingPrice($firstSize ?? 'medium') }};
    
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
    
    // Extra toppings
    const extraToppings = document.querySelectorAll('.extra-topping');
    const extraToppingCounter = document.getElementById('extra-topping-count');
    
    extraToppings.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateExtraToppingCount();
            updateTotalPrice();
        });
    });
    
    function updateExtraToppingCount() {
        let totalSelected = 0;
        let totalPrice = 0;
        extraToppings.forEach(checkbox => {
            if (checkbox.checked) {
                const countsAs = parseInt(checkbox.dataset.countsAs || 1);
                totalSelected += countsAs;
                totalPrice += extraToppingPrice * countsAs;
            }
        });
        
        extraToppingCount = totalSelected;
        extraToppingCounter.textContent = totalSelected;
        return totalPrice;
    }
    
    // Extra toppings toggle functionality
    const addExtraToppingsYes = document.getElementById('add-extra-toppings-yes');
    const addExtraToppingsNo = document.getElementById('add-extra-toppings-no');
    const extraToppingsContainer = document.getElementById('extra-toppings-container');
    
    if (addExtraToppingsYes && addExtraToppingsNo && extraToppingsContainer) {
        addExtraToppingsYes.addEventListener('click', function() {
            extraToppingsContainer.classList.remove('d-none');
            updateTotalPrice();
        });
        
        addExtraToppingsNo.addEventListener('click', function() {
            extraToppingsContainer.classList.add('d-none');
            updateTotalPrice();
        });
    }
    
    // Total price calculation
    function updateTotalPrice() {
        const totalPriceElement = document.getElementById('total-price');
        
        let totalPrice = basePrice;
        let extraToppingsTotal = 0;
        
        // Calculate extra toppings price
        if (addExtraToppingsYes && addExtraToppingsYes.checked) {
            extraToppingsTotal = updateExtraToppingCount();
        }
        
        // Update the displayed price
        totalPrice += extraToppingsTotal;
        totalPriceElement.textContent = totalPrice.toFixed(2);
    }
    
    // Initialize
    updateToppingCount();
    updateTotalPrice();
    
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