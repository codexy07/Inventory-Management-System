<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockIn;
use App\Http\Requests\StockInRequest;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index()
    {
        $transactions = StockIn::with('product', 'supplier')
            ->when(request('search'), function ($query, $search) {
                return $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('stock.in.index', compact('transactions'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('stock.in.create', compact('products', 'suppliers'));
    }

    public function store(StockInRequest $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        try {
            DB::transaction(function () use ($request) {
                $stockIn = StockIn::create($request->validated());

                Product::where('id', $request->product_id)
                    ->increment('quantity', $request->quantity);

                $stockIn->load('product', 'supplier');
                $supplierName = $stockIn->supplier?->name ?? 'N/A';
                ActivityLogger::created($stockIn, "Stocked in {$request->quantity} units of \"{$stockIn->product->name}\" from {$supplierName}");
            });
        } catch (\Exception $e) {
            return redirect()->route('stock.in.index')
                ->with('error', 'Failed to add stock. ' . $e->getMessage());
        }

        return redirect()->route('stock.in.index')
            ->with('success', 'Stock added successfully.');
    }

    public function edit(StockIn $stockIn)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('stock.in.edit', compact('stockIn', 'products', 'suppliers'));
    }

    public function update(StockInRequest $request, StockIn $stockIn)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        DB::transaction(function () use ($request, $stockIn) {
            $oldQuantity = $stockIn->quantity;
            $newQuantity = $request->quantity;
            $diff = $newQuantity - $oldQuantity;

            $original = $stockIn->getOriginal();
            $stockIn->update($request->validated());

            if ($diff > 0) {
                Product::where('id', $stockIn->product_id)
                    ->increment('quantity', $diff);
            } elseif ($diff < 0) {
                Product::where('id', $stockIn->product_id)
                    ->decrement('quantity', abs($diff));
            }

            ActivityLogger::updated($stockIn, $original);
        });

        return redirect()->route('stock.in.index')
            ->with('success', 'Stock-in record updated successfully.');
    }

    public function destroy(StockIn $stockIn)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            DB::transaction(function () use ($stockIn) {
                $stockIn->load('product');

                Product::where('id', $stockIn->product_id)
                    ->decrement('quantity', $stockIn->quantity);

                $stockIn->delete();
                ActivityLogger::deleted($stockIn);
            });
        } catch (\Exception $e) {
            return redirect()->route('stock.in.index')
                ->with('error', 'Failed to delete stock-in record. The product quantity could not be adjusted.');
        }

        return redirect()->route('stock.in.index')
            ->with('success', 'Stock-in record deleted. Product quantity adjusted.');
    }
}
