# CashShift Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Cash shift lifecycle per operator per store: open, close, and gate operational routes until a shift is open (RN-001).

## Ubiquitous language

| Term | Definition |
|------|------------|
| **Cash shift** | Operator work session at one store |
| **Open shift** | Active shift allowing sales on operational routes |
| **Closed shift** | Shift ended with optional closing cash count |

## Related business rules

| RN | Summary |
|----|---------|
| RN-001 | No sale / operational POS without open shift at current store |
| RN-002 | Operator may have only one open shift at a time |
| RN-003 | Closing consolidates totals (sales, payment methods, cash variance) |
| RN-004 | Reopen closed shift — Manager only; audited as `cash_shift.reopened` |

## API (MVP)

| Method | Path | Middleware | Description |
|--------|------|------------|-------------|
| POST | `/api/operational/shifts/open` | auth, role, store.context | Open shift |
| POST | `/api/operational/shifts/close` | auth, role, store.context | Close current shift |
| GET | `/api/operational/shifts/current` | auth, role, store.context | Current open shift or null |
| GET | `/api/admin/shifts?store_id=` | auth, role:manager | List shifts for assigned store |
| GET | `/api/admin/shifts/{id}/report` | auth, role:manager | Closing report |
| POST | `/api/admin/shifts/{id}/reopen` | auth, role:manager | Reopen closed shift (RN-004); fails if operator already has an open shift |

## Notes

- Snake case key: `cash_shift`
- Errors: `SHIFT_*` in `ErrorCode.php`
