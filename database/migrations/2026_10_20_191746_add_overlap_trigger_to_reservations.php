<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The function holds the rule.
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION prevent_overlapping_reservation() RETURNS trigger AS $$
        BEGIN
          IF NEW.status IN ('pending', 'confirmed') AND EXISTS (
            SELECT 1 FROM reservations r
            WHERE r.venue_id = NEW.venue_id
              AND r.id <> COALESCE(NEW.id, 0)
              AND (r.status = 'confirmed' OR (r.status = 'pending' AND r.expires_at > now()))
              AND NEW.start_date <= r.end_date
              AND NEW.end_date   >= r.start_date
          ) THEN
            RAISE EXCEPTION 'Venue already booked for these dates';
          END IF;
          RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;
        SQL);

        // The trigger tells PostgreSQL when to run it.
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_no_overlap
        BEFORE INSERT OR UPDATE ON reservations
        FOR EACH ROW EXECUTE FUNCTION prevent_overlapping_reservation();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_no_overlap ON reservations');
        DB::unprepared('DROP FUNCTION IF EXISTS prevent_overlapping_reservation()');
    }
};
