<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name'       => 'System',
                'last_name'  => 'Administrator',
                'email'      => 'admin@pac.edu.ph',
                'password'   => Hash::make('PAC@Admin2025!'),
                'role'       => 'admin',
                'extra_info' => 'Super Admin — do not delete',
            ],
        ];

        foreach ($admins as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        $this->command->info('✅ Admin accounts seeded.');
        $this->command->table(
            ['Name', 'Email', 'Password'],
            array_map(fn($a) => [
                $a['name'] . ' ' . $a['last_name'],
                $a['email'],
                'PAC@Admin2025!  ← change this after first login!',
            ], $admins)
        );
    }
}