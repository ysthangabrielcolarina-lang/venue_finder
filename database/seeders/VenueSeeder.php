<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Barangay;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'owner@example.com')->firstOrFail();

        // TODO: SAMPLE LANG TOH 
        $venues = [
            [
                'barangay' => 'Dayap', 'name' => 'Sample Venue A',
                'address' => 'Replace with real address', 'capacity' => 100,
                'base_price' => 15000, 'down_payment' => 5000,
                'gcash_number' => '09000000000',
                'amenities' => ['Parking', 'Videoke', 'Restrooms'],
                'has_owner' => true,
            ],
            [
                'barangay' => 'Lamot 1', 'name' => 'Sample Venue B',
                'address' => 'Replace with real address', 'capacity' => 60,
                'base_price' => 8000, 'down_payment' => 0,
                'gcash_number' => null,
                'amenities' => ['Tables and chairs', 'Restrooms'],
                'has_owner' => false,
            ],
            [
                'barangay' => 'Mabacan', 'name' => 'Sample Venue C',
                'address' => 'Replace with real address', 'capacity' => 200,
                'base_price' => 30000, 'down_payment' => 10000,
                'gcash_number' => null,
                'amenities' => ['Air conditioning', 'Parking', 'Sound system', 'Kitchen'],
                'has_owner' => false,
            ],
        ];

        foreach ($venues as $data) {
            $venue = Venue::create([
                'barangay_id' => Barangay::where('name', $data['barangay'])->firstOrFail()->id,
                'name' => $data['name'],
                'address' => $data['address'],
                'capacity' => $data['capacity'],
                'base_price' => $data['base_price'],
                'down_payment' => $data['down_payment'],
                'gcash_number' => $data['gcash_number'],
            ]);

            $venue->amenities()->attach(
                Amenity::whereIn('name', $data['amenities'])->pluck('id')
            );

            // Only venues with a participating owner get linked to one.
            // The others stay information-only, as in your plan.
            if ($data['has_owner']) {
                $venue->owners()->attach($owner->id);
            }
        }
    }
}