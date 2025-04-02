<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductExtraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $extras = ProductExtra::with('category')
            ->orderBy('name')
            ->paginate(10);
        
        return view('admin.extras.index', compact('extras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        
        return view('admin.extras.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'sometimes|boolean',
            'max_quantity' => 'required|integer|min:1|max:10',
            'is_default' => 'sometimes|boolean',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('extras', 'public');
            $validated['image'] = $imagePath;
        }

        // Set boolean values
        $validated['active'] = $request->has('active');
        $validated['is_default'] = $request->has('is_default');

        // Create the product extra
        $productExtra = ProductExtra::create($validated);

        // Attach products if provided
        if ($request->has('product_ids')) {
            $productExtra->products()->attach($request->product_ids);
        }

        return redirect()->route('admin.extras.index')
            ->with('success', 'Product extra created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductExtra $extra)
    {
        $extra->load('category', 'products');
        
        return view('admin.extras.show', compact('extra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductExtra $extra)
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $selectedProducts = $extra->products->pluck('id')->toArray();
        
        return view('admin.extras.edit', compact('extra', 'categories', 'products', 'selectedProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductExtra $extra)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'sometimes|boolean',
            'max_quantity' => 'required|integer|min:1|max:10',
            'is_default' => 'sometimes|boolean',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($extra->image) {
                Storage::disk('public')->delete($extra->image);
            }
            
            $imagePath = $request->file('image')->store('extras', 'public');
            $validated['image'] = $imagePath;
        }

        // Set boolean values
        $validated['active'] = $request->has('active');
        $validated['is_default'] = $request->has('is_default');

        // Update the product extra
        $extra->update($validated);

        // Sync products
        if ($request->has('product_ids')) {
            $extra->products()->sync($request->product_ids);
        } else {
            $extra->products()->detach();
        }

        return redirect()->route('admin.extras.index')
            ->with('success', 'Product extra updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductExtra $extra)
    {
        // Delete image if exists
        if ($extra->image) {
            Storage::disk('public')->delete($extra->image);
        }
        
        // Delete the product extra
        $extra->delete();
        
        return redirect()->route('admin.extras.index')
            ->with('success', 'Product extra deleted successfully.');
    }

    /**
     * Bulk toggle active status
     */
    public function bulkToggleActive(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:product_extras,id',
            'active' => 'required|boolean',
        ]);

        $count = ProductExtra::whereIn('id', $validated['ids'])
            ->update(['active' => $validated['active']]);

        $status = $validated['active'] ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.extras.index')
            ->with('success', "$count product extras $status successfully.");
    }

    /**
     * Get product extras by category for AJAX request
     */
    public function getByCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $extras = ProductExtra::active()
            ->byCategory($request->category_id)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'max_quantity', 'is_default']);

        return response()->json($extras);
    }
} 