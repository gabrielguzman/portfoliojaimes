#!/bin/sh
set -eu
cd /var/www/html
mkdir -p storage/app/public storage/app/private storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage bootstrap/cache
php artisan storage:link --force --no-interaction
php artisan migrate --force --no-interaction
php artisan optimize --no-interaction
su -s /bin/sh www-data -c 'node bootstrap/ssr/ssr.js' &
exec apache2-foreground
