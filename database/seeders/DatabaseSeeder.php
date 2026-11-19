<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            // password is 'password' by default in UserFactory
        ]);

        $this->call([
            UserSeeder::class,
            DomainSeeder::class,
            PseSeeder::class,
        ]);
    }
}
