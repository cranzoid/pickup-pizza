<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    /**
     * Display the raw options for a specific order.
     */
    public function viewOrderOptions($id)
    {
        $order = Order::with('items')->findOrFail($id);
        
        // Process options for each item for easier viewing
        foreach ($order->items as $item) {
            $item->decoded_options = json_decode($item->options, true) ?: [];
            
            // If there are wing flavors, add a lookup
            if (isset($item->decoded_options['wing_flavors']) && is_numeric($item->decoded_options['wing_flavors'])) {
                $wingFlavors = [
                    '1' => 'Plain',
                    '2' => 'Mild',
                    '3' => 'Medium',
                    '4' => 'Hot',
                    '5' => 'Suicide',
                    '6' => 'Honey Garlic',
                    '7' => 'BBQ',
                    '8' => 'Sweet & Sour',
                    '9' => 'Honey Hot',
                    '10' => 'Dry Cajun'
                ];
                
                $flavorId = $item->decoded_options['wing_flavors'];
                if (isset($wingFlavors[$flavorId])) {
                    $item->decoded_options['wing_flavor_name'] = $wingFlavors[$flavorId];
                }
            }
        }
        
        // Return a simple view with all the debug data
        return view('admin.debug.order_options', compact('order'));
    }
}
