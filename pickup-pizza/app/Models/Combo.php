<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Combo extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'regular_price',
        'image',
        'active',
        'category_id',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'regular_price' => 'float',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the category that owns the combo.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * The products that belong to the combo.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'combo_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }
    
    /**
     * The upsell products that are suggested with this combo.
     */
    public function upsellProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'combo_upsell_product', 'combo_id', 'product_id')
            ->withTimestamps();
    }
    
    /**
     * Scope a query to only include active combos.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Scope a query to only include combos in a specific category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
    
    /**
     * Calculate the savings amount for this combo.
     */
    public function getSavingsAttribute()
    {
        if (!$this->regular_price || $this->regular_price <= $this->price) {
            return 0;
        }
        
        return $this->regular_price - $this->price;
    }
    
    /**
     * Calculate the savings percentage for this combo.
     */
    public function getSavingsPercentAttribute()
    {
        if (!$this->regular_price || $this->regular_price <= $this->price) {
            return 0;
        }
        
        return ($this->savings / $this->regular_price) * 100;
    }
    
    /**
     * Get a list of the product IDs in this combo.
     */
    public function getProductIdsAttribute()
    {
        return $this->products->pluck('id')->toArray();
    }
    
    /**
     * Get a list of the upsell product IDs for this combo.
     */
    public function getUpsellProductIdsAttribute()
    {
        return $this->upsellProducts->pluck('id')->toArray();
    }
    
    /**
     * Delete the combo's image from storage when the combo is deleted.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($combo) {
            if ($combo->image) {
                \Storage::delete('public/' . $combo->image);
            }
        });
    }
}
