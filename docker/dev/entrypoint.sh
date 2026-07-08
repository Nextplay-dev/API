#!/bin/sh

psql -d postgres -tc "SELECT 1 FROM pg_database WHERE datname = 'nextplay_test'" | grep -q 1 || \
psql -d postgres -c "CREATE DATABASE nextplay_test;"

psql -d nextplay_test -c "CREATE EXTENSION IF NOT EXISTS btree_gist;"

service supervisor start &
exec php artisan octane:frankenphp --watch --poll --caddyfile=/etc/frankenphp/Caddyfile
