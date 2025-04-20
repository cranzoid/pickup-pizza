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
            
            // Load the products for the daily special
            if ($dailySpecial) {
                $dailySpecial->load(['products' => function($query) use ($today) {
                    // Filter products based on the current day (only show today's specific product)
                    $query->where('name', 'like', ucfirst($today) . ' Special%');
                }, 'products.category']);
            }
        } else {
            // Weekday special - only show the special for the current day
            $dailySpecial = Category::active()
                ->dailySpecials()
                ->where('day_of_week', $today)
                ->first();
                
            // Load the products for the daily special
            if ($dailySpecial) {
                $dailySpecial->load('products.category');
            }
        }
        
        return view('home.index', compact('categories', 'dailySpecial'));
    }
}
