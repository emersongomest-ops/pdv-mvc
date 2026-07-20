#!/usr/bin/env bash
# Restore smoke: load newest (or given) dump into pdv_restore_smoke, assert, drop.
# Does not modify the live `pdv` database.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

MYSQL_SERVICE="${MYSQL_SERVICE:-mysql}"
MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-root_secret}"
SMOKE_DB="${SMOKE_DB:-pdv_restore_smoke}"
BACKUP_DIR="${BACKUP_DIR:-$ROOT/backups}"
DUMP_FILE="${1:-}"

mysql_root() {
  docker compose exec -T "$MYSQL_SERVICE" \
    mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" "$@"
}

if ! docker compose ps --status running --services 2>/dev/null | grep -qx "$MYSQL_SERVICE"; then
  echo "error: service '$MYSQL_SERVICE' is not running" >&2
  exit 1
fi

if [[ -z "$DUMP_FILE" ]]; then
  DUMP_FILE="$(ls -1t "$BACKUP_DIR"/pdv-*.sql.gz 2>/dev/null | head -1 || true)"
fi

if [[ -z "$DUMP_FILE" || ! -f "$DUMP_FILE" ]]; then
  echo "error: no dump found. Run: bash scripts/backup-mysql.sh" >&2
  exit 1
fi

echo "[verify] using dump: $DUMP_FILE"

if [[ -f "${DUMP_FILE}.sha256" ]]; then
  echo "[verify] checking sha256"
  if command -v sha256sum >/dev/null 2>&1; then
    (cd "$(dirname "$DUMP_FILE")" && sha256sum -c "$(basename "$DUMP_FILE").sha256")
  elif command -v shasum >/dev/null 2>&1; then
    expected="$(awk '{print $1}' "${DUMP_FILE}.sha256")"
    actual="$(shasum -a 256 "$DUMP_FILE" | awk '{print $1}')"
    [[ "$expected" == "$actual" ]] || { echo "error: checksum mismatch" >&2; exit 1; }
  fi
fi

cleanup() {
  echo "[verify] dropping ${SMOKE_DB}"
  mysql_root -e "DROP DATABASE IF EXISTS \`${SMOKE_DB}\`;" || true
}
trap cleanup EXIT

echo "[verify] preparing ${SMOKE_DB}"
mysql_root -e "DROP DATABASE IF EXISTS \`${SMOKE_DB}\`; CREATE DATABASE \`${SMOKE_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Dump may contain "CREATE DATABASE / USE pdv" — rewrite to smoke DB.
echo "[verify] restoring into ${SMOKE_DB}"
gunzip -c "$DUMP_FILE" \
  | sed -e "s/\`pdv\`/\`${SMOKE_DB}\`/g" -e "s/USE \`pdv\`/USE \`${SMOKE_DB}\`/g" \
  | mysql_root

echo "[verify] asserting schema + seed row"
tables="$(mysql_root -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${SMOKE_DB}' AND table_name IN ('users','stores','products','sales','audit_logs');")"
if [[ "$tables" != "5" ]]; then
  echo "error: expected 5 core tables, found ${tables}" >&2
  exit 1
fi

manager_count="$(mysql_root -N -e "SELECT COUNT(*) FROM \`${SMOKE_DB}\`.users WHERE email='manager@pos.test';")"
if [[ "$manager_count" != "1" ]]; then
  echo "error: expected manager@pos.test row, found ${manager_count}" >&2
  exit 1
fi

# MFA / PII columns present (migration applied in source env)
mfa_col="$(mysql_root -N -e "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema='${SMOKE_DB}' AND table_name='users' AND column_name='mfa_secret';")"
if [[ "$mfa_col" != "1" ]]; then
  echo "error: users.mfa_secret missing — dump may be from pre-MFA schema" >&2
  exit 1
fi

echo "[verify] OK — restore path works (live database untouched)"
