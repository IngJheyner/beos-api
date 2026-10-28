#!/bin/sh
set -e

# El código se monta desde el host. PHP-FPM y Artisan escriben cache y logs como www-data.
chown -R www-data:www-data storage bootstrap/cache

if [ "$(id -u)" = "0" ] && [ "$1" != "php-fpm" ]; then
  exec runuser -u www-data -- "$@"
fi

exec docker-php-entrypoint "$@"
