# Architecture — POS (PDV)

> **Last updated:** 2026-07-15  
> **Language:** English (code, domain, errors, docs)

## 1. Overview

Monolithic **Laravel 13** (PHP 8.5) API + **React** SPA. Two UI modes: **Operational** (cashier) and **Administrative** (manager). **Multi-store** from day one.

Patterns: **MVC** (delivery), **Clean Architecture** (dependencies inward), **DDD** (bounded contexts), **SOLID** with **SRP** as default.

## 2. Layering

```
┌─────────────────────────────────────────────────────────┐
│  Delivery (MVC)                                         │
│  HTTP Controllers · React routes · API Resources        │
├─────────────────────────────────────────────────────────┤
│  Application                                            │
│  Use cases / Actions · DTOs · Orchestration             │
├─────────────────────────────────────────────────────────┤
│  Domain                                                 │
│  Entities · Value Objects · Domain Services · RN rules  │
├─────────────────────────────────────────────────────────┤
│  Infrastructure                                         │
│  Eloquent repos · Payment stubs · Redis · Neo4j client  │
└─────────────────────────────────────────────────────────┘
```

**Dependency rule:** Domain has zero framework imports. Application depends on Domain interfaces. Infrastructure implements interfaces.

## 3. Bounded contexts (initial)

| Context | Responsibility |
|---------|----------------|
| **Identity & Access** | Users, roles (Operator, Manager), store assignment |
| **Store** | Multi-store config, operator store context |
| **Catalog** | Products, categories, pricing |
| **Inventory** | Stock per store, adjustments |
| **Sales** | Cart, sale completion, fiscal receipt |
| **Payments** | Payment lines, gateway abstraction (stub) |
| **Customers** | Registry, history per store, lifetime spend |
| **Promotions** | Manager-defined campaigns, coupons |
| **Refunds & Returns** | Full/partial reversal |
| **Analytics** | Dashboards: new customers, recurrence, spend |
| **Cash Shift** | Open/close shift per operator per store |

## 4. SOLID (project defaults)

| Principle | Application |
|-----------|-------------|
| **SRP** | One class = one reason to change. Separate: `CompleteSale`, `IssueFiscalReceipt`, `ApplyPromotion`, etc. |
| **OCP** | Payment providers, promotion evaluators: extend via new implementations, not edits to core |
| **LSP** | Repository implementations interchangeable (stub vs live gateway) |
| **ISP** | Small interfaces: `PaymentGatewayInterface`, `FiscalReceiptGeneratorInterface` |
| **DIP** | Application depends on interfaces; Laravel binds in `AppServiceProvider` |

## 5. Repository pattern (required)

### Payments (RN-054)

```php
interface PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult;
    public function refund(RefundRequest $request): RefundResult;
}
```

- MVP: `StubPaymentGateway` — simulates success, no external call
- Launch: `MercadoPagoGateway`, `StripeGateway`, etc. — swap via config/DI
- **Webhook confirmation (RN-054):** `POST /api/webhooks/payments/{provider}` — HMAC-verified, idempotent `payment_webhook_events`, transitions `payment_lines.status` (`pending` → `confirmed`/`failed`). See `docs/domains/payments.md`.

### Other repositories (examples)

| Interface | Responsibility |
|-----------|----------------|
| `SaleRepositoryInterface` | Persist/load sales |
| `CustomerRepositoryInterface` | Customer registry + history aggregates |
| `InventoryRepositoryInterface` | Stock per store |
| `PromotionRepositoryInterface` | Active promotions per customer/store |
| `FiscalReceiptRepositoryInterface` | Receipt storage |

## 6. Data stores

| Store | Role |
|-------|------|
| **MySQL** | Primary transactional DB (sales, catalog, customers, shifts) |
| **Redis** | Cache, sessions, rate limiting, queue backing |
| **PostgreSQL** | Optional: analytics/reporting read models if MySQL OLTP load grows |
| **Neo4j** | Optional: relationship analytics (customer↔store↔product graphs, recurrence paths) |

Decision: start MySQL + Redis; add PostgreSQL/Neo4j when analytics queries justify (ADR pending).

**Local (Laragon / Windows):** `.env` may use `DB_CONNECTION=sqlite`. Enable `extension=pdo_sqlite` (and `sqlite3`) in the active `php.ini` — without it, `artisan serve` returns HTTP 500 (`could not find driver`) on login/CSRF. Prefer `composer serve` / `composer test` (sets `PHP_INI_SCAN_DIR=php-conf.d`). Use `SESSION_DRIVER=file` and `CACHE_STORE=file`; keep Telescope/Pulse disabled unless profiling (`TELESCOPE_ENABLED=false`, `PULSE_ENABLED=false`) to avoid SQLite lock contention. After migrate, run `php artisan db:seed` for demo users `operator@pos.test` / `manager@pos.test` (password `password`; manager TOTP secret `JBSWY3DPEHPK3PXP` — ADR-0010). Include Vite ports in `SANCTUM_STATEFUL_DOMAINS` (5173–5175). SPA must not treat `sessionStorage` alone as auth: boot validates via `GET /api/auth/me` before internal routes (`RequireAuth`).

## 7. Frontend structure (React)

```
src/
├── apps/
│   ├── operational/pages/     # Smart route pages (cashier)
│   └── administrative/
│       ├── pages/             # Smart route pages (manager)
│       └── guards/            # Route authz (RequireManager)
├── features/                  # Bounded contexts — reuse
│   ├── auth/                  # hooks + presentational UI
│   ├── admin/
│   ├── catalog/
│   └── pos/                   # context + hooks SRP + containers + components
├── shared/
│   ├── api/                   # HTTP client + types
│   ├── session/               # Auth/store/shift context
│   ├── lib/                   # Pure helpers (money format)
│   └── ui/                    # Dumb primitives (MoneyText, Field)
```

**Smart vs dumb (Context7 / React hooks guidance):**
- **Dumb (presentational):** props only — `features/*/ui/*`, `shared/ui/*`
- **Smart:** data + effects — `features/*/hooks/*` and thin `apps/*/pages/*` that wire hooks → UI
- **POS state:** contexts separados para sale, catalog, held carts, customer e activity; consumidores assinam apenas o estado necessário para evitar re-render cruzado.
- **Money display:** API returns decimal strings; `formatMoney` / `MoneyText` only format — cents stay on the backend (ADR-0005)

## 8. Testing strategy

| Layer | Style |
|-------|-------|
| Domain | **TDD** — unit tests per `RN-*` rule |
| Application | TDD on use cases with mocked repos |
| API | Feature tests |
| Flows | **BDD** (Pest + scenarios from `business-rules.md` §5) |

## 9. Domain scaffolding (`php artisan create`)

From `projects/pdv/backend`:

```bash
php artisan create Sales
php artisan create Promotions
php artisan create CashShift
```

Creates per bounded context:

```
app/Domain/{Domain}/Entities|ValueObjects|Events|Exceptions|Repositories|Services
app/Application/{Domain}/Actions|DTOs|Queries
app/Infrastructure/{Domain}/Persistence/Models|Repositories
app/Http/{Domain}/Controllers|Requests|Resources
tests/Unit/Domain/{Domain}
tests/Feature/{Domain}
tests/BDD/Features/{snake_case}
database/factories/{Domain}
docs/domains/{snake_case}.md   # under projects/pdv/docs/
```

Stubs: `stubs/domain/`. Command: `app/Console/Commands/CreateDomainCommand.php`. Scaffolder (SRP): `app/Support/Domain/DomainScaffolder.php`.

## 10. Documentation layout

All under `projects/pdv/docs/`:

| File | Purpose |
|------|---------|
| `business-rules.md` | RN-* source of truth |
| `architecture.md` | This file |
| `errors.md` | Central error catalog |
| `security.md` | Threat model + mitigations |
| `adr/` | Architecture decision records |

## 11. ADRs (initial)

| # | Decision | Date |
|---|----------|------|
| ADR-001 | Monolith Laravel + React SPA | 2026-07-15 |
| ADR-002 | MySQL primary; Redis cache/queue | 2026-07-15 |
| ADR-003 | Payment stub + repository for gateway swap | 2026-07-15 |
| ADR-004 | English as system language | 2026-07-15 |
| ADR-005 | Multi-store from MVP | 2026-07-15 |
| ADR-0005 | Money as integer cents; API decimal strings | 2026-07-16 |
| ADR-0006 | Hybrid parallel / sequential processing (Concurrency + Bus) | 2026-07-16 |
| ADR-0007 | Observability (Telescope/Pulse/Pail) + Redis messaging / Horizon | 2026-07-16 |
| ADR-0008 | Customer PII encryption at rest (LGPD) | 2026-07-20 |
| ADR-0009 | SOAP outbound payment acquirer; REST for app + webhooks | 2026-07-20 |

## 12. Money

- **Storage / domain:** integer **cents** (`unsignedBigInteger` / PHP `int`).
- **HTTP API:** decimal strings with two places (e.g. `"13.00"`).
- **Boundary:** `App\Domain\Shared\Money::fromDecimalInput` / `toDecimalString`.
- **Promotions percent:** same ×100 scale (`10.00%` → `1000`); see [adr/0005-money-as-integer-cents.md](adr/0005-money-as-integer-cents.md).

## 13. Processing model (parallel / sequential / async)

See [adr/0006-hybrid-parallel-sequential-processing.md](adr/0006-hybrid-parallel-sequential-processing.md).

| Layer | Tool | Use when |
|-------|------|----------|
| Action critical path | `DB::transaction` (sequential) | Payments, stock, sale/refund mutations |
| Same-request parallel reads | `Concurrency::run` | Independent metrics / aggregates |
| Post-response side effects | `Concurrency::defer` | Fire-and-forget reporting |
| Ordered async | `BusOrchestrator::chain` | A → B → C on the queue |
| Parallel async | `BusOrchestrator::batch` | Independent jobs + batch callbacks |
| Hybrid async | `BusOrchestrator::hybrid` | Chain of jobs and/or batches |

- **Code:** `BusOrchestrator`, `AbstractQueuedJob`, `DispatchSaleSideEffects` + `App\Jobs\Sales\*`.
- **Config:** `config/concurrency.php` (`CONCURRENCY_DRIVER`; tests use `sync`); queue `after_commit` default true for `database`.
- **Rule:** Domain never imports `Bus` / `Concurrency`; Actions orchestrate after the transactional boundary.
- **Complete sale:** critical path sync; post-commit **batch** = analytics ∥ customer lifetime (ADR-0006).
- **Admin dashboard:** `GetAdminDashboardMetricsAction` + `Concurrency::run` for independent KPI counts. Catalog product counts stay global; `sales_completed`, `open_shifts`, and `customers_total` (via `customer_store_stats`) are limited to the manager's `store_user` assignments (RN-064).
- **Admin sales list (RN-061 / RN-064):** `ListAdminSalesAction` + `SalesRepository::listForAdmin(..., $allowedStoreIds)` — filters: period (`from`/`to` on `completed_at`), store, operator, customer, payment method; default status `completed`; always `whereIn(store_id, assigned)`; explicit unassigned `store_id` → `AUTH_STORE_ACCESS_DENIED`; `GET /api/admin/sales`.
- **Admin sale detail + refunds UI (RN-016–019a / RN-064):** `ShowAdminSaleAction` → `GET /api/admin/sales/{id}`; refund list/create validate `sale.store_id` against assignment; frontend `/admin/refunds?sale_id=`.
- **Shift closing report (RN-003 / RN-063 / RN-064):** `CashShiftRepository::buildClosingReport` — sales totals, payment-method totals, expected cash (`opening + cash payments`), variance when closing count provided; returned on `POST /operational/shifts/close` and `GET /admin/shifts/{id}/report`; list via `GET /admin/shifts?store_id=`; admin list/report require store assignment.
- **Admin inventory (RN-064):** list/adjust require `store_user` assignment before read/write.
- **Admin multi-store gate (RN-064):** `AssertManagerStoreAccess` + `StoreRepository::assignedStoreIds` / `userCanAccessStore`; foreign store → 403 `AUTH_STORE_ACCESS_DENIED`. Catalog admin remains global (no store filter). Notifications list hides entries whose `store_id` is no longer assigned.
- **Admin user management (RN-062):** `UsersRepository` + `List/Create/Show/UpdateUserAction` — roles `manager`/`operator`, store sync via `store_user`, soft deactivation via `is_active` (no physical delete; preserves sales/shifts/audit). Create requires password (≥8, confirmed) and ≥1 store; update allows optional password. Manager cannot deactivate or demote self (`AUTH_CANNOT_MODIFY_SELF`). Password never returned (model `hashed` + `UserResource`). Routes under `role:manager`: `GET|POST /api/admin/users`, `GET|PATCH /api/admin/users/{userId}`. Frontend `/admin/users`.
- **Immutable audit trail (RN-070):** bounded context `Audit` — append-only `audit_logs` (model blocks UPDATE/DELETE; DB triggers on MySQL/SQLite; no mutation routes). `AuditLogRepository::append` inside the same `DB::transaction` as the sensitive mutation. Events: `catalog.product.price_changed` (only when `base_price` changes), `inventory.stock_adjusted`, `refund.created` / `return.created`, `promotion.created` / `promotion.updated`, `cash_shift.reopened` (RN-004). Not audited: initial product price, apply/remove promotion on sale. Admin query: `ListAdminAuditLogsAction` → `GET /api/admin/audit-logs` (filters + cursor; RN-064: assigned stores OR `store_id IS NULL`; foreign store → `AUTH_STORE_ACCESS_DENIED`). Frontend `/admin/audit-log`.
- **Financial mutation idempotency (RN-073):** `IdempotencyGuard` + `idempotency_records` on `POST .../sales/{id}/complete` and `POST /api/admin/sales/{id}/refunds`. Required `Idempotency-Key`; claim via unique `(scope, key)`; replay same hash; conflict on hash mismatch / in-flight. Correlates with `X-Request-Id` on the row. Retention 7d via `idempotency:purge` (daily schedule; `IDEMPOTENCY_RETENTION_DAYS`). Payment webhooks remain idempotent via `payment_webhook_events` (RN-054).
- **Shift reopen (RN-004):** `ReopenCashShiftAction` → `POST /api/admin/shifts/{id}/reopen` (manager + store assignment). Clears `closed_at` / closing cash; blocked if shift already open or operator already has another open shift (`SHIFT_ALREADY_OPEN` 409).
- **Analytics & campaigns (RN-080–084):** `GetAdminAnalyticsAction` / `ListCampaignCustomersAction` → `GET /api/admin/analytics`, `GET /api/admin/campaigns/customers`. Registrations by day; recurrence from `customer_store_stats` on assigned stores; top spend with per-store breakdown; birthday month + address region filters. Frontend `/admin/analytics`.
- **Operational catalog stock (perf):** `ListOperationalProductsAction` loads inventory via `InventoryRepository::mapForStoreProducts` (one `WHERE IN`) — O(P) time/space, O(1) inventory round-trips. Search pushed to SQL (`LOWER(name|sku) LIKE`). POS search is debounced to the API.
- **POS first-item path (perf):** `POST /api/operational/sales` may include `product_id` + `quantity`; `CreateSaleAction` creates the cart and first line in one DB transaction (single RTT). Subsequent adds still use `POST .../lines`.
- **Operational catalog pagination (perf):** `GET /api/operational/catalog/products` uses keyset cursor (`name`,`id`) with `per_page` + `meta.next_cursor`; inventory batch applies to the page only. POS UI: Load more.

## 14. Observability and messaging

See [adr/0007-observability-and-messaging.md](adr/0007-observability-and-messaging.md).

| Concern | Tool | Notes |
|---------|------|--------|
| Live logs (dev) | **Pail** | `composer dev` |
| Deep debug (local) | **Telescope** | `/telescope`; registered only when `APP_ENV=local` |
| App metrics | **Pulse** | `/pulse`; `viewPulse` → active Manager |
| Correlation | **`AssignCorrelationId`** | `X-Request-Id` header + `request_id` log context |
| Structured logs | **`structured` channel** | Optional via `LOG_STACK=single,structured` |
| Async transport | **Redis queue** | `QUEUE_CONNECTION=redis` (`.env.example`) |
| Worker dashboard | **Horizon** | `/horizon`; Linux/macOS workers (`ext-pcntl`); Windows uses `queue:listen redis` |

### Domain events and notifications (D3)

| Piece | Location | Notes |
|-------|----------|-------|
| Event | `SaleCompleted` | Dispatched post-commit from `CompleteSaleAction` |
| Listener | `NotifyManagersOfSaleCompleted` | `ShouldQueue`; active managers on the sale store |
| Notification | `SaleCompletedNotification` | `database` channel; ADR-0005 decimal `total` in payload |
| API | `GET /api/admin/notifications` | Manager role; latest 20 rows |

### Realtime broadcasting (D4)

| Piece | Location | Notes |
|-------|----------|-------|
| Server | **Laravel Reverb** | `php artisan reverb:start`; port `8080` in `.env.example` |
| Transport | `BROADCAST_CONNECTION=reverb` | PHPUnit uses `null` |
| Payload | `SaleCompletedNotification` | `via`: `database` + `broadcast` |
| Auth | `routes/channels.php` | `App.Models.User.{id}` — active user, id match |
| Frontend | `laravel-echo` + `pusher-js` | `useAdminRealtimeNotifications`; Vite proxy `/broadcasting` |
| Dev | `composer dev` | Adds Reverb process alongside queue + Vite |
