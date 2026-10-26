<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@bonekaku.test')->first();
        $staffGudang = User::where('email', 'budi.staff@bonekaku.test')->first() ?? $admin;
        $kasir = User::where('email', 'staff@bonekaku.test')->first() ?? $admin;
        $nadia = User::where('email', 'customer@bonekaku.test')->first();
        $rizky = User::where('email', 'rizky@bonekaku.test')->first();

        $supNusantara = Supplier::where('name', 'PT Kreasi Boneka Nusantara (Bandung)')->first();
        $supYelvo = Supplier::where('name', 'Rumah Produksi Yelvo Lembut Jakarta')->first();

        $prodGiant = Product::where('sku', 'BNK-TDB-001')->first();
        $prodSweater = Product::where('sku', 'BNK-TDB-002')->first();
        $prodPanda = Product::where('sku', 'BNK-ANM-001')->first();
        $prodKucing = Product::where('sku', 'BNK-ANM-003')->first();
        $prodChibi = Product::where('sku', 'BNK-PLH-001')->first();
        $prodWisuda = Product::where('sku', 'BNK-WSD-001')->first();
        $prodBuket = Product::where('sku', 'BNK-WSD-002')->first();
        $prodBantal = Product::where('sku', 'BNK-BTL-001')->first();

        // ---------------------------------------------------------------------
        // 1. PURCHASE 1 (Stock-In)
        // ---------------------------------------------------------------------
        $purchase1 = Purchase::updateOrCreate(
            ['purchase_number' => 'PO-202610-0001'],
            [
                'supplier_id' => $supNusantara->id,
                'user_id' => $admin->id,
                'total' => 2775000.00,
                'status' => 'completed',
                'notes' => 'Restock batch awal beruang teddy bear untuk persiapan musim liburan.',
                'purchased_at' => '2026-10-15 09:30:00',
            ]
        );

        $purchase1->items()->delete();
        PurchaseItem::create([
            'purchase_id' => $purchase1->id,
            'product_id' => $prodGiant->id,
            'quantity' => 10,
            'unit_cost' => 180000.00,
            'subtotal' => 1800000.00,
        ]);
        PurchaseItem::create([
            'purchase_id' => $purchase1->id,
            'product_id' => $prodSweater->id,
            'quantity' => 15,
            'unit_cost' => 65000.00,
            'subtotal' => 975000.00,
        ]);

        // ---------------------------------------------------------------------
        // 2. PURCHASE 2 (Stock-In)
        // ---------------------------------------------------------------------
        $purchase2 = Purchase::updateOrCreate(
            ['purchase_number' => 'PO-202610-0002'],
            [
                'supplier_id' => $supYelvo->id,
                'user_id' => $staffGudang->id,
                'total' => 3190000.00,
                'status' => 'completed',
                'notes' => 'Pengadaan batch baru boneka karakter hewan & bantal leher microfiber lembut.',
                'purchased_at' => '2026-10-18 14:15:00',
            ]
        );

        $purchase2->items()->delete();
        PurchaseItem::create([
            'purchase_id' => $purchase2->id,
            'product_id' => $prodPanda->id,
            'quantity' => 20,
            'unit_cost' => 55000.00,
            'subtotal' => 1100000.00,
        ]);
        PurchaseItem::create([
            'purchase_id' => $purchase2->id,
            'product_id' => $prodKucing->id,
            'quantity' => 15,
            'unit_cost' => 70000.00,
            'subtotal' => 1050000.00,
        ]);
        PurchaseItem::create([
            'purchase_id' => $purchase2->id,
            'product_id' => $prodBantal->id,
            'quantity' => 20,
            'unit_cost' => 52000.00,
            'subtotal' => 1040000.00,
        ]);

        // ---------------------------------------------------------------------
        // 3. SALE 1 (Stock-Out) - Customer: Nadia
        // ---------------------------------------------------------------------
        $sale1 = Sale::updateOrCreate(
            ['sale_number' => 'INV-202610-0001'],
            [
                'user_id' => $kasir->id,
                'customer_id' => $nadia->id,
                'customer_name' => $nadia->name,
                'payment_method' => 'QRIS',
                'payment_status' => 'paid',
                'total' => 465000.00,
                'status' => 'completed',
                'notes' => "Pembelian kado ulang tahun sahabat. Dilengkapi bungkus pita manis Diza's Plushie.",
                'sold_at' => '2026-10-20 11:20:00',
            ]
        );

        $sale1->items()->delete();
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $prodGiant->id,
            'quantity' => 1,
            'unit_price' => 275000.00,
            'subtotal' => 275000.00,
        ]);
        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $prodPanda->id,
            'quantity' => 2,
            'unit_price' => 95000.00,
            'subtotal' => 190000.00,
        ]);

        // ---------------------------------------------------------------------
        // 4. SALE 2 (Stock-Out) - Customer: Rizky
        // ---------------------------------------------------------------------
        $sale2 = Sale::updateOrCreate(
            ['sale_number' => 'INV-202610-0002'],
            [
                'user_id' => $kasir->id,
                'customer_id' => $rizky->id,
                'customer_name' => $rizky->name,
                'payment_method' => 'Transfer Bank',
                'payment_status' => 'paid',
                'total' => 273000.00,
                'status' => 'completed',
                'notes' => 'Pesanan kado wisuda rekan kampus. Termasuk kartu ucapan graduation custom.',
                'sold_at' => '2026-10-21 16:45:00',
            ]
        );

        $sale2->items()->delete();
        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $prodWisuda->id,
            'quantity' => 2,
            'unit_price' => 99000.00,
            'subtotal' => 198000.00,
        ]);
        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $prodBuket->id,
            'quantity' => 1,
            'unit_price' => 75000.00,
            'subtotal' => 75000.00,
        ]);

        // ---------------------------------------------------------------------
        // 5. SALE 3 (Stock-Out) - Customer: Nadia
        // ---------------------------------------------------------------------
        $sale3 = Sale::updateOrCreate(
            ['sale_number' => 'INV-202610-0003'],
            [
                'user_id' => $kasir->id,
                'customer_id' => $nadia->id,
                'customer_name' => $nadia->name,
                'payment_method' => 'E-Wallet',
                'payment_status' => 'paid',
                'total' => 270000.00,
                'status' => 'completed',
                'notes' => "Pembelian langsung di kasir outlet Diza's Plushie Store.",
                'sold_at' => '2026-10-22 13:10:00',
            ]
        );

        $sale3->items()->delete();
        SaleItem::create([
            'sale_id' => $sale3->id,
            'product_id' => $prodChibi->id,
            'quantity' => 2,
            'unit_price' => 89000.00,
            'subtotal' => 178000.00,
        ]);
        SaleItem::create([
            'sale_id' => $sale3->id,
            'product_id' => $prodBantal->id,
            'quantity' => 1,
            'unit_price' => 92000.00,
            'subtotal' => 92000.00,
        ]);
    }
}
