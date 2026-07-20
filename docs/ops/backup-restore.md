# Backup & restore (MySQL)

Operational runbook for the Docker Compose stack (`projects/pdv`). Satisfies the launch checklist item **Backup restore tested** in [`docs/security.md`](../security.md) §12.

## Scope

| Asset | In backup? | Notes |
|-------|------------|--------|
| MySQL (`pdv`) | **Yes** | Source of truth for sales, stock, users, encrypted customer PII, audit logs |
| Redis | No (MVP) | Cache / queue / payment outbox — rebuild empty; `payments:reconcile` recovers pending lines |
| `storage/` volume | Optional | Logs, local files; not required to reopen POS after DB restore |
| Secrets (`.env.docker`) | **Separate** | `APP_KEY`, `CUSTOMER_PII_*` — without them ciphertext is unusable (ADR-0008) |

**Do not** store DB dumps and encryption keys in the same place.

## Prerequisites

- Stack up: `docker compose up -d` from `projects/pdv`
- Bash + Docker Compose v2
- Enough disk under `projects/pdv/backups/` (gitignored)

## Backup

```bash
cd projects/pdv
bash scripts/backup-mysql.sh
```

Creates `backups/pdv-YYYYMMDD-HHMMSS.sql.gz` plus a small `*.sha256` sidecar.

Optional env overrides:

| Variable | Default |
|----------|---------|
| `COMPOSE_PROJECT` | current directory compose project |
| `MYSQL_SERVICE` | `mysql` |
| `MYSQL_DATABASE` | `pdv` |
| `MYSQL_ROOT_PASSWORD` | `root_secret` |
| `BACKUP_DIR` | `./backups` |

## Restore (production-like)

1. Stop writers (scale app to 0 or put maintenance).
2. Restore into the live database **only** after a fresh backup of the current state:

```bash
gunzip -c backups/pdv-YYYYMMDD-HHMMSS.sql.gz \
  | docker compose exec -T mysql \
    mysql -uroot -proot_secret pdv
```

3. Restart `app` (migrations should be no-op if dump includes schema).
4. Confirm secrets in `.env.docker` match the environment that produced the dump.
5. Smoke: login operator/manager, open shift, list products.

## Restore smoke test (safe)

Does **not** touch the live `pdv` schema. Creates `pdv_restore_smoke`, loads the dump, asserts tables + demo manager, then drops the DB.

```bash
cd projects/pdv
bash scripts/backup-mysql.sh          # optional if a dump already exists
bash scripts/restore-mysql-verify.sh  # uses newest backups/pdv-*.sql.gz
```

Exit `0` = restore path verified. Record date/operator in the checklist below.

## Checklist (fill after each drill)

| Step | OK | Date / who |
|------|----|------------|
| Backup script produced `.sql.gz` + `.sha256` | ☐ | |
| SHA256 matches file | ☐ | |
| `restore-mysql-verify.sh` exit 0 | ☐ | |
| Live restore drill (staging) login + one sale | ☐ | |
| Secrets restored from separate vault | ☐ | |
| Dump stored off-box / encrypted at rest | ☐ | |

## RPO / RTO (MVP targets)

| Metric | Target | How |
|--------|--------|-----|
| RPO | ≤ 24h | Daily cron of `backup-mysql.sh` + offsite copy |
| RTO | ≤ 2h | Documented restore + smoke; practice quarterly |

Tighten for production (binlog / continuous backup) when going multi-store live.

## Security notes

- Dumps contain **encrypted** customer PII columns and MFA secrets — treat as confidential.
- Prefer `mysqldump --single-transaction` (script default) for InnoDB consistency.
- Host port `3307` is for local ops only; do not expose in production.
