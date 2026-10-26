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
                'category_id' => $categories['Pashmina Cantik'],
                'sku' => 'HJB-PSH-001',
                'name' => 'Pashmina Ceruty Babydoll Premium Rose Dust',
                'size' => '175 x 75 cm',
                'material' => 'Ceruty Babydoll Armani Original (Jatuh & Flowy)',
                'image_url' => 'images/hijab/pashmina-ceruty.svg',
                'purchase_price' => 25000.00,
                'selling_price' => 45000.00,
                'stock' => 25,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Pashmina Cantik'],
                'sku' => 'HJB-PSH-002',
                'name' => 'Pashmina Silk Cradenza Shimmer Champagne',
                'size' => '180 x 75 cm',
                'material' => 'Silk Cradenza Shimmering Glossy Lux',
                'image_url' => 'images/hijab/pashmina-silk.svg',
                'purchase_price' => 35000.00,
                'selling_price' => 65000.00,
                'stock' => 3, // Low stock alert (3 <= 5)
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Pashmina Cantik'],
                'sku' => 'HJB-PSH-003',
                'name' => 'Pashmina Kaos Modal Rayon Soft Beige',
                'size' => '180 x 70 cm',
                'material' => 'Kaos Modal Rayon Super Adem & Elastis',
                'image_url' => 'images/hijab/pashmina-rayon.svg',
                'purchase_price' => 28000.00,
                'selling_price' => 49000.00,
                'stock' => 18,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Segi Empat (Square)'],
                'sku' => 'HJB-SQR-001',
                'name' => 'Segi Empat Voal Ultrafine Laser Cut Sage',
                'size' => '115 x 115 cm',
                'material' => 'Voal Ultrafine Tryspan Anti Letoy (Tegak Paripurna)',
                'image_url' => 'images/hijab/segiempat-voal.svg',
                'purchase_price' => 30000.00,
                'selling_price' => 55000.00,
                'stock' => 30,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Segi Empat (Square)'],
                'sku' => 'HJB-SQR-002',
                'name' => 'Segi Empat Paris Premium Jadul Lavender',
                'size' => '110 x 110 cm',
                'material' => 'Katun Paris Premium Serat Halus & Ringan',
                'image_url' => 'images/hijab/segiempat-paris.svg',
                'purchase_price' => 18000.00,
                'selling_price' => 35000.00,
                'stock' => 2, // Low stock alert (2 <= 5)
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Segi Empat (Square)'],
                'sku' => 'HJB-SQR-003',
                'name' => 'Segi Empat Motif Silk Eksklusif Botanical Royal',
                'size' => '120 x 120 cm',
                'material' => 'Velvet Silk Digital Printing Non-Slip',
                'image_url' => 'images/hijab/segiempat-motif.svg',
                'purchase_price' => 45000.00,
                'selling_price' => 85000.00,
                'stock' => 12,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Bergo & Hijab Instan'],
                'sku' => 'HJB-INS-001',
                'name' => 'Bergo Hamidah Daily Jersey Instan Peach',
                'size' => 'Size M (PD 70cm, PB 80cm)',
                'material' => 'Jersey Premium Spandex Tebal & Tidak Menerawang',
                'image_url' => 'images/hijab/bergo-hamidah.svg',
                'purchase_price' => 20000.00,
                'selling_price' => 38000.00,
                'stock' => 40,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Bergo & Hijab Instan'],
                'sku' => 'HJB-INS-002',
                'name' => 'Bergo Sporty Aerobic Dry-Fit Slate',
                'size' => 'Size S (PD 60cm, PB 70cm)',
                'material' => 'Dry-Fit Breathable Cepat Kering & Ringan',
                'image_url' => 'images/hijab/bergo-sport.svg',
                'purchase_price' => 17000.00,
                'selling_price' => 32000.00,
                'stock' => 4, // Low stock alert (4 <= 5)
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Khimar & Syar\'i Series'],
                'sku' => 'HJB-SYR-001',
                'name' => 'French Khimar 2 Layer Syar\'i Dusty Mauve',
                'size' => 'Jumbo (PD 110cm, PB 135cm)',
                'material' => 'Wollpeach Silky Jetblack Jatuh & Menutup Dada',
                'image_url' => 'images/hijab/khimar-french.svg',
                'purchase_price' => 65000.00,
                'selling_price' => 119000.00,
                'stock' => 15,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $categories['Inner & Aksesoris Hijab'],
                'sku' => 'HJB-ACC-001',
                'name' => 'Inner Ciput Ninja 4in1 Anti-Pusing Nude',
                'size' => 'All Size Elastic',
                'material' => 'Rayon Spandex Cooling Antibakteri & Anti Gerah',
                'image_url' => 'images/hijab/inner-ciput.svg',
                'purchase_price' => 12000.00,
                'selling_price' => 25000.00,
                'stock' => 50,
                'minimum_stock' => 10,
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
