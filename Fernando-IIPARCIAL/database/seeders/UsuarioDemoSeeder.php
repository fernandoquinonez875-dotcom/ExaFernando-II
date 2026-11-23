<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioDemoSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'admin',
                'email' => 'admin@Fernando-IIPARCIAL.test',
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
