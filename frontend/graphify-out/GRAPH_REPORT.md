# Graph Report - projects\pdv\frontend  (2026-07-19)

## Corpus Check
- 76 files · ~17,383 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 398 nodes · 1001 edges · 18 communities (17 shown, 1 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 5 edges (avg confidence: 0.5)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `76037f89`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- SessionContext.tsx
- compilerOptions
- compilerOptions
- client.ts
- package.json
- devDependencies
- plugins
- tsconfig.json
- SessionContext.tsx
- PDV Frontend — Operational SPA
- formatApiError
- types.ts
- useAdminDashboard.ts
- useAdminSales.ts
- useAdminShifts.ts
- useAdminCustomers.ts

## God Nodes (most connected - your core abstractions)
1. `formatApiError()` - 33 edges
2. `react` - 31 edges
3. `useSession()` - 23 edges
4. `usePosActivity()` - 18 edges
5. `compilerOptions` - 18 edges
6. `listStores()` - 15 edges
7. `compilerOptions` - 15 edges
8. `usePosSale()` - 13 edges
9. `Store` - 13 edges
10. `ErrorBanner()` - 13 edges

## Surprising Connections (you probably didn't know these)
- `HomeRedirect()` --calls--> `useSession()`  [EXTRACTED]
  projects/pdv/frontend/src/App.tsx → projects/pdv/frontend/src/shared/session/SessionContext.tsx
- `ShiftsPage()` --calls--> `useAdminShifts()`  [EXTRACTED]
  projects/pdv/frontend/src/apps/administrative/pages/ShiftsPage.tsx → projects/pdv/frontend/src/features/admin/hooks/useAdminShifts.ts
- `StoreSelectPage()` --calls--> `listStores()`  [EXTRACTED]
  projects/pdv/frontend/src/apps/operational/pages/StoreSelectPage.tsx → projects/pdv/frontend/src/shared/api/client.ts
- `useAdminAuditLogs()` --calls--> `formatApiError()`  [EXTRACTED]
  projects/pdv/frontend/src/features/admin/hooks/useAdminAuditLogs.ts → projects/pdv/frontend/src/shared/session/SessionContext.tsx
- `useAdminCustomers()` --calls--> `formatApiError()`  [EXTRACTED]
  projects/pdv/frontend/src/features/admin/hooks/useAdminCustomers.ts → projects/pdv/frontend/src/shared/session/SessionContext.tsx

## Import Cycles
- None detected.

## Communities (18 total, 1 thin omitted)

### Community 0 - "SessionContext.tsx"
Cohesion: 0.12
Nodes (37): react, PosLayoutProps, CartContainer(), CatalogContainer(), ActivityContext, ActivityState, CatalogContext, CatalogState (+29 more)

### Community 1 - "compilerOptions"
Cohesion: 0.08
Nodes (23): DOM, src, vite/client, compilerOptions, allowArbitraryExtensions, allowImportingTsExtensions, erasableSyntaxOnly, jsx (+15 more)

### Community 2 - "compilerOptions"
Cohesion: 0.10
Nodes (19): node, vite.config.ts, compilerOptions, allowImportingTsExtensions, erasableSyntaxOnly, lib, module, moduleDetection (+11 more)

### Community 3 - "client.ts"
Cohesion: 0.13
Nodes (21): ProductTable(), ProductTableProps, CartCustomerForm(), CartCustomerFormProps, CartLines(), CartLinesProps, CartPaymentForm(), CartPaymentFormProps (+13 more)

### Community 4 - "package.json"
Cohesion: 0.06
Nodes (35): laravel-echo, oxlint, dependencies, laravel-echo, pusher-js, react, react-dom, react-router-dom (+27 more)

### Community 5 - "devDependencies"
Cohesion: 0.13
Nodes (24): RefundsPage(), LineSelection, useAdminRefunds(), RefundsPanel(), RefundsPanelProps, refundTypes, ApiClientError, apiRequest() (+16 more)

### Community 6 - "plugins"
Cohesion: 0.22
Nodes (8): plugins, rules, react/only-export-components, react/rules-of-hooks, $schema, oxc, typescript, warn

### Community 9 - "SessionContext.tsx"
Cohesion: 0.12
Nodes (28): App(), HomeRedirect(), RequireManager(), RequireAuth(), LoginPage(), PosPage(), ShiftPage(), StoreSelectPage() (+20 more)

### Community 10 - "PDV Frontend — Operational SPA"
Cohesion: 0.33
Nodes (5): Env notes, Flow, PDV Frontend — Operational + Administrative SPA, Run locally, Structure

### Community 11 - "formatApiError"
Cohesion: 0.11
Nodes (22): CatalogPage(), InventoryPage(), ShiftsPage(), emptyAdjust(), InventoryAdjustForm, useAdminInventory(), InventoryPanel(), InventoryPanelProps (+14 more)

### Community 12 - "types.ts"
Cohesion: 0.08
Nodes (43): AuditLogPage(), UsersPage(), useAdminAuditLogs(), useAdminShifts(), emptyForm(), formFromUser(), useAdminUsers(), UserFormState (+35 more)

### Community 13 - "useAdminDashboard.ts"
Cohesion: 0.17
Nodes (15): DashboardPage(), toAdminNotification(), useAdminDashboard(), RealtimeNotificationPayload, useAdminRealtimeNotifications(), DashboardView(), DashboardViewProps, formatActivity() (+7 more)

### Community 14 - "useAdminSales.ts"
Cohesion: 0.19
Nodes (15): PromotionsPage(), emptyForm(), formFromPromotion(), PromotionFormState, toPayload(), useAdminPromotions(), PromotionsPanel(), PromotionsPanelProps (+7 more)

### Community 15 - "useAdminShifts.ts"
Cohesion: 0.21
Nodes (11): SalesPage(), useAdminSales(), paymentMethods, SalesFilters(), SalesFiltersProps, formatWhen(), SalesTable(), SalesTableProps (+3 more)

### Community 16 - "useAdminCustomers.ts"
Cohesion: 0.25
Nodes (11): CustomersPage(), CustomerFormState, emptyForm(), formFromCustomer(), useAdminCustomers(), CustomersPanel(), CustomersPanelProps, createAdminCustomer() (+3 more)

## Knowledge Gaps
- **109 isolated node(s):** `$schema`, `typescript`, `oxc`, `react/rules-of-hooks`, `warn` (+104 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **1 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `react` connect `SessionContext.tsx` to `client.ts`, `devDependencies`, `plugins`, `SessionContext.tsx`, `formatApiError`, `types.ts`, `useAdminDashboard.ts`, `useAdminSales.ts`, `useAdminShifts.ts`, `useAdminCustomers.ts`?**
  _High betweenness centrality (0.086) - this node is a cross-community bridge._
- **Why does `plugins` connect `plugins` to `SessionContext.tsx`?**
  _High betweenness centrality (0.030) - this node is a cross-community bridge._
- **Why does `formatApiError()` connect `SessionContext.tsx` to `SessionContext.tsx`, `devDependencies`, `formatApiError`, `types.ts`, `useAdminDashboard.ts`, `useAdminSales.ts`, `useAdminShifts.ts`, `useAdminCustomers.ts`?**
  _High betweenness centrality (0.026) - this node is a cross-community bridge._
- **What connects `$schema`, `typescript`, `oxc` to the rest of the system?**
  _109 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `SessionContext.tsx` be split into smaller, more focused modules?**
  _Cohesion score 0.12395929694727105 - nodes in this community are weakly interconnected._
- **Should `compilerOptions` be split into smaller, more focused modules?**
  _Cohesion score 0.08333333333333333 - nodes in this community are weakly interconnected._
- **Should `compilerOptions` be split into smaller, more focused modules?**
  _Cohesion score 0.1 - nodes in this community are weakly interconnected._