<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');

        $products = [
            [
                'category_id' => $categories['Teddy Bear Classic'],
                'sku' => 'BNK-TDB-001',
                'name' => 'Boneka Teddy Bear Giant Ribbon Cream',
                'size' => 'XL (100cm)',
                'material' => 'Bulu Rasfur Halus + Kapas Silikon Grade A',
                'image_url' => 'images/plushies/teddy-bear-pita.jpg',
                'purchase_price' => 180000.00,
                'selling_price' => 275000.00,
                'stock' => 8,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Teddy Bear Classic'],
                'sku' => 'BNK-TDB-002',
                'name' => 'Boneka Beruang Sweater Rajut Coklat',
                'size' => 'M (45cm)',
                'material' => 'Yelvo Import Halus + Sweater Rajut Wool Premium',
                'image_url' => 'images/plushies/teddy-bear-mini.jpg',
                'purchase_price' => 65000.00,
                'selling_price' => 110000.00,
                'stock' => 2, // Low stock alert (2 <= 5)
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Boneka Karakter Hewan'],
                'sku' => 'BNK-ANM-001',
                'name' => 'Boneka Panda Bambu Menggemaskan',
                'size' => 'M (40cm)',
                'material' => 'Kain Yelvo Premium + Dacron HCS Elastis',
                'image_url' => 'images/plushies/panda-bambu.jpg',
                'purchase_price' => 55000.00,
                'selling_price' => 95000.00,
                'stock' => 14,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Boneka Karakter Hewan'],
                'sku' => 'BNK-ANM-002',
                'name' => 'Boneka Kelinci Telinga Panjang Pink',
                'size' => 'S (35cm)',
                'material' => 'Spandek Elastis Super Lembut + Micro Dacron',
                'image_url' => 'images/plushies/kelinci-pink.jpg',
                'purchase_price' => 45000.00,
                'selling_price' => 79000.00,
                'stock' => 3, // Low stock alert (3 <= 5)
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Boneka Karakter Hewan'],
                'sku' => 'BNK-ANM-003',
                'name' => 'Boneka Kucing Boba Milk Tea',
                'size' => 'L (50cm)',
                'material' => 'Yelvo Stretch Lembut + Kapas Microfiber Hypoallergenic',
                'image_url' => 'images/plushies/kucing-boba.jpg',
                'purchase_price' => 70000.00,
                'selling_price' => 125000.00,
                'stock' => 10,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Plushie Anime & K-Pop'],
                'sku' => 'BNK-PLH-001',
                'name' => 'Plushie Karakter Chibi Idol Edition',
                'size' => 'S (25cm)',
                'material' => 'Velboa Halus + Bordir Komputer Presisi Tinggi',
                'image_url' => 'images/plushies/chibi-idol.jpg',
                'purchase_price' => 48000.00,
                'selling_price' => 89000.00,
                'stock' => 18,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Plushie Anime & K-Pop'],
                'sku' => 'BNK-PLH-002',
                'name' => 'Plushie Maskot Rubah Ekor Sembilan',
                'size' => 'M (30cm)',
                'material' => 'Kain Nylex & Yelvo Kombinasi + Dacron Padat',
                'image_url' => 'images/plushies/rubah-pastel.jpg',
                'purchase_price' => 60000.00,
                'selling_price' => 105000.00,
                'stock' => 2, // Low stock alert (2 <= 5)
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Boneka Wisuda & Buket'],
                'sku' => 'BNK-WSD-001',
                'name' => 'Boneka Beruang Toga Wisuda Custom Nama',
                'size' => 'M (30cm)',
                'material' => 'Rasfur Lembut + Toga Satin & Selempang Bludru Bordir',
                'image_url' => 'images/plushies/beruang-wisuda.jpg',
                'purchase_price' => 50000.00,
                'selling_price' => 99000.00,
                'stock' => 12,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Boneka Wisuda & Buket'],
                'sku' => 'BNK-WSD-002',
                'name' => 'Buket Boneka Mini Graduation Series',
                'size' => 'S (25cm)',
                'material' => 'Mini Bear Yelvo + Kertas Buket Wrapping Korea & Pita Satin',
                'image_url' => 'images/plushies/buket-boneka.jpg',
                'purchase_price' => 40000.00,
                'selling_price' => 75000.00,
                'stock' => 7,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Bantal Boneka (Plush Pillow)'],
                'sku' => 'BNK-BTL-001',
                'name' => 'Bantal Boneka Leher Travel Shiba Inu',
                'size' => 'M (30cm)',
                'material' => 'Spandek Fleece Lembut Dingin + Memory Foam Partikel',
                'image_url' => 'images/plushies/bantal-shiba.jpg',
                'purchase_price' => 52000.00,
                'selling_price' => 92000.00,
                'stock' => 15,
                'minimum_stock' => 5,
            ],
        ];

        foreach ($products as $prod) {
            Product::updateOrCreate(
                ['sku' => $prod['sku']],
                $prod
            );
        }
    }
}
