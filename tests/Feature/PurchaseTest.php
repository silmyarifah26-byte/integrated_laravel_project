<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;
    protected Supplier $supplier;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin HijabSociety',
            'email' => 'admin@hijabsociety.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Toko Hijab',
            'email' => 'staff@hijabsociety.test',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        $this->customer = User::create([
            'name' => 'Customer HijabSociety',
            'email' => 'customer@hijabsociety.test',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $category = Category::create(['name' => 'Pashmina & Hijab Segi Empat']);

        $this->supplier = Supplier::create([
            'name' => 'PT Hijab Mandiri Tekstil Bandung',
            'phone' => '022-1234567',
        ]);

        $this->product1 = Product::create([
            'category_id' => $category->id,
            'sku' => 'HJB-PSH-001',
            'name' => 'Pashmina Ceruty Baby Doll Sage Green',
            'purchase_price' => 35000,
            'selling_price' => 65000,
            'stock' => 10,
            'minimum_stock' => 5,
        ]);

        $this->product2 = Product::create([
            'category_id' => $category->id,
            'sku' => 'HJB-VOAL-002',
            'name' => 'Segi Empat Voal Ultrafine Dusty Pink',
            'purchase_price' => 40000,
            'selling_price' => 75000,
            'stock' => 5,
            'minimum_stock' => 3,
        ]);
    }

    public function test_staff_and_admin_can_access_purchases_index_and_create(): void
    {
        $this->actingAs($this->staff)->get('/purchases')->assertStatus(200);
        $this->actingAs($this->staff)->get('/purchases/create')->assertStatus(200);

        $this->actingAs($this->admin)->get('/purchases')->assertStatus(200);
        $this->actingAs($this->admin)->get('/purchases/create')->assertStatus(200);
    }

    public function test_customer_cannot_access_purchases(): void
    {
        $this->actingAs($this->customer)->get('/purchases')->assertStatus(403);
        $this->actingAs($this->customer)->get('/purchases/create')->assertStatus(403);
    }

    public function test_store_multi_item_purchase_updates_stock_atomically(): void
    {
        $initialStock1 = $this->product1->stock; // 10
        $initialStock2 = $this->product2->stock; // 5

        $response = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'purchased_at' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Pengadaan batch hijab ceruty & voal baru',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 10,
                    'unit_cost' => 55000, // 10 * 55000 = 550,000
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 15,
                    'unit_cost' => 42000, // 15 * 42000 = 630,000
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check purchase record
        $purchase = Purchase::first();
        $this->assertNotNull($purchase);
        $this->assertEquals(1180000, $purchase->total);
        $this->assertEquals('completed', $purchase->status);
        $this->assertEquals($this->staff->id, $purchase->user_id);
        $this->assertNotNull($purchase->purchased_at);
        $this->assertStringStartsWith('PO-', $purchase->purchase_number);

        // Check stock increments
        $this->assertEquals($initialStock1 + 10, $this->product1->fresh()->stock);
        $this->assertEquals($initialStock2 + 15, $this->product2->fresh()->stock);

        // Check items
        $this->assertCount(2, $purchase->items);
    }

    public function test_purchase_validation_rules_reject_invalid_payloads(): void
    {
        // 1. Missing supplier and empty items
        $resEmpty = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => '',
            'items' => [],
        ]);
        $resEmpty->assertSessionHasErrors(['supplier_id', 'items']);

        // 2. Negative quantity and negative unit cost
        $resNegative = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'purchased_at' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => -5,
                    'unit_cost' => -10000,
                ],
            ],
        ]);
        $resNegative->assertSessionHasErrors(['items.0.quantity', 'items.0.unit_cost']);

        // 3. Duplicate product_id in items
        $resDuplicate = $this->actingAs($this->staff)->post('/purchases', [
            'supplier_id' => $this->supplier->id,
            'purchased_at' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 5,
                    'unit_cost' => 35000,
                ],
                [
                    'product_id' => $this->product1->id, // Duplikat
                    'quantity' => 10,
                    'unit_cost' => 35000,
                ],
            ],
        ]);
        $resDuplicate->assertSessionHasErrors(['items.0.product_id', 'items.1.product_id']);
    }

    public function test_staff_cannot_cancel_purchase(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->admin->id,
            'purchase_number' => 'PO-20261024-0001',
            'total' => 200000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $response = $this->actingAs($this->staff)->post("/purchases/{$purchase->id}/cancel");
        $response->assertStatus(403);
    }

    public function test_admin_can_cancel_purchase_and_stock_is_deducted(): void
    {
        // First create purchase with 5 units of product1
        $this->product1->update(['stock' => 20]);

        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->admin->id,
            'purchase_number' => 'PO-20261024-0002',
            'total' => 250000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $purchase->items()->create([
            'product_id' => $this->product1->id,
            'quantity' => 5,
            'unit_cost' => 50000,
            'subtotal' => 250000,
        ]);

        $response = $this->actingAs($this->admin)->post("/purchases/{$purchase->id}/cancel");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Stock should be deducted from 20 to 15
        $this->assertEquals(15, $this->product1->fresh()->stock);
        $this->assertEquals('cancelled', $purchase->fresh()->status);
    }

    public function test_admin_cannot_cancel_purchase_if_stock_would_become_negative(): void
    {
        // Product stock is only 2, but purchase was for 5 units (3 units were already sold)
        $this->product1->update(['stock' => 2]);

        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->admin->id,
            'purchase_number' => 'PO-20261024-0003',
            'total' => 250000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $purchase->items()->create([
            'product_id' => $this->product1->id,
            'quantity' => 5,
            'unit_cost' => 50000,
            'subtotal' => 250000,
        ]);

        $response = $this->actingAs($this->admin)->post("/purchases/{$purchase->id}/cancel");
        $response->assertSessionHas('error');

        // Stock and status remain untouched
        $this->assertEquals(2, $this->product1->fresh()->stock);
        $this->assertEquals('completed', $purchase->fresh()->status);
    }

    public function test_purchases_create_with_product_id_preselects_product_for_restock(): void
    {
        $response = $this->actingAs($this->staff)->get(route('purchases.create', ['product_id' => $this->product1->id]));

        $response->assertStatus(200);
        $response->assertSee('Mode Restock Cepat: Produk ' . $this->product1->name);
        $response->assertSee($this->product1->sku);
    }
}
