/**
 * PDV Screens Sync — Figma plugin
 * Tokens match frontend/src/index.css (DM Sans, dark theme, accent #3d9a7a).
 * Nav labels match AdminShell.tsx links.
 */

const C = {
  bg: { r: 0.059, g: 0.078, b: 0.098 }, // #0f1419
  elevated: { r: 0.102, g: 0.133, b: 0.173 }, // #1a222c
  soft: { r: 0.141, g: 0.188, b: 0.251 }, // #243040
  border: { r: 0.184, g: 0.239, b: 0.302 }, // #2f3d4d
  text: { r: 0.91, g: 0.933, b: 0.957 }, // #e8eef4
  muted: { r: 0.545, g: 0.604, b: 0.671 }, // #8b9aab
  accent: { r: 0.239, g: 0.604, b: 0.478 }, // #3d9a7a
  danger: { r: 0.831, g: 0.329, b: 0.29 }, // #d4544a
  warning: { r: 0.831, g: 0.627, b: 0.09 }, // #d4a017
  overlay: { r: 0, g: 0, b: 0 },
}

const ADMIN_NAV = [
  'Dashboard',
  'Analytics',
  'Catalog',
  'Sales',
  'Shifts',
  'Users',
  'Customers',
  'Promotions',
  'Inventory',
  'Refunds',
  'Audit log',
]

const W = 1440
const H = 900
const SIDEBAR_W = 240

/** @type {'DM Sans' | 'Inter'} */
let FONT_FAMILY = 'DM Sans'

figma.showUI(__html__, { width: 320, height: 200 })

figma.ui.onmessage = async (msg) => {
  if (msg.type !== 'sync') return
  try {
    const report = await syncAll()
    figma.ui.postMessage({ type: 'done', text: report })
  } catch (err) {
    figma.ui.postMessage({
      type: 'error',
      text: err && err.message ? err.message : String(err),
    })
  }
}

async function ensureFonts() {
  try {
    await figma.loadFontAsync({ family: 'DM Sans', style: 'Regular' })
    await figma.loadFontAsync({ family: 'DM Sans', style: 'Medium' })
    await figma.loadFontAsync({ family: 'DM Sans', style: 'SemiBold' })
    await figma.loadFontAsync({ family: 'DM Sans', style: 'Bold' })
    FONT_FAMILY = 'DM Sans'
  } catch (_) {
    await figma.loadFontAsync({ family: 'Inter', style: 'Regular' })
    await figma.loadFontAsync({ family: 'Inter', style: 'Medium' })
    await figma.loadFontAsync({ family: 'Inter', style: 'Semi Bold' })
    await figma.loadFontAsync({ family: 'Inter', style: 'Bold' })
    FONT_FAMILY = 'Inter'
  }
}

function useFamily() {
  return FONT_FAMILY
}

function solid(color, opacity) {
  const paint = { type: 'SOLID', color }
  if (opacity !== undefined && opacity < 1) paint.opacity = opacity
  return paint
}

function text(parent, chars, opts) {
  opts = opts || {}
  const t = figma.createText()
  const family = useFamily()
  let style = opts.style || 'Regular'
  if (family === 'Inter' && style === 'SemiBold') style = 'Semi Bold'
  t.fontName = { family, style }
  t.characters = chars
  t.fontSize = opts.size || 14
  t.fills = [solid(opts.color || C.text)]
  if (opts.align) t.textAlignHorizontal = opts.align
  if (opts.width) {
    t.textAutoResize = 'HEIGHT'
    t.resize(opts.width, 10)
  }
  parent.appendChild(t)
  if (opts.fill) t.layoutSizingHorizontal = 'FILL'
  return t
}

async function getOrCreatePage(name) {
  let page = figma.root.children.find((p) => p.name === name)
  if (!page) {
    page = figma.createPage()
    page.name = name
  }
  await figma.setCurrentPageAsync(page)
  return page
}

function nextX(page) {
  let maxX = 0
  for (const child of page.children) {
    maxX = Math.max(maxX, child.x + ('width' in child ? child.width : 0))
  }
  return maxX === 0 ? 0 : maxX + 80
}

function removeFrameNamed(page, name) {
  for (const child of [...page.children]) {
    if (child.name === name) child.remove()
  }
}

function panel(parent, name) {
  const f = figma.createAutoLayout('VERTICAL', {
    name,
    itemSpacing: 12,
    paddingTop: 16,
    paddingBottom: 16,
    paddingLeft: 16,
    paddingRight: 16,
  })
  f.fills = [solid(C.elevated)]
  f.strokes = [solid(C.border)]
  f.strokeWeight = 1
  f.cornerRadius = 10
  parent.appendChild(f)
  f.layoutSizingHorizontal = 'FILL'
  return f
}

function btn(parent, label, primary) {
  const b = figma.createAutoLayout('HORIZONTAL', {
    name: 'Btn/' + label,
    paddingTop: 10,
    paddingBottom: 10,
    paddingLeft: 16,
    paddingRight: 16,
  })
  b.cornerRadius = 8
  b.fills = [solid(primary ? C.accent : C.soft)]
  b.primaryAxisAlignItems = 'CENTER'
  b.counterAxisAlignItems = 'CENTER'
  text(b, label, { size: 13, style: 'SemiBold', color: C.text })
  parent.appendChild(b)
  return b
}

function input(parent, label, value) {
  const wrap = figma.createAutoLayout('VERTICAL', {
    name: 'Field/' + label,
    itemSpacing: 6,
  })
  text(wrap, label, { size: 12, color: C.muted, style: 'Medium' })
  const box = figma.createAutoLayout('HORIZONTAL', {
    paddingTop: 10,
    paddingBottom: 10,
    paddingLeft: 12,
    paddingRight: 12,
  })
  box.fills = [solid(C.soft)]
  box.strokes = [solid(C.border)]
  box.strokeWeight = 1
  box.cornerRadius = 8
  text(box, value, { size: 13, color: C.text })
  wrap.appendChild(box)
  box.layoutSizingHorizontal = 'FILL'
  parent.appendChild(wrap)
  wrap.layoutSizingHorizontal = 'FILL'
  return wrap
}

function table(parent, headers, rows) {
  const wrap = panel(parent, 'Table')
  wrap.itemSpacing = 0
  wrap.paddingTop = 0
  wrap.paddingBottom = 0
  wrap.paddingLeft = 0
  wrap.paddingRight = 0

  const head = figma.createAutoLayout('HORIZONTAL', {
    name: 'thead',
    paddingTop: 12,
    paddingBottom: 12,
    paddingLeft: 16,
    paddingRight: 16,
    itemSpacing: 12,
  })
  head.fills = [solid(C.soft)]
  headers.forEach((h) => {
    const cell = text(head, h, { size: 12, color: C.muted, style: 'SemiBold' })
    cell.layoutGrow = 1
  })
  wrap.appendChild(head)
  head.layoutSizingHorizontal = 'FILL'

  rows.forEach((row, i) => {
    const tr = figma.createAutoLayout('HORIZONTAL', {
      name: 'tr/' + i,
      paddingTop: 12,
      paddingBottom: 12,
      paddingLeft: 16,
      paddingRight: 16,
      itemSpacing: 12,
    })
    if (i % 2 === 1) tr.fills = [solid(C.bg, 0.35)]
    row.forEach((cell) => {
      const t = text(tr, cell, { size: 13, color: C.text })
      t.layoutGrow = 1
    })
    wrap.appendChild(tr)
    tr.layoutSizingHorizontal = 'FILL'
  })
  return wrap
}

function buildSidebar(parent, active) {
  const side = figma.createAutoLayout('VERTICAL', {
    name: 'Sidebar',
    itemSpacing: 4,
    paddingTop: 24,
    paddingBottom: 24,
    paddingLeft: 16,
    paddingRight: 16,
  })
  side.resize(SIDEBAR_W, H)
  side.fills = [solid(C.elevated)]
  side.strokes = [solid(C.border)]
  side.strokeWeight = 1
  side.primaryAxisAlignItems = 'MIN'
  side.layoutSizingVertical = 'FIXED'
  side.layoutSizingHorizontal = 'FIXED'

  text(side, 'PDV Admin', { size: 18, style: 'Bold', color: C.text })

  const nav = figma.createAutoLayout('VERTICAL', {
    name: 'Nav',
    itemSpacing: 2,
    paddingTop: 20,
  })
  nav.layoutGrow = 1

  ADMIN_NAV.forEach((label) => {
    const link = figma.createAutoLayout('HORIZONTAL', {
      name: 'Nav/' + label,
      paddingTop: 10,
      paddingBottom: 10,
      paddingLeft: 12,
      paddingRight: 12,
    })
    const isActive = label === active
    link.cornerRadius = 8
    link.fills = isActive ? [solid(C.accent, 0.22)] : []
    text(link, label, {
      size: 13,
      style: isActive ? 'SemiBold' : 'Regular',
      color: isActive ? C.accent : C.muted,
    })
    nav.appendChild(link)
    link.layoutSizingHorizontal = 'FILL'
  })
  side.appendChild(nav)
  nav.layoutSizingHorizontal = 'FILL'
  nav.layoutSizingVertical = 'FILL'

  const footer = figma.createAutoLayout('VERTICAL', {
    name: 'Footer',
    itemSpacing: 8,
    paddingTop: 16,
  })
  text(footer, 'Alex Manager · manager', { size: 12, color: C.muted })
  text(footer, 'Open POS', { size: 13, style: 'Medium', color: C.accent })
  btn(footer, 'Sign out', false)
  side.appendChild(footer)
  footer.layoutSizingHorizontal = 'FILL'

  parent.appendChild(side)
  return side
}

function buildAdminFrame(page, screenName, activeNav, fillMain) {
  removeFrameNamed(page, screenName)

  const root = figma.createAutoLayout('HORIZONTAL', {
    name: screenName,
    itemSpacing: 0,
  })
  root.resize(W, H)
  root.fills = [solid(C.bg)]
  root.clipsContent = true
  root.x = nextX(page)
  root.y = 0
  root.primaryAxisSizingMode = 'FIXED'
  root.counterAxisSizingMode = 'FIXED'

  buildSidebar(root, activeNav)

  const main = figma.createAutoLayout('VERTICAL', {
    name: 'Main',
    itemSpacing: 20,
    paddingTop: 32,
    paddingBottom: 32,
    paddingLeft: 32,
    paddingRight: 32,
  })
  main.fills = [solid(C.bg)]
  main.layoutGrow = 1
  root.appendChild(main)
  main.layoutSizingHorizontal = 'FILL'
  main.layoutSizingVertical = 'FILL'

  fillMain(main)

  page.appendChild(root)
  return root.id
}

function header(main, title, subtitle, actions) {
  const row = figma.createAutoLayout('HORIZONTAL', {
    name: 'Header',
    itemSpacing: 16,
  })
  row.primaryAxisAlignItems = 'SPACE_BETWEEN'
  row.counterAxisAlignItems = 'MIN'

  const left = figma.createAutoLayout('VERTICAL', { itemSpacing: 6 })
  text(left, title, { size: 28, style: 'Bold' })
  text(left, subtitle, { size: 14, color: C.muted })
  row.appendChild(left)

  if (actions && actions.length) {
    const acts = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 8 })
    actions.forEach((a) => btn(acts, a.label, a.primary))
    row.appendChild(acts)
  }

  main.appendChild(row)
  row.layoutSizingHorizontal = 'FILL'
  return row
}

function buildIdleSession(page) {
  removeFrameNamed(page, 'Idle session warning')

  const root = figma.createFrame()
  root.name = 'Idle session warning'
  root.resize(W, H)
  root.fills = [solid(C.bg)]
  root.x = nextX(page)
  root.y = 0
  root.clipsContent = true

  // Dimmed POS background hint
  const bgHint = figma.createAutoLayout('VERTICAL', {
    name: 'POS backdrop',
    paddingTop: 40,
    paddingLeft: 40,
    itemSpacing: 12,
  })
  bgHint.resize(W, H)
  bgHint.fills = [solid(C.bg)]
  text(bgHint, 'POS Checkout (dimmed)', { size: 20, style: 'Bold', color: C.muted })
  text(bgHint, 'Cart · Payment · Receipt', { size: 14, color: C.muted })
  root.appendChild(bgHint)
  bgHint.x = 0
  bgHint.y = 0

  const overlay = figma.createFrame()
  overlay.name = 'Overlay'
  overlay.resize(W, H)
  overlay.fills = [solid(C.overlay, 0.65)]
  root.appendChild(overlay)
  overlay.x = 0
  overlay.y = 0

  const dialog = figma.createAutoLayout('VERTICAL', {
    name: 'Idle dialog',
    itemSpacing: 16,
    paddingTop: 28,
    paddingBottom: 28,
    paddingLeft: 28,
    paddingRight: 28,
  })
  dialog.fills = [solid(C.elevated)]
  dialog.strokes = [solid(C.warning, 0.5)]
  dialog.strokeWeight = 1
  dialog.cornerRadius = 12
  dialog.resize(420, 10)
  dialog.primaryAxisSizingMode = 'AUTO'
  dialog.counterAxisSizingMode = 'FIXED'

  text(dialog, 'Session idle', { size: 20, style: 'Bold', color: C.warning })
  text(
    dialog,
    'No activity detected. You will be signed out after 15 minutes of idle time (shared POS lock). Move the mouse or press a key to stay signed in.',
    { size: 14, color: C.muted, width: 364 },
  )
  const actions = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 10 })
  btn(actions, 'Stay signed in', true)
  btn(actions, 'Sign out now', false)
  dialog.appendChild(actions)

  root.appendChild(dialog)
  dialog.x = (W - 420) / 2
  dialog.y = (H - 220) / 2

  page.appendChild(root)
  return root.id
}

async function syncAll() {
  await ensureFonts()

  const created = []

  const ops = await getOrCreatePage('01 — Operational')
  created.push('Idle: ' + buildIdleSession(ops))

  const admin = await getOrCreatePage('02 — Administrative')

  created.push(
    'Dashboard: ' +
      buildAdminFrame(admin, 'Admin — Dashboard', 'Dashboard', (main) => {
        header(main, 'Dashboard', 'Manager overview — store ops at a glance.')
        const kpis = figma.createAutoLayout('HORIZONTAL', {
          name: 'KPIs',
          itemSpacing: 12,
          layoutWrap: 'WRAP',
        })
        ;[
          ['Access', 'Admin', 'Manager session'],
          ['Products', '128', '112 active'],
          ['Inactive', '16', 'Catalog'],
          ['Customers', '1 240', 'Your stores'],
          ['Sales completed', '86', 'Today'],
          ['Open shifts', '3', 'Your stores'],
        ].forEach(([label, value, em]) => {
          const card = panel(kpis, 'KPI/' + label)
          card.resize(180, 10)
          card.layoutSizingHorizontal = 'FIXED'
          text(card, label, { size: 12, color: C.muted })
          text(card, value, { size: 22, style: 'Bold' })
          text(card, em, { size: 12, color: C.muted })
        })
        main.appendChild(kpis)
        kpis.layoutSizingHorizontal = 'FILL'

        const activity = panel(main, 'Recent activity')
        text(activity, 'Recent activity', { size: 16, style: 'SemiBold' })
        ;[
          'Sale #4821 completed · R$ 189,90',
          'Stock adjust · SKU COFFEE-250',
          'Refund #91 approved · R$ 45,00',
        ].forEach((line) => text(activity, line, { size: 13, color: C.muted }))
      }),
  )

  created.push(
    'Analytics: ' +
      buildAdminFrame(admin, 'Admin — Analytics', 'Analytics', (main) => {
        header(
          main,
          'Analytics',
          'Registrations over time, recurrence index, spend by store, birthday and regional campaign filters (RN-080–084).',
        )
        const row = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 16 })
        const left = panel(row, 'Recurrence')
        text(left, 'Recurrence (RN-081)', { size: 16, style: 'SemiBold' })
        text(left, 'Index 1.42 · 318 repeat / 890 with purchases', { size: 13, color: C.muted })
        const right = panel(row, 'Spend by store')
        text(right, 'Spend by store (RN-082)', { size: 16, style: 'SemiBold' })
        text(right, 'Downtown R$ 42.1k · Airport R$ 28.4k', { size: 13, color: C.muted })
        main.appendChild(row)
        row.layoutSizingHorizontal = 'FILL'
        left.layoutSizingHorizontal = 'FILL'
        right.layoutSizingHorizontal = 'FILL'

        table(main, ['Date', 'Registrations'], [
          ['2026-07-18', '12'],
          ['2026-07-17', '9'],
          ['2026-07-16', '15'],
        ])

        const campaign = panel(main, 'Campaign filters')
        text(campaign, 'Campaign filters (RN-083/084)', { size: 16, style: 'SemiBold' })
        const filters = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 12 })
        input(filters, 'Birth month', '7')
        input(filters, 'Region', 'SP')
        btn(filters, 'Run filter', true)
        campaign.appendChild(filters)
        filters.layoutSizingHorizontal = 'FILL'
        text(campaign, 'Matched customers: 24', { size: 13, color: C.muted })
      }),
  )

  created.push(
    'Catalog: ' +
      buildAdminFrame(admin, 'Admin — Catalog', 'Catalog', (main) => {
        header(main, 'Catalog', 'Products, prices, and active status.', [
          { label: 'New product', primary: true },
        ])
        table(
          main,
          ['SKU', 'Name', 'Price', 'Status'],
          [
            ['COFFEE-250', 'Coffee 250g', 'R$ 28,90', 'Active'],
            ['TEA-100', 'Green tea 100g', 'R$ 16,50', 'Active'],
            ['MUG-01', 'Ceramic mug', 'R$ 39,00', 'Inactive'],
          ],
        )
      }),
  )

  created.push(
    'Sales: ' +
      buildAdminFrame(admin, 'Admin — Sales', 'Sales', (main) => {
        header(
          main,
          'Sales',
          'Filter completed sales by period, assigned store, operator, customer, payment (RN-061/064).',
          [{ label: 'Refresh payments', primary: false }],
        )
        const filters = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 12 })
        input(filters, 'From', '2026-07-01')
        input(filters, 'To', '2026-07-19')
        input(filters, 'Store', 'Downtown')
        input(filters, 'Payment', 'Any')
        btn(filters, 'Apply', true)
        btn(filters, 'Clear', false)
        main.appendChild(filters)
        filters.layoutSizingHorizontal = 'FILL'
        table(
          main,
          ['Sale', 'Store', 'Operator', 'Total', 'Payment', 'When'],
          [
            ['#4821', 'Downtown', 'Ana', 'R$ 189,90', 'Card', '19 Jul 14:22'],
            ['#4820', 'Airport', 'João', 'R$ 54,00', 'Cash', '19 Jul 13:01'],
            ['#4819', 'Downtown', 'Ana', 'R$ 312,40', 'PIX', '19 Jul 11:48'],
          ],
        )
      }),
  )

  created.push(
    'Shifts: ' +
      buildAdminFrame(admin, 'Admin — Shifts', 'Shifts', (main) => {
        header(
          main,
          'Shifts',
          'Closing reports per store — sales totals, payment mix, cash variance. Managers may reopen closed shifts (RN-003/004/063).',
        )
        const top = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 12 })
        input(top, 'Store', 'Downtown')
        btn(top, 'Load shifts', true)
        main.appendChild(top)
        top.layoutSizingHorizontal = 'FILL'

        const split = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 16 })
        const list = panel(split, 'Shift list')
        text(list, 'Shifts', { size: 16, style: 'SemiBold' })
        ;['#88 Open · Ana · opened 08:02', '#87 Closed · João · −R$ 2,00', '#86 Closed · Ana · +R$ 0,50'].forEach(
          (l) => text(list, l, { size: 13, color: C.muted }),
        )
        const report = panel(split, 'Closing report')
        text(report, 'Closing report #87', { size: 16, style: 'SemiBold' })
        text(report, 'Sales R$ 4.820,00 · Cash R$ 1.200 · Card R$ 2.800 · PIX R$ 820', {
          size: 13,
          color: C.muted,
        })
        text(report, 'Expected cash R$ 1.300 · Counted R$ 1.298 · Variance −R$ 2,00', {
          size: 13,
          color: C.warning,
        })
        btn(report, 'Reopen shift', false)
        main.appendChild(split)
        split.layoutSizingHorizontal = 'FILL'
        list.layoutSizingHorizontal = 'FILL'
        report.layoutSizingHorizontal = 'FILL'
      }),
  )

  created.push(
    'Users: ' +
      buildAdminFrame(admin, 'Admin — Users', 'Users', (main) => {
        header(
          main,
          'Users',
          'Manage operators and managers, roles, status, store access, and MFA reset (RN-062 / RN-074).',
          [{ label: 'New user', primary: true }],
        )
        const search = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 12 })
        input(search, 'Search', 'ana@')
        btn(search, 'Search', true)
        main.appendChild(search)
        search.layoutSizingHorizontal = 'FILL'
        table(
          main,
          ['Name', 'Email', 'Role', 'Status', 'Stores', 'MFA'],
          [
            ['Ana Operadora', 'ana@pos.test', 'operator', 'Active', 'Downtown', 'On'],
            ['João Operador', 'joao@pos.test', 'operator', 'Active', 'Airport', 'Off'],
            ['Alex Manager', 'manager@pos.test', 'manager', 'Active', 'All', 'On'],
          ],
        )
        const form = panel(main, 'Edit user')
        text(form, 'Edit user', { size: 16, style: 'SemiBold' })
        const fields = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 12 })
        input(fields, 'Name', 'Ana Operadora')
        input(fields, 'Role', 'operator')
        input(fields, 'Stores', 'Downtown')
        form.appendChild(fields)
        fields.layoutSizingHorizontal = 'FILL'
        const acts = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 8 })
        btn(acts, 'Save', true)
        btn(acts, 'Reset MFA', false)
        form.appendChild(acts)
      }),
  )

  created.push(
    'Customers: ' +
      buildAdminFrame(admin, 'Admin — Customers', 'Customers', (main) => {
        header(main, 'Customers', 'Customer registry for assigned stores.', [
          { label: 'New customer', primary: true },
        ])
        table(
          main,
          ['Name', 'Document', 'Phone', 'Region', 'Purchases'],
          [
            ['Maria Silva', '***.***.***-12', '(11) 9****-1234', 'SP', '8'],
            ['Pedro Lima', '***.***.***-45', '(21) 9****-5678', 'RJ', '2'],
          ],
        )
      }),
  )

  created.push(
    'Promotions: ' +
      buildAdminFrame(admin, 'Admin — Promotions', 'Promotions', (main) => {
        header(main, 'Promotions', 'Percent / fixed / combo rules with audit trail.', [
          { label: 'New promotion', primary: true },
        ])
        table(
          main,
          ['Name', 'Type', 'Value', 'Window', 'Status'],
          [
            ['Weekend 10%', 'percent', '10%', 'Fri–Sun', 'Active'],
            ['Mug + Coffee', 'combo', '−R$ 10', 'Jul', 'Active'],
            ['Clearance tea', 'fixed', '−R$ 5', 'Ended', 'Inactive'],
          ],
        )
      }),
  )

  created.push(
    'Inventory: ' +
      buildAdminFrame(admin, 'Admin — Inventory', 'Inventory', (main) => {
        header(main, 'Inventory', 'Per-store stock levels and adjustments (RN-040+).')
        const top = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 12 })
        input(top, 'Store', 'Downtown')
        btn(top, 'Load', true)
        main.appendChild(top)
        top.layoutSizingHorizontal = 'FILL'
        table(
          main,
          ['SKU', 'On hand', 'Reserved', 'Available'],
          [
            ['COFFEE-250', '42', '2', '40'],
            ['TEA-100', '18', '0', '18'],
            ['MUG-01', '7', '1', '6'],
          ],
        )
        const adj = panel(main, 'Adjust')
        text(adj, 'Stock adjust', { size: 16, style: 'SemiBold' })
        const row = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 12 })
        input(row, 'SKU', 'COFFEE-250')
        input(row, 'Delta', '-2')
        input(row, 'Reason', 'Breakage')
        btn(row, 'Apply adjust', true)
        adj.appendChild(row)
        row.layoutSizingHorizontal = 'FILL'
      }),
  )

  created.push(
    'Refunds: ' +
      buildAdminFrame(admin, 'Admin — Refunds', 'Refunds', (main) => {
        header(main, 'Refunds', 'Returns and refunds against completed sales.')
        table(
          main,
          ['Refund', 'Sale', 'Amount', 'Reason', 'Status', 'When'],
          [
            ['#91', '#4810', 'R$ 45,00', 'Defective', 'Approved', '18 Jul'],
            ['#90', '#4802', 'R$ 28,90', 'Customer change', 'Approved', '17 Jul'],
          ],
        )
      }),
  )

  created.push(
    'Audit: ' +
      buildAdminFrame(admin, 'Admin — Audit log', 'Audit log', (main) => {
        header(
          main,
          'Audit log',
          'Sensitive actions: price changes, stock adjusts, refunds/returns, promotion management (RN-070).',
        )
        const filters = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 12 })
        input(filters, 'Action', 'inventory.adjust')
        input(filters, 'Store', 'Downtown')
        input(filters, 'From', '2026-07-01')
        btn(filters, 'Apply', true)
        btn(filters, 'Clear', false)
        main.appendChild(filters)
        filters.layoutSizingHorizontal = 'FILL'
        table(
          main,
          ['When', 'Actor', 'Action', 'Entity', 'Store'],
          [
            ['19 Jul 14:01', 'Alex Manager', 'catalog.price_update', 'product:12', '—'],
            ['19 Jul 13:40', 'Alex Manager', 'inventory.adjust', 'sku:COFFEE-250', 'Downtown'],
            ['18 Jul 16:12', 'Alex Manager', 'refund.create', 'refund:91', 'Downtown'],
            ['18 Jul 11:05', 'Alex Manager', 'promotion.update', 'promo:3', '—'],
          ],
        )
      }),
  )

  return (
    'Synced frames on 01 — Operational + 02 — Administrative:\n' +
    created.map((c) => '• ' + c.split(':')[0]).join('\n') +
    '\n\nOpen those pages in the file. Re-run replaces frames with the same names.'
  )
}
