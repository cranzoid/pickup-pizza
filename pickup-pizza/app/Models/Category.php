<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'active',
        'is_daily_special',
        'day_of_week',
    ];
    
    protected $casts = [
        'active' => 'boolean',
        'is_daily_special' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    /**
     * Get the products in this category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Get the combos in this category.
     */
    public function combos(): HasMany
    {
        return $this->hasMany(Combo::class);
    }
    
    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Scope a query to order by sort_order.
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
    
    /**
     * Scope a query to only include daily specials.
     */
    public function scopeDailySpecials($query)
    {
        return $query->where('is_daily_special', true);
    }
    
    /**
     * Get daily special for a specific day.
     */
    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }
}
