<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestRoleSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'testrole@example.com'],
            [
                'name' => 'TestRole',
                'password' => Hash::make('testpass123'),
                'role' => 'manager',
            ]
        );
    }
}
