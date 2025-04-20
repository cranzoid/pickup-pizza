<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Topping;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the menu index.
     */
    public function index()
    {
        // Get all active categories with their products
        $categories = Category::active()->sorted()
            ->with(['products' => function($query) {
                $query->active()->sorted();
            }])
            ->get();
        
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
                    $query->active()->sorted();
                    // Filter products based on the current day (only show today's specific product)
                    $query->where('name', 'like', ucfirst($today) . ' Special%');
                }, 'products.category']);
            }
        } else {
            // Daily special - only show the special for the current day
            $dailySpecial = Category::active()
                ->dailySpecials()
                ->where('day_of_week', $today)
                ->first();
                
            // Load the products for the daily special
            if ($dailySpecial) {
                $dailySpecial->load(['products' => function($query) {
                    $query->active()->sorted();
                }, 'products.category']);
            }
        }
        
        // Get popular products
        $popularProducts = Product::active()
            ->where('is_popular', true)
            ->take(4)
            ->get();
        
        // Load the category for each popular product
        if ($popularProducts->isNotEmpty()) {
            $popularProducts->load('category');
        }
        
        return view('menu.index', compact('categories', 'dailySpecial', 'popularProducts'));
    }
    
    /**
     * Display a specific category.
     */
    public function category($slug)
    {
        // Find the category by slug
        $category = Category::active()->where('slug', $slug)->firstOrFail();
        
        // Get all active products in this category
        $products = Product::active()->sorted()
            ->where('category_id', $category->id)
            ->get();
        
        // Get all active categories for navigation links
        $categories = Category::active()->sorted()->get();
        
        return view('menu.category', compact('category', 'products', 'categories'));
    }
    
    /**
     * Show a specific product
     */
    public function product($category, $product)
    {
        // Find the category
        $category = Category::where('slug', $category)->where('is_active', true)->firstOrFail();
        
        // Find the product
        $product = Product::where('slug', $product)
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->firstOrFail();
        
        // For debugging purposes
        \Log::info("Product name: " . $product->name);
        
        // Load product extras if the product has extras
        if ($product->has_extras) {
            $product->load(['extras' => function($query) {
                $query->active()->orderBy('is_default', 'desc')->orderBy('name');
            }]);
        }
        
        // Get all active toppings
        $toppings = Topping::active()->sorted()->get();
        
        // Check if this is the Ultimate Pizza & Wings Combo
        if (strpos($product->name, 'Ultimate Pizza & Wings Combo') !== false) {
            // For Ultimate Pizza & Wings Combo
            // Get the size from the product name if available
            $firstSize = 'medium';
            
            // Check if sizes is already an array before decoding
            $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
            
            \Log::info("Using template: menu.ultimate-combo");
            // Use the Ultimate Combo specific template
            return view('menu.ultimate-combo', compact('category', 'product', 'toppings', 'firstSize'));
        }
        
        // Get add-ons if they exist
        $addOns = json_decode($product->add_ons ?? '{}', true);
        
        // Get the size from the product name if available
        $firstSize = 'medium';
        if (strpos($product->name, 'Medium') !== false) {
            $firstSize = 'medium';
        } elseif (strpos($product->name, 'Large') !== false) {
            $firstSize = 'large';
        } elseif (strpos($product->name, 'X-Large') !== false || strpos($product->name, 'XL') !== false) {
            $firstSize = 'xl';
        } elseif (strpos($product->name, 'Jumbo') !== false) {
            $firstSize = 'jumbo';
        } elseif (strpos($product->name, 'Slab') !== false) {
            $firstSize = 'slab';
        }
        
        // Use the two-pizza-combos view for 2-for-1 pizzas
        if (strpos($product->name, '2 For 1') !== false) {
            \Log::info("Using template: menu.two-pizza-combos");
            return view('menu.two-pizza-combos', compact('category', 'product', 'toppings', 'firstSize', 'addOns'));
        }
        
        // Check if this is a combo product (category ID 3) but not a 2 For 1 or Ultimate Combo
        if ($product->category_id == 3 && 
            strpos($product->name, '2 For 1') === false && 
            strpos($product->name, 'Ultimate Pizza & Wings Combo') === false) {
            \Log::info("Using template: menu.Pizza-combo-New for {$product->name} based on category ID");
            return view('menu.Pizza-combo-New', compact('category', 'product', 'toppings', 'firstSize', 'addOns'));
        }
        
        // Use specialty pizza template for specialty pizzas
        if ($product->is_specialty) {
            \Log::info("Using specialty pizza template for {$product->name}");
            return view('menu.specialty-pizza', compact('category', 'product', 'toppings', 'firstSize'));
        }
        
        \Log::info("Using default template: menu.product");
        return view('menu.product', compact('category', 'product', 'toppings'));
    }
}
