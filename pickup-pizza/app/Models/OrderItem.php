<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'name',
        'size',
        'quantity',
        'unit_price',
        'subtotal',
        'options',
        'notes',
        'is_upsell',
    ];
    
    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'options' => 'json',
        'is_upsell' => 'boolean',
        'quantity' => 'integer',
    ];
    
    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the related product.
     * This is a dynamic method that will return the product or combo depending on the item_type.
     */
    public function item()
    {
        if ($this->item_type === 'product') {
            return $this->belongsTo(Product::class, 'item_id');
        } elseif ($this->item_type === 'combo') {
            return $this->belongsTo(Combo::class, 'item_id');
        }
        
        return null;
    }
    
    /**
     * Scope a query to only include items of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('item_type', $type);
    }
    
    /**
     * Scope a query to only include upsell items.
     */
    public function scopeUpsells($query)
    {
        return $query->where('is_upsell', true);
    }
    
    /**
     * Calculate subtotal based on unit price and quantity.
     */
    public function calculateSubtotal()
    {
        $this->subtotal = $this->unit_price * $this->quantity;
        return $this->subtotal;
    }
}
