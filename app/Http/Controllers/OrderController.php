<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('product', 'user');

        if ($search = $request->input('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->where('quantity', '>', 0)->orderBy('name')->get();
        return view('orders.create', compact('products'));
    }

    public function store(OrderRequest $request)
    {
        $validated = $request->validated();

        $product = Product::where('id', $validated['product_id'])
            ->lockForUpdate()
            ->firstOrFail();

        if ($product->quantity < $validated['quantity']) {
            return back()->withInput()->withErrors([
                'quantity' => "Insufficient stock. Only {$product->quantity} units available for {$product->name}."
            ]);
        }

        try {
            DB::transaction(function () use ($validated, $product) {
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $validated['quantity'],
                    'status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                ]);

                $product->decrement('quantity', $validated['quantity']);

                ActivityLogger::created($order, "Order #{$order->id} created for {$validated['quantity']} units of \"{$product->name}\"");
            });
        } catch (\Exception $e) {
            return redirect()->route('orders.index')
                ->with('error', 'Failed to create order. ' . $e->getMessage());
        }

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load('product', 'user');
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
        $products = Product::where('status', 'active')->orderBy('name')->get();
        return view('orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $original = $order->getOriginal();

        try {
            DB::transaction(function () use ($order, $validated) {
                $oldStatus = $order->status;
                $newStatus = $validated['status'];

                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    Product::where('id', $order->product_id)
                        ->increment('quantity', $order->quantity);
                } elseif ($newStatus !== 'cancelled' && $oldStatus === 'cancelled') {
                    $product = Product::where('id', $order->product_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($product->quantity < $order->quantity) {
                        throw new \Exception("Insufficient stock to re-activate order. Only {$product->quantity} units available.");
                    }

                    $product->decrement('quantity', $order->quantity);
                }

                $order->update([
                    'status' => $newStatus,
                    'notes' => $validated['notes'] ?? $order->notes,
                ]);

                ActivityLogger::updated($order, $original, "Order #{$order->id} status changed from \"{$oldStatus}\" to \"{$newStatus}\"");
            });
        } catch (\Exception $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            DB::transaction(function () use ($order) {
                if ($order->status !== 'cancelled') {
                    Product::where('id', $order->product_id)
                        ->increment('quantity', $order->quantity);
                }

                $order->delete();
                ActivityLogger::deleted($order, "Order #{$order->id} deleted");
            });
        } catch (\Exception $e) {
            return redirect()->route('orders.index')
                ->with('error', 'Failed to delete order.');
        }

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function invoice(Order $order)
    {
        if (!auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $order->load('product', 'user');
        $pdf = Pdf::loadView('orders.invoice', compact('order'));
        return $pdf->download("invoice-order-{$order->id}.pdf");
    }
}
