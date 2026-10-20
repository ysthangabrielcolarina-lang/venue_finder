<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            'Air conditioning', 'Parking', 'Swimming pool', 'Videoke',
            'Kitchen', 'Tables and chairs', 'Restrooms', 'Sound system', 'WiFi',
        ];

        foreach ($amenities as $name) {
            Amenity::create(['name' => $name]);
        }
    }
}