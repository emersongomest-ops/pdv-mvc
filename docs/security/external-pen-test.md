# External penetration test — engagement brief (PDV)

> **Status:** Scope pack ready (engineering). **Execution** by a third party is still required before multi-store production go-live.  
> **Date:** 2026-07-20  
> **Related:** [`asvs-l2-gap-review.md`](./asvs-l2-gap-review.md), [`../security.md`](../security.md) §12, [`../ops/production-hardening.md`](../ops/production-hardening.md)

This document is the handoff for an external tester or firm. It does **not** replace the test itself.

---

## 1. Objectives

1. Validate that ASVS L2 desk controls hold under adversarial exercise (authn, session, IDOR, abuse).
2. Find issues outside automated Feature tests (logic bugs, misconfig, edge TLS/headers, SPA XSS).
3. Produce a remediable findings list with severity, repro, and residual risk.

**Out of engagement (unless separately contracted):** physical security, social engineering of staff, DDoS that could affect shared infra, destructive DB wipes on production data.

---

## 2. Rules of engagement

| Item | Requirement |
|------|-------------|
| **Written auth** | Signed SOW + scope before any testing |
| **Environment** | Prefer **staging** that mirrors prod (TLS, `APP_ENV=production` flags, Redis/MySQL isolated). Production only with explicit change window and backup |
| **Hours** | Agree blackout windows for payment/webhook noise |
| **Rate limits** | Expect `throttle:login`, `throttle:refunds`, Turnstile after N failures — do not treat as DoS unless agreed |
| **Data** | Use seeded/demo PII only; no real customer CPF dumps in reports |
| **Stop-the-line** | Critical RCE / auth bypass → notify contact within 1h; pause further exploitation of that vector |
| **Disclosure** | Private report to owner; no public CVE without coordinated disclosure |

**Contacts (fill before kickoff):**

| Role | Name / channel |
|------|----------------|
| Product / owner | `<PREENCHER>` |
| Engineering on-call | `<PREENCHER>` |
| Staging URL | `<PREENCHER>` |
| Emergency stop | `<PREENCHER>` |

---

## 3. In scope

| Surface | Notes |
|---------|--------|
| SPA (React) + Laravel API | Cookie Sanctum session; CSRF + SameSite |
| `/api/*` and `/api/v1/*` | Same route table (ADR-0011); both allowed |
| Auth | Login, logout, `/api/auth/me`, manager MFA setup/verify/recovery, admin MFA reset |
| RBAC / IDOR | Operator vs manager; store context; admin resources by `store_id` / sale id |
| Cart / sales / complete | Idempotency-Key on create/line/complete |
| Refunds | Manager-only; throttle |
| Inventory adjust, promotions, catalog CRUD | Manager |
| Cash shift open/close/reopen | Ops + admin |
| Payment webhooks | HMAC; CSRF-exempt path under `api` and `api/v1` |
| Audit log query | Manager, store scope |
| Headers / TLS | Prod-like staging: HSTS, Secure cookies, no debug |

### Suggested accounts (seed or provision)

| Role | Purpose |
|------|---------|
| Operator | POS flows, shift, no admin |
| Manager (MFA enrolled) | Admin + MFA challenge |
| Manager (MFA locked) | Reset MFA by second manager (RN-074) |
| Inactive user | Must not establish session |

Share passwords/TOTP secrets **out of band** (not in git). Demo seeds exist for local only — do not reuse demo secrets on internet-facing staging.

---

## 4. Out of scope (default)

- Card acquirer SOAP live calls (ADR-0009 stub / 501 until WSDL)
- Neo4j / analytics backends not exposed publicly
- Third-party Cloudflare Turnstile / HIBP availability (treat as dependency; abuse of those vendors’ APIs is out)
- Source-code review as primary deliverable (optional add-on; code is available if contracted)
- Legal / LGPD counsel sign-off (parallel track — [`docs/legal/`](../legal/))

---

## 5. Known controls (do not mark as “finding” without impact)

Testers should treat these as **expected defenses**. Bypass or weakness still counts as a finding.

| Control | Where |
|---------|--------|
| Login rate limit + Turnstile after N failures | `LoginUserAction`, `TURNSTILE_*` |
| Manager MFA TOTP + recovery + admin reset | ADR-0010, RN-074 |
| HIBP uncompromised passwords on create/update | `PASSWORD_UNCOMPROMISED` |
| Store IDOR checks | `StorePolicy`, `AssertManagerStoreAccess` |
| Idempotency on financial POSTs | RN-073 |
| Webhook HMAC | `HmacPaymentWebhookSignatureVerifier` |
| Prod boot fail-closed | `ProductionSecurityConfigAssertor` |
| Audit append-only on sensitive mutations | RN-070 |
| Automated regression | `tests/Feature/Security/*` |

Desk matrix: [`asvs-l2-gap-review.md`](./asvs-l2-gap-review.md).

---

## 6. Priority test themes (checklist for vendor)

Use as a minimum script; vendor may add OWASP WSTG coverage.

### Authentication & session

- [ ] Credential stuffing / brute force beyond throttle; CAPTCHA bypass or reuse
- [ ] Session fixation; logout invalidation; concurrent sessions
- [ ] MFA skip / brute TOTP / recovery code reuse; MFA reset abuse (self-reset, cross-tenant)
- [ ] CSRF on cookie mutations; missing `X-XSRF-TOKEN` / SameSite edge cases

### Access control

- [ ] Horizontal IDOR: sale, refund, shift, inventory, customer, audit across stores
- [ ] Vertical: operator → admin routes; manager without store assignment
- [ ] Mass assignment / role escalation on user create-update

### Business logic

- [ ] Complete sale without open shift; stock race; double complete with/without Idempotency-Key
- [ ] Refund above remaining; replay Idempotency-Key with altered body
- [ ] Promotion stacking / inactive product

### Injection & XSS

- [ ] SQLi / command injection on all FormRequest fields (expect parameterized Eloquent)
- [ ] Stored/reflected XSS in catalog names, customer fields, admin UI
- [ ] Open redirect on auth flows (if any)

### Payments & webhooks

- [ ] Webhook without/invalid HMAC; replay; provider path confusion (`/api` vs `/api/v1`)
- [ ] Reconcile endpoints authz

### Config & transport (staging≈prod)

- [ ] `APP_DEBUG` leak; stack traces; directory listing
- [ ] Missing HSTS / Secure / HttpOnly; mixed content
- [ ] Exposed Redis/MySQL/Telescope/Pulse

---

## 7. Evidence pack for the tester (attach to SOW)

| Artifact | Path / note |
|----------|-------------|
| Architecture | `docs/architecture.md` |
| Security map | `docs/security.md` |
| ASVS L2 desk review | `docs/security/asvs-l2-gap-review.md` |
| Prod hardening | `docs/ops/production-hardening.md` |
| API versioning | `docs/adr/0011-api-uri-versioning.md` |
| OpenAPI / routes | `php artisan route:list` on staging; SPA uses `/api/...` |
| Error catalog | `ErrorCode` enum (generic client messages) |

---

## 8. Finding report format (required)

| Field | Content |
|-------|---------|
| ID | `PT-YYYY-NNN` |
| Title | Short |
| Severity | Critical / High / Medium / Low / Info (CVSS or vendor scale + mapping) |
| Asset | URL / role / store |
| Steps | Repro numbered |
| Impact | Business + data |
| Evidence | Request/response redacted |
| Remediation | Concrete fix hint |
| Retest | Pass / Fail date |

**Go-live gate (suggested):** zero open **Critical/High** on in-scope assets; Medium accepted only with owner sign-off and ticket.

---

## 9. Closure of `docs/security.md` §12 item

Mark **External penetration test** complete only when **all** are true:

1. [ ] SOW signed; this brief used as scope baseline  
2. [ ] Report delivered for agreed environment  
3. [ ] Critical/High remediated (or accepted in writing)  
4. [ ] Retest evidence attached (ticket or `docs/security/pen-test-reports/` — **do not commit** raw reports with secrets; store privately)

Until then, keep the checklist box **open**. This file alone only closes the **preparation** line.

---

## 10. Revision

| Date | Change |
|------|--------|
| 2026-07-20 | Initial engagement brief after Turnstile + `/api/v1` ASVS slices |
