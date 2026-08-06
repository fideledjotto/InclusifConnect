<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@inclusif-connect.org'],
            [
                'name' => "Équipe Inclusif Connect",
                'password' => Hash::make('ChangeMoi!2026'),
                'role' => 'administrateur',
            ]
        );
    }
}
