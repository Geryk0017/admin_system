<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'admin@gmail.com',
            'first_name' => 'Geryk Martin',
            'middle_name' => '',
            'last_name' => 'Admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        User::create([
            'email' => 'verifier@gmail.com',
            'first_name' => 'Geryk Martin',
            'middle_name' => '',
            'last_name' => 'Verifier',
            'password' => Hash::make('verifier123'),
            'role' => 'verifier',
            'status' => 'active'
        ]);

        User::create([
            'email' => 'developer@gmail.com',
            'first_name' => 'Geryk Martin',
            'middle_name' => '',
            'last_name' => 'Developer',
            'password' => Hash::make('developer123'),
            'role' => 'developer',
            'status' => 'active'
        ]);

        User::create([
            'email' => 'developer2@gmail.com',
            'first_name' => 'Geryk Martin',
            'middle_name' => '',
            'last_name' => 'Developer 2',
            'password' => Hash::make('developer123'),
            'role' => 'developer',
            'status' => 'active'
        ]);
    }
}
