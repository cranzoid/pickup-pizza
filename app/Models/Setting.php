<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public static function getDefaultSettings()
    {
        return [
            // Business information
            'business_name' => 'PISA Pizza',
            'business_email' => 'support@pisapizza.ca',
            'business_phone' => '(905) 547-5777',
            'business_address' => '55 Parkdale Ave North, Hamilton, ON L8H 5W7',
            
            // Order settings
            'min_pickup_time' => 30,
            'max_future_days' => 7,
            'pickup_interval' => 15,
            
            // Tax settings
            'tax_enabled' => true,
            'tax_rate' => 13.0,
            'tax_name' => 'HST',
            
            // Payment settings
            'online_payment_enabled' => true,
            'pay_at_pickup_enabled' => true,
            'stripe_public_key' => '',
            'stripe_secret_key' => '',
            
            // Business hours - defaults
            // ... existing code ...
        ];
    }
} 