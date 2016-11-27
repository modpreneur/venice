#!/bin/bash sh

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
mkdir -p /var/app/web/compiled

composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist --no-interaction

bin/console assetic:dump --env=prod --no-debug

chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/web/compiled
chmod -R 0777 /var/app/var/logs

exec apache2-foreground