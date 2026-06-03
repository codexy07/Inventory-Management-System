<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\SupplierRequest;
use App\Helpers\ActivityLogger;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::select('suppliers.*')
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(DISTINCT product_id)')
                    ->from('stock_in')
                    ->whereColumn('supplier_id', 'suppliers.id');
            }, 'products_count')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        return view('suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $supplier = Supplier::create($request->validated());

        ActivityLogger::created($supplier);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $original = $supplier->getOriginal();
        $supplier->update($request->validated());

        ActivityLogger::updated($supplier, $original);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            $supplier->delete();
            ActivityLogger::deleted($supplier);
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Cannot delete this supplier. It may have associated stock transactions.');
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
