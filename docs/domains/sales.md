# Sales Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Open carts, lines, hold/resume, complete sale with payments and fiscal receipt side effects.

## Related business rules

| RN | Summary |
|----|---------|
| RN-010–015 | Cart, complete, fiscal |
| RN-050–051 | Payments on complete |
| RN-073 | `Idempotency-Key` on create sale, add line, and complete |

## HTTP surface (cart + complete)

| Method | Path | Notes |
|--------|------|--------|
| `POST` | `/api/operational/sales` | Header `Idempotency-Key`; optional `{ product_id, quantity }` |
| `POST` | `/api/operational/sales/{saleId}/lines` | Header `Idempotency-Key`; body `{ product_id, quantity }` |
| `POST` | `/api/operational/sales/{saleId}/complete` | Header `Idempotency-Key`; body `{ payments: [...] }` |

## Notes

- Snake case key: `sales`
- Safe retry: same key + same payload replays stored JSON (`Idempotent-Replayed: true`)
- Retention: daily `php artisan idempotency:purge` (default 7 days)
