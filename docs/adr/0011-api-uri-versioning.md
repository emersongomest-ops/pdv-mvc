# ADR-0011 — API URI versioning (`/api/v1`)

- **Status:** Accepted
- **Date:** 2026-07-20
- **Context:** ASVS / `docs/security.md` ask for versioned HTTP APIs so breaking changes do not silently break clients. The SPA and tests today call unversioned `/api/*`. A full cutover would touch every frontend path and domain doc without a breaking change to justify it.

- **Decision:**
  1. **Canonical contract:** `/api/v1/*` mirrors the same route table as `/api/*` (same controllers, middleware, payloads).
  2. **Compatibility:** Unversioned `/api/*` remains supported for the first-party SPA until an explicit cutover; no deprecation clock in MVP.
  3. **Breaking changes:** Introduce `/api/v2` (or a new resource shape under v1 only when additive). Never silently change semantics on an existing versioned path.
  4. **Webhooks:** Both `/api/webhooks/...` and `/api/v1/webhooks/...` are CSRF-exempt; prefer documenting `/api/v1/webhooks/...` for external providers going forward.
  5. **SPA:** May keep `/api/...` until cutover; new external integrations should use `/api/v1/...`.

- **Consequences:**
  - Routes are registered twice from `routes/api.php` (default `api` prefix + `api/v1`).
  - Idempotency scopes stay logical (`sales.complete:{id}`), not URI-based — aliases do not split claims.
  - Checklist item “API versioning” closes as **policy + live alias**, not as SPA migration.
