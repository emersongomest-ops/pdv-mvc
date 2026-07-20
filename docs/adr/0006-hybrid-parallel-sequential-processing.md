# ADR-0006 — Hybrid parallel / sequential processing

- **Status:** Accepted
- **Date:** 2026-07-16
- **Context:** The PDV API mixes (1) transactional use cases that must stay consistent, (2) independent read aggregations, and (3) post-commit side effects that can retry. Without a clear model, developers either block the HTTP request with sequential work that could be parallel, or move money/stock mutations into queues and break RN integrity.

- **Decision:** Use Laravel 13 primitives in a **hybrid** model — pick the smallest tool that fits:

  | Need | Tool | Notes |
  |------|------|--------|
  | Money, stock, payment, refund inside one consistency boundary | **Synchronous `DB::transaction` in the Action** | Never parallelize; never queue the mutation itself |
  | Independent reads / metrics in one request | **`Concurrency::run`** | Prefer `sync` driver in tests; `process`/`fork` in CLI/prod when useful |
  | Side effects after response (no return value needed) | **`Concurrency::defer`** | Metrics / non-critical reporting |
  | Ordered async steps (A then B then C) | **`Bus::chain` / `BusOrchestrator::chain`** | Stop on first failure |
  | Independent async jobs | **`Bus::batch` / `BusOrchestrator::batch`** | Parallel workers + `then`/`catch`/`finally` |
  | Ordered stages with parallel work inside a stage | **`BusOrchestrator::hybrid`** (`chain` of jobs and/or batches) | Laravel: chain containing `Bus::batch([...])` |

  **Rules:**
  1. Application Actions own the **critical path** (transaction + domain rules).
  2. Queue jobs live under `App\Jobs\`. Prefer dispatching **after** `DB::transaction` returns (see `DispatchSaleSideEffects`). Use `$this->afterCommit()` on a job only when it is dispatched from *inside* an open transaction. Avoid defaulting all jobs to `afterCommit`: PHPUnit `RefreshDatabase` wraps tests in a transaction and would defer those jobs past assertions.
  3. Domain layer stays free of `Bus`, `Concurrency`, and queue facades (DIP).
  4. Tests: `QUEUE_CONNECTION=sync` (already); concurrency driver `sync` when exercising `Concurrency`.

- **Consequences:**
  - Fatia A: ADR + architecture § + `BusOrchestrator` + `AbstractQueuedJob`.
  - Fatia B: `GetAdminDashboardMetricsAction` loads independent KPIs with `Concurrency::run` (named results); tests use `CONCURRENCY_DRIVER=sync`.
  - Fatia C: `CompleteSaleAction` keeps payment/stock/complete/fiscal in `DB::transaction`. After commit, `DispatchSaleSideEffects` runs a **named `Bus::batch`** with:
    - `RecordSaleCompletedAnalyticsJob` (always)
    - `RecordCustomerPurchaseJob` (when `customer_id` is set)
  - Side-effect failures are logged/reported; they must **not** roll back a completed sale.
  - Fiscal/payment/stock stay synchronous until a future ADR says otherwise.
  - Sources: Laravel 13 [Queues](https://laravel.com/docs/13.x/queues) (batch, chain, batches-in-chains) and [Concurrency](https://laravel.com/docs/13.x/concurrency).
