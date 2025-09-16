<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::create([
            'name' => 'Admin User',
            'email' => 'admin@sportyka.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Staff One',
            'email' => 'staff@example.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
        ]);

        User::factory()->create([
            'name' => 'Customer One',
            'email' => 'customer@example.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
        ]);

    }
}        
