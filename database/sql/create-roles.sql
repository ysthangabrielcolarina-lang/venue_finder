-- Run once per PostgreSQL server, as the postgres user.
-- Replace the passwords, and use the same values in your .env file.
CREATE ROLE venue_app LOGIN PASSWORD 'change_me_1';
CREATE ROLE venue_readonly LOGIN PASSWORD 'change_me_2';