#!/bin/sh
set -eu

echo "[entrypoint] waiting for mysql..."
export MYSQL_PWD="${DB_PASSWORD:-pdv_secret}"
until mysqladmin ping -h"${DB_HOST:-mysql}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-pdv}" --silent; do
  sleep 2
done
unset MYSQL_PWD
echo "[entrypoint] mysql is up"

cd /var/www/html

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

if [ -z "${APP_KEY:-}" ]; then
  echo "[entrypoint] generating APP_KEY for this process"
  APP_KEY="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"
  export APP_KEY
fi

php artisan config:clear --no-interaction || true
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction
php artisan storage:link --no-interaction 2>/dev/null || true

exec "$@"
