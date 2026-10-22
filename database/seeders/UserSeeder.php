<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // forceCreate skips $fillable, because role is not mass assignable.
        // The 'hashed' cast in User.php hashes the password automatically.
        User::forceCreate([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'is_approved' => true,
        ]);

        User::forceCreate([
            'name' => 'Sample Owner',
            'email' => 'owner@example.com',
            'password' => 'password',
            'role' => 'owner',
            'is_approved' => true,
        ]);

        User::forceCreate([
            'name' => 'Second Owner',
            'email' => 'owner2@example.com',
            'password' => 'password',
            'role' => 'owner',
            'is_approved' => true,
        ]);

        User::forceCreate([
            'name' => 'Sample Customer',
            'email' => 'customer@example.com',
            'password' => 'password',
            'role' => 'customer',
            'is_approved' => true,
        ]);
    }
}
