# OWASP ASVS Level 2 — gap review (PDV)

- **Date:** 2026-07-20  
- **Standard:** [OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/) **4.0.3**, Level **2** (as applicable to a modular monolith SPA + Laravel API)  
- **Method:** Desk review against code, tests, and ops docs (not a third-party penetration test)  
- **Evidence roots:** `projects/pdv/backend`, `frontend`, `docker`, `docs/`

**Status legend**

| Status | Meaning |
|--------|---------|
| **Pass** | Met for L2 intent in current MVP scope |
| **Partial** | Controls exist but incomplete vs L2 wording |
| **Gap** | Missing or contradicted by evidence |
| **N/A** | Out of scope for current product shape |

This review **closes** the checklist item “OWASP ASVS L2 review” in [`docs/security.md`](../security.md) §12 as a **documented gap analysis**. It does **not** replace an external pen-test.

---

## Executive summary

| Bucket | Count (approx.) |
|--------|-----------------|
| Pass | Strong on authn/session cookie SPA, RBAC/IDOR store scope, Eloquent bindings, MFA managers, audit append-only, payment webhook HMAC, supply-chain audits |
| Partial / Gap (priority) | TLS/Secure cookies in prod defaults, API versioning claim, HIBP/CAPTCHA, formal threat model, admin MFA reset |

**Recommended next engineering slices (ordered):**

1. Prod env hardening checklist (`APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, TLS/HSTS)  
2. Admin MFA reset / break-glass (beyond recovery codes)  
3. External pen-test before multi-store production go-live  
4. Optional: HIBP / CAPTCHA after repeated failures  

---

## V1 — Architecture, design, threat modeling

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Secure design docs | **Partial** | `docs/architecture.md`, ADRs, `business-rules.md`, this file. No formal STRIDE/DFD threat model artifact. |
| Security champions / review | **Partial** | Baseline rule `.cursor/rules/06-seguranca-baseline.mdc`; Security feature tests. No mandatory external review gate in CI. |
| Trust boundaries | **Pass** | Delivery → Application → Domain → Infrastructure; Sanctum cookie same-origin; webhook HMAC boundary. |

---

## V2 — Authentication

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Credential storage | **Pass** | `User` password `hashed` cast (bcrypt/argon via Laravel). |
| Login brute-force | **Pass** | `throttle:login` + `LoginUserAction` RateLimiter; `AuthSecurityBaselineTest`. |
| MFA for privileged | **Pass** | Manager TOTP (ADR-0010); operators password-only by design. |
| MFA recovery | **Pass** | Eight one-time recovery codes on enroll confirm (RN-067); admin MFA reset still deferred. |
| Password policy L2 | **Partial** | Create/update: `Password::min(12)` via `Password::defaults()`. Login accepts existing shorter demo passwords (`min:8`). |
| Breach password check | **Gap** | HIBP optional in security.md — not implemented. |
| CAPTCHA after failures | **Gap** | Listed as mitigation; not implemented. |
| Generic auth errors | **Pass** | `ErrorCode` / `ApiErrorResponse` (no user enumeration beyond inactive). |

---

## V3 — Session management

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Session regenerate on login | **Pass** | Operator: regenerate after password; manager: after MFA verify/confirm. |
| Cookie flags | **Partial** | `http_only` default true; `same_site=lax`; `secure` env-driven — `.env*.example` defaults `SESSION_SECURE_COOKIE=false` (OK local; must flip in prod TLS). |
| Session serialization | **Pass** | `session.serialization = json`. |
| Logout invalidates server session | **Pass** | `LogoutUserAction` + `SessionGateTest`. |
| Idle / absolute timeout | **Partial** | Framework session lifetime only; no explicit idle UX warning. |
| MFA pending session | **Pass** | `mfa.pending_user_id` until TOTP; no admin access before verify (`ManagerMfaTest`). |

---

## V4 — Access control

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Deny by default | **Pass** | `auth`, `role:manager`, `store.context`, `shift.open`. |
| IDOR / store scope | **Pass** | `AssertManagerStoreAccess`, `StorePolicy`, `AdminStoreAccessIdorTest`, audit filter 403. |
| Function-level RBAC | **Pass** | Operator vs Manager routes; RN-071. |
| Horizontal privilege | **Pass** | Cross-store admin denied in tests. |
| Vertical privilege | **Pass** | Operator cannot hit `/api/admin/*`. |

---

## V5 — Validation, sanitization, encoding

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Input validation at edge | **Pass** | FormRequests across domains. |
| Output encoding XSS | **Pass** | React default escaping; no `dangerouslySetInnerHTML` found in app src. |
| Parameterized SQL | **Pass** | Eloquent/query builder; no raw user concat found in `app/`. |
| Mass assignment | **Pass** | Explicit `$fillable` / attributes. |

---

## V6 — Stored cryptography

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| PII at rest | **Pass** | ADR-0008 dedicated keys + blind indexes. |
| MFA secret at rest | **Pass** | `mfa_secret` encrypted cast. |
| No PAN storage | **Pass** | Card path 501 / stub; ADR-0009. |
| Key management | **Partial** | Keys in `.env.docker`; backup runbook warns separate storage; no KMS/rotation runbook yet. |

---

## V7 — Error handling and logging

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Generic API errors | **Pass** | Domain `ErrorCode` catalog. |
| Security audit trail | **Pass** | Append-only `audit_logs` + triggers (RN-070). |
| Sensitive data in logs | **Partial** | Correlation id middleware; ensure dumps/logs never print PII plaintext (ops discipline). |
| Debug in production | **Gap (config)** | Examples ship `APP_DEBUG=true` — prod must force `false`. |

---

## V8 — Data protection

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Sensitive data classification | **Partial** | PII encrypted; privacy/retention **drafts** in `docs/legal/` — counsel sign-off pending |
| Backup confidentiality | **Pass** | `docs/ops/backup-restore.md` + gitignore dumps; secrets separate. |
| Client-side storage | **Partial** | `sessionStorage` for UX hint only; boot revalidates `/api/auth/me`. |

---

## V9 — Communication

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| TLS in transit | **Partial** | Local Docker HTTP `:8080`. Production requires TLS terminator + HSTS. |
| HSTS / secure cookies | **Gap (prod)** | Not configured in `docker/nginx.conf` (HTTP-only local). |
| Outbound SSRF | **Partial** | No user-supplied URL fetch in MVP; SOAP stub local — revisit when live WSDL/HTTP clients land. |

---

## V10 — Malicious code

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| No unsafe exec | **Pass** | No `exec`/`shell_exec` on user input in `app/`. |
| Dependency integrity | **Pass** | Lockfiles + `composer audit` / `npm audit` (§15). |

---

## V11 — Business logic

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Sale/shift invariants | **Pass** | RN-001+ enforced in Actions + Feature tests. |
| Refund limits / throttle | **Pass** | Domain rules + `throttle:refunds`. |
| Payment confirmation | **Partial** | Webhook HMAC + reconcile; card issuer verify still 501. |

---

## V12 — Files and resources

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Upload | **N/A** | No arbitrary upload MVP. |
| Path traversal | **N/A** | No user path APIs. |

---

## V13 — API

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Authn on API | **Pass** | Sanctum session + MFA gate for managers. |
| Authorization | **Pass** | Role + store context. |
| Rate limiting | **Partial** | Login, refunds, MFA, webhooks; not universal per-route budget. |
| API versioning | **Gap** | Docs mention `/api/v1`; routes are `/api/...` unversioned. |
| Webhook authenticity | **Pass** | HMAC verifier + tests. |

---

## V14 — Configuration

| ID (theme) | Status | Evidence / gap |
|------------|--------|----------------|
| Security headers | **Pass** | `docker/nginx.conf`: CSP, frame, nosniff, referrer, permissions-policy (local HTTP; HSTS when TLS terminates). |
| Redis hardening | **Pass** | Compose `--requirepass` + `REDIS_PASSWORD`; healthcheck via `REDISCLI_AUTH`; not published on host by default. |
| MySQL least privilege | **Partial** | App user `pdv` OK; root used only for backup scripts; host bind optional via debug compose. |
| Secrets not in VCS | **Pass** | `.env` / `.env.docker` gitignored; Compose uses `${VAR:?…}` (no password literals in YAML). |
| Image integrity | **Pass** | Base images pinned `tag@sha256` (`docker/images.lock`). |

---

## Mapping to automated tests

| Area | Test location |
|------|----------------|
| Login throttle / CSRF posture | `tests/Feature/Security/AuthSecurityBaselineTest.php` |
| Refund throttle + sales IDOR | `tests/Feature/Security/RefundThrottleAndSalesIdorTest.php` |
| Manager MFA | `tests/Feature/Auth/ManagerMfaTest.php` |
| Admin store IDOR | `tests/Feature/Admin/AdminStoreAccessIdorTest.php` |
| Audit scope | `tests/Feature/Audit/AdminAuditLogTest.php` |
| Backup restore smoke | `scripts/restore-mysql-verify.sh` |

---

## Sign-off

| Role | Outcome |
|------|---------|
| Engineering (this review) | ASVS L2 **desk review complete**; residual gaps tracked above |
| External pen-test | **Still recommended** before production (separate checklist line or reopen §12) |

When gaps 1–3 (headers, password min 12, prod env) are closed, update this file’s status column and re-run targeted Feature tests.
