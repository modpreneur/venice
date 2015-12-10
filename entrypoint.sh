#!/bin/bash sh

composer run-script post-install-cmd --no-interaction

mkdir -p /var/app/var/cache
mkdir -p /var/app/var/logs
chmod -R 0777 /var/app/var/cache
chmod -R 0777 /var/app/var/logs

exec apache2-foreground