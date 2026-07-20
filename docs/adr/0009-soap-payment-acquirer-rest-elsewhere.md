# ADR-0009 — SOAP outbound for payment acquirer; REST elsewhere

- **Status:** Accepted
- **Date:** 2026-07-20
- **Context:** Some acquirers expose SOAP/WSDL for capture and refund. Mixing SOAP into the POS SPA API would slow iteration and break Sanctum/JSON conventions. Asynchronous settlement still arrives as HTTP callbacks.

- **Decision:**
  - **SOAP** only for outbound `PaymentGatewayInterface` (`SoapPaymentGateway`: charge/refund).
  - **REST** for all PDV HTTP APIs and `POST /api/webhooks/payments/{provider}` (HMAC).
  - Default `PAYMENTS_SOAP_MODE=stub` builds/validates envelopes locally (XXE-safe `DOMDocument` + `LIBXML_NONET`) without network I/O.
  - Live mode uses `SoapClient` against `PAYMENTS_SOAP_WSDL` / `PAYMENTS_SOAP_ENDPOINT`.
  - Non-cash settlement is async: Redis/array **outbox** + webhook retry queue; `payments:reconcile` runs **hourly**; operators/admins can force refresh via API.

- **Consequences:**
  - Latency on live charge is higher than REST SDKs; acceptable for acquirer constraint.
  - Card issuer verification remains behind `CardInstrumentValidatorInterface` (not implemented until WSDL operation exists).
  - Stub `StubPaymentGateway` retained in codebase for reference but DI binds `SoapPaymentGateway`.
  - Webhook is preferred; reconcile is the safety net when callbacks are delayed or the endpoint was briefly unavailable.
