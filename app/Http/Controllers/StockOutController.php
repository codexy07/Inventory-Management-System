<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOut;
use App\Http\Requests\StockOutRequest;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index()
    {
        $transactions = StockOut::with('product')
            ->when(request('search'), function ($query, $search) {
                return $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('stock.out.index', compact('transactions'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $products = Product::where('quantity', '>', 0)->orderBy('name')->get();
        return view('stock.out.create', compact('products'));
    }

    public function store(StockOutRequest $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            DB::transaction(function () use ($request) {
                $product = Product::where('id', $request->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->quantity < $request->quantity) {
                    throw new \Exception(
                        "Insufficient stock. Only {$product->quantity} units available for {$product->name}."
                    );
                }

                $stockOut = StockOut::create($request->validated());

                $product->decrement('quantity', $request->quantity);

                ActivityLogger::created($stockOut, "Stocked out {$request->quantity} units of \"{$product->name}\"");
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()->route('stock.out.index')
            ->with('success', 'Stock removed successfully.');
    }

    public function edit(StockOut $stockOut)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $products = Product::orderBy('name')->get();
        return view('stock.out.edit', compact('stockOut', 'products'));
    }

    public function update(StockOutRequest $request, StockOut $stockOut)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            DB::transaction(function () use ($request, $stockOut) {
                $oldQuantity = $stockOut->quantity;
                $newQuantity = $request->quantity;
                $diff = $oldQuantity - $newQuantity; // positive = restoring stock, negative = removing more

                $original = $stockOut->getOriginal();

                $product = Product::where('id', $stockOut->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($diff < 0) {
                    $extraNeeded = abs($diff);
                    if ($product->quantity < $extraNeeded) {
                        throw new \Exception(
                            "Insufficient stock. Only {$product->quantity} units available, but need {$extraNeeded} more."
                        );
                    }
                }

                $stockOut->update($request->validated());

                if ($diff > 0) {
                    $product->increment('quantity', $diff);
                } elseif ($diff < 0) {
                    $product->decrement('quantity', abs($diff));
                }

                ActivityLogger::updated($stockOut, $original);
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()->route('stock.out.index')
            ->with('success', 'Stock-out record updated successfully.');
    }

    public function destroy(StockOut $stockOut)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            DB::transaction(function () use ($stockOut) {
                $stockOut->load('product');

                Product::where('id', $stockOut->product_id)
                    ->increment('quantity', $stockOut->quantity);

                $stockOut->delete();
                ActivityLogger::deleted($stockOut);
            });
        } catch (\Exception $e) {
            return redirect()->route('stock.out.index')
                ->with('error', 'Failed to delete stock-out record. The product quantity could not be restored.');
        }

        return redirect()->route('stock.out.index')
            ->with('success', 'Stock-out record deleted. Product quantity restored.');
    }
}
