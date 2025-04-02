<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = Setting::getAllSettings();
        
        return view('admin.settings.index', [
            'settings' => $settings
        ]);
    }
    
    /**
     * Update the specified settings section.
     */
    public function update(Request $request)
    {
        $section = $request->input('section');
        $settings = $request->input('settings', []);
        
        // Validate based on section
        switch ($section) {
            case 'business':
                $this->validateBusinessSettings($request);
                break;
            case 'orders':
                $this->validateOrderSettings($request);
                break;
            case 'tax':
                $this->validateTaxSettings($request);
                break;
            case 'payment':
                $this->validatePaymentSettings($request);
                break;
            case 'hours':
                $this->validateHoursSettings($request);
                break;
            default:
                return redirect()->back()
                    ->with('error', 'Invalid settings section.');
        }
        
        // Handle boolean settings that might not be present in the request when unchecked
        if ($section === 'payment') {
            // Set these to false if they don't exist in the request
            if (!isset($settings['online_payment_enabled'])) {
                $settings['online_payment_enabled'] = false;
            }
            if (!isset($settings['pay_at_pickup_enabled'])) {
                $settings['pay_at_pickup_enabled'] = false;
            }
        } else if ($section === 'tax') {
            // Handle tax_enabled checkbox
            if (!isset($settings['tax_enabled'])) {
                $settings['tax_enabled'] = false;
            }
        } else if ($section === 'hours') {
            // Handle day_open checkboxes
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                if (!isset($settings[$day . '_open'])) {
                    $settings[$day . '_open'] = false;
                }
            }
        }
        
        // Save all settings from the request
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
        
        return redirect()->back()
            ->with('success', 'Settings updated successfully.');
    }
    
    /**
     * Validate business information settings.
     */
    private function validateBusinessSettings(Request $request)
    {
        $request->validate([
            'settings.business_name' => 'required|string|max:100',
            'settings.business_email' => 'required|email|max:255',
            'settings.business_phone' => 'required|string|max:20',
            'settings.business_address' => 'required|string|max:255',
        ]);
    }
    
    /**
     * Validate order settings.
     */
    private function validateOrderSettings(Request $request)
    {
        $request->validate([
            'settings.min_pickup_time' => 'required|integer|min:0|max:1440',
            'settings.max_future_days' => 'required|integer|min:1|max:90',
            'settings.pickup_interval' => 'required|integer|in:15,30,60',
        ]);
    }
    
    /**
     * Validate tax settings.
     */
    private function validateTaxSettings(Request $request)
    {
        $request->validate([
            'settings.tax_enabled' => 'nullable|boolean',
            'settings.tax_rate' => 'required|numeric|min:0|max:30',
            'settings.tax_name' => 'required|string|max:50',
        ]);
    }
    
    /**
     * Validate payment settings.
     */
    private function validatePaymentSettings(Request $request)
    {
        $request->validate([
            'settings.online_payment_enabled' => 'nullable|boolean',
            'settings.pay_at_pickup_enabled' => 'nullable|boolean',
            'settings.stripe_public_key' => 'required_if:settings.online_payment_enabled,1|nullable|string',
            'settings.stripe_secret_key' => 'required_if:settings.online_payment_enabled,1|nullable|string',
        ]);
    }
    
    /**
     * Validate business hours settings.
     */
    private function validateHoursSettings(Request $request)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        $rules = [];
        foreach ($days as $day) {
            $rules["settings.{$day}_open"] = 'nullable|boolean';
            $rules["settings.{$day}_from"] = "required_if:settings.{$day}_open,1|nullable|date_format:H:i";
            $rules["settings.{$day}_to"] = "required_if:settings.{$day}_open,1|nullable|date_format:H:i";
        }
        
        $request->validate($rules);
    }
} 