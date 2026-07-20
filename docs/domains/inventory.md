# Inventory Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Per-store stock quantities and administrative adjustments. Adjustments require a reason and are audited (RN-023 / RN-070) under RN-064 store assignment.

## Ubiquitous language

| Term | Definition |
|------|------------|
| **Stock** | Quantity on hand for a product in a store |
| **Adjustment** | Manager-driven quantity change with mandatory reason |

## Related business rules

| RN | Summary |
|----|---------|
| RN-023 | Manual adjust is administrative; reason mandatory + audited |
| RN-064 | Manager must be assigned to the store |
| RN-070 | `inventory.stock_adjusted` in same transaction as stock write |

## Notes

- Snake case key: `inventory`
- Admin: list/adjust under `/api/admin/inventory/*`
- Sale completion locks all tracked inventories in one query, validates quantities in memory, then persists decrements with one parameterized upsert. Query count stays constant as cart lines grow.
- Audit detail: `docs/domains/audit.md`
