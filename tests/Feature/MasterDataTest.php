<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;
    protected Category $category;

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

        $this->customer = User::create([
            'name' => 'Customer Test',
            'email' => 'customer@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->category = Category::create([
            'name' => 'Teddy Bear Test',
            'description' => 'Kategori uji coba',
        ]);
    }

    public function test_staff_can_view_categories_and_suppliers_but_not_create(): void
    {
        // Can view index
        $resCategories = $this->actingAs($this->staff)->get('/categories');
        $resCategories->assertStatus(200);

        $resSuppliers = $this->actingAs($this->staff)->get('/suppliers');
        $resSuppliers->assertStatus(200);

        // Cannot access create form (403 Forbidden)
        $resCatCreate = $this->actingAs($this->staff)->get('/categories/create');
        $resCatCreate->assertStatus(403);

        $resSupCreate = $this->actingAs($this->staff)->get('/suppliers/create');
        $resSupCreate->assertStatus(403);
    }

    public function test_customer_cannot_view_categories_or_suppliers_or_users(): void
    {
        $this->actingAs($this->customer)->get('/categories')->assertStatus(403);
        $this->actingAs($this->customer)->get('/suppliers')->assertStatus(403);
        $this->actingAs($this->customer)->get('/users')->assertStatus(403);
    }

    public function test_admin_can_create_category_with_validation(): void
    {
        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => 'Boneka Karakter Baru',
            'description' => 'Deskripsi kategori baru',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Boneka Karakter Baru']);

        // Duplicate name test
        $dupResponse = $this->actingAs($this->admin)->post('/categories', [
            'name' => 'Boneka Karakter Baru',
        ]);
        $dupResponse->assertSessionHasErrors('name');
    }

    public function test_category_cannot_be_deleted_if_has_products(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'sku' => 'BNK-TST-001',
            'name' => 'Boneka Uji Coba',
            'purchase_price' => 50000,
            'selling_price' => 80000,
            'stock' => 10,
            'minimum_stock' => 5,
        ]);

        $response = $this->actingAs($this->admin)->delete("/categories/{$this->category->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }

    public function test_supplier_cannot_be_deleted_if_has_purchases(): void
    {
        $supplier = Supplier::create([
            'name' => 'Pabrik Boneka Test',
            'phone' => '021-123456',
        ]);

        Purchase::create([
            'supplier_id' => $supplier->id,
            'user_id' => $this->admin->id,
            'purchase_number' => 'PO-TST-0001',
            'total' => 500000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->delete("/suppliers/{$supplier->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }

    public function test_admin_can_create_product_with_validation(): void
    {
        $response = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'BNK-NEW-001',
            'name' => 'Boneka Panda Jumbo',
            'category_id' => $this->category->id,
            'size' => 'XL (100cm)',
            'material' => 'Yelvo Super Lembut',
            'purchase_price' => 150000,
            'selling_price' => 240000,
            'stock' => 12,
            'minimum_stock' => 5,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['sku' => 'BNK-NEW-001']);
    }

    public function test_admin_can_upload_product_photo(): void
    {
        $sourceFile = public_path('images/plushies/teddy-bear-pita.jpg');
        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload_');
        copy($sourceFile, $tempFile);
        $file = new \Illuminate\Http\UploadedFile($tempFile, 'upload_plushie.jpg', 'image/jpeg', null, true);

        $response = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'BNK-UPLOAD-001',
            'name' => 'Boneka Upload Foto',
            'category_id' => $this->category->id,
            'image_file' => $file,
            'purchase_price' => 50000,
            'selling_price' => 90000,
            'minimum_stock' => 5,
        ]);

        $response->assertRedirect('/products');
        $product = Product::where('sku', 'BNK-UPLOAD-001')->first();
        $this->assertNotNull($product);
        $this->assertStringContainsString('upload-plushie.jpg', $product->image_url);

        // Clean up uploaded test file
        $uploadedPath = public_path(ltrim($product->image_url, '/'));
        if (file_exists($uploadedPath)) {
            @unlink($uploadedPath);
        }
    }

    public function test_product_soft_delete_preserves_transaction_integrity(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'sku' => 'BNK-TX-001',
            'name' => 'Boneka Beruang Transaksi',
            'purchase_price' => 50000,
            'selling_price' => 90000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);

        $supplier = Supplier::create([
            'name' => 'Pabrik Transaksi',
            'phone' => '021-999999',
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'user_id' => $this->admin->id,
            'purchase_number' => 'PO-TX-0001',
            'total' => 250000,
            'status' => 'completed',
            'purchased_at' => now(),
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_cost' => 50000,
            'subtotal' => 250000,
        ]);

        $response = $this->actingAs($this->admin)->delete("/products/{$product->id}");
        $response->assertRedirect('/products');
        $response->assertSessionHas('success');

        // Verify soft deleted
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_cannot_delete_self_in_user_management(): void
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }
}
