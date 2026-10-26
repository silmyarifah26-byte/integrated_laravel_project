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
                'name' => 'PT Kreasi Boneka Nusantara (Bandung)',
                'phone' => '022-7654321',
                'email' => 'order@kreasiboneka.co.id',
                'address' => 'Kawasan Industri Cimahi Selatan Kav. 12, Bandung, Jawa Barat',
            ],
            [
                'name' => 'CV Cikampek Plushie Mandiri',
                'phone' => '0267-891234',
                'email' => 'sales@cikampekplushie.com',
                'address' => 'Jl. Raya Cikampek No. 45, Karawang, Jawa Barat',
            ],
            [
                'name' => 'Grosir Boneka Impor Surabaya',
                'phone' => '031-5432198',
                'email' => 'import@bonekasby.com',
                'address' => 'Komplek Pergudangan Margomulyo Permai Blok B-8, Surabaya, Jawa Timur',
            ],
            [
                'name' => 'Rumah Produksi Yelvo Lembut Jakarta',
                'phone' => '021-5876543',
                'email' => 'halo@yelvolembut.id',
                'address' => 'Jl. Kamal Muara Indah III No. 20, Penjaringan, Jakarta Utara',
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
