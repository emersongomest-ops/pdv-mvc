# Production hardening (ASVS L2 — PDV)

> Local Docker stays HTTP on `:8080`. Production must terminate **TLS** in front of (or on) nginx and flip secure defaults below.

## Required application env (`APP_ENV=production`)

Copy from [`backend/.env.production.example`](../../backend/.env.production.example) and fill secrets.

| Variable | Required value | Why |
|----------|----------------|-----|
| `APP_ENV` | `production` | Enables fail-closed boot guard |
| `APP_DEBUG` | `false` | No stack traces to clients (ASVS V7) |
| `SESSION_SECURE_COOKIE` | `true` | `Secure` flag on session cookie (ASVS V3; needs HTTPS) |
| `SESSION_HTTP_ONLY` | `true` | Default; keep |
| `SESSION_SAME_SITE` | `lax` (or `strict` if UX allows) | CSRF posture |
| `APP_URL` | `https://…` | Canonical HTTPS origin |
| `LOG_LEVEL` | `info` or `warning` | Avoid debug noise/PII in logs |

**Boot guard:** `ProductionSecurityConfigAssertor` aborts boot if `production` + (`APP_DEBUG=true` **or** `SESSION_SECURE_COOKIE` not true). Verified by `tests/Unit/Support/Security/ProductionSecurityConfigAssertorTest.php`.

## TLS + HSTS (edge)

1. Terminate TLS (load balancer, Caddy, or nginx with certificates).
2. Use [`docker/nginx.tls.conf.example`](../../docker/nginx.tls.conf.example) as a starting point (includes `Strict-Transport-Security`).
3. Do **not** enable HSTS on plain HTTP local compose — browsers would pin HTTPS and break `:8080`.

## Pre-go-live checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`
- [ ] HTTPS only for clients; HTTP redirects to HTTPS
- [ ] HSTS header present on HTTPS responses (`max-age` ≥ 31536000; add `includeSubDomains` only if all subdomains are ready)
- [ ] `SANCTUM_STATEFUL_DOMAINS` lists the production SPA host(s)
- [ ] Redis/MySQL not published on the public internet (Compose default)
- [ ] `composer audit` / `npm audit` clean (see `docs/security.md` §15)
- [ ] Backup restore smoke (`docs/ops/backup-restore.md`)
- [ ] External pen-test scheduled (still open on `docs/security.md` §12)

## Local vs production

| Concern | Local (`.env.docker` / `.env.example`) | Production |
|---------|----------------------------------------|------------|
| `APP_DEBUG` | `true` OK | **must** `false` |
| `SESSION_SECURE_COOKIE` | `false` OK on HTTP | **must** `true` |
| HSTS | omitted | required behind TLS |
| Port publish | `:8080` only | TLS 443; no DB/Redis public ports |
