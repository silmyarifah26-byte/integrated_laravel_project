<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the role-specific dashboard.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Admin & Staff Dashboard View
        if ($user->isAdmin() || $user->isStaff()) {
            $totalProducts = Product::count();
            $totalCategories = Category::count();
            $totalSuppliers = Supplier::count();

            // Today's completed sales (nominal & count)
            $todaySalesTotal = (float) Sale::where('status', 'completed')
                ->whereDate('sold_at', Carbon::today())
                ->sum('total');
            $todaySalesCount = (int) Sale::where('status', 'completed')
                ->whereDate('sold_at', Carbon::today())
                ->count();

            // This month's completed sales (nominal & count)
            $monthSalesTotal = (float) Sale::where('status', 'completed')
                ->whereMonth('sold_at', Carbon::now()->month)
                ->whereYear('sold_at', Carbon::now()->year)
                ->sum('total');
            $monthSalesCount = (int) Sale::where('status', 'completed')
                ->whereMonth('sold_at', Carbon::now()->month)
                ->whereYear('sold_at', Carbon::now()->year)
                ->count();

            // This month's completed stock purchases (Stock-In nominal)
            $monthPurchasesTotal = (float) Purchase::where('status', 'completed')
                ->whereMonth('purchased_at', Carbon::now()->month)
                ->whereYear('purchased_at', Carbon::now()->year)
                ->sum('total');

            // Low Stock Alert Count & List (stock <= minimum_stock)
            $totalLowStock = Product::whereColumn('stock', '<=', 'minimum_stock')->count();
            $lowStockProducts = Product::with('category')
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->orderBy('stock', 'asc')
                ->get();

            // 5 Recent Sales
            $recentSales = Sale::with(['user', 'customer'])
                ->latest('sold_at')
                ->take(5)
                ->get();

            // 5 Recent Purchases
            $recentPurchases = Purchase::with(['supplier', 'user'])
                ->latest('purchased_at')
                ->take(5)
                ->get();

            $totalUsers = $user->isAdmin() ? User::count() : null;

            return view('dashboard', compact(
                'user',
                'totalProducts',
                'totalCategories',
                'totalSuppliers',
                'todaySalesTotal',
                'todaySalesCount',
                'monthSalesTotal',
                'monthSalesCount',
                'monthPurchasesTotal',
                'totalLowStock',
                'lowStockProducts',
                'recentSales',
                'recentPurchases',
                'totalUsers'
            ));
        }

        // 2. Customer Dashboard View
        $myOrdersCount = Sale::where(function ($userQuery) use ($user) {
                $userQuery->where('customer_id', $user->id)
                          ->orWhere('user_id', $user->id);
            })
            ->where(function ($nameQuery) use ($user) {
                $nameQuery->where('customer_name', $user->name)
                          ->orWhere('customer_name', 'like', $user->name . ' (%');
            })
            ->where('status', 'completed')
            ->count();

        $myTotalSpent = (float) Sale::where(function ($userQuery) use ($user) {
                $userQuery->where('customer_id', $user->id)
                          ->orWhere('user_id', $user->id);
            })
            ->where(function ($nameQuery) use ($user) {
                $nameQuery->where('customer_name', $user->name)
                          ->orWhere('customer_name', 'like', $user->name . ' (%');
            })
            ->where('status', 'completed')
            ->sum('total');

        $myRecentOrders = Sale::with(['items.product'])
            ->where(function ($userQuery) use ($user) {
                $userQuery->where('customer_id', $user->id)
                          ->orWhere('user_id', $user->id);
            })
            ->where(function ($nameQuery) use ($user) {
                $nameQuery->where('customer_name', $user->name)
                          ->orWhere('customer_name', 'like', $user->name . ' (%');
            })
            ->latest('sold_at')
            ->take(5)
            ->get();

        $featuredProducts = Product::with('category')
            ->where('stock', '>', 0)
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'user',
            'myOrdersCount',
            'myTotalSpent',
            'myRecentOrders',
            'featuredProducts'
        ));
    }
}
