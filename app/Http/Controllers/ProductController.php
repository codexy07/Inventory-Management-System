<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Http\Requests\ProductRequest;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'supplier')
            ->when(request('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('category_id'), function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->paginate(15);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(ProductRequest $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        ActivityLogger::created($product);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $data = $request->validated();
        $original = $product->getOriginal();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        ActivityLogger::updated($product, $original);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            ActivityLogger::deleted($product);
        } catch (\Exception $e) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot delete this product. It may have associated stock transactions.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
