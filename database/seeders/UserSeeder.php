<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => "Admin Owner Diza's Plushie",
                'email' => 'admin@bonekaku.test',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'address' => 'Ruko Boneka Ceria Lt. 2, Jl. Tanjung Duren No. 88, Jakarta Barat',
            ],
            [
                'name' => 'Siti Kasir & Gudang',
                'email' => 'staff@bonekaku.test',
                'role' => 'staff',
                'password' => Hash::make('password123'),
                'phone' => '081234567891',
                'address' => 'Jl. Kemanggisan Raya No. 14, Jakarta Barat',
            ],
            [
                'name' => 'Budi Staff Inventori',
                'email' => 'budi.staff@bonekaku.test',
                'role' => 'staff',
                'password' => Hash::make('password123'),
                'phone' => '081234567892',
                'address' => 'Jl. Kebon Jeruk Baru No. 25, Jakarta Barat',
            ],
            [
                'name' => 'Nadia Putri (Customer)',
                'email' => 'customer@bonekaku.test',
                'role' => 'customer',
                'password' => Hash::make('password123'),
                'phone' => '081987654321',
                'address' => 'Apartemen Mediterania Garden Tower B Lt. 12, Jakarta Barat',
            ],
            [
                'name' => 'Rizky Pratama (Customer)',
                'email' => 'rizky@bonekaku.test',
                'role' => 'customer',
                'password' => Hash::make('password123'),
                'phone' => '081987654322',
                'address' => 'Jl. Anggrek Cakra No. 7, Jakarta Barat',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
