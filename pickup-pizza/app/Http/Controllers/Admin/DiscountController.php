<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DiscountController extends Controller
{
    /**
     * Display a listing of the discounts.
     */
    public function index()
    {
        $discounts = Discount::with(['products', 'categories'])
            ->latest()
            ->paginate(10);

        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new discount.
     */
    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('admin.discounts.create', compact('products', 'categories'));
    }

    /**
     * Store a newly created discount in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateDiscount($request);
        
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateUniqueCode();
        }
        
        $discount = Discount::create($validated);
        
        // Handle product restrictions
        if ($request->has('apply_to') && $request->apply_to === 'specific_products') {
            $discount->products()->sync($request->products ?? []);
        }
        
        // Handle category restrictions
        if ($request->has('apply_to') && $request->apply_to === 'specific_categories') {
            $discount->categories()->sync($request->categories ?? []);
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Discount code created successfully!');
    }

    /**
     * Display the specified discount.
     */
    public function show(Discount $discount)
    {
        $discount->load(['products', 'categories', 'orders']);
        
        // Get usage statistics
        $usageStats = [
            'total' => $discount->orders->count(),
            'thisMonth' => $discount->orders->filter(function ($order) {
                return $order->created_at->isCurrentMonth();
            })->count(),
            'lastMonth' => $discount->orders->filter(function ($order) {
                return $order->created_at->isLastMonth();
            })->count(),
            'totalSavings' => $discount->orders->sum('discount_amount')
        ];
        
        return view('admin.discounts.show', compact('discount', 'usageStats'));
    }

    /**
     * Show the form for editing the specified discount.
     */
    public function edit(Discount $discount)
    {
        $products = Product::active()->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        $selectedProducts = $discount->products->pluck('id')->toArray();
        $selectedCategories = $discount->categories->pluck('id')->toArray();
        
        return view('admin.discounts.edit', compact(
            'discount',
            'products',
            'categories',
            'selectedProducts',
            'selectedCategories'
        ));
    }

    /**
     * Update the specified discount in storage.
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $this->validateDiscount($request, $discount->id);
        
        $discount->update($validated);
        
        // Handle product restrictions
        if ($request->has('apply_to')) {
            if ($request->apply_to === 'specific_products') {
                $discount->products()->sync($request->products ?? []);
                $discount->categories()->detach();
            } elseif ($request->apply_to === 'specific_categories') {
                $discount->categories()->sync($request->categories ?? []);
                $discount->products()->detach();
            } else {
                // All products
                $discount->products()->detach();
                $discount->categories()->detach();
            }
        }

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Discount code updated successfully!');
    }

    /**
     * Remove the specified discount from storage.
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Discount code deleted successfully!');
    }
    
    /**
     * Validate the discount form data.
     */
    private function validateDiscount(Request $request, $discountId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('discounts')->ignore($discountId),
            ],
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_customer' => 'nullable|integer|min:1',
            'apply_to' => 'nullable|in:all_products,specific_products,specific_categories',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'active' => 'sometimes|boolean',
        ];
        
        return $request->validate($rules);
    }
    
    /**
     * Generate a unique discount code.
     */
    private function generateUniqueCode()
    {
        $attempts = 0;
        $maxAttempts = 10;
        
        do {
            $code = strtoupper(Str::random(8));
            $codeExists = Discount::where('code', $code)->exists();
            $attempts++;
        } while ($codeExists && $attempts < $maxAttempts);
        
        if ($codeExists) {
            // If we still couldn't generate a unique code, add a timestamp
            $code = strtoupper(Str::random(6) . time());
        }
        
        return $code;
    }
} 