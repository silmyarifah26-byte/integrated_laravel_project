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
                'name' => 'Owner & Super Admin HijabSociety',
                'email' => 'admin@hijabsociety.com',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'address' => 'HijabSociety Flagship Store Lt. 2, Jl. Riau No. 45, Bandung',
            ],
            [
                'name' => 'Siti Aisyah (Kasir & Staff Toko)',
                'email' => 'staff@hijabsociety.com',
                'role' => 'staff',
                'password' => Hash::make('password123'),
                'phone' => '081234567891',
                'address' => 'Jl. Buah Batu Raya No. 120, Bandung',
            ],
            [
                'name' => 'Budi Santoso (Staff Gudang & Inventori)',
                'email' => 'budi.staff@hijabsociety.com',
                'role' => 'staff',
                'password' => Hash::make('password123'),
                'phone' => '081234567892',
                'address' => 'Jl. Soekarno Hatta No. 200, Bandung',
            ],
            [
                'name' => 'Nadia Putri (Customer)',
                'email' => 'customer@hijabsociety.com',
                'role' => 'customer',
                'password' => Hash::make('password123'),
                'phone' => '081987654321',
                'address' => 'Komplek Dago Asri Blok C-8, Bandung',
            ],
            [
                'name' => 'Zahra Pradiza (Customer)',
                'email' => 'zahra@hijabsociety.com',
                'role' => 'customer',
                'password' => Hash::make('password123'),
                'phone' => '081987654322',
                'address' => 'Jl. Setiabudi No. 15, Bandung',
            ],
            // Compatibility aliases for tests if needed
            [
                'name' => 'Admin Legacy Boneka',
                'email' => 'admin@bonekaku.test',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'phone' => '081234567899',
                'address' => 'Bandung',
            ],
            [
                'name' => 'Staff Legacy Boneka',
                'email' => 'staff@bonekaku.test',
                'role' => 'staff',
                'password' => Hash::make('password123'),
                'phone' => '081234567898',
                'address' => 'Bandung',
            ],
            [
                'name' => 'Staff Gudang Legacy',
                'email' => 'budi.staff@bonekaku.test',
                'role' => 'staff',
                'password' => Hash::make('password123'),
                'phone' => '081234567897',
                'address' => 'Bandung',
            ],
            [
                'name' => 'Customer Legacy',
                'email' => 'customer@bonekaku.test',
                'role' => 'customer',
                'password' => Hash::make('password123'),
                'phone' => '081987654329',
                'address' => 'Bandung',
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
