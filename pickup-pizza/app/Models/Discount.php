<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'max_uses',
        'max_uses_per_customer',
        'used_count',
        'active'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'float',
        'min_order_amount' => 'float',
        'max_discount_amount' => 'float',
        'max_uses' => 'integer',
        'max_uses_per_customer' => 'integer',
        'used_count' => 'integer',
        'active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($discount) {
            // Ensure code is uppercase
            if ($discount->code) {
                $discount->code = strtoupper($discount->code);
            }
            
            // Initialize used_count
            $discount->used_count = 0;
        });
        
        static::updating(function ($discount) {
            // Ensure code is uppercase
            if ($discount->code) {
                $discount->code = strtoupper($discount->code);
            }
        });
    }
    
    /**
     * Get the products that this discount applies to.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'discount_product')
            ->withTimestamps();
    }
    
    /**
     * Get the categories that this discount applies to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'discount_category')
            ->withTimestamps();
    }
    
    /**
     * Get the orders that used this discount.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Scope a query to only include active discounts.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Scope a query to only include valid discounts (not expired and not reached usage limit).
     */
    public function scopeValid($query, $date = null)
    {
        $date = $date ?: Carbon::now();
        
        return $query->where('active', true)
            ->where(function ($q) use ($date) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $date);
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')
                    ->orWhereRaw('used_count < max_uses');
            });
    }
    
    /**
     * Check if this discount is valid for the given order amount.
     */
    public function isValidForAmount($amount)
    {
        if (!$this->active) {
            return false;
        }

        // Check if the discount is expired
        if ($this->start_date && Carbon::now()->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && Carbon::now()->gt($this->end_date)) {
            return false;
        }

        // Check if maximum uses has been reached
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        // Check if the order amount meets the minimum requirement
        if ($this->min_order_amount && $amount < $this->min_order_amount) {
            return false;
        }

        return true;
    }
    
    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount($subtotal)
    {
        if (!$this->isValidForAmount($subtotal)) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);
        } elseif ($this->type === 'fixed') {
            $discount = $this->value;
        }

        // Cap the discount at max_discount_amount if set
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        // Ensure the discount does not exceed the subtotal
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        return $discount;
    }
    
    /**
     * Format the discount value for display.
     */
    public function getFormattedValueAttribute()
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        
        return '$' . number_format($this->value, 2);
    }
    
    /**
     * Get the status of the discount.
     */
    public function getStatusAttribute()
    {
        if (!$this->active) {
            return 'inactive';
        }
        
        if ($this->end_date && Carbon::now()->gt($this->end_date)) {
            return 'expired';
        }
        
        if ($this->start_date && Carbon::now()->lt($this->start_date)) {
            return 'scheduled';
        }
        
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return 'exhausted';
        }
        
        return 'active';
    }
    
    /**
     * Check if the discount applies to a specific product.
     */
    public function appliesToProduct($productId)
    {
        // If no specific products or categories, it applies to all
        if ($this->products->isEmpty() && $this->categories->isEmpty()) {
            return true;
        }
        
        // Check if product is directly included
        if ($this->products->contains($productId)) {
            return true;
        }
        
        // Check if product's category is included
        $product = Product::find($productId);
        if ($product && $product->category_id) {
            return $this->categories->contains($product->category_id);
        }
        
        return false;
    }
    
    /**
     * Get the used_count attribute
     */
    public function getUsedCountAttribute()
    {
        // If the field exists in the model, use it, otherwise return usage_count
        return $this->attributes['used_count'] ?? $this->attributes['usage_count'] ?? 0;
    }
    
    /**
     * Set the used_count attribute
     */
    public function setUsedCountAttribute($value)
    {
        // If we're using usage_count in the database, set that instead
        if (array_key_exists('usage_count', $this->attributes)) {
            $this->attributes['usage_count'] = $value;
        } else {
            $this->attributes['used_count'] = $value;
        }
    }
    
    /**
     * Check if discount is valid
     */
    public function isValid()
    {
        // Check if active
        if (!$this->active) {
            return false;
        }

        // Check if expired
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }
    
    /**
     * Calculate the discount amount for a given subtotal
     */
    public function calculateDiscountAmount($subtotal)
    {
        if (!$this->isValid()) {
            return 0;
        }

        $amount = 0;
        
        if ($this->type === 'percentage') {
            $amount = $subtotal * ($this->value / 100);
        } elseif ($this->type === 'fixed') {
            $amount = $this->value;
            
            // Fixed amount cannot be greater than the subtotal
            if ($amount > $subtotal) {
                $amount = $subtotal;
            }
        }
        
        return round($amount, 2);
    }
}
