<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'pickup_time',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'payment_method',
        'payment_status',
        'payment_id',
        'order_status',
        'order_notes',
        'discount_id',
    ];
    
    protected $casts = [
        'pickup_time' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    
    /**
     * Get the discount used for this order.
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }
    
    /**
     * Get the items for this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'PZ';
        $date = now()->format('Ymd');
        $random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        $orderNumber = $prefix . $date . $random;
        
        // Check if the order number already exists
        while (self::where('order_number', $orderNumber)->exists()) {
            $random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $orderNumber = $prefix . $date . $random;
        }
        
        return $orderNumber;
    }
    
    /**
     * Scope a query to only include orders with specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }
    
    /**
     * Scope a query to only include orders for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('pickup_time', $date);
    }
    
    /**
     * Scope a query to only include orders for today.
     */
    public function scopeForToday($query)
    {
        return $query->whereDate('pickup_time', today());
    }
    
    /**
     * Scope a query to only include orders for a specific week.
     */
    public function scopeForWeek($query, $weekStart = null)
    {
        $weekStart = $weekStart ?? now()->startOfWeek();
        $weekEnd = (clone $weekStart)->endOfWeek();
        
        return $query->whereBetween('pickup_time', [$weekStart, $weekEnd]);
    }
    
    /**
     * Scope a query to only include orders for a specific month.
     */
    public function scopeForMonth($query, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        return $query->whereMonth('pickup_time', $month)
            ->whereYear('pickup_time', $year);
    }
}
