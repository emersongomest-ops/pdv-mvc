# ADR-0005 — Money as integer cents

- **Status:** Accepted
- **Date:** 2026-07-16
- **Context:** Monetary values were stored and computed as `decimal(…,2)` with BCMath string math. Float/decimal drift and inconsistent rounding across layers (DB, domain, HTTP) increase risk in totals, payments, promotions, and refunds.

- **Decision:** Persist and calculate money as **integer cents** in domain and database. HTTP API continues to accept and return **decimal strings** (e.g. `"13.00"`). Conversion happens only at the HTTP boundary via `App\Domain\Shared\Money`.

  - `R$13.00` → DB/domain `1300` → API `"13.00"`
  - Promotion percent uses the same ×100 scale: `10.00%` → `1000` (`Money::percentOf` divides by `10000`)

- **Consequences:**
  - All money columns are `unsignedBigInteger` (after migration `2026_07_16_920000_convert_money_columns_to_integer_cents`).
  - Model casts use `integer`.
  - Feature tests keep asserting JSON decimal strings; factories/tests that write to DB use cents.
  - Frontend may still send numeric JSON; boundary converts with `Money::fromDecimalInput`.
