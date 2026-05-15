#!/bin/sh

service supervisor start &
exec php artisan octane:frankenphp --watch --caddyfile=/etc/frankenphp/Caddyfile
