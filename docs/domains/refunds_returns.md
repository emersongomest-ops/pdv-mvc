# RefundsReturns Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Full/partial refunds and returns against completed sales, with payment stub reversal, optional stock restock, and audit trail (who, when, reason, sale id).

## Ubiquitous language

| Term | Definition |
|------|------------|
| Full refund | Reverse remaining sale amount; restocks remaining items (RN-016) |
| Partial refund | Money for subset of lines; no restock (RN-017) |
| Full return | All remaining items returned + full remaining refund (RN-018) |
| Partial return | Subset of items restocked + proportional refund (RN-019) |

## Related business rules

| RN | Summary |
|----|---------|
| RN-011 | Completed sale immutable; corrections via refund/return |
| RN-016 | Full refund |
| RN-017 | Partial refund |
| RN-018 | Full return |
| RN-019 | Partial return |
| RN-019a / RN-070 | Audit trail |
| RN-073 | Idempotency-Key required on create refund |

## HTTP surface

| Method | Path | Role |
|--------|------|------|
| `POST` | `/api/admin/sales/{saleId}/refunds` | Manager |
| `GET` | `/api/admin/sales/{saleId}/refunds` | Manager |

Body: `{ type, reason, lines?: [{ sale_line_id, quantity }] }`  
Header (POST): `Idempotency-Key` (RN-073)

## Notes

- Snake case key: `refunds_returns`
- Errors: `REF_*` + `IDEMPOTENCY_*` in `docs/errors.md`
- Create path appends `refund.created` or `return.created` in the same transaction (RN-070); see `docs/domains/audit.md`
- Replay-safe create via `IdempotencyGuard` (same key + payload → stored 201); old keys purged daily (`idempotency:purge`)
