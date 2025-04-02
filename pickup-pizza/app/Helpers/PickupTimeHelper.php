<?php

namespace App\Helpers;

use App\Models\Setting;
use Carbon\Carbon;

class PickupTimeHelper
{
    /**
     * Get available pickup times for a specific date.
     * 
     * @param string $date Date in Y-m-d format
     * @return array Array of available pickup times
     */
    public static function getAvailablePickupTimes($date)
    {
        $settings = new Setting();
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        
        // Get business hours for the selected day
        $isOpen = $settings->get($dayOfWeek . '_open', true);
        
        if (!$isOpen) {
            return [];
        }
        
        $openTime = $settings->get($dayOfWeek . '_from', '11:00');
        $closeTime = $settings->get($dayOfWeek . '_to', '22:00');
        
        // Get minimum pickup time (in minutes)
        $minPickupTime = $settings->get('min_pickup_time', 30);
        
        // Get pickup interval (in minutes)
        $pickupInterval = $settings->get('pickup_interval', 15);
        
        // Calculate available times
        $times = [];
        $now = Carbon::now();
        $requestedDate = Carbon::parse($date);
        $isToday = $requestedDate->isSameDay($now);
        
        // Start time is either the opening time or current time + minimum pickup time
        $startTime = Carbon::parse($date . ' ' . $openTime);
        
        if ($isToday) {
            $earliestPickup = $now->copy()->addMinutes($minPickupTime);
            
            if ($earliestPickup->greaterThan($startTime)) {
                $startTime = $earliestPickup;
            }
        }
        
        // Ensure start time is rounded up to the next interval
        $minutes = $startTime->minute;
        $roundedMinutes = ceil($minutes / $pickupInterval) * $pickupInterval;
        
        // Adjust for the case when rounding puts us into the next hour
        if ($roundedMinutes >= 60) {
            $hoursToAdd = floor($roundedMinutes / 60);
            $roundedMinutes = $roundedMinutes % 60;
            $startTime->addHours($hoursToAdd);
        }
        
        $startTime->minute($roundedMinutes)->second(0);
        
        // End time is the closing time
        $endTime = Carbon::parse($date . ' ' . $closeTime);
        
        // If start time is after or equal to end time, no time slots are available
        if ($startTime->greaterThanOrEqualTo($endTime)) {
            return [];
        }
        
        // Generate time slots at specified intervals
        $current = $startTime->copy();
        
        // Ensure we have at least one time slot before closing
        // We need at least 'minPickupTime' minutes before closing
        $lastValidTime = $endTime->copy()->subMinutes($minPickupTime);
        
        while ($current->lessThanOrEqualTo($lastValidTime)) {
            $times[] = $current->format('H:i');
            $current->addMinutes($pickupInterval);
        }
        
        return $times;
    }
    
    /**
     * Format a time for display
     * 
     * @param string $time Time in H:i format
     * @return string Formatted time (e.g., "11:30 AM")
     */
    public static function formatTime($time)
    {
        return Carbon::parse($time)->format('g:i A');
    }
    
    /**
     * Get available dates for pickup
     * 
     * @return array Array of available dates
     */
    public static function getAvailableDates()
    {
        $settings = new Setting();
        $maxFutureDays = $settings->get('max_future_days', 7);
        
        $dates = [];
        $now = Carbon::now();
        
        // Get all required days, including ones where the business is closed
        // so we can check the next available days
        for ($i = 0; $i < $maxFutureDays + 7; $i++) {
            $date = $now->copy()->addDays($i);
            $dayOfWeek = strtolower($date->format('l'));
            $isOpen = $settings->get($dayOfWeek . '_open', true);
            
            // Add only open days to the list, but limit to maxFutureDays
            if ($isOpen && count($dates) < $maxFutureDays) {
                // For today, check if we're still within business hours
                if ($date->isToday()) {
                    $closeTime = $settings->get($dayOfWeek . '_to', '22:00');
                    $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $closeTime);
                    $minPickupTime = $settings->get('min_pickup_time', 30);
                    
                    // If current time + minimum pickup time is after closing time,
                    // don't include today as an option
                    if ($now->copy()->addMinutes($minPickupTime)->greaterThan($endTime)) {
                        continue;
                    }
                    
                    // Also check if there are available times
                    $availableTimes = self::getAvailablePickupTimes($date->format('Y-m-d'));
                    if (empty($availableTimes)) {
                        continue;
                    }
                }
                
                $dates[] = [
                    'value' => $date->format('Y-m-d'),
                    'label' => $date->format('l, F j, Y') . ($date->isToday() ? ' (Today)' : ''),
                    'is_today' => $date->isToday()
                ];
            }
        }
        
        return $dates;
    }
} 