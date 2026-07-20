# ADR-0011 — API URI versioning (`/api/v1`)

- **Status:** Accepted
- **Date:** 2026-07-20
- **Context:** ASVS / `docs/security.md` ask for versioned HTTP APIs so breaking changes do not silently break clients.

- **Decision:**
  1. **Canonical contract:** `/api/v1/*` mirrors the same route table as `/api/*` (same controllers, middleware, payloads).
  2. **Compatibility:** Unversioned `/api/*` remains supported for scripts, webhooks already pointed there, and gradual migration; no hard deprecation clock in MVP.
  3. **Breaking changes:** Introduce `/api/v2` (or a new resource shape under v1 only when additive). Never silently change semantics on an existing versioned path.
  4. **Webhooks:** Both `/api/webhooks/...` and `/api/v1/webhooks/...` are CSRF-exempt; prefer `/api/v1/webhooks/...` for new provider configs.
  5. **SPA:** First-party React client uses `/api/v1` (`API_BASE` in `frontend/src/shared/api/client.ts`) as of 2026-07-20 cutover.

- **Consequences:**
  - Routes are registered twice from `routes/api.php` (default `api` prefix + `api/v1`).
  - Idempotency scopes stay logical (`sales.complete:{id}`), not URI-based — aliases do not split claims.
  - Domain docs may still show `/api/...` shorthand; treat as equivalent to `/api/v1/...` unless noted.
