# Customers Domain

> Bounded context. Rules: RN-013, RN-030–033.

## Responsibility

Customer registry (PII), identification at POS by CPF, optional attach to in-progress sales, and purchase spend aggregates (lifetime + per store).

## Ubiquitous language

| Term | Definition |
|------|------------|
| Customer | Registered person with name, email, CPF, phone, birth_date, address |
| Walk-in | Sale completed without `customer_id` (RN-013) |
| Lifetime spend | Denormalized total of completed sales with this customer |
| Store stats | Per-store purchase count and spend for analytics (RN-033) |
| Blind index | HMAC of normalized CPF/email for equality search without storing plaintext digests of the raw value alone |

## Related business rules

| RN | Summary |
|----|---------|
| RN-013 | Sale may complete without identified customer |
| RN-030 | Identified customer must exist in registry |
| RN-031 | Required fields: name, email, CPF, phone, birth_date, address |
| RN-032 | CPF unique system-wide (`cpf_hash`) |
| RN-033 | History per store + lifetime spend |

## HTTP surface

| Method | Path | Role |
|--------|------|------|
| `GET/POST` | `/api/admin/customers` | Manager (full decrypted PII) |
| `GET/PATCH` | `/api/admin/customers/{id}` | Manager |
| `GET` | `/api/operational/customers?cpf=` | Operator/Manager — **masked CPF** |
| `POST` | `/api/operational/customers` | Operator/Manager |
| `POST` | `/api/operational/sales/{id}/customer` | Operator/Manager + open shift |

## Privacy (LGPD)

- At rest: `cpf`, `email`, `phone`, `address`, `birth_date` encrypted (`CUSTOMER_PII_ENCRYPTION_KEY`). See [ADR-0008](../adr/0008-customer-pii-encryption-lgpd.md).
- Lookup: `cpf_hash` / `email_hash` (HMAC). Admin search: name substring + exact CPF/email when query shape matches.
- PAN/card data is never persisted.
- Legal drafts + counsel pack: [privacy](../legal/privacy-policy.md), [retention](../legal/data-retention.md), [sign-off](../legal/counsel-sign-off.md).
- Retention job: `php artisan customers:anonymize-expired` (daily schedule); `CUSTOMER_PII_RETENTION_DAYS` (default 1825). Sets `anonymized_at`, keeps `id` for sale FKs.

## Notes

- CPF input may include punctuation; stored/normalized as 11 digits before encrypt + hash
- Errors: `CUST_*` in `docs/errors.md`
