<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Get all active categories
        $categories = Category::active()->sorted()->get();
        
        // Determine the current day of the week
        $today = strtolower(Carbon::now()->format('l'));
        
        // Get the daily special for today
        $dailySpecial = null;
        
        if ($today === 'friday' || $today === 'saturday' || $today === 'sunday') {
            // Weekend special
            $dailySpecial = Category::active()
                ->dailySpecials()
                ->where('day_of_week', 'weekend')
                ->first();
        } else {
            // Weekday special
            $dailySpecial = Category::active()
                ->dailySpecials()
                ->where('day_of_week', $today)
                ->first();
        }
        
        // Load the products for the daily special if it exists
        if ($dailySpecial) {
            $dailySpecial->load('products.category');
        }
        
        return view('home.index', compact('categories', 'dailySpecial'));
    }
}
