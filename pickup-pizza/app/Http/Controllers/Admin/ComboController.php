<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ComboController extends Controller
{
    /**
     * Display a listing of the combos.
     */
    public function index()
    {
        $combos = Combo::with('category')->orderBy('name')->paginate(10);
        return view('admin.combos.index', compact('combos'));
    }

    /**
     * Show the form for creating a new combo.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        return view('admin.combos.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created combo in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:combos',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'combo_items' => 'required|array|min:1',
            'combo_items.*.product_id' => 'required|exists:products,id',
            'combo_items.*.quantity' => 'required|integer|min:1',
            'combo_items.*.is_optional' => 'nullable|boolean',
            'combo_items.*.additional_price' => 'nullable|numeric|min:0',
            'has_upsells' => 'nullable|boolean',
            'upsell_product_id' => 'nullable|exists:products,id',
            'upsell_price' => 'nullable|numeric|min:0',
            'upsell_message' => 'nullable|string|max:255',
        ]);

        // Create combo
        $combo = new Combo();
        $combo->name = $validated['name'];
        $combo->slug = Str::slug($validated['name']);
        $combo->description = $validated['description'] ?? null;
        $combo->category_id = $validated['category_id'];
        $combo->price = $validated['price'];
        $combo->sort_order = $validated['display_order'] ?? 0;
        $combo->active = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('combos', 'public');
            $combo->image = $imagePath;
        }

        // Handle upsell data
        if ($request->has('has_upsells') && $request->has('upsell_product_id')) {
            $upsellData = [
                'product_id' => $request->upsell_product_id,
                'price' => $request->upsell_price ?? null,
                'message' => $request->upsell_message ?? 'Add for just $X more?'
            ];
            $combo->add_ons = json_encode(['upsell' => $upsellData]);
        }

        $combo->save();

        // Handle combo items
        foreach ($request->combo_items as $item) {
            $combo->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'is_optional' => isset($item['is_optional']),
                'additional_price' => $item['additional_price'] ?? 0,
                'max_toppings' => $item['max_toppings'] ?? null,
                'size' => $item['size'] ?? null,
            ]);
        }

        return redirect()->route('admin.combos.index')
            ->with('success', 'Combo created successfully.');
    }

    /**
     * Display the specified combo.
     */
    public function show(Combo $combo)
    {
        $combo->load(['category', 'products']);
        return view('admin.combos.show', compact('combo'));
    }

    /**
     * Show the form for editing the specified combo.
     */
    public function edit(Combo $combo)
    {
        $combo->load(['products']);
        $categories = Category::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        
        // Get upsell data
        $upsellData = null;
        $addOns = json_decode($combo->add_ons, true);
        if ($addOns && isset($addOns['upsell'])) {
            $upsellData = $addOns['upsell'];
        }
        
        return view('admin.combos.edit', compact('combo', 'categories', 'products', 'upsellData'));
    }

    /**
     * Update the specified combo in storage.
     */
    public function update(Request $request, Combo $combo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:combos,name,' . $combo->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'combo_items' => 'required|array|min:1',
            'combo_items.*.product_id' => 'required|exists:products,id',
            'combo_items.*.quantity' => 'required|integer|min:1',
            'combo_items.*.is_optional' => 'nullable|boolean',
            'combo_items.*.additional_price' => 'nullable|numeric|min:0',
            'has_upsells' => 'nullable|boolean',
            'upsell_product_id' => 'nullable|exists:products,id',
            'upsell_price' => 'nullable|numeric|min:0',
            'upsell_message' => 'nullable|string|max:255',
        ]);

        // Update combo
        $combo->name = $validated['name'];
        $combo->slug = Str::slug($validated['name']);
        $combo->description = $validated['description'] ?? null;
        $combo->category_id = $validated['category_id'];
        $combo->price = $validated['price'];
        $combo->sort_order = $validated['display_order'] ?? 0;
        $combo->active = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($combo->image) {
                Storage::disk('public')->delete($combo->image);
            }
            
            $imagePath = $request->file('image')->store('combos', 'public');
            $combo->image = $imagePath;
        }

        // Handle upsell data
        if ($request->has('has_upsells') && $request->has('upsell_product_id')) {
            $upsellData = [
                'product_id' => $request->upsell_product_id,
                'price' => $request->upsell_price ?? null,
                'message' => $request->upsell_message ?? 'Add for just $X more?'
            ];
            $combo->add_ons = json_encode(['upsell' => $upsellData]);
        } else {
            $combo->add_ons = null;
        }

        $combo->save();

        // Handle combo items - detach all existing and reattach
        $combo->products()->detach();
        
        foreach ($request->combo_items as $item) {
            $combo->products()->attach($item['product_id'], [
                'quantity' => $item['quantity'],
                'is_optional' => isset($item['is_optional']),
                'additional_price' => $item['additional_price'] ?? 0,
                'max_toppings' => $item['max_toppings'] ?? null,
                'size' => $item['size'] ?? null,
            ]);
        }

        return redirect()->route('admin.combos.index')
            ->with('success', 'Combo updated successfully.');
    }

    /**
     * Remove the specified combo from storage.
     */
    public function destroy(Combo $combo)
    {
        // Delete image if exists
        if ($combo->image) {
            Storage::disk('public')->delete($combo->image);
        }
        
        // Delete combo
        $combo->products()->detach();
        $combo->delete();

        return redirect()->route('admin.combos.index')
            ->with('success', 'Combo deleted successfully.');
    }
} 