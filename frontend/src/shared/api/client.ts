import type { ApiError } from './types'

function readCookie(name: string): string | null {
  const match = document.cookie.match(new RegExp(`(?:^|; )${name}=([^;]*)`))
  return match ? decodeURIComponent(match[1]) : null
}

export class ApiClientError extends Error {
  readonly status: number
  readonly error: ApiError

  constructor(status: number, error: ApiError) {
    super(error.message)
    this.name = 'ApiClientError'
    this.status = status
    this.error = error
  }
}

type SessionInvalidHandler = () => void

let sessionInvalidHandler: SessionInvalidHandler | null = null

/** Register handler for dead/invalid server session (401 / inactive). */
export function setSessionInvalidHandler(handler: SessionInvalidHandler | null) {
  sessionInvalidHandler = handler
}

function shouldInvalidateSession(path: string, status: number, code: string): boolean {
  // Boot/login/logout/MFA own their flows; avoid full-page bounce during /me probe.
  if (
    path === '/api/auth/login' ||
    path === '/api/auth/logout' ||
    path === '/api/auth/me' ||
    path.startsWith('/api/auth/mfa/')
  ) {
    return false
  }
  if (status === 401) {
    return true
  }
  return status === 403 && code === 'AUTH_ACCOUNT_INACTIVE'
}

let csrfRequest: Promise<void> | null = null

export function primeCsrf(): Promise<void> {
  if (readCookie('XSRF-TOKEN')) {
    return Promise.resolve()
  }

  if (!csrfRequest) {
    csrfRequest = fetch('/sanctum/csrf-cookie', {
      method: 'GET',
      credentials: 'include',
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(response.statusText || 'Failed to initialize secure session')
        }
      })
      .finally(() => {
        csrfRequest = null
      })
  }

  return csrfRequest
}

export async function apiRequest<T>(
  path: string,
  options: RequestInit = {},
): Promise<T> {
  const headers = new Headers(options.headers)
  headers.set('Accept', 'application/json')
  headers.set('X-Requested-With', 'XMLHttpRequest')

  if (options.body && !headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json')
  }

  const xsrf = readCookie('XSRF-TOKEN')
  if (xsrf) {
    headers.set('X-XSRF-TOKEN', xsrf)
  }

  const response = await fetch(path, {
    ...options,
    credentials: 'include',
    headers,
  })

  if (response.status === 204) {
    return undefined as T
  }

  const payload = await response.json().catch(() => null)

  if (!response.ok) {
    const error: ApiError = payload?.error ?? {
      code: 'HTTP_ERROR',
      message: response.statusText || 'Request failed',
    }
    if (shouldInvalidateSession(path, response.status, error.code)) {
      sessionInvalidHandler?.()
    }
    throw new ApiClientError(response.status, error)
  }

  return payload as T
}

export async function loginRequest(email: string, password: string) {
  await primeCsrf()
  return apiRequest<{
    data: {
      mfa_required: boolean
      mfa_setup_required: boolean
      user: import('./types').User
    }
  }>('/api/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  })
}

export async function beginMfaSetupRequest() {
  await primeCsrf()
  return apiRequest<{
    data: {
      secret: string
      otpauth_url: string
      qr_data_uri: string
    }
  }>('/api/auth/mfa/setup', {
    method: 'POST',
  })
}

export async function confirmMfaSetupRequest(code: string) {
  await primeCsrf()
  return apiRequest<{
    data: {
      mfa_required: boolean
      mfa_setup_required: boolean
      recovery_codes: string[]
      user: import('./types').User
    }
  }>('/api/auth/mfa/confirm', {
    method: 'POST',
    body: JSON.stringify({ code }),
  })
}

export async function verifyMfaChallengeRequest(code: string) {
  await primeCsrf()
  return apiRequest<{
    data: {
      mfa_required: boolean
      mfa_setup_required: boolean
      user: import('./types').User
    }
  }>('/api/auth/mfa/verify', {
    method: 'POST',
    body: JSON.stringify({ code }),
  })
}

export async function fetchCurrentUser() {
  return apiRequest<{ data: { user: import('./types').User } }>('/api/auth/me')
}

export async function logoutRequest() {
  await primeCsrf()
  return apiRequest<{ data: { logged_out: boolean } }>('/api/auth/logout', {
    method: 'POST',
  })
}

let storesRequest: ReturnType<typeof fetchStores> | null = null

function fetchStores() {
  return apiRequest<{ data: { stores: import('./types').Store[] } }>('/api/stores')
}

export function listStores() {
  if (!storesRequest) {
    storesRequest = fetchStores().finally(() => {
      storesRequest = null
    })
  }

  return storesRequest
}

export function selectStore(storeId: number) {
  return apiRequest<{ data: { store_id: number } }>('/api/stores/context', {
    method: 'POST',
    body: JSON.stringify({ store_id: storeId }),
  })
}

export function openShift(openingCashAmount = 100) {
  return apiRequest<{ data: { shift: unknown } }>('/api/operational/shifts/open', {
    method: 'POST',
    body: JSON.stringify({ opening_cash_amount: openingCashAmount }),
  })
}

export function getCurrentShift() {
  return apiRequest<{ data: { shift: unknown | null } }>('/api/operational/shifts/current')
}

export function closeShift(closingCashAmount?: number) {
  const body =
    closingCashAmount !== undefined
      ? JSON.stringify({ closing_cash_amount: closingCashAmount })
      : JSON.stringify({})
  return apiRequest<{
    data: {
      message: string
      shift: { id: number; status: string }
      report: import('./types').ShiftClosingReport
    }
  }>('/api/operational/shifts/close', {
    method: 'POST',
    body,
  })
}

export function listAdminShifts(storeId: number) {
  return apiRequest<{ data: { shifts: import('./types').AdminShiftSummary[] } }>(
    `/api/admin/shifts?store_id=${storeId}`,
  )
}

export function getAdminShiftReport(shiftId: number) {
  return apiRequest<{ data: { report: import('./types').ShiftClosingReport } }>(
    `/api/admin/shifts/${shiftId}/report`,
  )
}

export function reopenAdminShift(shiftId: number) {
  return apiRequest<{
    data: {
      message: string
      shift: {
        id: number
        store_id: number
        status: string
        opening_cash_amount: string
        closing_cash_amount: string | null
        opened_at: string | null
        closed_at: string | null
      }
    }
  }>(`/api/admin/shifts/${shiftId}/reopen`, {
    method: 'POST',
  })
}

export function listAdminUsers(params?: {
  search?: string
  cursor?: string
  per_page?: number
}) {
  const search = new URLSearchParams()
  if (params?.search) search.set('search', params.search)
  if (params?.cursor) search.set('cursor', params.cursor)
  if (params?.per_page !== undefined) search.set('per_page', String(params.per_page))
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{
    data: { users: import('./types').AdminUser[] }
    meta?: { next_cursor: string | null }
  }>(`/api/admin/users${query}`)
}

export function listAdminAuditLogs(filters: import('./types').AdminAuditFilters = {}) {
  const search = new URLSearchParams()
  if (filters.from) search.set('from', filters.from)
  if (filters.to) search.set('to', filters.to)
  if (filters.action) search.set('action', filters.action)
  if (filters.actor_id !== undefined) search.set('actor_id', String(filters.actor_id))
  if (filters.store_id !== undefined) search.set('store_id', String(filters.store_id))
  if (filters.subject_type) search.set('subject_type', filters.subject_type)
  if (filters.subject_id !== undefined) search.set('subject_id', String(filters.subject_id))
  if (filters.cursor) search.set('cursor', filters.cursor)
  if (filters.per_page !== undefined) search.set('per_page', String(filters.per_page))
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{
    data: { audit_logs: import('./types').AuditLogEntry[] }
    meta: { next_cursor: string | null }
  }>(`/api/admin/audit-logs${query}`)
}

export function getAdminAnalytics(params?: { registration_days?: number; top_customers?: number }) {
  const search = new URLSearchParams()
  if (params?.registration_days !== undefined) {
    search.set('registration_days', String(params.registration_days))
  }
  if (params?.top_customers !== undefined) {
    search.set('top_customers', String(params.top_customers))
  }
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{ data: import('./types').AdminAnalytics }>(`/api/admin/analytics${query}`)
}

export function listCampaignCustomers(filters: import('./types').CampaignCustomerFilters = {}) {
  const search = new URLSearchParams()
  if (filters.birth_month !== undefined) search.set('birth_month', String(filters.birth_month))
  if (filters.region) search.set('region', filters.region)
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{ data: { customers: import('./types').Customer[] } }>(
    `/api/admin/campaigns/customers${query}`,
  )
}

export function getAdminUser(userId: number) {
  return apiRequest<{ data: { user: import('./types').AdminUser } }>(
    `/api/admin/users/${userId}`,
  )
}

export function createAdminUser(payload: import('./types').CreateAdminUserPayload) {
  return apiRequest<{ data: { message: string; user: import('./types').AdminUser } }>(
    '/api/admin/users',
    {
      method: 'POST',
      body: JSON.stringify(payload),
    },
  )
}

export function updateAdminUser(
  userId: number,
  payload: import('./types').UpdateAdminUserPayload,
) {
  return apiRequest<{ data: { message: string; user: import('./types').AdminUser } }>(
    `/api/admin/users/${userId}`,
    {
      method: 'PATCH',
      body: JSON.stringify(payload),
    },
  )
}

export function listOperationalProducts(params?: {
  search?: string
  cursor?: string
  per_page?: number
  category_id?: number
}) {
  const search = new URLSearchParams()
  if (params?.search) search.set('search', params.search)
  if (params?.cursor) search.set('cursor', params.cursor)
  if (params?.per_page !== undefined) search.set('per_page', String(params.per_page))
  if (params?.category_id !== undefined) search.set('category_id', String(params.category_id))
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{
    data: { products: import('./types').Product[] }
    meta: { next_cursor: string | null }
  }>(`/api/operational/catalog/products${query}`)
}

export function getAdminDashboard() {
  return apiRequest<{
    data: {
      area: string
      message: string
      user_id: number | null
      metrics: import('./types').AdminDashboardMetrics
    }
  }>('/api/admin/dashboard')
}

export function listAdminNotifications() {
  return apiRequest<{
    data: {
      notifications: import('./types').AdminNotification[]
    }
  }>('/api/admin/notifications')
}

export function listAdminSales(filters: import('./types').AdminSalesFilters = {}) {
  const search = new URLSearchParams()
  if (filters.from) search.set('from', filters.from)
  if (filters.to) search.set('to', filters.to)
  if (filters.store_id !== undefined) search.set('store_id', String(filters.store_id))
  if (filters.operator_id !== undefined) search.set('operator_id', String(filters.operator_id))
  if (filters.customer_id !== undefined) search.set('customer_id', String(filters.customer_id))
  if (filters.payment_method) search.set('payment_method', filters.payment_method)
  if (filters.status) search.set('status', filters.status)
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{ data: { sales: import('./types').AdminSaleSummary[] } }>(
    `/api/admin/sales${query}`,
  )
}

export function getAdminSale(saleId: number) {
  return apiRequest<{ data: { sale: import('./types').Sale } }>(`/api/admin/sales/${saleId}`)
}

export function listRefundsForSale(saleId: number) {
  return apiRequest<{ data: { refunds: import('./types').Refund[] } }>(
    `/api/admin/sales/${saleId}/refunds`,
  )
}

export function createRefund(saleId: number, payload: import('./types').CreateRefundPayload) {
  return apiRequest<{ data: { message: string; refund: import('./types').Refund } }>(
    `/api/admin/sales/${saleId}/refunds`,
    {
      method: 'POST',
      body: JSON.stringify(payload),
    },
  )
}

export function listAdminProducts(params?: {
  category_id?: number
  is_active?: boolean
  search?: string
  cursor?: string
  per_page?: number
}) {
  const search = new URLSearchParams()
  if (params?.category_id !== undefined) {
    search.set('category_id', String(params.category_id))
  }
  if (params?.is_active !== undefined) {
    search.set('is_active', params.is_active ? '1' : '0')
  }
  if (params?.search) search.set('search', params.search)
  if (params?.cursor) search.set('cursor', params.cursor)
  if (params?.per_page !== undefined) search.set('per_page', String(params.per_page))
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{
    data: { products: import('./types').AdminProduct[] }
    meta?: { next_cursor: string | null }
  }>(`/api/admin/catalog/products${query}`)
}

export function listAdminCustomers(params?: {
  search?: string
  cursor?: string
  per_page?: number
}) {
  const search = new URLSearchParams()
  if (params?.search) search.set('search', params.search)
  if (params?.cursor) search.set('cursor', params.cursor)
  if (params?.per_page !== undefined) search.set('per_page', String(params.per_page))
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{
    data: { customers: import('./types').Customer[] }
    meta?: { next_cursor: string | null }
  }>(`/api/admin/customers${query}`)
}

export function createAdminCustomer(payload: import('./types').CustomerPayload) {
  return apiRequest<{ data: { customer: import('./types').Customer } }>('/api/admin/customers', {
    method: 'POST',
    body: JSON.stringify(payload),
  })
}

export function updateAdminCustomer(customerId: number, payload: Partial<import('./types').CustomerPayload>) {
  return apiRequest<{ data: { customer: import('./types').Customer } }>(
    `/api/admin/customers/${customerId}`,
    {
      method: 'PATCH',
      body: JSON.stringify(payload),
    },
  )
}

export function listAdminPromotions(params?: { cursor?: string; per_page?: number }) {
  const search = new URLSearchParams()
  if (params?.cursor) search.set('cursor', params.cursor)
  if (params?.per_page !== undefined) search.set('per_page', String(params.per_page))
  const query = search.toString() ? `?${search.toString()}` : ''
  return apiRequest<{
    data: { promotions: import('./types').Promotion[] }
    meta?: { next_cursor: string | null }
  }>(`/api/admin/promotions${query}`)
}

export function createAdminPromotion(payload: import('./types').PromotionPayload) {
  return apiRequest<{ data: { promotion: import('./types').Promotion } }>('/api/admin/promotions', {
    method: 'POST',
    body: JSON.stringify(payload),
  })
}

export function updateAdminPromotion(
  promotionId: number,
  payload: Partial<import('./types').PromotionPayload>,
) {
  return apiRequest<{ data: { promotion: import('./types').Promotion } }>(
    `/api/admin/promotions/${promotionId}`,
    {
      method: 'PATCH',
      body: JSON.stringify(payload),
    },
  )
}

export function listAdminInventory(storeId: number) {
  return apiRequest<{ data: { inventory: import('./types').StoreInventoryRow[] } }>(
    `/api/admin/inventory?store_id=${storeId}`,
  )
}

export function adjustAdminInventory(payload: import('./types').AdjustInventoryPayload) {
  return apiRequest<{ data: { inventory: import('./types').StoreInventoryRow } }>(
    '/api/admin/inventory/adjustments',
    {
      method: 'POST',
      body: JSON.stringify(payload),
    },
  )
}

export function createSale(payload?: { product_id: number; quantity?: number }) {
  return apiRequest<{ data: { message: string; sale: import('./types').Sale } }>(
    '/api/operational/sales',
    {
      method: 'POST',
      body: payload
        ? JSON.stringify({
            product_id: payload.product_id,
            quantity: payload.quantity ?? 1,
          })
        : undefined,
    },
  )
}

export function addSaleLine(saleId: number, productId: number, quantity = 1) {
  return apiRequest<{ data: { sale: import('./types').Sale } }>(
    `/api/operational/sales/${saleId}/lines`,
    {
      method: 'POST',
      body: JSON.stringify({ product_id: productId, quantity }),
    },
  )
}

export function holdSale(saleId: number, label?: string) {
  return apiRequest<{ data: { sale: import('./types').Sale } }>(
    `/api/operational/sales/${saleId}/hold`,
    {
      method: 'POST',
      body: JSON.stringify({ label: label || null }),
    },
  )
}

export function resumeSale(saleId: number) {
  return apiRequest<{ data: { sale: import('./types').Sale } }>(
    `/api/operational/sales/${saleId}/resume`,
    { method: 'POST' },
  )
}

export function listHeldSales() {
  return apiRequest<{ data: { sales: import('./types').Sale[] } }>(
    '/api/operational/sales/held',
  )
}

export function findCustomerByCpf(cpf: string) {
  return apiRequest<{ data: { customer: import('./types').Customer } }>(
    `/api/operational/customers?cpf=${encodeURIComponent(cpf)}`,
  )
}

export function attachCustomerToSale(saleId: number, customerId: number) {
  return apiRequest<{ data: { sale: import('./types').Sale } }>(
    `/api/operational/sales/${saleId}/customer`,
    {
      method: 'POST',
      body: JSON.stringify({ customer_id: customerId }),
    },
  )
}

export function applyPromotionToSale(saleId: number, code: string) {
  return apiRequest<{ data: { sale: import('./types').Sale } }>(
    `/api/operational/sales/${saleId}/promotions`,
    {
      method: 'POST',
      body: JSON.stringify({ code }),
    },
  )
}

export function completeSale(
  saleId: number,
  payments: Array<{ method: string; amount: string; cash_received?: string }>,
) {
  return apiRequest<{ data: { sale: import('./types').Sale } }>(
    `/api/operational/sales/${saleId}/complete`,
    {
      method: 'POST',
      body: JSON.stringify({ payments }),
    },
  )
}

export type PaymentReconcileSummary = {
  webhook_retries_attempted: number
  webhook_retries_succeeded: number
  webhook_retries_requeued: number
  settlements_attempted: number
  settlements_confirmed: number
  settlements_failed: number
  still_pending: number
}

export function reconcileOperationalPayments() {
  return apiRequest<{ data: PaymentReconcileSummary }>(
    '/api/operational/payments/reconcile',
    { method: 'POST' },
  )
}

export function reconcileAdminPayments() {
  return apiRequest<{ data: PaymentReconcileSummary }>(
    '/api/admin/payments/reconcile',
    { method: 'POST' },
  )
}
