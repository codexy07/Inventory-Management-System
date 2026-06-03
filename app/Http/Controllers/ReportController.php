<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function inventory()
    {
        $products = Product::with('category', 'supplier')->orderBy('name')->paginate(15);
        $grandTotal = Product::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0;

        return view('reports.inventory', compact('products', 'grandTotal'));
    }

    public function history(Request $request)
    {
        $products = Product::orderBy('name')->get();

        $stockInsQuery = StockIn::with('product', 'supplier');
        $stockOutsQuery = StockOut::with('product');

        if ($request->filled('product_id')) {
            $stockInsQuery->where('product_id', $request->product_id);
            $stockOutsQuery->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from')) {
            $stockInsQuery->where('date', '>=', $request->date_from);
            $stockOutsQuery->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $stockInsQuery->where('date', '<=', $request->date_to);
            $stockOutsQuery->where('date', '<=', $request->date_to);
        }

        $stockIns = $stockInsQuery->latest('date')->get()->map(function ($item) {
            $item->type = 'Stock In';
            $item->supplier_name = $item->supplier?->name ?? 'N/A';
            return $item;
        });

        $stockOuts = $stockOutsQuery->latest('date')->get()->map(function ($item) {
            $item->type = 'Stock Out';
            $item->supplier_name = '-';
            return $item;
        });

        $transactions = $stockIns->concat($stockOuts)
            ->sortByDesc('date')
            ->values();

        return view('reports.history', compact('products', 'transactions'));
    }

    public function lowStock()
    {
        $products = Product::with('category', 'supplier')
            ->where('quantity', '<=', \DB::raw('reorder_level'))
            ->orderBy('quantity')
            ->paginate(10);

        return view('reports.lowstock', compact('products'));
    }

    public function sales()
    {
        $orders = Order::with('product', 'user')
            ->where('status', 'completed')
            ->latest()
            ->paginate(15);

        $totalRevenue = Order::where('status', 'completed')->sum('total_price');
        $totalOrders = Order::where('status', 'completed')->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $monthlySales = Order::where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_price) as revenue, COUNT(*) as order_count')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit(12)
            ->get();

        return view('reports.sales', compact('orders', 'totalRevenue', 'totalOrders', 'avgOrderValue', 'monthlySales'));
    }

    public function products()
    {
        $products = Product::with('category', 'supplier')
            ->selectRaw('products.*, (SELECT COALESCE(SUM(quantity), 0) FROM stock_in WHERE product_id = products.id) as total_stock_in, (SELECT COALESCE(SUM(quantity), 0) FROM stock_out WHERE product_id = products.id) as total_stock_out')
            ->orderBy('name')
            ->paginate(15);

        return view('reports.products', compact('products'));
    }

    public function exportPdf(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $type = $request->get('type', 'inventory');
        $filename = "{$type}-report-" . now()->format('Y-m-d') . '.pdf';

        if ($type === 'inventory') {
            $products = Product::with('category', 'supplier')->orderBy('name')->get();
            $grandTotal = $products->sum(fn ($p) => $p->quantity * $p->price);
            $pdf = Pdf::loadView('reports.pdf', compact('products', 'grandTotal'));
        } elseif ($type === 'lowstock') {
            $products = Product::with('category', 'supplier')
                ->where('quantity', '<=', \DB::raw('reorder_level'))
                ->orderBy('quantity')
                ->get();
            $pdf = Pdf::loadView('reports.pdf-lowstock', compact('products'));
        } else {
            $products = Product::with('category', 'supplier')->orderBy('name')->get();
            $grandTotal = $products->sum(fn ($p) => $p->quantity * $p->price);
            $pdf = Pdf::loadView('reports.pdf', compact('products', 'grandTotal'));
        }

        return $pdf->download($filename);
    }

    public function exportCsv(Request $request)
    {
        $type = $request->get('type', 'inventory');
        $filename = "{$type}-report-" . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');

            if ($type === 'inventory') {
                fputcsv($file, ['ID', 'Product Name', 'SKU', 'Category', 'Supplier', 'Quantity', 'Price', 'Total Value', 'Status', 'Location']);
                Product::with('category', 'supplier')->orderBy('name')->each(function ($product) use ($file) {
                    fputcsv($file, [
                        $product->id,
                        $product->name,
                        $product->sku ?? '',
                        $product->category?->name ?? '',
                        $product->supplier?->name ?? '',
                        $product->quantity,
                        $product->price,
                        $product->quantity * $product->price,
                        $product->status,
                        $product->warehouse_location ?? '',
                    ]);
                });
            } elseif ($type === 'orders') {
                fputcsv($file, ['Order ID', 'Customer', 'Product', 'Quantity', 'Unit Price', 'Total', 'Status', 'Date']);
                Order::with('product', 'user')->latest()->each(function ($order) use ($file) {
                    fputcsv($file, [
                        $order->id,
                        $order->user->name,
                        $order->product->name ?? 'N/A',
                        $order->quantity,
                        $order->unit_price,
                        $order->total_price,
                        $order->status,
                        $order->created_at->format('Y-m-d'),
                    ]);
                });
            } elseif ($type === 'lowstock') {
                fputcsv($file, ['ID', 'Product Name', 'SKU', 'Category', 'Quantity', 'Reorder Level', 'Status']);
                Product::with('category')
                    ->where('quantity', '<=', \DB::raw('reorder_level'))
                    ->orderBy('quantity')
                    ->each(function ($product) use ($file) {
                        fputcsv($file, [
                            $product->id,
                            $product->name,
                            $product->sku ?? '',
                            $product->category?->name ?? '',
                            $product->quantity,
                            $product->reorder_level,
                            $product->quantity <= 0 ? 'Out of Stock' : 'Low Stock',
                        ]);
                    });
            } elseif ($type === 'sales') {
                fputcsv($file, ['Order ID', 'Customer', 'Product', 'Quantity', 'Unit Price', 'Total', 'Date']);
                Order::with('product', 'user')
                    ->where('status', 'completed')
                    ->latest()
                    ->each(function ($order) use ($file) {
                        fputcsv($file, [
                            $order->id,
                            $order->user->name,
                            $order->product->name ?? 'N/A',
                            $order->quantity,
                            $order->unit_price,
                            $order->total_price,
                            $order->created_at->format('Y-m-d'),
                        ]);
                    });
            } elseif ($type === 'products') {
                fputcsv($file, ['ID', 'Product Name', 'SKU', 'Category', 'Supplier', 'Quantity', 'Price', 'Stock In', 'Stock Out', 'Status']);
                Product::with('category', 'supplier')
                    ->selectRaw('products.*, (SELECT COALESCE(SUM(quantity), 0) FROM stock_in WHERE product_id = products.id) as total_stock_in, (SELECT COALESCE(SUM(quantity), 0) FROM stock_out WHERE product_id = products.id) as total_stock_out')
                    ->orderBy('name')
                    ->each(function ($product) use ($file) {
                        fputcsv($file, [
                            $product->id,
                            $product->name,
                            $product->sku ?? '',
                            $product->category?->name ?? '',
                            $product->supplier?->name ?? '',
                            $product->quantity,
                            $product->price,
                            $product->total_stock_in,
                            $product->total_stock_out,
                            $product->status,
                        ]);
                    });
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
