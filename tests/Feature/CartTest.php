<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Category $category;
    protected Product $productPashmina;
    protected Product $productVoal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::create([
            'name' => 'Aisyah Pelanggan',
            'email' => 'aisyah@hijabsociety.test',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->category = Category::create([
            'name' => 'Pashmina Series',
            'description' => 'Koleksi pashmina ceruty dan silk',
        ]);

        $this->productPashmina = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'HJB-PSH-001',
            'name' => 'Pashmina Ceruty Babydoll Armani',
            'size' => '175 x 75 cm',
            'material' => 'Ceruty Babydoll Premium',
            'purchase_price' => 35000,
            'selling_price' => 65000,
            'stock' => 15,
            'minimum_stock' => 5,
        ]);

        $this->productVoal = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'HJB-VOL-002',
            'name' => 'Segi Empat Voal Ultrafine Tryspan',
            'size' => '115 x 115 cm',
            'material' => 'Voal Ultrafine Premium',
            'purchase_price' => 45000,
            'selling_price' => 80000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);
    }

    public function test_customer_can_view_cart_page_when_empty(): void
    {
        $response = $this->actingAs($this->customer)->get('/cart');

        $response->assertStatus(200);
        $response->assertSee('Keranjang Belanja');
        $response->assertSee('Keranjang Belanja Anda Masih Kosong');
        $response->assertSee('Buka Katalog Hijab Sekarang');
    }

    public function test_customer_can_add_product_with_variations_to_cart(): void
    {
        $response = $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productPashmina->id,
            'color' => 'Rose Dust',
            'size' => 'Standar (175 x 75 cm)',
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check session cart
        $cart = session('cart');
        $this->assertNotEmpty($cart);
        $this->assertCount(1, $cart);

        $item = reset($cart);
        $this->assertEquals($this->productPashmina->id, $item['product_id']);
        $this->assertEquals('Rose Dust', $item['color']);
        $this->assertEquals('Standar (175 x 75 cm)', $item['size']);
        $this->assertEquals(2, $item['quantity']);
        $this->assertEquals(65000, $item['price']);
        $this->assertEquals(130000, $item['subtotal']);

        // Check view displays the item
        $cartPage = $this->actingAs($this->customer)->get('/cart');
        $cartPage->assertStatus(200);
        $cartPage->assertSee('Pashmina Ceruty Babydoll Armani');
        $cartPage->assertSee('Rose Dust');
        $cartPage->assertSee('Standar (175 x 75 cm)');
        $cartPage->assertSee('130.000');
    }

    public function test_adding_same_product_with_different_variation_creates_separate_cart_items(): void
    {
        // Add first variation (Rose Dust)
        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productPashmina->id,
            'color' => 'Rose Dust',
            'size' => 'Standar (175 x 75 cm)',
            'quantity' => 1,
        ]);

        // Add second variation of same product (Sage Green)
        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productPashmina->id,
            'color' => 'Sage Green',
            'size' => 'Standar (175 x 75 cm)',
            'quantity' => 2,
        ]);

        $cart = session('cart');
        $this->assertCount(2, $cart);

        // Verify total quantity in cart
        $totalQty = array_sum(array_column($cart, 'quantity'));
        $this->assertEquals(3, $totalQty);
    }

    public function test_adding_product_exceeding_stock_is_rejected(): void
    {
        $response = $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productVoal->id,
            'color' => 'Dusty Pink',
            'size' => 'Standar',
            'quantity' => 10, // Stock is only 5
        ]);

        $response->assertSessionHas('error');
        $cart = session('cart');
        $this->assertEmpty($cart);
    }

    public function test_customer_can_update_cart_item_quantity(): void
    {
        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productPashmina->id,
            'color' => 'Mocca',
            'size' => 'Standar',
            'quantity' => 1,
        ]);

        $cart = session('cart');
        $key = array_key_first($cart);

        // Update to 3 pcs
        $response = $this->actingAs($this->customer)->patch("/cart/update/{$key}", [
            'quantity' => 3,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $updatedCart = session('cart');
        $this->assertEquals(3, $updatedCart[$key]['quantity']);
        $this->assertEquals(195000, $updatedCart[$key]['subtotal']); // 3 * 65000
    }

    public function test_customer_can_remove_item_and_clear_cart(): void
    {
        // Add 2 items
        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productPashmina->id,
            'quantity' => 1,
        ]);
        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productVoal->id,
            'quantity' => 1,
        ]);

        $cart = session('cart');
        $this->assertCount(2, $cart);

        // Remove first item
        $firstKey = array_key_first($cart);
        $this->actingAs($this->customer)->delete("/cart/remove/{$firstKey}");

        $cartAfterRemove = session('cart');
        $this->assertCount(1, $cartAfterRemove);

        // Clear cart
        $this->actingAs($this->customer)->post('/cart/clear');
        $this->assertEmpty(session('cart'));
    }

    public function test_checkout_processes_cart_items_atomically_with_variation_and_decrements_stock(): void
    {
        $initialStockPashmina = $this->productPashmina->stock; // 15
        $initialStockVoal = $this->productVoal->stock;         // 5

        // Add 2 items to cart
        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productPashmina->id,
            'color' => 'Rose Dust',
            'size' => 'Standar (175 x 75 cm)',
            'quantity' => 2, // 2 * 65000 = 130000
        ]);

        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productVoal->id,
            'color' => 'Sage Green',
            'size' => '115 x 115 cm',
            'quantity' => 1, // 1 * 80000 = 80000
        ]);

        // Submit checkout
        $response = $this->actingAs($this->customer)->post('/cart/checkout', [
            'payment_method' => 'QRIS',
            'notes' => 'Tolong packing rapi ya kak',
        ]);

        // Assert redirect to sales invoice
        $response->assertRedirect();
        $this->assertEquals(1, Sale::count());
        $this->assertEquals(2, SaleItem::count());

        $sale = Sale::first();
        $this->assertEquals(210000, $sale->total); // 130000 + 80000
        $this->assertEquals($this->customer->id, $sale->customer_id);
        $this->assertEquals($this->customer->name, $sale->customer_name);
        $this->assertEquals('QRIS', $sale->payment_method);
        $this->assertEquals('completed', $sale->status);
        $this->assertEquals('Tolong packing rapi ya kak', $sale->notes);

        // Assert variation recorded in SaleItem
        $pashminaItem = $sale->items()->where('product_id', $this->productPashmina->id)->first();
        $this->assertNotNull($pashminaItem);
        $this->assertStringContainsString('Rose Dust', $pashminaItem->variation);
        $this->assertStringContainsString('Standar (175 x 75 cm)', $pashminaItem->variation);
        $this->assertEquals(2, $pashminaItem->quantity);

        // Assert stock decremented
        $this->assertEquals($initialStockPashmina - 2, $this->productPashmina->fresh()->stock);
        $this->assertEquals($initialStockVoal - 1, $this->productVoal->fresh()->stock);

        // Assert cart emptied
        $this->assertEmpty(session('cart'));

        // Assert invoice page renders variations correctly
        $invoicePage = $this->actingAs($this->customer)->get("/sales/{$sale->id}");
        $invoicePage->assertStatus(200);
        $invoicePage->assertSee('Rose Dust');
        $invoicePage->assertSee('Sage Green');
        $invoicePage->assertSee('210.000');
    }

    public function test_checkout_with_insufficient_stock_fails_atomically(): void
    {
        // Add 3 pcs Voal (stock is 5)
        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $this->productVoal->id,
            'color' => 'Sage Green',
            'size' => '115 x 115 cm',
            'quantity' => 3,
        ]);

        // Simulate another customer buys out the stock before this checkout happens
        $this->productVoal->update(['stock' => 1]);

        // Customer attempts checkout
        $response = $this->actingAs($this->customer)->post('/cart/checkout', [
            'payment_method' => 'QRIS',
        ]);

        $response->assertRedirect('/cart');
        $response->assertSessionHasErrors('cart');

        // Verify no sale was created and stock was not decremented
        $this->assertEquals(0, Sale::count());
        $this->assertEquals(1, $this->productVoal->fresh()->stock);
    }
}
