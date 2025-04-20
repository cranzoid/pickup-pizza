<?php

namespace App\Http\Controllers;

use App\Helpers\EmailHelper;
use App\Helpers\PickupTimeHelper;
use App\Mail\NewOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function index()
    {
        // Check if cart has items
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty. Please add items before checkout.');
        }
        
        // Get settings
        $settings = new Setting();
        
        // Check if at least one payment method is enabled
        $onlinePaymentEnabled = $settings->get('online_payment_enabled', true);
        $payAtPickupEnabled = $settings->get('pay_at_pickup_enabled', true);
        
        if (!$onlinePaymentEnabled && !$payAtPickupEnabled) {
            return redirect()->route('cart.index')->with('error', 'No payment methods are currently available. Please try again later.');
        }
        
        // Get available pickup dates and times
        $availableDates = PickupTimeHelper::getAvailableDates();
        $firstAvailableDate = $availableDates[0]['value'] ?? date('Y-m-d');
        $availableTimes = PickupTimeHelper::getAvailablePickupTimes($firstAvailableDate);
        
        // Get stripe public key for checkout page
        $stripePublicKey = $settings->get('stripe_public_key');
        
        // Get tax settings to display on checkout page
        $taxEnabled = $settings->get('tax_enabled', true);
        $taxRate = $settings->get('tax_rate', 13);
        $taxName = $settings->get('tax_name', 'Tax');
        
        return view('checkout.index', compact(
            'availableDates', 
            'availableTimes', 
            'onlinePaymentEnabled', 
            'payAtPickupEnabled',
            'stripePublicKey',
            'taxEnabled',
            'taxRate',
            'taxName',
            'settings'
        ));
    }
    
    /**
     * Get available pickup times for a given date.
     */
    public function getPickupTimes(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);
        
        $availableTimes = PickupTimeHelper::getAvailablePickupTimes($request->date);
        
        $formattedTimes = [];
        foreach ($availableTimes as $time) {
            $formattedTimes[] = [
                'value' => $time,
                'label' => PickupTimeHelper::formatTime($time)
            ];
        }
        
        return response()->json($formattedTimes);
    }
    
    /**
     * Process the checkout.
     */
    public function process(Request $request)
    {
        // Validate the request
        $this->validateCheckout($request);
        
        // Get cart items and settings
        $cartItems = session()->get('cart', []);
        $settings = new Setting();
        
        // Calculate totals
        $subtotal = $this->calculateSubtotal($cartItems);
        $taxRate = $settings->get('tax_rate', 13);
        $taxEnabled = $settings->get('tax_enabled', true);
        $taxAmount = $taxEnabled ? ($subtotal * $taxRate / 100) : 0;
        
        // Get discount if applied
        $discountId = null;
        $discountAmount = 0;
        if (session()->has('discount')) {
            $discountId = session('discount.id');
            $discountAmount = session('discount.amount');
        }
        
        // Calculate total
        $total = $subtotal + $taxAmount - $discountAmount;
        
        // Create the order
        $order = $this->createOrder($request, $subtotal, $taxAmount, $discountId, $discountAmount, $total);
        
        // Create order items
        $this->createOrderItems($order, $cartItems);
        
        // Process payment if using credit card
        if ($request->payment_method === 'credit_card') {
            try {
                // Get Stripe secret key from settings
                $stripeSecretKey = $settings->get('stripe_secret_key');
                
                // Ensure Stripe secret key is set
                if (empty($stripeSecretKey)) {
                    throw new \Exception('Payment configuration error. Please contact support.');
                }
                
                // Set your Stripe secret key
                \Stripe\Stripe::setApiKey($stripeSecretKey);
                
                // Create a PaymentIntent
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => round($total * 100), // Amount in cents
                    'currency' => 'cad',
                    'description' => 'Order #' . $order->order_number,
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ],
                    'payment_method' => $request->payment_method_id,
                    'confirm' => true,
                ]);
                
                if ($paymentIntent->status === 'succeeded') {
                    $order->payment_status = 'paid';
                    $order->payment_id = $paymentIntent->id;
                    $order->save();
                } else {
                    throw new \Exception('Payment processing failed.');
                }
            } catch (\Exception $e) {
                // Log the error
                \Log::error('Stripe payment failed: ' . $e->getMessage());
                
                // Delete the order
                $order->delete();
                
                // Redirect back with error
                return redirect()->route('checkout.index')
                    ->with('error', 'Payment processing failed: ' . $e->getMessage());
            }
        }
        
        // Send confirmation emails
        $this->sendOrderConfirmationEmails($order);
        
        // Clear the cart
        session()->forget(['cart', 'discount']);
        
        // Redirect to confirmation page
        return redirect()->route('checkout.confirmation', ['order' => $order->id]);
    }
    
    /**
     * Show the order confirmation page.
     */
    public function confirmation(Order $order)
    {
        return view('checkout.confirmation', compact('order'));
    }
    
    /**
     * Validate the checkout request.
     */
    private function validateCheckout(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|string',
            'payment_method' => 'required|in:credit_card,pay_in_store',
        ];
        
        // Add payment method ID validation if credit card is selected
        if ($request->payment_method === 'credit_card') {
            $rules['payment_method_id'] = 'required|string';
        }
        
        return $request->validate($rules);
    }
    
    /**
     * Calculate subtotal from cart items.
     */
    private function calculateSubtotal($cartItems)
    {
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['unit_price'] * $item['quantity'];
        }
        return $subtotal;
    }
    
    /**
     * Create a new order.
     */
    private function createOrder($request, $subtotal, $taxAmount, $discountId, $discountAmount, $total)
    {
        $order = new Order();
        
        // Customer information
        $order->customer_name = $request->name;
        $order->customer_email = $request->email;
        $order->customer_phone = $request->phone;
        
        // Order details
        $order->order_number = $this->generateOrderNumber();
        $order->order_status = 'pending';
        $order->payment_method = $request->payment_method;
        $order->payment_status = $request->payment_method === 'pay_in_store' ? 'pending' : 'pending';
        
        // Financial details
        $order->subtotal = $subtotal;
        $order->tax_amount = $taxAmount;
        $order->discount_id = $discountId;
        $order->discount_amount = $discountAmount;
        $order->total = $total;
        
        // Pickup details
        $order->pickup_time = $request->pickup_date . ' ' . $request->pickup_time . ':00';
        
        $order->save();
        
        return $order;
    }
    
    /**
     * Create order items from cart items.
     */
    private function createOrderItems($order, $cartItems)
    {
        foreach ($cartItems as $item) {
            $orderItem = new OrderItem();
            
            $orderItem->order_id = $order->id;
            $orderItem->item_type = $item['type'];
            $orderItem->item_id = $item['id'];
            $orderItem->name = $item['name'];
            $orderItem->size = $item['size'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->unit_price = $item['unit_price'];
            $orderItem->subtotal = $item['unit_price'] * $item['quantity'];
            $orderItem->is_upsell = $item['is_upsell'] ?? false;
            
            // Store ALL options as JSON - include everything from the cart item options
            $options = $item['options'] ?? [];
            
            // Add some standard fields if they don't exist in options already
            if (!isset($options['size']) && isset($item['size'])) {
                $options['size'] = $item['size'];
            }
            
            if (isset($item['notes']) && !empty($item['notes'])) {
                $options['notes'] = $item['notes'];
            }
            
            $orderItem->options = json_encode($options);
            
            $orderItem->save();
        }
    }
    
    /**
     * Generate a unique order number.
     */
    private function generateOrderNumber()
    {
        $prefix = 'PISA-';
        $date = date('Ymd');
        $random = strtoupper(Str::random(4));
        
        return $prefix . $date . '-' . $random;
    }
    
    /**
     * Send order confirmation emails.
     */
    private function sendOrderConfirmationEmails(Order $order)
    {
        try {
            // Send customer confirmation email
            \Mail::to($order->customer_email)->send(new OrderConfirmation($order));
            
            // Send admin notification email to admins
            EmailHelper::sendToAdmins(new NewOrderNotification($order));
        } catch (\Exception $e) {
            // Log the error but don't stop the process
            \Log::error('Failed to send order confirmation emails: ' . $e->getMessage());
        }
    }
}
