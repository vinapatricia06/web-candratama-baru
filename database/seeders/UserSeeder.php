<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User1;

class UserSeeder extends Seeder
{
    public function run()
    {
        User1::create([
            'nama' => 'Super Admin',
            'username' => 'superadmin',
            'password' => Hash::make('password123'),
            'role' => 'superadmin'
        ]);
    }
}
