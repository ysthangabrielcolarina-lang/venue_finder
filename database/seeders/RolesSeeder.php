<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Runs database/sql/roles.sql so the grants exist again after every rebuild.
        DB::unprepared(file_get_contents(database_path('sql/roles.sql')));
    }
}
