<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BarangaySeeder::class,
            AmenitySeeder::class,
            UserSeeder::class,
            VenueSeeder::class,
            RolesSeeder::class, // last: the tables exist by now
        ]);
    }
}
