<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('display_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        
        $category = new Category();
        $category->name = $validated['name'];
        $category->slug = Str::slug($validated['name']);
        $category->description = $validated['description'];
        $category->is_active = $validated['is_active'] ?? false;
        $category->display_order = $validated['display_order'] ?? 0;
        $category->day_specific = $validated['day_specific'] ?? false;
        $category->specific_day = $validated['specific_day'] ?? null;
        
        $category->save();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $this->validateCategory($request, $category->id);
        
        $category->name = $validated['name'];
        $category->slug = Str::slug($validated['name']);
        $category->description = $validated['description'];
        $category->is_active = $validated['is_active'] ?? false;
        $category->display_order = $validated['display_order'] ?? 0;
        $category->day_specific = $validated['day_specific'] ?? false;
        $category->specific_day = $validated['specific_day'] ?? null;
        
        $category->save();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if the category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with existing products.');
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
    
    /**
     * Validate the category data.
     */
    private function validateCategory(Request $request, $id = null)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $id ? Rule::unique('categories')->ignore($id) : Rule::unique('categories')
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
            'day_specific' => 'boolean',
            'specific_day' => [
                'nullable',
                'string',
                Rule::in($days)
            ],
        ]);
    }
} 