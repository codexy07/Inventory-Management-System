<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        return view('categories.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = Category::create($validated);

        ActivityLogger::created($category);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $original = $category->getOriginal();
        $category->update($validated);

        ActivityLogger::updated($category, $original);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            // Unassign products from this category before deleting
            $category->products()->update(['category_id' => null]);
            $category->delete();
            ActivityLogger::deleted($category);
        } catch (\Exception $e) {
            return redirect()->route('categories.index')
                ->with('error', 'Failed to delete category. It may have associated products or records.');
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
