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
                'name' => 'Administrator Utama',
                'email' => 'admin@admin.com',
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Operator Sistem',
                'email' => 'operator@admin.com',
                'role' => User::ROLE_OPERATOR,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.operator@diskominfo.go.id',
                'role' => User::ROLE_OPERATOR,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.user@jatimprov.go.id',
                'role' => User::ROLE_USER,
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Ahmad Rivaldi',
                'email' => 'ahmad.user@jatimprov.go.id',
                'role' => User::ROLE_USER,
                'status' => User::STATUS_INACTIVE,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.operator@dinas.go.id',
                'role' => User::ROLE_OPERATOR,
                'status' => User::STATUS_INACTIVE,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => $userData['role'],
                    'status' => $userData['status'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
