# Audit Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Central, immutable audit trail for sensitive administrative mutations. Writes are append-only and must succeed inside the same database transaction as the business change; reads are manager-only with multi-store scope (RN-064).

## Ubiquitous language

| Term | Definition |
|------|------------|
| **Audit log** | Append-only row: actor, optional store, action, subject, old/new values, allowlisted metadata, `occurred_at` |
| **Action** | Stable string enum (`AuditAction`), e.g. `catalog.product.price_changed` |
| **Subject** | Polymorphic target (`subject_type` + `subject_id`) |
| **Global entry** | `store_id = null` (catalog/promotion management) — visible to all managers |

## Related business rules

| RN | Summary |
|----|---------|
| RN-070 | Who/when/what for price, stock, refund/return, promotion create/update, shift reopen (RN-004) |
| RN-004 | Manager reopen closed shift — audited |
| RN-019a | Refund/return audit (implemented via RN-070 entries) |
| RN-023 | Stock adjust reason mandatory + audited |
| RN-064 | Managers only see assigned stores (+ global) |

## Audited events

| Action | When | Store scope |
|--------|------|-------------|
| `catalog.product.price_changed` | Effective `base_price` change only | Global |
| `inventory.stock_adjusted` | Admin inventory adjust | Store |
| `refund.created` / `return.created` | Refund/return create | Store (sale) |
| `promotion.created` / `promotion.updated` | Admin promotion create/update (incl. `is_active`, customer IDs) | Global |
| `cash_shift.reopened` | Manager reopens a closed shift (RN-004) | Store |

**Not audited:** product create price, apply/remove promotion on an in-progress sale.

## Immutability

- Model rejects `update` / `delete`
- DB triggers on MySQL and SQLite reject UPDATE/DELETE
- No HTTP mutation routes for audit logs

## HTTP surface

| Method | Path | Role |
|--------|------|------|
| `GET` | `/api/admin/audit-logs` | Manager |

Query filters: `from`, `to`, `action`, `actor_id`, `store_id`, `subject_type`, `subject_id`, `per_page` (max 100), `cursor`.

Scope: `(store_id IN assigned) OR store_id IS NULL`. Explicit unassigned `store_id` → `403 AUTH_STORE_ACCESS_DENIED`.

## Frontend

- Route `/admin/audit-log` — filters, table, expandable JSON details, cursor “Load more”

## Notes

- Snake case key: `audit`
- Errors: reuse `AUTH_*` / validation `422`; no dedicated `AUDIT_*` mutation codes (read-only API)
- Tests: `tests/Feature/Audit/AdminAuditLogTest.php`
