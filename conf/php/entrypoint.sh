#!/bin/sh
set -e

# Ensure Laravel writable directories are accessible on bind mounts.
# Avoid chown here to prevent failures on some host filesystems.
if [ -d /var/www/html/storage ]; then
  chmod -R 775 /var/www/html/storage || true
fi

if [ -d /var/www/html/bootstrap/cache ]; then
  chmod -R 775 /var/www/html/bootstrap/cache || true
fi

exec "$@"
