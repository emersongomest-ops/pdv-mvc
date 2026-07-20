# Store Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Multi-store configuration and **operator store context** per session. Users are assigned to one or more stores (`store_user`). Operational routes require an active store context in session (RN-065). Administrative store-scoped reads/writes (dashboard KPIs, sales, shifts, inventory, refunds, audit log filters) require assignment but **not** session context (RN-064). Catalog admin stays global. Global audit rows (`store_id=null`) are visible to all managers.

## Ubiquitous language

| Term | Definition |
|------|------------|
| **Store** | Physical branch; scopes inventory, shifts, and sales |
| **Store context** | Selected store id held in session (`store_context_id`) |
| **Assignment** | Many-to-many link between users and stores (`store_user`) |

## Related business rules

| RN | Summary |
|----|---------|
| RN-064 | Multi-store; inventory/shifts/sales scoped per store; users access assigned stores only |
| RN-065 | Operator works in one store context per session |

## API (MVP)

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/stores` | List active stores assigned to authenticated user |
| POST | `/api/stores/context` | Body: `{ "store_id": int }` — set session store context |

## Admin (RN-064)

- Gate: `AssertManagerStoreAccess` using `StoreRepository::userCanAccessStore` / `assignedStoreIds`
- Unassigned store or resource from another store → `AUTH_STORE_ACCESS_DENIED` (403)
- Do **not** apply `store.context` to `/api/admin/*` (aggregates need all assigned stores)

## Notes

- Snake case key: `store`
- Middleware: `store.context` on operational routes only
- Errors: `STORE_*` (domain), `AUTH_STORE_*` (access layer)
