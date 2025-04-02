<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ToppingController extends Controller
{
    /**
     * Display a listing of the toppings.
     */
    public function index(Request $request)
    {
        $query = Topping::query();
        
        // Apply filters
        if ($request->filled('type')) {
            $query->where('category', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Get results with pagination
        $toppings = $query->orderBy('category')->orderBy('name')->paginate(15)->withQueryString();
        
        return view('admin.toppings.index', compact('toppings'));
    }

    /**
     * Show the form for creating a new topping.
     */
    public function create()
    {
        return view('admin.toppings.create');
    }

    /**
     * Store a newly created topping in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateTopping($request);
        
        $topping = new Topping();
        $topping->name = $validated['name'];
        $topping->category = $validated['category'];
        $topping->counts_as = $validated['counts_as'] ?? 1;
        $topping->price_factor = $validated['price_factor'] ?? 1.0;
        $topping->display_order = $validated['display_order'] ?? 0;
        $topping->is_active = $validated['is_active'] ?? true;
        
        $topping->save();
        
        return redirect()->route('admin.toppings.index')
            ->with('success', 'Topping created successfully.');
    }

    /**
     * Show the form for editing the specified topping.
     */
    public function edit(Topping $topping)
    {
        return view('admin.toppings.edit', compact('topping'));
    }

    /**
     * Update the specified topping in storage.
     */
    public function update(Request $request, Topping $topping)
    {
        $validated = $this->validateTopping($request, $topping->id);
        
        $topping->name = $validated['name'];
        $topping->category = $validated['category'];
        $topping->counts_as = $validated['counts_as'] ?? 1;
        $topping->price_factor = $validated['price_factor'] ?? 1.0;
        $topping->display_order = $validated['display_order'] ?? 0;
        $topping->is_active = $validated['is_active'] ?? true;
        
        $topping->save();
        
        return redirect()->route('admin.toppings.index')
            ->with('success', 'Topping updated successfully.');
    }

    /**
     * Remove the specified topping from storage.
     */
    public function destroy(Topping $topping)
    {
        // Check if the topping is used in any products
        $productCount = $topping->products()->count();
        
        if ($productCount > 0) {
            return redirect()->route('admin.toppings.index')
                ->with('error', "Cannot delete topping. It is used in {$productCount} products.");
        }
        
        $topping->delete();
        
        return redirect()->route('admin.toppings.index')
            ->with('success', 'Topping deleted successfully.');
    }
    
    /**
     * Validate the topping data.
     */
    private function validateTopping(Request $request, $id = null)
    {
        $categories = ['meat', 'veggie', 'cheese', 'sauce', 'spice'];
        
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $id ? Rule::unique('toppings')->ignore($id) : Rule::unique('toppings')
            ],
            'category' => [
                'required',
                'string',
                Rule::in($categories)
            ],
            'counts_as' => 'nullable|integer|min:1|max:3',
            'price_factor' => 'nullable|numeric|min:0.5|max:3',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
    }
} 