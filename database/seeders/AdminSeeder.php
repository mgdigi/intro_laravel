<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crée 2 admins avec leurs users
        Admin::factory(2)->create();
    }
}
