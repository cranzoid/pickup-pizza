@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Checkout</h5>
                </div>
                <div class="card-body">
                    <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}">
                        @csrf
                        
                        <!-- Pickup Time -->
                        <div id="pickup-section" class="mb-4">
                            <h5>Pickup Time</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pickup_date" class="form-label">Date</label>
                                        <select class="form-select" id="pickup_date" name="pickup_date" required>
                                            @foreach($availableDates as $date)
                                                <option value="{{ $date['value'] }}" {{ $date['is_today'] ? 'selected' : '' }}>
                                                    {{ $date['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pickup_time" class="form-label">Time</label>
                                        <select class="form-select" id="pickup_time" name="pickup_time" required>
                                            @if(count($availableTimes) > 0)
                                                @foreach($availableTimes as $time)
                                                    <option value="{{ $time }}">{{ \App\Helpers\PickupTimeHelper::formatTime($time) }}</option>
                                                @endforeach
                                            @else
                                                <option value="">No times available</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="mb-4">
                            <h5>Contact Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="mb-4">
                            <h5>Payment Method</h5>
                            
                            @if(!$onlinePaymentEnabled && !$payAtPickupEnabled)
                                <div class="alert alert-warning">
                                    No payment methods are currently available. Please try again later.
                                </div>
                            @else
                                <div class="mb-3 form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" {{ $onlinePaymentEnabled ? '' : 'disabled' }} {{ old('payment_method') === 'credit_card' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="credit_card">
                                        <i class="fab fa-cc-visa me-1"></i> 
                                        <i class="fab fa-cc-mastercard me-1"></i>
                                        <i class="fab fa-cc-amex me-1"></i>
                                        Pay Now
                                    </label>
                                </div>
                                <div class="mb-3 form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_in_store" value="pay_in_store" {{ $payAtPickupEnabled ? '' : 'disabled' }} {{ old('payment_method', 'pay_in_store') === 'pay_in_store' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pay_in_store">
                                        <i class="fas fa-money-bill-wave me-1"></i> Pay at Pickup
                                    </label>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Stripe Elements will replace this -->
                        <div id="credit-card-form" class="d-none">
                            <div class="mb-3">
                                <label for="card-element" class="form-label">Credit or debit card</label>
                                <div id="card-element" class="form-control"></div>
                                <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                            </div>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" id="submit-button" class="btn btn-primary btn-lg">
                                <i class="fas fa-pizza-slice me-2"></i>Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    @php
                        $cartItems = session()->get('cart', []);
                        $cartTotal = 0;
                        foreach ($cartItems as $item) {
                            $cartTotal += $item['unit_price'] * $item['quantity'];
                        }
                        
                        $settings = new \App\Models\Setting();
                        $taxRate = $settings->get('tax_rate', 13);
                        $taxEnabled = $settings->get('tax_enabled', true);
                        $taxAmount = $taxEnabled ? ($cartTotal * $taxRate / 100) : 0;
                        
                        $discountAmount = session()->has('discount') ? session('discount.amount') : 0;
                        
                        $orderTotal = $cartTotal + $taxAmount - $discountAmount;
                    @endphp
                    
                    @if(count($cartItems) > 0)
                        <div class="order-items mb-3">
                            @foreach($cartItems as $index => $item)
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <span class="fw-bold">{{ $item['quantity'] }}x</span> {{ $item['name'] }}
                                        @if($item['size'])
                                            <span class="text-muted">({{ ucfirst($item['size']) }})</span>
                                        @endif
                                    </div>
                                    <div>${{ number_format($item['unit_price'] * $item['quantity'], 2) }}</div>
                                </div>
                            @endforeach
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span>${{ number_format($cartTotal, 2) }}</span>
                        </div>
                        
                        @if($taxEnabled)
                            <div class="d-flex justify-content-between mb-3">
                                <span>{{ $taxName }} ({{ $taxRate }}%):</span>
                                <span>${{ number_format($taxAmount, 2) }}</span>
                            </div>
                        @endif
                        
                        @if(session()->has('discount'))
                            <div class="d-flex justify-content-between mb-2">
                                <div>Discount:</div>
                                <div>-${{ number_format($discountAmount, 2) }}</div>
                            </div>
                        @endif
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between fw-bold mt-2 pt-2 border-top">
                            <div>Total:</div>
                            <div id="order-total">${{ number_format($orderTotal, 2) }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left me-2"></i>Return to Cart
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                            <p>Your cart is empty</p>
                            <a href="{{ route('menu.index') }}" class="btn btn-primary">Browse Menu</a>
                        </div>
                    @endif

                    <!-- Business Hours -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">Business Hours</h5>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-unstyled mb-0">
                                @php
                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                    $today = strtolower(date('l'));
                                @endphp
                                
                                @foreach($days as $day)
                                    @php
                                        $isOpen = $settings->get($day . '_open', true);
                                        $openTime = $settings->get($day . '_from', '11:00');
                                        $closeTime = $settings->get($day . '_to', '22:00');
                                        $isToday = ($day === $today);
                                    @endphp
                                    <li class="mb-2 {{ $isToday ? 'fw-bold' : '' }}">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ ucfirst($day) }}{{ $isToday ? ' (Today)' : '' }}</span>
                                            @if($isOpen)
                                                <span>{{ \App\Helpers\PickupTimeHelper::formatTime($openTime) }} - {{ \App\Helpers\PickupTimeHelper::formatTime($closeTime) }}</span>
                                            @else
                                                <span class="text-danger">Closed</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0">Order Notes</h5>
                        </div>
                        <div class="card-body p-3">
                            <textarea class="form-control" id="order_notes" name="order_notes" rows="3" placeholder="Add any special instructions here"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Stripe
        const stripe = Stripe('{{ $stripePublicKey }}');
        const elements = stripe.elements();
        
        // Create card element
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    color: '#dc3545',
                    iconColor: '#dc3545',
                },
            },
        });
        
        // Mount the card Element
        cardElement.mount('#card-element');
        
        // Handle validation errors
        cardElement.addEventListener('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Handle payment method toggle
        const creditCardRadio = document.getElementById('credit_card');
        const payInStoreRadio = document.getElementById('pay_in_store');
        const creditCardForm = document.getElementById('credit-card-form');
        const submitButton = document.getElementById('submit-button');
        const checkoutForm = document.getElementById('checkout-form');
        
        function updatePaymentMethod() {
            if (creditCardRadio && creditCardRadio.checked) {
                if (creditCardForm) creditCardForm.classList.remove('d-none');
            } else {
                if (creditCardForm) creditCardForm.classList.add('d-none');
            }
        }
        
        // Add event listeners only if elements exist
        if (creditCardRadio) creditCardRadio.addEventListener('change', updatePaymentMethod);
        if (payInStoreRadio) payInStoreRadio.addEventListener('change', updatePaymentMethod);
        
        // Initialize the form
        updatePaymentMethod();
        
        // Handle form submission
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(event) {
                if (creditCardRadio && creditCardRadio.checked) {
                    event.preventDefault();
                    
                    // Disable the submit button to prevent multiple submissions
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    
                    // Create payment method and submit the form
                    stripe.createPaymentMethod({
                        type: 'card',
                        card: cardElement,
                        billing_details: {
                            name: document.getElementById('name').value,
                            email: document.getElementById('email').value,
                            phone: document.getElementById('phone').value
                        }
                    }).then(function(result) {
                        if (result.error) {
                            // Show error
                            const errorElement = document.getElementById('card-errors');
                            errorElement.textContent = result.error.message;
                            
                            // Re-enable the submit button
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fas fa-pizza-slice me-2"></i>Place Order';
                        } else {
                            // Create a hidden input with the payment method ID
                            const hiddenInput = document.createElement('input');
                            hiddenInput.setAttribute('type', 'hidden');
                            hiddenInput.setAttribute('name', 'payment_method_id');
                            hiddenInput.setAttribute('value', result.paymentMethod.id);
                            checkoutForm.appendChild(hiddenInput);
                            
                            // Submit the form
                            checkoutForm.submit();
                        }
                    });
                }
            });
        }

        // Handle date change to update pickup times
        const pickupDateSelect = document.getElementById('pickup_date');
        const pickupTimeSelect = document.getElementById('pickup_time');
        
        // Function to update pickup times
        function updatePickupTimes(selectedDate) {
            // Disable the time select while loading
            pickupTimeSelect.disabled = true;
            pickupTimeSelect.innerHTML = '<option value="">Loading...</option>';
            
            // Fetch available times for the selected date
            fetch('{{ route('checkout.pickup-times') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ date: selectedDate })
            })
            .then(response => response.json())
            .then(data => {
                // Clear existing options
                pickupTimeSelect.innerHTML = '';
                
                if (data.length > 0) {
                    // Add new options
                    data.forEach(time => {
                        const option = document.createElement('option');
                        option.value = time.value;
                        option.textContent = time.label;
                        pickupTimeSelect.appendChild(option);
                    });
                    
                    // Re-enable the select
                    pickupTimeSelect.disabled = false;
                    
                    // Check if submit button should be enabled
                    document.getElementById('submit-button').disabled = false;
                } else {
                    // No times available
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No times available';
                    pickupTimeSelect.appendChild(option);
                    pickupTimeSelect.disabled = true;
                    
                    // Disable the submit button if no times available
                    document.getElementById('submit-button').disabled = true;
                    
                    // Show a warning message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-warning mt-2';
                    alertDiv.id = 'no-times-warning';
                    alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>No pickup times available for this date. Please select another date.';
                    
                    // Remove any existing alert
                    const existingAlert = document.getElementById('no-times-warning');
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                    
                    // Add the alert after the pickup time select
                    pickupTimeSelect.parentNode.appendChild(alertDiv);
                }
            })
            .catch(error => {
                console.error('Error fetching pickup times:', error);
                pickupTimeSelect.innerHTML = '<option value="">Error loading times</option>';
                pickupTimeSelect.disabled = true;
            });
        }
        
        // Initial load of pickup times
        if (pickupDateSelect && pickupTimeSelect) {
            // Load times for the initial selected date
            updatePickupTimes(pickupDateSelect.value);
            
            // Add event listener for date changes
            pickupDateSelect.addEventListener('change', function() {
                // Remove any existing alert
                const existingAlert = document.getElementById('no-times-warning');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                updatePickupTimes(this.value);
            });
        }
    });
</script>
@endpush
@endsection 