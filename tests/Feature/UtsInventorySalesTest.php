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

class UtsInventorySalesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;
    protected Category $category;
    protected Supplier $supplier;
    protected Product $productA;
    protected Product $productB;

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
            'description' => 'Kategori boneka beruang klasik',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'PT Boneka Indah Sejahtera',
            'contact_person' => 'Bpk. Hendra',
            'phone' => '081234567890',
            'email' => 'hendra@bonekaindah.test',
            'address' => 'Bandung',
        ]);

        $this->productA = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'BNK-TDB-001',
            'name' => 'Teddy Bear Cream Jumbo',
            'size' => 'L (70cm)',
            'material' => 'Rasfur Halus',
            'purchase_price' => 75000,
            'selling_price' => 125000,
            'stock' => 10,
            'minimum_stock' => 3,
        ]);

        $this->productB = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'BNK-TDB-002',
            'name' => 'Teddy Bear Coklat Mini',
            'size' => 'S (25cm)',
            'material' => 'Yelvo Lembut',
            'purchase_price' => 25000,
            'selling_price' => 50000,
            'stock' => 4,
            'minimum_stock' => 2,
        ]);
    }

    /**
     * 1. Guest yang belum login diarahkan ke /login saat mengakses /dashboard.
     */
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * 2. Validasi SKU unik dan harga negatif ditolak saat admin membuat produk.
     */
    public function test_admin_cannot_create_product_with_duplicate_sku_or_negative_price(): void
    {
        // Duplicate SKU test
        $responseDuplicateSku = $this->actingAs($this->admin)->post('/products', [
            'category_id' => $this->category->id,
            'sku' => 'BNK-TDB-001', // SKU already used by productA
            'name' => 'Boneka Duplikat SKU',
            'purchase_price' => 50000,
            'selling_price' => 80000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);

        $responseDuplicateSku->assertSessionHasErrors('sku');

        // Negative price test
        $responseNegativePrice = $this->actingAs($this->admin)->post('/products', [
            'category_id' => $this->category->id,
            'sku' => 'BNK-NEW-999',
            'name' => 'Boneka Harga Negatif',
            'purchase_price' => -25000, // Negative purchase price!
            'selling_price' => -50000,  // Negative selling price!
            'stock' => 5,
            'minimum_stock' => 2,
        ]);

        $responseNegativePrice->assertSessionHasErrors(['purchase_price', 'selling_price']);
    }

    /**
     * 3. Saat Staff/Admin menyimpan transaksi Purchase (Stock-In), stok bertambah dan total dihitung benar.
     */
    public function test_purchase_transaction_increases_product_stock(): void
    {
        $initialStock = $this->productA->stock; // 10
        $qtyToPurchase = 5;
        $unitCost = 70000;

        $response = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'purchased_at' => now()->toDateString(),
            'notes' => 'Pengadaan stok tambahan boneka',
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => $qtyToPurchase,
                    'unit_cost' => $unitCost,
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify stock increment
        $this->assertEquals($initialStock + $qtyToPurchase, $this->productA->fresh()->stock);

        // Verify Purchase total
        $purchase = Purchase::first();
        $this->assertNotNull($purchase);
        $this->assertEquals($qtyToPurchase * $unitCost, $purchase->total);
        $this->assertEquals('completed', $purchase->status);
    }

    /**
     * 4. Saat transaksi Sale berhasil, stok boneka berkurang sesuai kuantitas dan total dihitung dari selling_price di database.
     */
    public function test_sale_transaction_decreases_product_stock(): void
    {
        $initialStock = $this->productA->stock; // 10
        $qtyToSell = 3;

        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_name' => 'Pembeli Walk-in',
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => $qtyToSell,
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify stock decrement (10 - 3 = 7)
        $this->assertEquals($initialStock - $qtyToSell, $this->productA->fresh()->stock);

        // Verify Sale total calculated from official database selling_price (3 * 125,000 = 375,000)
        $sale = Sale::first();
        $this->assertNotNull($sale);
        $expectedTotal = $this->productA->selling_price * $qtyToSell;
        $this->assertEquals($expectedTotal, $sale->total);
        $this->assertEquals('completed', $sale->status);
    }

    /**
     * 5. Jika stok boneka 4 dan dibeli 6, transaksi ditolak (error validasi) dan stok tetap 4.
     */
    public function test_sale_with_insufficient_stock_is_rejected_and_stock_unchanged(): void
    {
        $this->assertEquals(4, $this->productB->stock); // Current stock is 4

        // Attempt to buy 6 units (exceeds stock)
        $response = $this->actingAs($this->customer)->post('/sales', [
            'items' => [
                [
                    'product_id' => $this->productB->id,
                    'quantity' => 6,
                ],
            ],
        ]);

        // Validation exception thrown and returned as session error
        $response->assertSessionHasErrors('items');

        // Stock MUST remain unchanged (4)
        $this->assertEquals(4, $this->productB->fresh()->stock);

        // No Sale transaction should be saved
        $this->assertEquals(0, Sale::count());
    }

    /**
     * 6. Atomicity: Dalam penjualan 2 item sekaligus, jika item 1 stoknya cukup tetapi item 2 stoknya kurang,
     * pastikan stok item 1 TIDAK berkurang dan tidak ada header/detail transaksi yang tersimpan (rollback sempurna).
     */
    public function test_sale_atomicity_rolls_back_all_items_if_second_item_fails(): void
    {
        $initialStockA = $this->productA->stock; // 10
        $initialStockB = $this->productB->stock; // 4

        // Item 1: productA qty 2 (sufficient: 2 <= 10)
        // Item 2: productB qty 10 (insufficient: 10 > 4)
        $response = $this->actingAs($this->staff)->post('/sales', [
            'customer_name' => 'Pelanggan Multi-Item',
            'items' => [
                [
                    'product_id' => $this->productA->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $this->productB->id,
                    'quantity' => 10,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items');

        // Verify atomic rollback: productA stock was NOT deducted
        $this->assertEquals($initialStockA, $this->productA->fresh()->stock);
        $this->assertEquals($initialStockB, $this->productB->fresh()->stock);

        // Verify no sale header or items created
        $this->assertEquals(0, Sale::count());
        $this->assertDatabaseEmpty('sale_items');
    }

    /**
     * 7. Staff dan Customer yang mengakses endpoint khusus Admin mendapatkan status 403 Forbidden.
     */
    public function test_authorization_prevents_staff_and_customer_from_accessing_admin_routes(): void
    {
        // 1. Staff cannot access User Management (Admin only)
        $this->actingAs($this->staff)->get('/users')->assertStatus(403);
        $this->actingAs($this->staff)->get('/users/create')->assertStatus(403);

        // 2. Staff cannot create, edit, or delete Category (Admin only)
        $this->actingAs($this->staff)->get('/categories/create')->assertStatus(403);
        $this->actingAs($this->staff)->post('/categories', ['name' => 'Kategori Baru'])->assertStatus(403);
        $this->actingAs($this->staff)->delete("/categories/{$this->category->id}")->assertStatus(403);

        // 3. Customer cannot access User Management, Purchases, Suppliers, or Categories
        $this->actingAs($this->customer)->get('/users')->assertStatus(403);
        $this->actingAs($this->customer)->get('/purchases')->assertStatus(403);
        $this->actingAs($this->customer)->get('/purchases/create')->assertStatus(403);
        $this->actingAs($this->customer)->get('/suppliers')->assertStatus(403);
        $this->actingAs($this->customer)->get('/categories')->assertStatus(403);
        $this->actingAs($this->customer)->post('/products', [
            'sku' => 'BNK-HACK-01',
            'name' => 'Produk Ilegal',
        ])->assertStatus(403);
    }
}
