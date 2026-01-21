<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KasirSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Kasir Utama',
            'email' => 'kasir@toko.com',
            'password' => Hash::make('kasir123'),
            'email_verified_at' => now(), // WAJIB biar lolos middleware verified
        ]);
    }
}
