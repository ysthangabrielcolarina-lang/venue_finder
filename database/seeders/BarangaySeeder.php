<?php

namespace Database\Seeders;

use App\Models\Barangay;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        // TODO: the coordinates are placeholders. Replace each pair with the real
        // center point: in Google Maps, right-click the barangay and click the numbers.
        $barangays = [
            'Balayhangin', 'Bangyas', 'Dayap', 'Hanggan', 'Imok', 'Lamot 1',
            'Lamot 2', 'Limao', 'Mabacan', 'Masiit', 'Paliparan', 'Perez',
            'Kanluran', 'Silangan', 'Prinza', 'San Isidro', 'Santo Tomas',
        ];

        foreach ($barangays as $name) {
            Barangay::create([
                'name' => $name,
                'latitude' => 14.147000,
                'longitude' => 121.315000,
            ]);
        }
    }
}
