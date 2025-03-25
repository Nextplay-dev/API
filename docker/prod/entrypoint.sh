#!/bin/sh

php artisan storage:link || true
service supervisor start
supervisorctl start "worker:*"
supervisorctl start "schedule"
php artisan migrate --force || true
php artisan db:seed --force || true
php artisan webpush:vapid
exec php artisan octane:frankenphp
