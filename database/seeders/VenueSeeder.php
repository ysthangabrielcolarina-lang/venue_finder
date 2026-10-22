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
        // TODO: replace these sample rows with your real venues.
        // Every venue has a participating owner, matched by the owner's email.
        $venues = [
            [
                'barangay' => 'Dayap', 'name' => 'Sample Venue A',
                'address' => 'Replace with real address', 'capacity' => 100,
                'base_price' => 15000, 'down_payment' => 5000,
                'gcash_number' => '09000000001',
                'amenities' => ['Parking', 'Videoke', 'Restrooms'],
                'owner' => 'owner@example.com',
            ],
            [
                'barangay' => 'Lamot 1', 'name' => 'Sample Venue B',
                'address' => 'Replace with real address', 'capacity' => 60,
                'base_price' => 8000, 'down_payment' => 0,
                'gcash_number' => null,
                'amenities' => ['Tables and chairs', 'Restrooms'],
                'owner' => 'owner2@example.com',
            ],
            [
                'barangay' => 'Mabacan', 'name' => 'Sample Venue C',
                'address' => 'Replace with real address', 'capacity' => 200,
                'base_price' => 30000, 'down_payment' => 10000,
                'gcash_number' => '09000000002',
                'amenities' => ['Air conditioning', 'Parking', 'Sound system', 'Kitchen'],
                'owner' => 'owner2@example.com',
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

            $owner = User::where('email', $data['owner'])->firstOrFail();
            $venue->owners()->attach($owner->id);
        }
    }
}
