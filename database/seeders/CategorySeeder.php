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
                'name' => 'Teddy Bear Classic',
                'description' => 'Koleksi beruang teddy klasik berbagai ukuran, berbulu lembut dan cocok untuk kado romantis maupun koleksi premium.',
            ],
            [
                'name' => 'Boneka Karakter Hewan',
                'description' => 'Boneka karakter fauna menggemaskan seperti panda, kelinci, kucing, rubah, dan hewan lainnya.',
            ],
            [
                'name' => 'Plushie Anime & K-Pop',
                'description' => 'Koleksi boneka plushie chibi edisi khusus bertema anime jepang dan idol grup pop terfavorit.',
            ],
            [
                'name' => 'Boneka Wisuda & Buket',
                'description' => 'Boneka beruang ber-toga wisuda lengkap dengan selempang custom serta buket mini boneka kelulusan.',
            ],
            [
                'name' => 'Bantal Boneka (Plush Pillow)',
                'description' => 'Bantal multifungsi empuk berbentuk karakter boneka untuk relaksasi leher, sofa, maupun traveling.',
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
