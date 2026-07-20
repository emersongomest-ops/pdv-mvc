# Security — POS (PDV)

> **Last updated:** 2026-07-20  
> **Scope:** Web POS (Laravel API + React), multi-store, payment stubs, customer PII (CPF, email, address, birth_date)
Defense-in-depth mapped to threat categories. Not exhaustive — review per release.

---

## 1. Security principles

| Principle | Implementation |
|-----------|----------------|
| Least privilege | Operator vs Manager RBAC; store-scoped access (RN-064, RN-071) |
| Fail secure | Deny by default; validate at domain + HTTP layer |
| Defense in depth | WAF/CDN + app controls + DB permissions + audit logs |
| No secrets in code | `.env` only; never commit keys |
| PII minimization | Expose only required fields per role; mask CPF in operational UI where possible |

---

## 2. Network & infrastructure

| Threat | Mitigation |
|--------|------------|
| **DoS / DDoS** | CDN/WAF (Cloudflare, etc.); rate limiting (Redis); Laravel throttle middleware; horizontal scale behind load balancer |
| **MitM** | TLS 1.2+ everywhere; HSTS; secure cookies (`Secure`, `HttpOnly`, `SameSite`) |
| **Packet sniffing** | TLS only; no sensitive data in query strings |
| **Session hijacking** | Short-lived tokens; rotation on login; bind session to IP/UA optional; Redis session store |
| **DNS poisoning** | DNSSEC where possible; certificate pinning for mobile apps (future) |
| **Port scanning** | Firewall: expose only 443; SSH bastion; fail2ban |

---

## 3. Web application

| Threat | Mitigation |
|--------|------------|
| **SQL injection** | Eloquent/parameterized queries only; forbid raw concatenation; PHPStan/Psalm static analysis |
| **XSS** | React auto-escape; CSP headers; sanitize rich text if any; `Content-Security-Policy` |
| **CSRF** | Laravel Sanctum/CSRF for cookie auth; SameSite cookies; API tokens for SPA |
| **Command injection** | No `exec`/`shell_exec` on user input; use Laravel APIs |
| **XXE** | Disable external entities in XML parsers; avoid XML uploads |
| **SSRF** | Allowlist outbound URLs; block internal IP ranges in HTTP client wrappers |
| **Path traversal** | `Storage::` disk abstraction; validate filenames; no user-controlled paths |
| **Insecure deserialization** | Avoid `unserialize` on untrusted data; JSON only for API |
| **Broken access control / IDOR** | Policy classes per resource; always scope by `store_id` + role; UUIDs for public ids |
| **Malicious file upload** | No arbitrary uploads in MVP; if added: MIME verify, store outside webroot, virus scan |

---

## 4. Malware & endpoints

| Threat | Mitigation |
|--------|------------|
| **Trojan / ransomware on POS** | Hardened OS; app not run as admin; backups; EDR on store PCs |
| **USB attacks** | Physical security policy; disable autorun on terminals |
| **Botnet / cryptojacking** | Not applicable server-side; monitor container/VM CPU |

---

## 5. Social engineering

| Threat | Mitigation |
|--------|------------|
| **Phishing** | MFA for Manager accounts; staff training; no password in email |
| **BEC** | Verify payment/bank changes out-of-band |
| **Tailgating** | Physical access policy (operational, not code) |

---

## 6. Credentials & authentication

| Threat | Mitigation |
|--------|------------|
| **Brute force / dictionary** | Rate limit login; lockout after N failures; CAPTCHA on admin |
| **Credential stuffing** | Breach password detection (Have I Been Pwned API optional); MFA |
| **Password spraying** | Same as brute force; unique emails per store admin |
| **Weak passwords** | Policy: min length 12, complexity; bcrypt/argon2 via Laravel |
| **Pass-the-hash** | HttpOnly cookies; short JWT TTL; refresh rotation |

**Auth stack:** Laravel Sanctum (cookie session SPA); **MFA for Manager (TOTP)** — ADR-0010.

---

## 7. Cryptography

| Threat | Mitigation |
|--------|------------|
| **Downgrade** | TLS min version; disable weak ciphers |
| **Weak hashing** | `bcrypt`/`argon2id` for passwords; encrypt PII at rest if required by policy |
| **Side-channel** | Use framework crypto; no custom ciphers |

---

## 8. Supply chain & advanced

| Threat | Mitigation |
|--------|------------|
| **Supply chain** | `composer audit`, `npm audit`, Dependabot; lock files committed; verify package integrity |
| **Zero-day** | Patch cadence; WAF rules; minimal attack surface |
| **Privilege escalation** | OS + DB least privilege; no `root` DB user for app |
| **APT / LotL** | Audit logs (RN-070); SIEM export; anomaly alerts on refund volume |

---

## 9. AI/ML (if AI features added later)

| Threat | Mitigation |
|--------|------------|
| **Prompt injection** | No LLM with direct DB write; human approval for actions |
| **Data poisoning** | Training data outside prod path |

---

## 10. Application-specific controls

| Area | Control |
|------|---------|
| **Multi-store IDOR** | Operational: middleware `store.context` + `StorePolicy` (RN-065). Admin store-scoped: `AssertManagerStoreAccess` / `store_user` on sales, shifts, inventory, refunds, dashboard KPIs, audit log filters (RN-064); catalog remains global |
| **Refunds** | Manager-only or threshold; full audit (RN-019a / RN-070) |
| **PII (CPF, email, phone, address, birth_date)** | Encrypt at rest (AES-256-CBC, dedicated key); blind indexes for CPF/email equality; operational CPF masked; see ADR-0008 |
| **Payment stub / SOAP acquirer** | No PAN stored; outbound acquirer protocol is SOAP; app API + payment webhooks remain REST |
| **Audit** | Append-only `audit_logs` (RN-070): Eloquent + DB triggers block UPDATE/DELETE; audit failure aborts mutation; managers see assigned stores + global rows; unassigned `store_id` filter → 403 `AUTH_STORE_ACCESS_DENIED` |
| **API** | Unversioned `/api/*` today (ASVS gap: `/api/v1` when breaking); Form Requests at boundary |
| **Headers** | Target CSP / frame / nosniff / referrer — **not yet on nginx** (see ASVS L2 review) |

---

## 11. Redis & databases

| Store | Hardening |
|-------|-----------|
| **MySQL** | Dedicated user, least grants; no public port; encrypted backups |
| **Redis** | Password + bind localhost/VPC; no `FLUSHALL` in prod |
| **PostgreSQL** | Same as MySQL if enabled |
| **Neo4j** | Auth enabled; not exposed publicly |

---

## 12. Checklist before launch

- [x] LGPD technical controls: customer PII encrypted at rest + blind indexes (ADR-0008)  
- [x] OWASP ASVS L2 **desk review** documented ([`docs/security/asvs-l2-gap-review.md`](./security/asvs-l2-gap-review.md)); external pen-test still recommended before prod  
- [x] `composer audit` / `npm audit` clean or accepted risks documented (see §15)  
- [x] MFA on Manager accounts (TOTP; ADR-0010)  
- [x] Rate limits on auth + refund endpoints (`throttle:login`, `throttle:refunds`)  
- [x] Backup restore tested (see [`docs/ops/backup-restore.md`](./ops/backup-restore.md); smoke: `scripts/restore-mysql-verify.sh`)  
- [x] LGPD privacy policy + data retention **drafts** ([`docs/legal/`](./legal/)) — counsel approval + DPO fields still required before prod  
- [ ] External penetration test (post ASVS gaps 1–3 or in parallel)  
- [ ] Legal sign-off on privacy/retention + fill controller/DPO placeholders  
- [ ] Card issuer SOAP verify (leave 501 until WSDL) — ADR-0009  

---

## 13. References

- OWASP Top 10  
- OWASP ASVS  
- Laravel security docs  
- RN-070, RN-071 (`business-rules.md`)
- ASVS L2 gap review: `docs/security/asvs-l2-gap-review.md`
- Project baseline: `.cursor/rules/06-seguranca-baseline.mdc` (always apply; violation = -3 scoring)

---

## 14. Baseline compliance — Auth & Store slices (2026-07-15)

| Baseline item | Status | Implementation |
|---------------|--------|----------------|
| SQLi — parameterized queries | ✅ | Eloquent only; no raw interpolated SQL in app code |
| Input validation at boundary | ✅ | `LoginRequest`, `SelectStoreContextRequest` |
| Password hashing | ✅ | Laravel `hashed` cast (bcrypt) |
| Login rate limiting | ✅ | `RateLimiter` in `LoginUserAction` + `throttle:login` on route |
| Session regenerate on login | ✅ | `LoginController` |
| Cookie HttpOnly / SameSite | ✅ | `config/session.php`; Sanctum stateful middleware |
| CSRF on mutations | ✅ | Sanctum `EnsureFrontendRequestsAreStateful` for SPA cookie auth |
| SPA session gate (no ghost UI) | ✅ | `GET /api/auth/me` on boot; `POST /api/auth/logout` invalidates server session; `RequireAuth` + clear local on 401/`AUTH_ACCOUNT_INACTIVE` |
| Authz / IDOR on store_id | ✅ | Operational: `StorePolicy::access` + `EnsureStoreContext`. Admin: `AssertManagerStoreAccess` on sales/shifts/inventory/refunds/dashboard; foreign store → `AUTH_STORE_ACCESS_DENIED` |
| Deny by default | ✅ | Routes behind `auth`, `role`, `store.context` |
| No hardcoded secrets | ✅ | `.env` only |
| Generic errors to client | ✅ | `ApiErrorResponse` + `ErrorCode` catalog |
| Session serialization | ✅ | `session.serialization = json` (not PHP serialize) |
| Supply chain | ✅ | `composer.lock` committed; run `composer audit` after dep changes |
| Refund rate limiting | ✅ | `throttle:refunds` (10/min per user+IP) on `POST /api/admin/sales/{id}/refunds` |

**Auto-assessment:** `[security: SQLi ok via Eloquent bindings, authz via StorePolicy + middleware re-check, FormRequest validation, throttle login+refunds, session regenerate, CSRF via Sanctum stateful API, no hardcoded secrets]`

---

## 15. Supply-chain audit log (2026-07-20)

| Tool | Scope | Result |
|------|-------|--------|
| `composer audit` | `projects/pdv/backend` | No security vulnerability advisories found |
| `npm audit` | `projects/pdv/frontend` | 0 vulnerabilities (info/low/moderate/high/critical) |

Accepted residual risks: none from these scans. Re-run after dependency changes.

Automated coverage added under `tests/Feature/Security/`:
- login throttle → `AUTH_TOO_MANY_ATTEMPTS`
- CSRF except allowlist for payment webhooks + enforced forgery on logout (PHPUnit normally bypasses CSRF)
- admin sales IDOR / operator denied
- refund endpoint 429 after limit
- webhook HMAC missing/invalid (existing + extended)

---

## 16. ASVS L2 desk review (2026-07-20)

Full matrix: [`docs/security/asvs-l2-gap-review.md`](./security/asvs-l2-gap-review.md).

**Top gaps to close next:** nginx security headers + CSP; password `min:12`; prod `APP_DEBUG=false` / `SESSION_SECURE_COOKIE=true` / Redis password; MFA recovery; external pen-test.
