# Payments Domain

> Bounded context. Rules: RN-050–RN-054.

## Responsibility

Payment lines on a sale, gateway charge/refund abstraction (**SOAP outbound** to the acquirer), and **asynchronous confirmation** via signed REST provider webhooks.

## Ubiquitous language

| Term | Definition |
|------|------------|
| Payment line | Amount + method recorded against a sale (`pending` \| `confirmed` \| `failed`) |
| Transaction reference | Gateway id linking charge intent ↔ webhook |
| Webhook event | Idempotent provider callback (`provider` + `provider_event_id` unique) |

## Related business rules

| RN | Summary |
|----|---------|
| RN-050 | Completed sale needs ≥1 payment line |
| RN-051 | Payments sum equals sale total (± R$ 0.01) |
| RN-052 | Methods supported; MVP charge may be stubbed |
| RN-053 | Cash: received + change |
| RN-054 | Gateways behind interface; SOAP transport for acquirer; REST for app + webhooks |

## Transport

| Direction | Protocol | Component |
|-----------|----------|-----------|
| POS/Admin ↔ API | REST (JSON) | Laravel routes |
| Acquirer charge/refund | **SOAP** | `SoapPaymentGateway` (`PAYMENTS_SOAP_MODE=stub\|live`) |
| Acquirer → PDV confirmation | REST + HMAC | `POST /api/webhooks/payments/{provider}` |

See [ADR-0009](../adr/0009-soap-payment-acquirer-rest-elsewhere.md).

## Async settlement resilience (A+B)

| Mechanism | Role |
|-----------|------|
| **A — Outbox** | Non-cash charge → `payment_lines.status=pending` + Redis/array outbox keyed by `transaction_reference` |
| **Webhook** | Preferred path: HMAC REST callback confirms/fails the line and clears outbox |
| **B — Retry queue** | If webhook arrives before the line exists (`PAY_WEBHOOK_UNKNOWN_REFERENCE`), payload is queued for retry |
| **Cron** | `payments:reconcile` hourly — drains retry queue + polls `PaymentGatewayInterface::queryChargeStatus` |
| **Force refresh** | `POST /api/operational/payments/reconcile` (store-scoped) and `POST /api/admin/payments/reconcile` (all) |

Cash remains sync `confirmed`. Driver: `PAYMENTS_RECONCILE_DRIVER=redis|array`.

## API

| Method | Path | Auth | Notes |
|--------|------|------|-------|
| POST | `/api/webhooks/payments/{provider}` | HMAC signature (no session) | `stub`, `mercadopago`, `stripe` allowlisted; CSRF exempt |
| POST | `/api/operational/payments/reconcile` | Operator/Manager + store | Force refresh pending + webhook retries |
| POST | `/api/admin/payments/reconcile` | Manager | Force refresh (all stores) |

### Webhook contract (normalized JSON)

```json
{
  "event_id": "evt_123",
  "type": "payment.confirmed",
  "transaction_reference": "stub-abc",
  "amount": 1500
}
```

- `type`: `payment.confirmed` \| `payment.failed`
- `amount`: optional cents; when present must match the payment line
- Header: `X-Payment-Webhook-Signature: sha256=<hmac_hex(raw_body, PAYMENT_WEBHOOK_SECRET)>`
- Duplicate `event_id` → HTTP 200 `{ duplicate: true }` (idempotent)
- First successful apply → HTTP 202

### Flow

1. Verify HMAC (`PaymentWebhookSignatureVerifierInterface`)
2. Normalize payload (`PaymentWebhookPayloadNormalizerInterface`)
3. Persist `payment_webhook_events` (unique provider+event_id)
4. Transition payment line by `transaction_reference` inside the same DB transaction

Cash is sync `confirmed` on complete-sale. Non-cash charges create `pending` lines + outbox; webhooks confirm them. If the webhook is late/missing, hourly `payments:reconcile` (or UI **Refresh payments**) polls the gateway and retries queued inbound payloads.

### Card payments (debit / credit)

Complete-sale payload requires `payments[].card`:

| Field | Role |
|-------|------|
| `holder_name` | Name printed on card |
| `number` | PAN (validated locally with Luhn; **not stored**) |
| `exp_month` / `exp_year` | Expiry |
| `indicated_person_name` | Person who should own the card |
| `belongs_to_indicated_person` | Operator confirmation flag |

Flow: `CardInstrumentFormatGuard` (local) → `CardInstrumentValidatorInterface` (acquirer). Bound implementation today: `NotImplementedCardInstrumentValidator` → HTTP **501** `PAY_METHOD_NOT_IMPLEMENTED` / *"Payment method is not implemented."* Swap the binding when the live gateway verifies the instrument.

## Errors

See `docs/errors.md` (`PAY_*`, `PAY_WEBHOOK_*`, `PAY_CARD_*`).
