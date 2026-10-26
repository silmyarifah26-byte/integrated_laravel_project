<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Policies\ProductPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\SalePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        Sale::class => SalePolicy::class,
        Purchase::class => PurchasePolicy::class,
        Product::class => ProductPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ---------------------------------------------------------------------
        // 1. Role-Based Gates
        // ---------------------------------------------------------------------
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('staff', fn (User $user) => $user->isStaff());
        Gate::define('customer', fn (User $user) => $user->isCustomer());
        Gate::define('admin-or-staff', fn (User $user) => $user->isAdmin() || $user->isStaff());

        // ---------------------------------------------------------------------
        // 2. Feature & Module Permission Gates
        // ---------------------------------------------------------------------
        // Admin: Akses penuh ke manajemen user, master data, laporan, dan pembatalan transaksi.
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
        Gate::define('manage-master-data', fn (User $user) => $user->isAdmin());
        Gate::define('cancel-transactions', fn (User $user) => $user->isAdmin());
        Gate::define('view-financial-reports', fn (User $user) => $user->isAdmin());

        // Staff: Akses ke transaksi pembelian, penjualan, dan melihat produk.
        Gate::define('operate-purchases', fn (User $user) => $user->isAdmin() || $user->isStaff());
        Gate::define('operate-sales', fn (User $user) => $user->isAdmin() || $user->isStaff());
        Gate::define('view-products', fn (User $user) => true);

        // Customer: Akses ke katalog produk dan riwayat belanja mereka sendiri.
        Gate::define('access-catalog', fn (User $user) => true);
        Gate::define('view-own-sales', fn (User $user) => true);

        // Granular Transaction Gates
        Gate::define('view-sale', function (User $user, Sale $sale) {
            if ($user->isAdmin() || $user->isStaff()) {
                return true;
            }
            return $sale->customer_id === $user->id || $sale->user_id === $user->id;
        });

        Gate::define('cancel-purchase', fn (User $user, Purchase $purchase) => $user->isAdmin());
        Gate::define('cancel-sale', fn (User $user, Sale $sale) => $user->isAdmin());
    }
}
