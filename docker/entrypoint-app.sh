#!/bin/sh
set -eu

run_as_app() {
  if [ "$(id -u)" = "0" ] && command -v gosu >/dev/null 2>&1; then
    gosu www-data "$@"
  else
    "$@"
  fi
}

if [ "${DOCKER_STRICT:-false}" = "true" ]; then
  if [ -z "${DB_PASSWORD:-}" ]; then
    echo "[entrypoint] DOCKER_STRICT: DB_PASSWORD is required" >&2
    exit 1
  fi
  if [ -z "${REDIS_PASSWORD:-}" ]; then
    echo "[entrypoint] DOCKER_STRICT: REDIS_PASSWORD is required" >&2
    exit 1
  fi
fi

echo "[entrypoint] waiting for mysql..."
export MYSQL_PWD="${DB_PASSWORD:?DB_PASSWORD is required}"
until mysqladmin ping -h"${DB_HOST:-mysql}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME:-pdv}" --silent; do
  sleep 2
done
unset MYSQL_PWD
echo "[entrypoint] mysql is up"

cd /var/www/html

# Docker uses env_file / process env; an empty .env avoids Dotenv file_get_contents warnings.
if [ ! -f .env ]; then
  touch .env
fi

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache storage/app
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

if [ -z "${APP_KEY:-}" ]; then
  KEY_FILE="storage/app/.docker_app_key"
  if [ -f "$KEY_FILE" ]; then
    echo "[entrypoint] loading APP_KEY from $KEY_FILE (set APP_KEY in .env.docker to pin explicitly)"
    APP_KEY="$(cat "$KEY_FILE")"
  else
    if [ "${DOCKER_STRICT:-false}" = "true" ]; then
      echo "[entrypoint] DOCKER_STRICT: APP_KEY is required (or existing $KEY_FILE)" >&2
      exit 1
    fi
    echo "[entrypoint] generating APP_KEY and persisting to $KEY_FILE"
    APP_KEY="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"
    printf '%s' "$APP_KEY" > "$KEY_FILE"
    chmod 600 "$KEY_FILE" 2>/dev/null || true
    chown www-data:www-data "$KEY_FILE" 2>/dev/null || true
  fi
  export APP_KEY
fi

run_as_app php artisan config:clear --no-interaction || true

if [ "${MIGRATE_ON_BOOT:-true}" = "true" ]; then
  run_as_app php artisan migrate --force --no-interaction
fi

if [ "${SEED_ON_BOOT:-false}" = "true" ]; then
  run_as_app php artisan db:seed --force --no-interaction
fi

# Symlink needs write on public/ (root); ignore if already linked
php artisan storage:link --no-interaction 2>/dev/null || true

# php-fpm master typically starts as root and drops workers to www-data
exec "$@"
