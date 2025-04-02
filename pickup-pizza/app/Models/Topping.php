<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topping extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'category',
        'counts_as',
        'price_factor',
        'display_order',
        'is_active'
    ];
    
    protected $casts = [
        'counts_as' => 'integer',
        'price_factor' => 'float',
        'display_order' => 'integer',
        'is_active' => 'boolean'
    ];
    
    /**
     * The products that have this topping.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_toppings')
            ->withPivot('is_default', 'quantity', 'size')
            ->withTimestamps();
    }
    
    /**
     * Get price for a specific size
     */
    public function getPriceForSize($size)
    {
        // Default prices by size
        $defaultPrices = [
            'small' => 1.25,
            'medium' => 1.60,
            'large' => 2.10,
            'extra_large' => 2.30,
            'jumbo' => 2.99
        ];
        
        // Apply price factor to the default price
        $basePrice = $defaultPrices[$size] ?? 1.60;
        return $basePrice * $this->price_factor;
    }
    
    /**
     * Scope a query to only include active toppings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to only include toppings of a specific category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }
    
    /**
     * Scope a query to order by display_order.
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
    
    /**
     * Get the category name for display
     */
    public function getCategoryNameAttribute()
    {
        $categories = [
            'meat' => 'Meat',
            'veggie' => 'Vegetable',
            'cheese' => 'Cheese',
            'sauce' => 'Sauce',
            'spice' => 'Spice'
        ];
        
        return $categories[$this->category] ?? ucfirst($this->category);
    }
}
