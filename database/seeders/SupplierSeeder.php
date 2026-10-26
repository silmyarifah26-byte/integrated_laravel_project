<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Hijab Mandiri Tekstil (Bandung)',
                'phone' => '022-7654321',
                'email' => 'order@hijabmandiri.co.id',
                'address' => 'Kawasan Industri Tekstil Majalaya Kav. 18, Bandung, Jawa Barat',
            ],
            [
                'name' => 'CV Ceruty Silk Indonesia',
                'phone' => '021-5876543',
                'email' => 'sales@cerutysilk.com',
                'address' => 'Pusat Grosir Tanah Abang Blok A Lt. 3 No. 45, Jakarta Pusat',
            ],
            [
                'name' => 'Galeri Konveksi Voal Utama Surabaya',
                'phone' => '031-5432198',
                'email' => 'supply@voalutama.co.id',
                'address' => 'Komplek Pergudangan Rungkut Industri Blok E-12, Surabaya, Jawa Timur',
            ],
            [
                'name' => 'Pusat Aksesoris Muslimah Solo',
                'phone' => '0271-891234',
                'email' => 'halo@aksesorishijab.id',
                'address' => 'Jl. Slamet Riyadi No. 120, Surakarta, Jawa Tengah',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::updateOrCreate(
                ['name' => $sup['name']],
                $sup
            );
        }
    }
}
