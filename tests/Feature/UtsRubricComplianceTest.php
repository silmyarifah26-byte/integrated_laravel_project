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

class UtsRubricComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;
    protected Category $category;
    protected Supplier $supplier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Owner Admin HijabSociety',
            'email' => 'admin@hijabsociety.id',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Kasir Syahputri',
            'email' => 'staff@hijabsociety.id',
            'password' => Hash::make('secret123'),
            'role' => 'staff',
        ]);

        $this->customer = User::create([
            'name' => 'Customer Annisa',
            'email' => 'customer@hijabsociety.id',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
        ]);

        $this->category = Category::create([
            'name' => 'Pashmina Sutra Ceruty',
            'description' => 'Kategori hijab pashmina berkualitas premium',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'CV Hijab Tekstil Bandung',
            'phone' => '022-7654321',
            'email' => 'supplier@tekstil.id',
            'address' => 'Bandung, Jawa Barat',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'HJB-RUB-001',
            'name' => 'Pashmina Silk Premium Sage',
            'size' => '180 x 75 cm',
            'material' => 'Silk Ceruty',
            'purchase_price' => 45000,
            'selling_price' => 85000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);
    }

    /**
     * RUBRIK 1: Proteksi Akses URL Server (Middleware/Authorization).
     * Seluruh route Admin dan Staff diproteksi ketat di level server.
     * Guest diarahkan ke login, Customer & Staff diblokir (403 Forbidden) saat mengetik URL manual.
     */
    public function test_rubric_1_server_url_protection_blocks_unauthorized_manual_url_access(): void
    {
        // A. Guest mengakses route terproteksi langsung diarahkan ke /login
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/products/create')->assertRedirect('/login');
        $this->get('/categories')->assertRedirect('/login');
        $this->get('/categories/create')->assertRedirect('/login');
        $this->get('/suppliers')->assertRedirect('/login');
        $this->get('/suppliers/create')->assertRedirect('/login');
        $this->get('/purchases')->assertRedirect('/login');
        $this->get('/purchases/create')->assertRedirect('/login');
        $this->get('/users')->assertRedirect('/login');

        // B. Customer mengetik URL manual untuk halaman Admin & Staff diblokir dengan HTTP 403 Forbidden
        $this->actingAs($this->customer)->get('/products/create')->assertStatus(403);
        $this->actingAs($this->customer)->get("/products/{$this->product->id}/edit")->assertStatus(403);
        $this->actingAs($this->customer)->get('/categories')->assertStatus(403);
        $this->actingAs($this->customer)->get('/categories/create')->assertStatus(403);
        $this->actingAs($this->customer)->get('/suppliers')->assertStatus(403);
        $this->actingAs($this->customer)->get('/suppliers/create')->assertStatus(403);
        $this->actingAs($this->customer)->get('/purchases')->assertStatus(403);
        $this->actingAs($this->customer)->get('/purchases/create')->assertStatus(403);
        $this->actingAs($this->customer)->get('/users')->assertStatus(403);
        $this->actingAs($this->customer)->get('/users/create')->assertStatus(403);

        // C. Staff mengetik URL manual untuk halaman khusus Admin (Master Data Mutation, User Management) diblokir HTTP 403 Forbidden
        $this->actingAs($this->staff)->get('/products/create')->assertStatus(403);
        $this->actingAs($this->staff)->get("/products/{$this->product->id}/edit")->assertStatus(403);
        $this->actingAs($this->staff)->get('/categories/create')->assertStatus(403);
        $this->actingAs($this->staff)->get("/categories/{$this->category->id}/edit")->assertStatus(403);
        $this->actingAs($this->staff)->get('/suppliers/create')->assertStatus(403);
        $this->actingAs($this->staff)->get("/suppliers/{$this->supplier->id}/edit")->assertStatus(403);
        $this->actingAs($this->staff)->get('/users')->assertStatus(403);
        $this->actingAs($this->staff)->get('/users/create')->assertStatus(403);

        // D. Admin memiliki akses resmi ke seluruh URL
        $this->actingAs($this->admin)->get('/products/create')->assertStatus(200);
        $this->actingAs($this->admin)->get('/categories/create')->assertStatus(200);
        $this->actingAs($this->admin)->get('/suppliers/create')->assertStatus(200);
        $this->actingAs($this->admin)->get('/purchases/create')->assertStatus(200);
        $this->actingAs($this->admin)->get('/users')->assertStatus(200);
    }

    /**
     * RUBRIK 2: Atomic Transaction & Pencegahan Stok Minus (DB::transaction & lockForUpdate).
     * Transaksi penjualan menolak pesanan jika stok tidak cukup, dan rollback atomik mencegah stok minus.
     */
    public function test_rubric_2_atomic_transaction_prevents_negative_stock_on_sales(): void
    {
        $this->assertEquals(5, $this->product->stock);

        // 1. Permintaan pembelian melebihi stok yang ada (minta 6 dari stok 5)
        $response = $this->actingAs($this->customer)->post('/sales', [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 6,
                ],
            ],
        ]);

        // Validasi wajib menolak transaksi dan memberikan pesan error
        $response->assertSessionHasErrors('items');

        // Pastikan tidak ada transaksi yang tersimpan dan stok tetap 5 (TIDAK menjadi minus -1)
        $this->assertEquals(0, Sale::count());
        $this->assertEquals(5, $this->product->fresh()->stock);

        // 2. Pembatalan Purchase juga dicek agar stok tidak menjadi minus jika barang sudah terjual
        $this->product->update(['stock' => 1]); // Misal dari 5 pcs, 4 pcs sudah terjual sehingga sisa 1

        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->admin->id,
            'purchase_number' => 'PO-20261026-9999',
            'total' => 135000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $purchase->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 3, // Ingin membatalkan 3 unit, padahal stok fisik toko hanya sisa 1
            'unit_cost' => 45000,
            'subtotal' => 135000,
        ]);

        // Pembatalan wajib ditolak dengan pesan error stok tidak mencukupi
        $cancelResponse = $this->actingAs($this->admin)->post("/purchases/{$purchase->id}/cancel");
        $cancelResponse->assertSessionHas('error');

        // Stok tidak berubah dan status transaksi tetap completed
        $this->assertEquals(1, $this->product->fresh()->stock);
        $this->assertEquals('completed', $purchase->fresh()->status);
    }

    /**
     * RUBRIK 3: Pengambilan Harga Murni dari Database Server.
     * Sistem tidak mempercayai harga dari input browser atau frontend.
     * Harga satuan wajib ditarik langsung dari database berdasarkan product_id yang valid.
     */
    public function test_rubric_3_pure_server_database_pricing_ignores_spoofed_frontend_prices(): void
    {
        $initialStock = $this->product->stock; // 5

        // Manipulasi input browser: mencoba membeli produk seharga Rp 85.000 dengan harga Rp 500 perak
        $response = $this->actingAs($this->customer)->post('/sales', [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_price' => 500, // Harga palsu dari manipulasi inspect element / curl
                ],
            ],
            'payment_method' => 'QRIS',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $sale = Sale::latest()->first();
        $this->assertNotNull($sale);

        // Server wajib menggunakan harga jual resmi dari database (Rp 85.000 x 2 = Rp 170.000), BUKAN Rp 500 x 2
        $this->assertEquals(170000, $sale->total);

        $saleItem = $sale->items->first();
        $this->assertEquals(85000, $saleItem->unit_price);
        $this->assertEquals(170000, $saleItem->subtotal);

        // Stok berkurang dari 5 menjadi 3
        $this->assertEquals($initialStock - 2, $this->product->fresh()->stock);
    }
}
