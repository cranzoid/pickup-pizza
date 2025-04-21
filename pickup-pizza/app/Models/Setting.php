<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'is_public',
    ];
    
    protected $casts = [
        'is_public' => 'boolean',
    ];
    
    /**
     * Get a setting value by key with optional default value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        // Try to get from cache first
        if (Cache::has('setting_' . $key)) {
            $value = Cache::get('setting_' . $key);
            // Try to get the type from the database to cast value
            $setting = self::where('key', $key)->first();
            if ($setting && $setting->type) {
                return self::castValue($value, $setting->type);
            }
            // Default casting (try to detect booleans)
            if ($value === '1' || $value === '0') {
                return $value === '1';
            }
            return $value;
        }
        
        // If not in cache, retrieve from database
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            // Store in cache for future requests
            Cache::put('setting_' . $key, $setting->value, now()->addDay());
            return self::castValue($setting->value, $setting->type ?? 'string');
        }
        
        return $default;
    }
    
    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return bool
     */
    public static function set($key, $value, $group = 'general')
    {
        $setting = self::firstOrNew(['key' => $key]);
        
        $setting->value = $value;
        $setting->group = $group;
        
        // Determine the type of value for proper casting
        if (is_bool($value) || $value === '0' || $value === '1' || $value === 0 || $value === 1) {
            $setting->type = 'boolean';
            // Store as string '0' or '1' for consistency
            $setting->value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        } elseif (is_numeric($value)) {
            $setting->type = 'number';
        } elseif (is_array($value)) {
            $setting->type = 'json';
            $setting->value = json_encode($value);
        } else {
            $setting->type = 'string';
        }
        
        $result = $setting->save();
        
        // Update cache
        if ($result) {
            Cache::put('setting_' . $key, $setting->value, now()->addDay());
        }
        
        return $result;
    }
    
    /**
     * Delete a setting.
     *
     * @param string $key
     * @return bool
     */
    public function remove($key)
    {
        // Remove from cache
        Cache::forget('setting_' . $key);
        
        // Remove from database
        return self::where('key', $key)->delete();
    }
    
    /**
     * Get all settings in a specific group.
     *
     * @param string $group
     * @return array
     */
    public function getGroup($group)
    {
        $settings = self::where('group', $group)->get();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        return $result;
    }
    
    /**
     * Get all public settings.
     */
    public static function getPublic()
    {
        return self::where('is_public', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            });
    }
    
    /**
     * Cast the value to its appropriate type.
     */
    private static function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? (float) $value : $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
    
    /**
     * Get all settings with default values.
     *
     * @return array
     */
    public static function getAllSettings()
    {
        $settings = self::all()->keyBy('key')->map(function($setting) {
            return self::castValue($setting->value, $setting->type ?? 'string');
        })->toArray();
        
        // Merge with default settings
        return array_merge(self::getDefaultSettings(), $settings);
    }
    
    /**
     * Get default settings.
     *
     * @return array
     */
    public static function getDefaultSettings()
    {
        return [
            // Business information
            'business_name' => 'PISA Pizza',
            'business_email' => 'info@pisapizza.com',
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
            'monday_open' => true,
            'monday_from' => '11:00',
            'monday_to' => '22:00',
            'tuesday_open' => true,
            'tuesday_from' => '11:00',
            'tuesday_to' => '22:00',
            'wednesday_open' => true,
            'wednesday_from' => '11:00',
            'wednesday_to' => '22:00',
            'thursday_open' => true,
            'thursday_from' => '11:00',
            'thursday_to' => '22:00',
            'friday_open' => true,
            'friday_from' => '11:00',
            'friday_to' => '23:00',
            'saturday_open' => true,
            'saturday_from' => '11:00',
            'saturday_to' => '23:00',
            'sunday_open' => true,
            'sunday_from' => '12:00',
            'sunday_to' => '22:00',
            
            // Discounts
            'discounts_enabled' => true,
        ];
    }
}
