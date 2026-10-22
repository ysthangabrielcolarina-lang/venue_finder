-- Nobody gets to create tables in the public schema except the administrator.
REVOKE CREATE ON SCHEMA public FROM PUBLIC;

-- Let both roles connect and see the schema.
GRANT CONNECT ON DATABASE venue_finder TO venue_app, venue_readonly;
GRANT USAGE ON SCHEMA public TO venue_app, venue_readonly;

-- venue_app: what the website needs, nothing more.
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO venue_app;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO venue_app;

-- Tables created later by the administrator get the same permissions automatically.
ALTER DEFAULT PRIVILEGES IN SCHEMA public
  GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO venue_app;
ALTER DEFAULT PRIVILEGES IN SCHEMA public
  GRANT USAGE, SELECT ON SEQUENCES TO venue_app;

-- venue_readonly: public information only. No users, no reservations.
GRANT SELECT ON venues, amenities, amenity_venue, barangays TO venue_readonly;