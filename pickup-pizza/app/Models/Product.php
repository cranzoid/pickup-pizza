<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image_path',
        'price',
        'sizes',
        'max_toppings',
        'free_toppings',
        'is_pizza',
        'is_specialty',
        'has_size_options',
        'has_toppings',
        'has_extras',
        'active',
        'is_featured',
        'sort_order',
        'display_day',
        'preselected_toppings',
        'is_customizable',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'sizes' => 'json',
        'is_pizza' => 'boolean',
        'is_specialty' => 'boolean',
        'has_size_options' => 'boolean',
        'has_toppings' => 'boolean',
        'has_extras' => 'boolean',
        'active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'max_toppings' => 'integer',
        'free_toppings' => 'integer',
        'preselected_toppings' => 'json',
        'is_customizable' => 'boolean',
    ];
    
    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Get the toppings for the product.
     */
    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'product_toppings')
            ->withPivot('is_default', 'quantity', 'size')
            ->withTimestamps();
    }
    
    /**
     * Get the default toppings for specialty pizzas.
     */
    public function defaultToppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'product_toppings')
            ->withPivot('is_default', 'quantity', 'size')
            ->wherePivot('is_default', true)
            ->withTimestamps();
    }
    
    /**
     * Get the combos that include this product.
     */
    public function combos(): BelongsToMany
    {
        return $this->belongsToMany(Combo::class, 'combo_products')
            ->withPivot('quantity', 'size', 'max_toppings', 'is_optional', 'additional_price')
            ->withTimestamps();
    }
    
    /**
     * Get the order items associated with this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'item_id')
                    ->where('item_type', 'product');
    }
    
    /**
     * Get the product extras associated with the product.
     */
    public function extras()
    {
        return $this->belongsToMany(ProductExtra::class);
    }
    
    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Scope a query to only include pizzas.
     */
    public function scopePizzas($query)
    {
        return $query->where('is_pizza', true);
    }
    
    /**
     * Scope a query to only include non-pizzas.
     */
    public function scopeNotPizzas($query)
    {
        return $query->where('is_pizza', false);
    }
    
    /**
     * Scope a query to only include specialty pizzas.
     */
    public function scopeSpecialty($query)
    {
        return $query->where('is_specialty', true);
    }
    
    /**
     * Scope a query to order by sort_order.
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
    
    /**
     * Get price for a specific size
     */
    public function getPriceForSize($size)
    {
        $sizes = is_array($this->sizes) ? $this->sizes : json_decode($this->sizes, true);
        
        if (is_array($sizes) && isset($sizes[$size])) {
            return $sizes[$size];
        }
        
        return $this->price;
    }
    
    /**
     * Get the display price (for specialty pizzas, this is the first size price)
     */
    public function getDisplayPrice()
    {
        // If it's a specialty pizza with sizes, return the first size price
        if ($this->is_specialty && !empty($this->sizes)) {
            $sizes = is_array($this->sizes) ? $this->sizes : json_decode($this->sizes, true);
            if (is_array($sizes) && !empty($sizes)) {
                return reset($sizes); // Return the first element in the sizes array
            }
        }
        
        // Otherwise return the base price
        return $this->price;
    }
    
    /**
     * Get extra topping price for a specific size
     */
    public function getExtraToppingPrice($size)
    {
        // Default extra topping prices by size
        $defaultPrices = [
            'small' => 1.25,
            'medium' => 1.60,
            'large' => 2.10,
            'extra_large' => 2.30,
            'jumbo' => 2.99
        ];
        
        return $defaultPrices[$size] ?? 1.00;
    }
}
