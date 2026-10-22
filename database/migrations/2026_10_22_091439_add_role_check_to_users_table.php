<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The database itself refuses any role other than these three.
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_users_role CHECK (role IN ('customer', 'owner', 'admin'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS chk_users_role');
    }
};
