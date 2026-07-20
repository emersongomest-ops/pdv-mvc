#!/usr/bin/env bash
# Backup MySQL (Docker Compose) → backups/pdv-*.sql.gz + sha256.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

MYSQL_SERVICE="${MYSQL_SERVICE:-mysql}"
MYSQL_DATABASE="${MYSQL_DATABASE:-pdv}"
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-root_secret}"
BACKUP_DIR="${BACKUP_DIR:-$ROOT/backups}"

mkdir -p "$BACKUP_DIR"

stamp="$(date -u +%Y%m%d-%H%M%S)"
out_sql="$BACKUP_DIR/pdv-${stamp}.sql"
out_gz="${out_sql}.gz"
out_sum="${out_gz}.sha256"

if ! docker compose ps --status running --services 2>/dev/null | grep -qx "$MYSQL_SERVICE"; then
  echo "error: service '$MYSQL_SERVICE' is not running (docker compose up -d)" >&2
  exit 1
fi

echo "[backup] dumping ${MYSQL_DATABASE} → ${out_gz}"
docker compose exec -T "$MYSQL_SERVICE" \
  mysqldump \
    -uroot \
    -p"${MYSQL_ROOT_PASSWORD}" \
    --single-transaction \
    --routines \
    --triggers \
    --hex-blob \
    --databases "$MYSQL_DATABASE" \
  > "$out_sql"

gzip -f "$out_sql"

if command -v sha256sum >/dev/null 2>&1; then
  sha256sum "$out_gz" > "$out_sum"
elif command -v shasum >/dev/null 2>&1; then
  shasum -a 256 "$out_gz" > "$out_sum"
else
  echo "warn: no sha256sum/shasum; skipping checksum" >&2
fi

echo "[backup] ok"
ls -lh "$out_gz" ${out_sum:+"$out_sum"}
