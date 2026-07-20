# Promotions Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Manager-created promotions/coupons, customer assignment scope, stacking rules, and application on in-progress sales (sale-level discount).

## Ubiquitous language

| Term | Definition |
|------|------------|
| Promotion / coupon | Manager-defined discount identified by `code` |
| Unique stacking | Exclusive — blocks any other promotion on the sale |
| Accumulable stacking | Stacks only with other accumulable promotions |
| All customers | `applies_to_all_customers=true` — usable without assignment |
| Assigned | Pivot `customer_promotion` — required when not all-customers |

## Related business rules

| RN | Summary |
|----|---------|
| RN-040 | Base price admin-only (already catalog) |
| RN-041/042 | Discount only via valid promotion |
| RN-043 | No automatic recurrence discount |
| RN-044 | Manager assigns to customer(s) or all |
| RN-045 | Never auto-generated; explicit enable |
| RN-046 | Unique vs accumulable stacking |
| RN-047 | Total never negative |
| RN-060 | Manager CRUD promotions |
| RN-070 | Create/update promotion audited (not apply/remove on sale) |

## HTTP surface

| Method | Path | Role |
|--------|------|------|
| `GET/POST` | `/api/admin/promotions` | Manager |
| `GET/PATCH` | `/api/admin/promotions/{id}` | Manager |
| `POST` | `/api/operational/sales/{id}/promotions` | body `{code}` |
| `DELETE` | `/api/operational/sales/{id}/promotions/{promotionId}` | Operator/Manager |

## Notes

- Snake case key: `promotions`
- Discount types: `percent`, `fixed`
- Errors: `PROMO_*` in `docs/errors.md`
- Admin create/update writes `promotion.created` / `promotion.updated` (RN-070); operational apply/remove on sale does not
