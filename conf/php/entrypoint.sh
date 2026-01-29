#!/bin/sh
set -e

# Ensure Laravel writable directories are accessible on bind mounts.
if [ -d /var/www/html/storage ]; then
  chown -R www-data:www-data /var/www/html/storage
  chmod -R 775 /var/www/html/storage
fi

if [ -d /var/www/html/bootstrap/cache ]; then
  chown -R www-data:www-data /var/www/html/bootstrap/cache
  chmod -R 775 /var/www/html/bootstrap/cache
fi

exec "$@"
