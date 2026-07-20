# ADR-0008 — Customer PII encryption at rest (LGPD)

- **Status:** Accepted
- **Date:** 2026-07-20
- **Context:** Customer records hold CPF, email, phone, address, and birth date. Storing these in plaintext increases breach impact and conflicts with LGPD principles of security and data minimization. Exact CPF/email lookup must still work for POS and uniqueness.

- **Decision:**
  - Encrypt `cpf`, `email`, `phone`, `address`, `birth_date` with AES-256-CBC via a dedicated `CUSTOMER_PII_ENCRYPTION_KEY` (separate from `APP_KEY`).
  - Persist blind indexes `cpf_hash` / `email_hash` (HMAC-SHA256 with `CUSTOMER_PII_BLIND_INDEX_KEY`) for equality search and unique constraints.
  - Keep `name` plaintext for list/sort/cursor pagination.
  - Never persist card PAN (PCI); card validation is ephemeral in memory only.
  - Operational CPF lookup returns a **masked** CPF; admin resources return decrypted full values to authorized managers.
  - Substring search on encrypted email/CPF is not supported; search uses name `LIKE` plus exact CPF/email when the query looks like an 11-digit CPF or an email.

- **Consequences:**
  - Key loss = irreversible PII ciphertext; keys must be backed up outside the DB.
  - Rotating blind-index key requires rehash of all customers.
  - Demo seeders/factories remain plaintext at the model layer; casts encrypt on write.
