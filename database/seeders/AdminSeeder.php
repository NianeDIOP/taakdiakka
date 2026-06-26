<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Compte super administrateur par défaut (pur admin, sans profil membre).
        User::updateOrCreate(
            ['email' => 'admin@taakdiakka.test'],
            [
                'name'     => 'Administration',
                'password' => Hash::make('admin1234'),
                'role'     => 'super_admin',
                'is_admin' => true,
                'status'   => 'active',
            ],
        );
    }
}
