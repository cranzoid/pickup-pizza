@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>System Settings</h1>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <!-- Business Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-store me-2"></i>Business Information
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="business">
                        
                        <div class="mb-3">
                            <label for="business_name" class="form-label">Business Name</label>
                            <input type="text" class="form-control" id="business_name" name="settings[business_name]" value="{{ $settings['business_name'] ?? 'PISA Pizza' }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_email" class="form-label">Business Email</label>
                            <input type="email" class="form-control" id="business_email" name="settings[business_email]" value="{{ $settings['business_email'] ?? '' }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_phone" class="form-label">Business Phone</label>
                            <input type="text" class="form-control" id="business_phone" name="settings[business_phone]" value="{{ $settings['business_phone'] ?? '' }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="business_address" class="form-label">Business Address</label>
                            <textarea class="form-control" id="business_address" name="settings[business_address]" rows="3">{{ $settings['business_address'] ?? '' }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Business Information
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Order Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Order Settings
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="orders">
                        
                        <div class="mb-3">
                            <label for="min_pickup_time" class="form-label">Minimum Pickup Time (minutes)</label>
                            <input type="number" class="form-control" id="min_pickup_time" name="settings[min_pickup_time]" value="{{ $settings['min_pickup_time'] ?? 30 }}" min="0">
                            <div class="form-text">Minimum time customers must select for pickup from current time</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="max_future_days" class="form-label">Maximum Future Ordering (days)</label>
                            <input type="number" class="form-control" id="max_future_days" name="settings[max_future_days]" value="{{ $settings['max_future_days'] ?? 7 }}" min="1">
                            <div class="form-text">How many days in advance customers can place orders</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="pickup_interval" class="form-label">Pickup Time Interval (minutes)</label>
                            <select class="form-select" id="pickup_interval" name="settings[pickup_interval]">
                                <option value="15" {{ ($settings['pickup_interval'] ?? 15) == 15 ? 'selected' : '' }}>15 minutes</option>
                                <option value="30" {{ ($settings['pickup_interval'] ?? 15) == 30 ? 'selected' : '' }}>30 minutes</option>
                                <option value="60" {{ ($settings['pickup_interval'] ?? 15) == 60 ? 'selected' : '' }}>60 minutes</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Order Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- Tax Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-percent me-2"></i>Tax Settings
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="tax">
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="tax_enabled" name="settings[tax_enabled]" value="1" {{ $settings['tax_enabled'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="tax_enabled">Enable Tax</label>
                            </div>
                            <div class="form-text">Turn on/off tax calculation at checkout</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control" id="tax_rate" name="settings[tax_rate]" value="{{ $settings['tax_rate'] ?? 8.5 }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="tax_name" class="form-label">Tax Name</label>
                            <input type="text" class="form-control" id="tax_name" name="settings[tax_name]" value="{{ $settings['tax_name'] ?? 'Tax' }}">
                            <div class="form-text">Display name for tax (e.g., "Sales Tax", "GST", etc.)</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Tax Settings
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Payment Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card me-2"></i>Payment Settings
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="payment">
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="online_payment_enabled" name="settings[online_payment_enabled]" value="1" {{ $settings['online_payment_enabled'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="online_payment_enabled">Enable Online Payments</label>
                            </div>
                            <div class="form-text">Allow customers to pay online via Stripe</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="pay_at_pickup_enabled" name="settings[pay_at_pickup_enabled]" value="1" {{ $settings['pay_at_pickup_enabled'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="pay_at_pickup_enabled">Enable Pay at Pickup</label>
                            </div>
                            <div class="form-text">Allow customers to pay when they pickup their order</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stripe Credentials</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Public Key</span>
                                <input type="text" class="form-control" id="stripe_public_key" name="settings[stripe_public_key]" value="{{ $settings['stripe_public_key'] ?? '' }}">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Secret Key</span>
                                <input type="password" class="form-control" id="stripe_secret_key" name="settings[stripe_secret_key]" value="{{ $settings['stripe_secret_key'] ?? '' }}">
                            </div>
                            <div class="form-text">Your Stripe API keys for payment processing</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Payment Settings
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Business Hours -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Business Hours
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="hours">
                        
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-capitalize">{{ $day }}</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input day-toggle" type="checkbox" id="{{ $day }}_open" 
                                           name="settings[{{ $day }}_open]" value="1" 
                                           data-day="{{ $day }}"
                                           {{ ($settings[$day . '_open'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $day }}_open">Open</label>
                                </div>
                            </div>
                            <div class="col-md-6 hours-inputs {{ ($settings[$day . '_open'] ?? true) ? '' : 'd-none' }}" id="{{ $day }}_hours">
                                <div class="input-group input-group-sm">
                                    <input type="time" class="form-control" name="settings[{{ $day }}_from]" value="{{ $settings[$day . '_from'] ?? '11:00' }}">
                                    <span class="input-group-text">to</span>
                                    <input type="time" class="form-control" name="settings[{{ $day }}_to]" value="{{ $settings[$day . '_to'] ?? '22:00' }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Business Hours
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle day open/closed
    const dayToggles = document.querySelectorAll('.day-toggle');
    dayToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const day = this.dataset.day;
            const hoursElement = document.getElementById(day + '_hours');
            
            if (this.checked) {
                hoursElement.classList.remove('d-none');
            } else {
                hoursElement.classList.add('d-none');
            }
        });
    });
});
</script>
@endpush

@endsection 