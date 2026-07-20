# Business Rules — POS (PDV)

> **Project:** `projects/pdv`  
> **Status:** Step 1 complete — business specification (source for TDD/BDD)  
> **Last updated:** 2026-07-15  
> **Target stack:** Laravel 13, PHP 8.5, React — monolithic MVC + Clean Architecture + DDD  
> **Language:** All system artifacts (code, errors, domain terms, docs) in **English**  
> **Context economy:** Reference rules by `RN-XXX` — see `.cursor/rules/05-sumarizacao.mdc`

This document is the **source of truth for business rules**. BDD scenarios and TDD tests must trace back to `RN-XXX`.

---

## 1. Product purpose

**Point of Sale (POS)** for in-store checkout, catalog/inventory control, and managerial oversight across **multiple stores**.

| View | Typical user | Goal |
|------|--------------|------|
| **Operational** | Cashier / operator | Fast checkout: customer CPF, coupons, price lookup, cart view/close, line cancel |
| **Administrative** | Manager | Store setup, users, products, stock, promotions/coupons, sales metrics, dashboards, commercial policies |

---

## 2. Ubiquitous language (glossary)

| Term | Definition |
|------|------------|
| **Sale** | Closed transaction: line items, amounts, payments, operator, store, fiscal receipt |
| **Sale line** | One product line: SKU, qty, unit price applied, line discounts |
| **Cash shift** | Operator work session; every sale belongs to an open shift at one **store** |
| **Customer** | Registered person; optional on walk-in sale |
| **Recurring customer** | Customer with purchase history; **no automatic discount** — used for analytics only unless a **promotion** applies |
| **Promotion** | Discount/campaign created by Manager, scoped to customer(s) or all customers |
| **Fiscal receipt** | Document generated on every completed sale (cupom fiscal) |
| **Refund** | Money returned without necessarily returning goods (full or partial) |
| **Return** | Goods brought back; may trigger full or partial refund |
| **Store** | Physical branch; scopes inventory, shifts, sales, and operator default context |

---

## 3. Actors and permissions

| Actor | View | Can | Cannot |
|-------|------|-----|--------|
| **Operator** | Operational | Open/close shift, run sales, apply allowed coupons/promotions, identify customer, partial line cancel | Change base price, delete products, full admin reports, create promotions |
| **Manager** | Admin + Operational | Everything Operator can + catalog, users, promotions, stock, dashboards, campaigns | — |

**RBAC:** Only **Operator** and **Manager** roles in MVP.

---

## 4. Business rules

### 4.1 Cash shift

| ID | Rule | Priority |
|----|------|----------|
| **RN-001** | No sale without an **open cash shift** for the authenticated operator at the current store. | Required |
| **RN-002** | Operator may have **only one open shift** at a time. | Required |
| **RN-003** | Closing a shift consolidates: sale totals, totals per payment method, cash variance if manual count provided. | Required |
| **RN-004** | Reopening a closed shift requires **Manager** authorization and is audited. | Required |

### 4.2 Sale (operational flow)

| ID | Rule | Priority |
|----|------|----------|
| **RN-010** | In-progress sale: add items, change qty, remove lines **before** completion. | Required |
| **RN-011** | Completed sale is immutable; corrections via **refund** and/or **return** (full or partial). | Required |
| **RN-012** | Completed sale records: store, operator, shift, timestamp, lines, subtotal, discounts, total, payments. | Required |
| **RN-013** | Sale may complete **without** identified customer (walk-in). | Required |
| **RN-014** | Operator may **park/hold** a cart with optional label for later retrieval (same store, same shift). | Required |
| **RN-015** | Every completed sale **must generate a fiscal receipt** (`FiscalReceipt`). | Required |

### 4.3 Refunds and returns

| ID | Rule | Priority |
|----|------|----------|
| **RN-016** | **Full refund**: reverse entire sale amount and linked stock/payment records per policy. | Required |
| **RN-017** | **Partial refund**: reverse a defined subset of lines or amount; remainder stays valid. | Required |
| **RN-018** | **Full return**: all items returned; triggers full refund when applicable. | Required |
| **RN-019** | **Partial return**: subset of items; stock and refund adjusted proportionally. | Required |
| **RN-019a** | Refund/return actions require audit trail (who, when, reason, original sale id). | Required |

### 4.4 Product and inventory

| ID | Rule | Priority |
|----|------|----------|
| **RN-020** | **Inactive** product cannot be added to new sales. | Required |
| **RN-021** | When stock control is on, qty sold cannot exceed **available stock** at that **store**. | Required |
| **RN-022** | Stock decrement on **sale completion**, not on cart add. | Required |
| **RN-023** | Manual stock adjustment is **administrative**; **reason is mandatory** and audited. | Required |

### 4.5 Customer

| ID | Rule | Priority |
|----|------|----------|
| **RN-030** | Identified customer must exist in registry. | Required |
| **RN-031** | Required fields: **name**, **email**, **CPF**, **phone**, **birth_date**, **address** (supports birthday and regional campaigns). | Required |
| **RN-032** | CPF must be unique in the system. | Required |
| **RN-033** | Purchase **history per store** and **lifetime total spend** are kept for analytics. | Required |

### 4.6 Prices, promotions, discounts

| ID | Rule | Priority |
|----|------|----------|
| **RN-040** | Base product price is set in **admin** view; Operator cannot change base price. | Required |
| **RN-041** | Line-level discount: only via **valid coupon/promotion** or Manager-defined rules. | Required |
| **RN-042** | Sale-level discount: only via **valid coupon/promotion** assigned by Manager. | Required |
| **RN-043** | **No automatic discount** for recurring customers; recurrence affects **dashboards only**. | Required |
| **RN-044** | Discounts apply **only** when Manager assigns a **promotion** to specific customer(s) or **all customers**. | Required |
| **RN-045** | Promotions are **never auto-generated**; Manager creates and enables them explicitly. | Required |
| **RN-046** | Each promotion has **stacking mode** chosen by Manager: **unique** (exclusive — blocks any other promotion on the sale) or **accumulable** (stacks only with other accumulable promotions). Unique and accumulable **cannot** combine on the same sale. | Required |
| **RN-047** | Final sale total **never negative**. | Required |

### 4.7 Payment

| ID | Rule | Priority |
|----|------|----------|
| **RN-050** | Completed sale requires **at least one** payment line. | Required |
| **RN-051** | Sum of payments equals sale total (tolerance: **R$ 0.01**). | Required |
| **RN-052** | MVP supports **all methods**: cash, PIX, debit card, credit card, voucher, store credit, others — **simulated only** (no real charge). | Required |
| **RN-053** | Cash payment: record amount received; change calculated automatically. | Required |
| **RN-054** | Payment gateways are behind a **repository interface**; swap stub for real provider without domain changes. | Required |

### 4.8 Administrative view and multi-store

| ID | Rule | Priority |
|----|------|----------|
| **RN-060** | Manager CRUD: products, categories, promotions, coupons. | Required |
| **RN-061** | Manager filters sales by period, store, operator, customer, payment method. | Required |
| **RN-062** | Manager manages users and roles (Operator, Manager). | Required |
| **RN-063** | Shift closing report available in admin view per store. | Required |
| **RN-064** | **Multi-store** enabled: inventory, shifts, sales scoped per store; Manager may access all assigned stores. | Required |
| **RN-065** | Operator works in **one store context** per session (selected or assigned). | Required |

### 4.9 Analytics and campaigns

| ID | Rule | Priority |
|----|------|----------|
| **RN-080** | Dashboard: **new customer registrations** over time. | Required |
| **RN-081** | Dashboard: **recurrence index** (repeat purchase rate). | Required |
| **RN-082** | Dashboard: spend **per store** and **total** per customer. | Required |
| **RN-083** | **Birthday campaigns**: filter customers by `birth_date`. | Required |
| **RN-084** | **Regional campaigns**: filter customers by **address** region. | Required |

### 4.10 Security and audit

| ID | Rule | Priority |
|----|------|----------|
| **RN-070** | Sensitive actions log **who**, **when**, and before/after: effective **price change**, admin **stock adjust**, **refund/return**, and promotion **create/update** (incl. activation/assignments). Append-only `audit_logs`; audit write failure rolls back the mutation. Initial product price and applying/removing a promotion on a sale are **not** audited. Managers query via `GET /api/admin/audit-logs` with RN-064 scope (assigned stores + global `store_id=null`). | Required |
| **RN-071** | Operator: operational UI only; Manager: both views. | Required |

---

## 5. BDD scenarios (draft)

### Feature: Manager-assigned promotion

```gherkin
Feature: Promotion applied only when assigned by manager
  # RN-043, RN-044, RN-045, RN-047

  Scenario: Recurring customer without promotion pays full price
    Given customer "Maria" with 10 prior purchases
    And no active promotion for "Maria"
    When operator completes a sale for "Maria"
    Then no automatic recurrence discount is applied

  Scenario: Customer receives manager promotion
    Given manager created promotion "SUMMER10" for customer "Maria"
    When operator applies "SUMMER10" at checkout
    Then sale total reflects promotion discount

  Scenario: Unique promotion blocks a second promotion
    Given an active unique promotion on the sale
    When operator tries to apply any other promotion
    Then the system rejects with PROMO_NOT_COMBINABLE

  Scenario: Accumulable promotions stack together
    Given two active accumulable promotions assigned to the customer
    When operator applies both at checkout
    Then sale total reflects the combined discount
```

### Feature: Fiscal receipt on sale

```gherkin
Feature: Fiscal receipt generation
  # RN-015

  Scenario: Completed sale generates receipt
    Given an open shift and a valid cart
    When operator completes the sale
    Then a fiscal receipt is created and linked to the sale
```

### Feature: Partial refund

```gherkin
Feature: Partial refund
  # RN-011, RN-017, RN-019a

  Scenario: Manager partial refund on one line
    Given a completed sale with lines A and B
    When manager authorizes partial refund for line A
    Then refund amount matches line A
    And line B remains sold
    And audit log records the action
```

### Feature: Immutable audit trail

```gherkin
Feature: Immutable audit trail
  # RN-070, RN-064

  Scenario: Price change is audited; rename-only is not
    Given a product with base price 10.00
    When manager changes base price to 12.50
    Then an audit log with action catalog.product.price_changed exists
    When manager only renames the product
    Then no additional price-change audit log is created

  Scenario: Manager cannot filter audit by unassigned store
    Given manager assigned only to store A
    When manager lists audit logs with store_id of store B
    Then response is 403 with code AUTH_STORE_ACCESS_DENIED
```

---

## 6. Out of scope (MVP)

| Item | Reason |
|------|--------|
| Real payment capture (acquirer/gateway live) | Stub via repository until launch |
| NF-e / government tax integration | Fiscal receipt is internal document for MVP |
| E-commerce channel | In-store POS only |
| Roles beyond Operator / Manager | Deferred |

---

## 7. Open decisions

_None — RN-046 resolved (unique vs accumulable per promotion, set by Manager)._

---

## 8. Traceability

| Future artifact | Source |
|-----------------|--------|
| `tests/Unit/` | Domain rules `RN-*` |
| `tests/BDD/` | §5 scenarios |
| `docs/architecture.md` | Layers, bounded contexts, repositories |
| `docs/errors.md` | Domain/application error catalog |
| `docs/security.md` | Threat model and controls |
