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
        $admin = User::where('email', 'admin@hijabsociety.com')->first() 
            ?? User::where('role', 'admin')->first();
        $staffGudang = User::where('email', 'cecep.staff@hijabsociety.com')->first() 
            ?? User::where('name', 'like', '%Cecep%')->first()
            ?? $admin;
        $kasir = User::where('email', 'staff@hijabsociety.com')->first() 
            ?? $admin;
        $nadia = User::where('email', 'customer@hijabsociety.com')->first() 
            ?? User::where('role', 'customer')->first();
        $zahra = User::where('email', 'zahra@hijabsociety.com')->first() 
            ?? $nadia;

        $supMandiri = Supplier::where('name', 'PT Hijab Mandiri Tekstil (Bandung)')->first();
        $supVoal = Supplier::where('name', 'Galeri Konveksi Voal Utama Surabaya')->first();

        $prodPashminaCeruty = Product::where('sku', 'HJB-PSH-001')->first();
        $prodPashminaSilk = Product::where('sku', 'HJB-PSH-002')->first();
        $prodVoal = Product::where('sku', 'HJB-SQR-001')->first();
        $prodParis = Product::where('sku', 'HJB-SQR-002')->first();
        $prodMotif = Product::where('sku', 'HJB-SQR-003')->first();
        $prodBergo = Product::where('sku', 'HJB-INS-001')->first();
        $prodKhimar = Product::where('sku', 'HJB-SYR-001')->first();
        $prodCiput = Product::where('sku', 'HJB-ACC-001')->first();

        // ---------------------------------------------------------------------
        // 1. PURCHASE 1 (Stock-In) - Batch Hijab Pashmina & Silk
        // ---------------------------------------------------------------------
        if ($supMandiri && $prodPashminaCeruty && $prodPashminaSilk) {
            $purchase1 = Purchase::updateOrCreate(
                ['purchase_number' => 'PO-202610-0001'],
                [
                    'supplier_id' => $supMandiri->id,
                    'user_id' => $admin->id,
                    'total' => 1250000.00,
                    'status' => 'completed',
                    'notes' => 'Restock awal bahan pashmina ceruty babydoll & cradenza shimmer silky.',
                    'purchased_at' => '2026-10-15 09:30:00',
                ]
            );

            $purchase1->items()->delete();
            PurchaseItem::create([
                'purchase_id' => $purchase1->id,
                'product_id' => $prodPashminaCeruty->id,
                'quantity' => 20,
                'unit_cost' => 25000.00,
                'subtotal' => 500000.00,
            ]);
            PurchaseItem::create([
                'purchase_id' => $purchase1->id,
                'product_id' => $prodPashminaSilk->id,
                'quantity' => 20,
                'unit_cost' => 37500.00,
                'subtotal' => 750000.00,
            ]);
        }

        // ---------------------------------------------------------------------
        // 2. PURCHASE 2 (Stock-In) - Batch Voal Laser Cut & Ciput Ninja
        // ---------------------------------------------------------------------
        if ($supVoal && $prodVoal && $prodCiput) {
            $purchase2 = Purchase::updateOrCreate(
                ['purchase_number' => 'PO-202610-0002'],
                [
                    'supplier_id' => $supVoal->id,
                    'user_id' => $staffGudang->id,
                    'total' => 1500000.00,
                    'status' => 'completed',
                    'notes' => 'Pengadaan batch baru voal ultrafine tryspan dan inner ciput ninja spandex.',
                    'purchased_at' => '2026-10-18 14:15:00',
                ]
            );

            $purchase2->items()->delete();
            PurchaseItem::create([
                'purchase_id' => $purchase2->id,
                'product_id' => $prodVoal->id,
                'quantity' => 30,
                'unit_cost' => 30000.00,
                'subtotal' => 900000.00,
            ]);
            PurchaseItem::create([
                'purchase_id' => $purchase2->id,
                'product_id' => $prodCiput->id,
                'quantity' => 50,
                'unit_cost' => 12000.00,
                'subtotal' => 600000.00,
            ]);
        }

        // ---------------------------------------------------------------------
        // 3. SALE 1 (Stock-Out) - Customer: Nadia Putri
        // ---------------------------------------------------------------------
        if ($nadia && $prodVoal && $prodPashminaCeruty) {
            $sale1 = Sale::updateOrCreate(
                ['sale_number' => 'INV-202610-0001'],
                [
                    'user_id' => $kasir->id,
                    'customer_id' => $nadia->id,
                    'customer_name' => $nadia->name,
                    'payment_method' => 'QRIS',
                    'payment_status' => 'paid',
                    'total' => 100000.00,
                    'status' => 'completed',
                    'notes' => 'Pesanan online HijabSociety via website catalog. Dikemas dengan box premium dan greeting card.',
                    'sold_at' => '2026-10-20 11:20:00',
                ]
            );

            $sale1->items()->delete();
            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $prodVoal->id,
                'quantity' => 1,
                'unit_price' => 55000.00,
                'subtotal' => 55000.00,
            ]);
            SaleItem::create([
                'sale_id' => $sale1->id,
                'product_id' => $prodPashminaCeruty->id,
                'quantity' => 1,
                'unit_price' => 45000.00,
                'subtotal' => 45000.00,
            ]);
        }

        // ---------------------------------------------------------------------
        // 4. SALE 2 (Stock-Out) - Customer: Zahra Pradiza
        // ---------------------------------------------------------------------
        if ($zahra && $prodKhimar && $prodCiput) {
            $sale2 = Sale::updateOrCreate(
                ['sale_number' => 'INV-202610-0002'],
                [
                    'user_id' => $kasir->id,
                    'customer_id' => $zahra->id,
                    'customer_name' => $zahra->name,
                    'payment_method' => 'Transfer Bank',
                    'payment_status' => 'paid',
                    'total' => 144000.00,
                    'status' => 'completed',
                    'notes' => 'Pembelian French Khimar Syar\'i 2 layer dusty mauve dan ciput ninja anti-pusing.',
                    'sold_at' => '2026-10-21 16:45:00',
                ]
            );

            $sale2->items()->delete();
            SaleItem::create([
                'sale_id' => $sale2->id,
                'product_id' => $prodKhimar->id,
                'quantity' => 1,
                'unit_price' => 119000.00,
                'subtotal' => 119000.00,
            ]);
            SaleItem::create([
                'sale_id' => $sale2->id,
                'product_id' => $prodCiput->id,
                'quantity' => 1,
                'unit_price' => 25000.00,
                'subtotal' => 25000.00,
            ]);
        }

        // ---------------------------------------------------------------------
        // 5. SALE 3 (Stock-Out) - Customer: Nadia Putri
        // ---------------------------------------------------------------------
        if ($nadia && $prodMotif && $prodPashminaSilk) {
            $sale3 = Sale::updateOrCreate(
                ['sale_number' => 'INV-202610-0003'],
                [
                    'user_id' => $kasir->id,
                    'customer_id' => $nadia->id,
                    'customer_name' => $nadia->name,
                    'payment_method' => 'E-Wallet',
                    'payment_status' => 'paid',
                    'total' => 150000.00,
                    'status' => 'completed',
                    'notes' => 'Pembelian langsung di kasir outlet butik HijabSociety.',
                    'sold_at' => '2026-10-22 13:10:00',
                ]
            );

            $sale3->items()->delete();
            SaleItem::create([
                'sale_id' => $sale3->id,
                'product_id' => $prodMotif->id,
                'quantity' => 1,
                'unit_price' => 85000.00,
                'subtotal' => 85000.00,
            ]);
            SaleItem::create([
                'sale_id' => $sale3->id,
                'product_id' => $prodPashminaSilk->id,
                'quantity' => 1,
                'unit_price' => 65000.00,
                'subtotal' => 65000.00,
            ]);
        }

        // ---------------------------------------------------------------------
        // 6. SALE 4 (Stock-Out) - Transaksi Kasir Hari Ini (Today's Sale)
        // ---------------------------------------------------------------------
        if ($nadia && $prodParis) {
            $saleToday = Sale::updateOrCreate(
                ['sale_number' => 'INV-' . now()->format('Ymd') . '-0001'],
                [
                    'user_id' => $kasir->id,
                    'customer_id' => $nadia->id,
                    'customer_name' => $nadia->name,
                    'payment_method' => 'Tunai / COD',
                    'payment_status' => 'paid',
                    'total' => 70000.00,
                    'status' => 'completed',
                    'notes' => 'Transaksi kasir butik hari ini - Segi Empat Paris Premium 2 pcs.',
                    'sold_at' => now(),
                ]
            );

            $saleToday->items()->delete();
            SaleItem::create([
                'sale_id' => $saleToday->id,
                'product_id' => $prodParis->id,
                'quantity' => 2,
                'unit_price' => 35000.00,
                'subtotal' => 70000.00,
            ]);
        }
    }
}
