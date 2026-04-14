<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ggcasestore.com'],
            [
                'name'     => 'Admin GG Case',
                'email'    => 'admin@ggcasestore.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );
    }
}
