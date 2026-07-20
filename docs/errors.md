# Error Catalog — POS (PDV)

> **Last updated:** 2026-07-15  
> **Convention:** All user-facing and log messages in **English**.  
> **Format:** `DOMAIN_ACTION_REASON` — HTTP code is transport hint only; domain uses exception/code enum.

Implement in `app/Domain/Shared/ErrorCode.php` (or per-context enums implementing a shared `ErrorCodeInterface`).

---

## Format

| Field | Description |
|-------|-------------|
| **Code** | Stable string, e.g. `SALE_SHIFT_NOT_OPEN` |
| **HTTP** | Suggested API status |
| **Message** | Clear English message for operators/managers |
| **RN** | Related business rule(s) |

---

## Shift (`SHIFT_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `SHIFT_NOT_OPEN` | 422 | Cannot sell without an open cash shift. | RN-001 |
| `SHIFT_ALREADY_OPEN` | 409 | You already have an open shift. | RN-002 |
| `SHIFT_STORE_MISMATCH` | 422 | Shift belongs to another store. | RN-065 |
| `SHIFT_REOPEN_DENIED` | 403 | Manager authorization required to reopen shift. | RN-004 |
| `SHIFT_NOT_FOUND` | 404 | Cash shift not found. | RN-001 |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/ShiftErrorCodeTest.php`, `tests/Feature/CashShift/*`.

## Sale (`SALE_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `SALE_NOT_FOUND` | 404 | Sale not found. | RN-010 |
| `SALE_LINE_NOT_FOUND` | 404 | Sale line not found. | RN-010 |
| `SALE_EMPTY_CART` | 422 | Cannot complete sale with an empty cart. | RN-010 |
| `SALE_ALREADY_COMPLETED` | 409 | Sale is already completed and cannot be modified. | RN-011 |
| `SALE_NEGATIVE_TOTAL` | 422 | Sale total cannot be negative. | RN-047 |
| `SALE_PAYMENT_MISMATCH` | 422 | Payment total does not match sale amount. | RN-051 |
| `SALE_NO_PAYMENT` | 422 | At least one payment is required. | RN-050 |
| `SALE_FISCAL_RECEIPT_FAILED` | 500 | Fiscal receipt could not be generated. | RN-015 |
| `SALE_NOT_HELD` | 422 | Sale is not on hold. | RN-014 |
| `SALE_CART_HELD` | 422 | Sale is on hold; resume before modifying. | RN-014 |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/SaleErrorCodeTest.php`, `tests/Feature/Sales/*`.

## Catalog (`CAT_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `CAT_PRODUCT_NOT_FOUND` | 404 | Product not found. | RN-020 |
| `CAT_CATEGORY_NOT_FOUND` | 404 | Category not found. | RN-060 |
| `CAT_SKU_DUPLICATE` | 409 | Product SKU already exists. | RN-060 |
| `CAT_CATEGORY_NAME_DUPLICATE` | 409 | Category name already exists. | RN-060 |
| `CAT_CATEGORY_IN_USE` | 409 | Category cannot be deleted while products are assigned. | RN-060 |
| `CAT_PRODUCT_IN_USE` | 409 | Product cannot be deleted after being sold. | RN-060 |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/CatalogErrorCodeTest.php`, `tests/Feature/Catalog/*`.

## Inventory (`INV_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `INV_INSUFFICIENT_STOCK` | 422 | Insufficient stock for this product at this store. | RN-021 |
| `INV_PRODUCT_INACTIVE` | 422 | Product is inactive and cannot be sold. | RN-020 |
| `INV_ADJUSTMENT_REASON_REQUIRED` | 422 | Stock adjustment requires a reason. | RN-023 |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/InventoryErrorCodeTest.php`, `tests/Feature/Inventory/*`.

## Customer (`CUST_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `CUST_NOT_FOUND` | 404 | Customer not found. | RN-030 |
| `CUST_CPF_DUPLICATE` | 409 | CPF already registered. | RN-032 |
| `CUST_REQUIRED_FIELD_MISSING` | 422 | Required customer field is missing. | RN-031 |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/CustomerErrorCodeTest.php`, `tests/Feature/Customers/*`.

## Promotion (`PROMO_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `PROMO_NOT_FOUND` | 404 | Promotion or coupon not found. | RN-044 |
| `PROMO_NOT_APPLICABLE` | 422 | Promotion does not apply to this customer or sale. | RN-044 |
| `PROMO_EXPIRED` | 422 | Promotion has expired. | RN-045 |
| `PROMO_NOT_ASSIGNED` | 422 | Promotion was not assigned to this customer. | RN-044 |
| `PROMO_NOT_COMBINABLE` | 422 | Promotion cannot be combined with another on this sale (unique vs accumulable rule). | RN-046 |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/PromotionErrorCodeTest.php`, `tests/Feature/Promotions/*`.

## Payment (`PAY_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `PAY_GATEWAY_UNAVAILABLE` | 503 | Payment service temporarily unavailable. | RN-054 |
| `PAY_STUB_DECLINED` | 422 | Payment declined (simulated). | RN-052 |
| `PAY_METHOD_UNSUPPORTED` | 422 | Payment method is not supported. | RN-052 |
| `PAY_CASH_INSUFFICIENT` | 422 | Cash received is less than amount due. | RN-053 |
| `PAY_METHOD_NOT_IMPLEMENTED` | 501 | Payment method is not implemented. | RN-054 |
| `PAY_CARD_HOLDER_NAME_INVALID` | 422 | Cardholder name is invalid. | RN-052 |
| `PAY_CARD_NUMBER_INVALID` | 422 | Card number is invalid. | RN-052 |
| `PAY_CARD_EXPIRY_INVALID` | 422 | Card expiry date is invalid. | RN-052 |
| `PAY_CARD_HOLDER_MISMATCH` | 422 | Cardholder name does not match the indicated person. | RN-052 |
| `PAY_CARD_OWNERSHIP_UNCONFIRMED` | 422 | Card ownership for the indicated person was not confirmed. | RN-052 |
| `PAY_WEBHOOK_INVALID_SIGNATURE` | 401 | Payment webhook signature is invalid. | RN-054 |
| `PAY_WEBHOOK_PAYLOAD_INVALID` | 422 | Payment webhook payload is invalid. | RN-054 |
| `PAY_WEBHOOK_PROVIDER_UNSUPPORTED` | 422 | Payment webhook provider is not supported. | RN-054 |
| `PAY_WEBHOOK_UNKNOWN_REFERENCE` | 404 | No payment line matches the webhook transaction reference. | RN-054 |
| `PAY_WEBHOOK_AMOUNT_MISMATCH` | 422 | Webhook amount does not match the payment line. | RN-051 |
| `PAY_WEBHOOK_INVALID_TRANSITION` | 422 | Payment line cannot transition to the webhook status. | RN-054 |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/PaymentErrorCodeTest.php`, `tests/Feature/Payments/PaymentWebhookTest.php`, `tests/Feature/Sales/CompleteSaleTest.php`.

## Refund & Return (`REF_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `REF_AMOUNT_EXCEEDS_SALE` | 422 | Refund amount exceeds refundable balance. | RN-017 |
| `REF_SALE_NOT_FOUND` | 404 | Original sale not found for refund. | RN-011 |
| `REF_ALREADY_FULLY_REFUNDED` | 409 | Sale has already been fully refunded. | RN-016 |
| `REF_RETURN_QTY_INVALID` | 422 | Return quantity exceeds sold quantity. | RN-019 |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/RefundErrorCodeTest.php`, `tests/Feature/RefundsReturns/*`.

## Access (`AUTH_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `AUTH_UNAUTHENTICATED` | 401 | Authentication required. | RN-071 |
| `AUTH_INVALID_CREDENTIALS` | 401 | Invalid email or password. | RN-071 |
| `AUTH_TOKEN_MISSING` | 401 | Authentication token is missing. | — |
| `AUTH_TOKEN_INVALID` | 401 | Authentication token is invalid. | — |
| `AUTH_TOKEN_EXPIRED` | 401 | Authentication token has expired. | — |
| `AUTH_FORBIDDEN` | 403 | You do not have permission for this action. | RN-071 |
| `AUTH_ROLE_DENIED` | 403 | Your role cannot access this area. | RN-071 |
| `AUTH_ADMIN_ONLY` | 403 | This area is restricted to managers. | RN-071 |
| `AUTH_OPERATOR_ONLY` | 403 | This area is restricted to cash operators. | RN-071 |
| `AUTH_ACCOUNT_INACTIVE` | 403 | Your account is inactive. | RN-071 |
| `AUTH_ACCOUNT_LOCKED` | 423 | Your account is temporarily locked. | — |
| `AUTH_EMAIL_NOT_VERIFIED` | 403 | Email address is not verified. | — |
| `AUTH_PASSWORD_EXPIRED` | 403 | Password has expired; please reset. | — |
| `AUTH_STORE_ACCESS_DENIED` | 403 | You do not have access to this store. | RN-064 |
| `AUTH_STORE_CONTEXT_REQUIRED` | 422 | Store context must be selected. | RN-065 |
| `AUTH_SESSION_EXPIRED` | 401 | Session has expired. | — |
| `AUTH_TOO_MANY_ATTEMPTS` | 429 | Too many login attempts. Try again later. | — |

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/AuthErrorCodeTest.php`, `tests/Feature/Auth/*`.

## Store (`STORE_*`)

| Code | HTTP | Message | RN |
|------|------|---------|-----|
| `STORE_NOT_FOUND` | 404 | Store not found. | RN-064 |
| `STORE_CONTEXT_REQUIRED` | 422 | Store context must be selected. | RN-065 |
| `STORE_INACTIVE` | 403 | Store is inactive and cannot be selected. | RN-064 |
| `STORE_NOT_ASSIGNED` | 403 | Store is not assigned to this user. | RN-064 |

Related auth codes for store access: `AUTH_STORE_ACCESS_DENIED`, `AUTH_STORE_CONTEXT_REQUIRED`.

Implemented in `backend/app/Domain/Shared/ErrorCode.php`. Tests: `tests/Unit/Domain/Shared/StoreErrorCodeTest.php`, `tests/Feature/Store/*`.

---

## Implementation notes

1. **Single mapper:** `ErrorCode::toHttpResponse()` — one place for code → JSON shape
2. **Domain exceptions** carry `ErrorCode` only; no HTTP in domain layer (SRP)
3. **Logging:** internal context (sale id, store id) in structured logs; never leak stack to client
4. **i18n later:** message keys if needed; MVP English only

---

## Adding new errors

1. Add row here with RN link  
2. Add enum case  
3. Add test asserting message + code  
4. Never reuse codes for different semantics
