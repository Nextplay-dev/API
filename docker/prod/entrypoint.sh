#!/bin/sh

php artisan storage:link || true
php artisan migrate --force || true
php artisan db:seed --force || true
service supervisor start
supervisorctl start "worker:*"
supervisorctl start "schedule"
supervisorctl start "reverb"
exec php artisan octane:frankenphp --caddyfile=/etc/frankenphp/Caddyfile
