<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pashmina Cantik',
                'description' => 'Koleksi pashmina modern berbahan ceruty, shimmer silk, dan kaos modal yang flowy serta mudah dikreasikan.',
            ],
            [
                'name' => 'Segi Empat (Square)',
                'description' => 'Hijab segi empat voal ultrafine, paris premium, dan voal motif eksklusif yang tegak di dahi dan nyaman seharian.',
            ],
            [
                'name' => 'Bergo & Hijab Instan',
                'description' => 'Hijab instan praktis dan bergo daily wear berbahan jersey adem untuk aktivitas harian, rumah, dan olahraga.',
            ],
            [
                'name' => 'Khimar & Syar\'i Series',
                'description' => 'Koleksi khimar syar\'i 2 layer dan French Khimar berpotongan anggun, panjang, dan menutup aurat dengan sempurna.',
            ],
            [
                'name' => 'Inner & Aksesoris Hijab',
                'description' => 'Ciput ninja anti-pusing, bandana rajut stretch, scrunchie bervolume, dan tuspin mutiara pelengkap gaya hijab.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }
    }
}
