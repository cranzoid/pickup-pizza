<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Apply filters
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Get results with pagination
        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        
        // Get categories for filter dropdown
        $categories = Category::orderBy('name')->get();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'display_order' => 'nullable|integer|min:0',
            'price' => 'required_without:has_sizes|nullable|numeric|min:0',
            'has_sizes' => 'nullable|boolean',
            'sizes' => 'array',
            'sizes.*.active' => 'boolean',
            'sizes.*.price' => 'nullable|numeric|min:0',
            'sizes.*.description' => 'nullable|string|max:255',
            'has_toppings' => 'nullable|boolean',
            'max_toppings' => 'nullable|integer|min:0',
            'free_toppings' => 'nullable|integer|min:0',
            'has_extras' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);
        
        // Create new product
        $product = new Product();
        $product->name = $validated['name'];
        $product->slug = Str::slug($validated['name']);
        $product->description = $validated['description'] ?? null;
        $product->category_id = $validated['category_id'];
        $product->sort_order = $validated['display_order'] ?? 0;
        $product->active = $request->has('is_active');
        
        // Handle sizes
        $product->has_size_options = $request->has('has_sizes');
        
        if (!$product->has_size_options) {
            $product->price = $validated['price'];
        } else {
            // Process sizes
            $sizes = [];
            foreach ($request->sizes as $key => $size) {
                if (!empty($size['active'])) {
                    $sizes[$key] = [
                        'price' => (float) $size['price'],
                        'description' => $size['description'] ?? '',
                    ];
                }
            }
            $product->sizes = $sizes;
        }
        
        // Handle topping options
        $product->has_toppings = $request->has('has_toppings');
        if ($product->has_toppings) {
            $product->max_toppings = $validated['max_toppings'] ?? 0;
            $product->free_toppings = $validated['free_toppings'] ?? 0;
        }
        
        // Handle extras options
        $product->has_extras = $request->has('has_extras');
        
        // Handle featured status
        $product->is_featured = $request->has('is_featured');
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $product->image_path = $request->file('image')->store('products', 'public');
        }
        
        $product->save();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'display_order' => 'nullable|integer|min:0',
            'price' => 'required_without:has_sizes|nullable|numeric|min:0',
            'has_sizes' => 'nullable|boolean',
            'sizes' => 'array',
            'sizes.*.active' => 'boolean',
            'sizes.*.price' => 'nullable|numeric|min:0',
            'sizes.*.description' => 'nullable|string|max:255',
            'has_toppings' => 'nullable|boolean',
            'max_toppings' => 'nullable|integer|min:0',
            'free_toppings' => 'nullable|integer|min:0',
            'has_extras' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);
        
        // Update product
        $product->name = $validated['name'];
        
        // Only update slug if name changed
        if ($product->isDirty('name')) {
            $product->slug = Str::slug($validated['name']);
        }
        
        $product->description = $validated['description'] ?? null;
        $product->category_id = $validated['category_id'];
        $product->sort_order = $validated['display_order'] ?? 0;
        $product->active = $request->has('is_active');
        
        // Handle sizes
        $product->has_size_options = $request->has('has_sizes');
        
        if (!$product->has_size_options) {
            $product->price = $validated['price'];
            $product->sizes = null;
        } else {
            // Process sizes
            $sizes = [];
            foreach ($request->sizes as $key => $size) {
                if (!empty($size['active'])) {
                    $sizes[$key] = [
                        'price' => (float) $size['price'],
                        'description' => $size['description'] ?? '',
                    ];
                }
            }
            $product->sizes = $sizes;
        }
        
        // Handle topping options
        $product->has_toppings = $request->has('has_toppings');
        if ($product->has_toppings) {
            $product->max_toppings = $validated['max_toppings'] ?? 0;
            $product->free_toppings = $validated['free_toppings'] ?? 0;
        } else {
            $product->max_toppings = null;
            $product->free_toppings = null;
        }
        
        // Handle extras options
        $product->has_extras = $request->has('has_extras');
        
        // Handle featured status
        $product->is_featured = $request->has('is_featured');
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            
            $product->image_path = $request->file('image')->store('products', 'public');
        }
        
        $product->save();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Check if the product is used in any orders
        $orderItemsCount = $product->orderItems()->count();
        
        if ($orderItemsCount > 0) {
            return redirect()->route('admin.products.index')
                ->with('error', "Cannot delete product. It is used in {$orderItemsCount} orders.");
        }
        
        // Delete product image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
} 