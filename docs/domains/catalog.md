# Catalog Domain

> Bounded context documentation. Link business rules by `RN-XXX`.

## Responsibility

Global product catalog (SKU, name, base price). Admin updates are manager-only; price changes are audited when `base_price` actually changes (RN-070).

## Ubiquitous language

| Term | Definition |
|------|------------|
| **Product** | Sellable item with SKU and `base_price` |
| **Base price** | Catalog unit price before promotions |

## Related business rules

| RN | Summary |
|----|---------|
| RN-040 | Base price admin-only |
| RN-070 | Effective price change → `catalog.product.price_changed` |

## Notes

- Snake case key: `catalog`
- Initial create price is **not** audited; only subsequent effective changes
- Audit detail: `docs/domains/audit.md`
