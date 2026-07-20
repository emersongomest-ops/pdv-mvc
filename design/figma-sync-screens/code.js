/**
 * PDV Screens Sync — one responsive system
 * One page. Each view = Desktop (1440) + Mobile (390) pair.
 * Tokens: frontend/src/index.css · Nav: AdminShell.tsx
 */

const C = {
  bg: { r: 0.059, g: 0.078, b: 0.098 },
  elevated: { r: 0.102, g: 0.133, b: 0.173 },
  soft: { r: 0.141, g: 0.188, b: 0.251 },
  border: { r: 0.184, g: 0.239, b: 0.302 },
  text: { r: 0.91, g: 0.933, b: 0.957 },
  muted: { r: 0.545, g: 0.604, b: 0.671 },
  accent: { r: 0.239, g: 0.604, b: 0.478 },
  danger: { r: 0.831, g: 0.329, b: 0.29 },
  warning: { r: 0.831, g: 0.627, b: 0.09 },
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

const DESKTOP = { w: 1440, h: 900 }
const MOBILE = { w: 390, h: 844 }

/** @type {'DM Sans' | 'Inter'} */
let FONT_FAMILY = 'DM Sans'

const PAGE_NAME = 'PDV — All views (Desktop + Mobile)'

figma.showUI(__html__, { width: 340, height: 220 })

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

function styleName(style) {
  if (FONT_FAMILY === 'Inter' && style === 'SemiBold') return 'Semi Bold'
  return style
}

function solid(color, opacity) {
  const paint = { type: 'SOLID', color }
  if (opacity !== undefined && opacity < 1) paint.opacity = opacity
  return paint
}

function text(parent, chars, opts) {
  opts = opts || {}
  const t = figma.createText()
  t.fontName = { family: FONT_FAMILY, style: styleName(opts.style || 'Regular') }
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

function btn(parent, label, primary) {
  const b = figma.createAutoLayout('HORIZONTAL', {
    name: 'Btn/' + label,
    paddingTop: 10,
    paddingBottom: 10,
    paddingLeft: 14,
    paddingRight: 14,
  })
  b.cornerRadius = 8
  b.fills = [solid(primary ? C.accent : C.soft)]
  b.primaryAxisAlignItems = 'CENTER'
  b.counterAxisAlignItems = 'CENTER'
  text(b, label, { size: 13, style: 'SemiBold' })
  parent.appendChild(b)
  return b
}

function field(parent, label, value, compact) {
  const wrap = figma.createAutoLayout('VERTICAL', {
    name: 'Field/' + label,
    itemSpacing: 6,
  })
  text(wrap, label, { size: compact ? 11 : 12, color: C.muted, style: 'Medium' })
  const box = figma.createAutoLayout('HORIZONTAL', {
    paddingTop: compact ? 8 : 10,
    paddingBottom: compact ? 8 : 10,
    paddingLeft: 12,
    paddingRight: 12,
  })
  box.fills = [solid(C.soft)]
  box.strokes = [solid(C.border)]
  box.strokeWeight = 1
  box.cornerRadius = 8
  text(box, value, { size: compact ? 12 : 13 })
  wrap.appendChild(box)
  box.layoutSizingHorizontal = 'FILL'
  parent.appendChild(wrap)
  wrap.layoutSizingHorizontal = 'FILL'
  return wrap
}

function panel(parent, name) {
  const f = figma.createAutoLayout('VERTICAL', {
    name,
    itemSpacing: 10,
    paddingTop: 14,
    paddingBottom: 14,
    paddingLeft: 14,
    paddingRight: 14,
  })
  f.fills = [solid(C.elevated)]
  f.strokes = [solid(C.border)]
  f.strokeWeight = 1
  f.cornerRadius = 10
  parent.appendChild(f)
  f.layoutSizingHorizontal = 'FILL'
  return f
}

function table(parent, headers, rows, compact) {
  const wrap = panel(parent, 'Table')
  wrap.itemSpacing = 0
  wrap.paddingTop = 0
  wrap.paddingBottom = 0
  wrap.paddingLeft = 0
  wrap.paddingRight = 0

  const head = figma.createAutoLayout('HORIZONTAL', {
    name: 'thead',
    paddingTop: 10,
    paddingBottom: 10,
    paddingLeft: compact ? 10 : 14,
    paddingRight: compact ? 10 : 14,
    itemSpacing: 8,
  })
  head.fills = [solid(C.soft)]
  headers.forEach((h) => {
    const cell = text(head, h, { size: compact ? 10 : 12, color: C.muted, style: 'SemiBold' })
    cell.layoutGrow = 1
  })
  wrap.appendChild(head)
  head.layoutSizingHorizontal = 'FILL'

  rows.forEach((row, i) => {
    const tr = figma.createAutoLayout('HORIZONTAL', {
      name: 'tr/' + i,
      paddingTop: 10,
      paddingBottom: 10,
      paddingLeft: compact ? 10 : 14,
      paddingRight: compact ? 10 : 14,
      itemSpacing: 8,
    })
    if (i % 2 === 1) tr.fills = [solid(C.bg, 0.35)]
    row.forEach((cell) => {
      const t = text(tr, cell, { size: compact ? 11 : 13 })
      t.layoutGrow = 1
    })
    wrap.appendChild(tr)
    tr.layoutSizingHorizontal = 'FILL'
  })
  return wrap
}

function pageHeader(main, title, subtitle, actions, mobile) {
  const row = figma.createAutoLayout(mobile ? 'VERTICAL' : 'HORIZONTAL', {
    name: 'Header',
    itemSpacing: mobile ? 12 : 16,
  })
  if (!mobile) {
    row.primaryAxisAlignItems = 'SPACE_BETWEEN'
    row.counterAxisAlignItems = 'MIN'
  }

  const left = figma.createAutoLayout('VERTICAL', { itemSpacing: 6 })
  text(left, title, { size: mobile ? 22 : 28, style: 'Bold' })
  text(left, subtitle, {
    size: mobile ? 12 : 14,
    color: C.muted,
    width: mobile ? 330 : undefined,
  })
  row.appendChild(left)
  left.layoutSizingHorizontal = 'FILL'

  if (actions && actions.length) {
    const acts = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 8 })
    actions.forEach((a) => btn(acts, a.label, a.primary))
    row.appendChild(acts)
    if (mobile) acts.layoutSizingHorizontal = 'FILL'
  }

  main.appendChild(row)
  row.layoutSizingHorizontal = 'FILL'
}

function adminSidebar(parent, active) {
  const side = figma.createAutoLayout('VERTICAL', {
    name: 'Sidebar',
    itemSpacing: 4,
    paddingTop: 24,
    paddingBottom: 24,
    paddingLeft: 16,
    paddingRight: 16,
  })
  side.resize(240, DESKTOP.h)
  side.fills = [solid(C.elevated)]
  side.strokes = [solid(C.border)]
  side.strokeWeight = 1
  side.layoutSizingVertical = 'FIXED'
  side.layoutSizingHorizontal = 'FIXED'

  text(side, 'PDV ADMIN', { size: 12, style: 'Bold', color: C.accent })

  const nav = figma.createAutoLayout('VERTICAL', {
    name: 'Nav',
    itemSpacing: 2,
    paddingTop: 16,
  })
  nav.layoutGrow = 1

  ADMIN_NAV.forEach((label) => {
    const link = figma.createAutoLayout('HORIZONTAL', {
      name: 'Nav/' + label,
      paddingTop: 9,
      paddingBottom: 9,
      paddingLeft: 12,
      paddingRight: 12,
    })
    const on = label === active
    link.cornerRadius = 8
    link.fills = on ? [solid(C.accent, 0.22)] : []
    text(link, label, {
      size: 13,
      style: on ? 'SemiBold' : 'Regular',
      color: on ? C.accent : C.muted,
    })
    nav.appendChild(link)
    link.layoutSizingHorizontal = 'FILL'
  })
  side.appendChild(nav)
  nav.layoutSizingHorizontal = 'FILL'
  nav.layoutSizingVertical = 'FILL'

  const footer = figma.createAutoLayout('VERTICAL', { itemSpacing: 8, paddingTop: 12 })
  text(footer, 'Alex Manager · manager', { size: 12, color: C.muted })
  text(footer, 'Open POS', { size: 13, style: 'Medium', color: C.accent })
  btn(footer, 'Sign out', false)
  side.appendChild(footer)
  footer.layoutSizingHorizontal = 'FILL'

  parent.appendChild(side)
  return side
}

function adminMobileNav(parent, active) {
  const bar = figma.createAutoLayout('VERTICAL', {
    name: 'Mobile nav',
    itemSpacing: 8,
    paddingTop: 12,
    paddingBottom: 12,
    paddingLeft: 12,
    paddingRight: 12,
  })
  bar.fills = [solid(C.elevated)]
  bar.strokes = [solid(C.border)]
  bar.strokeWeight = 1

  const top = figma.createAutoLayout('HORIZONTAL', {
    primaryAxisAlignItems: 'SPACE_BETWEEN',
    counterAxisAlignItems: 'CENTER',
  })
  text(top, 'PDV ADMIN', { size: 11, style: 'Bold', color: C.accent })
  text(top, 'Alex · manager', { size: 11, color: C.muted })
  bar.appendChild(top)
  top.layoutSizingHorizontal = 'FILL'

  const nav = figma.createAutoLayout('HORIZONTAL', {
    name: 'Nav wrap',
    itemSpacing: 6,
    layoutWrap: 'WRAP',
    counterAxisSpacing: 6,
  })
  ADMIN_NAV.forEach((label) => {
    const chip = figma.createAutoLayout('HORIZONTAL', {
      paddingTop: 6,
      paddingBottom: 6,
      paddingLeft: 10,
      paddingRight: 10,
    })
    const on = label === active
    chip.cornerRadius = 999
    chip.fills = [solid(on ? C.accent : C.soft, on ? 0.35 : 1)]
    text(chip, label, {
      size: 11,
      style: on ? 'SemiBold' : 'Regular',
      color: on ? C.accent : C.muted,
    })
    nav.appendChild(chip)
  })
  bar.appendChild(nav)
  nav.layoutSizingHorizontal = 'FILL'

  parent.appendChild(bar)
  bar.layoutSizingHorizontal = 'FILL'
  return bar
}

function deviceChrome(label, size) {
  const wrap = figma.createAutoLayout('VERTICAL', {
    name: label,
    itemSpacing: 8,
  })
  text(wrap, label + ' · ' + size.w + '×' + size.h, {
    size: 11,
    style: 'SemiBold',
    color: C.muted,
  })
  const screen = figma.createAutoLayout('VERTICAL', {
    name: 'Screen',
    itemSpacing: 0,
  })
  screen.resize(size.w, size.h)
  screen.fills = [solid(C.bg)]
  screen.clipsContent = true
  screen.cornerRadius = size.w < 500 ? 24 : 8
  screen.strokes = [solid(C.border)]
  screen.strokeWeight = 1
  screen.primaryAxisSizingMode = 'FIXED'
  screen.counterAxisSizingMode = 'FIXED'
  wrap.appendChild(screen)
  return { wrap, screen }
}

function makePair(parent, viewName, buildDesktop, buildMobile) {
  const pair = figma.createAutoLayout('VERTICAL', {
    name: 'View / ' + viewName,
    itemSpacing: 16,
    paddingTop: 24,
    paddingBottom: 24,
    paddingLeft: 24,
    paddingRight: 24,
  })
  pair.fills = [solid(C.elevated, 0.45)]
  pair.strokes = [solid(C.border)]
  pair.strokeWeight = 1
  pair.cornerRadius = 16

  text(pair, viewName, { size: 18, style: 'Bold' })

  const row = figma.createAutoLayout('HORIZONTAL', {
    name: 'Breakpoints',
    itemSpacing: 40,
    counterAxisAlignItems: 'MIN',
  })

  const desk = deviceChrome('Desktop', DESKTOP)
  buildDesktop(desk.screen)
  row.appendChild(desk.wrap)

  const mob = deviceChrome('Mobile', MOBILE)
  buildMobile(mob.screen)
  row.appendChild(mob.wrap)

  pair.appendChild(row)
  parent.appendChild(pair)
  pair.layoutSizingHorizontal = 'HUG'
  return pair
}

function adminShell(screen, active, mobile, fillMain) {
  if (mobile) {
    screen.layoutMode = 'VERTICAL'
    screen.itemSpacing = 0
    screen.paddingTop = 0
    screen.paddingBottom = 0
    screen.paddingLeft = 0
    screen.paddingRight = 0
    adminMobileNav(screen, active)
    const main = figma.createAutoLayout('VERTICAL', {
      name: 'Main',
      itemSpacing: 14,
      paddingTop: 16,
      paddingBottom: 16,
      paddingLeft: 14,
      paddingRight: 14,
    })
    main.layoutGrow = 1
    screen.appendChild(main)
    main.layoutSizingHorizontal = 'FILL'
    main.layoutSizingVertical = 'FILL'
    fillMain(main, true)
    return
  }

  screen.layoutMode = 'HORIZONTAL'
  screen.itemSpacing = 0
  adminSidebar(screen, active)
  const main = figma.createAutoLayout('VERTICAL', {
    name: 'Main',
    itemSpacing: 18,
    paddingTop: 28,
    paddingBottom: 28,
    paddingLeft: 28,
    paddingRight: 28,
  })
  main.layoutGrow = 1
  screen.appendChild(main)
  main.layoutSizingHorizontal = 'FILL'
  main.layoutSizingVertical = 'FILL'
  fillMain(main, false)
}

function buildLogin(screen, mobile) {
  screen.layoutMode = 'VERTICAL'
  screen.primaryAxisAlignItems = 'CENTER'
  screen.counterAxisAlignItems = 'CENTER'
  screen.paddingTop = 40
  screen.paddingBottom = 40
  screen.paddingLeft = mobile ? 20 : 40
  screen.paddingRight = mobile ? 20 : 40
  screen.itemSpacing = 20

  const card = figma.createAutoLayout('VERTICAL', {
    name: 'Login card',
    itemSpacing: 14,
    paddingTop: 28,
    paddingBottom: 28,
    paddingLeft: 24,
    paddingRight: 24,
  })
  card.resize(mobile ? 350 : 400, 10)
  card.primaryAxisSizingMode = 'AUTO'
  card.counterAxisSizingMode = 'FIXED'
  card.fills = [solid(C.elevated)]
  card.strokes = [solid(C.border)]
  card.strokeWeight = 1
  card.cornerRadius = 12

  text(card, 'PDV', { size: 28, style: 'Bold', color: C.accent })
  text(card, 'Sign in to continue', { size: 14, color: C.muted })
  field(card, 'Email', 'manager@pos.test', mobile)
  field(card, 'Password', '••••••••', mobile)

  const captcha = panel(card, 'Security check')
  text(captcha, 'Security check (Turnstile)', { size: 12, style: 'SemiBold', color: C.warning })
  text(captcha, 'Shown after failed login threshold', {
    size: 11,
    color: C.muted,
    width: mobile ? 280 : 320,
  })

  btn(card, 'Sign in', true)
  screen.appendChild(card)
}

function buildStoreSelect(screen, mobile) {
  screen.layoutMode = 'VERTICAL'
  screen.itemSpacing = 16
  screen.paddingTop = 32
  screen.paddingLeft = mobile ? 16 : 48
  screen.paddingRight = mobile ? 16 : 48
  text(screen, 'Select store', { size: mobile ? 22 : 28, style: 'Bold' })
  text(screen, 'Choose where you will operate today.', { size: 14, color: C.muted })
  ;['Downtown', 'Airport', 'Mall'].forEach((name) => {
    const card = panel(screen, name)
    text(card, name, { size: 16, style: 'SemiBold' })
    text(card, 'Tap to select', { size: 12, color: C.muted })
  })
}

function buildShift(screen, mobile) {
  screen.layoutMode = 'VERTICAL'
  screen.itemSpacing = 16
  screen.paddingTop = 32
  screen.paddingLeft = mobile ? 16 : 48
  screen.paddingRight = mobile ? 16 : 48
  text(screen, 'Open shift', { size: mobile ? 22 : 28, style: 'Bold' })
  text(screen, 'Count opening cash before checkout (RN-003).', {
    size: 14,
    color: C.muted,
    width: mobile ? 350 : undefined,
  })
  field(screen, 'Opening cash (R$)', '200,00', mobile)
  btn(screen, 'Open shift', true)
}

function buildPos(screen, mobile) {
  screen.layoutMode = mobile ? 'VERTICAL' : 'HORIZONTAL'
  screen.itemSpacing = 0

  const catalog = figma.createAutoLayout('VERTICAL', {
    name: 'Catalog',
    itemSpacing: 12,
    paddingTop: 20,
    paddingBottom: 20,
    paddingLeft: 16,
    paddingRight: 16,
  })
  if (!mobile) catalog.layoutGrow = 1
  text(catalog, 'POS · Downtown', { size: 16, style: 'Bold' })
  field(catalog, 'Scan / search', 'SKU or name…', mobile)
  ;['Coffee 250g · R$ 28,90', 'Green tea · R$ 16,50', 'Ceramic mug · R$ 39,00'].forEach((p) => {
    const row = panel(catalog, p)
    text(row, p, { size: 13 })
  })
  screen.appendChild(catalog)
  catalog.layoutSizingHorizontal = 'FILL'
  if (!mobile) catalog.layoutSizingVertical = 'FILL'

  const cart = figma.createAutoLayout('VERTICAL', {
    name: 'Cart',
    itemSpacing: 12,
    paddingTop: 20,
    paddingBottom: 20,
    paddingLeft: 16,
    paddingRight: 16,
  })
  cart.fills = [solid(C.elevated)]
  cart.resize(mobile ? MOBILE.w : 420, mobile ? 360 : DESKTOP.h)
  if (!mobile) {
    cart.layoutSizingVertical = 'FIXED'
    cart.layoutSizingHorizontal = 'FIXED'
  }
  text(cart, 'Cart', { size: 16, style: 'SemiBold' })
  text(cart, 'Coffee 250g × 2', { size: 13, color: C.muted })
  text(cart, 'Mug × 1', { size: 13, color: C.muted })
  text(cart, 'Total R$ 96,80', { size: 18, style: 'Bold' })
  btn(cart, 'Pay', true)
  screen.appendChild(cart)
  if (mobile) cart.layoutSizingHorizontal = 'FILL'
}

function buildIdle(screen, mobile) {
  screen.layoutMode = 'NONE'
  const bg = figma.createFrame()
  bg.name = 'POS dimmed'
  bg.resize(mobile ? MOBILE.w : DESKTOP.w, mobile ? MOBILE.h : DESKTOP.h)
  bg.fills = [solid(C.bg)]
  screen.appendChild(bg)
  bg.x = 0
  bg.y = 0

  const overlay = figma.createFrame()
  overlay.name = 'Overlay'
  overlay.resize(mobile ? MOBILE.w : DESKTOP.w, mobile ? MOBILE.h : DESKTOP.h)
  overlay.fills = [solid(C.overlay, 0.65)]
  screen.appendChild(overlay)
  overlay.x = 0
  overlay.y = 0

  const dialogW = mobile ? 340 : 420
  const dialog = figma.createAutoLayout('VERTICAL', {
    name: 'Idle dialog',
    itemSpacing: 14,
    paddingTop: 24,
    paddingBottom: 24,
    paddingLeft: 22,
    paddingRight: 22,
  })
  dialog.fills = [solid(C.elevated)]
  dialog.strokes = [solid(C.warning, 0.5)]
  dialog.strokeWeight = 1
  dialog.cornerRadius = 12
  dialog.resize(dialogW, 10)
  dialog.primaryAxisSizingMode = 'AUTO'
  dialog.counterAxisSizingMode = 'FIXED'
  text(dialog, 'Session idle', { size: 18, style: 'Bold', color: C.warning })
  text(
    dialog,
    'No activity detected. You will be signed out after 15 minutes of idle time (shared POS lock).',
    { size: 13, color: C.muted, width: dialogW - 44 },
  )
  const acts = figma.createAutoLayout(mobile ? 'VERTICAL' : 'HORIZONTAL', { itemSpacing: 8 })
  btn(acts, 'Stay signed in', true)
  btn(acts, 'Sign out now', false)
  dialog.appendChild(acts)
  if (mobile) acts.layoutSizingHorizontal = 'FILL'
  screen.appendChild(dialog)
  dialog.x = ((mobile ? MOBILE.w : DESKTOP.w) - dialogW) / 2
  dialog.y = mobile ? 220 : 320
}

function clearPage(page) {
  for (const child of [...page.children]) child.remove()
}

async function getSystemPage() {
  let page = figma.root.children.find((p) => p.name === PAGE_NAME)
  if (!page) {
    page = figma.createPage()
    page.name = PAGE_NAME
  }
  await figma.setCurrentPageAsync(page)
  clearPage(page)
  return page
}

function buildCover(parent) {
  const cover = figma.createAutoLayout('VERTICAL', {
    name: '00 — Cover / System map',
    itemSpacing: 16,
    paddingTop: 40,
    paddingBottom: 40,
    paddingLeft: 40,
    paddingRight: 40,
  })
  cover.resize(1600, 10)
  cover.primaryAxisSizingMode = 'AUTO'
  cover.counterAxisSizingMode = 'FIXED'
  cover.fills = [solid(C.elevated)]
  cover.strokes = [solid(C.border)]
  cover.strokeWeight = 1
  cover.cornerRadius = 16

  text(cover, 'PDV — Design system screens', { size: 32, style: 'Bold' })
  text(
    cover,
    'One canvas for every vision. Each view below is a single composition with Desktop (1440×900) and Mobile (390×844) side by side. Tokens match the SPA (DM Sans, dark theme, accent #3d9a7a).',
    { size: 15, color: C.muted, width: 1480 },
  )

  const cols = figma.createAutoLayout('HORIZONTAL', { itemSpacing: 40 })
  const ops = panel(cols, 'Operational')
  text(ops, 'Operational', { size: 16, style: 'SemiBold', color: C.accent })
  ;['Login (+ Turnstile)', 'Store select', 'Open shift', 'POS Checkout', 'Idle session'].forEach(
    (l) => text(ops, '· ' + l, { size: 13, color: C.muted }),
  )
  const adm = panel(cols, 'Administrative')
  text(adm, 'Administrative', { size: 16, style: 'SemiBold', color: C.accent })
  ADMIN_NAV.forEach((l) => text(adm, '· ' + l, { size: 13, color: C.muted }))
  cover.appendChild(cols)
  cols.layoutSizingHorizontal = 'FILL'
  ops.layoutSizingHorizontal = 'FILL'
  adm.layoutSizingHorizontal = 'FILL'

  text(
    cover,
    'Mobile admin: horizontal chip nav (matches AdminShell @media max-width 900px). Desktop: 240px sidebar.',
    { size: 13, color: C.muted, width: 1480 },
  )

  parent.appendChild(cover)
  return cover
}

function sectionTitle(parent, title) {
  const t = figma.createAutoLayout('VERTICAL', {
    name: 'Section / ' + title,
    itemSpacing: 8,
    paddingTop: 8,
  })
  text(t, title, { size: 24, style: 'Bold', color: C.accent })
  parent.appendChild(t)
  return t
}

async function syncAll() {
  await ensureFonts()
  const page = await getSystemPage()

  const board = figma.createAutoLayout('VERTICAL', {
    name: 'PDV Responsive Board',
    itemSpacing: 48,
    paddingTop: 40,
    paddingBottom: 80,
    paddingLeft: 40,
    paddingRight: 40,
  })
  board.x = 0
  board.y = 0
  board.fills = [solid(C.bg)]

  buildCover(board)
  sectionTitle(board, '01 — Operational')

  makePair(
    board,
    'Login',
    (s) => buildLogin(s, false),
    (s) => buildLogin(s, true),
  )
  makePair(
    board,
    'Store select',
    (s) => buildStoreSelect(s, false),
    (s) => buildStoreSelect(s, true),
  )
  makePair(
    board,
    'Open shift',
    (s) => buildShift(s, false),
    (s) => buildShift(s, true),
  )
  makePair(
    board,
    'POS Checkout',
    (s) => buildPos(s, false),
    (s) => buildPos(s, true),
  )
  makePair(
    board,
    'Idle session warning',
    (s) => buildIdle(s, false),
    (s) => buildIdle(s, true),
  )

  sectionTitle(board, '02 — Administrative')

  const adminViews = [
    {
      name: 'Dashboard',
      nav: 'Dashboard',
      fill: (main, mobile) => {
        pageHeader(main, 'Dashboard', 'Manager overview — store ops at a glance.', null, mobile)
        const kpis = figma.createAutoLayout('HORIZONTAL', {
          itemSpacing: 10,
          layoutWrap: 'WRAP',
          counterAxisSpacing: 10,
        })
        ;[
          ['Products', '128'],
          ['Customers', '1 240'],
          ['Sales', '86'],
          ['Open shifts', '3'],
        ].forEach(([l, v]) => {
          const card = panel(kpis, l)
          card.resize(mobile ? 150 : 200, 10)
          card.layoutSizingHorizontal = 'FIXED'
          text(card, l, { size: 11, color: C.muted })
          text(card, v, { size: mobile ? 18 : 22, style: 'Bold' })
        })
        main.appendChild(kpis)
        kpis.layoutSizingHorizontal = 'FILL'
      },
    },
    {
      name: 'Analytics',
      nav: 'Analytics',
      fill: (main, mobile) => {
        pageHeader(
          main,
          'Analytics',
          'Registrations, recurrence, spend, campaigns (RN-080–084).',
          null,
          mobile,
        )
        const p = panel(main, 'Recurrence')
        text(p, 'Recurrence index 1.42', { size: 16, style: 'SemiBold' })
        text(p, '318 repeat / 890 with purchases', { size: 13, color: C.muted })
        table(
          main,
          ['Date', 'Regs'],
          [
            ['07-18', '12'],
            ['07-17', '9'],
          ],
          mobile,
        )
      },
    },
    {
      name: 'Catalog',
      nav: 'Catalog',
      fill: (main, mobile) => {
        pageHeader(main, 'Catalog', 'Products and prices.', [{ label: 'New', primary: true }], mobile)
        table(
          main,
          mobile ? ['SKU', 'Price'] : ['SKU', 'Name', 'Price', 'Status'],
          mobile
            ? [
                ['COFFEE-250', 'R$ 28,90'],
                ['TEA-100', 'R$ 16,50'],
              ]
            : [
                ['COFFEE-250', 'Coffee 250g', 'R$ 28,90', 'Active'],
                ['TEA-100', 'Green tea', 'R$ 16,50', 'Active'],
              ],
          mobile,
        )
      },
    },
    {
      name: 'Sales',
      nav: 'Sales',
      fill: (main, mobile) => {
        pageHeader(
          main,
          'Sales',
          'Completed sales filters (RN-061).',
          [{ label: 'Refresh payments', primary: false }],
          mobile,
        )
        table(
          main,
          mobile ? ['Sale', 'Total'] : ['Sale', 'Store', 'Total', 'Pay'],
          mobile
            ? [
                ['#4821', 'R$ 189,90'],
                ['#4820', 'R$ 54,00'],
              ]
            : [
                ['#4821', 'Downtown', 'R$ 189,90', 'Card'],
                ['#4820', 'Airport', 'R$ 54,00', 'Cash'],
              ],
          mobile,
        )
      },
    },
    {
      name: 'Shifts',
      nav: 'Shifts',
      fill: (main, mobile) => {
        pageHeader(main, 'Shifts', 'Closing reports + reopen (RN-063).', null, mobile)
        const p = panel(main, 'Report')
        text(p, 'Shift #87 · Variance −R$ 2,00', { size: 14, style: 'SemiBold', color: C.warning })
        text(p, 'Sales R$ 4.820 · Cash R$ 1.200 · Card R$ 2.800', { size: 12, color: C.muted })
        btn(p, 'Reopen shift', false)
      },
    },
    {
      name: 'Users',
      nav: 'Users',
      fill: (main, mobile) => {
        pageHeader(
          main,
          'Users',
          'Roles, stores, MFA reset (RN-062/074).',
          [{ label: 'New user', primary: true }],
          mobile,
        )
        table(
          main,
          mobile ? ['Name', 'Role'] : ['Name', 'Email', 'Role', 'MFA'],
          mobile
            ? [
                ['Ana', 'operator'],
                ['Alex', 'manager'],
              ]
            : [
                ['Ana Operadora', 'ana@pos.test', 'operator', 'On'],
                ['Alex Manager', 'manager@pos.test', 'manager', 'On'],
              ],
          mobile,
        )
      },
    },
    {
      name: 'Customers',
      nav: 'Customers',
      fill: (main, mobile) => {
        pageHeader(main, 'Customers', 'Registry for assigned stores.', null, mobile)
        table(
          main,
          mobile ? ['Name', 'Region'] : ['Name', 'Document', 'Region'],
          mobile
            ? [
                ['Maria S.', 'SP'],
                ['Pedro L.', 'RJ'],
              ]
            : [
                ['Maria Silva', '***.***-12', 'SP'],
                ['Pedro Lima', '***.***-45', 'RJ'],
              ],
          mobile,
        )
      },
    },
    {
      name: 'Promotions',
      nav: 'Promotions',
      fill: (main, mobile) => {
        pageHeader(
          main,
          'Promotions',
          'Percent / fixed / combo.',
          [{ label: 'New', primary: true }],
          mobile,
        )
        table(
          main,
          ['Name', 'Type', 'Value'],
          [
            ['Weekend 10%', 'percent', '10%'],
            ['Mug + Coffee', 'combo', '−R$ 10'],
          ],
          mobile,
        )
      },
    },
    {
      name: 'Inventory',
      nav: 'Inventory',
      fill: (main, mobile) => {
        pageHeader(main, 'Inventory', 'Per-store stock + adjust.', null, mobile)
        table(
          main,
          ['SKU', 'On hand', 'Available'],
          [
            ['COFFEE-250', '42', '40'],
            ['TEA-100', '18', '18'],
          ],
          mobile,
        )
      },
    },
    {
      name: 'Refunds',
      nav: 'Refunds',
      fill: (main, mobile) => {
        pageHeader(main, 'Refunds', 'Returns against completed sales.', null, mobile)
        table(
          main,
          mobile ? ['Refund', 'Amount'] : ['Refund', 'Sale', 'Amount', 'Status'],
          mobile
            ? [
                ['#91', 'R$ 45,00'],
                ['#90', 'R$ 28,90'],
              ]
            : [
                ['#91', '#4810', 'R$ 45,00', 'Approved'],
                ['#90', '#4802', 'R$ 28,90', 'Approved'],
              ],
          mobile,
        )
      },
    },
    {
      name: 'Audit log',
      nav: 'Audit log',
      fill: (main, mobile) => {
        pageHeader(main, 'Audit log', 'Immutable sensitive actions (RN-070).', null, mobile)
        table(
          main,
          mobile ? ['Action', 'Actor'] : ['When', 'Actor', 'Action', 'Entity'],
          mobile
            ? [
                ['inventory.adjust', 'Alex'],
                ['refund.create', 'Alex'],
              ]
            : [
                ['19 Jul 13:40', 'Alex', 'inventory.adjust', 'COFFEE-250'],
                ['18 Jul 16:12', 'Alex', 'refund.create', 'refund:91'],
              ],
          mobile,
        )
      },
    },
  ]

  adminViews.forEach((view) => {
    makePair(
      board,
      view.name,
      (s) => adminShell(s, view.nav, false, view.fill),
      (s) => adminShell(s, view.nav, true, view.fill),
    )
  })

  page.appendChild(board)

  return (
    'One page created: "' +
    PAGE_NAME +
    '"\n\n' +
    'Cover + Operational (5) + Administrative (11)\n' +
    'Each view = Desktop 1440×900 + Mobile 390×844 in the same composition.\n\n' +
    'Open that page in the file. Re-run replaces the whole board.'
  )
}
