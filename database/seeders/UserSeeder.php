<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),
            'nom' => 'Gueye',
            'prenom' => 'Mohamed',
            'email' => 'admin@gmail.com',
            'telephone' => '780118223',
            'adresse' => 'Dakar, Sénégal',
            'nci' => '1767200700455',
            'password' => Hash::make('admin123'),
        ]);

        User::factory(10)->create();
    }
}
