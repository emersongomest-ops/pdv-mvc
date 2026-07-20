# ADR-0007 — Observability and messaging baseline

- **Status:** Accepted
- **Date:** 2026-07-16
- **Context:** PDV already uses queues (ADR-0006), Pail in `composer dev`, and ADR-002 (Redis). We need a consistent local/prod story for **monitoring**, **structured logs**, and **async messaging** before adding domain events and realtime (future slices).

- **Decision:**

  ### Observability

  | Tool | Scope | Access |
  |------|-------|--------|
  | **Pail** | Live log tail (dev) | CLI — already in `composer dev` |
  | **Telescope** | Deep debug (requests, jobs, queries, exceptions) | **Local only** (`TELESCOPE_ENABLED`); `/telescope` |
  | **Pulse** | App health KPIs (slow jobs/endpoints, usage) | `/pulse`; `viewPulse` gate → active **Manager** |
  | **Correlation ID** | `AssignCorrelationId` middleware | `X-Request-Id` in/out; `request_id` in log context |

  Structured logs: default `LOG_CHANNEL=stack`; optional `LOG_STACK=single,structured` adds JSON lines (`config/logging.php` → `structured` channel) for log aggregators.

  ### Messaging (async)

  | Layer | Choice | Notes |
  |-------|--------|-------|
  | Transport | **Redis** (`QUEUE_CONNECTION=redis`) | ADR-002; default in `.env.example` |
  | Workers (Linux/macOS) | **Horizon** | Dashboard `/horizon`; `viewHorizon` gate → active **Manager** |
  | Workers (Windows dev) | **`queue:listen redis`** | Horizon requires `ext-pcntl` + `ext-posix` (not on Windows) |
  | Jobs | `AbstractQueuedJob` + `BusOrchestrator` | ADR-0006; batches/chains unchanged |

  PHPUnit keeps `QUEUE_CONNECTION=sync`, `TELESCOPE_ENABLED=false`, `PULSE_ENABLED=false`.

- **Consequences:**
  - New deps: `laravel/pulse`, `laravel/horizon`, `laravel/telescope` (dev).
  - Migrations: Pulse + Telescope tables (run `php artisan migrate` after pull).
  - Prod: run Horizon on Linux workers; Redis required for queue + Horizon metrics.
  - Windows dev: Redis + `queue:listen`; skip `artisan horizon`.
  - D3 (done): `SaleCompleted` domain event → queued listener → `database` notifications; `GET /api/admin/notifications`.
  - D4 (done): **Reverb** + `broadcast` channel on `SaleCompletedNotification`; Echo on admin dashboard (`App.Models.User.{id}` private channel).

- **Sources:** Laravel 13 [Telescope](https://laravel.com/docs/13.x/telescope), [Pulse](https://laravel.com/docs/13.x/pulse), [Horizon](https://laravel.com/docs/13.x/horizon), [Logging](https://laravel.com/docs/13.x/logging).
