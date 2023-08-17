<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'uuid' => (string) Str::uuid(),
            'first_name' => 'admin',
            'last_name' => 'buckhill',
            'is_admin' => 1,
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin',
            'address' => 'Remetinečka cesta 13, 10000, Zagreb, Croatia',
            'phone_number' => '+385 1 4663 719',
        ]);
    }
}
