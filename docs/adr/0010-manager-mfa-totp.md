# ADR-0010 — Manager MFA (TOTP)

- **Status:** Accepted
- **Date:** 2026-07-20
- **Context:** `docs/security.md` requires MFA on Manager accounts against phishing and credential stuffing. Operators use shared POS stations where TOTP friction is undesirable for MVP.

- **Decision:**
  - Managers must complete TOTP (RFC 6238) after password before a Sanctum session is established.
  - Password success for managers stores only `mfa.pending_user_id` in session (guest until verify/confirm).
  - First login without `mfa_confirmed_at` forces enroll via `POST /api/auth/mfa/setup` + `confirm`.
  - Secret stored with Laravel `encrypted` cast (`APP_KEY`); OTP replay blocked via `verifyKeyNewer` + `mfa_last_otp_timestamp`.
  - Library: `pragmarx/google2fa` + `bacon/bacon-qr-code` (SVG QR as data URI).
  - Operators unchanged (password-only).

- **Consequences:**
  - Demo manager seeds with fixed secret `JBSWY3DPEHPK3PXP` (local/demo only).
  - Recovery codes and MFA reset-by-admin deferred.
  - Existing manager login feature tests must complete MFA challenge.
