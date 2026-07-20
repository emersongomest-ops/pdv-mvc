# PDV Screens Sync (Figma plugin)

Local plugin that fills the [PDV — Screens](https://www.figma.com/design/UhplgsORC4T0QVq0Zvwyez) file when **Figma MCP quota is exhausted**.

## What it creates

| Page | Frames |
|------|--------|
| `01 — Operational` | **Idle session warning** (dialog over dimmed POS) |
| `02 — Administrative` | Full sidebar nav (matches `AdminShell.tsx`) on: Dashboard, Analytics, Catalog, Sales, Shifts, Users, Customers, Promotions, Inventory, Refunds, Audit log |

Tokens: dark theme from `frontend/src/index.css` (`#0f1419`, accent `#3d9a7a`, DM Sans with Inter fallback).

## How to run

1. Open the Figma file **PDV — Screens** (`UhplgsORC4T0QVq0Zvwyez`).
2. Menu **Plugins → Development → Import plugin from manifest…**
3. Select `projects/pdv/design/figma-sync-screens/manifest.json`.
4. Run **PDV Screens Sync** → click **Sync all screens**.
5. Switch to pages `01 — Operational` and `02 — Administrative`.

Re-running **replaces** frames with the same names (safe to iterate).

## Why not MCP?

Account is **Full seat on Starter**. MCP daily/monthly call budget was hit (`use_figma` / `generate_figma_design` both blocked). After quota resets or after upgrading Figma plan, the agent can refine via MCP again.

Upgrade link from Figma: team MCP paywall on the Emerson Gomes team.
