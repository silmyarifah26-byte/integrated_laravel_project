<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer1;
    protected User $customer2;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Test',
            'email' => 'staff@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        $this->customer1 = User::create([
            'name' => 'Nadia Customer',
            'email' => 'nadia@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->customer2 = User::create([
            'name' => 'Rizky Customer',
            'email' => 'rizky@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $category = Category::create(['name' => 'Teddy Bear']);

        $this->product1 = Product::create([
            'category_id' => $category->id,
            'sku' => 'BNK-S1-001',
            'name' => 'Teddy Bear Cream',
            'purchase_price' => 50000,
            'selling_price' => 90000,
            'stock' => 10,
            'minimum_stock' => 3,
        ]);

        $this->product2 = Product::create([
            'category_id' => $category->id,
            'sku' => 'BNK-S2-002',
            'name' => 'Panda Lucu',
            'purchase_price' => 45000,
            'selling_price' => 75000,
            'stock' => 4,
            'minimum_stock' => 2,
        ]);
    }

    public function test_all_roles_can_access_sales_index_and_create(): void
    {
        $this->actingAs($this->staff)->get('/sales')->assertStatus(200);
        $this->actingAs($this->admin)->get('/sales')->assertStatus(200);
        $this->actingAs($this->customer1)->get('/sales')->assertStatus(200);

        $this->actingAs($this->staff)->get('/sales/create')->assertStatus(200);
        $this->actingAs($this->customer1)->get('/sales/create')->assertStatus(200);
    }

    public function test_customer_can_create_self_order_and_stock_is_decremented(): void
    {
        $initialStock = $this->product1->stock; // 10

        $response = $this->actingAs($this->customer1)->post('/sales', [
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2, // 2 * 90000 = 180,000
                ],
            ],
            'notes' => 'Pesanan boneka saya',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertEquals($this->customer1->id, $sale->customer_id);
        $this->assertEquals($this->customer1->name, $sale->customer_name);
        $this->assertEquals(180000, $sale->total);
        $this->assertEquals('completed', $sale->status);
        $this->assertStringStartsWith('INV-', $sale->sale_number);

        // Stock decremented from 10 to 8
        $this->assertEquals($initialStock - 2, $this->product1->fresh()->stock);
    }

    public function test_insufficient_stock_causes_transaction_rollback(): void
    {
        $initialStock1 = $this->product1->stock; // 10
        $initialStock2 = $this->product2->stock; // 4

        // Item 1 is fine (2 <= 10), but Item 2 exceeds stock (10 > 4)
        $response = $this->actingAs($this->customer1)->post('/sales', [
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 10, // Exceeds stock of 4!
                ],
            ],
        ]);

        // Validation error
        $response->assertSessionHasErrors('items');

        // Verify total rollback: NO sale recorded, product1 stock NOT decremented
        $this->assertEquals(0, Sale::count());
        $this->assertEquals($initialStock1, $this->product1->fresh()->stock);
        $this->assertEquals($initialStock2, $this->product2->fresh()->stock);
    }

    public function test_customer_cannot_view_other_customers_receipt(): void
    {
        $saleOfCustomer1 = Sale::create([
            'user_id' => $this->customer1->id,
            'customer_id' => $this->customer1->id,
            'customer_name' => $this->customer1->name,
            'sale_number' => 'INV-20261024-0001',
            'total' => 90000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        // Customer 1 can view
        $this->actingAs($this->customer1)->get("/sales/{$saleOfCustomer1->id}")->assertStatus(200);

        // Customer 2 cannot view (403 Forbidden)
        $this->actingAs($this->customer2)->get("/sales/{$saleOfCustomer1->id}")->assertStatus(403);

        // Staff & Admin can view
        $this->actingAs($this->staff)->get("/sales/{$saleOfCustomer1->id}")->assertStatus(200);
        $this->actingAs($this->admin)->get("/sales/{$saleOfCustomer1->id}")->assertStatus(200);
    }

    public function test_staff_cannot_cancel_sale_but_admin_can_and_stock_is_restored(): void
    {
        $this->product1->update(['stock' => 8]);

        $sale = Sale::create([
            'user_id' => $this->staff->id,
            'customer_id' => $this->customer1->id,
            'customer_name' => $this->customer1->name,
            'sale_number' => 'INV-20261024-0002',
            'total' => 180000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $sale->items()->create([
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 90000,
            'subtotal' => 180000,
        ]);

        // Staff cannot cancel
        $this->actingAs($this->staff)->post("/sales/{$sale->id}/cancel")->assertStatus(403);

        // Admin can cancel
        $response = $this->actingAs($this->admin)->post("/sales/{$sale->id}/cancel");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Stock restored from 8 to 10
        $this->assertEquals(10, $this->product1->fresh()->stock);
        $this->assertEquals('cancelled', $sale->fresh()->status);
    }

    public function test_sale_persists_payment_method_and_payment_status(): void
    {
        $response = $this->actingAs($this->customer1)->post('/sales', [
            'payment_method' => 'Transfer Bank',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 1,
                ],
            ],
            'notes' => 'Pembayaran via Bank Mandiri',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', [
            'customer_id' => $this->customer1->id,
            'payment_method' => 'Transfer Bank',
            'payment_status' => 'paid',
        ]);
    }

    public function test_customer_sales_index_does_not_show_new_order_button_and_syncs_with_dashboard(): void
    {
        $sale = Sale::create([
            'user_id' => $this->customer1->id,
            'customer_id' => $this->customer1->id,
            'customer_name' => $this->customer1->name,
            'sale_number' => 'INV-20261026-0009',
            'total' => 90000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $response = $this->actingAs($this->customer1)->get('/sales');
        $response->assertStatus(200);
        $response->assertSee('Riwayat Pesanan Saya');
        $response->assertDontSee('Pesan Boneka Baru');
        $response->assertSee('INV-20261026-0009');

        // Verify dashboard also shows this sale
        $dashResponse = $this->actingAs($this->customer1)->get('/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('INV-20261026-0009');
    }

    public function test_sales_create_with_product_id_preselects_product_in_cart(): void
    {
        $response = $this->actingAs($this->customer1)->get('/sales/create?product_id=' . $this->product1->id);
        $response->assertStatus(200);
        $response->assertSee('Keranjang Boneka yang Dibeli');
        $response->assertSee($this->product1->name);
    }
}
