# Key rotation runbook — PDV

> **Date:** 2026-07-20  
> **Closes:** threat-model residual **TM-05** / ASVS “Key management” Partial  
> **Related:** [`backup-restore.md`](./backup-restore.md), [`production-hardening.md`](./production-hardening.md), ADR-0008, ADR-0010

**Principles**

1. Never store DB dumps and encryption keys in the same vault folder.  
2. Rotate one key family per change window (do not rotate `APP_KEY` and PII keys in the same untested step).  
3. Prefer staging rehearsal with a recent restore smoke.  
4. Dual-decrypt (old+new key) is **not** implemented in app code today — most rotations need a **maintenance window** or a one-off re-encrypt script.

Generate 32-byte keys the same way Laravel does:

```bash
# from projects/pdv/backend
php -r "echo 'base64:'.base64_encode(random_bytes(32)), PHP_EOL;"
```

Or `php artisan key:generate --show` for `APP_KEY`-shaped values.

---

## Inventory

| Secret | Env var | Used for | Rotation impact |
|--------|---------|----------|-----------------|
| App encryption | `APP_KEY` | Cookie/session encryption, Eloquent `encrypted` casts (MFA secret, recovery codes) | All sessions invalid; MFA ciphertext unreadable until re-encrypt or re-enroll |
| Customer PII | `CUSTOMER_PII_ENCRYPTION_KEY` | AES-256-CBC customer fields (`PiiCrypto`) | Cannot decrypt customers without re-encrypt under new key |
| Blind index | `CUSTOMER_PII_BLIND_INDEX_KEY` | `cpf_hash` / `email_hash` HMAC | Lookups break until all hashes recomputed |
| Payment webhook | `PAYMENT_WEBHOOK_SECRET` | HMAC verify | Webhooks fail until provider + app match |
| Redis | `REDIS_PASSWORD` | Cache/queue/session if Redis | Restart clients with new password |
| DB | `DB_PASSWORD` / MySQL users | OLTP | App downtime while updating |

---

## Pre-flight (every rotation)

| Step | OK |
|------|----|
| Announce maintenance; stop POS writers if decrypt/re-encrypt needed | ☐ |
| Fresh MySQL backup + SHA256 (`scripts/backup-mysql.sh`) | ☐ |
| Confirm backup **without** embedding the new keys in the same archive | ☐ |
| Copy current `.env` / secret manager values to a break-glass note (offline) | ☐ |
| Staging: restore smoke (`scripts/restore-mysql-verify.sh`) then dry-run the same rotation | ☐ |
| On-call + rollback owner named | ☐ |

---

## A. `PAYMENT_WEBHOOK_SECRET` (lowest risk)

1. Generate new secret; store in vault.  
2. Configure acquirer to **also** accept the new secret (or cut over in their UI).  
3. Deploy app env with the new secret.  
4. Send a test webhook (or wait for next real event); confirm `payment_webhook_events` / payment line transition.  
5. Retire old secret on provider side.

**Rollback:** restore previous env value; re-point provider.

---

## B. `REDIS_PASSWORD` / `DB_PASSWORD`

1. Update secret in Redis/MySQL **and** app env in one coordinated restart.  
2. `docker compose up -d` (or orchestrator rolling update) so app picks new password.  
3. Smoke: login, queue worker processes a job, cache write/read.

**Rollback:** revert both sides together (DB/Redis + env).

---

## C. `APP_KEY`

Laravel does not keep dual keys for `encrypted` casts in this project.

### Preferred MVP procedure (force MFA re-enroll)

1. Maintenance window; backup DB.  
2. Set new `APP_KEY` in env; clear config cache (`php artisan config:clear`).  
3. Invalidate sessions (flush session store / Redis session keys / `sessions` table).  
4. For each manager with MFA: clear `mfa_secret`, `mfa_confirmed_at`, `mfa_recovery_codes`, `mfa_last_otp_timestamp` (or use admin MFA reset RN-074 while old key still valid **before** cutover — do resets **before** changing `APP_KEY`).  
5. After cutover: managers complete MFA setup again on next login.  
6. Smoke: operator login; manager MFA enroll + verify.

### Alternative (re-encrypt MFA fields)

Only if you must preserve existing TOTP secrets without re-enroll:

1. With **old** `APP_KEY` still active, export decryptable MFA payloads (one-off artisan/tinker — do not log secrets).  
2. Switch `APP_KEY`.  
3. Re-write encrypted attributes under the new key.  
4. Verify managers can still TOTP.

There is **no** shipped artisan command for this — treat as custom script reviewed in PR, run once, delete.

**Rollback:** restore previous `APP_KEY` **and** DB backup taken before step 2 (ciphertext is bound to the key that wrote it).

---

## D. `CUSTOMER_PII_ENCRYPTION_KEY`

`PiiCrypto` uses a single key (config `pii.encryption_key`). No dual-decrypt.

### Procedure (maintenance + re-encrypt)

1. Backup DB; keep **old** key in break-glass.  
2. Deploy a **one-off** re-encrypt job/command that for each customer:  
   - decrypt fields with old key;  
   - encrypt with new key;  
   - save (casts / `PiiCrypto`).  
3. Only after 100% success: point env to the **new** key; remove old key from running config.  
4. Smoke: admin customer show (full PII); operational CPF search (exact); create new customer.

Until that command exists in-repo, **do not** rotate this key in production without an approved script. Losing the old key with ciphertext still encrypted under it = permanent PII loss.

**Blind index note:** if only the encryption key rotates, hashes can stay. If you also rotate the blind-index key, do section E in the same window after ciphertext is readable.

---

## E. `CUSTOMER_PII_BLIND_INDEX_KEY`

1. With encryption key still able to decrypt (or plaintext available in memory during job): for each customer recompute `cpf_hash` / `email_hash` via `PiiCrypto::blindIndex` with the **new** key.  
2. Switch env to the new blind-index key.  
3. Smoke: uniqueness constraints; find-by-CPF / email.

Rotating this key **without** recomputing hashes breaks POS customer lookup.

---

## F. Post-rotation checklist

| Check | OK |
|-------|----|
| `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true` still true in prod | ☐ |
| Login operator + manager (MFA) | ☐ |
| Customer create + exact CPF find | ☐ |
| Payment webhook test event | ☐ |
| Queue / reconcile worker healthy | ☐ |
| Old secrets removed from servers and CI vars (kept only in vault offline for retention window) | ☐ |
| Incident ticket closed; threat-model TM-05 noted if first production rotation | ☐ |

---

## Cadence (suggested)

| Key | Cadence |
|-----|---------|
| `PAYMENT_WEBHOOK_SECRET` | On staff change / suspected leak / yearly |
| `APP_KEY` | On suspected leak; otherwise rare (high cost) |
| `CUSTOMER_PII_*` | On suspected leak; otherwise rare + always with re-encrypt job |
| Redis / DB passwords | On staff change / yearly |

---

## History

| Date | Note |
|------|------|
| 2026-07-20 | Initial runbook (no dual-key support in app; documents safe MVP paths) |
