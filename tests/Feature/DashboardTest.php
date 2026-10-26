<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;
    protected Category $category;
    protected Supplier $supplier;
    protected Product $productNormal;
    protected Product $productLowStock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Owner',
            'email' => 'admin@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Siti Staff',
            'email' => 'staff@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        $this->customer = User::create([
            'name' => 'Nadia Customer',
            'email' => 'customer@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->category = Category::create([
            'name' => 'Teddy Bear Classic',
            'description' => 'Boneka beruang',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'PT Boneka Indah Sejahtera',
            'contact_person' => 'Bpk. Hendra',
            'phone' => '081234567890',
            'email' => 'hendra@bonekaindah.test',
            'address' => 'Bandung',
        ]);

        $this->productNormal = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'BNK-TDB-001',
            'name' => 'Teddy Bear Cream Jumbo',
            'size' => 'L (70cm)',
            'material' => 'Rasfur Halus',
            'purchase_price' => 75000,
            'selling_price' => 125000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);

        $this->productLowStock = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'BNK-TDB-002',
            'name' => 'Teddy Bear Coklat Mini',
            'size' => 'S (25cm)',
            'material' => 'Yelvo Super Lembut',
            'purchase_price' => 25000,
            'selling_price' => 45000,
            'stock' => 2,
            'minimum_stock' => 5, // stock <= minimum_stock -> triggers low stock alert!
        ]);

        // Create completed sale today
        $sale = Sale::create([
            'user_id' => $this->admin->id,
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'sale_number' => 'INV-20261024-0001',
            'total' => 250000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $sale->items()->create([
            'product_id' => $this->productNormal->id,
            'quantity' => 2,
            'unit_price' => 125000,
            'subtotal' => 250000,
        ]);

        // Create completed purchase this month
        $purchase = Purchase::create([
            'user_id' => $this->admin->id,
            'supplier_id' => $this->supplier->id,
            'purchase_number' => 'PO-20261024-0001',
            'total' => 750000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $purchase->items()->create([
            'product_id' => $this->productNormal->id,
            'quantity' => 10,
            'unit_cost' => 75000,
            'subtotal' => 750000,
        ]);
    }

    public function test_admin_dashboard_displays_aggregate_metrics_and_low_stock_table(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('ROLE: ADMINISTRATOR / OWNER');

        // Check aggregate values
        $response->assertSee('Penjualan Hari Ini');
        $response->assertSee('250.000'); // Today's sale
        $response->assertSee('1 Transaksi Hari Ini');
        $response->assertSee('Penjualan Bulan Ini');
        $response->assertSee('750.000'); // Purchase month
        $response->assertSee('Pembelian Bulan Ini');

        // Check Low Stock Alert Table
        $response->assertSee('Monitoring Peringatan Stok Rendah');
        $response->assertSee('BNK-TDB-002');
        $response->assertSee('Teddy Bear Coklat Mini');
        $response->assertSee('2 pcs'); // Current stock
        $response->assertSee('5 pcs'); // Min stock
        $response->assertSee('Restock / Buat Purchase');

        // Check Recent Transactions
        $response->assertSee('5 Transaksi Penjualan Terakhir');
        $response->assertSee('INV-20261024-0001');
        $response->assertSee('5 Transaksi Pembelian Terakhir');
        $response->assertSee('PO-20261024-0001');
    }

    public function test_staff_dashboard_displays_operational_metrics_and_low_stock_monitoring(): void
    {
        $response = $this->actingAs($this->staff)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('ROLE: STAFF KASIR');
        $response->assertSee('Penjualan Hari Ini');
        $response->assertSee('Pembelian Bulan Ini');
        $response->assertSee('Monitoring Peringatan Stok Rendah');
        $response->assertSee('Teddy Bear Coklat Mini');
        $response->assertSee('5 Transaksi Penjualan Terakhir');
        $response->assertSee('5 Transaksi Pembelian Terakhir');
    }

    public function test_customer_dashboard_displays_order_history_and_doll_recommendations(): void
    {
        $response = $this->actingAs($this->customer)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('ROLE: PELANGGAN (CUSTOMER)');
        $response->assertSee('Halo, ' . $this->customer->name);

        // Customer stats
        $response->assertSee('Pesanan Selesai');
        $response->assertSee('250.000'); // Spent

        // 5 recent orders
        $response->assertSee('5 Riwayat Pesanan Terbaru Saya');
        $response->assertSee('INV-20261024-0001');

        // Available doll recommendations
        $response->assertSee('Rekomendasi Boneka Tersedia');
        $response->assertSee('Teddy Bear Cream Jumbo');
        $response->assertSee('Buat Pesanan');

        // Internal business metrics must NOT be visible to customer
        $response->assertDontSee('Pembelian Bulan Ini');
        $response->assertDontSee('Monitoring Peringatan Stok Rendah');
        $response->assertDontSee('PO-20261024-0001');
    }
}
