# Graph Report - projects\pdv\frontend  (2026-07-16)

## Corpus Check
- 67 files · ~14,894 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 352 nodes · 860 edges · 16 communities (15 shown, 1 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 2 edges (avg confidence: 0.5)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `7bf6a697`
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

## God Nodes (most connected - your core abstractions)
1. `react` - 28 edges
2. `formatApiError()` - 27 edges
3. `useSession()` - 23 edges
4. `usePosActivity()` - 18 edges
5. `compilerOptions` - 18 edges
6. `compilerOptions` - 15 edges
7. `usePosSale()` - 13 edges
8. `listStores()` - 13 edges
9. `usePosSaleState()` - 12 edges
10. `usePosHeld()` - 12 edges

## Surprising Connections (you probably didn't know these)
- `HomeRedirect()` --calls--> `useSession()`  [EXTRACTED]
  projects/pdv/frontend/src/App.tsx → projects/pdv/frontend/src/shared/session/SessionContext.tsx
- `StoreSelectPage()` --calls--> `listStores()`  [EXTRACTED]
  projects/pdv/frontend/src/apps/operational/pages/StoreSelectPage.tsx → projects/pdv/frontend/src/shared/api/client.ts
- `useAdminAuditLogs()` --calls--> `formatApiError()`  [EXTRACTED]
  projects/pdv/frontend/src/features/admin/hooks/useAdminAuditLogs.ts → projects/pdv/frontend/src/shared/session/SessionContext.tsx
- `useAdminDashboard()` --calls--> `formatApiError()`  [EXTRACTED]
  projects/pdv/frontend/src/features/admin/hooks/useAdminDashboard.ts → projects/pdv/frontend/src/shared/session/SessionContext.tsx
- `useAdminDashboard()` --calls--> `useSession()`  [EXTRACTED]
  projects/pdv/frontend/src/features/admin/hooks/useAdminDashboard.ts → projects/pdv/frontend/src/shared/session/SessionContext.tsx

## Import Cycles
- None detected.

## Communities (16 total, 1 thin omitted)

### Community 0 - "SessionContext.tsx"
Cohesion: 0.13
Nodes (35): CartContainer(), CatalogContainer(), ActivityContext, ActivityState, CatalogContext, CatalogState, CustomerContext, CustomerState (+27 more)

### Community 1 - "compilerOptions"
Cohesion: 0.08
Nodes (23): DOM, src, vite/client, compilerOptions, allowArbitraryExtensions, allowImportingTsExtensions, erasableSyntaxOnly, jsx (+15 more)

### Community 2 - "compilerOptions"
Cohesion: 0.10
Nodes (19): node, vite.config.ts, compilerOptions, allowImportingTsExtensions, erasableSyntaxOnly, lib, module, moduleDetection (+11 more)

### Community 3 - "client.ts"
Cohesion: 0.12
Nodes (22): formatWhen(), SalesTable(), SalesTableProps, CartCustomerForm(), CartCustomerFormProps, CartLines(), CartLinesProps, CartPaymentForm() (+14 more)

### Community 4 - "package.json"
Cohesion: 0.06
Nodes (35): laravel-echo, oxlint, dependencies, laravel-echo, pusher-js, react, react-dom, react-router-dom (+27 more)

### Community 5 - "devDependencies"
Cohesion: 0.23
Nodes (12): RefundsPage(), LineSelection, useAdminRefunds(), RefundsPanel(), RefundsPanelProps, refundTypes, createRefund(), getAdminSale() (+4 more)

### Community 6 - "plugins"
Cohesion: 0.22
Nodes (8): plugins, rules, react/only-export-components, react/rules-of-hooks, $schema, oxc, typescript, warn

### Community 9 - "SessionContext.tsx"
Cohesion: 0.12
Nodes (28): react, App(), HomeRedirect(), RequireManager(), PlaceholderPage(), RequireAuth(), LoginPage(), PosPage() (+20 more)

### Community 10 - "PDV Frontend — Operational SPA"
Cohesion: 0.33
Nodes (5): Env notes, Flow, PDV Frontend — Operational + Administrative SPA, Run locally, Structure

### Community 11 - "formatApiError"
Cohesion: 0.12
Nodes (18): CatalogPage(), DashboardView(), DashboardViewProps, formatActivity(), LoginForm(), LoginFormProps, useAdminProducts(), ProductTable() (+10 more)

### Community 12 - "types.ts"
Cohesion: 0.08
Nodes (42): AuditLogPage(), SalesPage(), UsersPage(), useAdminAuditLogs(), useAdminSales(), emptyForm(), formFromUser(), useAdminUsers() (+34 more)

### Community 13 - "useAdminDashboard.ts"
Cohesion: 0.23
Nodes (11): DashboardPage(), toAdminNotification(), useAdminDashboard(), RealtimeNotificationPayload, useAdminRealtimeNotifications(), getAdminDashboard(), listAdminNotifications(), AdminDashboardMetrics (+3 more)

### Community 14 - "useAdminSales.ts"
Cohesion: 0.22
Nodes (14): ApiClientError, apiRequest(), closeShift(), fetchCurrentUser(), getAdminUser(), loginRequest(), logoutRequest(), openShift() (+6 more)

### Community 15 - "useAdminShifts.ts"
Cohesion: 0.29
Nodes (9): ShiftsPage(), useAdminShifts(), formatWhen(), ShiftsPanel(), ShiftsPanelProps, getAdminShiftReport(), listAdminShifts(), AdminShiftSummary (+1 more)

## Knowledge Gaps
- **104 isolated node(s):** `$schema`, `typescript`, `oxc`, `react/rules-of-hooks`, `warn` (+99 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **1 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `react` connect `SessionContext.tsx` to `SessionContext.tsx`, `client.ts`, `devDependencies`, `plugins`, `formatApiError`, `types.ts`, `useAdminDashboard.ts`, `useAdminShifts.ts`?**
  _High betweenness centrality (0.091) - this node is a cross-community bridge._
- **Why does `plugins` connect `plugins` to `SessionContext.tsx`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Why does `formatApiError()` connect `SessionContext.tsx` to `SessionContext.tsx`, `devDependencies`, `formatApiError`, `types.ts`, `useAdminDashboard.ts`, `useAdminShifts.ts`?**
  _High betweenness centrality (0.020) - this node is a cross-community bridge._
- **What connects `$schema`, `typescript`, `oxc` to the rest of the system?**
  _104 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `SessionContext.tsx` be split into smaller, more focused modules?**
  _Cohesion score 0.12579281183932348 - nodes in this community are weakly interconnected._
- **Should `compilerOptions` be split into smaller, more focused modules?**
  _Cohesion score 0.08333333333333333 - nodes in this community are weakly interconnected._
- **Should `compilerOptions` be split into smaller, more focused modules?**
  _Cohesion score 0.1 - nodes in this community are weakly interconnected._