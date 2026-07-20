# PDV Frontend — Operational + Administrative SPA

Vite + React + TypeScript. Talks to Laravel API via Sanctum cookie session.

## Structure

```
src/
├── apps/operational/pages/       # Smart cashier routes
├── apps/administrative/pages/    # Smart admin routes
├── apps/administrative/guards/   # RequireManager
├── features/                     # hooks (smart) + ui (dumb)
│   ├── auth/
│   ├── admin/
│   ├── catalog/
│   └── pos/                      # context, hooks SRP, containers, components
└── shared/
    ├── api/
    ├── session/
    ├── lib/money.ts              # Decimal display helpers
    └── ui/                       # MoneyText, ErrorBanner, Field
```

Smart pages wire hooks → presentational components (SRP / reuse).

## Run locally

1. Backend (from `projects/pdv/backend`):

```bash
export PHP_INI_SCAN_DIR=php-conf.d
php artisan serve --host=127.0.0.1 --port=8000
```

2. Frontend:

```bash
cd projects/pdv/frontend
npm install
npm run dev
```

Open http://127.0.0.1:5173 — Vite proxies `/api` and `/sanctum` to `:8000`.

## Flow

**Operator:** Login → select store → open shift → POS  
**Manager:** Login → `/admin` (Dashboard / Catalog); “Open POS” for operational path

Money: API decimal strings; backend/DB integer cents (ADR-0005).

## Env notes

- `SESSION_DOMAIN=null`, `SESSION_SAME_SITE=lax`
- Same-site proxy avoids CORS for local SPA
