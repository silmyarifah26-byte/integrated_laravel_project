<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $customer;
    protected Category $categoryHijab;
    protected Category $categoryPashmina;

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
            'name' => 'Syahputri',
            'email' => 'siti@customer.test',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->categoryHijab = Category::create([
            'name' => 'Segi Empat Voal',
            'description' => 'Hijab segi empat bahan voal ultrafine',
        ]);

        $this->categoryPashmina = Category::create([
            'name' => 'Pashmina Ceruty',
            'description' => 'Pashmina flowy bahan ceruty babydoll',
        ]);
    }

    /**
     * Requirement: Pencegahan N+1 query pada pemanggilan relasi kategori menggunakan eager loading (with('category')).
     */
    public function test_product_index_eager_loads_category_preventing_n_plus_one(): void
    {
        // Buat 15 produk dengan kategori yang berbeda
        for ($i = 1; $i <= 15; $i++) {
            Product::create([
                'category_id' => ($i % 2 === 0) ? $this->categoryHijab->id : $this->categoryPashmina->id,
                'sku' => "HJB-TEST-" . sprintf('%03d', $i),
                'name' => "Hijab Model {$i}",
                'size' => '115 x 115 cm',
                'material' => 'Voal Premium',
                'purchase_price' => 45000,
                'selling_price' => 75000,
                'stock' => 20,
                'minimum_stock' => 5,
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($this->admin)->get('/products');
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Cari query ke tabel categories
        $categoryQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], 'from `categories`') || str_contains($q['query'], 'from "categories"');
        });

        // Pada pemanggilan tanpa eager loading, query categories akan dieksekusi 10x (1x per baris produk) = N+1.
        // Dengan eager loading with('category'), query ke categories hanya 1x (where in (...)) ditambah 1x untuk dropdown filter.
        $this->assertLessThanOrEqual(3, count($categoryQueries), 'Query categories harus menggunakan eager loading untuk mencegah masalah N+1');
    }

    /**
     * Requirement: Index dengan fitur pencarian (berdasarkan nama/SKU produk)
     */
    public function test_product_index_search_by_name_and_sku(): void
    {
        Product::create([
            'category_id' => $this->categoryHijab->id,
            'sku' => 'SKU-VOAL-ULTRA',
            'name' => 'Hijab Voal Ultrafine Laser Cut',
            'purchase_price' => 40000,
            'selling_price' => 75000,
            'stock' => 15,
            'minimum_stock' => 5,
        ]);

        Product::create([
            'category_id' => $this->categoryPashmina->id,
            'sku' => 'SKU-PSH-SILK',
            'name' => 'Pashmina Silk Cradenza Glamour',
            'purchase_price' => 55000,
            'selling_price' => 95000,
            'stock' => 10,
            'minimum_stock' => 3,
        ]);

        // 1. Cari berdasarkan SKU
        $resSku = $this->actingAs($this->admin)->get('/products?search=SKU-VOAL-ULTRA');
        $resSku->assertStatus(200);
        $resSku->assertSee('Hijab Voal Ultrafine Laser Cut');
        $resSku->assertDontSee('Pashmina Silk Cradenza Glamour');

        // 2. Cari berdasarkan Nama
        $resName = $this->actingAs($this->admin)->get('/products?search=Cradenza');
        $resName->assertStatus(200);
        $resName->assertSee('Pashmina Silk Cradenza Glamour');
        $resName->assertDontSee('Hijab Voal Ultrafine Laser Cut');
    }

    /**
     * Requirement: Filter kategori pada daftar produk
     */
    public function test_product_index_filter_by_category(): void
    {
        Product::create([
            'category_id' => $this->categoryHijab->id,
            'sku' => 'HJB-VOAL-01',
            'name' => 'Segi Empat Rawis Cantik',
            'purchase_price' => 30000,
            'selling_price' => 50000,
            'stock' => 8,
            'minimum_stock' => 2,
        ]);

        Product::create([
            'category_id' => $this->categoryPashmina->id,
            'sku' => 'HJB-PSH-01',
            'name' => 'Pashmina Plisket Full',
            'purchase_price' => 35000,
            'selling_price' => 60000,
            'stock' => 12,
            'minimum_stock' => 4,
        ]);

        $response = $this->actingAs($this->admin)->get("/products?category_id={$this->categoryPashmina->id}");
        $response->assertStatus(200);
        $response->assertSee('Pashmina Plisket Full');
        $response->assertDontSee('Segi Empat Rawis Cantik');
    }

    /**
     * Requirement: Pagination pada daftar produk (paginate(10))
     */
    public function test_product_index_pagination(): void
    {
        for ($i = 1; $i <= 14; $i++) {
            Product::create([
                'category_id' => $this->categoryHijab->id,
                'sku' => "HJB-PGN-" . sprintf('%02d', $i),
                'name' => "Hijab Batch {$i}",
                'purchase_price' => 40000,
                'selling_price' => 70000,
                'stock' => 10,
                'minimum_stock' => 2,
            ]);
        }

        // Halaman 1 (default 10 items)
        $resPage1 = $this->actingAs($this->admin)->get('/products');
        $resPage1->assertStatus(200);
        $this->assertEquals(10, $resPage1->viewData('products')->count());
        $this->assertEquals(14, $resPage1->viewData('products')->total());

        // Halaman 2 (sisa 4 items)
        $resPage2 = $this->actingAs($this->admin)->get('/products?page=2');
        $resPage2->assertStatus(200);
        $this->assertEquals(4, $resPage2->viewData('products')->count());
    }

    /**
     * Requirement: Form Create dengan validasi server-side yang ketat
     * (SKU unik, harga dan stok tidak boleh negatif, validasi relasi category_id)
     */
    public function test_product_create_strict_server_side_validation(): void
    {
        // 1. Coba simpan produk dengan harga dan stok negatif
        $responseNegative = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'HJB-MINUS-01',
            'name' => 'Hijab Minus Test',
            'category_id' => $this->categoryHijab->id,
            'purchase_price' => -25000,
            'selling_price' => -40000,
            'stock' => -5,
            'minimum_stock' => -1,
        ]);

        $responseNegative->assertSessionHasErrors([
            'purchase_price',
            'selling_price',
            'stock',
            'minimum_stock',
        ]);

        // 2. Coba simpan dengan category_id yang tidak ada di tabel categories
        $responseInvalidCategory = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'HJB-NOCAT-01',
            'name' => 'Hijab No Cat',
            'category_id' => 999999, // ID tidak ada
            'purchase_price' => 30000,
            'selling_price' => 50000,
            'minimum_stock' => 5,
        ]);
        $responseInvalidCategory->assertSessionHasErrors('category_id');

        // 3. Simpan produk valid pertama
        $responseValid = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'HJB-VALID-01',
            'name' => 'Pashmina Ceruty Baby Doll Sage Green',
            'category_id' => $this->categoryPashmina->id,
            'size' => '175 x 75 cm',
            'material' => 'Ceruty Babydoll Premium',
            'purchase_price' => 35000,
            'selling_price' => 65000,
            'stock' => 25,
            'minimum_stock' => 5,
        ]);
        $responseValid->assertRedirect('/products');
        $responseValid->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'sku' => 'HJB-VALID-01',
            'name' => 'Pashmina Ceruty Baby Doll Sage Green',
        ]);

        // 4. Coba simpan produk kedua dengan SKU duplikat
        $responseDuplicateSku = $this->actingAs($this->admin)->post('/products', [
            'sku' => 'HJB-VALID-01', // Duplicate SKU
            'name' => 'Hijab Kloning',
            'category_id' => $this->categoryPashmina->id,
            'purchase_price' => 30000,
            'selling_price' => 50000,
            'minimum_stock' => 5,
        ]);
        $responseDuplicateSku->assertSessionHasErrors('sku');
    }

    /**
     * Requirement: Form Edit dengan validasi server-side
     */
    public function test_product_update_strict_validation(): void
    {
        $product1 = Product::create([
            'category_id' => $this->categoryHijab->id,
            'sku' => 'HJB-EDIT-01',
            'name' => 'Hijab Asli 1',
            'purchase_price' => 30000,
            'selling_price' => 60000,
            'stock' => 10,
            'minimum_stock' => 3,
        ]);

        $product2 = Product::create([
            'category_id' => $this->categoryPashmina->id,
            'sku' => 'HJB-EDIT-02',
            'name' => 'Hijab Asli 2',
            'purchase_price' => 40000,
            'selling_price' => 70000,
            'stock' => 15,
            'minimum_stock' => 5,
        ]);

        // 1. Update produk 1 dengan SKU yang sama (harus diperbolehkan oleh Rule::unique()->ignore())
        $updateSelf = $this->actingAs($this->admin)->put("/products/{$product1->id}", [
            'sku' => 'HJB-EDIT-01',
            'name' => 'Hijab Asli 1 Diperbarui',
            'category_id' => $this->categoryHijab->id,
            'purchase_price' => 32000,
            'selling_price' => 65000,
            'minimum_stock' => 4,
        ]);
        $updateSelf->assertRedirect('/products');
        $updateSelf->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'name' => 'Hijab Asli 1 Diperbarui',
        ]);

        // 2. Update produk 1 dengan SKU produk 2 (harus ditolak karena duplikat)
        $updateDuplicate = $this->actingAs($this->admin)->put("/products/{$product1->id}", [
            'sku' => 'HJB-EDIT-02', // SKU milik produk 2
            'name' => 'Mencoba Duplikasi',
            'category_id' => $this->categoryHijab->id,
            'purchase_price' => 32000,
            'selling_price' => 65000,
            'minimum_stock' => 4,
        ]);
        $updateDuplicate->assertSessionHasErrors('sku');

        // 3. Update dengan harga negatif (harus ditolak)
        $updateNegative = $this->actingAs($this->admin)->put("/products/{$product1->id}", [
            'sku' => 'HJB-EDIT-01',
            'name' => 'Harga Minus',
            'category_id' => $this->categoryHijab->id,
            'purchase_price' => -1000,
            'selling_price' => 50000,
            'minimum_stock' => 4,
        ]);
        $updateNegative->assertSessionHasErrors('purchase_price');
    }

    /**
     * Requirement: Customer role permissions and catalog isolation
     */
    public function test_customer_role_catalog_view_and_mutation_protection(): void
    {
        $product = Product::create([
            'category_id' => $this->categoryHijab->id,
            'sku' => 'HJB-CUST-01',
            'name' => 'Hijab Spesial Pelanggan',
            'purchase_price' => 35000,
            'selling_price' => 69000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);

        // Customer mengakses /products otomatis mendapatkan view katalog responsif tanpa harga beli modal
        $response = $this->actingAs($this->customer)->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Hijab Spesial Pelanggan');
        $response->assertSee('69.000');
        $response->assertDontSee('Harga Beli / Modal');

        // Customer dilarang mengakses form create produk (403 Forbidden)
        $this->actingAs($this->customer)->get('/products-create/create')->assertStatus(403);
        $this->actingAs($this->customer)->post('/products', [
            'sku' => 'HJB-HACK',
            'name' => 'Produk Ilegal',
        ])->assertStatus(403);
    }

    public function test_product_automatically_assigned_aesthetic_placeholder_and_renders_via_asset_helper(): void
    {
        // 1. Admin creates product via POST without providing image_url or image_file
        $response = $this->actingAs($this->admin)->post('/products', [
            'category_id' => $this->categoryHijab->id,
            'sku' => 'HJB-AUTO-IMG',
            'name' => 'Pashmina Silk Auto Placeholder',
            'purchase_price' => 40000,
            'selling_price' => 75000,
            'stock' => 15,
            'minimum_stock' => 5,
            // image_url and image_file omitted
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'sku' => 'HJB-AUTO-IMG',
        ]);

        $product = Product::where('sku', 'HJB-AUTO-IMG')->firstOrFail();

        // 2. Assert automatic placeholder assignment
        $this->assertNotEmpty($product->image_url);
        $this->assertStringContainsString('images/hijab/', $product->image_url);
        $this->assertStringContainsString('pashmina-silk.jpg', $product->image_url);

        // 3. Assert asset helper returns valid web path
        $this->assertEquals(asset($product->image_path), $product->image_asset_url);

        // 4. Assert rendered correctly in catalog view using asset()
        $catalogResponse = $this->actingAs($this->customer)->get('/products');
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee($product->image_path);
        $catalogResponse->assertSee('Pashmina Silk Auto Placeholder');

        // 5. Assert rendered correctly in admin product index
        $indexResponse = $this->actingAs($this->admin)->get('/products');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($product->image_path);
    }
}
