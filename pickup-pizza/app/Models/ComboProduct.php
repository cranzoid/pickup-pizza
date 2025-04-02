<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComboProduct extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'combo_id',
        'product_id',
        'quantity',
        'size',
        'max_toppings',
        'is_optional',
        'additional_price',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'max_toppings' => 'integer',
        'is_optional' => 'boolean',
        'additional_price' => 'decimal:2',
    ];
    
    /**
     * Get the combo that owns the product.
     */
    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }
    
    /**
     * Get the product that belongs to the combo.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
