<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Who is logged in: the website sets app.user_id when Laravel authenticates the user.
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION app_user_id() RETURNS bigint
        LANGUAGE sql STABLE AS $$
            SELECT NULLIF(current_setting('app.user_id', true), '')::bigint
        $$;
        SQL);

        // The role is read from the users table, so the website only says WHO is logged in.
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION app_user_role() RETURNS text
        LANGUAGE sql STABLE SECURITY DEFINER SET search_path = public AS $$
            SELECT role FROM users WHERE id = app_user_id()
        $$;
        SQL);

        // Availability check that sees ALL reservations but returns only true or false.
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION dates_are_taken(p_venue bigint, p_start date, p_end date, p_except bigint DEFAULT NULL)
        RETURNS boolean
        LANGUAGE sql STABLE SECURITY DEFINER SET search_path = public AS $$
            SELECT EXISTS (
                SELECT 1 FROM reservations r
                WHERE r.venue_id = p_venue
                  AND (p_except IS NULL OR r.id <> p_except)
                  AND (r.status = 'confirmed' OR (r.status = 'pending' AND r.expires_at > now()))
                  AND p_start <= r.end_date
                  AND p_end >= r.start_date
            )
        $$;
        SQL);

        // The overlap trigger must also see every reservation, so it now runs with the owner's rights.
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION prevent_overlapping_reservation() RETURNS trigger
        LANGUAGE plpgsql SECURITY DEFINER SET search_path = public AS $$
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
        $$;
        SQL);

        // Turn row-level security on. Without a matching policy, a row is invisible.
        DB::unprepared('ALTER TABLE reservations ENABLE ROW LEVEL SECURITY');

        DB::unprepared(<<<'SQL'
        CREATE POLICY reservations_select ON reservations FOR SELECT
        USING (
            user_id = app_user_id()
            OR app_user_role() = 'admin'
            OR venue_id IN (SELECT venue_id FROM venue_user WHERE user_id = app_user_id())
        );
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE POLICY reservations_insert ON reservations FOR INSERT
        WITH CHECK (user_id = app_user_id());
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE POLICY reservations_update ON reservations FOR UPDATE
        USING (
            user_id = app_user_id()
            OR app_user_role() = 'admin'
            OR venue_id IN (SELECT venue_id FROM venue_user WHERE user_id = app_user_id())
        )
        WITH CHECK (
            user_id = app_user_id()
            OR app_user_role() = 'admin'
            OR venue_id IN (SELECT venue_id FROM venue_user WHERE user_id = app_user_id())
        );
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE POLICY reservations_delete ON reservations FOR DELETE
        USING (user_id = app_user_id() OR app_user_role() = 'admin');
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP POLICY IF EXISTS reservations_delete ON reservations');
        DB::unprepared('DROP POLICY IF EXISTS reservations_update ON reservations');
        DB::unprepared('DROP POLICY IF EXISTS reservations_insert ON reservations');
        DB::unprepared('DROP POLICY IF EXISTS reservations_select ON reservations');
        DB::unprepared('ALTER TABLE reservations DISABLE ROW LEVEL SECURITY');

        // Back to the original trigger function (no SECURITY DEFINER).
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

        DB::unprepared('DROP FUNCTION IF EXISTS dates_are_taken(bigint, date, date, bigint)');
        DB::unprepared('DROP FUNCTION IF EXISTS app_user_role()');
        DB::unprepared('DROP FUNCTION IF EXISTS app_user_id()');
    }
};
