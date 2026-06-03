<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // If admin, show admin dashboard
        if ($user && $user->isAdmin()) {
            return $this->adminDashboard();
        }

        // User dashboard (staff view)
        $totalProducts = Product::count();
        $totalQuantity = Product::sum('quantity');
        $totalValue = Product::select(DB::raw('SUM(quantity * price) as total'))->value('total') ?? 0;
        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSuppliers = Supplier::count();

        $lowStockProducts = Product::where('quantity', '<=', DB::raw('reorder_level'))
            ->orderBy('quantity')
            ->get();

        $topMovingProducts = Product::select('products.*', DB::raw('SUM(stock_out.quantity) as total_out'))
            ->leftJoin('stock_out', 'products.id', '=', 'stock_out.product_id')
            ->where('stock_out.date', '>=', now()->subDays(30))
            ->groupBy('products.id')
            ->orderByDesc('total_out')
            ->take(5)
            ->get();

        $recentStockIns = StockIn::with('product', 'supplier')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->type = 'Stock In';
                return $item;
            });

        $recentStockOuts = StockOut::with('product')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->type = 'Stock Out';
                return $item;
            });

        $recentTransactions = $recentStockIns->concat($recentStockOuts)
            ->sortByDesc('created_at')
            ->take(5);

        return view('dashboard', compact(
            'totalProducts',
            'totalQuantity',
            'totalValue',
            'totalOrders',
            'totalSuppliers',
            'lowStockProducts',
            'topMovingProducts',
            'recentTransactions'
        ));
    }

    public function adminDashboard()
    {
        $totalProducts = Product::count();
        $totalQuantity = Product::sum('quantity');
        $totalValue = Product::select(DB::raw('SUM(quantity * price) as total'))->value('total') ?? 0;
        $lowStockProducts = Product::where('quantity', '<=', DB::raw('reorder_level'))->count();
        $totalSuppliers = Supplier::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // Top moving products
        $topMovingProducts = Product::with('category')
            ->select('products.*', DB::raw('SUM(stock_out.quantity) as total_out'))
            ->leftJoin('stock_out', 'products.id', '=', 'stock_out.product_id')
            ->where('stock_out.date', '>=', now()->subDays(30))
            ->groupBy('products.id')
            ->orderByDesc('total_out')
            ->take(5)
            ->get();

        // Add stock_in counts
        $stockInCounts = StockIn::select('product_id', DB::raw('SUM(quantity) as stock_in_count'))
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('product_id')
            ->pluck('stock_in_count', 'product_id');

        foreach ($topMovingProducts as $product) {
            $product->stock_in_count = $stockInCounts[$product->id] ?? 0;
            $product->stock_out_count = $product->total_out ?? 0;
        }

        // Recent transactions
        $recentStockIns = StockIn::with('product', 'supplier')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->type = 'in';
                return $item;
            });

        $recentStockOuts = StockOut::with('product')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->type = 'out';
                return $item;
            });

        $recentTransactions = $recentStockIns->concat($recentStockOuts)
            ->sortByDesc('created_at')
            ->take(10);

        // Recent orders
        $recentOrders = Order::with('product', 'user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalProducts',
            'totalQuantity',
            'totalValue',
            'lowStockProducts',
            'topMovingProducts',
            'recentTransactions',
            'totalSuppliers',
            'totalCategories',
            'totalUsers',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalRevenue',
            'recentOrders'
        ));
    }
}
