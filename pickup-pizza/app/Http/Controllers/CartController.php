<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Topping;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        $cartItems = session()->get('cart', []);
        $cartTotal = $this->calculateCartTotal($cartItems);
        $settings = new Setting();
        
        // Calculate tax if enabled
        $taxRate = $settings->get('tax_rate', 13);
        $taxEnabled = $settings->get('tax_enabled', true);
        $taxName = $settings->get('tax_name', 'Tax');
        $taxAmount = $taxEnabled ? ($cartTotal * $taxRate / 100) : 0;
        
        // Get discount amount if applied
        $discountAmount = session()->has('discount') ? session('discount.amount') : 0;
        
        // Calculate order total
        $orderTotal = $cartTotal + $taxAmount - $discountAmount;
        
        return view('cart.index', compact('cartItems', 'cartTotal', 'taxAmount', 'orderTotal', 'taxName', 'taxRate', 'taxEnabled', 'settings'));
    }
    
    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string',
            'toppings' => 'nullable|array',
            'extras' => 'nullable|array',
            'extras.*.id' => 'exists:product_extras,id',
            'extras.*.quantity' => 'integer|min:1',
            'notes' => 'nullable|string|max:255',
            'add_extra_toppings' => 'nullable|in:yes,no',
            'extra_toppings' => 'nullable|array',
            'extra_toppings.*' => 'exists:toppings,id',
        ]);
        
        // Get the product
        $product = Product::findOrFail($request->product_id);
        
        // Get product price based on size if it has size options
        $price = $product->price;
        if ($product->has_size_options && $request->has('size')) {
            $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
            $price = $sizes[$request->size] ?? $product->price;
        }
        
        // Handle toppings if any
        $options = [];
        $toppingNames = [];
        
        if ($request->has('toppings') && is_array($request->toppings) && count($request->toppings) > 0) {
            $toppings = Topping::whereIn('id', $request->toppings)->get();
            $toppingPrice = 0;
            
            // Calculate extra toppings price
            if (count($toppings) > 1 && $product->is_pizza && !$product->is_specialty) {
                $extraToppingsCount = count($toppings) - 1; // First topping is included
                
                foreach ($toppings as $topping) {
                    $toppingNames[] = $topping->name;
                    
                    if ($extraToppingsCount > 0) {
                        $size = $request->size ?? 'medium';
                        $toppingPrice += $topping->getPriceForSize(strtolower($size));
                    }
                }
                
                // Add toppings price to product price
                $price += $toppingPrice;
            } else {
                // For specialty pizzas, just collect the topping names
                foreach ($toppings as $topping) {
                    $toppingNames[] = $topping->name;
                }
            }
            
            $options['toppings'] = $toppingNames;
        }
        
        // Handle extra toppings for specialty pizzas
        if ($request->has('add_extra_toppings') && $request->add_extra_toppings === 'yes' && 
            $request->has('extra_toppings') && is_array($request->extra_toppings) && 
            count($request->extra_toppings) > 0 && $product->is_specialty) {
            
            $extraToppings = Topping::whereIn('id', $request->extra_toppings)->get();
            $extraToppingPrice = 0;
            $extraToppingNames = [];
            
            foreach ($extraToppings as $topping) {
                $extraToppingNames[] = $topping->name;
                $size = strtolower($request->size ?? 'medium');
                
                // Use pricing from the product's add_ons if available
                $pricePerTopping = 1.60; // Default medium price
                
                if (!empty($product->add_ons)) {
                    $addOns = is_array($product->add_ons) ? $product->add_ons : json_decode($product->add_ons, true);
                    if (isset($addOns['extra_topping_price'])) {
                        if (is_array($addOns['extra_topping_price']) && isset($addOns['extra_topping_price'][$size])) {
                            $pricePerTopping = $addOns['extra_topping_price'][$size];
                        } elseif (!is_array($addOns['extra_topping_price'])) {
                            $pricePerTopping = $addOns['extra_topping_price'];
                        }
                    }
                } else {
                    // Fallback to default pricing if not in add_ons
                    switch($size) {
                        case 'small':
                            $pricePerTopping = 1.30;
                            break;
                        case 'medium':
                            $pricePerTopping = 1.60;
                            break;
                        case 'large':
                            $pricePerTopping = 2.10;
                            break;
                        case 'xl':
                        case 'extra_large':
                            $pricePerTopping = 2.30;
                            break;
                        case 'jumbo':
                        case 'slab':
                            $pricePerTopping = 2.90;
                            break;
                    }
                }
                
                $extraToppingPrice += $pricePerTopping * $topping->counts_as;
            }
            
            // Add extra toppings price to product price
            $price += $extraToppingPrice;
            
            // Add to options
            $options['extra_toppings'] = $extraToppingNames;
        }
        
        // Handle extra toppings for non-specialty pizzas
        if ($request->has('add_extra_toppings') && $request->add_extra_toppings === 'yes' && 
            $request->has('extra_toppings') && is_array($request->extra_toppings) && 
            count($request->extra_toppings) > 0 && !$product->is_specialty && $product->is_pizza) {
            
            $extraToppings = Topping::whereIn('id', $request->extra_toppings)->get();
            $extraToppingPrice = 0;
            $extraToppingNames = [];
            
            foreach ($extraToppings as $topping) {
                $extraToppingNames[] = $topping->name;
                $size = $request->size ?? 'medium';
                
                // Use exact pricing from the menu document
                $pricePerTopping = 1.60; // Default medium
                switch(strtolower($size)) {
                    case 'small':
                        $pricePerTopping = 1.30;
                        break;
                    case 'medium':
                        $pricePerTopping = 1.60;
                        break;
                    case 'large':
                        $pricePerTopping = 2.10;
                        break;
                    case 'xl':
                    case 'extra_large':
                        $pricePerTopping = 2.30;
                        break;
                    case 'jumbo':
                    case 'slab':
                        $pricePerTopping = 2.90;
                        break;
                }
                
                $extraToppingPrice += $pricePerTopping * $topping->counts_as;
            }
            
            // Add extra toppings price to product price
            $price += $extraToppingPrice;
            
            // Add to options
            $options['extra_toppings'] = $extraToppingNames;
        }
        
        // Handle extras if any
        if ($request->has('extras') && is_array($request->extras) && count($request->extras) > 0) {
            $extras = [];
            $extraPrice = 0;
            
            foreach ($request->extras as $extra) {
                if (isset($extra['id']) && isset($extra['quantity']) && $extra['quantity'] > 0) {
                    $productExtra = \App\Models\ProductExtra::find($extra['id']);
                    
                    if ($productExtra && $productExtra->active) {
                        // Ensure quantity doesn't exceed max_quantity
                        $quantity = min($extra['quantity'], $productExtra->max_quantity);
                        
                        // Add to options
                        $extras[] = [
                            'id' => $productExtra->id,
                            'name' => $productExtra->name,
                            'quantity' => $quantity,
                            'price' => $productExtra->price
                        ];
                        
                        // Add extra price to the total
                        $extraPrice += $productExtra->price * $quantity;
                    }
                }
            }
            
            if (!empty($extras)) {
                $options['extras'] = $extras;
                // Add extras price to the product price
                $price += $extraPrice;
            }
        }
        
        // Create cart item
        $item = [
            'id' => $product->id,
            'type' => 'product',
            'name' => $product->name,
            'size' => $request->size ?? null,
            'quantity' => (int) $request->quantity,
            'unit_price' => $price,
            'options' => $options,
            'notes' => $request->notes ?? null,
            'image' => $product->image_path,
        ];
        
        // Get existing cart items
        $cart = session()->get('cart', []);
        
        // Add new item to cart
        $cart[] = $item;
        
        // Save cart to session
        session()->put('cart', $cart);
        
        // Check for potential upsells based on the product added
        $upsell = $this->getUpsellForProduct($product);
        
        if ($upsell) {
            // Store upsell in session to show on redirect
            session()->flash('upsell', $upsell);
        }
        
        return redirect()->route('cart.index')->with('success', $product->name . ' has been added to your cart.');
    }
    
    /**
     * Get a potential upsell product based on the product added.
     */
    private function getUpsellForProduct($product)
    {
        // Check if product is a pizza - upsell with wings or drinks
        if ($product->is_pizza) {
            // For pizza products, suggest wings as an upsell
            $wings = Product::active()
                ->where('name', 'like', '%wings%')
                ->first();
                
            if ($wings) {
                return [
                    'product' => $wings,
                    'message' => 'Add wings to your order?',
                    'price' => $wings->price
                ];
            }
            
            // If no wings found, try drinks
            $drinks = Product::active()
                ->where(function($query) {
                    $query->where('name', 'like', '%pop%')
                        ->orWhere('name', 'like', '%soda%')
                        ->orWhere('name', 'like', '%drink%');
                })
                ->first();
                
            if ($drinks) {
                return [
                    'product' => $drinks,
                    'message' => 'Add a drink to your order?',
                    'price' => $drinks->price
                ];
            }
        }
        
        // If product is wings, upsell with garlic bread
        if (stripos($product->name, 'wings') !== false) {
            $garlicBread = Product::active()
                ->where('name', 'like', '%garlic bread%')
                ->first();
                
            if ($garlicBread) {
                return [
                    'product' => $garlicBread,
                    'message' => 'Add garlic bread to your order?',
                    'price' => $garlicBread->price
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Add an upsell product to the cart.
     */
    public function addUpsell(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);
        
        // Get the product
        $product = Product::findOrFail($request->product_id);
        
        // Create cart item
        $item = [
            'id' => $product->id,
            'type' => 'product',
            'name' => $product->name,
            'size' => null,
            'quantity' => (int) $request->quantity,
            'unit_price' => $product->price,
            'options' => [],
            'notes' => null,
            'image' => $product->image_path,
            'is_upsell' => true
        ];
        
        // Get existing cart items
        $cart = session()->get('cart', []);
        
        // Add new item to cart
        $cart[] = $item;
        
        // Save cart to session
        session()->put('cart', $cart);
        
        return redirect()->route('cart.index')->with('success', $product->name . ' has been added to your cart.');
    }
    
    /**
     * Update cart item.
     */
    public function update(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
            'update_action' => 'required|in:increase,decrease',
        ]);
        
        $cart = session()->get('cart', []);
        $index = $request->index;
        
        // Check if item exists
        if (!isset($cart[$index])) {
            return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
        }
        
        // Update quantity
        if ($request->update_action === 'increase') {
            if ($cart[$index]['quantity'] < 10) {
                $cart[$index]['quantity']++;
            }
        } else {
            if ($cart[$index]['quantity'] > 1) {
                $cart[$index]['quantity']--;
            } else {
                // Remove item if quantity becomes 0
                unset($cart[$index]);
                $cart = array_values($cart); // Re-index array
            }
        }
        
        // Save updated cart
        session()->put('cart', $cart);
        
        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }
    
    /**
     * Remove item from cart.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
        ]);
        
        $cart = session()->get('cart', []);
        $index = $request->index;
        
        // Check if item exists
        if (!isset($cart[$index])) {
            return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
        }
        
        // Remove item
        unset($cart[$index]);
        
        // Re-index array
        $cart = array_values($cart);
        
        // Save updated cart
        session()->put('cart', $cart);
        
        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }
    
    /**
     * Clear the cart.
     */
    public function clear()
    {
        session()->forget(['cart', 'discount']);
        
        return redirect()->route('cart.index')->with('success', 'Cart has been cleared.');
    }
    
    /**
     * Apply discount code.
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'discount_code' => 'required|string|exists:discounts,code',
        ]);
        
        // Get the discount code
        $discount = Discount::where('code', $request->discount_code)->first();
        
        // Check if discount is valid
        if (!$discount->isValid()) {
            return redirect()->route('cart.index')
                ->withErrors(['discount_code' => 'This discount code is no longer valid.']);
        }
        
        // Calculate discount amount
        $cartItems = session()->get('cart', []);
        $cartTotal = $this->calculateCartTotal($cartItems);
        $discountAmount = $discount->calculateDiscountAmount($cartTotal);
        
        // Save discount to session
        session()->put('discount', [
            'id' => $discount->id,
            'code' => $discount->code,
            'type' => $discount->type,
            'value' => $discount->value,
            'amount' => $discountAmount,
        ]);
        
        return redirect()->route('cart.index')->with('success', 'Discount code applied successfully.');
    }
    
    /**
     * Remove discount code.
     */
    public function removeDiscount()
    {
        session()->forget('discount');
        
        return redirect()->route('cart.index')->with('success', 'Discount code removed.');
    }
    
    /**
     * Calculate cart total.
     */
    private function calculateCartTotal($cartItems)
    {
        $total = 0;
        
        foreach ($cartItems as $item) {
            $total += $item['unit_price'] * $item['quantity'];
        }
        
        return $total;
    }
}
