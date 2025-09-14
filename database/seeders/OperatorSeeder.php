<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'operator@example.com'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password123'),
                'role' => 'operator',
            ]
        );
    }
}