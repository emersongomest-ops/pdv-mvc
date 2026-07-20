# Analytics Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Read-model analytics and campaign targeting for managers: registration trends, recurrence, per-store spend, birthday/region customer filters.

## Ubiquitous language

| Term | Definition |
|------|------------|
| **Registration bucket** | Count of new customers created on a calendar day |
| **Recurrence index** | Share of purchasing customers (assigned stores) with ≥2 purchases |
| **Store spend** | `customer_store_stats.total_spend` for a customer at one store |
| **Region filter** | Case-insensitive substring match on `customers.address` |

## Related business rules

| RN | Summary |
|----|---------|
| RN-080 | New customer registrations over time |
| RN-081 | Recurrence index (repeat purchase rate) |
| RN-082 | Spend per store and lifetime total per customer |
| RN-083 | Birthday campaigns — filter by `birth_date` month |
| RN-084 | Regional campaigns — filter by address region |
| RN-064 | Recurrence + top spend scoped to manager assigned stores |

## API

| Method | Path | Notes |
|--------|------|-------|
| GET | `/api/admin/analytics` | Optional `registration_days` (1–90), `top_customers` (1–100) |
| GET | `/api/admin/campaigns/customers` | Optional `birth_month` (1–12), `region` |

## Notes

- Snake case key: `analytics`
- Frontend: `/admin/analytics`
