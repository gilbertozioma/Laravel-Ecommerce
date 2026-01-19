<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        \App\Models\User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
    }
}
