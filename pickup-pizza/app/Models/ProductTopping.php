<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTopping extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'topping_id',
        'is_default',
        'quantity',
        'size',
    ];
    
    protected $casts = [
        'is_default' => 'boolean',
        'quantity' => 'integer',
    ];
    
    /**
     * Get the product that owns the topping.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the topping that belongs to the product.
     */
    public function topping(): BelongsTo
    {
        return $this->belongsTo(Topping::class);
    }
}
