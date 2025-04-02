<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General settings
            [
                'key' => 'site_name',
                'value' => 'PISA Pizza',
                'group' => 'general',
            ],
            [
                'key' => 'site_description',
                'value' => 'Best pizza in town!',
                'group' => 'general',
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@pisapizza.com',
                'group' => 'contact',
            ],
            [
                'key' => 'contact_phone',
                'value' => '555-123-4567',
                'group' => 'contact',
            ],
            [
                'key' => 'address',
                'value' => '123 Main St, Toronto, ON M5V 2L7',
                'group' => 'contact',
            ],
            
            // Order settings
            [
                'key' => 'min_order_amount',
                'value' => '10.00',
                'group' => 'order',
            ],
            [
                'key' => 'delivery_fee',
                'value' => '5.00',
                'group' => 'order',
            ],
            [
                'key' => 'tax_rate',
                'value' => '13',
                'group' => 'order',
            ],
            [
                'key' => 'tax_enabled',
                'value' => '1',
                'group' => 'order',
            ],
            
            // Business hours
            [
                'key' => 'monday_hours',
                'value' => '11:00-22:00',
                'group' => 'hours',
            ],
            [
                'key' => 'tuesday_hours',
                'value' => '11:00-22:00',
                'group' => 'hours',
            ],
            [
                'key' => 'wednesday_hours',
                'value' => '11:00-22:00',
                'group' => 'hours',
            ],
            [
                'key' => 'thursday_hours',
                'value' => '11:00-22:00',
                'group' => 'hours',
            ],
            [
                'key' => 'friday_hours',
                'value' => '11:00-23:00',
                'group' => 'hours',
            ],
            [
                'key' => 'saturday_hours',
                'value' => '12:00-23:00',
                'group' => 'hours',
            ],
            [
                'key' => 'sunday_hours',
                'value' => '12:00-21:00',
                'group' => 'hours',
            ],
            
            // Social media
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/pisapizza',
                'group' => 'social',
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/pisapizza',
                'group' => 'social',
            ],
            [
                'key' => 'twitter_url',
                'value' => 'https://twitter.com/pisapizza',
                'group' => 'social',
            ],
            // Pizza settings
            [
                'key' => 'enable_extra_toppings',
                'value' => '1',
                'group' => 'pizza',
                'type' => 'boolean',
                'is_public' => true,
            ],
            [
                'key' => 'separate_pizza_toppings',
                'value' => '1',
                'group' => 'pizza',
                'type' => 'boolean',
                'is_public' => true,
            ],
            [
                'key' => 'extra_toppings_button_color',
                'value' => 'red',
                'group' => 'pizza',
                'type' => 'string',
                'is_public' => true,
            ],
            [
                'key' => 'use_same_topping_list_for_extras',
                'value' => '1',
                'group' => 'pizza',
                'type' => 'boolean',
                'is_public' => true,
            ],
        ];
        
        foreach ($settings as $setting) {
            // Only create the setting if it doesn't already exist
            if (!Setting::where('key', $setting['key'])->exists()) {
                Setting::create($setting);
            }
        }
    }
}
