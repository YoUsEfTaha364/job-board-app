<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Company Owners
        User::create([
            'name' => 'Company Owner 1',
            'email' => 'owner1@example.com',
            'role' => 'company-owner',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Company Owner 2',
            'email' => 'owner2@example.com',
            'role' => 'company-owner',
            'password' => Hash::make('password'),
        ]);

        // Job Seekers
        User::create([
            'name' => 'Job Seeker 1',
            'email' => 'seeker1@example.com',
            'role' => 'job-seeker',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Job Seeker 2',
            'email' => 'seeker2@example.com',
            'role' => 'job-seeker',
            'password' => Hash::make('password'),
        ]);
    }
}
