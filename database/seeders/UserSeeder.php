<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Owner',
            'username' => 'owner',
            'email'    => 'owner@friedchicken.pos',
            'password' => Hash::make('owner123'),
            'role'     => 'owner',
            'is_active' => true,
        ]);

        User::create([
            'name'     => 'Firli',
            'username' => 'firli',
            'email'    => 'firli@friedchicken.pos',
            'password' => Hash::make('kasir123'),
            'role'     => 'kasir',
            'is_active' => true,
        ]);

        User::create([
            'name'     => 'Kasir 2',
            'username' => 'kasir2',
            'email'    => 'kasir2@friedchicken.pos',
            'password' => Hash::make('kasir123'),
            'role'     => 'kasir',
            'is_active' => true,
        ]);
    }
}
