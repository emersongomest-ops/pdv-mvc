# Sales Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Open carts, lines, hold/resume, complete sale with payments and fiscal receipt side effects.

## Related business rules

| RN | Summary |
|----|---------|
| RN-010–015 | Cart, complete, fiscal |
| RN-050–051 | Payments on complete |
| RN-073 | `Idempotency-Key` required on `POST .../sales/{id}/complete` |

## HTTP surface (complete)

| Method | Path | Notes |
|--------|------|--------|
| `POST` | `/api/operational/sales/{saleId}/complete` | Header `Idempotency-Key` (RN-073); body `{ payments: [...] }` |

## Notes

- Snake case key: `sales`
- Safe retry: same key + same payments payload replays stored JSON (`Idempotent-Replayed: true`)
