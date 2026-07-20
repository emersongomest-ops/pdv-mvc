# PDV — Point of Sale

Multi-store **Point of Sale**: operational cashier UI + managerial admin UI, built as a modular monolith with an explicit domain model.

**Stack:** Laravel 13 (PHP 8.3+) API + React 19 SPA · **Patterns:** MVC delivery + Clean Architecture + DDD · **Language:** English (code, domain, errors, docs — ADR-004).

> Specs: [`docs/`](./docs/). This README covers product intent, runtime, and the **engineering rationale** behind the stack — not a tutorial checklist.

---

## 1. What this project is (business)

In-store checkout, catalog/inventory control, and managerial oversight across **multiple physical stores**.

| View | Actor | Goal |
|------|--------|------|
| **Operational** | Operator (cashier) | Fast checkout: CPF lookup, coupons, cart, hold/resume, complete sale |
| **Administrative** | Manager | Catalog, users, stock, promotions, sales/shifts, refunds, audit, analytics |

### Core business rules (summary)

Rules are identified as `RN-XXX` in [`docs/business-rules.md`](./docs/business-rules.md). Highlights:

| Area | Rules (examples) | Intent |
|------|------------------|--------|
| Cash shift | RN-001–004 | No sale without an open shift; one open shift per operator; close consolidates totals; reopen = manager + audit |
| Sale | RN-010–015 | Mutable cart until complete; completed sale immutable; fiscal receipt required; hold/park supported |
| Refund / return | RN-016–019 | Full or partial; stock/payment side effects by policy |
| Payments | RN-050–054 | Charge/refund via `PaymentGatewayInterface` (**SOAP** outbound); confirmation webhooks REST |
| Catalog / inventory | RN-030+ | Products, categories, stock per store |
| Promotions | RN-040+ | Manager-created only; **no automatic discount** for recurrence |
| Multi-store / RBAC | RN-061–065 | Manager store assignment; IDOR denied with `AUTH_STORE_ACCESS_DENIED` |
| Audit | RN-070 | Append-only trail for sensitive mutations |
| Analytics | RN-080–084 | Registrations, recurrence, spend, campaign filters |

**Roles (MVP):** Operator + Manager only (RN-062).

**Deferred:** live acquirer WSDL / card issuer SOAP verify (adapter stub; card path **501** until bound). Customer PII encrypted at rest (ADR-0008). Privacy + retention **drafts** in [`docs/legal/`](./docs/legal/) (counsel review before prod).

---

## 2. Technologies

| Layer | Choice | Version / notes |
|-------|--------|-----------------|
| API | Laravel | 13.x |
| Language | PHP | 8.3+ (Docker image: 8.3-FPM) |
| Auth | Laravel Sanctum | Cookie session (SPA same-origin) |
| SPA | React + TypeScript | React 19, React Router 7 |
| Build | Vite | 8.x |
| Primary DB | MySQL | 8.4 in Docker; SQLite for local/tests |
| Cache / queue | Redis | 7.x in Docker |
| HTTP edge | nginx | Serves SPA + proxies `/api`, `/sanctum` |
| Tests | PHPUnit via `artisan test` | Feature + unit; RN IDs in test names/docs |
| Lint (FE) | oxlint | |

Optional / local-dev only: Horizon, Telescope, Pulse, Reverb (realtime). Docker compose uses `BROADCAST_CONNECTION=log` (no Reverb container).

---

## 3. Architecture

```
┌─────────────────────────────────────────────────────────┐
│  Delivery (MVC)                                         │
│  HTTP Controllers · FormRequests · API Resources · React│
├─────────────────────────────────────────────────────────┤
│  Application                                            │
│  Actions (use cases) · DTOs · orchestration             │
├─────────────────────────────────────────────────────────┤
│  Domain                                                 │
│  VOs · domain exceptions · repository interfaces · RNs  │
├─────────────────────────────────────────────────────────┤
│  Infrastructure                                         │
│  Eloquent repos · SoapPaymentGateway · Redis · queues   │
└─────────────────────────────────────────────────────────┘
```

**Dependency rule:** Domain has **zero** framework imports. Application depends on Domain interfaces. Infrastructure implements them; Laravel binds in `AppServiceProvider`.

### Bounded contexts

| Context | Responsibility |
|---------|----------------|
| Identity & Access | Users, roles, login/session |
| Store | Multi-store + operator store context |
| Catalog | Products, categories, pricing |
| Inventory | Stock per store, adjustments |
| Sales | Cart, complete, hold, fiscal receipt |
| Payments | Payment lines + gateway abstraction |
| Customers | Registry, CPF lookup, lifetime spend |
| Promotions | Manager campaigns / coupons |
| Refunds & Returns | Full/partial reversal |
| Cash Shift | Open/close/reopen per operator×store |
| Audit | Append-only sensitive-action log |
| Analytics | Dashboards & campaign lists |

### Frontend shape

Two **apps** (operational vs administrative) share **features** and **shared** API/session/UI. Smart hooks + thin pages; presentational UI under `features/*/ui`. POS state is split across contexts (sale, catalog, held carts, customer) to limit re-renders.

---

## 4. Folder organization

```
projects/pdv/
├── README.md                 # this file
├── docker-compose.yml        # nginx · app (PHP-FPM) · mysql · redis
├── docker/                   # Dockerfiles, nginx.conf, entrypoint
├── .env.docker.example       # copy → .env.docker (gitignored)
├── docs/
│   ├── business-rules.md     # RN-* source of truth
│   ├── architecture.md       # layers, ADRs index, slice notes
│   ├── errors.md             # stable error codes
│   ├── security.md           # threat model
│   ├── adr/                  # architecture decision records
│   └── domains/              # per-context API/RN notes
├── backend/                  # Laravel API
│   ├── app/
│   │   ├── Domain/           # pure domain per context
│   │   ├── Application/      # Actions / use cases
│   │   ├── Infrastructure/   # repository implementations
│   │   ├── Http/             # Controllers, Requests (by context)
│   │   ├── Models/           # Eloquent models (persistence)
│   │   └── Support/          # shared HTTP resources, money helpers, …
│   ├── database/migrations|seeders|factories
│   ├── routes/api.php
│   ├── tests/Feature|Unit
│   └── stubs/domain/         # php artisan create stubs
└── frontend/                 # React SPA
    └── src/
        ├── apps/operational|administrative
        ├── features/         # auth, pos, admin, catalog, …
        └── shared/           # api client, session, ui
```

Scaffold a new bounded context:

```bash
cd backend
php artisan create Sales
```

Creates Domain / Application / Infrastructure / Http / tests / `docs/domains/{name}.md` stubs.

---

## 5. How to run

### 5.1 Docker (recommended)

Requires [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or Engine + Compose v2).

```bash
cd projects/pdv
cp .env.example .env                 # Compose interpolation (MySQL/Redis passwords)
cp .env.docker.example .env.docker   # Laravel app env_file
docker compose up -d --build
```

Open **http://localhost:8080** — SPA and `/api` on the **same origin** (Sanctum-friendly).

| Service | Host port | Role |
|---------|-----------|------|
| nginx | `8080` | SPA + API reverse proxy |
| mysql / redis | *(none by default)* | Internal Docker network only |

Host access for DB tools (loopback only):

```bash
docker compose -f docker-compose.yml -f docker-compose.debug.yml up -d
# MySQL → 127.0.0.1:3307  ·  Redis → 127.0.0.1:6379
```

On boot, `app` waits for MySQL, runs migrations when `MIGRATE_ON_BOOT=true`, and seeds when `SEED_ON_BOOT=true` (defaults in `.env` / `.env.docker`).

Base images are **pinned by digest** (`docker/images.lock`). Refresh after security advisories:

```bash
bash scripts/docker-pin-digests.sh
# then update Dockerfiles + compose image lines to match the lockfile
```

```bash
docker compose logs -f app
docker compose down          # stop
docker compose down -v       # stop + wipe DB volumes
```

Never put GitHub/MCP tokens in `.env` / `.env.docker` (gitignored). Demo passwords in `.env.example` are **local-only**.

### 5.2 Demo credentials (after seed)

| Role | Email | Password | MFA |
|------|-------|----------|-----|
| Operator | `operator@pos.test` | `password` | — |
| Manager | `manager@pos.test` | `password` | TOTP secret `JBSWY3DPEHPK3PXP` (Authenticator app; demo only) |

| Kind | Value |
|------|--------|
| Products | SKUs `DEMO-BEV-*`, `DEMO-SNK-*`, `DEMO-GRO-*` (stock on store `MAIN`) |
| Customers | CPF `39053344705` (Maria), `52998224725` (João), `15350946056` (Ana) |
| Promotions | `WELCOME10` (all customers), `VIP5OFF` (Maria) |

**Happy path:** login → select store → open cash shift → add demo SKUs → optional CPF → complete sale (stub payment).

### 5.3 Backup / restore drill

See [`docs/ops/backup-restore.md`](./docs/ops/backup-restore.md).

```bash
bash scripts/backup-mysql.sh
bash scripts/restore-mysql-verify.sh   # safe smoke — does not overwrite live DB
```

### 5.4 Local (Laragon / without Docker)

```bash
cd backend
composer install
cp .env.example .env   # configure DB; SQLite OK for quick start
composer serve         # sets PHP_INI_SCAN_DIR=php-conf.d (pdo_sqlite)

cd ../frontend
npm install
npm run dev            # Vite; add ports to SANCTUM_STATEFUL_DOMAINS
```

Tests:

```bash
cd backend
composer test
# or: export PHP_INI_SCAN_DIR=php-conf.d && php artisan test
```

---

## 6. Engineering rationale

POS is a **consistency-heavy, latency-sensitive** domain: a wrong total, double stock decrement, or cross-store data leak is a business incident — not a UI bug. Choices below are constrained by that risk profile, not by “showing patterns.”

### 6.1 System shape

| Decision | Technical justification | Rejected alternative |
|----------|-------------------------|----------------------|
| **Modular monolith** (Laravel API + React SPA, ADR-001) | One consistency boundary for sale → stock → payment → fiscal. Microservices would force distributed transactions (or sagas) across the hottest path before the domain is stable. SPA keeps checkout UX independent of Blade rendering while sharing one auth cookie domain behind nginx. | Premature microservices (network hop + dual-write risk on every complete-sale). |
| **Clean Architecture + DDD contexts** | Rules like “completed sale is immutable” and “no sale without open shift” must be enforceable in one place, independent of HTTP or Eloquent. Bounded contexts (Sales vs Inventory vs Audit) limit blast radius when changing pricing vs refunds. | Fat controllers / “service” bags that mix validation, SQL, and HTTP — hard to test RN-* in isolation. |
| **Actions as use cases** | Checkout and admin mutations are long scripts with a single transactional narrative. One Action = one use case = one place to put `DB::transaction`, authz, and post-commit dispatch. Controllers only adapt HTTP. | God services (`SaleService` with 20 methods) that grow coupling across RNs. |
| **Repository interfaces (DIP)** | Inventory, payments, and audit persistence will change (indexes, read models, acquirer). Application code must not import Eloquent or HTTP SDKs so those swaps stay local to Infrastructure. | Calling Eloquent from Actions — every schema tweak becomes an application rewrite. |

### 6.2 Data & money

| Decision | Technical justification | Rejected alternative |
|----------|-------------------------|----------------------|
| **MySQL for OLTP** (ADR-002) | Strong row-level locking and mature FK support for `store_inventories`, sales lines, and shifts. POS writes are short transactions, not graph analytics. | Starting on a document store (weak multi-row integrity) or splitting OLTP/analytics DBs before load justifies it. |
| **Redis for cache / queue / rate limits** | Checkout must not wait on analytics jobs; login needs shared rate-limit state across FPM workers. Redis is the smallest reliable backing store for that. | Database queues only — lock contention under concurrent cashiers; or in-process cache — wrong under multiple PHP-FPM workers. |
| **Money as integer cents** (ADR-0005) | IEEE floats and mixed decimal rounding break “payments must equal total.” Integer cents make sum/discount/refund algebra exact; HTTP still speaks `"13.00"` via `Money` at the boundary so clients stay human-readable. | `float` / ad-hoc `round()` in controllers — classic cent-off bugs on promotions and split payments. |
| **Keyset (cursor) pagination** | Admin sales and POS catalog grow monotonically; `OFFSET` gets slower and can skip/duplicate rows under concurrent inserts. Cursor on `(name,id)` / `(completed_at,id)` is stable for “Load more.” | Page-number offset for large history tables. |

### 6.3 Consistency, concurrency, audit

| Decision | Technical justification | Rejected alternative |
|----------|-------------------------|----------------------|
| **Critical path synchronous in `DB::transaction`** (ADR-0006) | Stock decrement, payment lines, sale status, and fiscal receipt must commit together or not at all (RN-011/015/050). Queuing the mutation itself risks “sale completed, stock not updated” after a worker crash. | “Everything async” for throughput — incorrect for money/stock. |
| **Post-commit jobs for side effects** | Lifetime spend and analytics can retry; they must **not** roll back a paid sale. `Bus::batch` after commit isolates failure domains. | Updating analytics inside the same transaction as payment — one reporting bug aborts checkout. |
| **`Concurrency::run` for independent reads** | Admin dashboard KPIs do not share predicates; parallelizing cuts p95 without sharing locks. | Sequential COUNT queries that inflate TTFB with no consistency benefit. |
| **Append-only audit + DB triggers** (RN-070) | Sensitive mutations need forensic integrity. App-level “please don’t update” is bypassable via tinker/SQL. Triggers reject UPDATE/DELETE even if Eloquent is circumvented; audit rows are written in the **same** transaction as the business change so you never get “price changed with no log.” | Soft logging in controllers after the fact — can be skipped on exceptions / partial commits. |

### 6.4 AuthZ, multi-store, session

| Decision | Technical justification | Rejected alternative |
|----------|-------------------------|----------------------|
| **Multi-store in MVP** (ADR-005) | Inventory, shifts, and sales are physically store-scoped. Modeling a single store first encodes the wrong aggregate and makes IDOR fixes a retrofit. `store_user` + `AssertManagerStoreAccess` makes foreign `store_id` a hard 403 (`AUTH_STORE_ACCESS_DENIED`). | “Add stores later” — usually means scattered `where store_id` patches and missed filters. |
| **Sanctum SPA cookie (same-origin)** | Cashiers use a browser on a LAN/WAN; HttpOnly session cookies + CSRF beat storing bearer tokens in JS (XSS → full account takeover). Same-origin nginx (`/` + `/api`) removes brittle CORS for the primary deploy. SPA **revalidates** via `GET /api/auth/me` so a revoked server session cannot be faked from `sessionStorage`. | JWT in `localStorage` for a first-party POS — worse XSS impact, harder revocation. |
| **Role middleware + policies at the edge** | Operator must never hit admin mutators even with a crafted URL. Deny-by-default on `/api/admin/*` (`role:manager`) matches RN-062. | UI-only hiding of buttons — not an access control. |

### 6.5 Payments (stub adapter)

| Decision | Technical justification | Rejected alternative |
|----------|-------------------------|----------------------|
| **`PaymentGatewayInterface` + SOAP outbound + REST webhooks** (ADR-003/0009) | Acquirers that only speak SOAP must not dictate the POS API shape. Charge/refund use `SoapPaymentGateway` (envelope builder + stub/live mode). Settlement callbacks stay REST+HMAC so cashiers keep a JSON same-origin SPA. Card PAN never stored; PII at rest is encrypted separately (ADR-0008). | Embedding a vendor REST SDK in `CompleteSaleAction`, or forcing the whole monolith onto SOAP. |
| **Not wiring a live acquirer in this MVP** | Until charge/refund webhooks and reconciliation exist, a “real” SDK call would fake confidence: money would move without matching domain recovery paths (partial failure, timeout, double charge). Prefer a complete stubbed pipeline over a partial live integration. | Copy-pasting a provider quickstart into checkout to look “production-ready.” |

### 6.6 Frontend & operability

| Decision | Technical justification | Rejected alternative |
|----------|-------------------------|----------------------|
| **Feature-sliced React (ops vs admin apps)** | Cashier UX (hotkeys, cart latency) and admin UX (tables, filters) have different performance budgets. Split apps/routes with shared `features/*` avoid one mega-bundle and let POS state be partitioned (sale ≠ catalog ≠ held carts) to cut re-renders on every keystroke. | Single undifferentiated page tree — admin weight on the checkout critical path. |
| **English ubiquitous language** (ADR-004) | Error codes (`SHIFT_NOT_OPEN`), logs, and RN docs must match code identifiers; bilingual drift causes wrong fixes in incidents. | Portuguese-only domain terms in code + English HTTP — double glossary. |
| **RN-traced tests** | Regression on checkout is expensive. Mapping tests to `RN-XXX` makes gaps visible and keeps BDD scenarios honest against `business-rules.md`. | Snapshot-only or UI tests without rule IDs — failures don’t say which invariant broke. |
| **Docker Compose (nginx + FPM + MySQL + Redis)** | Matches the production-shaped topology (edge, app, OLTP, cache/queue). Entrypoint migrate/seed removes “works on my machine” for reviewers and new contributors. | Documenting only Laragon paths — unreproducible outside one Windows setup. |

### 6.7 What this is *not*

- Not a framework tutorial rearrange: contexts, money model, audit triggers, and store authz exist because **POS failure modes** demand them.
- Not microservices-for-resume: distribution cost was judged higher than modularity-inside-one-deployable.
- Not “finished payments product”: the deferred piece is the **acquirer adapter + reconciliation**, not the sale state machine.

Full ADRs: [`docs/architecture.md`](./docs/architecture.md), [`docs/adr/`](./docs/adr/), [`docs/security.md`](./docs/security.md).

---

## 7. Documentation map

| File | Purpose |
|------|---------|
| [docs/business-rules.md](./docs/business-rules.md) | Full RN-* catalog + BDD sketches |
| [docs/architecture.md](./docs/architecture.md) | Layers, contexts, money, processing, slice notes |
| [docs/errors.md](./docs/errors.md) | Stable API error codes |
| [docs/security.md](./docs/security.md) | Threat model + mitigations |
| [docs/domains/](./docs/domains/) | Per-context API surface |
| [docs/adr/](./docs/adr/) | Decision records |

---

## 8. Validation status

| Criterion | Status |
|-----------|--------|
| Required RNs covered by domain docs + tests where endpoints exist | ✅ |
| Stub checkout path: login → store → shift → cart → complete | ✅ |
| Admin: catalog, sales, shifts, users, customers, promotions, inventory, refunds, audit, analytics | ✅ |
| SPA session gate via `/api/auth/me` + logout | ✅ |
| Docker Compose demo stack | ✅ |
| Live payment acquirer | ☐ deferred — interface + stub in place (ADR-003); see §6.5 |
| Formal penetration test | ☐ hardening checklist in `security.md` §12 |

---

## 9. Seeders (idempotent)

`DatabaseSeeder` calls, in order:

1. `DemoStoreSeeder` — store `MAIN`
2. `OperatorUserSeeder` / `ManagerUserSeeder`
3. `DemoCatalogSeeder` / `DemoInventorySeeder`
4. `DemoCustomerSeeder` / `DemoPromotionSeeder`

```bash
php artisan db:seed
# or: php artisan db:seed --class=DemoCatalogSeeder
```
