# PDV Screens Sync (Figma plugin)

Local plugin for [PDV — Screens](https://www.figma.com/design/UhplgsORC4T0QVq0Zvwyez) when Figma MCP quota is exhausted.

## Design model (one board)

**One page:** `PDV — All views (Desktop + Mobile)`

Each vision is **one composition** with breakpoints side by side:

| Breakpoint | Size | Shell |
|------------|------|--------|
| Desktop | 1440×900 | Admin: 240px sidebar · POS: catalog + cart |
| Mobile | 390×844 | Admin: chip nav (matches `@media max-width 900px`) · POS: stacked |

### Covered views

**Operational:** Login (+ Turnstile), Store select, Open shift, POS Checkout, Idle session  

**Administrative:** Dashboard, Analytics, Catalog, Sales, Shifts, Users, Customers, Promotions, Inventory, Refunds, Audit log  

Tokens: `frontend/src/index.css` (dark, `#3d9a7a`, DM Sans → Inter fallback).

## How to run

1. Open the Figma file.
2. **Plugins → Development → Import plugin from manifest…** (or reload if already imported).
3. Select `design/figma-sync-screens/manifest.json`.
4. Run **PDV Screens Sync** → **Sync responsive board**.
5. Open page **`PDV — All views (Desktop + Mobile)`**.

Re-run **clears and rebuilds** that page only (other pages untouched).
